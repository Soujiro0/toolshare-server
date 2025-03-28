<?php
require_once 'Database.php';

class Item
{
    public $name;
    public $property_no;
    public $category_id;
    public $quantity;
    public $unit;
    public $brand;
    public $model;
    public $specification;
    public $status;
    public $item_condition;
    public $acquisition_date;

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create()
    {
        try {
            $sql = "INSERT INTO tbl_items (name, property_no, category_id, quantity, unit, brand, model, specification, status, item_condition, acquisition_date) 
                    VALUES (:name, :property_no, :category_id, :quantity, :unit, :brand, :model, :specification, :status, :item_condition, :acquisition_date)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':property_no', $this->property_no);
            $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $this->quantity, PDO::PARAM_INT);
            $stmt->bindParam(':unit', $this->unit);
            $stmt->bindParam(':brand', $this->brand);
            $stmt->bindParam(':model', $this->model);
            $stmt->bindParam(':specification', $this->specification);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':item_condition', $this->item_condition);
            $stmt->bindParam(':acquisition_date', $this->acquisition_date);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error saving item: " . $e->getMessage());
            return false;
        }
    }
    

    public function getAll()
    {
        try {
            $sql = "SELECT i.*, c.category_name 
                    FROM tbl_items i
                    JOIN tbl_item_category c ON i.category_id = c.category_id
                    ORDER BY i.date_created DESC";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching items: " . $e->getMessage());
            return false;
        }
    }    

    public function getById($id)
    {
        try {
            $sql = "SELECT i.*, c.category_name 
                    FROM tbl_items i
                    JOIN tbl_item_category c ON i.category_id = c.category_id
                    WHERE i.item_id = :item_id";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":item_id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching item by ID: " . $e->getMessage());
            return false;
        }
    }
    

    public function update($id, $data)
    {
        try {
            $sql = "UPDATE tbl_items SET 
                    property_no = :property_no, 
                    name = :name, 
                    category_id = :category_id, 
                    quantity = :quantity, 
                    unit = :unit, 
                    brand = :brand,
                    model = :model,
                    specification = :specification, 
                    status = :status, 
                    item_condition = :item_condition, 
                    acquisition_date = :acquisition_date,
                    date_updated = CURRENT_TIMESTAMP 
                    WHERE item_id = :id";
    
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':property_no', $data->property_no);
            $stmt->bindParam(':name', $data->name);
            $stmt->bindParam(':category_id', $data->category_id, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $data->quantity, PDO::PARAM_INT);
            $stmt->bindParam(':unit', $data->unit);
            $stmt->bindParam('brand', $data->brand);
            $stmt->bindParam('model', $data->model);
            $stmt->bindParam(':specification', $data->specification);
            $stmt->bindParam(':status', $data->status);
            $stmt->bindParam(':item_condition', $data->item_condition);
            $stmt->bindParam(':acquisition_date', $data->acquisition_date);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error updating item: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $db = Database::getInstance();
            $sql = "DELETE FROM tbl_items WHERE item_id = :item_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':item_id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting item: " . $e->getMessage());
            return false;
        }
    }
}
?>
