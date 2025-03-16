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
            $stmt = $this->db->prepare("
                SELECT 
                    br.*,
                    u.name AS borrower_name, 
                    h.name AS handled_by_name
                FROM tbl_borrow_requests br
                JOIN tbl_users u ON u.user_id = br.user_id
                LEFT JOIN tbl_users h ON h.user_id = br.handled_by
            ");
            $stmt->execute();
            $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch items for each request
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
            // Fetch the borrow request with borrower and handler names
            $stmt = $this->db->prepare("
                SELECT 
                    br.*, 
                    u.name AS user_name,  -- Borrower name
                    h.name AS handled_by_name  -- Handler name (can be NULL)
                FROM tbl_borrow_requests br
                JOIN tbl_users u ON u.user_id = br.user_id
                LEFT JOIN tbl_users h ON h.user_id = br.handled_by
                WHERE br.request_id = :request_id
            ");
            $stmt->bindParam(':request_id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $request = $stmt->fetch(PDO::FETCH_ASSOC);

            // If no request found, return an empty response
            if (!$request) {
                return null;
            }

            // Fetch borrowed items for the request
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
                    i.name AS item_name,
                    i.unit
                FROM tbl_borrow_request_items bri
                JOIN tbl_items i ON i.item_id = bri.item_id
                WHERE bri.request_id = :request_id
            ");
            $stmtItems->bindParam(':request_id', $id, PDO::PARAM_INT);
            $stmtItems->execute();
            $request['borrowed_items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            return $request;
        } catch (Exception $e) {
            error_log("Error fetching borrow request: " . $e->getMessage());
            return null;
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
            // Begin transaction
            $this->db->beginTransaction();

            // Fetch existing request status
            $stmtStatus = $this->db->prepare("SELECT status FROM tbl_borrow_requests WHERE request_id = :request_id");
            $stmtStatus->bindParam(':request_id', $id, PDO::PARAM_INT);
            $stmtStatus->execute();
            $existingRequest = $stmtStatus->fetch(PDO::FETCH_ASSOC);

            if (!$existingRequest) {
                throw new Exception("Request not found.");
            }

            $oldStatus = strtoupper($existingRequest['status']);
            $newStatus = strtoupper($data->status);

            // Determine updates based on status change
            $processed_date = null;
            $return_date = null;

            if (in_array($newStatus, ['APPROVED', 'REJECTED', 'BORROWED']) && $oldStatus !== $newStatus) {
                $processed_date = date('Y-m-d H:i:s'); // Set only if changed
            }

            if ($newStatus === 'RETURNED' && $oldStatus !== 'RETURNED') {
                $return_date = date('Y-m-d H:i:s');
            }

            // Update the borrow request record
            $stmt = $this->db->prepare("
            UPDATE tbl_borrow_requests 
            SET 
                user_id = :user_id, 
                status = :status, 
                processed_date = COALESCE(:processed_date, processed_date),
                return_date = COALESCE(:return_date, return_date),
                remarks = :remarks, 
                handled_by = :handled_by
            WHERE request_id = :request_id
        ");

            // Bind parameters
            $stmt->bindValue(':user_id', (int) $data->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':status', $data->status, PDO::PARAM_STR);
            $stmt->bindValue(':processed_date', $processed_date, $processed_date ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':return_date', $return_date, $return_date ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':handled_by', (int) $data->handled_by, PDO::PARAM_INT);
            $stmt->bindValue(':request_id', (int) $id, PDO::PARAM_INT);
            $stmt->bindValue(':remarks', !empty($data->remarks) ? $data->remarks : null, PDO::PARAM_NULL);

            if (!$stmt->execute()) {
                error_log("SQL Error: " . json_encode($stmt->errorInfo()));
                $this->db->rollBack();
                return false;
            }

            // Fetch borrowed items
            $stmtItems = $this->db->prepare("SELECT item_id, quantity FROM tbl_borrow_request_items WHERE request_id = :request_id");
            $stmtItems->bindParam(':request_id', $id, PDO::PARAM_INT);
            $stmtItems->execute();
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            // Adjust inventory only if the status actually changed
            if ($oldStatus !== $newStatus) {
                if ($newStatus === 'BORROWED') {
                    foreach ($items as $item) {
                        $this->subtractInventory($item['item_id'], $item['quantity']);
                    }
                } elseif ($newStatus === 'RETURNED') {
                    foreach ($items as $item) {
                        $this->addInventory($item['item_id'], $item['quantity']);
                    }

                    // Set returned_date for each borrowed item in tbl_borrow_request_items
                    $stmtUpdateItems = $this->db->prepare("
                    UPDATE tbl_borrow_request_items
                    SET returned_date = :returned_date
                    WHERE request_id = :request_id
                ");
                    $stmtUpdateItems->bindValue(':returned_date', $return_date, PDO::PARAM_STR);
                    $stmtUpdateItems->bindValue(':request_id', $id, PDO::PARAM_INT);
                    $stmtUpdateItems->execute();
                }
            }

            // Commit transaction
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Rollback on failure
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
                $newStatus = 'IN_USE';
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
