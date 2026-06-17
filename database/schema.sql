-- ============================================================
--  PackNGo — MySQL Database Schema
--  Compatible with: MySQL 5.7+ / MariaDB 10.3+ (XAMPP)
--  Encoding: utf8mb4 (full Unicode + emoji support)
--
--  TABLES:
--    1. users             — registered customers & admins
--    2. destinations      — travel destinations catalogue
--    3. packages          — tour packages
--    4. reservations      — booking records (core table)
--    5. newsletter_subs   — email subscribers
--    6. blog_posts        — blog/articles
--    7. blog_categories   — blog category taxonomy
--    8. gallery_images    — gallery image metadata
--    9. contact_messages  — general enquiry messages
--   10. admin_sessions    — secure admin login tracking
--   11. rate_limit_log    — IP-based rate limiting
--
--  Run:  mysql -u root packngo_db < schema.sql
-- ============================================================

-- CREATE DATABASE IF NOT EXISTS `packngo_db`
--     CHARACTER SET utf8mb4
--     COLLATE utf8mb4_unicode_ci;
-- 
-- USE `packngo_db`;

-- ── Drop in reverse FK order (safe re-run) ────────────────
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `page_visits`;
DROP TABLE IF EXISTS `visitors`;
DROP TABLE IF EXISTS `rate_limit_log`;
DROP TABLE IF EXISTS `admin_sessions`;
DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `gallery_images`;
DROP TABLE IF EXISTS `blog_posts`;
DROP TABLE IF EXISTS `blog_categories`;
DROP TABLE IF EXISTS `newsletter_subs`;
DROP TABLE IF EXISTS `reservations`;
DROP TABLE IF EXISTS `packages`;
DROP TABLE IF EXISTS `destinations`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  1. users
-- ============================================================
CREATE TABLE `users` (
    `id`              INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `first_name`      VARCHAR(50)      NOT NULL,
    `last_name`       VARCHAR(50)      NOT NULL,
    `email`           VARCHAR(120)     NOT NULL,
    `password_hash`   VARCHAR(255)     NOT NULL               COMMENT 'bcrypt hash',
    `phone_code`      VARCHAR(6)       NOT NULL DEFAULT '+92',
    `phone`           VARCHAR(20)               DEFAULT NULL,
    `role`            ENUM('customer','admin')  NOT NULL DEFAULT 'customer',
    `is_active`       TINYINT(1)       NOT NULL DEFAULT 1,
    `email_verified`  TINYINT(1)       NOT NULL DEFAULT 0,
    `verify_token`    VARCHAR(100)              DEFAULT NULL,
    `otp_code`        VARCHAR(255)              DEFAULT NULL  COMMENT 'bcrypt hash of current 6-digit OTP',
    `otp_expires`     DATETIME                  DEFAULT NULL  COMMENT 'OTP expiry (15 min)',
    `reset_token`     VARCHAR(100)              DEFAULT NULL,
    `reset_expires`   DATETIME                  DEFAULT NULL,
    `created_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`),
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_verify_token` (`verify_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  2. destinations
-- ============================================================
CREATE TABLE `destinations` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `slug`        VARCHAR(80)   NOT NULL               COMMENT 'URL-friendly key, e.g. ireland',
    `name`        VARCHAR(100)  NOT NULL,
    `tagline`     VARCHAR(200)           DEFAULT NULL,
    `about`       TEXT                   DEFAULT NULL,
    `category`    ENUM('beach','adventure','culture','romance','nature') NOT NULL,
    `country`     VARCHAR(80)            DEFAULT NULL,
    `best_season` VARCHAR(80)            DEFAULT NULL,
    `currency`    VARCHAR(30)            DEFAULT NULL,
    `language`    VARCHAR(80)            DEFAULT NULL,
    `climate`     VARCHAR(80)            DEFAULT NULL,
    `timezone`    VARCHAR(60)            DEFAULT NULL,
    `image_path`  VARCHAR(255)           DEFAULT NULL,
    `is_active`   TINYINT(1)    NOT NULL DEFAULT 1,
    `sort_order`  SMALLINT      NOT NULL DEFAULT 0,
    `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_dest_slug` (`slug`),
    INDEX `idx_dest_category` (`category`),
    INDEX `idx_dest_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  3. packages
-- ============================================================
CREATE TABLE `packages` (
    `id`          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `slug`        VARCHAR(80)     NOT NULL,
    `name`        VARCHAR(100)    NOT NULL,
    `tagline`     VARCHAR(200)             DEFAULT NULL,
    `description` TEXT                     DEFAULT NULL,
    `duration_days` TINYINT UNSIGNED NOT NULL DEFAULT 7,
    `price_usd`   DECIMAL(10,2)   NOT NULL,
    `icon`        VARCHAR(10)              DEFAULT '✦'  COMMENT 'Emoji or icon char used on frontend',
    `includes`    TEXT                     DEFAULT NULL COMMENT 'JSON array of inclusions',
    `is_active`   TINYINT(1)      NOT NULL DEFAULT 1,
    `is_featured` TINYINT(1)      NOT NULL DEFAULT 0,
    `sort_order`  SMALLINT        NOT NULL DEFAULT 0,
    `created_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_pkg_slug` (`slug`),
    INDEX `idx_pkg_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  4. reservations  (core booking table)
