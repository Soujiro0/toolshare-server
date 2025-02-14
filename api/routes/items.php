<?php
// api/routes/items.php
header("Content-Type: application/json");
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../controllers/ItemController.php';
require_once __DIR__ . '/../auth/middleware.php';

$method = $_SERVER['REQUEST_METHOD'];
$controller = new ItemController();

switch ($method) {
    case 'GET':
        requireAuth();
        $controller->listItems();
        break;
    case 'POST':
        requireAuth(['admin', 'super_admin']);
        $data = json_decode(file_get_contents("php://input"));
        $controller->createItem($data);
        break;
    case 'PATCH':
        requireAuth(['admin', 'super_admin']);
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $data = json_decode(file_get_contents("php://input"));
            $controller->updateItem($id, $data);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing item id"]);
        }
        break;
    case 'DELETE':
        requireAuth(['admin', 'super_admin']);
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $controller->deleteItem($id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing item id"]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
