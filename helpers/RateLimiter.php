<?php
/**
 * PackNGo — IP-Based Rate Limiter
 * Uses the rate_limit_log table to track requests per IP per action per window.
 */
declare(strict_types=1);

require_once __DIR__ . '/Response.php';
require_once dirname(__DIR__) . '/config/Database.php';

class RateLimiter
{
    /**
     * Check the rate limit; calls Response::tooManyRequests() and exits if exceeded.
     *
     * @param string $action      Identifier, e.g. 'reservation_submit'
     * @param string $ip          Client IP
     * @param int    $maxRequests Max hits allowed in $window seconds
     * @param int    $window      Window duration in seconds
     */
    public static function check(string $action, string $ip, int $maxRequests, int $window): void
    {
        try {
            $db = Database::getInstance();

            // Purge expired windows first
            $db->execute(
                "DELETE FROM rate_limit_log
                  WHERE window_start < DATE_SUB(NOW(), INTERVAL :window SECOND)",
                [':window' => $window]
            );

            // Try to increment existing row for this IP + action + current window
            $affected = $db->execute(
                "UPDATE rate_limit_log
                    SET hit_count = hit_count + 1
                  WHERE ip_address   = :ip
                    AND action       = :action
                    AND window_start >= DATE_SUB(NOW(), INTERVAL :window SECOND)",
                [':ip' => $ip, ':action' => $action, ':window' => $window]
            );

            if ($affected === 0) {
                // First hit in this window — insert row
                $db->execute(
                    "INSERT IGNORE INTO rate_limit_log (ip_address, action, hit_count, window_start)
                     VALUES (:ip, :action, 1, NOW())",
                    [':ip' => $ip, ':action' => $action]
                );
                return;
            }

            // Read current count
            $row = $db->fetchOne(
                "SELECT hit_count FROM rate_limit_log
                  WHERE ip_address   = :ip
                    AND action       = :action
                    AND window_start >= DATE_SUB(NOW(), INTERVAL :window SECOND)
                  LIMIT 1",
                [':ip' => $ip, ':action' => $action, ':window' => $window]
            );

            if ($row && (int)$row['hit_count'] > $maxRequests) {
                Response::tooManyRequests($window);
            }
        } catch (Throwable $e) {
            // Rate limiting failure should not block legitimate requests — log and continue
            error_log('[RateLimiter] ' . $e->getMessage());
        }
    }
}
