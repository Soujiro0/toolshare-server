CREATE DATABASE db_borrowing_system;
USE db_borrowing_system;

-- ====================================
-- 1. ROLES TABLE (For Admins & Faculty)
-- ====================================
CREATE TABLE tbl_roles (
    role_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

-- Insert predefined roles
INSERT INTO tbl_roles (role_name) VALUES ('Super Admin'), ('Admin'), ('Faculty');

-- ====================================
-- 2. USERS TABLE (Admins & Faculty in One Table)
-- ====================================
CREATE TABLE tbl_users (
    user_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    user_type ENUM('admin', 'faculty') NOT NULL, -- Differentiates users
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES tbl_roles(role_id) ON DELETE CASCADE
);

-- ====================================
-- 3. ITEM CATEGORIES TABLE
-- ====================================
CREATE TABLE tbl_item_category (
    category_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
);

-- ====================================
-- 4. ITEMS TABLE
-- ====================================
CREATE TABLE tbl_items (
    item_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_code VARCHAR(50) NOT NULL UNIQUE,
    item_name VARCHAR(100) NOT NULL,
    item_brand VARCHAR(100) NULL,
    model VARCHAR(100) NULL,
    is_bulk BOOLEAN NOT NULL DEFAULT FALSE,
    total_quantity INT UNSIGNED NOT NULL DEFAULT 0,
    category_id BIGINT UNSIGNED NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
    FOREIGN KEY (category_id) REFERENCES tbl_item_category(category_id) ON DELETE CASCADE
);

-- ====================================
-- 5. BORROW REQUESTS TABLE
-- ====================================
CREATE TABLE tbl_borrow_requests (
    request_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    borrower_name VARCHAR(100) NOT NULL,
    faculty_user_id BIGINT UNSIGNED NULL,
    faculty_verified BOOLEAN NOT NULL DEFAULT FALSE,
    item_borrowed JSON NOT NULL,
    purpose TEXT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    request_status ENUM('PENDING_FACULTY_APPROVAL', 'REJECTED_BY_FACULTY', 'APPROVED', 'CANCELLED') NOT NULL DEFAULT 'PENDING_FACULTY_APPROVAL',
    FOREIGN KEY (faculty_user_id) REFERENCES tbl_users(user_id) ON DELETE SET NULL
);

-- ====================================
-- 6. TRANSACTIONS TABLE
-- ====================================
CREATE TABLE tbl_transactions (
    transaction_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id BIGINT UNSIGNED NOT NULL,
    transaction_status ENUM('PENDING_ADMIN_APPROVAL', 'REJECTED_BY_ADMIN', 'BORROWED', 'RETURNED') NOT NULL DEFAULT 'PENDING_ADMIN_APPROVAL',
    admin_verified BOOLEAN NOT NULL DEFAULT FALSE,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES tbl_borrow_requests(request_id) ON DELETE CASCADE
);

-- ====================================
-- 7. ACTIVITY LOGS TABLE (Tracks Admin & Faculty Actions)
-- ====================================
CREATE TABLE tbl_activity_logs (
    log_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    action_type ENUM('CREATE', 'UPDATE', 'DELETE', 'LOGIN', 'REQUEST', 'REJECT', 'APPROVED', 'BORROWED', 'RETURN') NOT NULL,
    action_description TEXT NOT NULL,
    action_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    module ENUM('AUTH_LOGS', 'INVENTORY_LOGS', 'ACTIVITY_LOGS', 'BORROW_LOGS', 'TRANSACTION_LOGS') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES tbl_roles(role_id) ON DELETE CASCADE
);