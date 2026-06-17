-- ============================================================
--  PackNGo — Migration: Add reply columns to `contact_messages`
--  Run this ONCE if your database was created BEFORE the
--  admin "Reply to Feedback" feature was added.
--
--  Safe to run on a fresh database too (schema.sql already
--  includes these columns for new installs).
--
--  Usage:
--    mysql -u root packngo_db < database/add_contact_reply_columns.sql
-- ============================================================

USE `packngo_db`;

ALTER TABLE `contact_messages`
    ADD COLUMN IF NOT EXISTS `reply_message` TEXT         DEFAULT NULL COMMENT 'Admin reply sent to the user' AFTER `is_read`,
    ADD COLUMN IF NOT EXISTS `replied_at`    DATETIME     DEFAULT NULL AFTER `reply_message`,
    ADD COLUMN IF NOT EXISTS `replied_by`    INT UNSIGNED DEFAULT NULL COMMENT 'Admin user id who sent the reply' AFTER `replied_at`;

-- Add the index + foreign key only if they don't already exist
SET @fk_exists := (
    SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE()
      AND TABLE_NAME = 'contact_messages'
      AND CONSTRAINT_NAME = 'fk_contact_replied_by'
);

SET @sql := IF(@fk_exists = 0,
    'ALTER TABLE `contact_messages`
        ADD INDEX `idx_contact_replied_by` (`replied_by`),
        ADD CONSTRAINT `fk_contact_replied_by` FOREIGN KEY (`replied_by`)
            REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
    'SELECT "fk_contact_replied_by already exists — skipped."'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
