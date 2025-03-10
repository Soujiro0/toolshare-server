<?php
require_once "Database.php";

class BorrowRequest
{

    public $user_id;
    public $status;
    public $remarks;

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tbl_borrow_requests WHERE 1=1");
            $stmt->execute();
            $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 2. For each request, fetch its associated items
            foreach ($requests as &$request) {
                $stmtItems = $this->db->prepare("
                SELECT 
                    bri.request_item_id,
                    bri.request_id,
                    bri.item_id,
                    bri.quantity,
                    bri.item_condition_out,
                    bri.item_condition_in,
                    bri.damage_notes,
                    bri.returned_date,
                    bri.date_created,
                    bri.date_updated,
                    i.name AS name,
                    i.unit
                FROM tbl_borrow_request_items bri
                JOIN tbl_items i ON i.item_id = bri.item_id
                WHERE bri.request_id = :request_id
            ");
                $stmtItems->bindParam(':request_id', $request['request_id']);
                $stmtItems->execute();
                $request['borrowed_items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
            }

            return $requests;
        } catch (Exception $e) {
            error_log("Error fetching borrow requests: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tbl_borrow_requests WHERE request_id = :request_id");
            $stmt->bindParam(':request_id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching borrow request: " . $e->getMessage());
            return [];
        }
    }

    public function getByUserId($user_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tbl_borrow_requests WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Fetch associated items for each borrow request
            foreach ($requests as &$request) {
                $stmtItems = $this->db->prepare("
                    SELECT 
                        bri.request_item_id,
                        bri.request_id,
                        bri.item_id,
                        bri.quantity,
                        bri.item_condition_out,
                        bri.item_condition_in,
                        bri.damage_notes,
                        bri.returned_date,
                        bri.date_created,
                        bri.date_updated,
                        i.name AS name,
                        i.unit
                    FROM tbl_borrow_request_items bri
                    JOIN tbl_items i ON i.item_id = bri.item_id
                    WHERE bri.request_id = :request_id
                ");
                $stmtItems->bindParam(':request_id', $request['request_id'], PDO::PARAM_INT);
                $stmtItems->execute();
                $request['borrowed_items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
            }
    
            return $requests;
        } catch (Exception $e) {
            error_log("Error fetching borrow requests for user $user_id: " . $e->getMessage());
            return [];
        }
    }
    

    public function create()
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO tbl_borrow_requests (user_id, remarks) VALUES (:user_id, :remarks)");
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':remarks', $this->remarks);

            if ($stmt->execute()) {
                return $this->db->lastInsertId(); // ✅ Return last inserted request_id
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log("Error saving borrow request: " . $e->getMessage());
            return false;
        }
    }


