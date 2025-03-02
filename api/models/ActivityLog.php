<?php
require_once 'Database.php';

/**
 * Class ActivityLog
 * 
 * Manages activity logs for tracking user actions within the system.
 */
class ActivityLog 
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Retrieves a paginated list of activity logs with optional filters.
     * 
     * Query Parameters:
     * - `limit` (int)        : Number of logs per page (default: 10).
     * - `offset` (int)       : Starting point for pagination.
     * - `user_type` (string) : Filter by user role (optional).
     * - `action_type` (string): Filter by action type (optional).
     * - `start_date` (string): Filter logs from this date (YYYY-MM-DD, optional).
     * - `end_date` (string)  : Filter logs up to this date (YYYY-MM-DD, optional).
     * - `search_query` (string): Search within action descriptions (optional).
     * 
     * @param int $limit Number of records to retrieve.
     * @param int $offset Starting point for pagination.
     * @param string|null $role_id Role filter.
     * @param string|null $action_type Action type filter.
     * @param string|null $start_date Start date filter.
     * @param string|null $end_date End date filter.
     * @param string|null $search_query Search text within action descriptions.
     * @return array Array of activity logs.
     */
    public function getAll($limit = 10, $offset = 0, $role_id = null, $action_type = null, $start_date = null, $end_date = null, $search_query = null) 
    {
        $sql = "SELECT * FROM tbl_activity_logs WHERE 1=1";
        
        if ($role_id) {
            $sql .= " AND role_id = :role_id";
        }
        if ($action_type) {
            $sql .= " AND action_type = :action_type";
        }
        if ($start_date) {
            $sql .= " AND action_timestamp >= :start_date";
        }
        if ($end_date) {
            $sql .= " AND action_timestamp <= :end_date";
        }
        if ($search_query) {
            $sql .= " AND action_description LIKE :search_query";
        }

        $sql .= " ORDER BY action_timestamp DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        if ($role_id) {
            $stmt->bindParam(':role_id', $role_id);
        }
        if ($action_type) {
            $stmt->bindParam(':action_type', $action_type);
        }
        if ($start_date) {
            $stmt->bindParam(':start_date', $start_date);
        }
        if ($end_date) {
            $stmt->bindParam(':end_date', $end_date);
        }
        if ($search_query) {
            $search_query = "%$search_query%";
            $stmt->bindParam(':search_query', $search_query);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Gets the total count of activity logs with optional filters.
     * 
     * @param string|null $user_type Role filter.
     * @param string|null $action_type Action type filter.
     * @param string|null $start_date Start date filter.
     * @param string|null $end_date End date filter.
     * @param string|null $search_query Search text within action descriptions.
     * @return int Total count of matching logs.
     */
    public function getTotalCount($role_id = null, $action_type = null, $start_date = null, $end_date = null, $search_query = null) 
    {
        $sql = "SELECT COUNT(*) AS total FROM tbl_activity_logs WHERE 1=1";
        
        if ($role_id) {
            $sql .= " AND role_id = :role_id";
        }
        if ($action_type) {
            $sql .= " AND action_type = :action_type";
        }
        if ($start_date) {
            $sql .= " AND action_timestamp >= :start_date";
        }
        if ($end_date) {
            $sql .= " AND action_timestamp <= :end_date";
        }
        if ($search_query) {
            $sql .= " AND action_description LIKE :search_query";
        }

        $stmt = $this->db->prepare($sql);
        
        if ($role_id) {
            $stmt->bindParam(':role_id', $role_id);
        }
        if ($action_type) {
            $stmt->bindParam(':action_type', $action_type);
        }
        if ($start_date) {
            $stmt->bindParam(':start_date', $start_date);
        }
        if ($end_date) {
            $stmt->bindParam(':end_date', $end_date);
        }
        if ($search_query) {
            $search_query = "%$search_query%";
            $stmt->bindParam(':search_query', $search_query);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Retrieves a specific activity log by ID.
     * 
     * @param int $id Activity log ID.
     * @return array|null Log entry if found, null otherwise.
     */
    public function getLogById($id) 
    {
        $stmt = $this->db->prepare("SELECT * FROM tbl_activity_logs WHERE log_id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Creates a new activity log entry.
     * 
     * @param int $user_id ID of the user who performed the action.
     * @param string $user_name Name of the user.
     * @param string $role_id Role of the user.
     * @param string $action_type Type of action performed.
     * @param string $action_description Description of the action.
     * @return int ID of the newly created log entry.
     */
    public function create($user_id, $user_name, $role_id, $action_type, $action_description, $module) 
    {
        $stmt = $this->db->prepare("INSERT INTO tbl_activity_logs (user_id, user_name, role_id, action_type, action_description, module) VALUES (:user_id, :user_name, :role_id, :action_type, :action_description, :module)");
        
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":user_name", $user_name);
        $stmt->bindParam(":role_id", $role_id);
        $stmt->bindParam(":action_type", $action_type);
        $stmt->bindParam(":action_description", $action_description);
        $stmt->bindParam(":module", $module);
        
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    /**
     * Updates an existing activity log entry.
     * 
     * @param int $id Log ID to update.
     * @param string $action_description Updated action description.
     * @return bool True if successful, false otherwise.
     */
    public function update($id, $action_description) 
    {
        $stmt = $this->db->prepare("UPDATE tbl_activity_logs SET action_description = :action_description WHERE log_id = :id");
        $stmt->bindParam(":action_description", $action_description);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Deletes an activity log entry.
     * 
     * @param int $id Log ID to delete.
     * @return bool True if deletion was successful, false otherwise.
     */
    public function delete($id) 
    {
        $stmt = $this->db->prepare("DELETE FROM tbl_activity_logs WHERE log_id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>