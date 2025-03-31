<?php
require '../gen_functions/config.php';

header('Content-Type: application/json');

try {
    if (!isset($pdo)) {
        throw new Exception("Database connection failed! Check config.php.");
    }

    $stmt = $pdo->query("SELECT user_id, locker_no, start_date FROM subscriptions_tbl ORDER BY start_date DESC LIMIT 5");
    $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($subscriptions);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
