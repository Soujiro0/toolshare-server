<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/api/models/Database.php';

// Get the database connection instance
$db = Database::getInstance();

// Define super admin details
$username = 'admin';
$name = 'Admin 1';
$password = 'admin123'; // Plain text password (change to a secure value)
$email = 'admin@example.com';
$status = 'Active';
$role_id = 2;

// Hash the password using PHP's password_hash function
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepare the INSERT statement
$sql = "INSERT INTO admin_users (username, name, password, email, status, role_id) 
        VALUES (:username, :name, :password, :email, :status, :role_id)";
$stmt = $db->prepare($sql);

// Bind parameters
$stmt->bindParam(':username', $username);
$stmt->bindParam(':name', $name);
$stmt->bindParam(':password', $hashedPassword);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':status', $status);
$stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);

// Execute the query and check for success
if ($stmt->execute()) {
    echo "Admin account created successfully.";
} else {
    echo "Error creating super admin account.";
}
?>