<?php
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["subscribe"])) {
    $_SESSION["selected_plan"] = [
        "duration" => $_POST["duration"] ?? "N/A",
        "description" => $_POST["description"] ?? "N/A",
        "price" => $_POST["price"] ?? "N/A",
        "perks" => $_POST["perks"] ?? [] // Store perks as an array
    ];
}
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['register'])) {
        // Registration Process
        $firstName = $_POST['firstName'] ?? null;
        $middleName = $_POST['middleName'] ?? '';
        $lastName = $_POST['lastName'] ?? null;
        $gmail = $_POST['gmail'] ?? null;
        $password = $_POST['password'] ?? null;
        $confirmPassword = $_POST['confirmPassword'] ?? null;
        $role = 'user';

        if (!$firstName || !$lastName || !$gmail || !$password || !$confirmPassword) {
            $message = "❌ Please fill in all required fields.";
            $messageType = "error";
        } elseif ($password !== $confirmPassword) {
            $message = "❌ Passwords do not match.";
            $messageType = "error";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("SELECT * FROM user_tbl WHERE gmail = ?");
            $stmt->execute([$gmail]);

            if ($stmt->rowCount() > 0) {
                $message = "❌ This Gmail is already registered.";
                $messageType = "error";
            } else {
                $stmt = $pdo->prepare("INSERT INTO user_tbl (first_name, middle_name, last_name, gmail, password, role) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$firstName, $middleName, $lastName, $gmail, $hashedPassword, $role])) {
                    $message = "✅ Registration successful! You can now log in.";
                    $messageType = "success";
                } else {
                    $errorInfo = $stmt->errorInfo();
                    $message = "❌ Registration failed: " . $errorInfo[2];
                    $messageType = "error";
                }
            }
        }
    } elseif (isset($_POST['login'])) {
        // Login Process
        $gmail = $_POST['gmail'] ?? null;
        $password = $_POST['password'] ?? null;

        if (!$gmail || !$password) {
            $message = "❌ Please enter your email and password.";
            $messageType = "error";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM user_tbl WHERE gmail = ?");
            $stmt->execute([$gmail]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {



                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['user_role'] = $user['role'];

                if ($_SESSION['user_role'] == "admin") {
                    header("Location: admin_pages/index.php");
                    exit();
                } elseif ($_SESSION['user_role'] == "user") {
                    header("Location: user_pages/index.php");
                    exit();
                }
            } else {
                $message = "❌ Invalid email or password.";
                $messageType = "error";
            }
        }
    }
}





$selected_plan = $_SESSION["selected_plan"] ?? null;

$darkMode = isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'enabled' ? 'dark-mode' : '';
$isDarkMode = $darkMode === 'dark-mode' ? 'checked' : ''; // Sync checkbox state
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/subscription.css">
    <link rel="stylesheet" href="css/./subscription.css">
    <script src="https://kit.fontawesome.com/77ff7e1fdc.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="resources/favicon.png" type="resources/png">

    <title>Subscription Info</title>
</head>

