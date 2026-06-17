<?php
/**
 * PackNGo — Blog Controller
 * GET /api/blog/posts
 * GET /api/blog/posts?category=travel-tips&page=2
 * GET /api/blog/posts/:slug
 * GET /api/blog/categories
 */
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/Database.php';
require_once dirname(__DIR__) . '/Models/BaseModel.php';
require_once dirname(__DIR__) . '/Models/BlogModel.php';
require_once dirname(__DIR__, 2) . '/helpers/Sanitizer.php';
require_once dirname(__DIR__, 2) . '/helpers/Response.php';

class BlogController
{
    private BlogModel $model;

    public function __construct()
    {
        $this->model = new BlogModel();
    }

    /** GET /api/blog/posts */
    public function posts(): void
    {
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $perPage  = min(20, max(3, (int)($_GET['per_page'] ?? 9)));
        $category = Sanitizer::slug($_GET['category'] ?? '', 60);

        $result = $this->model->getPublished($page, $perPage, $category);
        Response::json(['success' => true, 'data' => $result]);
    }

    /** GET /api/blog/posts/:slug */
    public function show(string $slug): void
    {
        $slug = Sanitizer::slug($slug, 120);
        $post = $this->model->findBySlug($slug);
        if (!$post) {
            Response::notFound('Blog post not found.');
        }
        Response::json(['success' => true, 'data' => $post]);
    }

    /** GET /api/blog/categories */
    public function categories(): void
    {
        $cats = $this->model->getCategories();
        Response::json(['success' => true, 'data' => $cats]);
    }

    /** GET /api/blog/featured */
    public function featured(): void
    {
        $posts = $this->model->getFeatured();
        Response::json(['success' => true, 'data' => $posts]);
    }
}
