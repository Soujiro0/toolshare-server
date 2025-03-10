<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../auth/middleware.php';

$method = $_SERVER['REQUEST_METHOD'];
$controller = new UserController();

$requestUri = strtok($_SERVER['REQUEST_URI'], '?'); 
$segments = explode('/', trim($requestUri, '/')); 
$userId = isset($segments[4]) ? intval($segments[4]) : null;

switch ($method) {
    case 'GET':
        if ($userId) {
            $controller->getUserById($userId);
        } else {
            $controller->getAllUsers();
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!$data) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON payload"]);
            exit;
        }
        $controller->createUser($data);
        break;

    case 'PUT':
        if ($userId) {
            $data = json_decode(file_get_contents("php://input"));
            if (!$data) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid JSON payload"]);
                exit;
            }
            $controller->updateUser($userId, $data);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing user ID"]);
        }
        break;

    case 'DELETE':
        if ($userId) {
            $controller->deleteUser($userId);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing user ID"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>
