<?php
require '../gen_functions/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// PayMongo API Secret Key (Replace with your actual secret key)
$secret_key = "sk_test_L9Qsxkm3xH7iftqU6riZ7AX8";

// Check if session data exists
if (!isset($_SESSION["selected_plan"])) {
    echo json_encode(["error" => "No subscription plan selected."]);
    exit;
}

$selected_plan = $_SESSION["selected_plan"];

// Validate required fields
if (!isset($selected_plan["duration"], $selected_plan["description"], $selected_plan["price"])) {
    echo json_encode(["error" => "Missing required fields."]);
    exit;
}

// Convert price to cents (PayMongo requires amounts in cents)
$amount_in_cents = intval($selected_plan["price"]) * 100;

// Prepare checkout session data
$data = [
    "data" => [
        "attributes" => [
            "line_items" => [
                [
                    "currency" => "PHP",
                    "amount" => $amount_in_cents,
                    "name" => $selected_plan["duration"] . " Subscription",
                    "description" => $selected_plan["description"],
                    "quantity" => 1
                ]
            ],
            "payment_method_types" => ["gcash"],
            "description" => "Subscription Payment",
            "success_url" => "http://localhost/Final/LockerSystem/user_pages/success.php",
            "cancel_url" => "http://localhost/Final/LockerSystem/user_pages/cancel.php"
        ]
    ]
];

// Convert data to JSON
$json_data = json_encode($data);

// Initialize cURL request
$ch = curl_init("https://api.paymongo.com/v1/checkout_sessions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Basic " . base64_encode($secret_key . ":"),
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

// Execute request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Debugging: Log PayMongo response
file_put_contents("debug_log.txt", $response . PHP_EOL, FILE_APPEND);

// Decode JSON response
$response_data = json_decode($response, true);

// Check if response contains checkout URL
if ($http_code !== 200 || !isset($response_data["data"]["attributes"]["checkout_url"])) {
    echo json_encode(["error" => "Failed to create checkout session.", "response" => $response_data]);
    exit;
}

// Get checkout URL
$checkout_url = $response_data["data"]["attributes"]["checkout_url"];

// Return JSON response with valid PayMongo checkout URL
echo json_encode(["checkout_url" => $checkout_url]);
exit;
