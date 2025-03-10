INSERT INTO tbl_item_category (category_name) VALUES
('Tools'),
('Equipment'),
('Personal Protective Equipment'),
('Materials');

INSERT INTO tbl_items (name, property_no, category_id, quantity, unit, specification, status, item_condition, acquisition_date) VALUES
-- Tools
('Flaring tool', NULL, 1, 10, 'sets', NULL, 'Available', 'Good', NULL),
('Swaging tool', NULL, 1, 10, 'sets', NULL, 'Available', 'Good', NULL),
('Tube cutter', NULL, 1, 10, 'pcs', NULL, 'Available', 'Good', NULL),
('Tube bender (lever type) 5/8', NULL, 1, 3, 'pcs', NULL, 'Available', 'Good', NULL),
('Tube bender (lever type) 1/2', NULL, 1, 3, 'pcs', NULL, 'Available', 'Good', NULL),
('Tube bender (lever type) 5/16', NULL, 1, 3, 'pcs', NULL, 'Available', 'Good', NULL),
('Tube bender (lever type) 3/8', NULL, 1, 3, 'pcs', NULL, 'Available', 'Good', NULL),
('Tube bender (lever type) 1/4', NULL, 1, 3, 'pcs', NULL, 'Available', 'Good', NULL),
('Tube bender (spring type)', NULL, 1, 3, 'pcs', NULL, 'Available', 'Good', NULL),
('Service cylinder, 2.5 kg Capacity', NULL, 1, 2, 'pcs', NULL, 'Available', 'Good', NULL),
('Service cylinder, 10 kg Capacity', NULL, 1, 2, 'pcs', NULL, 'Available', 'Good', NULL),

-- Equipment
('Vacuum pump', NULL, 2, 3, 'units', 'Electric-driven, 5 cfm, portable', 'Available', 'Good', NULL),
('Recovery machine', NULL, 2, 3, 'units', 'Portable, R-22, R-410a', 'Available', 'Good', NULL),
('Refrigerant Cylinder, R-22', NULL, 2, 2, 'units', '10kg', 'Available', 'Good', NULL),
('Window Type AC, 220 volts', NULL, 2, 2, 'units', NULL, 'Available', 'Good', NULL),

-- Personal Protective Equipment (PPE)
('Safety gloves', NULL, 3, 15, 'pairs', NULL, 'Available', 'Good', NULL),
('Safety shoes', NULL, 3, 15, 'pairs', NULL, 'Available', 'Good', NULL),
('Face mask', NULL, 3, 15, 'pcs', NULL, 'Available', 'Good', NULL),

-- Materials
('Copper tube 1/4"', NULL, 4, 50, 'roll', '50 ft. per roll', 'Available', 'Good', NULL),
('Aluminum tube 3/8"', NULL, 4, 50, 'roll', '100 ft. per roll', 'Available', 'Good', NULL),
('Filter drier', NULL, 4, 15, 'pcs', '1/4" OD flared connection', 'Available', 'Good', NULL);

-- Insert sample users
INSERT INTO tbl_users (username, name, password, email, role_id) VALUES
('superadmin1', 'John Doe', 'supersecurepassword', 'superadmin@example.com', 1),
('admin1', 'Alice Smith', 'adminpassword', 'admin@example.com', 2),
('admin2', 'Bob Johnson', 'adminpassword', 'admin2@example.com', 2),
('instructor1', 'Charlie Brown', 'instructorpassword', 'instructor@example.com', 3),
('instructor2', 'Eve Davis', 'instructorpassword', 'instructor2@example.com', 3);
