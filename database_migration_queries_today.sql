-- =====================================================
-- Database Migration Queries for Today (2025-12-26)
-- Run these queries on your live database
-- =====================================================
-- NOTE: These are COMPLETELY NEW tables, not based on any existing table
-- =====================================================

-- =====================================================
-- 1. CREATE TABLE: debit_notes (NEW TABLE - NOT FROM QUOTATIONS)
-- =====================================================
CREATE TABLE IF NOT EXISTS `debit_notes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `debit_note_number` VARCHAR(255) NOT NULL,
  `debit_note_date` DATE NOT NULL,
  `reference_document_type` ENUM('Purchase Invoice', 'Sales Invoice', 'Dispatch', 'Manual') NULL,
  `reference_document_number` VARCHAR(255) NULL,
  `reference_document_id` BIGINT UNSIGNED NULL,
  `party_type` ENUM('Supplier', 'Customer') NULL,
  `party_id` BIGINT UNSIGNED NULL,
  `party_name` VARCHAR(255) NULL,
  `gst_number` VARCHAR(255) NULL,
  `currency` VARCHAR(255) NOT NULL DEFAULT 'INR',
  `gst_classification` ENUM('CGST_SGST', 'IGST') NULL,
  `gst_percentage` DECIMAL(5, 2) NOT NULL DEFAULT 18.00,
  `debit_note_reason` ENUM('Purchase Return', 'Rate Difference', 'Short Supply', 'Damage Compensation', 'Others') NULL,
  `remarks` TEXT NULL,
  `subtotal` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `cgst_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `sgst_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `igst_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `adjustments` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `total_debit_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `status` ENUM('Draft', 'Submitted', 'Cancelled') NOT NULL DEFAULT 'Draft',
  `submitted_by` BIGINT UNSIGNED NULL,
  `submitted_at` TIMESTAMP NULL,
  `cancel_reason` TEXT NULL,
  `organization_id` BIGINT UNSIGNED NULL,
  `branch_id` BIGINT UNSIGNED NULL,
  `created_by` BIGINT UNSIGNED NULL,
  `updated_by` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `deleted_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `debit_notes_debit_note_number_unique` (`debit_note_number`),
  KEY `debit_notes_debit_note_number_index` (`debit_note_number`),
  KEY `debit_notes_reference_document_type_index` (`reference_document_type`),
  KEY `debit_notes_status_index` (`status`),
  KEY `debit_notes_organization_id_foreign` (`organization_id`),
  KEY `debit_notes_branch_id_foreign` (`branch_id`),
  KEY `debit_notes_created_by_foreign` (`created_by`),
  KEY `debit_notes_updated_by_foreign` (`updated_by`),
  CONSTRAINT `debit_notes_organization_id_foreign` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `debit_notes_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `debit_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `debit_notes_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. CREATE TABLE: debit_note_items (NEW TABLE - NOT FROM QUOTATIONS)
-- =====================================================
CREATE TABLE IF NOT EXISTS `debit_note_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `debit_note_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NULL,
  `item_name` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `quantity` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  `unit_of_measure` VARCHAR(255) NULL,
  `rate` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `cgst_percentage` DECIMAL(5, 2) NOT NULL DEFAULT 0.00,
  `cgst_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `sgst_percentage` DECIMAL(5, 2) NOT NULL DEFAULT 0.00,
  `sgst_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `igst_percentage` DECIMAL(5, 2) NOT NULL DEFAULT 0.00,
  `igst_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `line_total` DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `debit_note_items_debit_note_id_foreign` (`debit_note_id`),
  KEY `debit_note_items_product_id_foreign` (`product_id`),
  CONSTRAINT `debit_note_items_debit_note_id_foreign` FOREIGN KEY (`debit_note_id`) REFERENCES `debit_notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `debit_note_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. ALTER TABLE: Add gst_classification column to debit_notes
-- (Only run if the table already exists without this column)
-- =====================================================
-- Check if column exists first, if not, run this:
ALTER TABLE `debit_notes` 
ADD COLUMN `gst_classification` ENUM('CGST_SGST', 'IGST') NULL AFTER `currency`;

-- =====================================================
-- 4. ALTER TABLE: Add gst_percentage column to debit_notes
-- (Only run if the table already exists without this column)
-- =====================================================
-- Check if column exists first, if not, run this:
ALTER TABLE `debit_notes` 
ADD COLUMN `gst_percentage` DECIMAL(5, 2) NOT NULL DEFAULT 18.00 AFTER `gst_classification`;

-- =====================================================
-- IMPORTANT NOTES:
-- =====================================================
-- 1. These are COMPLETELY NEW tables - NOT copied from quotations or any other table
-- 2. If the debit_notes table already exists, you only need to run queries 3 and 4
-- 3. If the debit_notes table doesn't exist, run query 1 (which includes gst_classification and gst_percentage)
-- 4. Always run query 2 to create the debit_note_items table (if it doesn't exist)
-- 5. Make sure the referenced tables exist: organizations, branches, users, products
-- 6. If you get foreign key constraint errors, check that the referenced tables exist
-- 7. These tables are independent and do NOT reference or use quotations table structure

-- =====================================================
-- VERIFICATION QUERIES (Run after migration)
-- =====================================================
-- Check if tables were created:
-- SHOW TABLES LIKE 'debit_notes';
-- SHOW TABLES LIKE 'debit_note_items';

-- Check table structure:
-- DESCRIBE debit_notes;
-- DESCRIBE debit_note_items;

-- Check if columns exist:
-- SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'debit_notes' 
-- AND COLUMN_NAME IN ('gst_classification', 'gst_percentage');

