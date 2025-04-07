-- Insert into tbl_items (Catalog level)
INSERT INTO tbl_items (name, category_id, unit, acquisition_date) VALUES
('Flaring tool', 1, 'sets', NULL),
('Swaging tool', 1, 'sets', NULL),
('Tube cutter', 1, 'pcs', NULL),
('Tube bender (lever type) 5/8', 1, 'pcs', NULL),
('Tube bender (lever type) 1/2', 1, 'pcs', NULL),
('Tube bender (lever type) 5/16', 1, 'pcs', NULL),
('Tube bender (lever type) 3/8', 1, 'pcs', NULL),
('Tube bender (lever type) 1/4', 1, 'pcs', NULL),
('Tube bender (spring type)', 1, 'pcs', NULL),
('Service cylinder, 2.5 kg Capacity', 1, 'pcs', NULL),
('Service cylinder, 10 kg Capacity', 1, 'pcs', NULL),
('Electrical pliers', 1, 'pcs', NULL),
('Pliers, long nose', 1, 'pcs', NULL),
('Pliers, diagonal', 1, 'pcs', NULL),
('Capillary tube cutter', 1, 'pcs', NULL),
('Screwdriver, flat', 1, 'pcs', NULL),
('Screwdriver, Philips', 1, 'pcs', NULL),
('Flat files, fine', 1, 'pcs', NULL),
('Allen wrench, metric', 1, 'pcs', NULL),
('Allen wrench, English', 1, 'sets', NULL),
('Adjustable wrench 8"', 1, 'pcs', NULL),
('Adjustable wrench 10"', 1, 'pcs', NULL),
('Open wrench, metric', 1, 'pcs', NULL),
('Open wrench, English', 1, 'pcs', NULL),
('Box wrench', 1, 'sets', NULL),
('Ratchet wrench (service valve)', 1, 'pcs', NULL),
('Vise grip, 8"', 1, 'pcs', NULL),
('Ballpein hammer, 8 oz', 1, 'pcs', NULL),
('Rubber mallet', 1, 'pcs', NULL),
('Hack saw, standard size', 1, 'pcs', NULL),
('Steel rule, metric & English, 12"', 1, 'pcs', NULL),
('Push rule, 15 meters', 1, 'pcs', NULL),
('L-square, 12"', 1, 'pcs', NULL),
('Pinch-off tool', 1, 'pcs', NULL),
('Soldering iron, 100w, 220 volts', 1, 'pcs', NULL),
('Aviation snip, straight', 1, 'pcs', NULL);


-- ====================================
-- Inserting Units for Each Item
-- ====================================

-- Flaring tool (10 units)
INSERT INTO tbl_item_units (item_id, property_no, brand, model, specification, item_condition, status, date_acquired) 
VALUES 
(1, '1-001', 'RIDGID', '345-DL', NULL, 'GOOD', 'AVAILABLE', NULL),
(1, '1-002', 'RIDGID', '345-DL', NULL, 'GOOD', 'AVAILABLE', NULL),
(1, '1-003', 'RIDGID', '345-DL', NULL, 'GOOD', 'AVAILABLE', NULL),
(1, '1-004', 'RIDGID', '345-DL', NULL, 'GOOD', 'AVAILABLE', NULL),
(1, '1-005', 'RIDGID', '345-DL', NULL, 'GOOD', 'AVAILABLE', NULL),
(1, '1-006', 'RIDGID', '345-DL', NULL, 'GOOD', 'AVAILABLE', NULL),
(1, '1-007', 'RIDGID', '345-DL', NULL, 'GOOD', 'AVAILABLE', NULL),
(1, '1-008', 'RIDGID', '345-DL', NULL, 'GOOD', 'AVAILABLE', NULL),
(1, '1-009', 'RIDGID', '345-DL', NULL, 'GOOD', 'AVAILABLE', NULL),
(1, '1-010', 'RIDGID', '345-DL', NULL, 'GOOD', 'AVAILABLE', NULL);

-- Swaging tool (10 units)
INSERT INTO tbl_item_units (item_id, property_no, brand, model, specification, item_condition, status, date_acquired) 
VALUES 
(2, '2-001', 'Yellow Jacket', '60407', NULL, 'GOOD', 'AVAILABLE', NULL),
(2, '2-002', 'Yellow Jacket', '60407', NULL, 'GOOD', 'AVAILABLE', NULL),
(2, '2-003', 'Yellow Jacket', '60407', NULL, 'GOOD', 'AVAILABLE', NULL),
(2, '2-004', 'Yellow Jacket', '60407', NULL, 'GOOD', 'AVAILABLE', NULL),
(2, '2-005', 'Yellow Jacket', '60407', NULL, 'GOOD', 'AVAILABLE', NULL),
(2, '2-006', 'Yellow Jacket', '60407', NULL, 'GOOD', 'AVAILABLE', NULL),
(2, '2-007', 'Yellow Jacket', '60407', NULL, 'GOOD', 'AVAILABLE', NULL),
(2, '2-008', 'Yellow Jacket', '60407', NULL, 'GOOD', 'AVAILABLE', NULL),
(2, '2-009', 'Yellow Jacket', '60407', NULL, 'GOOD', 'AVAILABLE', NULL),
(2, '2-010', 'Yellow Jacket', '60407', NULL, 'GOOD', 'AVAILABLE', NULL);

