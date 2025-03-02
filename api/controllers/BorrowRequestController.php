<?php
require_once __DIR__ . '/../models/BorrowRequest.php';

/**
 * Class BorrowRequestController
 * 
 * Handles API requests for managing borrow requests.
 */
class BorrowRequestController
{
    private $model;

    /**
     * Constructor initializes the BorrowRequest model.
     */
    public function __construct()
    {
        $this->model = new BorrowRequest();
    }

    /**
     * Retrieves a paginated list of borrow requests with optional filters.
     * 
     * Query Parameters:
     * - `limit` (int)             : Number of requests per page (default: 10).
     * - `page` (int)              : Page number for pagination (default: 1).
     * - `borrower_name` (string)  : Filter by borrower name (optional).
     * - `faculty_id` (int)        : Filter by faculty ID (optional).
     * - `faculty_verified` (bool) : Filter by verification status (optional).
     * - `item_borrowed` (string)  : Filter by borrowed item name (optional).
     * - `purpose` (string)        : Filter by purpose (optional).
     * - `request_date` (string)   : Filter by request date (format: YYYY-MM-DD) (optional).
     * - `request_status` (string)         : Filter by request status (optional).
     * - `sort_by` (string)        : Column to sort by (default: 'request_id').
     * - `order` (string)          : Sorting order ('ASC' or 'DESC', default: 'ASC').
     * - `search` (string)         : Search query to filter across multiple fields (optional).
     * 
     * @return void Outputs JSON response:
     * - Success: `{"requests": [...], "totalRequests": number}`
     * - Error: `{"message": "Error fetching requests", "error": string}`
     */
    public function listRequests()
    {
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        $borrower_name = isset($_GET['borrower_name']) ? $_GET['borrower_name'] : null;
        $faculty_id = isset($_GET['faculty_id']) ? intval($_GET['faculty_id']) : null;
        $faculty_verified = isset($_GET['faculty_verified']) ? filter_var($_GET['faculty_verified'], FILTER_VALIDATE_BOOLEAN) : null;
        $item_borrowed = isset($_GET['item_borrowed']) ? $_GET['item_borrowed'] : null;
        $purpose = isset($_GET['purpose']) ? $_GET['purpose'] : null;
        $request_date = isset($_GET['request_date']) ? $_GET['request_date'] : null;
        $request_status = isset($_GET['request_status']) ? $_GET['request_status'] : null;

        $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'request_id';
        $order = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC';
        $search = isset($_GET['search']) ? $_GET['search'] : null;

        try {
            $requests = $this->model->getAllRequests(
                $limit,
                $offset,
                $borrower_name,
                $faculty_id,
                $faculty_verified,
                $item_borrowed,
                $purpose,
                $request_date,
                $request_status,
                $sortBy,
                $order,
                $search
            );

            $totalRequests = $this->model->getTotalRequestCount(
                $borrower_name,
                $faculty_id,
                $faculty_verified,
                $item_borrowed,
                $purpose,
                $request_date,
                $request_status,
                $search
            );

            echo json_encode([
                "requests" => $requests,
                "totalRequests" => $totalRequests
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error fetching requests",
                "error" => $e->getMessage()
            ]);
        }
    }

    /**
     * Retrieves a specific borrow request by ID.
     * 
     * @param int $id The ID of the borrow request.
     * @return void Outputs JSON response:
     * - Success: `{"request": {...}}`
     * - Error: `{"message": "Request not found"}` or `{"message": "Error retrieving request", "error": string}`
     */
    public function getRequestById($id)
    {
        try {
            $request = $this->model->getRequestById($id);
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

    /**
     * Creates a new borrow request.
     * 
     * Expected JSON Payload:
     * - `borrower_name` (string)  : Name of the borrower.
     * - `faculty_id` (int)        : Faculty member ID.
     * - `item_borrowed` (int)     : ID of the borrowed item.
     * - `quantity_borrowed` (int) : Quantity of the item.
     * - `purpose` (string)        : Purpose of borrowing.
     * 
     * @param object $data JSON-decoded object with request details.
     * @return void Outputs JSON response:
     * - Success: `{"message": "Borrow request submitted successfully"}`
     * - Error: `{"message": "Missing required fields"}` or `{"message": "Error submitting request", "error": string}`
     */
    public function createRequest($data)
    {
        try {
            // Validate required fields
            if (!isset($data->borrower_name, $data->faculty_user_id, $data->item_borrowed, $data->quantity_borrowed, $data->purpose)) {
                http_response_code(400);
                echo json_encode(["message" => "Missing required fields"]);
                return;
            }

            // Create request
            if ($this->model->createRequest($data)) {
                http_response_code(201);
                echo json_encode(["message" => "Borrow request submitted successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to submit request"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error submitting request",
                "error" => $e->getMessage()
            ]);
        }
    }

    /**
     * Updates the status of a borrow request.
     * 
     * Expected JSON Payload:
     * - `status` (string) : New status ('PENDING', 'ACCEPTED', 'REJECTED').
     * 
     * @param int $id The ID of the borrow request.
     * @param object $data JSON-decoded object with updated status.
     * @return void Outputs JSON response:
     * - Success: `{"message": "Request updated successfully"}`
     * - Error: `{"message": "Invalid status"}` or `{"message": "Error updating request", "error": string}`
     */
    public function updateRequest($id, $data)
    {
        try {
            // Validate status
            if (!isset($data->request_status) || !in_array($data->request_status, ['PENDING_FACULTY_APPROVAL', 'REJECTED_BY_FACULTY', 'APPROVED', 'CANCELLED'])) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid status"]);
                return;
            }

            // Update request
            if ($this->model->updateRequestStatus($id, $data->request_status)) {
                http_response_code(200);
                echo json_encode(["message" => "Request updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to update request"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error updating request",
                "error" => $e->getMessage()
            ]);
        }
    }

    /**
     * Deletes a borrow request from the database.
     * 
     * @param int $id The ID of the borrow request.
     * @return void Outputs JSON response:
     * - Success: `{"message": "Request deleted successfully"}`
     * - Error: `{"message": "Error deleting request", "error": string}`
     */
    public function deleteRequest($id)
    {
        try {
            if ($this->model->deleteRequest($id)) {
                http_response_code(200);
                echo json_encode(["message" => "Request deleted successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to delete request"]);
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
