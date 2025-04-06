<?php
require_once __DIR__ . '/../models/BorrowRequest.php';

class BorrowRequestController {
    private $model;

    public function __construct() {
        $this->model = new BorrowRequest();
    }

    public function getAllRequest() {
        try {
            $data = $this->model->getAll();
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "Requests fetched successfully",
                "data" => $data
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Failed to fetch requests",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function getRequestById($request_id) {
        try {
            $data = $this->model->getById($request_id);
            if ($data) {
                http_response_code(200);
                echo json_encode([
                    "success" => true,
                    "message" => "Request found",
                    "data" => $data
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    "success" => false,
                    "message" => "Request not found"
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Error retrieving request",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function getRequestByUserId($user_id) {
        try {
            $data = $this->model->getByUserId($user_id);
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "message" => "User requests fetched",
                "data" => $data
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Error fetching user requests",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function createRequest($payload) {
        if (!isset($payload->user_id) || !isset($payload->items)) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Missing user_id or items array"
            ]);
            return;
        }

        try {
            $request_id = $this->model->createRequest(
                $payload->user_id,
                $payload->remarks ?? null,
                $payload->items
            );
            http_response_code(201);
            echo json_encode([
                "success" => true,
                "message" => "Request created successfully",
                "data" => ["request_id" => $request_id]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Error creating request",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function updateRequestByFaculty($request_id, $payload) {
        try {
            $request = $this->model->getByIdRaw($request_id);
            if (!$request) {
                http_response_code(404);
                echo json_encode(["success" => false, "message" => "Request not found"]);
                return;
            }
    
            if ($request['status'] !== 'PENDING') {
                http_response_code(403);
                echo json_encode(["success" => false, "message" => "Only pending requests can be modified"]);
                return;
            }
    
            $remarks = $payload->remarks ?? null;
            $items = $payload->items ?? [];
    
            $this->model->updateRemarks($request_id, $remarks);
    
            if (!empty($items)) {
                $this->model->syncRequestItems($request_id, $items);
            }
    
            echo json_encode(["success" => true, "message" => "Request updated successfully by faculty"]);
    
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Error updating request", "error" => $e->getMessage()]);
        }
    }

    public function updateRequestByAdmin($request_id, $payload) {
        try {
            $request = $this->model->getByIdRaw($request_id);
            if (!$request) {
                http_response_code(404);
                echo json_encode(["success" => false, "message" => "Request not found"]);
                return;
            }
    
            $status = $payload->status ?? null;
            if (!$status) {
                http_response_code(400);
                echo json_encode(["success" => false, "message" => "Missing status"]);
                return;
            }
    
            // $remarks = $payload->remarks ?? null;
            $handled_by = $payload->handled_by ?? null;
            $returned_date = ($status === 'RETURNED') ? date('Y-m-d H:i:s') : null;
    
            $this->model->updateStatusWithReturnDate($request_id, $status, $handled_by, $returned_date);
    
            echo json_encode(["success" => true, "message" => "Request updated successfully by admin"]);
    
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Error updating request", "error" => $e->getMessage()]);
        }
    }    

    public function deleteRequest($request_id) {
        try {
            $success = $this->model->delete($request_id);
            http_response_code($success ? 200 : 404);
            echo json_encode([
                "success" => $success,
                "message" => $success ? "Request deleted successfully" : "Request not found"
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Error deleting request",
                "error" => $e->getMessage()
            ]);
        }
    }
}