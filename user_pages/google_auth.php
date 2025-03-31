<?php

require '../PHPGangsta/GoogleAuthenticator.php';  // Fix: Added missing semicolon

//session_start();

// Initialize Google Authenticator
$gAuth = new PHPGangsta_GoogleAuthenticator();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle registration and verification
    if (isset($_POST['register'])) {
        // Generate a secret for the user
        $secret = $gAuth->createSecret();  // Fix: Use createSecret() instead of generateSecret()
        $_SESSION['secret'] = $secret;

        // Generate a QR code URL for Google Authenticator
        $qrUrl = $gAuth->getQRCodeGoogleUrl('example_user', $secret, 'MyApp');

        echo "<h3>Scan this QR code with Google Authenticator:</h3>";
        echo "<img src='$qrUrl' alt='QR Code'><br>";
        echo "<p>Your secret key (backup): <strong>$secret</strong></p>";
        echo "<a href='index.php'>Back</a>";
    } elseif (isset($_POST['verify'])) {
        // Verify the entered code
        $userCode = $_POST['code'];
        $secret = $_SESSION['secret'] ?? '';

        if ($gAuth->verifyCode($secret, $userCode, 2)) {  // Fix: Added tolerance parameter (2 minutes)
            echo "<h3>✅ Verification successful!</h3>";
        } else {
            echo "<h3>❌ Invalid code. Please try again.</h3>";
        }
        echo "<a href='index.php'>Back</a>";
    }
} else {
    // Show the registration and verification forms
    ?>
    <h2>Google Authenticator 2FA Example</h2>
    <form method="POST">
        <button type="submit" name="register">Register (Generate QR Code)</button>
    </form>
    <hr>
    <h3>Verify 2FA Code</h3>
    <form method="POST">
        <label for="code">Enter 6-digit code from Google Authenticator:</label><br>
        <input type="text" name="code" id="code" required>
        <button type="submit" name="verify">Verify Code</button>
    </form>
    <?php
}
?>