    public function update($id, $data)
    {
        try {
            $stmt = $this->db->prepare("UPDATE tbl_borrow_requests SET user_id = :user_id, status = :status, remarks = :remarks WHERE request_id = :request_id");
            $stmt->bindParam(':user_id', $data->user_id);
            $stmt->bindParam(':status', $data->status);
            $stmt->bindParam(':remarks', $data->remarks);
            $stmt->bindParam(':request_id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error updating borrow request: " . $e->getMessage());
            return false;
        }
    }

    public function updateRequestAndApprove($id, $data)
    {
        try {
            // Begin a transaction to ensure atomicity
            $this->db->beginTransaction();

            // 1. Update the borrow request record
            $stmt = $this->db->prepare("
            UPDATE tbl_borrow_requests 
            SET user_id = :user_id, status = :status, remarks = :remarks 
            WHERE request_id = :request_id
        ");
            $stmt->bindParam(':user_id', $data->user_id);
            $stmt->bindParam(':status', $data->status);
            $stmt->bindParam(':remarks', $data->remarks);
            $stmt->bindParam(':request_id', $id);
            $stmt->execute();


            // 2. Fetch the items in this borrow request
            $stmtItems = $this->db->prepare("SELECT * FROM tbl_borrow_request_items WHERE request_id = :request_id");
            $stmtItems->bindParam(':request_id', $id);
            $stmtItems->execute();
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
            // 3. Adjust inventory based on the new status
            if (strtoupper($data->status) === 'APPROVED') {
                // Subtract from inventory
                foreach ($items as $item) {

                    $this->subtractInventory($item['item_id'], $item['quantity']);
                }
            } elseif (strtoupper($data->status) === 'RETURNED') {
                // Add back to inventory
                foreach ($items as $item) {
                    $this->addInventory($item['item_id'], $item['quantity']);
                }
            }

            // Commit the transaction if all queries succeed
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Roll back all changes if any error occurs
            $this->db->rollBack();
            error_log("Error in updateRequestAndApprove: " . $e->getMessage());
            return false;
        }
    }


    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM tbl_borrow_requests WHERE request_id = :request_id");
            $stmt->bindParam(':request_id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting borrow request: " . $e->getMessage());
            return false;
        }
    }

    private function subtractInventory($itemId, $requestedQuantity)
    {
        // Lock row
        $stmtInventory = $this->db->prepare("SELECT quantity FROM tbl_items WHERE item_id = :item_id FOR UPDATE");
        $stmtInventory->bindParam(':item_id', $itemId);
        $stmtInventory->execute();
        $inventory = $stmtInventory->fetch(PDO::FETCH_ASSOC);

        if (!$inventory) {
            throw new Exception("Item not found in inventory for item_id: " . $itemId);
        }

        // Check if sufficient quantity is available
        if ($inventory['quantity'] < $requestedQuantity) {
            throw new Exception("Insufficient quantity for item_id: " . $itemId);
        }

        // Subtract the requested quantity
        $stmtUpdate = $this->db->prepare("UPDATE tbl_items SET quantity = quantity - :reqQty WHERE item_id = :item_id");
        $stmtUpdate->bindParam(':reqQty', $requestedQuantity);
        $stmtUpdate->bindParam(':item_id', $itemId);
        $stmtUpdate->execute();

        // Update status based on the new quantity
        $this->setItemStatus($itemId);
    }

    private function addInventory($itemId, $returnedQuantity)
    {
        // Lock row
        $stmtInventory = $this->db->prepare("SELECT quantity FROM tbl_items WHERE item_id = :item_id FOR UPDATE");
        $stmtInventory->bindParam(':item_id', $itemId);
        $stmtInventory->execute();
        $inventory = $stmtInventory->fetch(PDO::FETCH_ASSOC);

        if (!$inventory) {
            throw new Exception("Item not found in inventory for item_id: " . $itemId);
        }

        // Add the returned quantity back
        $stmtUpdate = $this->db->prepare("UPDATE tbl_items SET quantity = quantity + :retQty WHERE item_id = :item_id");
        $stmtUpdate->bindParam(':retQty', $returnedQuantity);
        $stmtUpdate->bindParam(':item_id', $itemId);
        $stmtUpdate->execute();

        // Update status based on the new quantity
        $this->setItemStatus($itemId);
    }

    private function setItemStatus($itemId)
    {
        // Retrieve the new quantity
        $stmtCheck = $this->db->prepare("SELECT quantity FROM tbl_items WHERE item_id = :item_id");
        $stmtCheck->bindParam(':item_id', $itemId);
        $stmtCheck->execute();
        $newInventory = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($newInventory) {
            // Determine new status based on quantity value
            if ($newInventory['quantity'] == 0) {
                $newStatus = 'NO STOCK';
            } else {
                $newStatus = 'AVAILABLE';
            }

            // Update the status column accordingly
            $stmtStatus = $this->db->prepare("UPDATE tbl_items SET status = :newStatus WHERE item_id = :item_id");
            $stmtStatus->bindParam(':newStatus', $newStatus);
            $stmtStatus->bindParam(':item_id', $itemId);
            $stmtStatus->execute();
        }
    }
}
