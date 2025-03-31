<?php
require '../gen_functions/config.php';  // Ensure database connection

header("Content-Type: application/json");  // Send JSON response

$response = ["status" => "ACCESS_DENIED"]; // Default response

if (!isset($_POST['rfid_tag'])) {
    echo json_encode(["status" => "ERROR", "message" => "Missing RFID Data"]);
    exit;
}

$rfid_tag = $_POST['rfid_tag'];

// Step 1: Get user_id from registered_rfid
$query = "SELECT user_id FROM registered_rfid WHERE rfid_tag = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $rfid_tag);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $user_id = $row['user_id'];

    // Step 2: Get locker_id assigned to this user
    $lockerQuery = "SELECT locker_id, lphw_id FROM lockers WHERE user_id = ?";
    $lockerStmt = $conn->prepare($lockerQuery);
    $lockerStmt->bind_param("i", $user_id);
    $lockerStmt->execute();
    $lockerResult = $lockerStmt->get_result();

    if ($lockerRow = $lockerResult->fetch_assoc()) {
        $lphw_id = $lockerRow['lphw_id'];

        // Step 3: Get pin_number using lphw_id
        $pinQuery = "SELECT pin_number FROM lockers_pin_hw WHERE lphw_id = ?";
        $pinStmt = $conn->prepare($pinQuery);
        $pinStmt->bind_param("i", $lphw_id);
        $pinStmt->execute();
        $pinResult = $pinStmt->get_result();

        if ($pinRow = $pinResult->fetch_assoc()) {
            $response = [
                "status" => "SUCCESS",
                "pin_number" => $pinRow['pin_number']
            ];
        }
    }
}

// Send JSON response to ESP32
echo json_encode($response);
?>
