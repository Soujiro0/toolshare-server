<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../cors.php';
require_once __DIR__ . '/../controllers/TransactionController.php';
require_once __DIR__ . '/../auth/middleware.php';

$method = $_SERVER['REQUEST_METHOD'];
$controller = new TransactionController();

switch ($method) {
    case 'GET':
        // requireAuth(['admin', 'super_admin']);
        if (isset($_GET['transaction_id'])) {
            $id = intval($_GET['transaction_id']);
            $controller->getTransactionById($id);
        } else {
            $controller->listTransactions();
        }
        break;

    case 'POST':
        // requireAuth(['faculty']);
        $data = json_decode(file_get_contents("php://input"));
        $controller->createTransaction($data);
        break;

    case 'PATCH':
        // requireAuth(['admin']);
        if (isset($_GET['transaction_id'])) {
            $id = intval($_GET['transaction_id']);
            $data = json_decode(file_get_contents("php://input"));
            $controller->updateTransaction($id, $data);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Transaction ID is required for update"]);
        }
        break;

    case 'DELETE':
        // requireAuth(['admin', 'super_admin']);
        if (isset($_GET['transaction_id'])) {
            $id = intval($_GET['transaction_id']);
            $controller->deleteTransaction($id);
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Transaction ID is required for deletion"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
