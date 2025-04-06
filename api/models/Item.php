<?php
require_once 'Database.php';

class Item
{
    public $name;
    public $category_id;
    public $unit;
    public $acquisition_date;
    public $units; // Array of unit data

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Create a new item and its units (brands, models)
    public function create($itemData, $unitData)
    {
        try {
            // Insert into tbl_items
            $sql = "INSERT INTO tbl_items (name, category_id, unit, acquisition_date) 
                    VALUES (:name, :category_id, :unit, :acquisition_date)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $itemData->name);
            $stmt->bindParam(':category_id', $itemData->category_id, PDO::PARAM_INT);
            $stmt->bindParam(':unit', $itemData->unit);
            $stmt->bindParam(':acquisition_date', $itemData->acquisition_date);
            $stmt->execute();
            $item_id = $this->db->lastInsertId(); // Get the ID of the newly inserted item

            // Get the last property_no to continue from
            $sql = "SELECT MAX(CAST(SUBSTRING(property_no, LOCATE('-', property_no) + 1) AS UNSIGNED)) as last_property_no
                    FROM tbl_item_units WHERE item_id = :item_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $last_property_no = $result['last_property_no'] ?? 0;

            // Insert the units for the item (with brand and model)
            foreach ($unitData as $unit) {
                $last_property_no++;
                $property_no = $item_id . '-' . str_pad($last_property_no, 3, '0', STR_PAD_LEFT); // Auto-generated property_no

                $sql = "INSERT INTO tbl_item_units (item_id, property_no, item_condition, status, brand, model, specification, date_acquired) 
                        VALUES (:item_id, :property_no, :item_condition, :status, :brand, :model, :specification, :date_acquired)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $stmt->bindParam(':property_no', $property_no);
                $stmt->bindParam(':item_condition', $unit->item_condition);
                $stmt->bindParam(':status', $unit->status);
                $stmt->bindParam(':brand', $unit->brand);
                $stmt->bindParam(':model', $unit->model);
                $stmt->bindParam(':specification', $unit->specification);
                $stmt->bindParam(':date_acquired', $unit->date_acquired);
                $stmt->execute();
            }

            return true;
        } catch (Exception $e) {
            error_log("Error saving item: " . $e->getMessage());
            return false;
        }
    }

    public function getAll()
    {
        try {
            $sql = "SELECT i.*, c.category_name, COUNT(u.unit_id) AS quantity
                    FROM tbl_items i
                    JOIN tbl_item_category c ON i.category_id = c.category_id
                    LEFT JOIN tbl_item_units u ON i.item_id = u.item_id
                    GROUP BY i.item_id
                    ORDER BY i.date_created DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Add the units for each item
            foreach ($items as &$item) {
                $item['units'] = $this->getUnitsForItem($item['item_id']);  // Get associated units for each item
            }

            return $items;
        } catch (Exception $e) {
            error_log("Error fetching items: " . $e->getMessage());
            return false;
        }
    }

    // Get item by ID
    public function getById($id)
    {
        try {
            $sql = "SELECT i.*, c.category_name, COUNT(u.property_no) as quantity 
                        FROM tbl_items i
                        JOIN tbl_item_category c ON i.category_id = c.category_id
                        LEFT JOIN tbl_item_units u ON i.item_id = u.item_id AND u.status = 'AVAILABLE'
                        WHERE i.item_id = :item_id
                        GROUP BY i.item_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":item_id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            $item['units'] = $this->getUnitsForItem($item['item_id']);

            return $item;
        } catch (Exception $e) {
            error_log("Error fetching item by ID: " . $e->getMessage());
            return false;
        }
    }

    // Helper function to fetch units for a specific item
    private function getUnitsForItem($itemId)
    {
        try {
            $sql = "SELECT * FROM tbl_item_units WHERE item_id = :item_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":item_id", $itemId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as &$item) {
                $item['units'] = $this->getUnitsForItem($item['item_id']);  // Get associated units for each item
            }
        } catch (Exception $e) {
            error_log("Error fetching units for item: " . $e->getMessage());
            return [];
        }
    }

    // Update Item Details (e.g., name, category, unit, acquisition_date)
    public function updateItem($itemId, $itemData)
    {
        try {
            $sql = "UPDATE tbl_items
                    SET name = :name, category_id = :category_id, unit = :unit, acquisition_date = :acquisition_date
                    WHERE item_id = :item_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $itemData->name);
            $stmt->bindParam(':category_id', $itemData->category_id);
            $stmt->bindParam(':unit', $itemData->unit);
            $stmt->bindParam(':acquisition_date', $itemData->acquisition_date);
            $stmt->bindParam(':item_id', $itemId);
            $stmt->execute();

            return true;
        } catch (Exception $e) {
            error_log("Error updating item: " . $e->getMessage());
            return false;
        }
    }

    // Update Item Unit Details (e.g., condition, status, brand, model, specification)
    public function updateUnit($unitId, $unitData)
    {
        try {
            $sql = "UPDATE tbl_item_units
                    SET item_condition = :item_condition, status = :status, brand = :brand, model = :model, specification = :specification, date_acquired = :date_acquired
                    WHERE unit_id = :unit_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':item_condition', $unitData->item_condition);
            $stmt->bindParam(':status', $unitData->status);
            $stmt->bindParam(':brand', $unitData->brand);
            $stmt->bindParam(':model', $unitData->model);
            $stmt->bindParam(':specification', $unitData->specification);
            $stmt->bindParam(':date_acquired', $unitData->date_acquired);
            $stmt->bindParam(':unit_id', $unitId);
            $stmt->execute();

            return true;
        } catch (Exception $e) {
            error_log("Error updating unit: " . $e->getMessage());
            return false;
        }
    }

    // Delete an item from the database
    public function deleteItem($itemId)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM tbl_items WHERE item_id = :id");
            $stmt->bindParam(':id', $itemId);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting item: " . $e->getMessage());
            return false;
        }
    }

    // Delete a unit from the database
    public function deleteUnit($unitId)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM tbl_item_units WHERE unit_id = :id");
            $stmt->bindParam(':id', $unitId);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error deleting unit: " . $e->getMessage());
            return false;
        }
    }

    // Find Item by Name
    public function findByName($name)
    {
        try {
            $sql = "SELECT * FROM tbl_items WHERE name = :name";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error finding item by name: " . $e->getMessage());
            return false;
        }
    }

    // Method to get the last property_no for a specific item
    private function getLastPropertyNo($itemId)
    {
        try {
            $sql = "SELECT MAX(CAST(SUBSTRING(property_no, LOCATE('-', property_no) + 1) AS UNSIGNED)) as last_property_no
                FROM tbl_item_units WHERE item_id = :item_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':item_id', $itemId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['last_property_no'] ?? 0;  // Default to 0 if no property_no exists
        } catch (Exception $e) {
            error_log("Error fetching last property_no: " . $e->getMessage());
            return 0;  // Default to 0 if there's an error
        }
    }

    // Method to add units to an existing item
    public function addUnitsToExistingItem($itemId, $unitData)
    {
        try {
            // Loop through each unit and insert it into tbl_item_units
            foreach ($unitData as $unit) {
                $lastPropertyNo = $this->getLastPropertyNo($itemId);  // You can reuse the logic to get the last property_no
                $lastPropertyNo++;
                $propertyNo = $itemId . '-' . str_pad($lastPropertyNo, 3, '0', STR_PAD_LEFT); // Auto-generated property_no

                // Insert the new unit
                $sql = "INSERT INTO tbl_item_units (item_id, property_no, item_condition, status, brand, model, specification, date_acquired) 
                            VALUES (:item_id, :property_no, :item_condition, :status, :brand, :model, :specification, :date_acquired)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':item_id', $itemId, PDO::PARAM_INT);
                $stmt->bindParam(':property_no', $propertyNo);
                $stmt->bindParam(':item_condition', $unit->item_condition);
                $stmt->bindParam(':status', $unit->status);
                $stmt->bindParam(':brand', $unit->brand);
                $stmt->bindParam(':model', $unit->model);
                $stmt->bindParam(':specification', $unit->specification);
                $stmt->bindParam(':date_acquired', $unit->date_acquired);
                $stmt->execute();
            }
        } catch (Exception $e) {
            error_log("Error adding units to existing item: " . $e->getMessage());
            return false;
        }
    }
}
