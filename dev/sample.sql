INSERT INTO tbl_item_category (category_name) VALUES
('Tools'),
('Equipment'),
('Personal Protective Equipment'),
('Materials');

INSERT INTO tbl_items (name, property_no, category_id, quantity, unit, brand, model, specification, status, item_condition, acquisition_date) VALUES
-- Tools
('Flaring tool', NULL, 1, 10, 'sets', 'RIDGID', '345-DL', NULL, 'AVAILABLE', 'GOOD', NULL),
('Swaging tool', NULL, 1, 10, 'sets', 'Yellow Jacket', '60407', NULL, 'AVAILABLE', 'GOOD', NULL),
('Tube cutter', NULL, 1, 10, 'pcs', 'Milwaukee', '48-22-4253', NULL, 'AVAILABLE', 'GOOD', NULL),
('Tube bender (lever type) 5/8', NULL, 1, 3, 'pcs', 'RIDGID', '456', NULL, 'AVAILABLE', 'GOOD', NULL),
('Tube bender (lever type) 1/2', NULL, 1, 3, 'pcs', 'Klein Tools', '89030', NULL, 'AVAILABLE', 'GOOD', NULL),
('Tube bender (lever type) 5/16', NULL, 1, 3, 'pcs', 'Imperial', '370-FH', NULL, 'AVAILABLE', 'GOOD', NULL),
('Tube bender (lever type) 3/8', NULL, 1, 3, 'pcs', 'RIDGID', '38048', NULL, 'AVAILABLE', 'GOOD', NULL),
('Tube bender (lever type) 1/4', NULL, 1, 3, 'pcs', 'Klein Tools', '89020', NULL, 'AVAILABLE', 'GOOD', NULL),
('Tube bender (spring type)', NULL, 1, 3, 'pcs', 'General Tools', '1271D', NULL, 'AVAILABLE', 'GOOD', NULL),
('Service cylinder, 2.5 kg Capacity', NULL, 1, 2, 'pcs', 'Robinair', '18191A', NULL, 'AVAILABLE', 'GOOD', NULL),
('Service cylinder, 10 kg Capacity', NULL, 1, 2, 'pcs', 'Yellow Jacket', '14710', NULL, 'AVAILABLE', 'GOOD', NULL),
('Electrical pliers', NULL, 1, 10, 'pcs', 'Klein Tools', 'D213-9NE', NULL, 'AVAILABLE', 'GOOD', NULL),
('Pliers, long nose', NULL, 1, 10, 'pcs', 'Klein Tools', 'D203-8N', NULL, 'AVAILABLE', 'GOOD', NULL),
('Pliers, diagonal', NULL, 1, 10, 'pcs', 'Channellock', '337', NULL, 'AVAILABLE', 'GOOD', NULL),
('Capillary tube cutter', NULL, 1, 10, 'pcs', 'General Tools', 'TC123', NULL, 'AVAILABLE', 'GOOD', NULL),
('Screwdriver, flat', NULL, 1, 10, 'pcs', 'Stanley', '66-565', NULL, 'AVAILABLE', 'GOOD', NULL),
('Screwdriver, Philips', NULL, 1, 10, 'pcs', 'Stanley', 'PH2', NULL, 'AVAILABLE', 'GOOD', NULL),
('Flat files, fine', NULL, 1, 10, 'pcs', 'Nicholson', '06601N', NULL, 'AVAILABLE', 'GOOD', NULL),
('Allen wrench, metric', NULL, 1, 10, 'pcs', 'Bondhus', '12137', NULL, 'AVAILABLE', 'GOOD', NULL),
('Allen wrench, English', NULL, 1, 5, 'sets', 'Bondhus', '17092', NULL, 'AVAILABLE', 'GOOD', NULL),
('Adjustable wrench 8"', NULL, 1, 10, 'pcs', 'Crescent', 'AC28VS', NULL, 'AVAILABLE', 'GOOD', NULL),
('Adjustable wrench 10"', NULL, 1, 10, 'pcs', 'Crescent', 'AC210VS', NULL, 'AVAILABLE', 'GOOD', NULL),
('Open wrench, metric', NULL, 1, 10, 'pcs', 'Tekton', '90191', NULL, 'AVAILABLE', 'GOOD', NULL),
('Open wrench, English', NULL, 1, 10, 'pcs', 'Tekton', '90201', NULL, 'AVAILABLE', 'GOOD', NULL),
('Box wrench', NULL, 1, 5, 'sets', 'GearWrench', '85988', NULL, 'AVAILABLE', 'GOOD', NULL),
('Ratchet wrench (service valve)', NULL, 1, 10, 'pcs', 'Yellow Jacket', '60613', NULL, 'AVAILABLE', 'GOOD', NULL),
('Vise grip, 8"', NULL, 1, 5, 'pcs', 'Irwin', '10WR', NULL, 'AVAILABLE', 'GOOD', NULL),
('Ballpein hammer, 8 oz', NULL, 1, 10, 'pcs', 'Stanley', '54-008', NULL, 'AVAILABLE', 'GOOD', NULL),
('Rubber mallet', NULL, 1, 10, 'pcs', 'Estwing', 'DFH12', NULL, 'AVAILABLE', 'GOOD', NULL),
('Hack saw, standard size', NULL, 1, 10, 'pcs', 'Stanley', '15-113', NULL, 'AVAILABLE', 'GOOD', NULL),
('Steel rule, metric & English, 12"', NULL, 1, 10, 'pcs', 'Westcott', '10414', NULL, 'AVAILABLE', 'GOOD', NULL),
('Push rule, 15 meters', NULL, 1, 10, 'pcs', 'Stanley', '33-725', NULL, 'AVAILABLE', 'GOOD', NULL),
('L-square, 12"', NULL, 1, 5, 'pcs', 'Empire', '1240', NULL, 'AVAILABLE', 'GOOD', NULL),
('Pinch-off tool', NULL, 1, 10, 'pcs', 'CPS', 'TLOP', NULL, 'AVAILABLE', 'GOOD', NULL),
('Soldering iron, 100w, 220 volts', NULL, 1, 5, 'pcs', 'Weller', 'SPG100', NULL, 'AVAILABLE', 'GOOD', NULL),
('Aviation snip, straight', NULL, 1, 2, 'pcs', 'MIDWEST', 'MWT-6716S', NULL, 'AVAILABLE', 'GOOD', NULL);

-- Insert sample users
INSERT INTO tbl_users (username, name, password, email, role_id) VALUES
('superadmin1', 'John Doe', '$2y$10$5HPgFmOJ8Sa8d4r3/0UI6eSFD/3pbHpDrWw6H/2WnSefq0wNbm9j6', 'superadmin@example.com', 1),
('admin1', 'Alice Smith', '$2y$10$5HPgFmOJ8Sa8d4r3/0UI6eSFD/3pbHpDrWw6H/2WnSefq0wNbm9j6', 'admin@example.com', 2),
('admin2', 'Bob Johnson', '$2y$10$5HPgFmOJ8Sa8d4r3/0UI6eSFD/3pbHpDrWw6H/2WnSefq0wNbm9j6', 'admin2@example.com', 2),
('instructor1', 'Charlie Brown', '$2y$10$5HPgFmOJ8Sa8d4r3/0UI6eSFD/3pbHpDrWw6H/2WnSefq0wNbm9j6', 'instructor@example.com', 3),
('instructor2', 'Eve Davis', '$2y$10$5HPgFmOJ8Sa8d4r3/0UI6eSFD/3pbHpDrWw6H/2WnSefq0wNbm9j6', 'instructor2@example.com', 3);