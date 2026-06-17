<?php
/**
 * PackNGo — Gallery Model
 */
declare(strict_types=1);
require_once __DIR__ . '/BaseModel.php';

class GalleryModel extends BaseModel
{
    protected string $table = 'gallery_images';

    public function getActive(?string $category = null): array
    {
        if ($category) {
            return $this->db->fetchAll(
                "SELECT g.*, d.name AS destination_name
                   FROM gallery_images g
              LEFT JOIN destinations d ON d.id = g.destination_id
                  WHERE g.is_active = 1 AND g.category = :cat
                  ORDER BY g.sort_order ASC, g.id ASC",
                [':cat' => $category]
            );
        }
        return $this->db->fetchAll(
            "SELECT g.*, d.name AS destination_name
               FROM gallery_images g
          LEFT JOIN destinations d ON d.id = g.destination_id
              WHERE g.is_active = 1
              ORDER BY g.sort_order ASC, g.id ASC"
        );
    }
}
