<?php
/**
 * PackNGo — Shared Auth Page Bootstrap
 * Included at the top of every auth/*.php page.
 * Starts the session, generates a CSRF token, and enforces session timeout.
 */
declare(strict_types=1);

// PackNGo project root (auth/ lives one level below it)
$backendRoot = dirname(__DIR__);

$config = require $backendRoot . '/config/app.php';

require_once $backendRoot . '/helpers/Session.php';
require_once $backendRoot . '/middleware/CsrfMiddleware.php';

// Start session
Session::start();

// Session timeout: if a logged-in user has been idle longer than the
// configured lifetime, force them back to login.
$sessionLifetime = $config['security']['session_lifetime'];
if (Session::isLoggedIn()) {
    $lastActivity = (int) Session::get('last_activity', time());
    if (time() - $lastActivity > $sessionLifetime) {
        Session::destroy();
        header('Location: ' . rtrim($config['app']['url'], '/') . '/auth/login.php?timeout=1');
        exit;
    }
    Session::set('last_activity', time());
}

// Values available to every auth page
$csrfToken = CsrfMiddleware::generate();
$appUrl    = rtrim($config['app']['url'], '/');
$user      = Session::user();
