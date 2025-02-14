<?php
// api/routes/borrow.php
header("Content-Type: application/json");
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../controllers/BorrowController.php';
require_once __DIR__ . '/../auth/middleware.php';

$method = $_SERVER['REQUEST_METHOD'];
$controller = new BorrowController();

switch ($method) {
    case 'GET':
        // List all borrow requests (authentication required)
        requireAuth();
        $controller->listBorrowRequests();
        break;
    case 'POST':
        // Create a new borrow request (accessible to anyone or with minimal auth)
        $data = json_decode(file_get_contents("php://input"));
        $controller->createBorrowRequest($data);
        break;
    case 'PATCH':
        // Update borrow request status (requires auth)
        requireAuth();
        $data = json_decode(file_get_contents("php://input"));
        if (isset($_GET['action']) && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            if ($_GET['action'] == 'signature') {
                $controller->confirmSignature($id);
            } elseif ($_GET['action'] == 'admin_verify') {
                $controller->verifyAdmin($id);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Invalid action"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing action or id"]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
