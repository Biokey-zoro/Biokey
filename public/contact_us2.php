<?php

require 'gen_functions/config.php';

// Check if the dark_mode cookie exists and apply dark mode class
$darkMode = isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'enabled' ? 'dark-mode' : '';
$isDarkMode = $darkMode === 'dark-mode' ? 'checked' : ''; // Sync checkbox state
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="./css/contact-us.css">
    <script src="https://kit.fontawesome.com/77ff7e1fdc.js" crossorigin="anonymous"></script>
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
                <li><a href="./index.php #features">Features</a></li>
                <li><a href="./index.php #plans">Subscribe</a></li>
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




    <div class="hero">
        <div class="hero-content">
            <h1>Contacts</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus lacinia odio vitae vestibulum.</p>
        </div>
    </div>

    <div class="contact-info">
        <div class="info-text">
            <h2>We are always ready to help you and answer your questions</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, <br>sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            <div class="details">
                <div>
                    <h4>Call Center</h4>
                    <p>+63 XXX XXX XXXX</p>
                </div>
                <div>
                    <h4>Our Location</h4>
                    <p>Manila, Philippines</p>
                </div>
                <div>
                    <h4>Email</h4>
                    <p>lockersystem@mail.co</p>
                </div>
                <div>
                    <h4>Social Network</h4>
                    <p>[Social Icons Here]</p>
                </div>
            </div>
        </div>
        <div class="contact-form">
            <h3>Get in Touch</h3>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, <br>sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            <form>
                <input type="text" placeholder="Full Name" required>
                <input type="email" placeholder="Email" required>
                <input type="text" placeholder="Subject" required>
                <textarea placeholder="Message" required></textarea>
                <button type="submit">Send a Message</button>
            </form>
        </div>
    </div>

    <div class="map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3860.6292863302893!2d120.9921549!3d14.6534462!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b686dd24e859%3A0xe442b57504cbf05f!2sUniversity%20of%20Caloocan%20City%20-%20South%20Campus!5e0!3m2!1sen!2sph!4v1707072345678!5m2!1sen!2sph"
            width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <div class="logo-footer">LOGO</div>
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