<?php
// api/controllers/AuthController.php

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../auth/auth.php';

class AuthController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function login($username, $password)
    {
        // Query from both admins and super_admins
        $sql = "SELECT id, password, 'admin' AS role FROM admins WHERE username = :username
                UNION ALL
                SELECT id, password, 'super_admin' AS role FROM super_admins WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $token = Auth::generateToken($user['id'], $user['role']);
            echo json_encode(["token" => $token]);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Invalid credentials"]);
        }
    }
}
