<?php
include '../gen_functions/config.php'; // Ensure this connects to your database

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id"])) {
    $pinId = intval($_POST["id"]);

    $query = "DELETE FROM lockers_pin_hw WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $pinId);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "SQL Error: " . $stmt->error; // Show SQL error
        }
        $stmt->close();
    } else {
        echo "Prepare Error: " . $conn->error; // Show prepare error
    }

    $conn->close();
}
?>
