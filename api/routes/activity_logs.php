<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../controllers/ActivityLogController.php';
require_once __DIR__ . '/../auth/middleware.php';

$method = $_SERVER['REQUEST_METHOD'];
$controller = new ActivityLogController();

switch ($method) {
    case 'GET':
        requireAuth(['admin', 'super_admin']);
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $controller->getLog($id);
        } else {
            $controller->listLogs();
        }
        break;
    case 'POST':
        requireAuth(['admin', 'super_admin']);
        $data = json_decode(file_get_contents("php://input"));
        $controller->createLog($data);
        break;
    case 'PUT':
    case 'PATCH':
        requireAuth(['super_admin']);
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $data = json_decode(file_get_contents("php://input"));
            $controller->updateLog($id, $data);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Log id is required for update"]);
        }
        break;
    case 'DELETE':
        requireAuth(['super_admin']);
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $controller->deleteLog($id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Log id is required for delete"]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>
