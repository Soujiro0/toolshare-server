<?php
// api/routes/items.php
header("Content-Type: application/json");
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../controllers/CategoryController.php';
require_once __DIR__ . '/../auth/middleware.php';

$method = $_SERVER['REQUEST_METHOD'];
$controller = new CategoryController();

$requestUri = strtok($_SERVER['REQUEST_URI'], '?'); 
$segments = explode('/', trim($requestUri, '/')); 
$itemId = isset($segments[4]) ? intval($segments[4]) : null;

switch ($method) {
    case 'GET':
        if ($itemId) { 
            $item = $controller->getCategoryById($itemId);
        } else { 
            $items = $controller->getAllCategory();
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!$data) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON payload"]);
            exit;
        }
        $controller->createCategory($data);
        break;

    case 'PUT':
        if ($itemId) {
            $data = json_decode(file_get_contents("php://input"));
            if (!$data) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid JSON payload"]);
                exit;
            }
            $controller->updateCategory($itemId, $data);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing item ID"]);
        }
        break;

    case 'DELETE':
        if ($itemId) {
            $controller->deleteCategory($itemId);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing item ID"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
