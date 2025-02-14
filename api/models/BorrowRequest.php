<?php
// api/models/BorrowRequest.php

require_once 'Database.php';

class BorrowRequest
{
    public $student_name;
    public $course_year;
    public $instructor_id;
    public $purpose;

    public function save()
    {
        $db = Database::getInstance();
        $sql = "INSERT INTO borrow_requests (student_name, course_year, instructor_id, purpose) 
                VALUES (:student_name, :course_year, :instructor_id, :purpose)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':student_name', $this->student_name);
        $stmt->bindParam(':course_year', $this->course_year);
        $stmt->bindParam(':instructor_id', $this->instructor_id);
        $stmt->bindParam(':purpose', $this->purpose);
        return $stmt->execute();
    }

    public function getAll()
    {
        $db = Database::getInstance();
        $sql = "SELECT * FROM borrow_requests";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $column, $value)
    {
        $db = Database::getInstance();
        $sql = "UPDATE borrow_requests SET $column = :value WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':value', $value, PDO::PARAM_BOOL);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
