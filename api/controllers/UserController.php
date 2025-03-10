<?php
require_once __DIR__ . '/../models/User.php';

class UserController
{
    private $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function getAllUsers()
    {
        try {
            $users = $this->model->getAll();
            $totalUsers = count($users);

            echo json_encode([
                "users" => $users,
                "total_users" => $totalUsers,
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error fetching users",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function getUserById($id)
    {
        try {
            $user = $this->model->getById($id);
            if ($user) {
                echo json_encode(["user" => $user]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "User not found"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error retrieving user",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function createUser($data)
    {
        try {
            $this->model->username = $data->username;
            $this->model->name = $data->name;
            $this->model->password = $data->password;
            $this->model->email = $data->email;
            $this->model->role_id = intval($data->role_id);

            if ($this->model->create()) {
                echo json_encode(["message" => "User created successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error creating user"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error creating user", "error" => $e->getMessage()]);
        }
    }

    public function updateUser($id, $data)
    {
        try {
            if ($this->model->update($id, $data)) {
                echo json_encode(["message" => "User updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error updating user"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error updating user",
                "error" => $e->getMessage()
            ]);
        }
    }
    
    

    public function deleteUser($id)
    {
        if ($this->model->delete($id)) {
            echo json_encode(["message" => "User deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error deleting user"]);
        }
    }
}
?>
