<?php
require_once 'Database.php';

class Category
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

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

    public function getAll()
    {
        try {
            $stmt =  $this->db->query("SELECT * FROM tbl_item_category ORDER BY category_name ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

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