-- Tube cutter (10 units)
INSERT INTO tbl_item_units (item_id, property_no, brand, model, specification, item_condition, status, date_acquired) 
VALUES 
(3, '3-001', 'Milwaukee', '48-22-4253', NULL, 'GOOD', 'AVAILABLE', NULL),
(3, '3-002', 'Milwaukee', '48-22-4253', NULL, 'GOOD', 'AVAILABLE', NULL),
(3, '3-003', 'Milwaukee', '48-22-4253', NULL, 'GOOD', 'AVAILABLE', NULL),
(3, '3-004', 'Milwaukee', '48-22-4253', NULL, 'GOOD', 'AVAILABLE', NULL),
(3, '3-005', 'Milwaukee', '48-22-4253', NULL, 'GOOD', 'AVAILABLE', NULL),
(3, '3-006', 'Milwaukee', '48-22-4253', NULL, 'GOOD', 'AVAILABLE', NULL),
(3, '3-007', 'Milwaukee', '48-22-4253', NULL, 'GOOD', 'AVAILABLE', NULL),
(3, '3-008', 'Milwaukee', '48-22-4253', NULL, 'GOOD', 'AVAILABLE', NULL),
(3, '3-009', 'Milwaukee', '48-22-4253', NULL, 'GOOD', 'AVAILABLE', NULL),
(3, '3-010', 'Milwaukee', '48-22-4253', NULL, 'GOOD', 'AVAILABLE', NULL);

-- Tube bender (lever type) 5/8 (3 units)
INSERT INTO tbl_item_units (item_id, property_no, brand, model, specification, item_condition, status, date_acquired) 
VALUES 
(4, '4-001', 'RIDGID', '456', NULL, 'GOOD', 'AVAILABLE', NULL),
(4, '4-002', 'RIDGID', '456', NULL, 'GOOD', 'AVAILABLE', NULL),
(4, '4-003', 'RIDGID', '456', NULL, 'GOOD', 'AVAILABLE', NULL);

-- Tube bender (lever type) 1/2 (3 units)
INSERT INTO tbl_item_units (item_id, property_no, brand, model, specification, item_condition, status, date_acquired) 
VALUES 
(5, '5-001', 'Klein Tools', '89030', NULL, 'GOOD', 'AVAILABLE', NULL),
(5, '5-002', 'Klein Tools', '89030', NULL, 'GOOD', 'AVAILABLE', NULL),
(5, '5-003', 'Klein Tools', '89030', NULL, 'GOOD', 'AVAILABLE', NULL);

-- Tube bender (lever type) 5/16 (3 units)
INSERT INTO tbl_item_units (item_id, property_no, brand, model, specification, item_condition, status, date_acquired) 
VALUES 
(6, '6-001', 'Imperial', '370-FH', NULL, 'GOOD', 'AVAILABLE', NULL),
(6, '6-002', 'Imperial', '370-FH', NULL, 'GOOD', 'AVAILABLE', NULL),
(6, '6-003', 'Imperial', '370-FH', NULL, 'GOOD', 'AVAILABLE', NULL);

-- Tube bender (lever type) 3/8 (3 units)
INSERT INTO tbl_item_units (item_id, property_no, brand, model, specification, item_condition, status, date_acquired) 
VALUES 
(7, '7-001', 'RIDGID', '38048', NULL, 'GOOD', 'AVAILABLE', NULL),
(7, '7-002', 'RIDGID', '38048', NULL, 'GOOD', 'AVAILABLE', NULL),
(7, '7-003', 'RIDGID', '38048', NULL, 'GOOD', 'AVAILABLE', NULL);

-- Tube bender (lever type) 1/4 (3 units)
INSERT INTO tbl_item_units (item_id, property_no, brand, model, specification, item_condition, status, date_acquired) 
VALUES 
(8, '8-001', 'Klein Tools', '89020', NULL, 'GOOD', 'AVAILABLE', NULL),
(8, '8-002', 'Klein Tools', '89020', NULL, 'GOOD', 'AVAILABLE', NULL),
(8, '8-003', 'Klein Tools', '89020', NULL, 'GOOD', 'AVAILABLE', NULL);

-- Tube bender (spring type) (3 units)
INSERT INTO tbl_item_units (item_id, property_no, brand, model, specification, item_condition, status, date_acquired) 
VALUES 
(9, '9-001', 'General Tools', '1271D', NULL, 'GOOD', 'AVAILABLE', NULL),
(9, '9-002', 'General Tools', '1271D', NULL, 'GOOD', 'AVAILABLE', NULL),
(9, '9-003', 'General Tools', '1271D', NULL, 'GOOD', 'AVAILABLE', NULL);

