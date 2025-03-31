<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['rfidTag']) || !isset($data['userId'])) {
    echo json_encode(['success' => false, 'message' => '❗ Invalid data received.']);
    exit;
}

$rfidTag = $data['rfidTag'];
$userId = $data['userId'];

require_once('../gen_functions/config.php');

// Check if RFID already exists
$checkStmt = $conn->prepare("SELECT * FROM registered_rfid WHERE rfid_tag = ?");
$checkStmt->bind_param("s", $rfidTag);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => '⚠️ RFID is already registered.']);
    exit;
}

// Insert new RFID data
$stmt = $conn->prepare("INSERT INTO registered_rfid (rfid_tag, user_id) VALUES (?, ?)");
$stmt->bind_param("si", $rfidTag, $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => '✅ RFID registered successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => '❗ Failed to register RFID. Try again.']);
}

$stmt->close();
$conn->close();
?>
