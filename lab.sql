CREATE DATABASE `db_service_pet` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

USE `db_service_pet`;

-- Accounts table
CREATE TABLE IF NOT EXISTS `accounts` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(15),
    `address` TEXT,
    `role` ENUM('customer', 'admin', 'staff') DEFAULT 'customer',
    `points` INT DEFAULT 0,
    `ranking` ENUM('Bronze', 'Silver', 'Gold', 'Diamond') DEFAULT 'Bronze',
    `verify_email_at` TIMESTAMP DEFAULT NULL,
    `verify_email_token` VARCHAR(255) DEFAULT NULL,
    `rating` DECIMAL(2,1) DEFAULT 0.0,
    `reset_password_at` TIMESTAMP DEFAULT NULL,
    `reset_password_token` VARCHAR(255) DEFAULT NULL,
    `avatar_url` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Service Types table
CREATE TABLE IF NOT EXISTS `service_types` (
    `service_type_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Services table
CREATE TABLE IF NOT EXISTS `services` (
    `service_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10,2),
    `discount_percent` INT default 0,
    `duration` INT,
    `service_type_id` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_type_id) REFERENCES service_types(service_type_id)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Service Images table
CREATE TABLE IF NOT EXISTS `service_images` (
    `image_id` INT AUTO_INCREMENT PRIMARY KEY,
    `service_id` INT NOT NULL,
    `image_url` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(service_id)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Discounts table
CREATE TABLE IF NOT EXISTS `discounts` (
    `discount_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `code` VARCHAR(255) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `percent` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Time lots table:
CREATE TABLE IF NOT EXISTS `time_slots` (
    `time_slot_id` INT AUTO_INCREMENT PRIMARY KEY,
    `start_time` VARCHAR(255) NOT NULL,
    `end_time` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Schedule Staffs table
CREATE TABLE IF NOT EXISTS `staff_schedules` (
  `staff_schedule_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `account_id` INT NOT NULL,
  `date` DATE NOT NULL,
  `time_slot_id` INT NOT NULL,
  `is_available` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`staff_schedule_id`),
  FOREIGN KEY (`account_id`) REFERENCES `accounts` (`user_id`),
  FOREIGN KEY (`time_slot_id`) REFERENCES `time_slots` (`time_slot_id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Pets table
CREATE TABLE IF NOT EXISTS `pets` (
    `pet_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `type` ENUM('dog', 'cat', 'bird', 'rabbit', 'hamster', 'other') NOT NULL,
    `breed` VARCHAR(100),
    `age` TINYINT CHECK (age >= 0 AND age <= 50),
    `age_unit` ENUM('days', 'weeks', 'months', 'years') DEFAULT 'years',
    `size` ENUM('tiny', 'small', 'medium', 'large', 'extra_large') DEFAULT 'medium',
    `weight` DECIMAL(5,2) DEFAULT NULL COMMENT 'Cân nặng (kg)',
    `color` VARCHAR(50) DEFAULT NULL,
    `gender` ENUM('male', 'female', 'unknown') DEFAULT 'unknown',
    `avatar_url` VARCHAR(500),
    `medical_notes` TEXT COMMENT 'Ghi chú y tế',
    `behavioral_notes` TEXT COMMENT 'Ghi chú hành vi',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `accounts`(`user_id`) ON DELETE CASCADE,

    INDEX `idx_type_size` (`type`, `size`),
    INDEX `idx_name` (`name`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bookings table với indexes được tối ưu
CREATE TABLE IF NOT EXISTS `bookings` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `booking_code` VARCHAR(20) NOT NULL UNIQUE COMMENT 'Mã đặt hàng ngắn gọn',
    `user_id` INT NOT NULL,
    `staff_id` INT NOT NULL,
    `status` ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show') DEFAULT 'pending',
    `booking_date` DATE NOT NULL COMMENT 'Ngày sử dụng dịch vụ',
    `total_pets` TINYINT NOT NULL DEFAULT 1,
    `total_services` TINYINT NOT NULL DEFAULT 1,
    `total_duration` SMALLINT NOT NULL DEFAULT 0 COMMENT 'Tổng thời gian (phút)',
    `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Tổng tiền trước giảm giá',
    `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Số tiền được giảm',
    `discount_percent` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Phần trăm giảm giá',
    `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Tổng tiền sau giảm giá',
    `discount_code` VARCHAR(50) DEFAULT NULL,
    `payment_method` ENUM('cash', 'vnpay', 'momo', 'bank_transfer') DEFAULT 'cash',
    `payment_status` ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    `paid_at` TIMESTAMP NULL,
    `notes` TEXT,
    `customer_notes` TEXT COMMENT 'Ghi chú từ khách hàng',
    `staff_notes` TEXT COMMENT 'Ghi chú từ nhân viên',
    `cancellation_reason` TEXT,
    `cancelled_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`user_id`) REFERENCES `accounts`(`user_id`) ON DELETE RESTRICT,
    FOREIGN KEY (`staff_id`) REFERENCES `accounts`(`user_id`) ON DELETE RESTRICT,
    
    -- Indexes được tối ưu
    INDEX `idx_user_status` (`user_id`, `status`),
    INDEX `idx_staff_date` (`staff_id`, `booking_date`),
    INDEX `idx_date_status` (`booking_date`, `status`),
    INDEX `idx_payment` (`payment_method`, `payment_status`),
    INDEX `idx_created_at` (`created_at`),
    INDEX `idx_discount_code` (`discount_code`),
    INDEX `idx_status_date` (`status`, `booking_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `booking_details` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `booking_id` BIGINT UNSIGNED NOT NULL,
    `service_id` INT NOT NULL,
    `quantity` TINYINT NOT NULL DEFAULT 1,
    `unit_price` DECIMAL(10,2) NOT NULL COMMENT 'Giá gốc từng dịch vụ',
    `discount_percent` TINYINT DEFAULT 0,
    `total_price` DECIMAL(10,2) NOT NULL COMMENT 'Tổng tiền dịch vụ này',
    `duration` SMALLINT NOT NULL COMMENT 'Thời gian dịch vụ (phút)',
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`service_id`) REFERENCES `services`(`service_id`) ON DELETE RESTRICT,
    
    INDEX `idx_booking_id` (`booking_id`),
    INDEX `idx_service_id` (`service_id`),
    UNIQUE KEY `uk_booking_service` (`booking_id`, `service_id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Booking Pets table - BẢNG MỚI QUAN TRỌNG
CREATE TABLE IF NOT EXISTS `booking_pets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `booking_id` BIGINT UNSIGNED NOT NULL,
    `pet_id` INT NOT NULL,
    `special_notes` TEXT COMMENT 'Ghi chú đặc biệt cho pet này',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`pet_id`) REFERENCES `pets`(`pet_id`) ON DELETE RESTRICT,
    
    UNIQUE KEY `uk_booking_pet` (`booking_id`, `pet_id`),
    INDEX `idx_pet_id` (`pet_id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `booking_staff_schedule` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id` BIGINT UNSIGNED NOT NULL,
  `staff_schedule_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`staff_schedule_id`) REFERENCES `staff_schedules` (`staff_schedule_id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng lưu chi tiết giao dịch VNPay
CREATE TABLE IF NOT EXISTS `vnpay_transactions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `booking_id` BIGINT UNSIGNED NOT NULL,
    `vnp_txn_ref` VARCHAR(100) NOT NULL COMMENT 'Mã giao dịch merchant',
    `vnp_amount` BIGINT NOT NULL COMMENT 'Số tiền * 100',
    `vnp_order_info` TEXT COMMENT 'Thông tin đơn hàng',
    `vnp_transaction_no` VARCHAR(50) NULL COMMENT 'Mã GD tại VNPay',
    `vnp_response_code` VARCHAR(10) NULL COMMENT 'Mã phản hồi',
    `vnp_transaction_status` VARCHAR(10) NULL COMMENT 'Trạng thái GD',
    `vnp_pay_date` VARCHAR(255) NULL COMMENT 'Thời gian thanh toán yyyyMMddHHmmss',
    `vnp_bank_code` VARCHAR(20) NULL COMMENT 'Mã ngân hàng',
    `vnp_bank_tran_no` VARCHAR(255) NULL COMMENT 'Mã GD tại ngân hàng',
    `vnp_card_type` VARCHAR(20) NULL COMMENT 'Loại thẻ',
    `vnp_secure_hash` TEXT NULL COMMENT 'Chữ ký bảo mật',
    `payment_url` TEXT NULL COMMENT 'URL thanh toán',
    `return_url_data` JSON NULL COMMENT 'Dữ liệu từ return URL',
    `ipn_data` JSON NULL COMMENT 'Dữ liệu từ IPN',
    `user_ip` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `status` ENUM('created', 'processing', 'success', 'failed') DEFAULT 'created',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
    
    INDEX `idx_booking_id` (`booking_id`),
    INDEX `idx_vnp_txn_ref` (`vnp_txn_ref`),
    INDEX `idx_vnp_transaction_no` (`vnp_transaction_no`),
    INDEX `idx_status_created` (`status`, `created_at`),
    UNIQUE KEY `uk_booking_vnp_txn` (`booking_id`, `vnp_txn_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `booking_id` BIGINT UNSIGNED NOT NULL,
    `user_id` INT NOT NULL,
    `service_id` INT NOT NULL,
    `rating` TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    `comment` TEXT,
    `is_anonymous` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `accounts`(`user_id`) ON DELETE CASCADE,
    FOREIGN KEY (`service_id`) REFERENCES `services`(`service_id`) ON DELETE CASCADE,
    
    INDEX `idx_rating` (`rating`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Tạo bảng blogs
CREATE TABLE IF NOT EXISTS `blogs` (
    `blog_id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `content` TEXT,
    `excerpt` TEXT,
    `featured_image` VARCHAR(500),
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `meta_title` VARCHAR(255),
    `meta_description` TEXT,
    `view_count` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_slug (slug),
    INDEX idx_created_at (created_at)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;