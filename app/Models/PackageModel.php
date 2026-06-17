<?php
/**
 * PackNGo — Package Model
 */
declare(strict_types=1);
require_once __DIR__ . '/BaseModel.php';

class PackageModel extends BaseModel
{
    protected string $table = 'packages';

    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM packages WHERE slug = :slug AND is_active = 1 LIMIT 1",
            [':slug' => $slug]
        );
    }

    public function getActive(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM packages WHERE is_active = 1 ORDER BY sort_order ASC"
        );
    }

    public function getFeatured(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM packages WHERE is_active = 1 AND is_featured = 1 ORDER BY sort_order ASC"
        );
    }
}
