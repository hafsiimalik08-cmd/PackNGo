<?php
/**
 * PackNGo — Public Entry Point
 * All requests are routed through here via .htaccess.
 */

declare(strict_types=1);

// ── Visitor Tracking ───────────────────────────────────────
require_once __DIR__ . '/helpers/Tracker.php';
Tracker::track();

// ── Bootstrap ──────────────────────────────────────────────
date_default_timezone_set('Asia/Karachi');
$config = require __DIR__ . '/config/app.php';

if ($config['app']['debug']) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

ini_set('error_log', __DIR__ . '/logs/app.log');

// ── Security headers ─────────────────────────────────────
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header_remove('X-Powered-By');

// ── CORS ──────────────────────────────────────────────────
$allowedOrigins = [
    'http://localhost',
    'http://127.0.0.1',
    'http://localhost/PackNGo',
];
$appUrl = $config['app']['url'] ?? '';
if ($appUrl !== '') {
    $allowedOrigins[] = $appUrl;
    $parsed = parse_url($appUrl);
    if (isset($parsed['scheme'], $parsed['host'])) {
        $appOrigin = $parsed['scheme'] . '://' . $parsed['host'];
        if (isset($parsed['port'])) {
            $appOrigin .= ':' . $parsed['port'];
        }
        $allowedOrigins[] = $appOrigin;
        // Also allow alternative scheme (http vs https)
        $altScheme = $parsed['scheme'] === 'https' ? 'http' : 'https';
        $allowedOrigins[] = $altScheme . '://' . $parsed['host'] . (isset($parsed['port']) ? ':' . $parsed['port'] : '');
    }
}
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: {$origin}");
    header('Access-Control-Allow-Credentials: true');
}
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token, X-Requested-With, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Global exception handler ───────────────────────────────
set_exception_handler(function (Throwable $e) use ($config): void {
    error_log('[PackNGo Uncaught] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    $msg = $config['app']['debug']
        ? $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')'
        : 'An unexpected error occurred. Please try again.';
    echo json_encode(['success' => false, 'message' => $msg]);
    exit;
});

// ── Resolve the effective URI ─────────────────────────────
// Admin panel sends requests as: index.php?_url=/api/admin/dashboard
// We need to detect this and route via the _url param
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Check if there's a _url query param (from admin panel JS)
$urlParam = $_GET['_url'] ?? '';
if ($urlParam !== '') {
    // Override SERVER vars so the router sees the correct path
    $decoded = '/' . ltrim(urldecode($urlParam), '/');
    $_SERVER['REQUEST_URI'] = $decoded;
    $uri = $decoded;
}

// If the path contains /api/, dispatch to the API router
if (str_contains((string)$uri, '/api/')) {
    require __DIR__ . '/routes/api.php';
    exit;
}

// ── For non-API requests, serve frontend HTML ──
$filename = basename((string)$uri);
if ($filename === '' || $filename === 'PackNGo' || $filename === 'index.php') {
    $filename = 'index.html';
}
$file = __DIR__ . '/' . ltrim($filename, '/');
if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'html') {
    readfile($file);
    exit;
}

http_response_code(404);
echo json_encode(['success' => false, 'message' => 'Resource not found.']);
