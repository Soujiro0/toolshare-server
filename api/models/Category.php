<?php
// api/models/Category.php
require_once 'Database.php';

class Category {
    public function create($name) {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->bindParam(":name", $name);
        $stmt->execute();
        return $db->lastInsertId();
    }

    public function getAll() {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT * FROM categories");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // New method: Get category by name (for duplication check)
    public function getByName($name) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM categories WHERE name = :name");
        $stmt->bindParam(":name", $name);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $name) {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE categories SET name = :name WHERE id = :id");
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
