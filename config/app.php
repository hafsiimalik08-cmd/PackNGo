<?php
/**
 * PackNGo — Application Configuration
 * Loads .env and exposes typed config values.
 */

declare(strict_types=1);

// ── Load .env ──────────────────────────────────────────────
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
            if (function_exists('putenv')) {
                @putenv("$key=$value");
            }
        }
    }
}

// ── Load config.php fallback (useful if .env is hidden on free hosting) ──
$customConfig = dirname(__DIR__) . '/config.php';
if (file_exists($customConfig)) {
    $custom = require $customConfig;
    if (is_array($custom)) {
        foreach ($custom as $key => $value) {
            $_ENV[$key]    = (string)$value;
            $_SERVER[$key] = (string)$value;
            if (function_exists('putenv')) {
                @putenv("$key=$value");
            }
        }
    }
}


function env(string $key, mixed $default = null): mixed
{
    $val = $_ENV[$key] ?? getenv($key);
    if ($val === false || $val === null) {
        return $default;
    }
    return match (strtolower((string) $val)) {
        'true', '1', 'yes' => true,
        'false', '0', 'no' => false,
        'null', ''         => null,
        default            => $val,
    };
}

// ── Global config array ────────────────────────────────────
return [

    'app' => [
        'name'      => env('APP_NAME', 'PackNGo'),
        'env'       => env('APP_ENV', 'production'),
        'url'       => env('APP_URL', 'http://localhost/PackNGo'),
        'debug'     => env('APP_DEBUG', false),
        'timezone'  => env('APP_TIMEZONE', 'Asia/Karachi'),
        'secret'    => env('APP_SECRET_KEY', ''),
    ],

    'db' => [
        'host'    => env('DB_HOST', '127.0.0.1'),
        'port'    => (int) env('DB_PORT', 3306),
        'name'    => env('DB_NAME', 'packngo_db'),
        'user'    => env('DB_USER', 'root'),
        'pass'    => env('DB_PASS', ''),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
    ],

    'mail' => [
        'host'         => env('MAIL_HOST', 'smtp.gmail.com'),
        'port'         => (int) env('MAIL_PORT', 587),
        'encryption'   => env('MAIL_ENCRYPTION', 'tls'),
        'username'     => env('MAIL_USERNAME', ''),
        'password'     => env('MAIL_PASSWORD', ''),
        'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@packngo.store'),
        'from_name'    => env('MAIL_FROM_NAME', 'PackNGo'),
        'admin_email'  => env('ADMIN_EMAIL', 'concierge@packngo.store'),
    ],

    'security' => [
        'session_lifetime'   => (int) env('SESSION_LIFETIME', 7200),
        'csrf_lifetime'      => (int) env('CSRF_TOKEN_LIFETIME', 3600),
        'bcrypt_rounds'      => (int) env('BCRYPT_ROUNDS', 12),
        'rate_limit_requests'=> (int) env('RATE_LIMIT_REQUESTS', 10),
        'rate_limit_window'  => (int) env('RATE_LIMIT_WINDOW', 3600),
    ],

    'upload' => [
        'max_size'      => (int) env('UPLOAD_MAX_SIZE', 5242880),
        'allowed_types' => explode(',', env('ALLOWED_IMAGE_TYPES', 'jpg,jpeg,png,webp')),
        'path'          => dirname(__DIR__) . '/public/uploads/',
    ],

    'paths' => [
        'root'    => dirname(__DIR__),
        'public'  => dirname(__DIR__) . '/public',
        'logs'    => dirname(__DIR__) . '/logs',
        'storage' => dirname(__DIR__) . '/storage',
    ],
];
