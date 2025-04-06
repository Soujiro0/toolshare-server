<?php
require_once 'Database.php';

class User
{
    public $username;
    public $name;
    public $password;
    public $email;
    public $role_id;

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create()
    {
        try {
            $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);

            $sql = "INSERT INTO tbl_users (username, name, password, email, role_id) 
                    VALUES (:username, :name, :password, :email, :role_id)";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':role_id', $this->role_id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    public function getAll()
    {
        try {
            $sql = "SELECT u.*, r.role_name 
                FROM tbl_users u
                JOIN tbl_roles r ON u.role_id = r.role_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching users: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id)
    {
        try {
            $sql = "SELECT u.*, r.role_name 
                FROM tbl_users u
                JOIN tbl_roles r ON u.role_id = r.role_id
                WHERE u.user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error retrieving user: " . $e->getMessage());
            return null;
        }
    }

    public function update($id, $data)
    {
        $query = "UPDATE tbl_users 
                    SET username = :username, 
                        name = :name, 
                        email = :email, 
                        password = :password,
                        role_id = :role_id, 
                        last_updated = NOW() 
                    WHERE user_id = :user_id";

        $stmt = $this->db->prepare($query);

        // Hash password if it's provided
        $hashedPassword = !empty($data->password) ? password_hash($data->password, PASSWORD_BCRYPT) : null;

        $stmt->bindParam(":username", $data->username);
        $stmt->bindParam(":name", $data->name);
        $stmt->bindParam(":email", $data->email);
        $stmt->bindParam(":role_id", $data->role_id, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $id, PDO::PARAM_INT);

        if ($hashedPassword) {
            $stmt->bindParam(":password", $hashedPassword);
        } else {
            // Keep the existing password
            $query = str_replace("password = :password,", "", $query);
        }

        return $stmt->execute();
    }

    public function delete($id)
    {
        try {
            $sql = "DELETE FROM tbl_users WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }
}
