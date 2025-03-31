<?php
//session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["subscribe"])) {
    $_SESSION["selected_plan"] = [
        "duration" => $_POST["duration"] ?? "N/A",
        "description" => $_POST["description"] ?? "N/A",
        "price" => $_POST["price"] ?? "N/A",
        "perks" => $_POST["perks"] ?? [] // Store perks as an array
    ];
}



$selected_plan = $_SESSION["selected_plan"] ?? null;


// Dark Mode Handling
$darkMode = isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'enabled' ? 'dark-mode' : '';
$isDarkMode = $darkMode === 'dark-mode' ? 'checked' : ''; // Sync checkbox state
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://kit.fontawesome.com/77ff7e1fdc.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>



    <section class="hero" id="hero">
        <div class="hero-container">
            <div class="hero-image">
                <img src="../resources/safetyBoxGif.gif" alt="Locker System">
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
                        <h1><img class="imgcard" src="../resources/shield.png" alt="secure protection"></h1>
                        <h3>Secure Protection</h3>
                        <p>Users generate unique PINs for secure locker access.</p>
                        <span class="plus"><img src="../resources/plus.svg" alt=""></span>
                    </div>
                    <div class="feature-card">
                        <h1><img class="imgcard" src="../resources/admin.png" alt=""></h1>
                        <h3>Admin Management</h3>
                        <p>Easy locker assignments and PIN resets by admins.</p>
                        <span class="plus"><img src="../resources/plus.svg" alt=""></span>
                    </div>
                    <div class="feature-card">
                        <h1><img class="imgcard" src="../resources/locked.png" alt=""></h1>
                        <h3>Inactivity Auto-Lock</h3>
                        <p>Lockers lock automatically after being idle for a set time.</p>
                        <span class="plus"><img src="../resources/plus.svg" alt=""></span>
                    </div>
                    <div class="feature-card">
                        <h1><img class="imgcard" src="../resources/encryption.png" alt=""></h1>
                        <h3>Encrypted Data</h3>
                        <p>PIN codes are securely encrypted to prevent breaches.</p>
                        <span class="plus"><img src="../resources/plus.svg" alt=""></span>
                    </div>
                    <div class="feature-card">
                        <h1><img class="imgcard" src="../resources/pin.png" alt=""></h1>
                        <h3>Reset Options</h3>
                        <p>Hassle-free PIN reset for users who forget their codes.</p>
                        <span class="plus"><img src="../resources/plus.svg" alt=""></span>
                    </div>
                    <div class="feature-card">
                        <h1><img class="imgcard" src="../resources/board.png" alt=""></h1>
                        <h3>Microcontroller Automation</h3>
                        <p>Arduino-controlled locking/unlocking processes.</p>
                        <span class="plus"><img src="../resources/plus.svg" alt=""></span>
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
                    <img src="../resources/mobileLogin.png" alt="Register Icon">
                </div>
                <div class="step-number">01</div>
                <div class="step-content">
                    <h3 class="step-h3">Register and create a <br> unique PIN</h3>
                    <p class="step-p">Users can quickly register and <br>create a unique PIN for secure access.</p>
                </div>
            </div>

            <div class="step">
                <div class="step-img">
                    <img src="../resources/security.png" alt="Unlock Locker Icon">
                </div>
                <div class="step-number">02</div>
                <div class="step-content">
                    <h3 class="step-h3">Enter your PIN to <br> unlock your locker</h3>
                    <p class="step-p">Users can enter their PIN <br> to easily unlock their locker.</p>
                </div>
            </div>

            <div class="step">
                <div class="step-img">
                    <img src="../resources/reset.png" alt="Reset PIN Icon">
                </div>
                <div class="step-number">03</div>
                <div class="step-content">
                    <h3 class="step-h3">Reset PIN if forgotten via <br> the admin or interface</h3>
                    <p class="step-p">Users can reset their PIN if <br> forgotten through the admin <br>or interface.</p>
                </div>
            </div>

            <div class="step">
                <div class="step-img">
                    <img src="../resources/fingerprint.png" alt="Biometric Registration Icon">
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
                <img src="../resources/technology.png" alt="Secure">
            </div>
        </div>

        <div class="about-features">
            <div class="about-feature">
                <div class="about-feature-icon">
                    <img src="../resources/microchipSolid.svg" alt="Secure">
                </div>
                <div class="about-feature-text">
                    <h4>Arduino Microcontroller Integration</h4>
                    <p>Users can reset their PIN if forgotten through the admin or interface.</p>
                </div>
            </div>
            <div class="about-feature">
                <div class="about-feature-icon">
                    <img src="../resources/halvedShield.svg" alt="Encryption">
                </div>
                <div class="about-feature-text">
                    <h4>PIN Encryption</h4>
                    <p>Ensures the highest level of data security.</p>
                </div>
            </div>
            <div class="about-feature">
                <div class="about-feature-icon">
                    <img src="../resources/plug.svg" alt="Backup">
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

                        <button type="button" class="btn btn-primary" onclick="location.href='billing.php'">Subscribe</button>
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

                        <button type="button" class="btn btn-primary" onclick="location.href='billing.php'">Subscribe</button>
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

                        <button type="button" class="btn btn-primary" onclick="location.href='billing.php'">Subscribe</button>
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
            <div class="logo-footer" ><img src="../resources/logo.png"></div>
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



</body>

</html>