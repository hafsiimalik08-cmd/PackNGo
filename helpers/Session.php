<?php
/**
 * PackNGo — Session Helper
 * Centralises secure session configuration.
 */
declare(strict_types=1);

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $config = require dirname(__DIR__) . '/config/app.php';

        // Secure session settings
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.gc_maxlifetime', (string)$config['security']['session_lifetime']);

        // Use Secure cookies only on HTTPS (production or detected HTTPS)
        $isHttps = ($config['app']['env'] === 'production')
            || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
            || (($_SERVER['SERVER_PORT'] ?? 0) == 443);
        if ($isHttps) {
            ini_set('session.cookie_secure', '1');
        }

        session_name('PACKNGO_SESS');
        session_start();
        self::$started = true;

        // General session timeout validation
        $lifetime = (int) ($config['security']['session_lifetime'] ?? 7200);
        $lastActivity = $_SESSION['last_activity'] ?? null;
        if ($lastActivity !== null && (time() - (int)$lastActivity > $lifetime)) {
            self::destroy();
            self::start();
        }
        $_SESSION['last_activity'] = time();
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        self::$started = false;
    }

    public static function isLoggedIn(): bool
    {
        return isset($_SESSION['user']['id']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? '') === 'admin';
    }
}
