<?php
// api/controllers/ReturnController.php

require_once __DIR__ . '/../models/Database.php';

class ReturnController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function processReturn($data)
    {
        // $data should include transaction_id and optionally a damage_report
        $transaction_id = $data->transaction_id;
        $damage_report = isset($data->damage_report) ? $data->damage_report : null;
        $status = ($damage_report) ? 'Damaged' : 'Returned';

        $sql = "UPDATE transactions SET returned_date = NOW(), status = :status, damage_report = :damage_report 
                WHERE id = :transaction_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':damage_report', $damage_report);
        $stmt->bindParam(':transaction_id', $transaction_id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Return processed successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error processing return"]);
        }
    }
}
