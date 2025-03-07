<?php
require_once __DIR__ . '/../models/User.php';

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    // Get all users with optional role_id filter
    public function getUsers()
    {
        $role_id = isset($_GET['role_id']) ? intval($_GET['role_id']) : null;
        echo json_encode($this->userModel->getAllUsers($role_id));
    }


    // Get user by ID
    public function getUser($user_id)
    {
        $user = $this->userModel->getUserById($user_id);
        if ($user) {
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "User not found"]);
        }
    }

    // Create a new user
    public function createUser()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['username'], $data['name'], $data['password'], $data['email'], $data['role_id'], $data['user_type'])) {
            http_response_code(400);
            echo json_encode(["message" => "Missing required fields"]);
            return;
        }

        if ($this->userModel->createUser($data['username'], $data['name'], $data['password'], $data['email'], $data['role_id'], $data['user_type'])) {
            echo json_encode(["message" => "User created successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to create user"]);
        }
    }

    // Update an existing user
    public function updateUser($user_id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['username'], $data['name'], $data['email'], $data['role_id'], $data['user_type'])) {
            http_response_code(400);
            echo json_encode(["message" => "Missing required fields"]);
            return;
        }

        if ($this->userModel->updateUser($user_id, $data['username'], $data['name'], $data['email'], $data['role_id'], $data['user_type'])) {
            echo json_encode(["message" => "User updated successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to update user"]);
        }
    }

    // Delete a user
    public function deleteUser($user_id)
    {
        if ($this->userModel->deleteUser($user_id)) {
            echo json_encode(["message" => "User deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to delete user"]);
        }
    }
}
