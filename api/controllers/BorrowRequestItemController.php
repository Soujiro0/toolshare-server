<?php
// app/controllers/BorrowRequestItemController.php
require_once __DIR__ . '/../models/BorrowRequestItem.php';

class BorrowRequestItemController {

    public function getAllBorrowRequestItems() {
        $model = new BorrowRequestItem();
        $items = $model->getAll();
        echo json_encode(["borrow_request_items" => $items]);
    }

    public function getBorrowRequestItemById($id) {
        $model = new BorrowRequestItem();
        $item = $model->getById($id);
        if ($item) {
            echo json_encode(["borrow_request_item" => $item]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Borrow request item not found"]);
        }
    }

    public function createBorrowRequestItem($data) {
        $model = new BorrowRequestItem();
        $model->request_id         = $data->request_id;         // The borrow request this item belongs to
        $model->item_id            = $data->item_id;
        $model->quantity           = $data->quantity;
        $model->item_condition_out = $data->item_condition_out; // e.g., 'Good'
        // Add any other properties as needed

        if ($model->create()) {
            echo json_encode(["message" => "Borrow request item created successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error creating borrow request item"]);
        }
    }

    public function updateBorrowRequestItem($id, $data) {
        $model = new BorrowRequestItem();
        if ($model->update($id, $data)) {
            echo json_encode(["message" => "Borrow request item updated successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error updating borrow request item"]);
        }
    }

    public function deleteBorrowRequestItem($id) {
        $model = new BorrowRequestItem();
        if ($model->delete($id)) {
            echo json_encode(["message" => "Borrow request item deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error deleting borrow request item"]);
        }
    }
}
