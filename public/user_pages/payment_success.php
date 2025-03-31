<?php
session_start();
require '../gen_functions/config.php';

$user_id = $_SESSION['user_id']; 

// Activate subscription in the database
$stmt = $pdo->prepare("UPDATE subscriptions_tbl SET status='Active' WHERE user_id=:user_id");
$stmt->execute(["user_id" => $user_id]);

echo "Payment successful! Your subscription is now active.";
?>
