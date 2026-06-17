<?php
/**
 * PackNGo — Admin Authentication Middleware
 * Guards all admin-only routes.
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/helpers/Session.php';
require_once dirname(__DIR__) . '/helpers/Response.php';

class AdminAuthMiddleware
{
    /**
     * Call at the top of any admin-only controller method.
     * Sends 401/403 and exits if the request is not authenticated as admin.
     */
    public static function require(): void
    {
        Session::start();

        if (!Session::isLoggedIn()) {
            Response::unauthorized('You must be logged in to access this resource.');
        }

        if (!Session::isAdmin()) {
            Response::forbidden('You do not have permission to access this resource.');
        }

        // Optional: check session hasn't expired past our config lifetime
        $config  = require dirname(__DIR__) . '/config/app.php';
        $lifetime = $config['security']['session_lifetime'];
        $lastActivity = (int) Session::get('last_activity', time());

        if (time() - $lastActivity > $lifetime) {
            Session::destroy();
            Response::unauthorized('Session expired. Please log in again.');
        }

        // Update last activity timestamp
        Session::set('last_activity', time());
    }
}
