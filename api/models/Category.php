<?php
require_once 'Database.php';

/**
 * Class Category
 * 
 * Handles CRUD operations for item categories.
 */
class Category
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Creates a new category.
     * 
     * @param string $name The name of the category.
     * @return int|false Returns the last inserted category ID on success, false on failure.
     */
    public function create($name)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO tbl_item_category (category_name) VALUES (:name)");
            $stmt->bindParam(":name", $name);
            $stmt->execute();
            return  $this->db->lastInsertId();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Retrieves all categories.
     * 
     * @return array Returns an array of all categories.
     */
    public function getAll()
    {
        try {
            $stmt =  $this->db->query("SELECT * FROM tbl_item_category ORDER BY category_name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Retrieves a category by its ID.
     * 
     * @param int $id The category ID.
     * @return array|null Returns an associative array of the category data, or null if not found.
     */
    public function getById($id)
    {
        try {
            $stmt =  $this->db->prepare("SELECT * FROM tbl_item_category WHERE category_id = :id");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retrieves a category by its name.
     * 
     * @param string $name The category name.
     * @return array|null Returns an associative array of the category data, or null if not found.
     */
    public function getByName($name)
    {
        try {
            $stmt =  $this->db->prepare("SELECT * FROM tbl_item_category WHERE category_name = :name");
            $stmt->bindParam(":name", $name);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Updates a category by its ID.
     * 
     * @param int $id The category ID.
     * @param string $name The new category name.
     * @return bool Returns true if the update was successful, false otherwise.
     */
    public function update($id, $name)
    {
        try {
            $stmt = $this->db->prepare("UPDATE tbl_item_category SET category_name = :name WHERE category_id = :id");
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Deletes a category by its ID.
     * 
     * @param int $id The category ID.
     * @return bool Returns true if deletion was successful, false otherwise.
     */
    public function delete($id)
    {
        try {
            $stmt =  $this->db->prepare("DELETE FROM tbl_item_category WHERE category_id = :id");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            return false;
        }
    }
}
