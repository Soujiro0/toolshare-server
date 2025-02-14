<?php
// api/routes/categories.php
header("Content-Type: application/json");
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../controllers/CategoryController.php';
require_once __DIR__ . '/../auth/middleware.php';

$method = $_SERVER['REQUEST_METHOD'];
$controller = new CategoryController();

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $controller->getCategory($id);
        } else {
            $controller->listCategories();
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $controller->createCategory($data);
        break;

    case 'PUT':
    case 'PATCH':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $data = json_decode(file_get_contents("php://input"));
            $controller->updateCategory($id, $data);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Category id is required for update"]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $controller->deleteCategory($id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Category id is required for delete"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>
