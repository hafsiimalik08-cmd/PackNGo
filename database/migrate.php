#!/usr/bin/env php
<?php
/**
 * PackNGo — Database Setup Script
 *
 * Run this once from the command line to create the database and all tables:
 *
 *   cd C:\xampp\htdocs\PackNGo
 *   php database/migrate.php
 *
 * Or open it in browser (XAMPP):
 *   http://localhost/PackNGo/database/migrate.php?secret=SETUP_SECRET
 *
 * IMPORTANT: Delete or move this file after setup.
 */

declare(strict_types=1);

// ── CLI or web with secret ─────────────────────────────────────────
$isCli = PHP_SAPI === 'cli';

if (!$isCli) {
    $secret = file_exists(dirname(__DIR__) . '/.env')
        ? (getenv('APP_SECRET_KEY') ?: 'SETUP_SECRET')
        : 'SETUP_SECRET';
    if (($_GET['secret'] ?? '') !== $secret) {
        http_response_code(403);
        die('403 Forbidden. Add ?secret=YOUR_APP_SECRET_KEY to the URL.');
    }
    header('Content-Type: text/plain; charset=utf-8');
}

function out(string $msg): void {
    echo $msg . PHP_EOL;
    if (PHP_SAPI !== 'cli') flush();
}

// ── Load config ────────────────────────────────────────────────────
$config = require dirname(__DIR__) . '/config/app.php';
$db     = $config['db'];

out("═══════════════════════════════════════════");
out("  PackNGo — Database Migration");
out("═══════════════════════════════════════════");
out("Host:     " . $db['host']);
out("Database: " . $db['name']);
out("User:     " . $db['user']);
out("");

// ── Connect to the database ────────────────────────
try {
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $db['host'], $db['port'], $db['name'], $db['charset']);
    $pdo = new PDO($dsn, $db['user'], $db['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    out("✔ Connected to MySQL database: " . $db['name']);
} catch (PDOException $e) {
    out("✘ Cannot connect to MySQL: " . $e->getMessage());
    out("");
    out("Troubleshooting:");
    out("  • Make sure the database '" . $db['name'] . "' exists (create it first on your hosting panel if online).");
    out("  • Make sure MySQL service is running.");
    out("  • Check DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS in your config.");
    exit(1);
}

// ── Read schema ────────────────────────────────────────────────────
$schemaFile = __DIR__ . '/schema.sql';
if (!file_exists($schemaFile)) {
    out("✘ schema.sql not found at: " . $schemaFile);
    exit(1);
}

$sql = file_get_contents($schemaFile);
out("✔ schema.sql loaded (" . number_format(strlen($sql)) . " bytes).");
out("");

// ── Execute each statement ─────────────────────────────────────────
// Split on semicolons that are NOT inside strings (simple approach for our controlled schema)
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

$statements = array_filter(
    array_map('trim', explode(';', $sql)),
    fn($s) => strlen($s) > 5
);

$success = 0;
$errors  = 0;

foreach ($statements as $stmt) {
    // Skip pure comments
    if (preg_match('/^(--|#|\*)/m', ltrim($stmt)) && strlen(trim(preg_replace('/^(--|#).*$/m', '', $stmt))) < 5) {
        continue;
    }
    // Skip DELIMITER blocks — PDO can't handle them; we handle stored procs separately
    if (stripos($stmt, 'DELIMITER') !== false) {
        continue;
    }
    // Skip stored procedure body after DELIMITER change
    if (stripos($stmt, 'CREATE PROCEDURE') !== false) {
        // Execute without DELIMITER wrappers
        $procBody = preg_replace('/DELIMITER\s+\$\$.*?DELIMITER\s+;/s', '', $stmt);
        $stmt = $procBody ?: $stmt;
    }

    try {
        $pdo->exec($stmt);
        $success++;
        // Print table creation notices
        if (preg_match('/CREATE\s+(?:TABLE|DATABASE|PROCEDURE)\s+(?:IF NOT EXISTS\s+)?[`"]?(\w+)/i', $stmt, $m)) {
            out("  ✔ " . $m[0]);
        }
    } catch (PDOException $e) {
        // Ignore "table already exists" and "procedure already exists"
        if ($e->getCode() == '42S01' || $e->getCode() == 1050 || $e->getCode() == 1304) {
            $success++;
        } else {
            out("  ✘ ERROR [" . $e->getCode() . "]: " . $e->getMessage());
            out("    SQL: " . substr(trim($stmt), 0, 120));
            $errors++;
        }
    }
}

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

out("");
out("═══════════════════════════════════════════");
out("  Migration complete.");
out("  Statements executed: {$success}");
if ($errors) out("  Errors: {$errors}");
out("═══════════════════════════════════════════");
out("");
out("NEXT STEPS:");
out("  1. Copy .env.example to .env and fill in your settings.");
out("  2. Delete this file (database/migrate.php) for security.");
out("  3. Install PHPMailer:  composer install");
out("  4. Default admin login:");
out("       Email:    admin@packngo.store");
out("       Password: Admin@PackNGo123");
out("  5. CHANGE THE ADMIN PASSWORD immediately after first login.");
out("");
