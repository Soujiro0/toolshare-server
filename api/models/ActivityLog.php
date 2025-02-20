<?php
require_once 'Database.php';
class ActivityLog {
    public function getAll($limit = 10, $offset = 0, $user_type = null, $action_type = null, $start_date = null, $end_date = null, $search_query = null) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM activity_logs WHERE 1=1";
        if ($user_type) {
            $sql .= " AND role = :user_type";
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
            $sql .= " AND action LIKE :search_query";
        }
        $sql .= " ORDER BY action_timestamp DESC LIMIT :limit OFFSET :offset";
        $stmt = $db->prepare($sql);
        if ($user_type) {
            $stmt->bindParam(':user_type', $user_type);
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

    public function getTotalCount($user_type = null, $action_type = null, $start_date = null, $end_date = null, $search_query = null) {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) AS total FROM activity_logs WHERE 1=1";
        if ($user_type) {
            $sql .= " AND role = :user_type";
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
            $sql .= " AND action LIKE :search_query";
        }
        $stmt = $db->prepare($sql);
        if ($user_type) {
            $stmt->bindParam(':user_type', $user_type);
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

    public function getById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM activity_logs WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($user_id, $user_name, $role, $action_type, $action) {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, user_name, role, action_type, action) VALUES (:user_id, :user_name, :role, :action_type, :action)");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":user_name", $user_name);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":action_type", $action_type);
        $stmt->bindParam(":action", $action);
        $stmt->execute();
        return $db->lastInsertId();
    }

    public function update($id, $action) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE activity_logs SET action = :action WHERE id = :id");
        $stmt->bindParam(":action", $action);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM activity_logs WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>