<?php
require_once __DIR__ . '/../models/ActivityLog.php';
class ActivityLogController {
        /**
     * List all activity logs.
     */
    public function listLogs() {
        // Get pagination params from query string
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;
        $user_type = isset($_GET['user_type']) ? $_GET['user_type'] : null;
        $action_type = isset($_GET['action_type']) ? $_GET['action_type'] : null;
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
        $search_query = isset($_GET['search_query']) ? $_GET['search_query'] : null;

        try {
            $activityLog = new ActivityLog();
            $logs = $activityLog->getAll($limit, $offset, $user_type, $action_type, $start_date, $end_date, $search_query);
            $totalLogs = $activityLog->getTotalCount($user_type, $action_type, $start_date, $end_date, $search_query);

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
     * Create a new activity log entry.
     */
    public function createLog($data) {
        if (!isset($data->user_id, $data->user_name, $data->role, $data->action_type, $data->action)) {
            http_response_code(400);
            echo json_encode(["message" => "user_id, and action are required"]);
            return;
        }
        $activityLog = new ActivityLog();
        try {
            $id = $activityLog->create($data->user_id, $data->user_name, $data->role, $data->action_type, $data->action);
            echo json_encode(["message" => "Activity log created", "id" => $id]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error creating log", "error" => $e->getMessage()]);
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
