<?php
require '../gen_functions/config.php'; 
header('Content-Type: application/json');

if (!isset($pdo)) {
    echo json_encode(["error" => "Database connection failed."]);
    exit;
}

// UPDATE THE SUBSCRIPTION STATUS
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['sub_id']) || !isset($_POST['status'])) {
        echo json_encode(["error" => "Invalid request."]);
        exit;
    }

    $sub_id = $_POST['sub_id'];
    $status = $_POST['status'];

    try {
        $sql = "UPDATE subscriptions_tbl SET status = :status WHERE sub_id = :sub_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['status' => $status, 'sub_id' => $sub_id]);

        echo json_encode(["success" => true]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(["error" => "Database error: " . $e->getMessage()]);
        exit;
    }
}

try {
    $sql = "SELECT 
                subscriptions_tbl.sub_id, 
                subscriptions_tbl.user_id, 
                CONCAT(user_tbl.first_name, ' ', user_tbl.middle_name, ' ', user_tbl.last_name) AS full_name, 
                subscriptions_tbl.locker_no, 
                subscriptions_tbl.status, 
                subscriptions_tbl.start_date, 
                subscriptions_tbl.end_date 
            FROM subscriptions_tbl 
            JOIN user_tbl ON subscriptions_tbl.user_id = user_tbl.user_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["data" => $data]); 
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>
