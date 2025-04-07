<?php
require_once "Database.php";

class BorrowRequestItem
{
    public $request_id;
    public $unit_id;
    public $quantity;
    public $item_condition_out;

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function assignUnitsToRequest($request_id, $units)
    {
        try {
            // Begin transaction
            $this->db->beginTransaction();

            // Validate request status
            $statusSql = "SELECT status FROM tbl_borrow_requests WHERE request_id = :request_id";
            $stmt = $this->db->prepare($statusSql);
            $stmt->execute([':request_id' => $request_id]);
            $request = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$request) {
                throw new Exception("Borrow request not found.");
            }

            if ($request['status'] !== 'APPROVED') {
                throw new Exception("Borrow request is not approved.");
            }

            // Prepare insert statements outside the loop for efficiency
            $insertSql = "INSERT INTO tbl_borrow_request_items (request_id, unit_id, item_condition_out)
                            VALUES (:request_id, :unit_id, :item_condition_out)";
            $insertStmt = $this->db->prepare($insertSql);

            // Loop through units and assign them
            $unitsToUpdate = []; // Array to track which units need to be updated to IN_USE

            foreach ($units as $unit) {
                if (!isset($unit->unit_id, $unit->item_condition_out)) {
                    throw new Exception("Missing unit_id or item_condition_out in unit data.");
                }

                // Check unit availability and validity
                $unitSql = "SELECT iu.unit_id, iu.item_id, iu.status, brs.item_id AS requested_item_id
                            FROM tbl_item_units iu
                            JOIN tbl_borrow_request_summary brs ON iu.item_id = brs.item_id
                            WHERE iu.unit_id = :unit_id AND brs.request_id = :request_id";
                $stmt = $this->db->prepare($unitSql);
                $stmt->execute([':unit_id' => $unit->unit_id, ':request_id' => $request_id]);
                $unitData = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$unitData) {
                    throw new Exception("Unit ID {$unit->unit_id} is not part of the requested items.");
                }

                if ($unitData['status'] !== 'AVAILABLE') {
                    throw new Exception("Unit ID {$unit->unit_id} is not available. (status: {$unitData['status']}).");
                }

                // Insert assignment into tbl_borrow_request_items
                $insertStmt->execute([
                    ':request_id' => $request_id,
                    ':unit_id' => $unit->unit_id,
                    ':item_condition_out' => $unit->item_condition_out
                ]);

                // Track the unit for status update after all validation is done
                $unitsToUpdate[] = $unit->unit_id;
            }

            // Now, update unit status to IN_USE (after all validation and inserts)
            if (!empty($unitsToUpdate)) {
                $updateSql = "UPDATE tbl_item_units SET status = 'IN_USE' WHERE unit_id = :unit_id";
                $updateStmt = $this->db->prepare($updateSql);
                foreach ($unitsToUpdate as $unit_id) {
                    $updateStmt->execute([':unit_id' => $unit_id]);
                }
            }

