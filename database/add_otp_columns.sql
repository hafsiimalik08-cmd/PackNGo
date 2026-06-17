-- ============================================================
--  PackNGo — Migration: Add OTP columns to `users`
--  Run this ONCE if your database was created BEFORE the
--  email-OTP verification feature was added.
--
--  Safe to run on a fresh database too (schema.sql already
--  includes these columns for new installs).
--
--  Usage:
--    mysql -u root packngo_db < database/add_otp_columns.sql
-- ============================================================

USE `packngo_db`;

ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `otp_code`    VARCHAR(255) DEFAULT NULL COMMENT 'bcrypt hash of current 6-digit OTP' AFTER `verify_token`,
    ADD COLUMN IF NOT EXISTS `otp_expires` DATETIME     DEFAULT NULL COMMENT 'OTP expiry (15 min)' AFTER `otp_code`;