-- Service cylinder, 2.5 kg Capacity (2 units)
INSERT INTO tbl_item_units (item_id, property_no, brand, model, specification, item_condition, status, date_acquired) 
VALUES 
(10, '10-001', 'Robinair', '18191A', NULL, 'GOOD', 'AVAILABLE', NULL),
(10, '10-002', 'Robinair', '18191A', NULL, 'GOOD', 'AVAILABLE', NULL);

-- Service cylinder, 10 kg Capacity (2 units)
INSERT INTO tbl_item_units (item_id, property_no, brand, model, specification, item_condition, status, date_acquired) 
VALUES 
(11, '11-001', 'Yellow Jacket', '14710', NULL, 'GOOD', 'AVAILABLE', NULL),
(11, '11-002', 'Yellow Jacket', '14710', NULL, 'GOOD', 'AVAILABLE', NULL);

-- Electrical pliers (10 units)
INSERT INTO tbl_item_units (item_id, property_no, brand, model, specification, item_condition, status, date_acquired) 
VALUES 
(12, '12-001', 'Klein Tools', 'D213-9NE', NULL, 'GOOD', 'AVAILABLE', NULL),
(12, '12-002', 'Klein Tools', 'D213-9NE', NULL, 'GOOD', 'AVAILABLE', NULL),
(12, '12-003', 'Klein Tools', 'D213-9NE', NULL, 'GOOD', 'AVAILABLE', NULL),
(12, '12-004', 'Klein Tools', 'D213-9NE', NULL, 'GOOD', 'AVAILABLE', NULL),
(12, '12-005', 'Klein Tools', 'D213-9NE', NULL, 'GOOD', 'AVAILABLE', NULL),
(12, '12-006', 'Klein Tools', 'D213-9NE', NULL, 'GOOD', 'AVAILABLE', NULL),
(12, '12-007', 'Klein Tools', 'D213-9NE', NULL, 'GOOD', 'AVAILABLE', NULL),
(12, '12-008', 'Klein Tools', 'D213-9NE', NULL, 'GOOD', 'AVAILABLE', NULL),
(12, '12-009', 'Klein Tools', 'D213-9NE', NULL, 'GOOD', 'AVAILABLE', NULL),
(12, '12-010', 'Klein Tools', 'D213-9NE', NULL, 'GOOD', 'AVAILABLE', NULL);

INSERT INTO tbl_item_units (item_id, property_no, brand, model, specification, item_condition, status, date_acquired) VALUES
(13, '13-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(13, '13-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(13, '13-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(13, '13-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(13, '13-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(14, '14-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(14, '14-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(14, '14-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(14, '14-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(14, '14-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(15, '15-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(15, '15-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(15, '15-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(15, '15-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(15, '15-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(16, '16-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(16, '16-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(16, '16-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(16, '16-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(16, '16-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(17, '17-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(17, '17-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(17, '17-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(17, '17-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(17, '17-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(18, '18-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(18, '18-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(18, '18-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(18, '18-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(18, '18-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(19, '19-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(19, '19-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(19, '19-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(19, '19-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(19, '19-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(20, '20-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(20, '20-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(20, '20-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(20, '20-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(20, '20-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(21, '21-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(21, '21-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(21, '21-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(21, '21-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(21, '21-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(22, '22-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(22, '22-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(22, '22-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(22, '22-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(22, '22-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(23, '23-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(23, '23-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(23, '23-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(23, '23-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(23, '23-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(24, '24-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(24, '24-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(24, '24-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(24, '24-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(24, '24-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(25, '25-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(25, '25-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(25, '25-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(25, '25-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(25, '25-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(26, '26-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(26, '26-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(26, '26-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(26, '26-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(26, '26-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(27, '27-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(27, '27-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(27, '27-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(27, '27-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(27, '27-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(28, '28-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(28, '28-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(28, '28-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(28, '28-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(28, '28-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(29, '29-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(29, '29-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(29, '29-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(29, '29-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(29, '29-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(30, '30-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(30, '30-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(30, '30-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(30, '30-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(30, '30-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(31, '31-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(31, '31-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(31, '31-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(31, '31-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(31, '31-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(32, '32-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(32, '32-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(32, '32-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(32, '32-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(32, '32-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(33, '33-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(33, '33-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(33, '33-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(33, '33-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(33, '33-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(34, '34-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(34, '34-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(34, '34-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(34, '34-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(34, '34-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(35, '35-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(35, '35-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(35, '35-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(35, '35-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(35, '35-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(36, '36-001', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(36, '36-002', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(36, '36-003', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(36, '36-004', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07'),
(36, '36-005', 'Generic', 'Standard', 'N/A', 'GOOD', 'AVAILABLE', '2025-04-07');
