<?php
// api/routes/borrow_request_items.php
header("Content-Type: application/json");
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../controllers/BorrowRequestItemController.php';
require_once __DIR__ . '/../auth/middleware.php';

$method = $_SERVER['REQUEST_METHOD'];
$controller = new BorrowRequestItemController();

$requestUri = strtok($_SERVER['REQUEST_URI'], '?'); 
$segments = explode('/', trim($requestUri, '/')); 
$itemRequestId = isset($segments[4]) ? intval($segments[4]) : null;

switch ($method) {
    case 'GET':
        if ($itemRequestId) {
            if (isset($_GET['history']) && $_GET['history'] == 'true') {
                $controller->getBorrowHistoryByItemId($itemRequestId);
            } else {
                $controller->getBorrowRequestItemById($itemRequestId);
            }
        } else { 
            $controller->getAllBorrowRequestItems();
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!$data) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON payload"]);
            exit;
        }
        $controller->createBorrowRequestItem($data);
        break;

    case 'PUT':
        if ($itemRequestId) {
            $data = json_decode(file_get_contents("php://input"));
            if (!$data) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid JSON payload"]);
                exit;
            }
            $controller->updateBorrowRequestItem($itemRequestId, $data);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing borrow request item ID"]);
        }
        break;

    case 'DELETE':
        if ($itemRequestId) {
            $controller->deleteBorrowRequestItem($itemRequestId);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing borrow request item ID"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
