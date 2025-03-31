<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="../css/contact-us.css">
    <script src="https://kit.fontawesome.com/77ff7e1fdc.js" crossorigin="anonymous"></script>

</head>

<body>

    <div class="hero-contact">
        <div class="hero-content-contact">
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
</body>

</html>