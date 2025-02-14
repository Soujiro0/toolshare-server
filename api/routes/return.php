<?php
// api/routes/return.php
header("Content-Type: application/json");
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../controllers/ReturnController.php';
require_once __DIR__ . '/../auth/middleware.php';

// Only admin can process returns
requireAuth('admin');

$method = $_SERVER['REQUEST_METHOD'];
$controller = new ReturnController();

switch ($method) {
    case 'PATCH':
        $data = json_decode(file_get_contents("php://input"));
        $controller->processReturn($data);
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
