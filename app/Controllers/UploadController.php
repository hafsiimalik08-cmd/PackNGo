<?php
/**
 * PackNGo — Upload Controller
 * POST /api/admin/upload — Admin only, handles gallery image uploads.
 */
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/Database.php';
require_once dirname(__DIR__) . '/Models/BaseModel.php';
require_once dirname(__DIR__) . '/Models/GalleryModel.php';
require_once dirname(__DIR__, 2) . '/helpers/Sanitizer.php';
require_once dirname(__DIR__, 2) . '/helpers/Response.php';
require_once dirname(__DIR__, 2) . '/middleware/AdminAuthMiddleware.php';

class UploadController
{
    private GalleryModel $gallery;

    public function __construct()
    {
        AdminAuthMiddleware::require();
        $this->gallery = new GalleryModel();
    }

    public function image(): void
    {
        $config = require dirname(__DIR__, 2) . '/config/app.php';

        if (empty($_FILES['image'])) {
            Response::json(['success' => false, 'message' => 'No file uploaded.'], 400);
            return;
        }

        $file     = $_FILES['image'];
        $maxSize  = $config['upload']['max_size'];
        $allowed  = $config['upload']['allowed_types'];
        $uploadDir= $config['upload']['path'] . 'gallery/';

        // Validate
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Response::json(['success' => false, 'message' => 'Upload error code: ' . $file['error']], 400);
            return;
        }
        if ($file['size'] > $maxSize) {
            Response::json(['success' => false, 'message' => 'File too large. Max ' . ($maxSize / 1024 / 1024) . ' MB.'], 413);
            return;
        }

        // Verify MIME (do not trust file extension alone)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        $mimeAllowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($mime, $mimeAllowed, true)) {
            Response::json(['success' => false, 'message' => 'Invalid file type. Allowed: JPG, PNG, WEBP.'], 415);
            return;
        }

        // Generate safe filename
        $ext      = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
            default      => 'jpg',
        };
        $filename = bin2hex(random_bytes(12)) . '.' . $ext;
        $destPath = $uploadDir . $filename;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            Response::json(['success' => false, 'message' => 'Failed to save file.'], 500);
            return;
        }

        // Save to DB
        $title   = Sanitizer::string($_POST['title']    ?? '', 150);
        $alt     = Sanitizer::string($_POST['alt_text'] ?? $title, 200);
        $cat     = Sanitizer::string($_POST['category'] ?? '', 60);
        $destId  = !empty($_POST['destination_id']) ? (int)$_POST['destination_id'] : null;

        $id = $this->gallery->insert([
            'destination_id' => $destId,
            'title'          => $title,
            'file_name'      => $filename,
            'file_path'      => 'public/uploads/gallery/' . $filename,
            'alt_text'       => $alt,
            'category'       => $cat,
            'is_active'      => 1,
            'sort_order'     => 0,
        ]);

        Response::json([
            'success'   => true,
            'id'        => $id,
            'file_name' => $filename,
            'file_path' => 'public/uploads/gallery/' . $filename,
            'message'   => 'Image uploaded successfully.',
        ], 201);
    }

    /** DELETE /api/admin/gallery/:id */
    public function deleteImage(int $id): void
    {
        $img = $this->gallery->findById($id);
        if (!$img) {
            Response::notFound('Image not found.');
        }
        // Remove file
        $full = dirname(__DIR__, 2) . '/' . $img['file_path'];
        if (file_exists($full)) {
            @unlink($full);
        }
        $ok = $this->gallery->delete($id) > 0;
        Response::json(['success' => $ok, 'message' => $ok ? 'Image deleted.' : 'Delete failed.']);
    }
}
