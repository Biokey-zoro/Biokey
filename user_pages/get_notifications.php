<?php
require '../gen_functions/config.php'; // Database connection

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT id, message, created_at FROM notifications ORDER BY created_at DESC LIMIT 10");
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($notifications);
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
