<?php
require '../gen_functions/config.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $locker_id = $_POST['locker_id'];
    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("UPDATE lockers SET user_id = ?, status = 'occupied' WHERE locker_id = ?");
    $stmt->bind_param("ii", $user_id, $locker_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Locker assigned successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to assign locker."]);
    }

    $stmt->close();
    $conn->close();
}
?>
