<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../controllers/ItemController.php';

$method = $_SERVER['REQUEST_METHOD'];
$controller = new ItemController();

$requestUri = strtok($_SERVER['REQUEST_URI'], '?');
$segments = explode('/', trim($requestUri, '/'));
$itemId = isset($segments[4]) ? intval($segments[4]) : null;
$unitId = isset($segments[4]) && $segments[4] === 'units' ? intval($segments[5]) : null;

switch ($method) {
    case 'GET':
        if (isset($segments[4]) && is_numeric($segments[4])) {
            $controller->getItemById($itemId);
        } else {
            $controller->getAllItems();
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!$data) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON payload"]);
            exit;
        }
        $controller->createItem($data);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!$data) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON payload"]);
            exit;
        }

        // Example: /api/routes/items.php/units/3
        if (isset($segments[4]) && $segments[4] === 'units') {
            $controller->updateUnit($unitId, $data);
        }
        // Example: /api/routes/items.php/9
        elseif (isset($segments[4]) && is_numeric($segments[4])) {
            $controller->updateItem($itemId, $data);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Invalid or missing ID"]);
        }
        break;

    case 'DELETE':
        // Delete item
        if (isset($segments[4]) && is_numeric($segments[4])) {
            $controller->deleteItem($itemId);
        }
        // Delete unit under an item
        elseif (isset($segments[4]) && $segments[4] === 'units' && isset($segments[4]) && is_numeric($segments[5])) {
            $controller->deleteUnit($unitId);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Invalid request for deletion"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
