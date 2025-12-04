-- ============================================================================
-- DATABASE SCHEMA FOR LARAVEL APPLICATION
-- ============================================================================
-- 
-- File này chứa tất cả các bảng cần thiết cho ứng dụng Laravel
-- Chạy file này trong MySQL để tạo các bảng
-- 
-- Sử dụng: mysql -u username -p database_name < schema.sql
-- 
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- BẢNG ACCOUNTS - Dùng cho Authentication (Đăng nhập Google)
-- ============================================================================
-- Bảng chính để xác thực người dùng, hỗ trợ đăng nhập Google OAuth

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    
    -- Google OAuth fields
    `google_id` VARCHAR(255) NULL UNIQUE COMMENT 'Google User ID',
    
    -- Microsoft OAuth fields
    `microsoft_id` VARCHAR(255) NULL UNIQUE COMMENT 'Microsoft User ID',
    
    -- Thông tin cơ bản
    `name` VARCHAR(255) NOT NULL COMMENT 'Tên người dùng',
    `email` VARCHAR(255) NOT NULL UNIQUE COMMENT 'Email',
    `email_verified_at` TIMESTAMP NULL COMMENT 'Thời điểm xác thực email',
    `password` VARCHAR(255) NULL COMMENT 'Mật khẩu (nullable cho Google/Microsoft login)',
    
    -- Avatar từ Google/Microsoft
    `avatar` TEXT NULL COMMENT 'URL avatar từ Google hoặc base64 từ Microsoft',
    
    -- Role để phân quyền
    `role` VARCHAR(255) NOT NULL DEFAULT 'user' COMMENT 'Vai trò: user, admin, etc.',
    
    -- Liên kết với bảng users cũ (optional)
    `user_id` INT UNSIGNED NULL COMMENT 'Liên kết với bảng users cũ',
    
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    
    PRIMARY KEY (`id`),
    INDEX `accounts_user_id_index` (`user_id`),
    INDEX `accounts_microsoft_id_index` (`microsoft_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- BẢNG PERSONAL_ACCESS_TOKENS - Laravel Sanctum API Tokens
-- ============================================================================
-- Lưu trữ API tokens cho authentication

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tokenable_type` VARCHAR(255) NOT NULL,
    `tokenable_id` BIGINT UNSIGNED NOT NULL,
    `name` TEXT NOT NULL COMMENT 'Tên token',
    `token` VARCHAR(64) NOT NULL UNIQUE COMMENT 'Token hash',
    `abilities` TEXT NULL COMMENT 'Quyền của token',
    `last_used_at` TIMESTAMP NULL COMMENT 'Lần sử dụng cuối',
    `expires_at` TIMESTAMP NULL COMMENT 'Thời điểm hết hạn',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    
    PRIMARY KEY (`id`),
    INDEX `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`),
    INDEX `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- BẢNG SESSIONS - Lưu trữ Session
-- ============================================================================
-- Lưu trữ session của người dùng (nếu dùng database driver)

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
    `id` VARCHAR(255) NOT NULL,
    `user_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    
    PRIMARY KEY (`id`),
    INDEX `sessions_user_id_index` (`user_id`),
    INDEX `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- BẢNG PASSWORD_RESET_TOKENS - Reset Password
-- ============================================================================
-- Lưu trữ token reset password

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL,
    
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- BẢNG CACHE - Laravel Cache
-- ============================================================================
-- Lưu trữ cache (nếu dùng database driver)

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
    `key` VARCHAR(255) NOT NULL,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL,
    
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- BẢNG CACHE_LOCKS - Laravel Cache Locks
-- ============================================================================

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
    `key` VARCHAR(255) NOT NULL,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL,
    
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- BẢNG JOBS - Laravel Queue Jobs
-- ============================================================================
-- Lưu trữ jobs trong queue (nếu dùng database driver)

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `queue` VARCHAR(255) NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `attempts` TINYINT UNSIGNED NOT NULL,
    `reserved_at` INT UNSIGNED NULL,
    `available_at` INT UNSIGNED NOT NULL,
    `created_at` INT UNSIGNED NOT NULL,
    
    PRIMARY KEY (`id`),
    INDEX `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- BẢNG JOB_BATCHES - Laravel Job Batches
-- ============================================================================

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
    `id` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `total_jobs` INT NOT NULL,
    `pending_jobs` INT NOT NULL,
    `failed_jobs` INT NOT NULL,
    `failed_job_ids` LONGTEXT NOT NULL,
    `options` MEDIUMTEXT NULL,
    `cancelled_at` INT NULL,
    `created_at` INT NOT NULL,
    `finished_at` INT NULL,
    
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- BẢNG FAILED_JOBS - Laravel Failed Jobs
-- ============================================================================

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` VARCHAR(255) NOT NULL UNIQUE,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `exception` LONGTEXT NOT NULL,
    `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- HƯỚNG DẪN SỬ DỤNG
-- ============================================================================
-- 
-- 1. Tạo database (nếu chưa có):
--    CREATE DATABASE utt_ic40 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 
-- 2. Chạy file SQL này:
--    mysql -u dinhduc -p utt_ic40 < database/schema.sql
-- 
-- 3. Hoặc copy nội dung vào phpMyAdmin/MySQL Workbench để chạy
-- 
-- ============================================================================
