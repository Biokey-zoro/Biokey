<?php
session_start();
require '../gen_functions/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

try {
    // Fetch user details
    $stmt = $pdo->prepare("SELECT first_name, last_name, role FROM user_tbl WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch total user count
    $countStmt = $pdo->query("SELECT COUNT(*) AS total_users FROM user_tbl");
    $userCount = $countStmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode([
            "name" => $user['first_name'] . " " . $user['last_name'],
            "role" => $user['role'],
            "total_users" => $userCount['total_users']
        ]);
    } else {
        echo json_encode(["error" => "User not found"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error"]);
}
?>
