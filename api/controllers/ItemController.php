<?php
require_once __DIR__ . '/../models/Item.php';

/**
 * Class ItemController
 * 
 * Handles API requests for managing inventory items.
 */
class ItemController
{

    private $model;

    /**
     * Constructor initializes the Item model.
     */
    public function __construct()
    {
        $this->model = new Item();
    }

    /**
     * Retrieves a paginated list of items with optional filters.
     * 
     * Query Parameters:
     * - `limit` (int)       : Number of items per page (default: 10).
     * - `page` (int)        : Page number for pagination (default: 1).
     * - `category_id` (int) : Filter by category (optional).
     * - `sort_by` (string)  : Column to sort by (default: 'item_id').
     * - `order` (string)    : Sorting order ('ASC' or 'DESC', default: 'ASC').
     * - `search` (string)   : Search query to filter by item name or code (optional).
     * 
     * @return void Outputs JSON response:
     * - Success: `{"items": [...], "totalItems": number}`
     * - Error: `{"message": "Error fetching items", "error": string}`
     */
    public function listItems()
    {
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

        $sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'item_id';
        $order = isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ? 'DESC' : 'ASC';
        $search = isset($_GET['search']) ? $_GET['search'] : null;

        try {
            $items = $this->model->getAll($limit, $offset, $category_id, $sortBy, $order, $search);
            $totalItems = $this->model->getTotalCount($category_id, $search);

            echo json_encode([
                "items" => $items,
                "totalItems" => $totalItems,
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error fetching items",
                "error" => $e->getMessage()
            ]);
        }
    }

    /**
     * Retrieves a specific borrow request by ID.
     * 
     * @param int $id The ID of the borrow request.
     * @return void Outputs JSON response:
     * - Success: `{"request": {...}}`
     * - Error: `{"message": "Request not found"}` or `{"message": "Error retrieving request", "error": string}`
     */
    public function getItemById($id)
    {
        try {
            $request = $this->model->getItemById($id);
            if ($request) {
                echo json_encode(["request" => $request]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Request not found"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error retrieving request",
                "error" => $e->getMessage()
            ]);
        }
    }

    /**
     * Creates a new item in the database.
     * 
     * Expected JSON Payload:
     * - `item_code` (string)  : Unique identifier for the item.
     * - `item_name` (string)  : Name of the item.
     * - `item_brand` (string) : Brand of the item.
     * - `model` (string)      : Model name/number.
     * - `is_bulk` (bool)      : Whether the item is bulk or not.
     * - `total_quantity` (int): Total available quantity.
     * - `category_id` (int)   : Category ID.
     * 
     * @param object $data JSON-decoded object with item details.
     * @return void Outputs JSON response:
     * - Success: `{"message": "Item created successfully"}`
     * - Error: `{"message": "Error creating item", "error": string}`
     */
    public function createItem($data)
    {
        try {
            $this->model->item_code = $data->item_code;
            $this->model->item_name = $data->item_name;
            $this->model->item_brand = $data->item_brand;
            $this->model->model = $data->model;
            $this->model->is_bulk = (bool) $data->is_bulk;
            $this->model->total_quantity = $data->total_quantity;
            $this->model->category_id = $data->category_id;

            if ($this->model->save()) {
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

    /**
     * Updates an existing item in the database.
     * 
     * Expected JSON Payload:
     * - `item_code` (string)  : Unique identifier for the item.
     * - `item_name` (string)  : Name of the item.
     * - `item_brand` (string) : Brand of the item.
     * - `model` (string)      : Model name/number.
     * - `is_bulk` (bool)      : Whether the item is bulk or not.
     * - `total_quantity` (int): Total available quantity.
     * - `category_id` (int)   : Category ID.
     * 
     * @param int $id The ID of the item to update.
     * @param object $data JSON-decoded object with updated item details.
     * @return void Outputs JSON response:
     * - Success: `{"message": "Item updated successfully"}`
     * - Error: `{"message": "Error updating item", "error": string}`
     */
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

    /**
     * Deletes an item from the database.
     * 
     * @param int $id The ID of the item to delete.
     * @return void Outputs JSON response:
     * - Success: `{"message": "Item deleted successfully"}`
     * - Error: `{"message": "Error deleting item", "error": string}`
     */
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
