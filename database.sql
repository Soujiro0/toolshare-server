CREATE DATABASE borrowing_system;
USE borrowing_system;

-- ================================
-- USERS TABLES
-- ================================

-- Super Admin Table
CREATE TABLE super_admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin Table (For managing borrow & return transactions)
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Faculty (Instructors)
CREATE TABLE faculty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    contact_number VARCHAR(15),
    email VARCHAR(100) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================
-- BORROW REQUEST (FORM-BASED)
-- ================================
-- Students fill out this form to request borrowing items;
-- must be signed by instructor and verified by admin.

CREATE TABLE borrow_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    course_year VARCHAR(50) NOT NULL, -- e.g. 'BSIT 2nd Year'
    instructor_id INT NOT NULL,       -- Instructor in charge
    purpose TEXT NOT NULL,
    signature_confirmed BOOLEAN DEFAULT FALSE, -- Instructor must approve
    admin_verified BOOLEAN DEFAULT FALSE,      -- Admin must verify before lending
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (instructor_id) REFERENCES faculty(id) ON DELETE CASCADE
);

-- ================================
-- ITEMS TABLE
-- ================================

CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL,
    total_quantity INT NOT NULL CHECK (total_quantity >= 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Condition of each item (per unit tracking, optional usage)
CREATE TABLE item_conditions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    condition_status ENUM('New', 'Good', 'Fair', 'Damaged') NOT NULL DEFAULT 'Good',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
);

-- ================================
-- TRANSACTION LOGS (BORROWING & RETURNING)
-- ================================
-- Links borrow_requests to actual item transactions.

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,  -- Link to borrow_requests
    item_id INT NOT NULL,     -- Which item is borrowed
    quantity INT NOT NULL CHECK (quantity > 0),
    borrowed_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    due_date DATE NOT NULL,
    returned_date TIMESTAMP NULL,
    status ENUM('Pending','Borrowed','Returned','Overdue','Damaged') DEFAULT 'Pending',
    verified_by INT,          -- Admin who approved the borrowing
    received_by INT,          -- Admin who processed the return
    damage_report TEXT NULL,  -- If damaged, store notes
    FOREIGN KEY (request_id) REFERENCES borrow_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES admins(id) ON DELETE SET NULL,
    FOREIGN KEY (received_by) REFERENCES admins(id) ON DELETE SET NULL
);

-- ================================
-- DAMAGE REPORTS (IF ITEM IS DAMAGED)
-- ================================

CREATE TABLE damage_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    reported_by INT NOT NULL,  -- Admin who reported
    report_details TEXT NOT NULL,
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (reported_by) REFERENCES admins(id) ON DELETE CASCADE
);

-- ================================
-- ACTIVITY LOGS (Super Admin/Admin Actions)
-- ================================

CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('Super Admin', 'Admin') NOT NULL,
    user_id INT NOT NULL,
    action TEXT NOT NULL,
    action_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ================================
-- ADDITIONAL TABLES
-- ================================

-- Categories (For better organization of items)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);

-- Roles (For Users, especially admins and super admins)
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);
INSERT INTO roles (name) VALUES ('super_admin'), ('admin');

-- Map Items to Categories
ALTER TABLE items ADD COLUMN category_id INT;
ALTER TABLE items ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

-- ================================
-- INDEXES & PERFORMANCE OPTIMIZATION
-- ================================

CREATE INDEX idx_request_status ON borrow_requests(signature_confirmed, admin_verified);
CREATE INDEX idx_transaction_status ON transactions(status);
CREATE INDEX idx_activity_user ON activity_logs(user_id);

-- Map Users to Roles
ALTER TABLE super_admins
ADD COLUMN role_id INT NOT NULL DEFAULT 1,
ADD CONSTRAINT fk_super_admins_role FOREIGN KEY (role_id) REFERENCES roles(id);

ALTER TABLE admins
ADD COLUMN role_id INT NOT NULL DEFAULT 2,
ADD CONSTRAINT fk_admins_role FOREIGN KEY (role_id) REFERENCES roles(id);
