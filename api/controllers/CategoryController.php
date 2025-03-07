<?php
require_once __DIR__ . '/../models/Category.php';

/**
 * Class CategoryController
 * 
 * Handles API requests for category management.
 */
class CategoryController
{
    /**
     * Retrieves all categories.
     * 
     * @return void Outputs JSON response containing the list of categories.
     */
    public function listCategories()
    {
        $category = new Category();
        try {
            $categories = $category->getAll();
            echo json_encode(["categories" => $categories]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error fetching categories",
                "error" => $e->getMessage()
            ]);
        }
    }

    /**
     * Retrieves a specific category by its ID.
     * 
     * @param int $id The category ID.
     * @return void Outputs JSON response with category data or an error message.
     */
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
            echo json_encode([
                "message" => "Error fetching category",
                "error" => $e->getMessage()
            ]);
        }
    }

    /**
     * Creates a new category.
     * 
     * @param object $data JSON-decoded request body containing category_name.
     * @return void Outputs JSON response with success or error message.
     */
    public function createCategory($data)
    {
        if (!isset($data->category_name) || empty(trim($data->category_name))) {
            http_response_code(400);
            echo json_encode(["message" => "Category name is required"]);
            return;
        }

        $category = new Category();

        // Check for duplication
        $existing = $category->getByName($data->category_name);
        if ($existing) {
            http_response_code(409); // Conflict
            echo json_encode(["message" => "Category already exists"]);
            return;
        }

        try {
            $id = $category->create($data->category_name);
            echo json_encode(["message" => "Category created successfully", "category_id" => $id]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error creating category",
                "error" => $e->getMessage()
            ]);
        }
    }

    /**
     * Updates an existing category by ID.
     * 
     * @param int $id The category ID.
     * @param object $data JSON-decoded request body containing category_name.
     * @return void Outputs JSON response with success or error message.
     */
    public function updateCategory($id, $data)
    {
        if (!isset($data->category_name) || empty(trim($data->category_name))) {
            http_response_code(400);
            echo json_encode(["message" => "Category name is required"]);
            return;
        }

        $category = new Category();

        // Check if category exists
        if (!$category->getById($id)) {
            http_response_code(404);
            echo json_encode(["message" => "Category not found"]);
            return;
        }

        // Check for duplicate name in other categories
        $existing = $category->getByName($data->category_name);
        if ($existing && $existing['category_id'] != $id) {
            http_response_code(409);
            echo json_encode(["message" => "Category name already in use"]);
            return;
        }

        try {
            if ($category->update($id, $data->category_name)) {
                echo json_encode(["message" => "Category updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error updating category"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error updating category",
                "error" => $e->getMessage()
            ]);
        }
    }

    /**
     * Deletes a category by ID.
     * 
     * @param int $id The category ID.
     * @return void Outputs JSON response with success or error message.
     */
    public function deleteCategory($id)
    {
        $category = new Category();

        // Check if category exists
        if (!$category->getById($id)) {
            http_response_code(404);
            echo json_encode(["message" => "Category not found"]);
            return;
        }

        try {
            if ($category->delete($id)) {
                echo json_encode(["message" => "Category deleted successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error deleting category"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Error deleting category",
                "error" => $e->getMessage()
            ]);
        }
    }
}
?>