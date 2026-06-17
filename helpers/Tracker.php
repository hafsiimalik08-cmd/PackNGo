<?php
/**
 * PackNGo — Visitor Tracker Helper
 */
declare(strict_types=1);

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Session.php';

class Tracker
{
    public static function track(): void
    {
        // Start the session
        Session::start();

        // Check if we are running in CLI or tracking an admin request
        if (php_sapi_name() === 'cli') {
            return;
        }

        // We do not track admin panel requests to avoid cluttering stats
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (str_contains($uri, '/admin') || str_contains($uri, '/api/admin')) {
            return;
        }

        // Only track HTML page views and the root directory
        $path = parse_url($uri, PHP_URL_PATH);
        $extName = pathinfo($path, PATHINFO_EXTENSION);
        $isHtml = ($extName === 'html' || $extName === 'php');
        
        // Check if it's the root directory (e.g. /PackNGo/ or /)
        $isRoot = false;
        $cleanPath = trim((string)$path, '/');
        if ($cleanPath === 'PackNGo' || $cleanPath === '') {
            $isRoot = true;
        }

        if (!$isHtml && !$isRoot) {
            return; // Skip API calls, static assets, images, etc.
        }

        // Clean page URL (e.g., /PackNGo/About.html -> About.html)
        $pageUrl = basename((string)$path);
        if ($pageUrl === '' || $pageUrl === 'PackNGo' || $pageUrl === 'index.php') {
            $pageUrl = 'index.html';
        }

        // One-time check per session to auto-create tables if they don't exist
        if (!Session::get('visitor_tables_checked')) {
            try {
                $db = Database::getInstance();
                $tableExists = $db->fetchOne("SHOW TABLES LIKE 'visitors'");
                if (!$tableExists) {
                    $db->execute("
                        CREATE TABLE IF NOT EXISTS `visitors` (
                            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                            `session_id` VARCHAR(255) NOT NULL,
                            `user_id` INT UNSIGNED DEFAULT NULL,
                            `ip_address` VARCHAR(45) DEFAULT NULL,
                            `user_agent` VARCHAR(300) DEFAULT NULL,
                            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `uq_visitor_session` (`session_id`),
                            CONSTRAINT `fk_visitor_user` FOREIGN KEY (`user_id`)
                                REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ");
                    $db->execute("
                        CREATE TABLE IF NOT EXISTS `page_visits` (
                            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                            `visitor_id` INT UNSIGNED NOT NULL,
                            `page_url` VARCHAR(255) NOT NULL,
                            `referrer` VARCHAR(255) DEFAULT NULL,
                            `visited_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`),
                            CONSTRAINT `fk_visit_visitor` FOREIGN KEY (`visitor_id`)
                                REFERENCES `visitors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ");
                }
                Session::set('visitor_tables_checked', true);
            } catch (Throwable $e) {
                error_log('[PackNGo Tracker Schema] ' . $e->getMessage());
            }
        }

        // Get or generate session ID
        $sessionId = session_id();
        if (empty($sessionId)) {
            return;
        }

        // Obtain visitor IP, User Agent, and Referrer
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $referrer  = $_SERVER['HTTP_REFERER'] ?? null;

        // Check if visitor is logged in (for future compatibility)
        $userId = null;
        if (Session::isLoggedIn()) {
            $user = Session::user();
            $userId = isset($user['id']) ? (int)$user['id'] : null;
        }

        try {
            $db = Database::getInstance();

            // Find or create visitor record
            $visitor = $db->fetchOne(
                "SELECT id, user_id FROM visitors WHERE session_id = :session_id LIMIT 1",
                [':session_id' => $sessionId]
            );

            $visitorId = null;
            if ($visitor) {
                $visitorId = (int)$visitor['id'];
                // Update user_id if they logged in during this session
                if ($userId !== null && $visitor['user_id'] === null) {
                    $db->execute(
                        "UPDATE visitors SET user_id = :user_id WHERE id = :id",
                        [':user_id' => $userId, ':id' => $visitorId]
                    );
                }
            } else {
                // Insert new visitor
                $db->execute(
                    "INSERT INTO visitors (session_id, user_id, ip_address, user_agent, created_at, updated_at)
                     VALUES (:session_id, :user_id, :ip_address, :user_agent, NOW(), NOW())",
                    [
                        ':session_id' => $sessionId,
                        ':user_id'     => $userId,
                        ':ip_address'  => $ipAddress,
                        ':user_agent'  => $userAgent ? substr((string)$userAgent, 0, 300) : null
                    ]
                );
                $visitorId = $db->lastInsertId();
            }

            if ($visitorId) {
                // Avoid logging duplicate page refresh within 10 seconds to keep stats clean
                $lastVisit = $db->fetchOne(
                    "SELECT page_url, visited_at FROM page_visits 
                     WHERE visitor_id = :visitor_id 
                     ORDER BY visited_at DESC LIMIT 1",
                    [':visitor_id' => $visitorId]
                );

                $shouldLog = true;
                if ($lastVisit && $lastVisit['page_url'] === $pageUrl) {
                    $timeDiff = time() - strtotime((string)$lastVisit['visited_at']);
                    if ($timeDiff < 10) {
                        $shouldLog = false;
                    }
                }

                if ($shouldLog) {
                    $db->execute(
                        "INSERT INTO page_visits (visitor_id, page_url, referrer, visited_at)
                         VALUES (:visitor_id, :page_url, :referrer, NOW())",
                        [
                            ':visitor_id' => $visitorId,
                            ':page_url'    => $pageUrl,
                            ':referrer'    => $referrer ? substr((string)$referrer, 0, 255) : null
                        ]
                    );
                }
            }
        } catch (Throwable $e) {
            // Log database error silently to not disrupt the visitor
            error_log('[PackNGo Tracker] ' . $e->getMessage());
        }
    }
}
