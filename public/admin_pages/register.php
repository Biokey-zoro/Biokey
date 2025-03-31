<?php
require '../gen_functions/config.php';  

$message = '';  // Initialize message variable for SweetAlert
$messageType = '';  // Initialize message type (success or error)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Registration process
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'] ?? '';
    $lastName = $_POST['lastName'];
    $gmail = $_POST['gmail'];
    $mobileNo = $_POST['mobileNo'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);  // Encrypt the password
    $role = 'admin';  // Default role as 'user'

    // Check if Gmail already exists
    $stmt = $pdo->prepare("SELECT * FROM user_tbl WHERE gmail = ?");
    $stmt->execute([$gmail]);

    if ($stmt->rowCount() > 0) {
        $message = "❌ This Gmail is already registered.";
        $messageType = "error";
    } else {
        // Insert user into the database
        $stmt = $pdo->prepare("INSERT INTO user_tbl (first_name, middle_name, last_name, gmail, mobile_no, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$firstName, $middleName, $lastName, $gmail, $mobileNo, $password, $role])) {
            $message = "✅ Registration successful! You can now log in.";
            $messageType = "success";
        } else {
            $errorInfo = $stmt->errorInfo();
            $message = "❌ Registration failed: " . $errorInfo[2];
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="adminstyle.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
</head>
<body>
<div class="container-register">
<div class="inner-container15">

        <div id="title">
            <i class="material-icons lock">lock</i> Register
        </div>
        <form action="register.php" method="POST">
            <!-- Email Input -->
            <div class="input">
                <input id="gmail" name="gmail" placeholder="Email" type="email" required class="validate" autocomplete="off">
            </div>

            <!-- First Name -->
            <div class="input">
                <input id="firstName" name="firstName" placeholder="First Name" type="text" required class="validate" autocomplete="off">
            </div>

            <!-- Middle Name (Optional) -->
            <div class="input">
                <input id="middleName" name="middleName" placeholder="Middle Name (Optional)" type="text" class="validate" autocomplete="off">
            </div>

            <!-- Last Name -->
            <div class="input">
                <input id="lastName" name="lastName" placeholder="Last Name" type="text" required class="validate" autocomplete="off">
            </div>

            <!-- Password -->
            <div class="input">
                <input id="password" name="password" placeholder="Password" type="password" required class="validate" autocomplete="off">
            </div>

            <!-- Hidden Inputs -->
            <input type="hidden" name="mobileNo" value="0000000000">
            <input type="hidden" name="role" value="user">

            <!-- Terms of Service Checkbox -->
            <div class="remember-me">
                <input type="checkbox" id="terms" required>
                <label for="terms" class="accept">I accept the Terms of Service</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn">Register</button>
        </form>

        <!-- Privacy Policy -->
        <div class="privacy">
            <a href="#">Privacy Policy</a>
        </div>

        <!-- Already Have an Account -->
        <div class="register">
            <a href="login.php"><button type="button" id="register-link">Log In here</button></a>
        </div>
    </div>
</div>
</div>

<?php if (!empty($message)): ?>
    <script>
        Swal.fire({
            icon: '<?php echo $messageType; ?>',
            title: '<?php echo $messageType === "success" ? "Success" : "Error"; ?>',
            text: '<?php echo $message; ?>'
        });
    </script>
    <?php endif; ?>
</body>
</html>
