<?php
session_start();
require('../gen_functions/config.php');

// Debugging: Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

// Validate received fingerprint data
if (!isset($data['credentialId'], $data['publicKey'])) {
    echo json_encode(["status" => "error", "message" => "Invalid fingerprint data."]);
    exit;
}

$credentialId = $data['credentialId'];
$publicKey = $data['publicKey'];

// Debugging: Log the received data
error_log("User ID: $user_id, Credential ID: $credentialId, Public Key: $publicKey");

// Store fingerprint credentials in the database
$query = "INSERT INTO webauthn_credentials (user_id, credential_id, public_key) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
    exit;
}

$stmt->bind_param("iss", $user_id, $credentialId, $publicKey);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Fingerprint registered successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to register fingerprint. Error: " . $stmt->error]);
}
?>
