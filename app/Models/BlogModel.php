<?php
/**
 * PackNGo — Blog Model
 */
declare(strict_types=1);
require_once __DIR__ . '/BaseModel.php';

class BlogModel extends BaseModel
{
    protected string $table = 'blog_posts';

    public function getPublished(int $page = 1, int $perPage = 9, string $category = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [':limit' => $perPage, ':offset' => $offset];
        $catJoin  = '';

        if ($category !== '') {
            $catJoin            = "JOIN blog_categories bc2 ON bc2.id = p.category_id AND bc2.slug = :catslug";
            $params[':catslug'] = $category;
        }

        $countParams = $category !== '' ? [':catslug' => $category] : [];
        $totalRow = $this->db->fetchOne(
            "SELECT COUNT(*) AS cnt FROM blog_posts p {$catJoin} WHERE p.is_published = 1",
            $countParams
        );

        $rows = $this->db->fetchAll(
            "SELECT p.*, bc.name AS category_name, bc.slug AS category_slug
               FROM blog_posts p
          LEFT JOIN blog_categories bc ON bc.id = p.category_id
                    {$catJoin}
              WHERE p.is_published = 1
           ORDER BY p.is_featured DESC, p.published_at DESC
              LIMIT :limit OFFSET :offset",
            $params
        );

        $total = (int)($totalRow['cnt'] ?? 0);
        return [
            'data'         => $rows,
            'total'        => $total,
            'current_page' => $page,
            'last_page'    => max(1, (int) ceil($total / $perPage)),
        ];
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetchOne(
            "SELECT p.*, bc.name AS category_name, bc.slug AS category_slug
               FROM blog_posts p
          LEFT JOIN blog_categories bc ON bc.id = p.category_id
              WHERE p.slug = :slug AND p.is_published = 1
              LIMIT 1",
            [':slug' => $slug]
        );
    }

    public function getFeatured(): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, bc.name AS category_name
               FROM blog_posts p
          LEFT JOIN blog_categories bc ON bc.id = p.category_id
              WHERE p.is_published = 1 AND p.is_featured = 1
           ORDER BY p.published_at DESC
              LIMIT 3"
        );
    }

    public function getCategories(): array
    {
        return $this->db->fetchAll(
            "SELECT bc.*, COUNT(p.id) AS post_count
               FROM blog_categories bc
          LEFT JOIN blog_posts p ON p.category_id = bc.id AND p.is_published = 1
              GROUP BY bc.id
              ORDER BY bc.sort_order ASC"
        );
    }
}
