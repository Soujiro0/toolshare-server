<?php
require_once __DIR__ . '/../models/Item.php';
class ItemController
{
    public function listItems()
    {
        /**
         * Default value params
         * limit = 10
         * page = 1
         */
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        try {
            $item = new Item();
            $items = $item->getAll($limit, $offset);
            $totalItems = $item->getTotalCount();

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

    public function createItem($data)
    {
        $item = new Item();
        $item->name = $data->name;
        $item->category = $data->category;
        $item->total_quantity = $data->total_quantity;
        $item->category_id = isset($data->category_id) ? $data->category_id : null;

        if ($item->save()) {
            echo json_encode(["message" => "Item created successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error creating item"]);
        }
    }

    public function updateItem($id, $data)
    {
        $item = new Item();
        if ($item->update($id, $data)) {
            echo json_encode(["message" => "Item updated successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error updating item"]);
        }
    }

    public function deleteItem($id)
    {
        $item = new Item();
        if ($item->delete($id)) {
            echo json_encode(["message" => "Item deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error deleting item"]);
        }
    }
}
