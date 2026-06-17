<?php
/**
 * PackNGo — Newsletter Controller
 * POST /api/newsletter/subscribe
 * GET  /api/newsletter/unsubscribe?token=...
 */
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/Database.php';
require_once dirname(__DIR__) . '/Models/BaseModel.php';
require_once dirname(__DIR__) . '/Models/NewsletterModel.php';
require_once dirname(__DIR__, 2) . '/helpers/Validator.php';
require_once dirname(__DIR__, 2) . '/helpers/Sanitizer.php';
require_once dirname(__DIR__, 2) . '/helpers/RateLimiter.php';
require_once dirname(__DIR__, 2) . '/helpers/Response.php';
require_once dirname(__DIR__, 2) . '/middleware/CsrfMiddleware.php';

class NewsletterController
{
    private NewsletterModel $model;

    public function __construct()
    {
        $this->model = new NewsletterModel();
    }

    public function subscribe(): void
    {
        $ip = Sanitizer::ip();
        RateLimiter::check('newsletter_sub', $ip, 5, 3600);
        CsrfMiddleware::verify();

        $raw = $_POST;
        if (empty($raw)) {
            $raw = (array) json_decode(file_get_contents('php://input'), true);
        }

        $v = new Validator($raw);
        $v->required('email')->email('email')->maxLen('email', 120);
        if ($v->fails()) {
            Response::json(['success' => false, 'errors' => $v->errors()], 422);
            return;
        }

        $email = Sanitizer::email($raw['email']);
        $name  = !empty($raw['name']) ? Sanitizer::name($raw['name']) : null;

        try {
            $subscribed = $this->model->subscribe($email, $name, $ip);
        } catch (Throwable $e) {
            error_log('[Newsletter] ' . $e->getMessage());
            Response::json(['success' => false, 'message' => 'Subscription failed. Please try again.'], 500);
            return;
        }

        if (!$subscribed) {
            Response::json(['success' => false, 'message' => 'This email is already subscribed.'], 409);
            return;
        }

        Response::json(['success' => true, 'message' => 'You have been subscribed to our newsletter!']);
    }

    public function unsubscribe(): void
    {
        $token = Sanitizer::string($_GET['token'] ?? '', 60);
        if (empty($token)) {
            Response::json(['success' => false, 'message' => 'Invalid token.'], 400);
            return;
        }
        $ok = $this->model->unsubscribeByToken($token);
        Response::json([
            'success' => $ok,
            'message' => $ok ? 'You have been unsubscribed.' : 'Token not found or already unsubscribed.',
        ], $ok ? 200 : 404);
    }
}
