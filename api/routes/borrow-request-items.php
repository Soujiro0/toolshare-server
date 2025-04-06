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
$unitId = isset($segments[4]) ? intval($segments[4]) : null;

switch ($method) {
    case 'GET':
        if (isset($_GET['unit_id'])) {
            $unitId = intval($_GET['unit_id']);
            $controller->getAssignedUnitById($unitId);
        } elseif (isset($_GET['request_id'])) {
            $request_id = intval($_GET['request_id']);
            $controller->getAssignedUnitRequestId($request_id);
        } else {
            $controller->getAllAssignedUnits();
        }
        break;


    case 'POST':
        $payload = json_decode(file_get_contents('php://input'));
        if (isset($payload->request_id) && isset($payload->assigned_units)) {
            $controller->assignUnits($payload);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid payload']);
        }
        break;

    case 'PUT':
        // Route for updating assigned unit details by admin
        if (isset($segments[4]) && $segments[4] == 'assigned-unit' && isset($segments[5])) {
            $payload = json_decode(file_get_contents('php://input'));
            // $payload->unit_id = $segments[3];  // Unit ID from the URL
            $controller->updateAssignedUnit($payload);
        } elseif (isset($segments[4]) && $segments[4] === 'return' && isset($segments[5])) {
            $payload = json_decode(file_get_contents('php://input'));
            $controller->returnAllItems($payload);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
        }
        break;

    case 'DELETE':
        // Handle DELETE request to delete assigned item
        if (isset($segments[4]) && isset($segments[5])) {
            $request_id = intval($segments[4]);
            $unit_id = intval($segments[5]);
        
            $payload = new stdClass();
            $payload->request_id = $request_id;
            $payload->unit_id = $unit_id;
        
            // Call the deleteAssignedItem method
            $controller->deleteAssignedItem($payload);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request or parameters']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
