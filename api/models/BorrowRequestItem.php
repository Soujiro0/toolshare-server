<?php
// app/models/BorrowRequestItem.php
require_once "Database.php";

class BorrowRequestItem {
    public $request_id;
    public $item_id;
    public $quantity;
    public $item_condition_out;
    // Add other properties as needed

    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tbl_borrow_request_items");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching borrow request items: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tbl_borrow_request_items WHERE request_item_id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching borrow request item: " . $e->getMessage());
            return null;
        }
    }

    public function create() {
        try {
            $stmt = $this->db->prepare("INSERT INTO tbl_borrow_request_items (request_id, item_id, quantity, item_condition_out) VALUES (:request_id, :item_id, :quantity, :item_condition_out)");
            $stmt->bindParam(':request_id', $this->request_id);
            $stmt->bindParam(':item_id', $this->item_id);
            $stmt->bindParam(':quantity', $this->quantity);
            $stmt->bindParam(':item_condition_out', $this->item_condition_out);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error creating borrow request item: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $stmt = $this->db->prepare("UPDATE tbl_borrow_request_items SET request_id = :request_id, item_id = :item_id, quantity = :quantity, item_condition_out = :item_condition_out WHERE request_item_id = :id");
            $stmt->bindParam(':request_id', $data->request_id);
            $stmt->bindParam(':item_id', $data->item_id);
            $stmt->bindParam(':quantity', $data->quantity);
            $stmt->bindParam(':item_condition_out', $data->item_condition_out);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error updating borrow request item: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM tbl_borrow_request_items WHERE request_item_id = :id");
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting borrow request item: " . $e->getMessage());
            return false;
        }
    }
}
