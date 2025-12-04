-- ============================================================================
-- DATABASE SCHEMA - TỐI ƯU CHO ĐĂNG NHẬP GOOGLE & MICROSOFT
-- ============================================================================
-- File SQL tối ưu chỉ giữ những bảng và cột cần thiết
-- Chạy: mysql -u dinhduc -p utt_ic40 < schema_optimized.sql
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- BẢNG ACCOUNTS - Authentication (Google & Microsoft OAuth)
-- ============================================================================
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    
    -- OAuth Provider IDs
    `google_id` VARCHAR(255) NULL UNIQUE COMMENT 'Google User ID',
    `microsoft_id` VARCHAR(255) NULL UNIQUE COMMENT 'Microsoft User ID',
    
    -- Thông tin user cơ bản
    `name` VARCHAR(255) NOT NULL COMMENT 'Tên người dùng',
    `email` VARCHAR(255) NOT NULL UNIQUE COMMENT 'Email',
    `avatar` TEXT NULL COMMENT 'Avatar URL hoặc base64',
    
    -- Timestamps
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_google_id` (`google_id`),
    INDEX `idx_microsoft_id` (`microsoft_id`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================================
-- BẢNG PERSONAL_ACCESS_TOKENS - Laravel Sanctum API Tokens
-- ============================================================================
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `tokenable_type` VARCHAR(255) NOT NULL,
    `tokenable_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL COMMENT 'Tên token',
    `token` VARCHAR(64) NOT NULL UNIQUE COMMENT 'Token hash',
    `abilities` TEXT NULL COMMENT 'Permissions',
    `last_used_at` TIMESTAMP NULL,
    `expires_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    
    PRIMARY KEY (`id`),
    INDEX `idx_tokenable` (`tokenable_type`, `tokenable_id`),
    INDEX `idx_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- HƯỚNG DẪN SỬ DỤNG
-- ============================================================================
-- 
-- 1. Tạo database (nếu chưa có):
--    CREATE DATABASE utt_ic40 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 
-- 2. Chạy file này:
--    mysql -u dinhduc -p utt_ic40 < schema_optimized.sql
-- 
-- 3. Kiểm tra:
--    SHOW TABLES;
--    DESCRIBE accounts;
--    DESCRIBE personal_access_tokens;
-- 
-- ============================================================================
-- CẤU TRÚC BẢNG ACCOUNTS
-- ============================================================================
-- Cột đã XÓA (không cần thiết):
-- - email_verified_at: OAuth providers đã verify email
-- - password: Không dùng password authentication
-- - role: Có thể thêm sau nếu cần phân quyền
-- - user_id: Không cần liên kết với bảng cũ
-- - remember_token: Dùng API token thay vì session
-- 
-- Cột GIỮ LẠI (cần thiết):
-- - id: Primary key
-- - google_id: Định danh user từ Google
-- - microsoft_id: Định danh user từ Microsoft
-- - name: Tên hiển thị
-- - email: Email (unique)
-- - avatar: Ảnh đại diện
-- - created_at, updated_at: Theo dõi thời gian
-- ============================================================================
