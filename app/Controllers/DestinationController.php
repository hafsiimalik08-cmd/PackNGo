<?php
/**
 * PackNGo — Destination Controller
 * GET /api/destinations          → all active destinations
 * GET /api/destinations?cat=beach → by category
 * GET /api/destinations/search?q=paris
 * GET /api/packages              → all active packages
 */
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/Database.php';
require_once dirname(__DIR__) . '/Models/BaseModel.php';
require_once dirname(__DIR__) . '/Models/DestinationModel.php';
require_once dirname(__DIR__) . '/Models/PackageModel.php';
require_once dirname(__DIR__, 2) . '/helpers/Sanitizer.php';
require_once dirname(__DIR__, 2) . '/helpers/Response.php';

class DestinationController
{
    private DestinationModel $destModel;
    private PackageModel     $pkgModel;

    public function __construct()
    {
        $this->destModel = new DestinationModel();
        $this->pkgModel  = new PackageModel();
    }

    public function index(): void
    {
        $category = Sanitizer::string($_GET['cat'] ?? '', 30);
        $data     = $this->destModel->getActive($category);
        Response::json(['success' => true, 'data' => $data]);
    }

    public function search(): void
    {
        $q = Sanitizer::string($_GET['q'] ?? '', 100);
        if (strlen($q) < 2) {
            Response::json(['success' => false, 'message' => 'Query too short.'], 400);
            return;
        }
        $data = $this->destModel->search($q);
        Response::json(['success' => true, 'data' => $data, 'count' => count($data)]);
    }

    public function show(string $slug): void
    {
        $slug = Sanitizer::string($slug, 80);
        $dest = $this->destModel->findBySlug($slug);
        if (!$dest) {
            Response::json(['success' => false, 'message' => 'Destination not found.'], 404);
            return;
        }
        Response::json(['success' => true, 'data' => $dest]);
    }

    public function packages(): void
    {
        $data = $this->pkgModel->getActive();
        // Decode JSON includes field
        foreach ($data as &$pkg) {
            if (!empty($pkg['includes'])) {
                $pkg['includes'] = json_decode($pkg['includes'], true);
            }
        }
        Response::json(['success' => true, 'data' => $data]);
    }
}
