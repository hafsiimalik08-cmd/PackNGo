<?php
/**
 * PackNGo — HTTP Response Helper
 */
declare(strict_types=1);

class Response
{
    /**
     * Send a JSON response and exit.
     */
    public static function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        // Do NOT expose server info
        header_remove('X-Powered-By');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Redirect and exit.
     */
    public static function redirect(string $url, int $status = 302): never
    {
        http_response_code($status);
        header('Location: ' . $url);
        exit;
    }

    /**
     * Send a 405 Method Not Allowed.
     */
    public static function methodNotAllowed(array $allowed = []): never
    {
        if (!empty($allowed)) {
            header('Allow: ' . implode(', ', $allowed));
        }
        self::json(['success' => false, 'message' => 'Method not allowed.'], 405);
    }

    /**
     * Send a 404.
     */
    public static function notFound(string $message = 'Not found.'): never
    {
        self::json(['success' => false, 'message' => $message], 404);
    }

    /**
     * 401 Unauthorised.
     */
    public static function unauthorized(string $message = 'Authentication required.'): never
    {
        self::json(['success' => false, 'message' => $message], 401);
    }

    /**
     * 403 Forbidden.
     */
    public static function forbidden(string $message = 'Access denied.'): never
    {
        self::json(['success' => false, 'message' => $message], 403);
    }

    /**
     * 429 Too Many Requests.
     */
    public static function tooManyRequests(int $retryAfter = 3600): never
    {
        header("Retry-After: {$retryAfter}");
        self::json(['success' => false, 'message' => 'Too many requests. Please try again later.'], 429);
    }
}
