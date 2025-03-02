<?php
require_once __DIR__ . '/../models/Database.php';

class Transaction
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Gets a filtered list of transactions with optional pagination and sorting,
     * including borrow request details.
     *
     * @param int $limit Maximum number of records to return
     * @param int $offset Offset for pagination
     * @param int|null $request_id Filter by request ID
     * @param string|null $status Filter by status
     * @param bool|null $admin_verified Filter by admin verification status
     * @param int|null $admin_user_id Filter by user ID
     * @param string $sortBy Column to sort by
     * @param string $order Sort order (ASC or DESC)
     * @param string|null $search General search term
     * @return array List of transactions with borrow request details
     */
    public function getAllTransactions(
        $limit = 10,
        $offset = 0,
        $request_id = null,
        $transaction_status = null,
        $admin_verified = null,
        $admin_user_id = null,
        $sortBy = 'transaction_id',
        $order = 'ASC',
        $search = null
    ) {
        try {
            $sql = "SELECT t.*, br.borrower_name, br.faculty_user_id, br.faculty_verified, 
                        br.item_borrowed, br.quantity_borrowed, br.purpose, br.request_date, br.request_status
                FROM tbl_transactions t
                JOIN tbl_borrow_requests br ON t.request_id = br.request_id
                WHERE 1=1";

            if ($request_id !== null) {
                $sql .= " AND t.request_id = :request_id";
            }
            if ($transaction_status !== null) {
                $sql .= " AND t.transaction_status = :transaction_status";
            }
            if ($admin_verified !== null) {
                $sql .= " AND t.admin_verified = :admin_verified";
            }
            if ($admin_user_id !== null) {
                $sql .= " AND t.admin_user_id = :admin_user_id";
            }
            if ($search !== null) {
                $sql .= " AND (br.borrower_name LIKE :search OR br.item_borrowed LIKE :search OR br.purpose LIKE :search)";
            }

            // Validate sort column to prevent SQL injection
            $allowedColumns = [
                'transaction_id',
                'request_id',
                'transaction_status',
                'admin_verified',
                'admin_user_id',
                'transaction_date'
            ];
            $sortBy = in_array($sortBy, $allowedColumns) ? $sortBy : 'transaction_id';
            $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

            $sql .= " ORDER BY $sortBy $order LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);

            if ($request_id !== null) {
                $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
            }
            if ($transaction_status !== null) {
                $stmt->bindParam(':transaction_status', $transaction_status, PDO::PARAM_STR);
            }
            if ($admin_verified !== null) {
                $adminVerifiedValue = $admin_verified ? 1 : 0;
                $stmt->bindParam(':admin_verified', $adminVerifiedValue, PDO::PARAM_INT);
            }
            if ($admin_user_id !== null) {
                $stmt->bindParam(':admin_user_id', $admin_user_id, PDO::PARAM_INT);
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
            error_log("Error fetching transactions: " . $e->getMessage());
            return [];
        }
    }


    /**
     * Gets the total count of transactions matching the given filters.
     *
     * @param int|null $transaction_id Filter by transaction ID
     * @param int|null $request_id Filter by request ID
     * @param string|null $status Filter by status
     * @param bool|null $admin_verified Filter by admin verification status
     * @param int|null $admin_user_id Filter by user ID
     * @param string|null $search General search term
     * @return int Total number of matching transactions
     */
    public function getTotalTransactionCount(
        $request_id = null,
        $status = null,
        $admin_verified = null,
        $admin_user_id = null,
        $search = null
    ) {
        try {
            $sql = "SELECT COUNT(*) as total FROM tbl_transactions WHERE 1=1";

            if ($request_id !== null) {
                $sql .= " AND request_id = :request_id";
            }
            if ($status !== null) {
                $sql .= " AND status = :status";
            }
            if ($admin_verified !== null) {
                $sql .= " AND admin_verified = :admin_verified";
            }
            if ($admin_user_id !== null) {
                $sql .= " AND admin_user_id = :admin_user_id";
            }
            if ($search !== null) {
                $sql .= " AND (borrower_name LIKE :search OR item_borrowed LIKE :search OR purpose LIKE :search)";
            }

            $stmt = $this->db->prepare($sql);

            if ($request_id !== null) {
                $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
            }
            if ($status !== null) {
                $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            }
            if ($admin_verified !== null) {
                $adminVerifiedValue = $admin_verified ? 1 : 0;
                $stmt->bindParam(':admin_verified', $adminVerifiedValue, PDO::PARAM_INT);
            }
            if ($admin_user_id !== null) {
                $stmt->bindParam(':admin_user_id', $admin_user_id, PDO::PARAM_INT);
            }
            if ($search !== null) {
                $searchTerm = "%$search%";
                $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)$result['total'];
        } catch (Exception $e) {
            error_log("Error getting total transaction count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get a transaction by ID
     * 
     * @param int $id The transaction ID
     * @return array|false Transaction data or false if not found
     */
    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tbl_transactions WHERE transaction_id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching transaction by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a new transaction
     * 
     * @param object $data Transaction data
     * @return bool Success status
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO tbl_transactions (request_id, admin_user_id, transaction_date)
                    VALUES (:request_id, :admin_user_id, NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':request_id', $data->request_id, PDO::PARAM_INT);

            // Handle null admin_user_id
            if (isset($data->admin_user_id) && $data->admin_user_id !== null) {
                $stmt->bindParam(':admin_user_id', $data->admin_user_id, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':admin_user_id', null, PDO::PARAM_NULL);
            }

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error creating transaction: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update transaction status and admin verification
     * 
     * @param int $id Transaction ID
     * @param string $transaction_status New status
     * @param bool $admin_verified Admin verification status
     * @return bool Success status
     */
    public function updateStatus($id, $transaction_status, $admin_verified)
    {
        try {
            $sql = "UPDATE tbl_transactions SET transaction_status = :transaction_status, admin_verified = :admin_verified 
                    WHERE transaction_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':transaction_status', $transaction_status, PDO::PARAM_STR);
            $adminVerifiedValue = $admin_verified ? 1 : 0;
            $stmt->bindParam(':admin_verified', $adminVerifiedValue, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error updating transaction status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a transaction
     * 
     * @param int $id Transaction ID
     * @return bool Success status
     */
    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM tbl_transactions WHERE transaction_id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting transaction: " . $e->getMessage());
            return false;
        }
    }
}
