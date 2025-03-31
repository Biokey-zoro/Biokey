<?php
require '../gen_functions/config.php'; // Ensure you have a database connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $locker_number = $_POST["locker_number"];
    $pin_number = $_POST["pin_number"];

    if (!empty($locker_number) && !empty($pin_number)) {
        $stmt = $conn->prepare("INSERT INTO lockers (locker_number, pin_number) VALUES (?, ?)");
        $stmt->bind_param("is", $locker_number, $pin_number);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid input"]);
    }
}

$conn->close();
?>
