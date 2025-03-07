CREATE DATABASE db_tool_share_borrowing_system;
USE db_tool_share_borrowing_system;

-- ====================================
-- 1. ROLES TABLE (For Admins & Instructor)
-- ====================================
CREATE TABLE tbl_roles (
    role_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

-- Insert predefined roles
INSERT INTO tbl_roles (role_name) VALUES ('Super Admin'), ('Admin'), ('Instuctor');

-- ====================================
-- 2. USERS TABLE (Admins & Instructor in One Table)
-- ====================================
CREATE TABLE tbl_users (
    user_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES tbl_roles(role_id) ON DELETE CASCADE
);

-- ====================================
-- 3. ITEM CATEGORIES TABLE
-- ====================================
CREATE TABLE tbl_item_category (
    category_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
);

-- ====================================
-- 4. ITEMS TABLE
-- ====================================
CREATE TABLE tbl_items (
    item_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    property_no VARCHAR(50) UNIQUE,
    category_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit VARCHAR(20) NOT NULL,
    specification TEXT,
    status ENUM('AVAILABLE', 'NO STOCK', 'IN USE', 'UNDER REPAIR') NOT NULL DEFAULT 'AVAILABLE',
    item_condition ENUM('EXCELLENT', 'GOOD', 'FAIR', 'POOR') NOT NULL DEFAULT 'GOOD',
    acquisition_date DATE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES tbl_item_category(category_id) ON DELETE CASCADE
);

-- ====================================
-- 5. BORROW REQUESTS TABLE (The borrowing slip)
-- ====================================
CREATE TABLE tbl_borrow_requests (
    request_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('PENDING', 'APPROVED', 'REJECTED', 'BORROWED', 'RETURNED') DEFAULT 'PENDING',
    remarks TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE
);

-- ====================================
-- 6. BORROW REQUEST ITEMS TABLE (The line items on the slip)
-- ====================================
CREATE TABLE tbl_borrow_request_items (
    request_item_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id BIGINT UNSIGNED NOT NULL,
    item_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    item_condition_out ENUM('EXCELLENT', 'GOOD', 'FAIR', 'POOR') DEFAULT 'GOOD',
    item_condition_in ENUM('EXCELLENT', 'GOOD', 'FAIR', 'POOR') DEFAULT 'GOOD',
    damage_notes TEXT,
    returned_date TIMESTAMP NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES tbl_borrow_requests(request_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES tbl_items(item_id) ON DELETE CASCADE
);