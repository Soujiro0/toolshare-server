<?php
require_once __DIR__ . '/../models/ActivityLog.php';

/**
 * Class ActivityLogController
 * 
 * Handles activity log operations, including listing, retrieving, creating, updating, and deleting logs.
 */
class ActivityLogController
{

    private $model;

    /**
     * Constructor initializes the ActivityLog model.
     */
    public function __construct()
    {
        $this->model = new ActivityLog();
    }

    /**
     * List all activity logs with pagination and filtering options.
     * 
     * Query Parameters:
     * - `limit` (int)        : Number of logs per page (default: 10).
     * - `page` (int)         : Current page number.
     * - `role_id` (string) : Filter by user role (optional).
     * - `action_type` (string): Filter by action type (optional).
     * - `start_date` (string): Filter logs from this date (YYYY-MM-DD, optional).
     * - `end_date` (string)  : Filter logs up to this date (YYYY-MM-DD, optional).
     * - `search_query` (string): Search within action descriptions (optional).
     */
    public function listLogs()
    {
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        $role_id = $_GET['role_id'] ?? null;
        $action_type = $_GET['action_type'] ?? null;

        $start_date = $_GET['start_date'] ?? null;
        $end_date = $_GET['end_date'] ?? null;

        $search_query = $_GET['search_query'] ?? null;

        try {
            $logs = $this->model->getAll($limit, $offset, $role_id, $action_type, $start_date, $end_date, $search_query);
            $totalLogs = $this->model->getTotalCount($role_id, $action_type, $start_date, $end_date, $search_query);

            echo json_encode(["logs" => $logs, "totalLogs" => $totalLogs]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error fetching logs", "error" => $e->getMessage()]);
        }
    }

    /**
     * Retrieve a single activity log by ID.
     * 
     * @param int $id Activity log ID.
     */
    public function getLog($id)
    {
        try {
            $log = $this->model->getLogById($id);
            if ($log) {
                echo json_encode($log);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Log not found"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error fetching log", "error" => $e->getMessage()]);
        }
    }

    /**
     * Create a new activity log entry.
     * 
     * @param object $data JSON object containing user_id, user_name, role_id, action_type, and action_description.
     */
    public function createLog($data)
    {
        if (!isset($data->user_id, $data->user_name, $data->role_id, $data->action_type, $data->action_description, $data->module)) {
            http_response_code(400);
            echo json_encode(["message" => "Missing required fields: user_id, user_name, role_id, action_type, action_description"]);
            return;
        }
        try {
            $id = $this->model->create($data->user_id, $data->user_name, $data->role_id, $data->action_type, $data->action_description, $data->module);
            echo json_encode(["message" => "Activity log created", "id" => $id]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error creating log", "error" => $e->getMessage()]);
        }
    }

    /**
     * Update an existing activity log's action description.
     * 
     * @param int $id Log ID to update.
     * @param object $data JSON object containing updated action_description.
     */
    public function updateLog($id, $data)
    {
        if (!isset($data->action_description)) {
            http_response_code(400);
            echo json_encode(["message" => "action_description is required for update"]);
            return;
        }
        $activityLog = new ActivityLog();
        try {
            $$this->model->update($id, $data->action_description);
            echo json_encode(["message" => "Activity log updated successfully"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error updating log", "error" => $e->getMessage()]);
        }
    }

    /**
     * Delete an activity log by ID.
     * 
     * @param int $id Log ID to delete.
     */
    public function deleteLog($id)
    {
        $activityLog = new ActivityLog();
        try {
            $activityLog->delete($id);
            echo json_encode(["message" => "Activity log deleted successfully"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error deleting log", "error" => $e->getMessage()]);
        }
    }
}
