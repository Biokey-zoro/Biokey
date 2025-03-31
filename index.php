<?php
session_start(); // ✅ Start session first

require 'gen_functions/config.php'; // ✅ Load config after session start

// Check if dark_mode cookie exists
$darkMode = isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'enabled' ? 'dark-mode' : '';
$isDarkMode = $darkMode === 'dark-mode' ? 'checked' : ''; // Sync checkbox state

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
                // ✅ Set session variables after verifying the user
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['user_role'] = $user['role'];

                // ✅ Debugging - Check if session is really set
                if (!isset($_SESSION['user_id'])) {
                    die(json_encode(["status" => "error", "message" => "Session user_id not set properly"]));
                }

                // Redirect based on role
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
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>BioKey | Landing Page</title>
    <link rel="stylesheet" href="./css/style.css">
    <script src="https://kit.fontawesome.com/77ff7e1fdc.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="resources/favicon.png" type="resources/png">



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

        <div class="menu-icon1" onclick="toggleMenu()">
            <i class="fa-solid fa-bars"></i>
        </div>

        <div class="menu-icon" onclick="toggleMenu()">
            <i class="fas fa-bars"></i>
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
                    <div class="remember-me">
                        <input type="checkbox">
                        <label for="remember">Remember me</label>
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

    <section class="hero" id="hero">
        <div class="hero-container">
            <div class="hero-image">
                <img src="resources/safetyBoxGif.gif" alt="Locker System">
            </div>
            <div class="hero-content">
                <h1>Secure and Convenient <br> <span>Locker System</span></h1>
                <p>Say goodbye to keys and hello to hassle-free storage solutions.</p>
                <div class="cta-buttons">
                    <a href="#plans" class="subscription">See Plans <i class="fa-solid fa-circle-chevron-right fa-lg"></i></a>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="features-section">
        <div class="features-container">
            <!-- Features Info -->
            <div class="features-info">
                <h2>FEATURES</h2>
                <p>Our lockers are designed to provide a safe, <br>organized, and convenient storage experience.</p>
            </div>

            <div class="slider-controls">
                <button class="prev-btn" onclick="moveSlide(-1)"><i class="fa-solid fa-chevron-left fa-sm" style="color: #213555;"></i></button>
                <button class="next-btn" onclick="moveSlide(1)"><i class="fa-solid fa-chevron-right fa-sm" style="color: #213555;"></i></button>
            </div>
            <div class="features-slider-wrapper">
                <div class="features-slider">
                    <div class="feature-card">
                        <h1><img class="imgcard" src="./resources/shield.png" alt="secure protection"></h1>
                        <h3>Secure Protection</h3>
                        <p>Users generate unique PINs for secure locker access.</p>
                        <span class="plus"><img src="resources/plus.svg" alt=""></span>
                    </div>
                    <div class="feature-card">
                        <h1><img class="imgcard" src="./resources/admin.png" alt=""></h1>
                        <h3>Admin Management</h3>
                        <p>Easy locker assignments and PIN resets by admins.</p>
                        <span class="plus"><img src="resources/plus.svg" alt=""></span>
                    </div>
                    <div class="feature-card">
                        <h1><img class="imgcard" src="./resources/locked.png" alt=""></h1>
                        <h3>Inactivity Auto-Lock</h3>
                        <p>Lockers lock automatically after being idle for a set time.</p>
                        <span class="plus"><img src="resources/plus.svg" alt=""></span>
                    </div>
                    <div class="feature-card">
                        <h1><img class="imgcard" src="./resources/encryption.png" alt=""></h1>
                        <h3>Encrypted Data</h3>
                        <p>PIN codes are securely encrypted to prevent breaches.</p>
                        <span class="plus"><img src="resources/plus.svg" alt=""></span>
                    </div>
                    <div class="feature-card">
                        <h1><img class="imgcard" src="./resources/pin.png" alt=""></h1>
                        <h3>Reset Options</h3>
                        <p>Hassle-free PIN reset for users who forget their codes.</p>
                        <span class="plus"><img src="resources/plus.svg" alt=""></span>
                    </div>
                    <div class="feature-card">
                        <h1><img class="imgcard" src="./resources/board.png" alt=""></h1>
                        <h3>Microcontroller Automation</h3>
                        <p>Arduino-controlled locking/unlocking processes.</p>
                        <span class="plus"><img src="resources/plus.svg" alt=""></span>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script>
        let currentIndex = 0;
        const totalSlides = 1.5;
        const slider = document.querySelector(".features-slider");

        function moveSlide(direction) {
            currentIndex += direction;

            if (currentIndex < 0) currentIndex = 0;
            if (currentIndex > totalSlides) currentIndex = totalSlides;

            slider.style.transform = `translateX(${-currentIndex * (200 + 15)}px)`;
        }
    </script>


    <section class="how-it-works" id="how-it-works">
        <h2 class="header-hiw">How It Works?</h2>
        <div class="timeline">
            <div class="step">
                <div class="step-img">
                    <img src="./resources/mobileLogin.png" alt="Register Icon">
                </div>
                <div class="step-number">01</div>
                <div class="step-content">
                    <h3 class="step-h3">Register and create a <br> unique PIN</h3>
                    <p class="step-p">Users can quickly register and <br>create a unique PIN for secure access.</p>
                </div>
            </div>

            <div class="step">
                <div class="step-img">
                    <img src="./resources/security.png" alt="Unlock Locker Icon">
                </div>
                <div class="step-number">02</div>
                <div class="step-content">
                    <h3 class="step-h3">Enter your PIN to <br> unlock your locker</h3>
                    <p class="step-p">Users can enter their PIN <br> to easily unlock their locker.</p>
                </div>
            </div>

            <div class="step">
                <div class="step-img">
                    <img src="./resources/reset.png" alt="Reset PIN Icon">
                </div>
                <div class="step-number">03</div>
                <div class="step-content">
                    <h3 class="step-h3">Reset PIN if forgotten via <br> the admin or interface</h3>
                    <p class="step-p">Users can reset their PIN if <br> forgotten through the admin or interface.</p>
                </div>
            </div>

            <div class="step">
                <div class="step-img">
                    <img src="./resources/fingerprint.png" alt="Biometric Registration Icon">
                </div>
                <div class="step-number">04</div>
                <div class="step-content">
                    <h3 class="step-h3">Biometric Registration <br> After Login</h3>
                    <p class="step-p">Users can register their <br> biometric data after logging <br> in for enhanced security.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- about -->
    <section class="about-technology">
        <h3>About the Technology</h3>

        <div class="about-content">
            <div class="about-text">
                <p>
                    This Modernized Locker System is a secure and user-friendly storage solution using microcontrollers like Arduino for
                    automated locking and unlocking. Users create unique encrypted
                    PINs for access, while an admin interface manages locker assignments
                    and PIN resets. It enhances convenience by eliminating physical keys
                    but requires electricity and lacks secondary authentication methods.
                    This system prioritizes simplicity, security, and efficiency for modern storage needs.
                </p>
            </div>
            <div class="about-img">
                <img src="resources/technology.png" alt="Secure">
            </div>
        </div>

        <div class="about-features">
            <div class="about-feature">
                <div class="about-feature-icon">
                    <img src="resources/microchipSolid.svg" alt="Secure">
                </div>
                <div class="about-feature-text">
                    <h4>Arduino Microcontroller Integration</h4>
                    <p>Users can reset their PIN if forgotten through the admin or interface.</p>
                </div>
            </div>
            <div class="about-feature">
                <div class="about-feature-icon">
                    <img src="resources/halvedShield.svg" alt="Encryption">
                </div>
                <div class="about-feature-text">
                    <h4>PIN Encryption</h4>
                    <p>Ensures the highest level of data security.</p>
                </div>
            </div>
            <div class="about-feature">
                <div class="about-feature-icon">
                    <img src="resources/plug.svg" alt="Backup">
                </div>
                <div class="about-feature-text">
                    <h4>Backup Plans</h4>
                    <p>Addressing limitations like power outages with optional UPS.</p>
                </div>
            </div>
        </div>
    </section>


    <section class="pricing" id="plans">
        <div class="title">
            <h2>Ready to get Started?</h2>
            <p class="info">Select the perfect locker plan that fits your needs. <br> Enjoy secure, convenient, and flexible storage options tailored just for you.</p>
        </div>

        <div class="cards-container">
            <div class="inner-container">
                <div class="card">
                    <h3 class="plan-name">Weekly</h3>
                    <p class="description">Perfect for short-term users who need flexibility.</p>
                    <p class="price">$10 <span>/ week</span></p>
                    <form method="POST" action="subscription.php">
                        <input type="hidden" name="duration" value="Weekly">
                        <input type="hidden" name="description" value="Perfect for short-term users who need flexibility.">
                        <input type="hidden" name="price" value="10">

                        <input type="hidden" name="perks[]" value="24/7 Access">
                        <input type="hidden" name="perks[]" value="No long-term commitment">
                        <input type="hidden" name="perks[]" value="Basic customer support">

                        <button type="button" class="btn btn-primary" onclick="location.href='subscriptionforguest.php'">Subscribe</button>
                    </form>
                    <ul class="perks">
                        <li>24/7 Access</li>
                        <li>No long-term commitment</li>
                        <li>Basic customer support</li>
                    </ul>
                </div>
            </div>

            <div class="inner-container">
                <div class="card">
                    <h3 class="plan-name">Monthly</h3>
                    <p class="description">Great for regular users looking for a cost-effective plan with full access.</p>
                    <p class="price">$30 <span>/ month</span></p>
                    <form method="POST" action="subscription.php">
                        <input type="hidden" name="duration" value="Monthly">
                        <input type="hidden" name="description" value="Great for regular users looking for a cost-effective plan with full access.">
                        <input type="hidden" name="price" value="30">

                        <input type="hidden" name="perks[]" value="24/7 Access">
                        <input type="hidden" name="perks[]" value="Priority support">
                        <input type="hidden" name="perks[]" value="Discount on additional services">
                        <input type="hidden" name="perks[]" value="Free upgrade opportunities">
                        <button type="button" class="btn btn-primary" onclick="location.href='subscriptionforguest.php'">Subscribe</button>
                    </form>
                    <ul class="perks">
                        <li>24/7 Access</li>
                        <li>Priority support</li>
                        <li>Discount on additional services</li>
                        <li>Free upgrade opportunities</li>
                    </ul>
                </div>
            </div>

            <div class="inner-container">
                <div class="card">
                    <h3 class="plan-name">Yearly</h3>
                    <p class="description">Best value! Get unlimited access for a whole year at a discounted rate.</p>
                    <p class="price">$100 <span>/ year</span></p>
                    <form method="POST" action="subscription.php">
                        <input type="hidden" name="duration" value="Yearly">
                        <input type="hidden" name="description" value="Best value! Get unlimited access for a whole year at a discounted rate.">
                        <input type="hidden" name="price" value="100">

                        <input type="hidden" name="perks[]" value="24/7 Access">
                        <input type="hidden" name="perks[]" value="VIP priority support">
                        <input type="hidden" name="perks[]" value="Exclusive discounts & offers">
                        <input type="hidden" name="perks[]" value="Free premium upgrades">
                        <input type="hidden" name="perks[]" value="Complimentary bonus services">
                        <button type="button" class="btn btn-primary" onclick="location.href='subscriptionforguest.php'">Subscribe</button>
                    </form>
                    <ul class="perks">
                        <li>24/7 Access</li>
                        <li>VIP priority support</li>
                        <li>Exclusive discounts & offers</li>
                        <li>Free premium upgrades</li>
                        <li>Complimentary bonus services</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-container">
            <div class="footer-left">
            <div class="logo-footer" ><img src="resources/logo.png"></div>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            <div class="footer-links">
                <div class="footer-column">
                    <h4>About</h4>
                    <a href="#">About</a>
                    <a href="#">Services</a>
                    <a href="#">Careers</a>
                </div>
                <div class="footer-column">
                    <h4>Resources</h4>
                    <a href="#">Help</a>
                    <a href="#">Terms</a>
                    <a href="#">Privacy</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>©2025. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.body.style.opacity = "1";

            document.querySelectorAll("a[href]").forEach(link => {
                link.addEventListener("click", function(event) {
                    const href = this.getAttribute("href");

                    if (href.startsWith("#") || href.startsWith("http")) return;

                    event.preventDefault();

                    document.body.classList.add("fade-out");

                    setTimeout(() => {
                        window.location.href = href;
                    }, 500);
                });
            });
        });
    </script>

    <script>
        let menuList = document.getElementById("menuList");

        function toggleMenu() {
            menuList.classList.toggle("open");
        }

        // Close menu when clicking outside
        document.addEventListener("click", function(event) {
            let menuIcon = document.querySelector(".menu-icon");
            if (!menuIcon.contains(event.target) && !menuList.contains(event.target)) {
                menuList.classList.remove("open");
            }
        });
    </script>
</body>

</html>