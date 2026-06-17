<?php
/**
 * PackNGo — Admin Controller (Complete)
 */
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/Database.php';
require_once dirname(__DIR__) . '/Models/BaseModel.php';
require_once dirname(__DIR__) . '/Models/ReservationModel.php';
require_once dirname(__DIR__) . '/Models/UserModel.php';
require_once dirname(__DIR__) . '/Models/ContactModel.php';
require_once dirname(__DIR__) . '/Models/NewsletterModel.php';
require_once dirname(__DIR__) . '/Models/DestinationModel.php';
require_once dirname(__DIR__) . '/Models/PackageModel.php';
require_once dirname(__DIR__) . '/Models/BlogModel.php';
require_once dirname(__DIR__) . '/Models/GalleryModel.php';
require_once dirname(__DIR__, 2) . '/helpers/Sanitizer.php';
require_once dirname(__DIR__, 2) . '/helpers/Validator.php';
require_once dirname(__DIR__, 2) . '/helpers/Response.php';
require_once dirname(__DIR__, 2) . '/helpers/Mailer.php';
require_once dirname(__DIR__, 2) . '/helpers/Session.php';
require_once dirname(__DIR__, 2) . '/middleware/AdminAuthMiddleware.php';

class AdminController
{
    private ReservationModel $resModel;
    private UserModel        $userModel;
    private ContactModel     $contactModel;
    private NewsletterModel  $nlModel;
    private DestinationModel $destModel;
    private PackageModel     $pkgModel;
    private BlogModel        $blogModel;
    private GalleryModel     $galleryModel;
    private Database         $db;

    public function __construct()
    {
        AdminAuthMiddleware::require();
        $this->db           = Database::getInstance();
        $this->resModel     = new ReservationModel();
        $this->userModel    = new UserModel();
        $this->contactModel = new ContactModel();
        $this->nlModel      = new NewsletterModel();
        $this->destModel    = new DestinationModel();
        $this->pkgModel     = new PackageModel();
        $this->blogModel    = new BlogModel();
        $this->galleryModel = new GalleryModel();
    }

    // ── Dashboard ──────────────────────────────────────────

    public function dashboard(): void
    {
        $resSummary  = $this->resModel->getSummary();
        $recentRes   = $this->resModel->listForAdmin(1, 8);
        $unreadMsgs  = count($this->contactModel->getUnread());
        $nlCount     = $this->nlModel->count(['is_active' => 1]);
        $totalUsers  = $this->userModel->count(['role' => 'customer']);
        $totalDests  = $this->destModel->count(['is_active' => 1]);
        $totalPkgs   = $this->pkgModel->count(['is_active' => 1]);
        $totalVisitors = (int)($this->db->fetchOne("SELECT COUNT(*) AS cnt FROM visitors")['cnt'] ?? 0);

        $monthlyRevenue = $this->db->fetchAll(
            "SELECT DATE_FORMAT(created_at,'%b %Y') AS month,
                    SUM(total_price) AS revenue,
                    COUNT(*) AS bookings
               FROM reservations
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                AND status != 'cancelled'
              GROUP BY YEAR(created_at), MONTH(created_at)
              ORDER BY created_at ASC"
        );

        $recentUsers = $this->db->fetchAll(
            "SELECT id, first_name, last_name, email, created_at
               FROM users WHERE role='customer'
              ORDER BY created_at DESC LIMIT 5"
        );

        Response::json([
            'success' => true,
            'data'    => [
                'stats' => [
                    'reservations'    => $resSummary,
                    'total_users'     => $totalUsers,
                    'total_dests'     => $totalDests,
                    'total_packages'  => $totalPkgs,
                    'unread_messages' => $unreadMsgs,
                    'newsletter_subs' => $nlCount,
                    'total_visitors'  => $totalVisitors,
                ],
                'recent_reservations' => $recentRes['data'],
                'recent_users'        => $recentUsers,
                'monthly_revenue'     => $monthlyRevenue,
            ],
        ]);
    }

    // ── Reports ────────────────────────────────────────────

