<?php
// api/controllers/ActivityLogController.php

require_once __DIR__ . '/../models/ActivityLog.php';

class ActivityLogController {
    /**
     * Create a new activity log entry.
     */
    public function createLog($data) {
        if (!isset($data->user_type, $data->user_id, $data->action_type, $data->action)) {
            http_response_code(400);
            echo json_encode(["message" => "user_type, user_id, and action are required"]);
            return;
        }
        $activityLog = new ActivityLog();
        try {
            $id = $activityLog->create($data->user_type, $data->user_id, $data->action_type, $data->action);
            echo json_encode(["message" => "Activity log created", "id" => $id]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error creating log", "error" => $e->getMessage()]);
        }
    }

    /**
     * List all activity logs.
     */
    public function listLogs() {
        // Get pagination params from query string
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        try {
            $activityLog = new ActivityLog();
            $logs = $activityLog->getAll($limit, $offset);
            $totalLogs = $activityLog->getTotalCount();

            // Return paginated logs and total count in JSON
            echo json_encode([
                "logs" => $logs,
                "totalLogs" => $totalLogs,
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error fetching logs",
                "error" => $e->getMessage()
            ]);
        }
    }

    /**
     * Get a single activity log by ID.
     */
    public function getLog($id) {
        $activityLog = new ActivityLog();
        try {
            $log = $activityLog->getById($id);
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
     * Update an activity log's action.
     */
    public function updateLog($id, $data) {
        if (!isset($data->action)) {
            http_response_code(400);
            echo json_encode(["message" => "action is required for update"]);
            return;
        }
        $activityLog = new ActivityLog();
        try {
            $activityLog->update($id, $data->action);
            echo json_encode(["message" => "Activity log updated successfully"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error updating log", "error" => $e->getMessage()]);
        }
    }

    /**
     * Delete an activity log.
     */
    public function deleteLog($id) {
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
?>
