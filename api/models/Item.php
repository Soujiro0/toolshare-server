<?php
require_once 'Database.php';

/**
 * Class Item
 * 
 * Handles CRUD operations for items in the inventory system.
 */
class Item
{
    public $item_code;
    public $item_name;
    public $item_brand;
    public $model;
    public $is_bulk;
    public $total_quantity;
    public $category_id;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Inserts a new item into the database.
     * @return bool Returns true if the operation is successful, otherwise false.
     */
    public function save()
    {
        try {
            $sql = "INSERT INTO tbl_items (item_code, item_name, item_brand, model, is_bulk, total_quantity, category_id) 
                    VALUES (:item_code, :item_name, :item_brand, :model, :is_bulk, :total_quantity, :category_id)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':item_code', $this->item_code);
            $stmt->bindParam(':item_name', $this->item_name);
            $stmt->bindParam(':item_brand', $this->item_brand);
            $stmt->bindParam(':model', $this->model);
            $stmt->bindParam(':is_bulk', $this->is_bulk, PDO::PARAM_BOOL);
            $stmt->bindParam(':total_quantity', $this->total_quantity, PDO::PARAM_INT);
            $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error saving item: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves all items from the database with optional filters.
     * 
     * @param int $limit Number of items per page (default: 10).
     * @param int $offset Starting point for pagination (default: 0).
     * @param int|null $category_id Filters by category ID (optional).
     * @param string $sortBy Column to sort by (default: 'item_id').
     * @param string $order Sorting order: 'ASC' or 'DESC' (default: 'ASC').
     * @param string|null $search Search term for item name or code (optional).
     * 
     * @return array Returns an array of items.
     */
    public function getAll($limit = 10, $offset = 0, $category_id = null, $sortBy = 'item_id', $order = 'ASC', $search = null)
    {
        try {
            $sql = "SELECT * FROM tbl_items WHERE 1=1";
            
            if ($category_id !== null) {
                $sql .= " AND category_id = :category_id";
            }
            if ($search !== null) {
                $sql .= " AND (item_name LIKE :search OR item_code LIKE :search)";
            }
            $sql .= " ORDER BY $sortBy $order LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            if ($category_id !== null) {
                $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
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
            error_log("Error fetching items: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieves the total count of items, optionally filtered by category or search term.
     * 
     * @param int|null $category_id Filters by category ID (optional).
     * @param string|null $search Search term for item name or code (optional).
     * 
     * @return int Returns the total count of matching items.
     */
    public function getTotalCount($category_id = null, $search = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM tbl_items WHERE 1=1";
            
            if ($category_id !== null) {
                $sql .= " AND category_id = :category_id";
            }
            if ($search !== null) {
                $sql .= " AND (item_name LIKE :search OR item_code LIKE :search)";
            }
            
            $stmt = $this->db->prepare($sql);
            if ($category_id !== null) {
                $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            }
            if ($search !== null) {
                $searchTerm = "%$search%";
                $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
            }
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (Exception $e) {
            error_log("Error fetching item count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Retrieves a specific item by ID.
     * 
     * @param int $id The ID of the item.
     * 
     * @return void Outputs JSON response.
     */
    public function getItemById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM tbl_items WHERE item_id = :item_id");
        $stmt->execute([':item_id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Updates an existing item in the database.
     * 
     * @param int $id The ID of the item to update.
     * @param object $data Object containing updated item details.
     * 
     * @return bool Returns true if update is successful, otherwise false.
     */
    public function update($id, $data)
    {
        try {
            $sql = "UPDATE tbl_items SET 
                    item_code = :item_code, 
                    item_name = :item_name, 
                    item_brand = :item_brand, 
                    model = :model, 
                    is_bulk = :is_bulk, 
                    total_quantity = :total_quantity, 
                    category_id = :category_id 
                    WHERE item_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':item_code', $data->item_code);
            $stmt->bindParam(':item_name', $data->item_name);
            $stmt->bindParam(':item_brand', $data->item_brand);
            $stmt->bindParam(':model', $data->model);
            $stmt->bindParam(':is_bulk', $data->is_bulk, PDO::PARAM_BOOL);
            $stmt->bindParam(':total_quantity', $data->total_quantity, PDO::PARAM_INT);
            $stmt->bindParam(':category_id', $data->category_id, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error updating item: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes an item from the database.
     * 
     * @param int $id The ID of the item to delete.
     * 
     * @return bool Returns true if delete is successful, otherwise false.
     */
    public function delete($id)
    {
        try {
            $db = Database::getInstance();
            $sql = "DELETE FROM tbl_items WHERE item_id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting item: " . $e->getMessage());
            return false;
        }
    }
}
?>
