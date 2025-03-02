<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../controllers/BorrowRequestController.php';
require_once __DIR__ . '/../auth/middleware.php';

$method = $_SERVER['REQUEST_METHOD'];
$controller = new BorrowRequestController();

switch ($method) {
    case 'GET':
        // requireAuth(['admin', 'super_admin']);
        if (isset($_GET['request_id'])) {
            $id = intval($_GET['request_id']);
            $controller->getRequestById($id);
        } else {
            $controller->listRequests();
        }
        break;

    case 'POST':
        // requireAuth(['faculty']);
        $data = json_decode(file_get_contents("php://input"));
        $controller->createRequest($data);
        break;

    case 'PATCH':
        // requireAuth(['faculty']);
        if (isset($_GET['request_id'])) {
            $id = intval($_GET['request_id']);
            $data = json_decode(file_get_contents("php://input"));
            $controller->updateRequest($id, $data);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Borrow request ID is required for update"]);
        }
        break;

    case 'DELETE':
        // requireAuth(['admin', 'super_admin']);
        if (isset($_GET['request_id'])) {
            $id = intval($_GET['request_id']);
            $controller->deleteRequest($id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Borrow request ID is required for deletion"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
