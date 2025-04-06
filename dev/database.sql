-- ====================================
-- 1. ROLES TABLE
-- ====================================
CREATE TABLE tbl_roles (
    role_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

-- Insert sample roles
INSERT INTO tbl_roles (role_name) VALUES ('SUPER_ADMIN'), ('ADMIN'), ('INSTRUCTOR');

-- ====================================
-- 2. USERS TABLE
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

-- Insert sample users
INSERT INTO tbl_users (username, name, password, email, role_id) VALUES
('superadmin', 'Jerry Castrudes', '$2y$10$5HPgFmOJ8Sa8d4r3/0UI6eSFD/3pbHpDrWw6H/2WnSefq0wNbm9j6', 'superadmin@example.com', 1),
('admin1', 'Alice Smith', '$2y$10$5HPgFmOJ8Sa8d4r3/0UI6eSFD/3pbHpDrWw6H/2WnSefq0wNbm9j6', 'admin@example.com', 2),
('admin2', 'Bob Marley', '$2y$10$5HPgFmOJ8Sa8d4r3/0UI6eSFD/3pbHpDrWw6H/2WnSefq0wNbm9j6', 'admin2@example.com', 2),
('instructor1', 'Charlie Putt', '$2y$10$5HPgFmOJ8Sa8d4r3/0UI6eSFD/3pbHpDrWw6H/2WnSefq0wNbm9j6', 'instructor@example.com', 3),
('instructor2', 'Shet Sharon', '$2y$10$5HPgFmOJ8Sa8d4r3/0UI6eSFD/3pbHpDrWw6H/2WnSefq0wNbm9j6', 'instructor2@example.com', 3);

-- ====================================
-- 3. ITEM CATEGORY TABLE
-- ====================================
CREATE TABLE tbl_item_category (
    category_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
);

-- Insert sample item categories
INSERT INTO tbl_item_category (category_name) VALUES
('Tools'),
('Equipment'),
('Personal Protective Equipment'),
('Materials');

-- ====================================
-- 4. ITEMS TABLE (Catalog/Template level)
-- ====================================
CREATE TABLE tbl_items (
    item_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    unit VARCHAR(20) NOT NULL,
    acquisition_date DATE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES tbl_item_category(category_id) ON DELETE CASCADE
);

-- ====================================
-- 5. ITEM UNITS TABLE (Physical units of each item)
-- ====================================
CREATE TABLE tbl_item_units (
    unit_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_id BIGINT UNSIGNED NOT NULL,
    property_no VARCHAR(50) UNIQUE NOT NULL,
    brand VARCHAR(100),
    model VARCHAR(100),
    specification TEXT,
    item_condition ENUM('EXCELLENT', 'GOOD', 'FAIR', 'POOR') NOT NULL DEFAULT 'GOOD',
    status ENUM('AVAILABLE', 'IN_USE', 'UNDER_REPAIR') NOT NULL DEFAULT 'AVAILABLE',
    date_acquired DATE,
    FOREIGN KEY (item_id) REFERENCES tbl_items(item_id) ON DELETE CASCADE
);

-- ====================================
-- 6. BORROW REQUESTS TABLE
-- ====================================
CREATE TABLE tbl_borrow_requests (
    request_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    status ENUM('PENDING', 'APPROVED', 'REJECTED', 'CLAIMED', 'RETURNED') DEFAULT 'PENDING',
    remarks TEXT NULL,
    handled_by BIGINT UNSIGNED NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    return_date TIMESTAMP NULL DEFAULT NULL,
    processed_date TIMESTAMP NULL DEFAULT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES tbl_users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (handled_by) REFERENCES tbl_users(user_id) ON DELETE SET NULL
);

-- ====================================
-- 7. BORROW REQUESTS TABLE
-- ====================================

CREATE TABLE tbl_borrow_request_summary (
    request_summary_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id BIGINT UNSIGNED NOT NULL,
    item_id BIGINT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES tbl_borrow_requests(request_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES tbl_items(item_id) ON DELETE CASCADE
);

-- ====================================
-- 8. BORROW REQUEST ITEMS TABLE
-- ====================================
CREATE TABLE tbl_borrow_request_items (
    request_item_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id BIGINT UNSIGNED NOT NULL,
    unit_id BIGINT UNSIGNED NOT NULL,
    item_condition_out ENUM('EXCELLENT', 'GOOD', 'FAIR', 'POOR') DEFAULT 'GOOD',
    item_condition_in ENUM('EXCELLENT', 'GOOD', 'FAIR', 'POOR') DEFAULT 'GOOD',
    damage_status ENUM('DAMAGED', 'UNDAMAGED') DEFAULT 'UNDAMAGED',
    damage_notes TEXT,
    returned_date TIMESTAMP NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES tbl_borrow_requests(request_id) ON DELETE CASCADE,
    FOREIGN KEY (unit_id) REFERENCES tbl_item_units(unit_id) ON DELETE CASCADE
);