INSERT INTO tbl_users (username, name, password, email, role_id, user_type) VALUES
('superadmin', 'Super Admin', 'hashed_pass1', 'superadmin@example.com', 1, 'admin'),
('admin1', 'Admin One', 'hashed_pass2', 'admin1@example.com', 2, 'admin'),
('admin2', 'Admin Two', 'hashed_pass3', 'admin2@example.com', 2, 'admin'),
('faculty1', 'Faculty One', 'hashed_pass4', 'faculty1@example.com', 3, 'faculty'),
('faculty2', 'Faculty Two', 'hashed_pass5', 'faculty2@example.com', 3, 'faculty'),
('faculty3', 'Faculty Three', 'hashed_pass6', 'faculty3@example.com', 3, 'faculty'),
('faculty4', 'Faculty Four', 'hashed_pass7', 'faculty4@example.com', 3, 'faculty'),
('faculty5', 'Faculty Five', 'hashed_pass8', 'faculty5@example.com', 3, 'faculty'),
('faculty6', 'Faculty Six', 'hashed_pass9', 'faculty6@example.com', 3, 'faculty'),
('faculty7', 'Faculty Seven', 'hashed_pass10', 'faculty7@example.com', 3, 'faculty');

INSERT INTO tbl_item_category (category_name) VALUES 
('Laptops'), 
('Projectors'), 
('Cameras'), 
('Printers'), 
('Monitors'), 
('Speakers'), 
('Microphones'), 
('Tablets'), 
('Accessories'), 
('Others');

INSERT INTO tbl_items (item_code, item_name, item_brand, model, is_bulk, total_quantity, category_id) VALUES
('LAP001', 'Dell Inspiron', 'Dell', 'Inspiron 15', FALSE, 5, 1),
('LAP002', 'MacBook Pro', 'Apple', 'M2 16-inch', FALSE, 3, 1),
('PROJ001', 'Epson Projector', 'Epson', 'XGA 6000', FALSE, 2, 2),
('CAM001', 'Canon DSLR', 'Canon', 'EOS 90D', FALSE, 4, 3),
('PRN001', 'HP LaserJet', 'HP', 'LaserJet 1020', FALSE, 6, 4),
('MON001', 'LG Ultrawide', 'LG', '34WN80C-B', FALSE, 3, 5),
('SPK001', 'JBL Speaker', 'JBL', 'Charge 5', FALSE, 10, 6),
('MIC001', 'Blue Yeti Mic', 'Blue', 'Yeti X', FALSE, 5, 7),
('TAB001', 'iPad Pro', 'Apple', '11-inch 2023', FALSE, 4, 8),
('ACC001', 'Wireless Mouse', 'Logitech', 'MX Master 3', TRUE, 20, 9);

INSERT INTO tbl_borrow_requests (borrower_name, faculty_user_id, faculty_verified, item_borrowed, quantity_borrowed, purpose) VALUES
('John Doe', 4, TRUE, 1, 1, 'Lecture presentation'),
('Jane Smith', 5, FALSE, 2, 1, 'Work demo'),
('Mike Ross', 6, TRUE, 3, 1, 'Lab experiment'),
('Lisa Wong', 7, FALSE, 4, 2, 'Photography project'),
('Tom Hardy', 8, TRUE, 5, 1, 'Printing research papers'),
('Emma Stone', 9, TRUE, 6, 1, 'Graphic designing'),
('Chris Evans', 10, TRUE, 7, 1, 'Event audio setup'),
('Natalie Portman', 4, FALSE, 8, 1, 'Podcast recording'),
('Robert Downey', 5, TRUE, 9, 1, 'Digital illustration'),
('Scarlett Johansson', 6, FALSE, 10, 1, 'Office work');

INSERT INTO tbl_transactions (request_id, transaction_status, admin_verified, admin_user_id) VALUES
(1, 'BORROWED', TRUE, 2),
(2, 'REJECTED_BY_ADMIN', FALSE, 2),
(3, 'BORROWED', TRUE, 3),
(4, 'RETURNED', TRUE, 3),
(5, 'BORROWED', TRUE, 2),
(6, 'PENDING_ADMIN_APPROVAL', FALSE, NULL),
(7, 'BORROWED', TRUE, 2),
(8, 'RETURNED', TRUE, 3),
(9, 'REJECTED_BY_ADMIN', FALSE, 2),
(10, 'PENDING_ADMIN_APPROVAL', FALSE, NULL);

INSERT INTO tbl_activity_logs (user_id, user_name, role_id, action_type, action_description, module) VALUES
(1, 'Super Admin', 1, 'LOGIN', 'Super Admin logged in', 'AUTH_LOGS'),
(2, 'Admin One', 2, 'CREATE', 'Added a new user', 'INVENTORY_LOGS'),
(3, 'Admin Two', 2, 'UPDATE', 'Updated item details', 'ACTIVITY_LOGS'),
(4, 'Faculty One', 3, 'REQUEST', 'Requested an item', 'BORROW_LOGS'),
(5, 'Faculty Two', 3, 'REJECT', 'Rejected a request', 'BORROW_LOGS'),
(6, 'Faculty Three', 3, 'APPROVED', 'Approved a request', 'BORROW_LOGS'),
(7, 'Faculty Four', 3, 'BORROWED', 'Borrowed a laptop', 'TRANSACTION_LOGS'),
(8, 'Faculty Five', 3, 'RETURN', 'Returned an item', 'TRANSACTION_LOGS'),
(9, 'Faculty Six', 3, 'DELETE', 'Deleted a request', 'BORROW_LOGS'),
(10, 'Faculty Seven', 3, 'LOGIN', 'Faculty logged in', 'AUTH_LOGS');
