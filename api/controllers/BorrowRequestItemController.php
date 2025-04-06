<?php
require_once __DIR__ . '/../models/BorrowRequestItem.php';

class BorrowRequestItemController
{

    private $model;

    public function __construct()
    {
        $this->model = new BorrowRequestItem();
    }

    public function assignUnits($payload)
    {
        try {
            if (!isset($payload->request_id) || !isset($payload->assigned_units)) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "error" => "Invalid payload. Missing 'request_id' or 'assigned_units'."
                ]);
                return;
            }

            $request_id = $payload->request_id;
            $units = $payload->assigned_units;

            if (!is_array($units) || count($units) === 0) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "error" => "Assigned units must be a non-empty array."
                ]);
                return;
            }

            $this->model->assignUnitsToRequest($request_id, $units);

            echo json_encode([
                "success" => true,
                "message" => "Units successfully assigned to request ID $request_id"
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    // Get all assigned units
    public function getAllAssignedUnits()
    {
        try {
            $assignedUnits = $this->model->getAllAssignedUnits();
            echo json_encode([
                "success" => true,
                "data" => $assignedUnits
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    // Get assigned unit by unit ID
    public function getAssignedUnitById($unit_id)
    {
        try {
            $assignedUnit = $this->model->getAssignedUnitById($unit_id);
            echo json_encode([
                "success" => true,
                "data" => $assignedUnit
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    // Get assigned unit by unit ID
    public function getAssignedUnitRequestId($request_id)
    {
        try {
            $requestAssignedUnit = $this->model->getAssignedUnitByRequestId($request_id);
            echo json_encode([
                "success" => true,
                "data" => $requestAssignedUnit
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    // New method to update assigned units by admin
    public function updateAssignedUnit($payload)
    {
        try {
            // Check required fields
            if (!isset($payload->unit_id) || !isset($payload->item_condition_out) || !isset($payload->status)) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "error" => "Invalid payload. Missing 'unit_id', 'item_condition_out', or 'status'."
                ]);
                return;
            }

            $unit_id = $payload->unit_id;
            $item_condition_out = $payload->item_condition_out;
            $status = $payload->status;

            // Validate status, assuming possible statuses are "AVAILABLE", "IN_USE", "DAMAGED", "RETURNED"
            $validStatuses = ['AVAILABLE', 'IN_USE', 'UNDER_MAINTENANCE'];
            if (!in_array($status, $validStatuses)) {
                http_response_code(400);
                echo json_encode(["success" => false, "error" => "Invalid unit status."]);
                return;
            }

            // Call the model function to update unit
            $this->model->updateUnit($unit_id, $item_condition_out, $status);

            echo json_encode([
                "success" => true,
                "message" => "Unit successfully updated."
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    public function returnAllItems($payload)
    {
        try {
            // Ensure required fields are present
            if (!isset($payload->request_id) || !isset($payload->returned_units)) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "error" => "Missing request_id or returned_units"
                ]);
                return;
            }

            $request_id = $payload->request_id;
            $returnedUnits = $payload->returned_units;

            // Process each returned unit
            foreach ($returnedUnits as $unit) {
                if (!isset($unit->unit_id) || !isset($unit->damage_status) || !isset($unit->damage_notes)) {
                    http_response_code(400);
                    echo json_encode([
                        "success" => false,
                        "error" => "Missing fields in one of the returned_units"
                    ]);
                    return;
                }

                // Update return details for each unit and set its status to 'AVAILABLE'
                $this->model->updateReturnDetails($unit->unit_id, $unit->damage_status, $unit->damage_notes);
                $this->model->updateUnitStatusToAvailable($unit->unit_id);
            }

            // ✅ Check if all units in this request are returned
            $allReturned = $this->model->areAllUnitsReturned($request_id);

            // Only mark as RETURNED if all units are returned
            if ($allReturned) {
                $this->model->markRequestAsReturned($request_id);
                echo json_encode([
                    "success" => true,
                    "message" => "All items returned. Request marked as RETURNED."
                ]);
            } else {
                echo json_encode([
                    "success" => true,
                    "message" => "Units updated, but some items are still not returned. Request status unchanged."
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }

    public function deleteAssignedItem($payload)
    {
        try {
            // Ensure required fields are present
            if (!isset($payload->request_id) || !isset($payload->unit_id)) {
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "values" => $payload->request_id,
                    "error" => "Missing request_id or unit_id"
                ]);
                return;
            }

            $request_id = $payload->request_id;
            $unit_id = $payload->unit_id;

            // Call the model to delete the assigned item
            $result = $this->model->deleteAssignedItem($request_id, $unit_id);

            if ($result) {
                echo json_encode([
                    "success" => true,
                    "message" => "Assigned item deleted successfully."
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Failed to delete assigned item."
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => $e->getMessage()
            ]);
        }
    }
}
