<?php
// api/models/Item.php

require_once 'Database.php';

class Item
{
    public $name;
    public $category;
    public $total_quantity;
    public $category_id;

    public function save()
    {
        $db = Database::getInstance();
        $sql = "INSERT INTO items (name, category, total_quantity, category_id) 
                VALUES (:name, :category, :total_quantity, :category_id)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':total_quantity', $this->total_quantity);
        $stmt->bindParam(':category_id', $this->category_id);
        return $stmt->execute();
    }

    public function getAll($limit = 10, $offset = 0, $category = null, $sortBy = 'id', $order = 'ASC', $search = null)
    {
        $db = Database::getInstance();
        $sql = "SELECT * FROM items WHERE 1=1";
        if ($category) {
            $sql .= " AND category = :category";
        }
        if ($search) {
            $sql .= " AND name LIKE :search";
        }
        $sql .= " ORDER BY $sortBy $order LIMIT :limit OFFSET :offset";
        $stmt = $db->prepare($sql);
        if ($category) {
            $stmt->bindParam(':category', $category);
        }
        if ($search) {
            $searchTerm = '%' . $search . '%';
            $stmt->bindParam(':search', $searchTerm);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCount($category = null, $search = null)
    {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) AS total FROM items WHERE 1=1";
        if ($category) {
            $sql .= " AND category = :category";
        }
        if ($search) {
            $sql .= " AND name LIKE :search";
        }
        $stmt = $db->prepare($sql);
        if ($category) {
            $stmt->bindParam(':category', $category);
        }
        if ($search) {
            $searchTerm = '%' . $search . '%';
            $stmt->bindParam(':search', $searchTerm);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function update($id, $data)
    {
        $db = Database::getInstance();
        $sql = "UPDATE items SET name = :name, category = :category, 
                total_quantity = :total_quantity, category_id = :category_id WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $data->name);
        $stmt->bindParam(':category', $data->category);
        $stmt->bindParam(':total_quantity', $data->total_quantity);
        $stmt->bindParam(':category_id', $data->category_id);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $db = Database::getInstance();
        $sql = "DELETE FROM items WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
