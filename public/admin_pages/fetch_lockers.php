<?php
require '../gen_functions/config.php'; // Ensure this file has the correct database connection

header('Content-Type: application/json');

$query = "SELECT locker_id, locker_number FROM lockers";
$result = $conn->query($query);

$lockers = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lockers[] = $row;
    }
}

echo json_encode($lockers);
$conn->close();
?>
