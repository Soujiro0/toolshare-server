<?php
require_once __DIR__ . '/../models/Item.php';

class ItemController
{
    private $model;

    public function __construct()
    {
        $this->model = new Item();
    }

    public function getAllItems()
    {
        try {
            $items = $this->model->getAll();
            $totalItems = count($items);

            echo json_encode([
                "items" => $items,
                "total_items" => $totalItems,
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error fetching items",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function getItemById($id)
    {
        try {
            $item = $this->model->getById($id);
            if ($item) {
                echo json_encode(["item" => $item]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Item not found"]);
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
            $this->model->property_no = $data->property_no;
            $this->model->name = $data->name;
            $this->model->category_id = $data->category_id;
            $this->model->quantity = $data->quantity;
            $this->model->unit = $data->unit;
            $this->model->specification = $data->specification;
            $this->model->status = $data->status ?? 'Available';
            $this->model->item_condition = $data->item_condition ?? 'Good';
            $this->model->acquisition_date = $data->acquisition_date ?? null;
    
            if ($this->model->create()) {
                echo json_encode(["message" => "Item created successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error creating item"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error creating item",
                "error" => $e->getMessage()
            ]);
        }
    }
    

    public function updateItem($id, $data)
    {
        try {
            if ($this->model->update($id, $data)) {
                echo json_encode(["message" => "Item updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error updating item"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error updating item",
                "error" => $e->getMessage()
            ]);
        }
    }

    public function deleteItem($id)
    {
        try {
            if ($this->model->delete($id)) {
                echo json_encode(["message" => "Item deleted successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error deleting item"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error deleting item",
                "error" => $e->getMessage()
            ]);
        }
    }
}
