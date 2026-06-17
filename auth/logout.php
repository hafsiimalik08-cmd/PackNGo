<?php
/**
 * PackNGo — Logout
 * Destroys the session and redirects to the login page.
 */
declare(strict_types=1);
require_once __DIR__ . '/_header.php';

Session::destroy();
header('Location: ' . $appUrl . '/auth/login.php?logged_out=1');
exit;
