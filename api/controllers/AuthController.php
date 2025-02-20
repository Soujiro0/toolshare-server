<?php
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
        /**
         *  Query from the unified admin_users table joined with roles to get the role name,
         *  and include the name field from admin_users.
         */
        $sql = "SELECT au.id, au.name, au.password, r.name AS role 
                FROM admin_users au
                JOIN roles r ON au.role_id = r.id
                WHERE au.username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Optionally, you can include the user's name in the payload.
            $token = Auth::generateToken($user['id'], $user['name'], $user['role']);
            echo json_encode(["token" => $token, "name" => $user['name']]);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Invalid credentials"]);
        }
    }
}