            // Commit transaction
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Rollback on error
            $this->db->rollBack();
            error_log("Error in assignUnitsToRequest: " . $e->getMessage());
            throw new Exception("Failed to assign units: " . $e->getMessage());
        }
    }

    public function getAssignedUnitsByRequest($request_id)
    {
        try {
            $sql = "SELECT 
                        bri.request_id,
                        bri.unit_id,
                        bri.item_condition_out,
                        bri.item_condition_in,
                        bri.returned_date,
                        iu.property_no,
                        iu.brand,
                        iu.model,
                        iu.specification,
                        iu.item_condition,
                        iu.status,
                        i.name AS item_name,
                        ic.category_name,
                        br.status AS request_status,
                        u.name AS requested_by,
                        br.request_date,
                        br.return_date
                    FROM tbl_borrow_request_items bri
                    JOIN tbl_item_units iu ON bri.unit_id = iu.unit_id
                    JOIN tbl_items i ON iu.item_id = i.item_id
                    JOIN tbl_item_category ic ON i.category_id = ic.category_id
                    JOIN tbl_borrow_requests br ON bri.request_id = br.request_id
                    JOIN tbl_users u ON br.user_id = u.user_id
                    WHERE bri.request_id = :request_id";

            $stmt = $this->db->prepare($sql);

            // Execute with parameter
            $stmt->execute([':request_id' => $request_id]);

            // Check if the result is empty
            if ($stmt->rowCount() > 0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                throw new Exception("No assigned units found for the given request ID.");
            }
        } catch (PDOException $e) {
            // Database-related error
            error_log("Database error: " . $e->getMessage());  // Log the error for debugging
            throw new Exception("An error occurred while retrieving data from the database.");
        } catch (Exception $e) {
            // Handle other errors
            error_log("Error: " . $e->getMessage());  // Log the error
            throw new Exception($e->getMessage());  // Rethrow the exception
        }
    }

    // Get all assigned units across all requests
    public function getAllAssignedUnits()
    {
        try {
            $sql = "SELECT 
                        bri.request_id,
                        bri.unit_id,
                        bri.item_condition_out,
                        bri.item_condition_in,
                        bri.returned_date,
                        bri.damage_status,
                        bri.damage_notes,
                        iu.property_no,
                        iu.brand,
                        iu.model,
                        iu.specification,
                        iu.item_condition,
                        iu.status,
                        i.name AS item_name,
                        ic.category_name,
                        br.status AS request_status,
                        u.name AS requested_by,
                        br.request_date,
                        br.return_date
                    FROM tbl_borrow_request_items bri
                    JOIN tbl_item_units iu ON bri.unit_id = iu.unit_id
                    JOIN tbl_items i ON iu.item_id = i.item_id
                    JOIN tbl_item_category ic ON i.category_id = ic.category_id
                    JOIN tbl_borrow_requests br ON bri.request_id = br.request_id
                    JOIN tbl_users u ON br.user_id = u.user_id";

            $stmt = $this->db->prepare($sql);

            // Execute the query
            $stmt->execute();

            // Check if the result is empty
            if ($stmt->rowCount() > 0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                throw new Exception("No assigned units found.");
            }
        } catch (PDOException $e) {
            // Database-related error
            error_log("Database error: " . $e->getMessage());  // Log the error for debugging
            throw new Exception("An error occurred while retrieving data from the database.");
        } catch (Exception $e) {
            // Handle other errors
            error_log("Error: " . $e->getMessage());  // Log the error
            throw new Exception($e->getMessage());  // Rethrow the exception
        }
    }

    // Get assigned unit by unit ID
    public function getAssignedUnitById($unit_id)
    {
        try {
            $sql = "SELECT 
                        bri.request_id,
                        bri.unit_id,
                        bri.item_condition_out,
                        bri.item_condition_in,
                        bri.returned_date,
                        bri.damage_status,
                        bri.damage_notes,
                        iu.property_no,
                        iu.brand,
                        iu.model,
                        iu.specification,
                        iu.item_condition,
                        iu.status,
                        i.name AS item_name,
                        ic.category_name,
                        br.status AS request_status,
                        u.name AS requested_by,
                        br.request_date,
                        br.return_date
                    FROM tbl_borrow_request_items bri
                    JOIN tbl_item_units iu ON bri.unit_id = iu.unit_id
                    JOIN tbl_items i ON iu.item_id = i.item_id
                    JOIN tbl_item_category ic ON i.category_id = ic.category_id
                    JOIN tbl_borrow_requests br ON bri.request_id = br.request_id
                    JOIN tbl_users u ON br.user_id = u.user_id
                    WHERE bri.unit_id = :unit_id";

            $stmt = $this->db->prepare($sql);

            // Execute with parameter
            $stmt->execute([':unit_id' => $unit_id]);

            // Check if the result is empty
            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                throw new Exception("No assigned unit found for the given unit ID.");
            }
        } catch (PDOException $e) {
            // Database-related error
            error_log("Database error: " . $e->getMessage());  // Log the error for debugging
            throw new Exception("An error occurred while retrieving data from the database.");
        } catch (Exception $e) {
            // Handle other errors
            error_log("Error: " . $e->getMessage());  // Log the error
            throw new Exception($e->getMessage());  // Rethrow the exception
        }
    }

    // Get assigned unit by unit ID
    public function getAssignedUnitByRequestId($request_id)
    {
        try {
            $sql = "SELECT 
                            bri.request_id,
                            bri.unit_id,
                            bri.item_condition_out,
                            bri.item_condition_in,
                            bri.returned_date,
                            bri.damage_status,
                            bri.damage_notes,
                            iu.property_no,
                            iu.brand,
                            iu.model,
                            iu.specification,
                            iu.item_condition,
                            iu.status,
                            i.name AS item_name,
                            ic.category_name,
                            br.status AS request_status,
                            u.name AS requested_by,
                            br.request_date,
                            br.return_date
                        FROM tbl_borrow_request_items bri
                        JOIN tbl_item_units iu ON bri.unit_id = iu.unit_id
                        JOIN tbl_items i ON iu.item_id = i.item_id
                        JOIN tbl_item_category ic ON i.category_id = ic.category_id
                        JOIN tbl_borrow_requests br ON bri.request_id = br.request_id
                        JOIN tbl_users u ON br.user_id = u.user_id
                        WHERE bri.request_id = :request_id";

            $stmt = $this->db->prepare($sql);

            // Execute with parameter
            $stmt->execute([':request_id' => $request_id]);

            // Check if the result is empty
            if ($stmt->rowCount() > 0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                throw new Exception("No assigned request found for the given request ID.");
            }
        } catch (PDOException $e) {
            // Database-related error
            error_log("Database error: " . $e->getMessage());  // Log the error for debugging
            throw new Exception("An error occurred while retrieving data from the database.");
        } catch (Exception $e) {
            // Handle other errors
            error_log("Error: " . $e->getMessage());  // Log the error
            throw new Exception($e->getMessage());  // Rethrow the exception
        }
    }

    public function updateUnit($unit_id, $item_condition_out, $status, $return_date = null)
    {
        try {
            // Start a database transaction
            $this->db->beginTransaction();

            // Update the unit in the tbl_item_units table
            $updateSql = "UPDATE tbl_item_units 
                          SET item_condition = :item_condition_out, status = :status
                          WHERE unit_id = :unit_id";

            $stmt = $this->db->prepare($updateSql);

            // Execute the query with the provided data
            $stmt->execute([
                ':unit_id' => $unit_id,
                ':item_condition_out' => $item_condition_out,
                ':status' => $status,
            ]);

            // Commit the transaction
            $this->db->commit();

            return true;
        } catch (Exception $e) {
            // Rollback in case of error
            $this->db->rollBack();
            throw new Exception("Error updating unit: " . $e->getMessage());
        }
    }

    // Method to update return details (damage status and notes)
    public function updateReturnDetails($request_id, $unit_id, $damage_status, $damage_notes)
    {
        try {
            $this->db->beginTransaction();
    
            $updateSql = "UPDATE tbl_borrow_request_items 
                          SET damage_status = :damage_status, damage_notes = :damage_notes, returned_date = NOW()
                          WHERE unit_id = :unit_id AND request_id = :request_id";
    
            $stmt = $this->db->prepare($updateSql);
            
    
            $stmt->execute([
                ':unit_id' => $unit_id,
                ':request_id' => $request_id,
                ':damage_status' => $damage_status,
                ':damage_notes' => $damage_notes
            ]);
    
            if ($stmt->rowCount() === 0) {
                throw new Exception("No record found for unit_id $unit_id and request_id $request_id.");
            }
    
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Error updating return details: " . $e->getMessage());
        }
    }
    

    // Method to update the unit status to 'AVAILABLE' after return
    public function updateUnitStatusToAvailable($unit_id)
    {
        try {
            // Update the unit status to 'AVAILABLE' (indicating it's returned)
            $updateSql = "UPDATE tbl_item_units SET status = 'AVAILABLE' WHERE unit_id = :unit_id";

            $stmt = $this->db->prepare($updateSql);

            // Execute the query
            $stmt->execute([':unit_id' => $unit_id]);

            return true;
        } catch (Exception $e) {
            throw new Exception("Error updating unit status: " . $e->getMessage());
        }
    }

    // Method to mark the request as 'RETURNED' once all items are returned
    public function markRequestAsReturned($request_id)
    {
        try {
            // First, check if all items are returned
            if ($this->areAllUnitsReturned($request_id)) {
                // If all items are returned, update the request status to 'RETURNED'
                $sql = "UPDATE tbl_borrow_requests
                    SET status = 'RETURNED', return_date = NOW()
                    WHERE request_id = :request_id";

                $stmt = $this->db->prepare($sql);
                $stmt->execute([':request_id' => $request_id]);

                return true;
            } else {
                throw new Exception("Not all units have been returned yet.");
            }
        } catch (Exception $e) {
            throw new Exception("Error updating request status: " . $e->getMessage());
        }
    }


    // Method to check if all units for a specific request have been returned
    public function areAllUnitsReturned($request_id)
    {
        try {
            // Query to check if all assigned units are returned by checking the 'returned_date' field
            $sql = "SELECT COUNT(*) as unreturned
                FROM tbl_borrow_request_items
                WHERE request_id = :request_id AND returned_date IS NULL";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':request_id' => $request_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // If the count of unreturned items is 0, then all items have been returned
            return $result['unreturned'] == 0;
        } catch (Exception $e) {
            throw new Exception("Error checking unreturned items: " . $e->getMessage());
        }
    }

    // Method to delete an assigned item from the borrow request
    public function deleteAssignedItem($request_id, $unit_id)
    {
        try {
            // Delete the item from the borrow request items table
            $deleteSql = "DELETE FROM tbl_borrow_request_items
                      WHERE request_id = :request_id AND unit_id = :unit_id";

            $stmt = $this->db->prepare($deleteSql);
            $stmt->execute([
                ':request_id' => $request_id,
                ':unit_id' => $unit_id
            ]);

            // Optionally, reset the unit's status to AVAILABLE if it's not already
            $this->updateUnitStatusToAvailable($unit_id);

            return true;
        } catch (Exception $e) {
            throw new Exception("Error deleting assigned item: " . $e->getMessage());
        }
    }
}
