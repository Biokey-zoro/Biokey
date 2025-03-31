<?php
session_start();  // Start the session to get user_id

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User is not logged in.";
    exit();
}

$user_id = $_SESSION['user_id'];  // Get user_id from session

require_once '../gen_functions/config.php';  // Include database connection file

// Check if the image is uploaded
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $image = $_FILES['image']['tmp_name'];
    $imageData = addslashes(file_get_contents($image));

    // Prepare SQL statement to insert the image along with the user_id
    $stmt = $conn->prepare("INSERT INTO faces (user_id, image) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $imageData);  // "is" means INT and STRING (image as binary)
    
    if ($stmt->execute()) {
        echo "Image uploaded and saved successfully!";
    } else {
        echo "Error uploading image: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "No image uploaded.";
}

$conn->close();
?>
