INSERT INTO tbl_item_category (category_name) VALUES
('Tools'),
('Equipment'),
('Personal Protective Equipment'),
('Materials');


-- Insert sample users
INSERT INTO tbl_users (username, name, password, email, role_id) VALUES
('superadmin', 'Jerry Castrudes', '$2y$10$5HPgFmOJ8Sa8d4r3/0UI6eSFD/3pbHpDrWw6H/2WnSefq0wNbm9j6', 'superadmin@example.com', 1),
('admin1', 'Alice Smith', '$2y$10$5HPgFmOJ8Sa8d4r3/0UI6eSFD/3pbHpDrWw6H/2WnSefq0wNbm9j6', 'admin@example.com', 2),
('admin2', 'Bob Marley', '$2y$10$5HPgFmOJ8Sa8d4r3/0UI6eSFD/3pbHpDrWw6H/2WnSefq0wNbm9j6', 'admin2@example.com', 2),
('instructor1', 'Charlie Putt', '$2y$10$5HPgFmOJ8Sa8d4r3/0UI6eSFD/3pbHpDrWw6H/2WnSefq0wNbm9j6', 'instructor@example.com', 3),
('instructor2', 'Shet Sharon', '$2y$10$5HPgFmOJ8Sa8d4r3/0UI6eSFD/3pbHpDrWw6H/2WnSefq0wNbm9j6', 'instructor2@example.com', 3);