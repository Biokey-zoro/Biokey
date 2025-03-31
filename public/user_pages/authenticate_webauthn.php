<?php
session_start();
require ('../gen_functions/config.php');

if (!isset($_SESSION['user_id'])) {
    die(json_encode(["status" => "error", "message" => "User not logged in."]));
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['credentialId'])) {
    die(json_encode(["status" => "error", "message" => "Invalid fingerprint data."]));
}

$credentialId = $data['credentialId'];

// Check if the credential exists in the database
$query = "SELECT * FROM webauthn_credentials WHERE user_id = ? AND credential_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $user_id, $credentialId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Fingerprint authenticated successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Fingerprint not recognized."]);
}
?>
