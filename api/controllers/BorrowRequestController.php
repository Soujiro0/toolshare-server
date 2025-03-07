<?php
require_once __DIR__ . '/../models/BorrowRequest.php';

class BorrowRequestController
{

    private $model;

    public function __construct()
    {
        $this->model = new BorrowRequest();
    }

    public function getAllRequest()
    {
        $request = new BorrowRequest();
        try {
            $requests = $request->getAll();
            $totalRequests = count($requests);

            echo json_encode([
                "request" => $requests,
                "total_requests" => $totalRequests
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error fetching borrow requests",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function getRequestById($id)
    {
        try {
            $request = $this->model->getById($id);
            if ($request) {
                echo json_encode(["request" => $request]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Request not found"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error retrieving request",
                "error" => $e->getMessage()
            ]);
        }
    }

    // POST /api/borrow-requests
    public function createRequest($data)
    {
        try {
            $this->model->user_id = $data->user_id;
            $this->model->remarks = $data->remarks;

            if ($this->model->create()) {
                echo json_encode(["message" => "Request created successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error creating request"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error creating request",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function updateRequest($id, $data)
    {
        try {

            // Check if we're approving the request
            if (isset($data->status) && $data->status === 'APPROVED' || $data->status === 'RETURNED') {
                $result = $this->model->updateRequestAndApprove($id, $data);
            } else {
                $result = $this->model->update($id, $data);
            }

            if ($result) {
                echo json_encode(["message" => "Request updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error updating request"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error updating request",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function deleteRequest($id)
    {
        try {
            if ($this->model->delete($id)) {
                echo json_encode(["message" => "Request deleted successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error deleting request"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error deleting request",
                "error" => $e->getMessage()
            ]);
        }
    }
}
