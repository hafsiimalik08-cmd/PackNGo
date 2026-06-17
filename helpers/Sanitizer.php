<?php
/**
 * PackNGo — Sanitizer
 * Central input cleaning — always sanitize before using user data.
 */
declare(strict_types=1);

class Sanitizer
{
    /**
     * General-purpose string: strip tags, trim, truncate.
     */
    public static function string(string $value, int $maxLen = 255): string
    {
        $value = strip_tags(trim($value));
        return mb_substr($value, 0, $maxLen);
    }

    /**
     * Proper name (letters, spaces, hyphens, apostrophes).
     */
    public static function name(string $value, int $maxLen = 100): string
    {
        $value = preg_replace('/[^\p{L}\s\'\-]/u', '', trim($value));
        return mb_substr((string)$value, 0, $maxLen);
    }

    /**
     * Email: lowercase + RFC filter.
     */
    public static function email(string $value): string
    {
        return strtolower(trim(filter_var($value, FILTER_SANITIZE_EMAIL)));
    }

    /**
     * Digits only.
     */
    public static function digits(string $value, int $maxLen = 20): string
    {
        $value = preg_replace('/[^0-9]/', '', $value);
        return mb_substr((string)$value, 0, $maxLen);
    }

    /**
     * Multi-line text: strip dangerous tags, preserve newlines, truncate.
     */
    public static function textarea(string $value, int $maxLen = 1000): string
    {
        $value = strip_tags(trim($value));
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
        return mb_substr((string)$value, 0, $maxLen);
    }

    /**
     * Safe integer from user input.
     */
    public static function int(mixed $value, int $min = 0, int $max = PHP_INT_MAX): int
    {
        $int = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        return max($min, min($max, $int));
    }

    /**
     * Get real client IP, aware of proxies.
     */
    public static function ip(): string
    {
        $keys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }

    /**
     * Slug: lowercase, alphanumeric + hyphens.
     */
    public static function slug(string $value, int $maxLen = 100): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\-]/', '-', $value);
        $value = preg_replace('/-+/', '-', (string)$value);
        return mb_substr(trim($value, '-'), 0, $maxLen);
    }

    /**
     * Boolean from various truthy representations.
     */
    public static function bool(mixed $value): bool
    {
        return in_array(strtolower((string)$value), ['1', 'true', 'yes', 'on'], true);
    }

    /**
     * Sanitize a filename for upload storage.
     */
    public static function filename(string $name): string
    {
        $name = preg_replace('/[^a-zA-Z0-9._\-]/', '_', basename($name));
        return mb_substr((string)$name, 0, 200);
    }
}
