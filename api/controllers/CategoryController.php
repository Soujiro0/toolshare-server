<?php
require_once __DIR__ . '/../models/Category.php';
class CategoryController
{
    public function listCategories()
    {
        $category = new Category();
        try {
            $categories = $category->getAll();
            echo json_encode($categories);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error fetching categories", "error" => $e->getMessage()]);
        }
    }

    public function getCategory($id)
    {
        $category = new Category();
        try {
            $result = $category->getById($id);
            if ($result) {
                echo json_encode($result);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Category not found"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error fetching category", "error" => $e->getMessage()]);
        }
    }

    public function createCategory($data)
    {
        if (!isset($data->name)) {
            http_response_code(400);
            echo json_encode(["message" => "Category name is required"]);
            return;
        }
        $category = new Category();

        // Check for duplication
        $existing = $category->getByName($data->name);
        if ($existing) {
            http_response_code(409); // Conflict
            echo json_encode(["message" => "Category already exists"]);
            return;
        }

        try {
            $id = $category->create($data->name);
            echo json_encode(["message" => "Category created successfully", "id" => $id]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error creating category", "error" => $e->getMessage()]);
        }
    }

    public function updateCategory($id, $data)
    {
        if (!isset($data->name)) {
            http_response_code(400);
            echo json_encode(["message" => "Category name is required"]);
            return;
        }
        $category = new Category();
        try {
            $category->update($id, $data->name);
            echo json_encode(["message" => "Category updated successfully"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error updating category", "error" => $e->getMessage()]);
        }
    }

    public function deleteCategory($id)
    {
        $category = new Category();
        try {
            $category->delete($id);
            echo json_encode(["message" => "Category deleted successfully"]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error deleting category", "error" => $e->getMessage()]);
        }
    }
}
