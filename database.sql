-- =====================================
-- 1. Create Database and Use It
-- =====================================
CREATE DATABASE IF NOT EXISTS borrowing_system;
USE borrowing_system;

-- =====================================
-- 2. Lookup Tables (Static Data)
-- =====================================

-- Roles: stores user roles (for admins and super admins)
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);
-- Insert default roles (if not already inserted)
INSERT IGNORE INTO roles (name) VALUES ('super_admin'), ('admin');

-- Categories: for organizing items
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- =====================================
-- 3. Users (Admin & Super Admin Combined)
-- =====================================
-- Instead of separate tables for super_admins and admins, we combine them into one table.
-- Admin Users Table (Combined for Admins and Super Admins)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,        -- Full name
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- =====================================
-- 4. Faculty (Instructors)
-- =====================================
CREATE TABLE IF NOT EXISTS faculty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    contact_number VARCHAR(15),
    email VARCHAR(100) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================
-- 5. Borrow Requests
-- =====================================
-- Form-based borrow requests (submitted by students; signed by instructor and verified by admin)
CREATE TABLE IF NOT EXISTS borrow_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    course_year VARCHAR(50) NOT NULL,  -- e.g., 'BSIT 2nd Year'
    instructor_id INT NOT NULL,        -- references faculty
    purpose TEXT NOT NULL,
    signature_confirmed BOOLEAN DEFAULT FALSE,
    admin_verified BOOLEAN DEFAULT FALSE,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES faculty(id) ON DELETE CASCADE
);

-- =====================================
-- 6. Items & Inventory
-- =====================================
CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL,  -- Denormalized category name for quick access
    total_quantity INT NOT NULL CHECK (total_quantity >= 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    category_id INT,  -- Foreign key reference to categories table
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- For per-unit condition tracking (optional)
CREATE TABLE IF NOT EXISTS item_conditions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    condition_status ENUM('New', 'Good', 'Fair', 'Damaged') NOT NULL DEFAULT 'Good',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
);

-- =====================================
-- 7. Transactions & Damage Reports
-- =====================================
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,  -- link to borrow_requests
    item_id INT NOT NULL,     -- which item is borrowed
    quantity INT NOT NULL CHECK (quantity > 0),
    borrowed_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    due_date DATE NOT NULL,
    returned_date TIMESTAMP NULL,
    status ENUM('Pending', 'Borrowed', 'Returned', 'Overdue', 'Damaged') DEFAULT 'Pending',
    verified_by INT,  -- admin who approved
    received_by INT,  -- admin who processed return
    damage_report TEXT,  -- if item is damaged
    FOREIGN KEY (request_id) REFERENCES borrow_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES admin_users(id) ON DELETE SET NULL,
    FOREIGN KEY (received_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS damage_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    reported_by INT NOT NULL,  -- admin who reported damage
    report_details TEXT NOT NULL,
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (reported_by) REFERENCES admin_users(id) ON DELETE CASCADE
);

-- =====================================
-- 8. Activity Logs
-- =====================================
-- Logs admin actions; we reference the admin_users table for normalization.
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,           -- Admin user id performing the action
    user_name VARCHAR(100) NOT NULL, -- Denormalized user name
    role VARCHAR(50) NOT NULL,       -- Denormalized role of the user (e.g., 'super_admin' or 'admin')
    action_type VARCHAR(50) NOT NULL, -- e.g., Create, Update, Delete, etc.
    action TEXT NOT NULL,
    action_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admin_users(id)
);

-- =====================================
-- 9. Indexes & Performance Optimizations
-- =====================================
CREATE INDEX idx_request_status ON borrow_requests(signature_confirmed, admin_verified);
CREATE INDEX idx_transaction_status ON transactions(status);
CREATE INDEX idx_activity_user ON activity_logs(user_id);
