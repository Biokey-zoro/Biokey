<?php
include('database.php');

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'];
$face_encoding = $data['face_encoding'];

// Query the 'faces' table to verify the user's face encoding
$stmt = $conn->prepare("SELECT * FROM faces WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Compare the encoding (for simplicity, assume it's an exact match)
    if ($row['face_encoding'] === $face_encoding) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Face not recognized."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No face data found for this user."]);
}

$stmt->close();
$conn->close();
?>