    public function reports(): void
    {
        $byPackage = $this->db->fetchAll(
            "SELECT COALESCE(p.name, r.package_text, 'No Package') AS package,
                    COUNT(*) AS bookings,
                    COALESCE(SUM(r.total_price),0) AS revenue
               FROM reservations r
          LEFT JOIN packages p ON p.id = r.package_id
              WHERE r.status != 'cancelled'
              GROUP BY package ORDER BY revenue DESC"
        );

        $byDest = $this->db->fetchAll(
            "SELECT COALESCE(d.name, r.destination_text, 'Unknown') AS destination,
                    COUNT(*) AS bookings,
                    COALESCE(SUM(r.total_price),0) AS revenue
               FROM reservations r
          LEFT JOIN destinations d ON d.id = r.destination_id
              WHERE r.status != 'cancelled'
              GROUP BY destination ORDER BY bookings DESC LIMIT 10"
        );

        $monthly = $this->db->fetchAll(
            "SELECT DATE_FORMAT(created_at,'%Y-%m') AS month_key,
                    DATE_FORMAT(created_at,'%b %Y')  AS month_label,
                    COUNT(*) AS total,
                    SUM(status='confirmed')  AS confirmed,
                    SUM(status='completed')  AS completed,
                    SUM(status='cancelled')  AS cancelled,
                    COALESCE(SUM(CASE WHEN status!='cancelled' THEN total_price ELSE 0 END),0) AS revenue
               FROM reservations
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
              GROUP BY month_key, month_label
              ORDER BY month_key ASC"
        );

        // Daily visitors (last 14 days)
        $dailyVisitors = $this->db->fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y-%m-%d') AS date_key,
                    DATE_FORMAT(created_at, '%b %d') AS date_label,
                    COUNT(*) AS visitor_count
               FROM visitors
              WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
              GROUP BY date_key, date_label
              ORDER BY date_key ASC"
        );

