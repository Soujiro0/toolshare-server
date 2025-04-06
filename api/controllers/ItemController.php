<?php
require_once __DIR__ . '/../models/Item.php';

class ItemController
{
    private $model;

    public function __construct()
    {
        $this->model = new Item();
    }

    // Get all items with quantities
    public function getAllItems()
    {
        try {
            $items = $this->model->getAll();
            echo json_encode([
                "items" => $items,
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error fetching items",
                "error" => $e->getMessage()
            ]);
        }
    }

    // Get item by ID
    public function getItemById($id)
    {
        try {
            $item = $this->model->getById($id);
            if ($item) {
                echo json_encode([
                    "item" => $item
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    "message" => "Item not found"
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error retrieving item",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function createItem($data)
    {
        try {
            // Check if the item already exists by name
            $existingItem = $this->model->findByName($data->name);

            if ($existingItem) {
                // Item exists, use the existing item ID
                $itemId = $existingItem['item_id'];
                $itemData = new stdClass();
                $itemData->name = $data->name;
                $itemData->category_id = $existingItem['category_id'];
                $itemData->unit = $existingItem['unit'];
                $itemData->acquisition_date = $data->acquisition_date;

                // Loop through units and prepare data for each unit (brand, model, and quantity)
                $unitData = [];
                foreach ($data->units as $unit) {
                    // For each brand, create the number of units specified in the "quantity" field
                    for ($i = 0; $i < $unit->quantity; $i++) {
                        $unitItem = new stdClass();
                        $unitItem->property_no = null; // Will be generated automatically
                        $unitItem->brand = $unit->brand;
                        $unitItem->model = $unit->model;
                        $unitItem->specification = $unit->specification;
                        $unitItem->item_condition = $unit->item_condition;
                        $unitItem->status = 'AVAILABLE';
                        $unitItem->date_acquired = $data->acquisition_date;
                        $unitData[] = $unitItem;
                    }
                }

                // Insert the new units under the existing item ID
                $this->model->addUnitsToExistingItem($itemId, $unitData);

                // Respond with a success message
                echo json_encode([
                    "message" => "Units added to existing item successfully"
                ]);
            } else {
                // Item does not exist, create a new item and its associated units
                $itemData = new stdClass();
                $itemData->name = $data->name;
                $itemData->category_id = $data->category_id;
                $itemData->unit = $data->unit;
                $itemData->acquisition_date = $data->acquisition_date;

                // Loop through units and prepare data for each unit (brand, model, and quantity)
                $unitData = [];
                foreach ($data->units as $unit) {
                    // For each brand, create the number of units specified in the "quantity" field
                    for ($i = 0; $i < $unit->quantity; $i++) {
                        $unitItem = new stdClass();
                        $unitItem->property_no = null; // Will be generated automatically
                        $unitItem->brand = $unit->brand;
                        $unitItem->model = $unit->model;
                        $unitItem->specification = $unit->specification;
                        $unitItem->item_condition = $unit->item_condition;
                        $unitItem->status = 'AVAILABLE';
                        $unitItem->date_acquired = $data->acquisition_date;
                        $unitData[] = $unitItem;
                    }
                }

                // Create the item and its associated units
                $this->model->create($itemData, $unitData);

                echo json_encode([
                    "message" => "New item and units created successfully"
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error creating or adding units",
                "error" => $e->getMessage()
            ]);
        }
    }

    // Update item details (name, category, unit, acquisition_date)
    public function updateItem($id, $data)
    {
        try {
            // Check if the required fields exist
            if (empty($data->name) || empty($data->category_id) || empty($data->unit) || empty($data->acquisition_date)) {
                http_response_code(400);
                echo json_encode([
                    "message" => "Missing required fields"
                ]);
                return;
            }

            // Prepare the item data
            $itemData = new stdClass();
            $itemData->name = $data->name;
            $itemData->category_id = $data->category_id;
            $itemData->unit = $data->unit;
            $itemData->acquisition_date = $data->acquisition_date;

            // Update the item in the database
            $result = $this->model->updateItem($id, $itemData);
            if ($result) {
                echo json_encode([
                    "message" => "Item updated successfully"
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    "message" => "Failed to update item"
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error updating item", "error" => $e->getMessage()
            ]);
        }
    }


    // Update unit details (condition, status, brand, model, specification)

    public function updateUnit($unitId, $data)
    {
        try {
            // Check if the required fields exist
            if (empty($data->item_condition) || empty($data->status) || empty($data->brand) || empty($data->model) || empty($data->specification) || empty($data->date_acquired)) {
                http_response_code(400);
                echo json_encode([
                    "message" => "Missing required fields"
                ]);
                return;
            }

            // Prepare the unit data
            $unitData = new stdClass();
            $unitData->item_condition = $data->item_condition;
            $unitData->status = $data->status;
            $unitData->brand = $data->brand;
            $unitData->model = $data->model;
            $unitData->specification = $data->specification;
            $unitData->date_acquired = $data->date_acquired;

            // Update the unit in the database
            $result = $this->model->updateUnit($unitId, $unitData);
            if ($result) {
                echo json_encode([
                    "message" => "Unit updated successfully"
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    "message" => "Failed to update unit"
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error updating unit", 
                "error" => $e->getMessage()
            ]);
        }
    }
    // Delete an item
    public function deleteItem($itemId)
    {
        try {
            // Proceed to delete the item
            $result = $this->model->deleteItem($itemId);
            if ($result) {
                echo json_encode([
                    "message" => "Item deleted successfully"
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    "message" => "Failed to delete item"
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error deleting item", 
                "error" => $e->getMessage()
            ]);
        }
    }

    // Delete a unit
    public function deleteUnit($unitId)
    {
        try {
            // Proceed to delete the unit
            $result = $this->model->deleteUnit($unitId);
            if ($result) {
                echo json_encode([
                    "message" => "Unit deleted successfully"
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    "message" => "Failed to delete unit"
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error deleting unit", 
                "error" => $e->getMessage()
            ]);
        }
    }
}
