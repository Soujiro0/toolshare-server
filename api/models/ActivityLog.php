<?php
// api/models/ActivityLog.php

require_once 'Database.php';

class ActivityLog {
    /**
     * Create a new activity log entry.
     */
    public function create($user_type, $user_id, $action_type, $action) {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO activity_logs (user_type, user_id, action_type, action) VALUES (:user_type, :user_id, :action_type, :action)");
        $stmt->bindParam(":user_type", $user_type);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":action_type", $action_type);
        $stmt->bindParam(":action", $action);
        $stmt->execute();
        return $db->lastInsertId();
    }

    /**
     * Retrieve all activity logs (ordered by most recent).
     */
    public function getAll($limit = 10, $offset = 0) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM activity_logs 
                ORDER BY action_timestamp DESC 
                LIMIT :limit OFFSET :offset";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total logs count for pagination
     */
    public function getTotalCount() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT COUNT(*) AS total FROM activity_logs");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Retrieve a single log entry by ID.
     */
    public function getById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM activity_logs WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update an activity log's action.
     * (Note: Typically logs are immutable, but this is provided for full CRUD.)
     */
    public function update($id, $action) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE activity_logs SET action = :action WHERE id = :id");
        $stmt->bindParam(":action", $action);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Delete an activity log entry.
     */
    public function delete($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM activity_logs WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
