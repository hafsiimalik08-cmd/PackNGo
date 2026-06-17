<?php
/**
 * PackNGo — Production / Hosting Configuration Fallback
 * 
 * Rename this file to config.php in your project root.
 * This is an alternative to .env, which is often hidden on free hosting file managers.
 * If config.php exists, these values will override/supplement .env settings.
 */

return [
    // --- Application ---
    'APP_NAME' => 'PackNGo',
    'APP_ENV' => 'production',          // development | production
    'APP_URL' => 'https://yourdomain.com', // Change to your actual domain with https
    'APP_DEBUG' => 'false',               // Set to false in production to hide error details
    'APP_TIMEZONE' => 'Asia/Karachi',
    'APP_SECRET_KEY' => 'CHANGE_THIS_TO_A_RANDOM_64_CHARACTER_STRING', // Generate a random key

    // --- Database Configuration (Get these from your hosting panel) ---
    'DB_HOST' => '127.0.0.1',           // e.g. sql123.infinityfree.com or localhost
    'DB_PORT' => '3306',
    'DB_NAME' => 'your_db_name',        // Your database name
    'DB_USER' => 'your_db_user',        // Your database username
    'DB_PASS' => 'your_db_password',    // Your database password
    'DB_CHARSET' => 'utf8mb4',

    // --- Email (SMTP / PHPMailer) ---
    'MAIL_HOST' => 'smtp.gmail.com',
    'MAIL_PORT' => '587',
    'MAIL_ENCRYPTION' => 'tls',
    'MAIL_USERNAME' => 'your@gmail.com',
    'MAIL_PASSWORD' => 'your_app_password',
    'MAIL_FROM_ADDRESS' => 'noreply@yourdomain.com',
    'MAIL_FROM_NAME' => 'PackNGo Concierge',
    'ADMIN_EMAIL' => 'admin@yourdomain.com',

    // --- Security ---
    'SESSION_LIFETIME' => '7200',
    'CSRF_TOKEN_LIFETIME' => '3600',
    'BCRYPT_ROUNDS' => '12',
    'RATE_LIMIT_REQUESTS' => '10',
    'RATE_LIMIT_WINDOW' => '3600',

    // --- File Uploads ---
    'UPLOAD_MAX_SIZE' => '5242880',
    'ALLOWED_IMAGE_TYPES' => 'jpg,jpeg,png,webp',
];
