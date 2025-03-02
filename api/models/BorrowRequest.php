<?php
require_once 'Database.php';

class BorrowRequest
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Gets a filtered list of borrow requests with optional pagination and sorting.
     *
     * @param int $limit Maximum number of records to return
     * @param int $offset Offset for pagination
     * @param string|null $borrower_name Filter by borrower name
     * @param int|null $user_id Filter by user ID
     * @param bool|null $faculty_verified Filter by faculty verification status
     * @param string|null $item_borrowed Filter by item borrowed
     * @param string|null $purpose Filter by purpose
     * @param string|null $request_date Filter by request date (YYYY-MM-DD)
     * @param string|null $status Filter by status
     * @param string $sortBy Column to sort by
     * @param string $order Sort order (ASC or DESC)
     * @param string|null $search General search term
     * @return array List of borrow requests
     */
    public function getAllRequests(
        $limit = 10,
        $offset = 0,
        $borrower_name = null,
        $user_id = null,
        $faculty_verified = null,
        $item_borrowed = null,
        $purpose = null,
        $request_date = null,
        $request_status = null,
        $sortBy = 'request_id',
        $order = 'ASC',
        $search = null
    ) {
        try {
            $sql = "SELECT * FROM tbl_borrow_requests WHERE 1=1";

            if ($borrower_name !== null) {
                $sql .= " AND borrower_name LIKE :borrower_name";
            }
            if ($user_id !== null) {
                $sql .= " AND user_id = :user_id";
            }
            if ($faculty_verified !== null) {
                $sql .= " AND faculty_verified = :faculty_verified";
            }
            if ($item_borrowed !== null) {
                $sql .= " AND item_borrowed LIKE :item_borrowed";
            }
            if ($purpose !== null) {
                $sql .= " AND purpose LIKE :purpose";
            }
            if ($request_date !== null) {
                $sql .= " AND DATE(request_date) = :request_date";
            }
            if ($request_status !== null) {
                $sql .= " AND request_status = :request_status";
            }
            if ($search !== null) {
                $sql .= " AND (borrower_name LIKE :search OR item_borrowed LIKE :search OR purpose LIKE :search)";
            }

            // Validate sort column to prevent SQL injection
            $allowedColumns = [
                'request_id',
                'borrower_name',
                'user_id',
                'faculty_verified',
                'item_borrowed',
                'quantity_borrowed',
                'purpose',
                'request_date',
                'request_status'
            ];
            $sortBy = in_array($sortBy, $allowedColumns) ? $sortBy : 'request_id';
            $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

            $sql .= " ORDER BY $sortBy $order LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);

            if ($borrower_name !== null) {
                $borrowerTerm = "%$borrower_name%";
                $stmt->bindParam(':borrower_name', $borrowerTerm, PDO::PARAM_STR);
            }
            if ($user_id !== null) {
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            }
            if ($faculty_verified !== null) {
                $verifiedValue = $faculty_verified ? 1 : 0;
                $stmt->bindParam(':faculty_verified', $verifiedValue, PDO::PARAM_INT);
            }
            if ($item_borrowed !== null) {
                $itemTerm = "%$item_borrowed%";
                $stmt->bindParam(':item_borrowed', $itemTerm, PDO::PARAM_STR);
            }
            if ($purpose !== null) {
                $purposeTerm = "%$purpose%";
                $stmt->bindParam(':purpose', $purposeTerm, PDO::PARAM_STR);
            }
            if ($request_date !== null) {
                $stmt->bindParam(':request_date', $request_date, PDO::PARAM_STR);
            }
            if ($request_status !== null) {
                $stmt->bindParam(':request_status', $request_status, PDO::PARAM_STR);
            }
            if ($search !== null) {
                $searchTerm = "%$search%";
                $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
            }

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching requests: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Gets the total count of borrow requests matching the given filters.
     *
     * @param string|null $borrower_name Filter by borrower name
     * @param int|null $faculty_user_id Filter by faculty ID
     * @param bool|null $faculty_verified Filter by faculty verification status
     * @param string|null $item_borrowed Filter by item borrowed
     * @param string|null $purpose Filter by purpose
     * @param string|null $request_date Filter by request date (YYYY-MM-DD)
     * @param string|null $status Filter by status
     * @param string|null $search General search term
     * @return int Total number of matching requests
     */
    public function getTotalRequestCount(
        $borrower_name = null,
        $faculty_user_id = null,
        $faculty_verified = null,
        $item_borrowed = null,
        $purpose = null,
        $request_date = null,
        $request_status = null,
        $search = null
    ) {
        try {
            $sql = "SELECT COUNT(*) as total FROM tbl_borrow_requests WHERE 1=1";

            if ($borrower_name !== null) {
                $sql .= " AND borrower_name LIKE :borrower_name";
            }
            if ($faculty_user_id !== null) {
                $sql .= " AND faculty_user_id = :faculty_user_id";
            }
            if ($faculty_verified !== null) {
                $sql .= " AND faculty_verified = :faculty_verified";
            }
            if ($item_borrowed !== null) {
                $sql .= " AND item_borrowed LIKE :item_borrowed";
            }
            if ($purpose !== null) {
                $sql .= " AND purpose LIKE :purpose";
            }
            if ($request_date !== null) {
                $sql .= " AND DATE(request_date) = :request_date";
            }
            if ($request_status !== null) {
                $sql .= " AND status = :status";
            }
            if ($search !== null) {
                $sql .= " AND (borrower_name LIKE :search OR item_borrowed LIKE :search OR purpose LIKE :search)";
            }

            $stmt = $this->db->prepare($sql);

            if ($borrower_name !== null) {
                $borrowerTerm = "%$borrower_name%";
                $stmt->bindParam(':borrower_name', $borrowerTerm, PDO::PARAM_STR);
            }
            if ($faculty_user_id !== null) {
                $stmt->bindParam(':faculty_user_id', $faculty_user_id, PDO::PARAM_INT);
            }
            if ($faculty_verified !== null) {
                $verifiedValue = $faculty_verified ? 1 : 0;
                $stmt->bindParam(':faculty_verified', $verifiedValue, PDO::PARAM_INT);
            }
            if ($item_borrowed !== null) {
                $itemTerm = "%$item_borrowed%";
                $stmt->bindParam(':item_borrowed', $itemTerm, PDO::PARAM_STR);
            }
            if ($purpose !== null) {
                $purposeTerm = "%$purpose%";
                $stmt->bindParam(':purpose', $purposeTerm, PDO::PARAM_STR);
            }
            if ($request_date !== null) {
                $stmt->bindParam(':request_date', $request_date, PDO::PARAM_STR);
            }
            if ($request_status !== null) {
                $stmt->bindParam(':status', $request_status, PDO::PARAM_STR);
            }
            if ($search !== null) {
                $searchTerm = "%$search%";
                $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)$result['total'];
        } catch (Exception $e) {
            error_log("Error getting total request count: " . $e->getMessage());
            return 0;
        }
    }

    public function getRequestById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM tbl_borrow_requests WHERE request_id = :request_id");
        $stmt->execute([':request_id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createRequest($data)
    {
        $sql = "INSERT INTO tbl_borrow_requests (borrower_name, faculty_user_id, item_borrowed, quantity_borrowed, purpose, request_date) 
                VALUES (:borrower_name, :faculty_user_id, :item_borrowed, :quantity_borrowed, :purpose, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':borrower_name' => $data->borrower_name,
            ':faculty_user_id' => $data->faculty_user_id,
            ':item_borrowed' => $data->item_borrowed,
            ':quantity_borrowed' => $data->quantity_borrowed,
            ':purpose' => $data->purpose
        ]);
    }

    public function updateRequestStatus($id, $request_status)
    {
        $sql = "UPDATE tbl_borrow_requests SET request_status = :request_status WHERE request_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':request_status' => $request_status, ':id' => $id]);
    }

    public function deleteRequest($id)
    {
        $stmt = $this->db->prepare("DELETE FROM tbl_borrow_requests WHERE request_id = :request_id");
        return $stmt->execute([':request_id' => $id]);
    }
}
