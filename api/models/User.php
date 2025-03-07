<?php
require_once __DIR__ . '/Database.php';

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Fetch all users with optional role filter
    public function getAllUsers($role_id = null)
    {
        $sql = "SELECT u.user_id, u.username, u.name, u.email, r.role_name, u.user_type, u.date_created 
            FROM tbl_users u
            INNER JOIN tbl_roles r ON u.role_id = r.role_id";

        if ($role_id !== null) {
            $sql .= " WHERE u.role_id = :role_id";
        }

        $stmt = $this->db->prepare($sql);

        if ($role_id !== null) {
            $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch a single user by ID
    public function getUserById($user_id)
    {
        $sql = "SELECT u.user_id, u.username, u.name, u.email, r.role_name, u.user_type, u.date_created 
                FROM tbl_users u
                INNER JOIN tbl_roles r ON u.role_id = r.role_id
                WHERE u.user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create a new user
    public function createUser($username, $name, $password, $email, $role_id, $user_type)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO tbl_users (username, name, password, email, role_id, user_type) 
                VALUES (:username, :name, :password, :email, :role_id, :user_type)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role_id', $role_id);
        $stmt->bindParam(':user_type', $user_type);
        return $stmt->execute();
    }

    // Update user details
    public function updateUser($user_id, $username, $name, $email, $role_id, $user_type)
    {
        $sql = "UPDATE tbl_users 
                SET username = :username, name = :name, email = :email, role_id = :role_id, user_type = :user_type
                WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':role_id', $role_id);
        $stmt->bindParam(':user_type', $user_type);
        return $stmt->execute();
    }

    // Delete a user
    public function deleteUser($user_id)
    {
        $sql = "DELETE FROM tbl_users WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }
}
