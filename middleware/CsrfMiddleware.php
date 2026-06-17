<?php
/**
 * PackNGo — CSRF Middleware
 *
 * Double-submit cookie pattern:
 *   1. A token is stored in the session.
 *   2. The frontend sends it as X-CSRF-Token header OR _csrf POST field.
 *   3. We verify they match.
 *
 * For AJAX requests from the static HTML frontend, include the token
 * in every POST call as a header or body field.
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/helpers/Session.php';
require_once dirname(__DIR__) . '/helpers/Response.php';

class CsrfMiddleware
{
    private const TOKEN_KEY     = 'csrf_token';
    private const TOKEN_EXPIRY  = 'csrf_expiry';
    private const LIFETIME      = 3600; // 1 hour

    /**
     * Generate (or refresh) a CSRF token and return it.
     * Store it in the session so we can verify on POST.
     */
    public static function generate(): string
    {
        Session::start();
        // Rotate token if expired or not set
        if (
            !Session::has(self::TOKEN_KEY) ||
            time() > (int)Session::get(self::TOKEN_EXPIRY, 0)
        ) {
            Session::set(self::TOKEN_KEY, bin2hex(random_bytes(32)));
            Session::set(self::TOKEN_EXPIRY, time() + self::LIFETIME);
        }
        return (string) Session::get(self::TOKEN_KEY);
    }

    /**
     * Verify the CSRF token on mutating requests.
     * Call this at the start of every POST/PUT/DELETE handler.
     */
    public static function verify(): void
    {
        // Skip CSRF for safe methods
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return;
        }

        Session::start();

        // Accept token from header (AJAX) or POST body
        $submitted = $_SERVER['HTTP_X_CSRF_TOKEN']
            ?? $_POST['_csrf']
            ?? (json_decode(file_get_contents('php://input'), true)['_csrf'] ?? '');

        $stored = Session::get(self::TOKEN_KEY, '');

        if (
            empty($submitted) ||
            empty($stored) ||
            !hash_equals($stored, $submitted) ||
            time() > (int)Session::get(self::TOKEN_EXPIRY, 0)
        ) {
            // Rotate token on failure
            Session::forget(self::TOKEN_KEY);
            Session::forget(self::TOKEN_EXPIRY);
            Response::json(['success' => false, 'message' => 'CSRF token invalid or expired. Please refresh the page.'], 403);
        }

        // Rotate token after successful use (single-use pattern)
        Session::set(self::TOKEN_KEY, bin2hex(random_bytes(32)));
        Session::set(self::TOKEN_EXPIRY, time() + self::LIFETIME);
    }
}
