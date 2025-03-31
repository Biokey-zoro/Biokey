<?php
require 'gen_functions/config.php'; // Database connection

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_code'], $_POST['new_password'])) {
    $entered_code = $_POST['reset_code'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    $gmail = $_SESSION['gmail'] ?? '';

    // Validate code
    $stmt = $pdo->prepare("SELECT reset_code FROM user_tbl WHERE gmail = ?");
    $stmt->execute([$gmail]);
    $user = $stmt->fetch();

    if ($user && $user['reset_code'] == $entered_code) {
        // Update password
        $stmt = $pdo->prepare("UPDATE user_tbl SET password = ?, reset_code = NULL WHERE gmail = ?");
        $stmt->execute([$new_password, $gmail]);

        session_destroy();
        header("Location: login.php?success=Password reset successful!");
        exit();
    } else {
        $message = "❌ Invalid reset code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <form method="POST">
        <input type="text" name="reset_code" placeholder="Enter Reset Code" required>
        <input type="password" name="new_password" placeholder="Enter New Password" required>
        <button type="submit">Reset Password</button>
    </form>
    <p><?php echo $message; ?></p>
</body>
</html>
