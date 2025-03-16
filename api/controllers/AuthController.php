<?php
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../auth/auth.php';

/**
 * AuthController handles user authentication.
 */
class AuthController
{
    private $db;

    /**
     * Constructor initializes the database connection.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Authenticates a user and generates a JWT token.
     *
     * @param string $username The username provided by the user.
     * @param string $password The password provided by the user.
     *
     * @return void Outputs JSON response with token and user details or an error message.
     */
    public function login($username, $password)
    {
        try {
            // Query the users table
            $sql = "
                SELECT user_id, name, password, role_id
                FROM tbl_users
                WHERE username = :username
                LIMIT 1;
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password and return JWT token if valid
            if ($user && password_verify($password, $user['password'])) {
                // Fetch role name
                $roleSql = "SELECT role_name FROM tbl_roles WHERE role_id = :role_id LIMIT 1";
                $roleStmt = $this->db->prepare($roleSql);
                $roleStmt->bindParam(':role_id', $user['role_id']);
                $roleStmt->execute();
                $role = $roleStmt->fetch(PDO::FETCH_ASSOC)['role_name'] ?? 'Unknown';

                // Generate JWT token
                $token = Auth::generateToken($user['user_id'], $user['name'], $role);

                // Update last login timestamp
                // $updateSql = "UPDATE tbl_users SET last_login = NOW() WHERE user_id = :user_id";
                // $updateStmt = $this->db->prepare($updateSql);
                // $updateStmt->bindParam(':user_id', $user['user_id']);
                // $updateStmt->execute();

                echo json_encode([
                    "token" => $token,
                    "user_id" => $user['user_id'],
                    "name" => $user['name'],
                    "role" => $role
                ]);
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Invalid credentials"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Authentication failed",
                "error" => $e->getMessage()
            ]);
        }
    }
}
?>