<?php
/**
 * PackNGo — Admin Seeder
 * Run once from CLI or browser to create/reset the admin account.
 * Usage (CLI):  php database/seed_admin.php
 * Usage (web):  http://localhost/PackNGo/database/seed_admin.php
 * DELETE this file after first use!
 */
declare(strict_types=1);

// ── Security Check ──────────────────────────────────────────
$isCli = PHP_SAPI === 'cli';
if (!$isCli) {
    $config = require dirname(__DIR__) . '/config/app.php';
    $secret = $config['app']['secret'] ?? 'SETUP_SECRET';
    if (($_GET['secret'] ?? '') !== $secret) {
        http_response_code(403);
        die('403 Forbidden. Add ?secret=YOUR_APP_SECRET_KEY to the URL.');
    }
    header('Content-Type: text/plain; charset=utf-8');
}

require_once dirname(__DIR__) . '/config/Database.php';

$db = Database::getInstance();

// ── Config ──────────────────────────────────────────────────
$adminEmail    = 'admin@packngo.store';
$adminPassword = 'Admin@PackNGo123';   // CHANGE THIS
$firstName     = 'Admin';
$lastName      = 'PackNGo';

// ── Hash ────────────────────────────────────────────────────
$hash = password_hash($adminPassword, PASSWORD_BCRYPT, ['cost' => 12]);

// ── Upsert ──────────────────────────────────────────────────
$existing = $db->fetchOne("SELECT id FROM users WHERE email = :e", [':e' => $adminEmail]);

if ($existing) {
    $db->execute(
        "UPDATE users SET password_hash=:h, role='admin', is_active=1, email_verified=1 WHERE id=:id",
        [':h' => $hash, ':id' => $existing['id']]
    );
    echo "✅ Admin account updated (id={$existing['id']}).\n";
} else {
    $db->execute(
        "INSERT INTO users (first_name, last_name, email, password_hash, role, is_active, email_verified)
              VALUES (:fn, :ln, :e, :h, 'admin', 1, 1)",
        [':fn' => $firstName, ':ln' => $lastName, ':e' => $adminEmail, ':h' => $hash]
    );
    echo "✅ Admin account created.\n";
}

echo "   Email   : $adminEmail\n";
echo "   Password: $adminPassword\n";
echo "\n⚠️  DELETE this file now: database/seed_admin.php\n";
