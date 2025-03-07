<?php
require_once __DIR__ . '/../models/Transaction.php';

class TransactionController
{
    private $model;

    public function __construct()
    {
        $this->model = new Transaction();
    }

    public function listTransactions()
    {
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;
    
        $request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : null;
        $transaction_status = isset($_GET['transaction_status']) ? $_GET['transaction_status'] : null;
        $admin_verified = isset($_GET['admin_verified']) ? filter_var($_GET['admin_verified'], FILTER_VALIDATE_BOOLEAN) : null;
    
        $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'transaction_id';
        $order = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC';
        $search = isset($_GET['search']) ? $_GET['search'] : null;
    
        try {
            $transactions = $this->model->getAllTransactions(
                $limit,
                $offset,
                $request_id,
                $transaction_status,
                $admin_verified,
                $sortBy,
                $order,
                $search
            );
    
            $totalTransactions = $this->model->getTotalTransactionCount(
                $request_id,
                $transaction_status,
                $admin_verified,
                $search
            );
    
            echo json_encode([
                "transactions" => $transactions,
                "totalTransactions" => $totalTransactions
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error fetching transactions",
                "error" => $e->getMessage()
            ]);
        }
    }
    

    public function getTransactionById($id)
    {
        try {
            $transaction = $this->model->getById($id);
            if ($transaction) {
                echo json_encode($transaction);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Transaction not found"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error fetching transaction", "error" => $e->getMessage()]);
        }
    }

    public function createTransaction($data)
    {
        try {
            if (!isset($data->request_id)) {
                http_response_code(400);
                echo json_encode(["message" => "Missing required fields"]);
                return;
            }

            if ($this->model->create($data)) {
                http_response_code(201);
                echo json_encode(["message" => "Transaction created successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to create transaction"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error creating transaction", "error" => $e->getMessage()]);
        }
    }

    public function updateTransaction($id, $data)
    {
        try {
            if (!isset($data->transaction_status) || !in_array($data->transaction_status, ["PENDING_ADMIN_APPROVAL", "REJECTED_BY_ADMIN", "BORROWED", "RETURNED"])) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid status"]);
                return;
            }

            $admin_verified = isset($data->admin_verified) ? (bool) $data->admin_verified : false;

            if ($this->model->updateStatus($id, $data->transaction_status, $admin_verified)) {
                echo json_encode(["message" => "Transaction updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to update transaction"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error updating transaction", "error" => $e->getMessage()]);
        }
    }

    public function deleteTransaction($id)
    {
        try {
            if ($this->model->delete($id)) {
                echo json_encode(["message" => "Transaction deleted successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to delete transaction"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error deleting transaction", "error" => $e->getMessage()]);
        }
    }
}
