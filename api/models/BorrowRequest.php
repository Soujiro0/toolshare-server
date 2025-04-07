<?php
require_once "Database.php";

class BorrowRequest
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function createRequest($user_id, $remarks, $items = [])
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("INSERT INTO tbl_borrow_requests (user_id, remarks) VALUES (:user_id, :remarks)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':remarks', $remarks);
            $stmt->execute();
            $request_id = $this->db->lastInsertId();

            foreach ($items as $item) {
                $stmtItem = $this->db->prepare("INSERT INTO tbl_borrow_request_summary (request_id, item_id, quantity) VALUES (:request_id, :item_id, :quantity)");
                $stmtItem->bindParam(':request_id', $request_id);
                $stmtItem->bindParam(':item_id', $item->item_id);
                $stmtItem->bindParam(':quantity', $item->quantity);
                $stmtItem->execute();
            }

            $this->db->commit();
            return $request_id;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getAll()
    {
        try {
            $stmt = $this->db->prepare("
            SELECT 
                br.request_id,
                br.user_id,
                u.name AS requested_by,
                br.status,
                br.remarks,
                br.handled_by,
                h.name AS handled_by_name, -- NEW: name of handler
                br.request_date,
                br.return_date,
                br.processed_date,
                br.date_created,
                br.date_updated,
                i.item_id,
                i.name AS item_name,
                brs.quantity
            FROM tbl_borrow_requests br
            JOIN tbl_users u ON br.user_id = u.user_id
            LEFT JOIN tbl_users h ON br.handled_by = h.user_id -- NEW JOIN
            LEFT JOIN tbl_borrow_request_summary brs ON br.request_id = brs.request_id
            LEFT JOIN tbl_items i ON brs.item_id = i.item_id
            ORDER BY br.date_created DESC
        ");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group items per request_id
            $requests = [];
            foreach ($rows as $row) {
                $reqId = $row['request_id'];

                if (!isset($requests[$reqId])) {
                    $requests[$reqId] = [
                        "request_id" => $row['request_id'],
                        "user_id" => $row['user_id'],
                        "requested_by" => $row['requested_by'],
                        "status" => $row['status'],
                        "remarks" => $row['remarks'],
                        "handled_by" => $row['handled_by'], // ✅ Correct handler ID
                        "handled_by_name" => $row['handled_by_name'],
                        "request_date" => $row['request_date'],
                        "return_date" => $row['return_date'],
                        "processed_date" => $row['processed_date'],
                        "date_created" => $row['date_created'],
                        "date_updated" => $row['date_updated'],
                        "requested_items" => []
                    ];
                }

                if ($row['item_id']) {
                    $requests[$reqId]["requested_items"][] = [
                        "item_id" => $row['item_id'],
                        "name" => $row['item_name'],
                        "quantity" => $row['quantity']
                    ];
                }
            }
            return array_values($requests);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getById($request_id)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT 
                br.request_id,
                br.user_id,
                u.name AS requested_by,
                br.status,
                br.remarks,
                br.handled_by,
                h.name AS handled_by_name, -- NEW: name of handler
                br.request_date,
                br.return_date,
                br.processed_date,
                br.date_created,
                br.date_updated,
                i.item_id,
                i.name AS item_name,
                brs.quantity
            FROM tbl_borrow_requests br
            JOIN tbl_users u ON br.user_id = u.user_id
            LEFT JOIN tbl_users h ON br.handled_by = h.user_id -- NEW JOIN
            LEFT JOIN tbl_borrow_request_summary brs ON br.request_id = brs.request_id
            LEFT JOIN tbl_items i ON brs.item_id = i.item_id
            WHERE br.request_id = :request_id
            ORDER BY br.date_created DESC
        ");

            $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$rows) {
                return null;
            }

            // Build request data with grouped items
            $request = [
                "request_id" => $rows[0]['request_id'],
                "user_id" => $rows[0]['user_id'],
                "requested_by" => $rows[0]['requested_by'],
                "status" => $rows[0]['status'],
                "remarks" => $rows[0]['remarks'],
                "handled_by" => $rows[0]['handled_by'], // ✅ Correct handler ID
                "handled_by_name" => $rows[0]['handled_by_name'],
                "request_date" => $rows[0]['request_date'],
                "return_date" => $rows[0]['return_date'],
                "processed_date" => $rows[0]['processed_date'],
                "date_created" => $rows[0]['date_created'],
                "date_updated" => $rows[0]['date_updated'],
                "requested_items" => []
            ];

            foreach ($rows as $row) {
                if ($row['item_id']) {
                    $request["requested_items"][] = [
                        "item_id" => $row['item_id'],
                        "name" => $row['item_name'],
                        "quantity" => $row['quantity']
                    ];
                }
            }

            return $request;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getByUserId($user_id)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT 
                br.request_id,
                br.user_id,
                u.name AS requested_by,
                br.status,
                br.remarks,
                br.handled_by,
                h.name AS handled_by_name, -- NEW: name of handler
                br.request_date,
                br.return_date,
                br.processed_date,
                br.date_created,
                br.date_updated,
                i.item_id,
                i.name AS item_name,
                brs.quantity
            FROM tbl_borrow_requests br
            JOIN tbl_users u ON br.user_id = u.user_id
            LEFT JOIN tbl_users h ON br.handled_by = h.user_id -- NEW JOIN
            LEFT JOIN tbl_borrow_request_summary brs ON br.request_id = brs.request_id
            LEFT JOIN tbl_items i ON brs.item_id = i.item_id
            WHERE br.user_id = :user_id
            ORDER BY br.date_created DESC
            ");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$rows) {
                return [];
            }

            $requests = [];

            foreach ($rows as $row) {
                $reqId = $row['request_id'];

                if (!isset($requests[$reqId])) {
                    $requests[$reqId] = [
                        "request_id" => $row['request_id'],
                        "user_id" => $row['user_id'],
                        "requested_by" => $row['requested_by'],
                        "status" => $row['status'],
                        "remarks" => $row['remarks'],
                        "handled_by" => $row['handled_by'], // ✅ Correct handler ID
                        "handled_by_name" => $row['handled_by_name'],
                        "request_date" => $row['request_date'],
                        "return_date" => $row['return_date'],
                        "processed_date" => $row['processed_date'],
                        "date_created" => $row['date_created'],
                        "date_updated" => $row['date_updated'],
                        "requested_items" => []
                    ];
                }

                if ($row['item_id']) {
                    $requests[$reqId]["requested_items"][] = [
                        "item_id" => $row['item_id'],
                        "name" => $row['item_name'],
                        "quantity" => $row['quantity']
                    ];
                }
            }
            return array_values($requests);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getByIdRaw($request_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tbl_borrow_requests WHERE request_id = :request_id");
            $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getByIdRaw Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateRemarks($request_id, $remarks)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE tbl_borrow_requests 
                SET remarks = :remarks, date_updated = NOW()
                WHERE request_id = :request_id
            ");
            $stmt->bindParam(':remarks', $remarks);
            $stmt->bindParam(':request_id', $request_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("updateRemarks Error: " . $e->getMessage());
            return false;
        }
    }

    public function syncRequestItems($request_id, $items)
    {
        try {
            // Get existing items
            $stmt = $this->db->prepare("SELECT item_id FROM tbl_borrow_request_summary WHERE request_id = :request_id");
            $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
            $stmt->execute();
            $existingItems = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $incomingItemIds = array_column($items, 'item_id');

            // Delete removed items
            foreach ($existingItems as $existingId) {
                if (!in_array($existingId, $incomingItemIds)) {
                    $delStmt = $this->db->prepare("DELETE FROM tbl_borrow_request_summary WHERE request_id = :request_id AND item_id = :item_id");
                    $delStmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
                    $delStmt->bindParam(':item_id', $existingId, PDO::PARAM_INT);
                    $delStmt->execute();
                }
            }

            // Insert or update items
            foreach ($items as $item) {
                $item_id = $item->item_id;
                $quantity = $item->quantity;

                $checkStmt = $this->db->prepare("SELECT COUNT(*) FROM tbl_borrow_request_summary WHERE request_id = :request_id AND item_id = :item_id");
                $checkStmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
                $checkStmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $checkStmt->execute();
                $exists = $checkStmt->fetchColumn() > 0;

                if ($exists) {
                    $updateStmt = $this->db->prepare("UPDATE tbl_borrow_request_summary SET quantity = ? WHERE request_id = ? AND item_id = ?");
                    $updateStmt->execute([$quantity, $request_id, $item_id]);
                } else {
                    $insertStmt = $this->db->prepare("INSERT INTO tbl_borrow_request_summary (request_id, item_id, quantity) VALUES (?, ?, ?)");
                    $insertStmt->execute([$request_id, $item_id, $quantity]);
                }
            }
            return true;
        } catch (PDOException $e) {
            error_log("syncRequestItems Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateStatusWithReturnDate($request_id, $status, $handled_by, $returned_date)
    {
        try {
            $sql = "
                UPDATE tbl_borrow_requests 
                SET status = :status,
                    handled_by = :handled_by,
                    processed_date = NOW(),
                    date_updated = NOW()
            ";

            if ($returned_date !== null) {
                $sql .= ", return_date = :return_date";
            }

            $sql .= " WHERE request_id = :request_id";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':handled_by', $handled_by);
            $stmt->bindParam(':request_id', $request_id);

            if ($returned_date !== null) {
                $stmt->bindParam(':return_date', $returned_date);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("updateStatusWithReturnDate Error: " . $e->getMessage());
            return false;
        }
    }

    public function delete($request_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM tbl_borrow_requests WHERE request_id = ?");
            return $stmt->execute([$request_id]);
        } catch (PDOException $e) {
            error_log("deleteRequest Error: " . $e->getMessage());
            return false;
        }
    }
}
