<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../controllers/BorrowRequestController.php';
require_once __DIR__ . '/../auth/middleware.php';

$method = $_SERVER['REQUEST_METHOD'];
$controller = new BorrowRequestController();

$requestUri = strtok($_SERVER['REQUEST_URI'], '?');
$segments = explode('/', trim($requestUri, '/'));
$requestId = isset($segments[count($segments) - 1]) && is_numeric($segments[count($segments) - 1])
    ? intval($segments[count($segments) - 1])
    : null;

switch ($method) {
    case 'GET':
        if ($requestId) {
            $controller->getRequestById($requestId);
        } elseif (isset($_GET['user_id'])) {
            $controller->getRequestByUserId(intval($_GET['user_id']));
        } else {
            $controller->getAllRequest();
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!$data) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid JSON payload"]);
            exit;
        }
        $controller->createRequest($data);
        break;

    case 'PUT':
        if ($requestId) {
            $data = json_decode(file_get_contents("php://input"));
            if (!$data) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid JSON payload"]);
                exit;
            }

            if (isset($segments[4]) && $segments[4] === 'faculty') {
                $controller->updateRequestByFaculty(intval($segments[5]), $data);
            } elseif (isset($segments[4]) && $segments[4] === 'admin') {
                $controller->updateRequestByAdmin(intval($segments[5]), $data);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "Invalid request type"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing request ID"]);
        }
        break;

    case 'DELETE':
        if ($requestId) {
            $controller->deleteRequest($requestId);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Missing request ID"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}