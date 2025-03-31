<?php
session_start();
require ('../gen_functions/config.php');

if (!isset($_SESSION['user_id'])) {
    die(json_encode(["status" => "error", "message" => "User not logged in."]));
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if ($data['action'] === "register") {
    $challenge = base64_encode(random_bytes(32));
    $userId = base64_encode(random_bytes(16));

    echo json_encode([
        "status" => "success",
        "challenge" => $challenge,
        "rp" => ["name" => "BIOKEY Locker System"],
        "user" => [
            "id" => $userId,
            "name" => "user@example.com",
            "displayName" => "User"
        ],
        "pubKeyCredParams" => [
            ["type" => "public-key", "alg" => -7],   // ES256 (Elliptic Curve)
            ["type" => "public-key", "alg" => -257]  // RS256 (RSA)
        ],
        "timeout" => 60000
    ]);
    exit;
}

if ($data['action'] === "verify") {
    if (!isset($data['credentialId'], $data['publicKey'])) {
        die(json_encode(["status" => "error", "message" => "Invalid fingerprint data."]));
    }

    $credentialId = $data['credentialId'];
    $publicKey = $data['publicKey'];

    $query = "INSERT INTO webauthn_credentials (user_id, credential_id, public_key) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $user_id, $credentialId, $publicKey);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Fingerprint registered successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to register fingerprint."]);
    }
}
?>
