<?php
/**
 * PackNGo — Gallery Controller
 * GET /api/gallery           → all active images
 * GET /api/gallery?cat=beach → by category
 */
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/Database.php';
require_once dirname(__DIR__) . '/Models/BaseModel.php';
require_once dirname(__DIR__) . '/Models/GalleryModel.php';
require_once dirname(__DIR__, 2) . '/helpers/Sanitizer.php';
require_once dirname(__DIR__, 2) . '/helpers/Response.php';

class GalleryController
{
    private GalleryModel $model;

    public function __construct()
    {
        $this->model = new GalleryModel();
    }

    public function index(): void
    {
        $category = Sanitizer::string($_GET['cat'] ?? '', 60);
        $images   = $this->model->getActive($category ?: null);
        Response::json(['success' => true, 'data' => $images, 'count' => count($images)]);
    }
}
