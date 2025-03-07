<?php
// api/routes/users.php
header("Content-Type: application/json");
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../auth/middleware.php';

$method = $_SERVER['REQUEST_METHOD'];
$controller = new UserController();

switch ($method) {
    case 'GET':
        // requireAuth();
        if (isset($_GET['user_id'])) {
            $id = intval($_GET['user_id']);
            $controller->getUser($id);
        } else {
            $controller->getUsers();
        }
        break;
    case 'POST':
        // requireAuth(['admin', 'super_admin']);
        $data = json_decode(file_get_contents("php://input"));
        $controller->createUser($data);
        break;
    case 'PATCH':
        // requireAuth(['admin', 'super_admin']);
        if (isset($_GET['user_id'])) {
            $id = intval($_GET['user_id']);
            $data = json_decode(file_get_contents("php://input"));
            $controller->updateUser($id, $data);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing user id"]);
        }
        break;
    case 'DELETE':
        // requireAuth(['admin', 'super_admin']);
        if (isset($_GET['user_id'])) {
            $id = intval($_GET['user_id']);
            $controller->deleteUser($id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing user id or role"]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}

