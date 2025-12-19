-- =====================================================
-- Database Changes for Today (2025-12-19)
-- =====================================================

-- =====================================================
-- 1. PETTY CASHES TABLE
-- =====================================================
CREATE TABLE `petty_cashes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `expense_id` VARCHAR(191) NOT NULL,
  `date` DATE NOT NULL,
  `expense_category` VARCHAR(191) NOT NULL,
  `description` TEXT NULL,
  `amount` DECIMAL(15, 2) NOT NULL,
  `payment_method` ENUM('Cash', 'Credit', 'Debit') NOT NULL,
  `paid_to` VARCHAR(191) NOT NULL,
  `receipt_path` VARCHAR(191) NULL,
  `remarks` TEXT NULL,
  `organization_id` BIGINT UNSIGNED NULL,
  `branch_id` BIGINT UNSIGNED NULL,
  `created_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `petty_cashes_expense_id_unique` (`expense_id`),
  KEY `petty_cashes_organization_id_foreign` (`organization_id`),
  KEY `petty_cashes_branch_id_foreign` (`branch_id`),
  KEY `petty_cashes_created_by_foreign` (`created_by`),
  CONSTRAINT `petty_cashes_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `petty_cashes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `petty_cashes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. ATTENDANCES TABLE
-- =====================================================
CREATE TABLE `attendances` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` DATE NOT NULL,
  `employee_id` BIGINT UNSIGNED NOT NULL,
  `status` ENUM('Present', 'Absent') NOT NULL DEFAULT 'Present',
  `organization_id` BIGINT UNSIGNED NULL,
  `branch_id` BIGINT UNSIGNED NULL,
  `created_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendances_date_employee_id_unique` (`date`, `employee_id`),
  KEY `attendances_employee_id_foreign` (`employee_id`),
  KEY `attendances_organization_id_foreign` (`organization_id`),
  KEY `attendances_branch_id_foreign` (`branch_id`),
  KEY `attendances_created_by_foreign` (`created_by`),
  CONSTRAINT `attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendances_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `attendances_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. LEAVE TYPES TABLE
-- =====================================================
CREATE TABLE `leave_types` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(191) NOT NULL,
  `description` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `organization_id` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `leave_types_name_unique` (`name`),
  KEY `leave_types_organization_id_foreign` (`organization_id`),
  CONSTRAINT `leave_types_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. LEAVES TABLE
-- =====================================================
CREATE TABLE `leaves` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` BIGINT UNSIGNED NOT NULL,
  `leave_type_id` BIGINT UNSIGNED NOT NULL,
  `date` DATE NOT NULL,
  `change_to_present` TINYINT(1) NOT NULL DEFAULT 0,
  `remarks` TEXT NULL,
  `organization_id` BIGINT UNSIGNED NULL,
  `branch_id` BIGINT UNSIGNED NULL,
  `created_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leaves_employee_id_foreign` (`employee_id`),
  KEY `leaves_leave_type_id_foreign` (`leave_type_id`),
  KEY `leaves_organization_id_foreign` (`organization_id`),
  KEY `leaves_branch_id_foreign` (`branch_id`),
  KEY `leaves_created_by_foreign` (`created_by`),
  CONSTRAINT `leaves_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leaves_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leaves_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leaves_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leaves_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- INSERT DEFAULT LEAVE TYPES
-- =====================================================
INSERT INTO `leave_types` (`name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
('Casual Leave', 'Casual leave for personal reasons', 1, NOW(), NOW()),
('Sick Leave', 'Leave for medical reasons', 1, NOW(), NOW()),
('Earned Leave', 'Earned leave based on service', 1, NOW(), NOW()),
('Compensatory Leave', 'Compensatory leave for overtime work', 1, NOW(), NOW()),
('Maternity Leave', 'Maternity leave for female employees', 1, NOW(), NOW()),
('Paternity Leave', 'Paternity leave for male employees', 1, NOW(), NOW());

