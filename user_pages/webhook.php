<?php
require '../gen_functions/config.php';

// Retrieve raw request body
$raw_payload = file_get_contents("php://input");
$payload = json_decode($raw_payload, true);

// Debugging: Log webhook payload
file_put_contents("webhook_log.txt", json_encode($payload, JSON_PRETTY_PRINT) . PHP_EOL, FILE_APPEND);

// Check if webhook event is valid
if (!isset($payload['data']['attributes']['type'])) {
    http_response_code(400);
    exit("Invalid webhook payload.");
}

// Process only successful payments
if ($payload['data']['attributes']['type'] === "checkout_session.payment.paid") {
    $checkout_data = $payload['data']['attributes']['data'];

    $user_id = $checkout_data['metadata']['user_id'];
    $locker_id = $checkout_data['metadata']['locker_id'];
    $subscription_type = $checkout_data['metadata']['subscription_type'];
    $payment_status = $checkout_data['attributes']['status'];
    $payment_amount = $checkout_data['attributes']['line_items'][0]['amount'] / 100; // Convert back to PHP currency
    $transaction_id = $checkout_data['id'];

    if ($payment_status === "active") {
        // Save transaction details to `subscriptions_tbl`
        $stmt = $conn->prepare("INSERT INTO subscriptions_tbl (user_id, locker_id, subscription_type, amount, transaction_id, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisiss", $user_id, $locker_id, $subscription_type, $payment_amount, $transaction_id, $payment_status);
        $stmt->execute();
        $stmt->close();
    }

    http_response_code(200);
    exit("Webhook processed successfully.");
}

http_response_code(400);
exit("Unhandled webhook event.");
?>