<body class="<?php echo $darkMode; ?>">

    <header class="header">
        <div class="logo">
            <img id="logoImage" src="resources/logo.png" alt="LOGO">
            <span class="logo-text">BIOKEY</span>
        </div>

        <nav>
            <ul id="menuList">
                <li><a href="./index.php">Home</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#plans">Subscribe</a></li>
                <li><a href="contact_us2.php">Contact</a></li>
            </ul>
        </nav>

        <div class="auth-buttons">
            <a href="#" id="signInButton" class="sign-in">Sign In</a>
            <a href="./resources/BioKey.apk" class="download-apk" download>Download APK</a>
            <label class="switch">
                <input class="inpt" type="checkbox" <?php echo $isDarkMode; ?>>
                <span class="slider"></span>
            </label>
        </div>
    </header>

    <script>
        function toggleMenu() {
            var menu = document.querySelector("nav ul");
            menu.classList.toggle("show");
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleSwitch = document.querySelector(".inpt");
            const body = document.body;
            const logoImage = document.getElementById("logoImage"); // Ensure you have this ID in your HTML

            // Sync checkbox with dark mode state
            toggleSwitch.checked = body.classList.contains("dark-mode");

            // Update logo based on the current mode
            updateLogo(toggleSwitch.checked);

            toggleSwitch.addEventListener("change", function() {
                let darkModeEnabled = toggleSwitch.checked;
                body.classList.toggle("dark-mode", darkModeEnabled);
                updateLogo(darkModeEnabled); // Call updateLogo on toggle

                // Send dark mode preference to PHP using fetch
                fetch("dark_mode.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "dark_mode=" + (darkModeEnabled ? "enabled" : "disabled"),
                    credentials: "include" // Ensures cookies persist across pages
                });
            });

            function updateLogo(darkModeEnabled) {
                if (darkModeEnabled) {
                    logoImage.src = "resources/logonidark.png"; // Path to dark mode logo
                } else {
                    logoImage.src = "resources/logo.png"; // Path to light mode logo
                }

                // Optional: Add error handling for image loading
                logoImage.onerror = function() {
                    console.error("Failed to load image: " + logoImage.src);
                    logoImage.src = "resources/logo.png"; // Fallback image
                };
            }
        });
    </script>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Get modal elements
            const loginModal = document.getElementById("loginModal");
            const signUpModal = document.getElementById("signUpModal");

            // Get buttons
            const signInButton = document.getElementById("signInButton");
            const signUpButton = document.getElementById("signUpButton");

            // Get close buttons
            const closeButtons = document.querySelectorAll(".close");

            // Open login modal when Sign In is clicked
            if (signInButton) {
                signInButton.addEventListener("click", function() {
                    loginModal.style.display = "block";
                });
            }

            // Open sign-up modal when Sign Up is clicked
            if (signUpButton) {
                signUpButton.addEventListener("click", function() {
                    signUpModal.style.display = "block";
                });
            }

            // Close modals when close button is clicked
            closeButtons.forEach(button => {
                button.addEventListener("click", function() {
                    loginModal.style.display = "none";
                    signUpModal.style.display = "none";
                });
            });

            // Close modals when clicking outside of them
            window.addEventListener("click", function(event) {
                if (event.target === loginModal) {
                    loginModal.style.display = "none";
                }
                if (event.target === signUpModal) {
                    signUpModal.style.display = "none";
                }
            });
        });
    </script>

    <!-- Your Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>

            <form class="form" action="" method="POST">
                <h1>LOGIN</h1>

                <div class="flex-column">
                    <label>Email</label>
                </div>
                <div class="inputForm">
                    <i class="fa-solid fa-at fa-sm" style="color: #1b2c48;"></i>
                    <input type="email" class="input" name="gmail" placeholder="Enter your Email" required>
                </div>

                <div class="flex-column">
                    <label>Password</label>
                </div>
                <div class="inputForm">
                    <i class="fa-solid fa-lock fa-sm" style="color: #1b2f48;"></i>
                    <input type="password" class="input" name="password" placeholder="Enter your Password" required>
                </div>

                <div class="flex-row">
                    <div>
                        <input type="checkbox">
                        <label>Remember me</label>
                    </div>
                    <span class="span">Forgot password?</span>
                </div>

                <button type="submit" class="button-submit" name="login">Sign In</button>
                <p class="p">Don't have an account? <span class="span" id="signUpButton">Sign Up</span></p>
            </form>
        </div>
    </div>

    <!-- Sign Up Modal -->
    <div id="signUpModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>

            <form class="form" action="" method="POST">
                <h1>CREATE ACCOUNT</h1>
                <input type="hidden" name="register" value="1">

                <div class="input-group">
                    <div class="flex-column">
                        <label>First Name</label>
                        <div class="inputForm">
                            <i class="fa-solid fa-user fa-sm" style="color: #162c48;"></i>
                            <input type="text" class="input" name="firstName" placeholder="First Name" required>
                        </div>
                    </div>

                    <div class="flex-column">
                        <label>Last Name</label>
                        <div class="inputForm">
                            <i class="fa-solid fa-user fa-sm" style="color: #162c48;"></i>
                            <input type="text" class="input" name="lastName" placeholder="Last Name" required>
                        </div>
                    </div>
                </div>

                <div class="flex-column">
                    <label>Email</label>
                </div>
                <div class="inputForm">
                    <i class="fa-solid fa-at fa-sm" style="color: #1b2c48;"></i>
                    <input type="email" class="input" name="gmail" placeholder="Email" required>
                </div>

                <div class="flex-column">
                    <label>Password</label>
                </div>
                <div class="inputForm">
                    <i class="fa-solid fa-lock fa-sm" style="color: #1b2f48;"></i>
                    <input type="password" class="input" name="password" placeholder="Password" required>
                </div>

                <div class="flex-column">
                    <label>Confirm Password</label>
                </div>
                <div class="inputForm">
                    <i class="fa-solid fa-lock fa-sm" style="color: #1b2f48;"></i>
                    <input type="password" class="input" name="confirmPassword" placeholder="Confirm Password" required>
                </div>

                <div class="flex-row">
                    <div>
                        <input type="checkbox" required>
                        <label>I accept terms of Service</label>
                    </div>
                </div>

                <button type="submit" class="button-submit">Sign Up</button>
                <p class="p">Already have an account? <span class="span" id="signInButton">Sign In</span></p>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const loginModal = document.getElementById("loginModal");
            const signUpModal = document.getElementById("signUpModal");
            const signUpBtn = document.getElementById("signUpButton");
            const signInBtn = document.getElementById("signInButton");
            const closeButtons = document.querySelectorAll(".close");

            loginModal.style.display = "none";
            signUpModal.style.display = "none";

            signUpBtn.addEventListener("click", function(event) {
                event.preventDefault();
                loginModal.classList.remove("show");
                setTimeout(() => {
                    loginModal.style.display = "none";
                    signUpModal.style.display = "flex";
                    signUpModal.classList.add("show");
                }, 200);
            });

            signInBtn.addEventListener("click", function(event) {
                event.preventDefault();
                signUpModal.classList.remove("show");
                setTimeout(() => {
                    signUpModal.style.display = "none";
                    loginModal.style.display = "flex";
                    loginModal.classList.add("show");
                }, 200);
            });

            closeButtons.forEach(button => {
                button.addEventListener("click", function() {
                    loginModal.style.display = "none";
                    signUpModal.style.display = "none";
                });
            });

            window.addEventListener("click", function(event) {
                if (event.target === loginModal) {
                    loginModal.style.display = "none";
                }
                if (event.target === signUpModal) {
                    signUpModal.style.display = "none";
                }
            });
        });
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const dropdownBtn = document.getElementById("dropdownBtn");
            const dropdownMenu = document.getElementById("dropdownMenu");

            dropdownBtn.addEventListener("click", function(event) {
                dropdownMenu.classList.toggle("show");
                event.stopPropagation();
            });

            document.addEventListener("click", function(event) {
                if (!dropdownBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.remove("show");
                }
            });
        });
    </script>

    <div class="billing-container">
        <!-- Left Section: Customer Info -->
        <div class="customer-info">
            <h2>
                <a href="javascript:history.back()" class="back-arrow">
                    <i class="fa-solid fa-arrow-left" style="color: #000000;"></i>
                </a>
                Billing
            </h2>


            <form action="#" method="POST">
                <h3 class="information">Customers Information</h3>
                <div class="input-group">
                    <input type="text" id="firstName" name="firstName" placeholder="First Name" required>
                    <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>
                </div>

                <div class="input-group">
                    <select id="country-code">
                        <option value="+63">🇵🇭 +63</option>
                        <option value="+1">🇺🇸 +1</option>
                        <option value="+44">🇬🇧 +44</option>
                        <option value="+61">🇦🇺 +61</option>
                        <option value="+44">🇧🇷 +55</option>
                        <option value="+44">🇨🇦 +1</option>
                    </select>
                    <input type="text" id="mobile" name="mobile" placeholder="Phone Number" required>
                </div>

                <div class="input-group">
                    <input type="email" id="email" name="email" placeholder="Email Address" required>
                </div>

                <div class="input-group">
                    <select id="Country">
                        <option value="PH">Philippines</option>
                        <option value="BN">Brunei</option>
                        <option value="KH">Cambodia</option>
                        <option value="ID">Indonesia</option>
                        <option value="LA">Laos</option>
                        <option value="MY">Malaysia</option>
                        <option value="MM">Myanmar</option>
                        <option value="SG">Singapore</option>
                        <option value="TH">Thailand</option>
                        <option value="VN">Vietnam</option>
                    </select>

                    <select id="City">
                        <option value="quezon">Quezon City</option>
                        <option value="manila">Manila</option>
                        <option value="makati">Makati</option>
                        <option value="taguig">Taguig</option>
                        <option value="caloocan">Caloocan</option>
                        <option value="malabon">Malabon</option>
                    </select>
                </div>


                <div class="input-group">
                    <input type="text" id="address" name="address" placeholder="Address" required>
                    <input type="text" id="postal" name="postal" placeholder="Postal" required>
                </div>
            </form>
        </div>


        <div class="plan-summary">
            <h3 class="plan-header">Subscription</h3>
            <div class="plan">
                <div class="plan-details">
                    <?php if ($selected_plan): ?>
                        <div class="card-container">
                            <div class="card">
                                <h3 class="duration"><?= htmlspecialchars($selected_plan["duration"]) ?></h3>
                                <p class="description"><?= htmlspecialchars($selected_plan["description"]) ?></p>
                                <p class="price">$<?= htmlspecialchars($selected_plan["price"]) ?></p>
                                <ul class="perks">
                                    <?php foreach ($selected_plan["perks"] as $perk): ?>
                                        <li><?= htmlspecialchars($perk) ?></li>
                                    <?php endforeach; ?>
                                </ul>

                            </div>
                        </div>
                    <?php else: ?>
                        <p class="selected">No plan selected.</p>
                    <?php endif; ?>
                </div>
            </div>

            <form action="checkout.php" method="POST">
                <input type="hidden" name="duration" value="<?= htmlspecialchars($selected_plan["duration"]) ?>">
                <input type="hidden" name="price" value="<?= htmlspecialchars($selected_plan["price"]) ?>">
                <button type="submit" class="payment-btn">Proceed to Payment</button>
            </form>


            <div class="terms">
                <input type="checkbox" required>
                <span>By confirming the order, I accept the <a href="#">terms of the user agreement</a>.</span>
            </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            const paymentBtn = document.querySelector(".payment-btn");
            const termsCheckbox = document.querySelector(".terms input");

            function validateForm() {
                let isValid = true;
                let emptyFields = [];
                let inputs = form.querySelectorAll("input[required]");

                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.style.border = "2px solid red";
                        input.setAttribute("title", "This field is required");
                        emptyFields.push(input.placeholder);
                        isValid = false;
                    } else {
                        input.style.border = "1px solid #ccc";
                        input.removeAttribute("title");
                    }
                });

                if (!termsCheckbox.checked) {
                    isValid = false;
                }

                if (!isValid) {
                    let message = emptyFields.length ?
                        `Please fill in the following fields: <b>${emptyFields.join(", ")}</b>.` :
                        "You must agree to the terms and conditions.";

                    Swal.fire({
                        icon: "error",
                        title: "Oops!",
                        html: message,
                        confirmButtonColor: "#6C63FF",
                    });
                }

                return isValid;
            }

            paymentBtn.addEventListener("click", function(event) {
                if (!validateForm()) {
                    event.preventDefault();
                } else {
                    form.submit();
                }
            });
        });
    </script>


</body>

</html>