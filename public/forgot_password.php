<?php
require 'gen_functions/config.php'; // Database connection
require 'vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gmail'])) {
    $gmail = trim($_POST['gmail']);

    // Check if email exists
    $stmt = $pdo->prepare("SELECT user_id FROM user_tbl WHERE gmail = ?");
    $stmt->execute([$gmail]);
    $user = $stmt->fetch();

    if ($user) {
        $reset_code = rand(100000, 999999); // 6-digit code
        $_SESSION['reset_code'] = $reset_code;
        $_SESSION['gmail'] = $gmail;

        // Save code in the database (Ensure reset_code column exists)
        $stmt = $pdo->prepare("UPDATE user_tbl SET reset_code = ? WHERE gmail = ?");
        $stmt->execute([$reset_code, $gmail]);

        // Send email
        $mail = new PHPMailer(true);
          try {
              $mail->isSMTP();
              $mail->Host       = 'smtp.gmail.com';
              $mail->SMTPAuth   = true;
              $mail->Username   = 'rodriguezrenzel.bsit@gmail.com';  // Change to your Gmail
              $mail->Password   = 'anki twha utzb hoil'; // Use the App Password (Fix 2)
              $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
              $mail->Port       = 587;

              $mail->setFrom('LockerSystem@gmail.com', 'Basta Locker');
              $mail->addAddress($gmail);
              $mail->isHTML(true);
              $mail->Subject = 'Password Reset Code';
              $mail->Body    = "<p>Your password reset code is: <strong>$reset_code</strong></p>";

              $mail->send();
              $message = "✅ Reset code sent to your email!";

              header("Location: reset_password.php");
              exit();

          } catch (Exception $e) {
              $message = "❌ Email failed: {$mail->ErrorInfo}";
          }

    } else {
        $message = "❌ Email not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>
    <form method="POST">
        <input type="email" name="gmail" placeholder="Enter your email" required>
        <button type="submit">Send Reset Code</button>
    </form>
    <p><?php echo $message; ?></p>
</body>
</html>
