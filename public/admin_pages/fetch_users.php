<?php
header('Content-Type: application/json');
require_once('../gen_functions/config.php');

// Ensure database connection exists
if (!$conn) {
    echo json_encode(["error" => "❗ Database connection failed"]);
    exit;
}

// Fetch only users with role 'user'
$query = "SELECT user_id, first_name, last_name FROM user_tbl WHERE role = 'user'";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(["error" => "❗ Database query failed: " . mysqli_error($conn)]);
    exit;
}

$users = [];
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = [
        "id" => $row['user_id'],
        "name" => $row['first_name'] . ' ' . $row['last_name']
    ];
}

// Return JSON data
echo json_encode($users);
mysqli_close($conn);
?>