-- ============================================================
CREATE TABLE `reservations` (
    `id`              INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `booking_ref`     VARCHAR(20)      NOT NULL               COMMENT 'Human-readable reference, e.g. PNG-20260612-0001',
    `user_id`         INT UNSIGNED               DEFAULT NULL  COMMENT 'NULL = guest booking',
    `first_name`      VARCHAR(50)      NOT NULL,
    `last_name`       VARCHAR(50)      NOT NULL,
    `email`           VARCHAR(120)     NOT NULL,
    `phone_code`      VARCHAR(6)       NOT NULL DEFAULT '+92',
    `phone`           VARCHAR(20)               DEFAULT NULL,
    `destination_id`  INT UNSIGNED               DEFAULT NULL,
    `destination_text`VARCHAR(100)               DEFAULT NULL  COMMENT 'Raw typed destination (denormalised)',
    `package_id`      INT UNSIGNED               DEFAULT NULL,
    `package_text`    VARCHAR(100)               DEFAULT NULL  COMMENT 'Raw selected package text',
    `departure_date`  DATE             NOT NULL,
    `return_date`     DATE                       DEFAULT NULL,
    `travellers`      VARCHAR(30)      NOT NULL               COMMENT 'e.g. 2 (Couple)',
    `budget_range`    VARCHAR(50)               DEFAULT NULL,
    `accommodation`   VARCHAR(100)              DEFAULT NULL,
    `special_requests`TEXT                       DEFAULT NULL,
    `status`          ENUM('pending','confirmed','cancelled','completed')
                                       NOT NULL DEFAULT 'pending',
    `total_price`     DECIMAL(10,2)             DEFAULT NULL,
    `admin_notes`     TEXT                       DEFAULT NULL,
    `ip_address`      VARCHAR(45)               DEFAULT NULL   COMMENT 'IPv4/IPv6',
    `user_agent`      VARCHAR(300)              DEFAULT NULL,
    `created_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_booking_ref` (`booking_ref`),
    INDEX `idx_res_email`     (`email`),
    INDEX `idx_res_status`    (`status`),
    INDEX `idx_res_depart`    (`departure_date`),
    INDEX `idx_res_created`   (`created_at`),
    CONSTRAINT `fk_res_user` FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_res_dest` FOREIGN KEY (`destination_id`)
        REFERENCES `destinations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_res_pkg`  FOREIGN KEY (`package_id`)
        REFERENCES `packages` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  5. newsletter_subs
-- ============================================================
CREATE TABLE `newsletter_subs` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `email`         VARCHAR(120)  NOT NULL,
    `name`          VARCHAR(100)           DEFAULT NULL,
    `is_active`     TINYINT(1)    NOT NULL DEFAULT 1,
    `token`         VARCHAR(100)           DEFAULT NULL  COMMENT 'Unsubscribe token',
    `subscribed_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `unsubscribed_at` DATETIME             DEFAULT NULL,
    `ip_address`    VARCHAR(45)            DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_newsletter_email` (`email`),
    INDEX `idx_nl_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  6. blog_categories
-- ============================================================
CREATE TABLE `blog_categories` (
    `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `slug`       VARCHAR(60)   NOT NULL,
    `name`       VARCHAR(80)   NOT NULL,
    `sort_order` SMALLINT      NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_blogcat_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  7. blog_posts
-- ============================================================
CREATE TABLE `blog_posts` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `category_id` INT UNSIGNED           DEFAULT NULL,
    `slug`        VARCHAR(120)  NOT NULL,
    `title`       VARCHAR(200)  NOT NULL,
    `excerpt`     VARCHAR(400)           DEFAULT NULL,
    `content`     LONGTEXT               DEFAULT NULL,
    `image_path`  VARCHAR(255)           DEFAULT NULL,
    `author`      VARCHAR(100)           DEFAULT 'PackNGo Team',
    `read_minutes`TINYINT UNSIGNED       DEFAULT 5,
    `is_featured` TINYINT(1)    NOT NULL DEFAULT 0,
    `is_published` TINYINT(1)   NOT NULL DEFAULT 0,
    `published_at`DATETIME               DEFAULT NULL,
    `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_post_slug` (`slug`),
    INDEX `idx_post_cat`       (`category_id`),
    INDEX `idx_post_published` (`is_published`, `published_at`),
    CONSTRAINT `fk_post_cat` FOREIGN KEY (`category_id`)
        REFERENCES `blog_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  8. gallery_images
-- ============================================================
CREATE TABLE `gallery_images` (
    `id`             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `destination_id` INT UNSIGNED           DEFAULT NULL,
    `title`          VARCHAR(150)           DEFAULT NULL,
    `file_name`      VARCHAR(255)  NOT NULL,
    `file_path`      VARCHAR(255)  NOT NULL,
    `alt_text`       VARCHAR(200)           DEFAULT NULL,
    `category`       VARCHAR(60)            DEFAULT NULL,
    `is_active`      TINYINT(1)    NOT NULL DEFAULT 1,
    `sort_order`     SMALLINT      NOT NULL DEFAULT 0,
    `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_gallery_dest`   (`destination_id`),
    INDEX `idx_gallery_active` (`is_active`),
    CONSTRAINT `fk_gallery_dest` FOREIGN KEY (`destination_id`)
        REFERENCES `destinations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  9. contact_messages
-- ============================================================
CREATE TABLE `contact_messages` (
    `id`            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(100)  NOT NULL,
    `email`         VARCHAR(120)  NOT NULL,
    `subject`       VARCHAR(200)           DEFAULT NULL,
    `message`       TEXT          NOT NULL,
    `is_read`       TINYINT(1)    NOT NULL DEFAULT 0,
    `reply_message` TEXT                   DEFAULT NULL  COMMENT 'Admin reply sent to the user',
    `replied_at`    DATETIME               DEFAULT NULL,
    `replied_by`    INT UNSIGNED           DEFAULT NULL  COMMENT 'Admin user id who sent the reply',
    `ip_address`    VARCHAR(45)            DEFAULT NULL,
    `created_at`    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_contact_read` (`is_read`),
    INDEX `idx_contact_replied_by` (`replied_by`),
    CONSTRAINT `fk_contact_replied_by` FOREIGN KEY (`replied_by`)
        REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  10. admin_sessions
-- ============================================================
CREATE TABLE `admin_sessions` (
    `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED  NOT NULL,
    `token_hash` VARCHAR(255)  NOT NULL  COMMENT 'SHA-256 of session token',
    `ip_address` VARCHAR(45)            DEFAULT NULL,
    `user_agent` VARCHAR(300)           DEFAULT NULL,
    `expires_at` DATETIME      NOT NULL,
    `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_asess_user`    (`user_id`),
    INDEX `idx_asess_expires` (`expires_at`),
    CONSTRAINT `fk_asess_user` FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  11. rate_limit_log
-- ============================================================
CREATE TABLE `rate_limit_log` (
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `ip_address`  VARCHAR(45)   NOT NULL,
    `action`      VARCHAR(60)   NOT NULL  COMMENT 'e.g. reservation_submit, newsletter_sub',
    `hit_count`   SMALLINT      NOT NULL DEFAULT 1,
    `window_start`DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_ratelimit` (`ip_address`, `action`, `window_start`),
    INDEX `idx_rl_ip_action` (`ip_address`, `action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  SEED DATA
-- ============================================================

-- ── Admin user (password: Admin@PackNGo123) ───────────────
--  Hash generated with: password_hash('Admin@PackNGo123', PASSWORD_BCRYPT, ['cost'=>12])
--  CHANGE THIS PASSWORD immediately after first login.
INSERT INTO `users`
    (`first_name`,`last_name`,`email`,`password_hash`,`phone_code`,`phone`,`role`,`is_active`,`email_verified`)
VALUES
    ('Admin','PackNGo','admin@packngo.store',
     '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
     '+92','3001234567','admin',1,1);

-- ── Destinations ─────────────────────────────────────────
INSERT INTO `destinations`
    (`slug`,`name`,`tagline`,`category`,`country`,`best_season`,`currency`,`language`,`climate`,`timezone`,`image_path`,`sort_order`)
VALUES
    ('ireland','Emerald Ireland','Where ancient cliffs meet emerald valleys and warm Irish hospitality.','nature','Ireland','May – September','Euro (€)','English & Irish','Mild & Rainy','GMT / IST','Images/Ireland.jpg',1),
    ('paris','Parisian Lights','The city of love, art, fashion and unforgettable cuisine.','romance','France','April – June, Sep – Oct','Euro (€)','French','Oceanic, Mild','CET (UTC+1)','Images/Paris.jpg',2),
    ('italy','Classic Italy','Ancient ruins, Renaissance art, spectacular food and breathtaking landscapes.','culture','Italy','April – June, Sep – Oct','Euro (€)','Italian','Mediterranean','CET (UTC+1)','Images/Italy.jpg',3),
    ('maldives','Maldives Luxury Escape','Overwater villas & crystal-clear lagoons.','beach','Maldives','November – April','Maldivian Rufiyaa','Dhivehi','Tropical','MVT (UTC+5)','Images/Maldives Luxury Escape.jpg',4),
    ('bali','Bali Beach Retreat','Tropical paradise with volcanic black-sand shores.','beach','Indonesia','April – October','Indonesian Rupiah','Balinese / Indonesian','Tropical','WIB (UTC+8)','Images/Bali Beach Retreat.jpg',5),
    ('nepal','Nepal Himalayan Trek','5 Days trekking to Everest Base Camp.','adventure','Nepal','Mar–May, Sep–Nov','Nepalese Rupee','Nepali','Alpine','NPT (UTC+5:45)','Images/Nepal Himalayan Trek.jpg',6),
    ('kenya','African Safari — Kenya','Big Five safari across the Masai Mara savanna.','adventure','Kenya','July – October','Kenyan Shilling','Swahili / English','Savanna','EAT (UTC+3)','Images/African Safari — Kenya.jpg',7),
    ('iceland','Iceland Aurora Hunt','Northern lights, geysers & volcanic landscapes.','adventure','Iceland','Oct – March','Icelandic Króna','Icelandic','Subarctic','GMT (UTC+0)','Images/Iceland Aurora Hunt.jpg',8),
    ('kyoto','Kyoto Cultural Immersion','Temples, geishas & Japanese ceremony.','culture','Japan','March – May, Oct','Japanese Yen','Japanese','Humid subtropical','JST (UTC+9)','Images/Kyoto Cultural Immersion.jpg',9),
    ('santorini','Santorini Sun & Sea','Iconic white-washed cliffs above the Aegean.','beach','Greece','June – September','Euro (€)','Greek','Mediterranean','EET (UTC+2)','Images/Santorini Sun & Sea.jpg',10);

-- ── Packages ─────────────────────────────────────────────
INSERT INTO `packages`
    (`slug`,`name`,`tagline`,`duration_days`,`price_usd`,`icon`,`includes`,`is_featured`,`sort_order`)
VALUES
    ('luxury-escape','Luxury Escape','7 Days of Pure Bliss',7,2500.00,'✦',
     '["5-star accommodation","Airport transfers","Private guided tours","24/7 concierge"]',0,1),
    ('honeymoon-special','Honeymoon Special','10 Days in Paradise',10,4000.00,'♡',
     '["Romantic overwater bungalows","Candlelit dinners","Spa treatments","Private excursions","Airport transfers"]',1,2),
    ('adventure-trek','Adventure Trek','5 Days of Exploration',5,1800.00,'⛰',
     '["Certified mountain/safari guide","Teahouse or camp accommodation","Airport transfers","First-aid certified guide"]',0,3),
    ('beach-nature','Beach & Nature','8 Days of Sun & Sea',8,3200.00,'🌊',
     '["Beach resort accommodation","Snorkelling & water sports","Island hopping tours","Airport transfers","Daily breakfast"]',0,4);

-- ── Blog categories ───────────────────────────────────────
INSERT INTO `blog_categories` (`slug`,`name`,`sort_order`) VALUES
    ('travel-tips',   'Travel Tips',       1),
    ('destinations',  'Destinations',      2),
    ('luxury',        'Luxury Travel',     3),
    ('adventure',     'Adventure',         4),
    ('honeymoon',     'Honeymoon',         5),
    ('culture',       'Culture & Heritage',6);

-- ── Stored Procedure: Generate booking reference ──────────
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS `sp_generate_booking_ref`(OUT p_ref VARCHAR(20))
BEGIN
    DECLARE v_date VARCHAR(8);
    DECLARE v_seq  INT;
    SET v_date = DATE_FORMAT(NOW(), '%Y%m%d');
    SELECT COALESCE(MAX(CAST(SUBSTRING(booking_ref, 14) AS UNSIGNED)), 0) + 1
      INTO v_seq
      FROM reservations
     WHERE booking_ref LIKE CONCAT('PNG-', v_date, '-%');
    SET p_ref = CONCAT('PNG-', v_date, '-', LPAD(v_seq, 4, '0'));
END $$
DELIMITER ;

-- ── Reservation OTPs (guest email verification for booking form) ──
CREATE TABLE IF NOT EXISTS `reservation_otps` (
    `email`      VARCHAR(120) NOT NULL,
    `otp_hash`   VARCHAR(255) NOT NULL,
    `expires_at` DATETIME     NOT NULL,
    `created_at` DATETIME     NOT NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  12. visitors (visitor sessions)
-- ============================================================
CREATE TABLE `visitors` (
    `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `session_id`    VARCHAR(255)     NOT NULL,
    `user_id`       INT UNSIGNED              DEFAULT NULL COMMENT 'NULL for guests',
    `ip_address`    VARCHAR(45)               DEFAULT NULL COMMENT 'IPv4/IPv6',
    `user_agent`    VARCHAR(300)              DEFAULT NULL,
    `created_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_visitor_session` (`session_id`),
    CONSTRAINT `fk_visitor_user` FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  13. page_visits (visitor activity tracking)
-- ============================================================
CREATE TABLE `page_visits` (
    `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `visitor_id`    INT UNSIGNED     NOT NULL,
    `page_url`      VARCHAR(255)     NOT NULL COMMENT 'e.g. index.html, About.html',
    `referrer`      VARCHAR(255)              DEFAULT NULL,
    `visited_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_visit_visitor` FOREIGN KEY (`visitor_id`)
        REFERENCES `visitors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