        // Weekly visitors (last 8 weeks)
        $weeklyVisitors = $this->db->fetchAll(
            "SELECT YEARWEEK(created_at, 1) AS week_key,
                    DATE_FORMAT(MIN(created_at), 'Week %v (%b %d)') AS week_label,
                    COUNT(*) AS visitor_count
               FROM visitors
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL 8 WEEK)
              GROUP BY week_key
              ORDER BY week_key ASC"
        );

        // Monthly visitors (last 12 months)
        $monthlyVisitors = $this->db->fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_key,
                    DATE_FORMAT(created_at, '%b %Y') AS month_label,
                    COUNT(*) AS visitor_count
               FROM visitors
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
              GROUP BY month_key, month_label
              ORDER BY month_key ASC"
        );

        // Top pages (activity stats)
        $topPages = $this->db->fetchAll(
            "SELECT page_url, COUNT(*) AS visit_count
               FROM page_visits
              GROUP BY page_url
              ORDER BY visit_count DESC
              LIMIT 10"
        );

        // Recent activity logs
        $recentActivity = $this->db->fetchAll(
            "SELECT pv.visited_at, pv.page_url, v.ip_address, v.user_agent
               FROM page_visits pv
               JOIN visitors v ON v.id = pv.visitor_id
              ORDER BY pv.visited_at DESC
              LIMIT 15"
        );

        Response::json(['success' => true, 'data' => [
            'by_package'       => $byPackage,
            'by_destination'   => $byDest,
            'monthly'          => $monthly,
            'summary'          => $this->resModel->getSummary(),
            'daily_visitors'   => $dailyVisitors,
            'weekly_visitors'  => $weeklyVisitors,
            'monthly_visitors' => $monthlyVisitors,
            'top_pages'        => $topPages,
            'recent_activity'  => $recentActivity,
        ]]);
    }

    // ── Users ──────────────────────────────────────────────

    public function listUsers(): void
    {
        $page   = max(1, (int)($_GET['page']  ?? 1));
        $limit  = min(50, max(10, (int)($_GET['limit'] ?? 20)));
        $search = Sanitizer::string($_GET['search'] ?? '', 100);
        $status = Sanitizer::string($_GET['status'] ?? '', 10);
        $offset = ($page - 1) * $limit;

        $where  = "WHERE role = 'customer'";
        $params = [':limit' => $limit, ':offset' => $offset];

        if ($search !== '') {
            $where .= " AND (first_name LIKE :s OR last_name LIKE :s OR email LIKE :s)";
            $params[':s'] = '%' . $search . '%';
        }
        if ($status === 'active')   { $where .= " AND is_active = 1"; }
        if ($status === 'inactive') { $where .= " AND is_active = 0"; }
        if ($status === 'verified') { $where .= " AND email_verified = 1"; }

        $cntP  = array_diff_key($params, [':limit' => 0, ':offset' => 0]);
        $total = (int)($this->db->fetchOne("SELECT COUNT(*) AS cnt FROM users $where", $cntP)['cnt'] ?? 0);

        $rows = $this->db->fetchAll(
            "SELECT id, first_name, last_name, email, phone, is_active, email_verified, created_at
               FROM users $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset",
            $params
        );

        Response::json(['success' => true, 'data' => [
            'data'         => $rows,
            'total'        => $total,
            'per_page'     => $limit,
            'current_page' => $page,
            'last_page'    => max(1, (int)ceil($total / $limit)),
        ]]);
    }

    public function getUser(int $id): void
    {
        $user = $this->db->fetchOne(
            "SELECT u.id, u.first_name, u.last_name, u.email, u.phone_code, u.phone,
                    u.role, u.is_active, u.email_verified, u.created_at,
                    COUNT(r.id) AS booking_count,
                    COALESCE(SUM(r.total_price),0) AS total_spent
               FROM users u
          LEFT JOIN reservations r ON r.user_id = u.id AND r.status != 'cancelled'
              WHERE u.id = :id GROUP BY u.id LIMIT 1",
            [':id' => $id]
        );
        if (!$user) { Response::notFound('User not found.'); }

        $bookings = $this->db->fetchAll(
            "SELECT r.booking_ref, r.departure_date, r.status, r.total_price, r.created_at,
                    COALESCE(d.name, r.destination_text) AS destination
               FROM reservations r
          LEFT JOIN destinations d ON d.id = r.destination_id
              WHERE r.user_id = :uid ORDER BY r.created_at DESC LIMIT 10",
            [':uid' => $id]
        );

        Response::json(['success' => true, 'data' => ['user' => $user, 'bookings' => $bookings]]);
    }

    public function updateUser(int $id): void
    {
        $raw    = (array) json_decode(file_get_contents('php://input'), true);
        $update = [];
        if (isset($raw['first_name'])) $update['first_name'] = Sanitizer::string($raw['first_name'], 50);
        if (isset($raw['last_name']))  $update['last_name']  = Sanitizer::string($raw['last_name'],  50);
        if (isset($raw['phone']))      $update['phone']      = Sanitizer::string($raw['phone'], 20);
        if (isset($raw['is_active']))  $update['is_active']  = (int)(bool)$raw['is_active'];
        if (empty($update)) { Response::json(['success' => false, 'message' => 'Nothing to update.'], 422); }
        $ok = $this->userModel->update($id, $update) >= 0;
        Response::json(['success' => $ok, 'message' => $ok ? 'User updated.' : 'User not found.']);
    }

    public function toggleUserBlock(int $id): void
    {
        $user = $this->userModel->findById($id);
        if (!$user) { Response::notFound('User not found.'); }
        if ($user['role'] === 'admin') { Response::forbidden('Cannot block admin accounts.'); }
        $new = $user['is_active'] ? 0 : 1;
        $this->userModel->update($id, ['is_active' => $new]);
        Response::json(['success' => true, 'is_active' => $new, 'message' => $new ? 'User activated.' : 'User blocked.']);
    }

    public function deleteUser(int $id): void
    {
        $user = $this->userModel->findById($id);
        if (!$user) { Response::notFound('User not found.'); }
        if ($user['role'] === 'admin') { Response::forbidden('Cannot delete admin accounts.'); }
        $this->db->execute("UPDATE reservations SET user_id = NULL WHERE user_id = :id", [':id' => $id]);
        $ok = $this->userModel->delete($id) > 0;
        Response::json(['success' => $ok, 'message' => $ok ? 'User deleted.' : 'Failed.']);
    }

    // ── Reservations ───────────────────────────────────────

    public function listReservations(): void
    {
        $page   = max(1, (int)($_GET['page']  ?? 1));
        $limit  = min(50, max(5, (int)($_GET['limit'] ?? 20)));
        $status = Sanitizer::string($_GET['status'] ?? '', 20);
        $search = Sanitizer::string($_GET['search'] ?? '', 100);
        $offset = ($page - 1) * $limit;

        $where  = '1=1';
        $params = [':limit' => $limit, ':offset' => $offset];

        if (in_array($status, ['pending','confirmed','cancelled','completed'], true)) {
            $where .= " AND r.status = :status"; $params[':status'] = $status;
        }
        if ($search !== '') {
            $where .= " AND (r.booking_ref LIKE :s OR r.first_name LIKE :s OR r.last_name LIKE :s OR r.email LIKE :s)";
            $params[':s'] = '%' . $search . '%';
        }

        $cntP  = array_diff_key($params, [':limit' => 0, ':offset' => 0]);
        $total = (int)($this->db->fetchOne("SELECT COUNT(*) AS cnt FROM reservations r WHERE $where", $cntP)['cnt'] ?? 0);

        $rows = $this->db->fetchAll(
            "SELECT r.id, r.booking_ref, r.first_name, r.last_name, r.email,
                    r.departure_date, r.travellers, r.status, r.total_price, r.created_at,
                    COALESCE(d.name, r.destination_text) AS destination_name,
                    COALESCE(p.name, r.package_text)     AS package_name
               FROM reservations r
          LEFT JOIN destinations d ON d.id = r.destination_id
          LEFT JOIN packages      p ON p.id = r.package_id
              WHERE $where ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset",
            $params
        );

        Response::json(['success' => true, 'data' => [
            'data'         => $rows,
            'total'        => $total,
            'per_page'     => $limit,
            'current_page' => $page,
            'last_page'    => max(1, (int)ceil($total / $limit)),
        ]]);
    }

    public function getReservation(int $id): void
    {
        $res = $this->db->fetchOne(
            "SELECT r.*,
                    COALESCE(d.name, r.destination_text) AS destination_name,
                    COALESCE(p.name, r.package_text)     AS package_name,
                    CONCAT(u.first_name,' ',u.last_name) AS user_name
               FROM reservations r
          LEFT JOIN destinations d ON d.id = r.destination_id
          LEFT JOIN packages      p ON p.id = r.package_id
          LEFT JOIN users         u ON u.id = r.user_id
              WHERE r.id = :id LIMIT 1",
            [':id' => $id]
        );
        if (!$res) { Response::notFound('Reservation not found.'); }
        Response::json(['success' => true, 'data' => $res]);
    }

    public function updateReservationStatus(int $id): void
    {
        $raw    = (array) json_decode(file_get_contents('php://input'), true);
        $status = Sanitizer::string($raw['status'] ?? '', 20);
        $notes  = Sanitizer::textarea($raw['admin_notes'] ?? '', 1000);
        $price  = isset($raw['total_price']) && $raw['total_price'] !== '' ? round((float)$raw['total_price'], 2) : null;

        if (!in_array($status, ['pending','confirmed','cancelled','completed'], true)) {
            Response::json(['success' => false, 'message' => 'Invalid status.'], 422);
        }

        $data = ['status' => $status, 'admin_notes' => $notes];
        if ($price !== null) $data['total_price'] = $price;

        $ok = $this->resModel->update($id, $data) >= 0;
        Response::json(['success' => $ok, 'message' => $ok ? 'Reservation updated.' : 'Not found.']);
    }

    public function deleteReservation(int $id): void
    {
        $ok = $this->resModel->delete($id) > 0;
        Response::json(['success' => $ok, 'message' => $ok ? 'Deleted.' : 'Not found.']);
    }

    // ── Destinations ───────────────────────────────────────

    public function listDestinations(): void
    {
        $dests = $this->destModel->findAll('sort_order', 'ASC');
        Response::json(['success' => true, 'data' => $dests]);
    }

    public function createDestination(): void
    {
        $raw = (array) json_decode(file_get_contents('php://input'), true);
        if (empty($raw['slug']) || empty($raw['name']) || empty($raw['category'])) {
            Response::json(['success' => false, 'message' => 'Name, Slug, and Category are required.'], 422);
        }
        $id = $this->destModel->insert([
            'slug'        => Sanitizer::slug($raw['slug'], 80),
            'name'        => Sanitizer::string($raw['name'], 100),
            'tagline'     => Sanitizer::string($raw['tagline'] ?? '', 200),
            'about'       => Sanitizer::textarea($raw['about'] ?? '', 2000),
            'category'    => Sanitizer::string($raw['category'], 20),
            'country'     => Sanitizer::string($raw['country'] ?? '', 80),
            'best_season' => Sanitizer::string($raw['best_season'] ?? '', 80),
            'currency'    => Sanitizer::string($raw['currency'] ?? '', 30),
            'language'    => Sanitizer::string($raw['language'] ?? '', 80),
            'climate'     => Sanitizer::string($raw['climate'] ?? '', 80),
            'timezone'    => Sanitizer::string($raw['timezone'] ?? '', 60),
            'is_active'   => (int)(bool)($raw['is_active'] ?? 1),
            'sort_order'  => (int)($raw['sort_order'] ?? 99),
        ]);
        Response::json(['success' => true, 'id' => $id, 'message' => 'Destination created.'], 201);
    }

    public function updateDestination(int $id): void
    {
        $raw     = (array) json_decode(file_get_contents('php://input'), true);
        $allowed = ['name','tagline','about','category','country','best_season','currency','language','climate','timezone','is_active','sort_order','image_path'];
        $data    = [];
        foreach ($allowed as $field) {
            if (!array_key_exists($field, $raw)) continue;
            $data[$field] = match($field) {
                'is_active','sort_order' => (int)$raw[$field],
                'about'                  => Sanitizer::textarea((string)$raw[$field], 2000),
                default                  => Sanitizer::string((string)$raw[$field], 200),
            };
        }
        $ok = $this->destModel->update($id, $data) >= 0;
        Response::json(['success' => $ok, 'message' => $ok ? 'Destination updated.' : 'Not found.']);
    }

    public function deleteDestination(int $id): void
    {
        $ok = $this->destModel->delete($id) > 0;
        Response::json(['success' => $ok, 'message' => $ok ? 'Deleted.' : 'Not found.']);
    }

    // ── Packages ───────────────────────────────────────────

    public function listPackages(): void
    {
        $pkgs = $this->pkgModel->findAll('sort_order', 'ASC');
        Response::json(['success' => true, 'data' => $pkgs]);
    }

    public function createPackage(): void
    {
        $raw = (array) json_decode(file_get_contents('php://input'), true);
        if (empty($raw['slug']) || empty($raw['name']) || !isset($raw['price_usd'])) {
            Response::json(['success' => false, 'message' => 'Name, Slug, and Price are required.'], 422);
        }
        $lines = is_array($raw['includes'] ?? null)
            ? $raw['includes']
            : explode("\n", (string)($raw['includes'] ?? ''));

        $id = $this->pkgModel->insert([
            'slug'          => Sanitizer::slug($raw['slug'], 80),
            'name'          => Sanitizer::string($raw['name'], 100),
            'tagline'       => Sanitizer::string($raw['tagline'] ?? '', 200),
            'description'   => Sanitizer::textarea($raw['description'] ?? '', 2000),
            'duration_days' => max(1, (int)($raw['duration_days'] ?? 7)),
            'price_usd'     => round(max(0, (float)$raw['price_usd']), 2),
            'icon'          => Sanitizer::string($raw['icon'] ?? '✦', 10),
            'includes'      => json_encode(array_values(array_filter(array_map('trim', $lines)))),
            'is_active'     => (int)(bool)($raw['is_active'] ?? 1),
            'is_featured'   => (int)(bool)($raw['is_featured'] ?? 0),
            'sort_order'    => (int)($raw['sort_order'] ?? 99),
        ]);
        Response::json(['success' => true, 'id' => $id, 'message' => 'Package created.'], 201);
    }

    public function updatePackage(int $id): void
    {
        $raw     = (array) json_decode(file_get_contents('php://input'), true);
        $allowed = ['name','tagline','description','duration_days','price_usd','icon','is_active','is_featured','sort_order'];
        $data    = [];
        foreach ($allowed as $field) {
            if (!array_key_exists($field, $raw)) continue;
            $data[$field] = match($field) {
                'price_usd'                      => round(max(0, (float)$raw[$field]), 2),
                'duration_days'                  => max(1, (int)$raw[$field]),
                'is_active','is_featured','sort_order' => (int)$raw[$field],
                'description'                    => Sanitizer::textarea((string)$raw[$field], 2000),
                default                          => Sanitizer::string((string)$raw[$field], 200),
            };
        }
        if (isset($raw['includes'])) {
            $lines = is_array($raw['includes'])
                ? $raw['includes']
                : explode("\n", (string)$raw['includes']);
            $data['includes'] = json_encode(array_values(array_filter(array_map('trim', $lines))));
        }
        $ok = $this->pkgModel->update($id, $data) >= 0;
        Response::json(['success' => $ok, 'message' => $ok ? 'Package updated.' : 'Not found.']);
    }

    public function deletePackage(int $id): void
    {
        $ok = $this->pkgModel->delete($id) > 0;
        Response::json(['success' => $ok, 'message' => $ok ? 'Deleted.' : 'Not found.']);
    }

    // ── Blog Posts ─────────────────────────────────────────

    public function listPosts(): void
    {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = min(30, max(5, (int)($_GET['limit'] ?? 15)));
        $offset = ($page - 1) * $limit;
        $total  = $this->blogModel->count([]);
        $rows   = $this->db->fetchAll(
            "SELECT p.id, p.title, p.slug, p.author, p.is_published, p.is_featured,
                    p.read_minutes, p.created_at, c.name AS category_name
               FROM blog_posts p LEFT JOIN blog_categories c ON c.id = p.category_id
              ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset",
            [':limit' => $limit, ':offset' => $offset]
        );
        Response::json(['success' => true, 'data' => [
            'data' => $rows, 'total' => $total, 'per_page' => $limit,
            'current_page' => $page, 'last_page' => max(1, (int)ceil($total / $limit)),
        ]]);
    }

    public function getPost(int $id): void
    {
        $post = $this->blogModel->findById($id);
        if (!$post) { Response::notFound('Post not found.'); }
        Response::json(['success' => true, 'data' => $post]);
    }

    public function createPost(): void
    {
        $raw = (array) json_decode(file_get_contents('php://input'), true);
        if (empty($raw['title']) || empty($raw['slug'])) {
            Response::json(['success' => false, 'message' => 'Title and Slug are required.'], 422);
        }
        $isPublished = (int)(bool)($raw['is_published'] ?? 0);
        $id = $this->blogModel->insert([
            'category_id'  => !empty($raw['category_id']) ? (int)$raw['category_id'] : null,
            'slug'         => Sanitizer::slug($raw['slug'], 120),
            'title'        => Sanitizer::string($raw['title'], 200),
            'excerpt'      => Sanitizer::string($raw['excerpt'] ?? '', 400),
            'content'      => strip_tags((string)($raw['content'] ?? ''), '<p><br><strong><em><ul><li><ol><h2><h3><blockquote><a>'),
            'author'       => Sanitizer::string($raw['author'] ?? 'PackNGo Team', 100),
            'read_minutes' => max(1, (int)($raw['read_minutes'] ?? 5)),
            'is_featured'  => (int)(bool)($raw['is_featured'] ?? 0),
            'is_published' => $isPublished,
            'published_at' => $isPublished ? date('Y-m-d H:i:s') : null,
        ]);
        Response::json(['success' => true, 'id' => $id, 'message' => 'Post created.'], 201);
    }

    public function updatePost(int $id): void
    {
        $raw     = (array) json_decode(file_get_contents('php://input'), true);
        $allowed = ['category_id','title','slug','excerpt','content','author','read_minutes','is_featured','is_published'];
        $data    = [];
        foreach ($allowed as $field) {
            if (!array_key_exists($field, $raw)) continue;
            $data[$field] = match($field) {
                'category_id','read_minutes'     => $raw[$field] !== null ? (int)$raw[$field] : null,
                'is_featured','is_published'     => (int)(bool)$raw[$field],
                'content'                        => strip_tags((string)$raw[$field], '<p><br><strong><em><ul><li><ol><h2><h3><blockquote><a>'),
                'slug'                           => Sanitizer::slug((string)$raw[$field], 120),
                default                          => Sanitizer::string((string)$raw[$field], 200),
            };
        }
        if (isset($data['is_published']) && $data['is_published']) {
            $existing = $this->blogModel->findById($id);
            if ($existing && !$existing['published_at']) {
                $data['published_at'] = date('Y-m-d H:i:s');
            }
        }
        $ok = $this->blogModel->update($id, $data) >= 0;
        Response::json(['success' => $ok, 'message' => $ok ? 'Post updated.' : 'Not found.']);
    }

    public function deletePost(int $id): void
    {
        $ok = $this->blogModel->delete($id) > 0;
        Response::json(['success' => $ok, 'message' => $ok ? 'Deleted.' : 'Not found.']);
    }

    public function listBlogCategories(): void
    {
        $cats = $this->db->fetchAll("SELECT * FROM blog_categories ORDER BY sort_order ASC");
        Response::json(['success' => true, 'data' => $cats]);
    }

    // ── Messages ───────────────────────────────────────────

    public function listMessages(): void
    {
        $all = ($_GET['all'] ?? '') === 'true';
        if ($all) {
            $page   = max(1, (int)($_GET['page'] ?? 1));
            $limit  = 20;
            $offset = ($page - 1) * $limit;
            $total  = $this->contactModel->count();
            $rows   = $this->db->fetchAll(
                "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT :l OFFSET :o",
                [':l' => $limit, ':o' => $offset]
            );
            Response::json(['success' => true, 'data' => [
                'data' => $rows, 'total' => $total, 'current_page' => $page,
                'last_page' => max(1, (int)ceil($total / $limit)), 'per_page' => $limit,
            ]]);
            return;
        }
        Response::json(['success' => true, 'data' => $this->contactModel->getUnread()]);
    }

    public function markMessageRead(int $id): void
    {
        $ok = $this->contactModel->markRead($id);
        Response::json(['success' => $ok, 'message' => $ok ? 'Marked as read.' : 'Not found.']);
    }

    public function deleteMessage(int $id): void
    {
        $ok = $this->contactModel->delete($id) > 0;
        Response::json(['success' => $ok, 'message' => $ok ? 'Deleted.' : 'Not found.']);
    }

    public function replyMessage(int $id): void
    {
        $raw = (array) json_decode(file_get_contents('php://input'), true);
        if (empty($raw)) {
            $raw = $_POST;
        }

        $v = new Validator($raw);
        $v->required('reply')->minLen('reply', 2)->maxLen('reply', 4000);

        if ($v->fails()) {
            Response::json(['success' => false, 'errors' => $v->errors()], 422);
            return;
        }

        $message = $this->contactModel->findById($id);
        if (!$message) {
            Response::notFound('Message not found.');
            return;
        }

        $replyText = Sanitizer::textarea((string)$raw['reply'], 4000);
        $admin     = Session::user();
        $adminId   = $admin['id'] ?? null;

        try {
            $saved = $this->contactModel->saveReply($id, $replyText, $adminId);

            if (!$saved) {
                Response::json(['success' => false, 'message' => 'Failed to save reply.'], 500);
                return;
            }

            $emailSent = true;
            try {
                $emailSent = Mailer::sendContactReply(
                    $message['email'],
                    $message['name'],
                    (string)($message['subject'] ?? ''),
                    (string)$message['message'],
                    $replyText
                );
            } catch (Throwable $e) {
                $emailSent = false;
                error_log('[AdminController] reply email failed: ' . $e->getMessage());
            }

            Response::json([
                'success'     => true,
                'message'     => $emailSent
                    ? 'Reply sent and emailed to the sender.'
                    : 'Reply saved, but the notification email could not be sent.',
            ]);
        } catch (Throwable $e) {
            error_log('[AdminController] replyMessage failed: ' . $e->getMessage());
            Response::json(['success' => false, 'message' => 'Failed to send reply. Please try again.'], 500);
        }
    }

    // ── Newsletter Subscribers ─────────────────────────────

    public function listSubscribers(): void
    {
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $result = $this->nlModel->paginate($page, 30, ['is_active' => 1], 'subscribed_at', 'DESC');
        Response::json(['success' => true, 'data' => $result]);
    }

    public function deleteSubscriber(int $id): void
    {
        $ok = $this->nlModel->delete($id) > 0;
        Response::json(['success' => $ok, 'message' => $ok ? 'Removed.' : 'Not found.']);
    }
}
