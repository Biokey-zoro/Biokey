<?php
include('../gen_functions/config.php');

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'];
$face_encoding = $data['face_encoding'];

// Prepare SQL query to insert facial data into 'faces' table
$stmt = $conn->prepare("INSERT INTO faces (user_id, face_encoding) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $face_encoding);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Error enrolling face."]);
}

$stmt->close();
$conn->close();
?>
