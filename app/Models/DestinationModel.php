<?php
/**
 * PackNGo — Destination Model
 */
declare(strict_types=1);
require_once __DIR__ . '/BaseModel.php';

class DestinationModel extends BaseModel
{
    protected string $table = 'destinations';

    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM destinations WHERE slug = :slug AND is_active = 1 LIMIT 1",
            [':slug' => $slug]
        );
    }

    public function getActive(string $category = ''): array
    {
        if ($category !== '') {
            return $this->db->fetchAll(
                "SELECT * FROM destinations WHERE is_active = 1 AND category = :cat ORDER BY sort_order ASC",
                [':cat' => $category]
            );
        }
        return $this->db->fetchAll(
            "SELECT * FROM destinations WHERE is_active = 1 ORDER BY sort_order ASC"
        );
    }

    public function search(string $query): array
    {
        $q = '%' . $query . '%';
        return $this->db->fetchAll(
            "SELECT * FROM destinations
              WHERE is_active = 1
                AND (name LIKE :q OR tagline LIKE :q OR country LIKE :q OR category LIKE :q)
              ORDER BY sort_order ASC",
            [':q' => $q]
        );
    }

    public function getCategories(): array
    {
        return $this->db->fetchAll(
            "SELECT category, COUNT(*) AS total
               FROM destinations
              WHERE is_active = 1
              GROUP BY category
              ORDER BY category ASC"
        );
    }
}
