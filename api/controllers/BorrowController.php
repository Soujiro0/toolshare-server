<?php
// api/controllers/BorrowController.php

require_once __DIR__ . '/../models/BorrowRequest.php';

class BorrowController
{
    public function createBorrowRequest($data)
    {
        $borrowRequest = new BorrowRequest();
        $borrowRequest->student_name = $data->student_name;
        $borrowRequest->course_year = $data->course_year;
        $borrowRequest->instructor_id = $data->instructor_id;
        $borrowRequest->purpose = $data->purpose;

        if ($borrowRequest->save()) {
            echo json_encode(["message" => "Borrow request submitted"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error submitting borrow request"]);
        }
    }

    public function listBorrowRequests()
    {
        $borrowRequest = new BorrowRequest();
        $requests = $borrowRequest->getAll();
        echo json_encode($requests);
    }

    public function confirmSignature($id)
    {
        $borrowRequest = new BorrowRequest();
        if ($borrowRequest->updateStatus($id, 'signature_confirmed', true)) {
            echo json_encode(["message" => "Instructor signature confirmed"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error confirming signature"]);
        }
    }

    public function verifyAdmin($id)
    {
        $borrowRequest = new BorrowRequest();
        if ($borrowRequest->updateStatus($id, 'admin_verified', true)) {
            echo json_encode(["message" => "Admin verified the borrow request"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Error verifying borrow request"]);
        }
    }
}
