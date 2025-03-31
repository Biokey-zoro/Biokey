<?php

require '../gen_functions/config.php';

// Redirect to login if user is not logged in
// if (!isset($_SESSION['user_id'])) {

//     header("Location: ../index.php");
//     exit();
    
// }else{

//     if($_SESSION['role'] != 'admin'){
//         header("Location: ../index.php");
//         exit();
//     }
// }

// Session Variables
$first_name = $_SESSION['first_name'];
$user_role = $_SESSION['user_role'];

// Dark Mode Handling
$darkMode = isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'enabled' ? 'dark-mode' : '';
$isDarkMode = $darkMode === 'dark-mode' ? 'checked' : ''; // Sync checkbox state

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Locker System</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://kit.fontawesome.com/77ff7e1fdc.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="../resources/favicon.png" type="resources/png">
</head>

<body class="<?php echo $darkMode; ?>">

<header class="header">
        <div class="logo">
            <img id="logoImage" src="../resources/logo.png" alt="LOGO">
            <span class="logo-text">BIOKEY</span>
        </div>
        <nav>
            <ul>
                <li><a href="index.php?page=dashboard">Dashboard</a></li>
                <li><a href="index.php?page=locker">Locker</a></li>
                <li><a href="index.php?page=rfid">RFID</a></li>
                <li><a href="index.php?page=register">Register</a></li>
                <li><a href="generate_report.php?report=users" target="_blank">Generate User Report</a><li>
            </ul>
        </nav>

 <!-- Right Side Container for Notification & Profile -->
<div class="right-container">
    <!-- Notification Bell -->
    <div class="notification-container">
        <div class="notification-icon" id="notifBtn">
            <i class="fa-solid fa-bell"></i>
            <span id="notifCount" class="notif-badge">0</span>
        </div>

        <!-- Notification Dropdown -->
        <div class="notification-dropdown" id="notifDropdown">
            <div class="notif-header"><strong>New Subscriptions</strong></div>
            <table class="notif-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Locker No</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="notifTableBody">
                    <tr class="notif-placeholder">
                        <td colspan="3">No new subscriptions</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
            <!-- Profile Icon -->
            <div class="user-info">
                <div class="dropdown">
                    <i class="fa-solid fa-user fa-lg" id="dropdownBtn" style="cursor: pointer;"></i>
                    <div class="dropdown-content" id="dropdownMenu">
                        <span class="name">Welcome, <?php echo htmlspecialchars($first_name); ?></span>
                        <!-- Dark Mode Toggle -->
                        <div class="theme-toggle">
                            <span>Theme</span>
                            <label class="switch">
                                <input id="darkModeToggle" type="checkbox" class="inpt" <?php echo $isDarkMode; ?>>
                                <span class="slider">
                                    <i class="icon fa-solid fa-sun sun"> </i>
                                    <i class="icon fa-solid fa-moon moon"></i>
                                </span>
                            </label>
                        </div>

                        <a href="index.php?page=profile"><i class="fa-regular fa-user"></i> Profile Settings</a>
                        <a href="../logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

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
                    logoImage.src = "../resources/logonidark.png"; // Path to dark mode logo
                } else {
                    logoImage.src = "../resources/logo.png"; // Path to light mode logo
                }

                // Optional: Add error handling for image loading
                logoImage.onerror = function() {
                    console.error("Failed to load image: " + logoImage.src);
                    logoImage.src = "../resources/logo.png"; // Fallback image
                };
            }
        });
    </script>

    <!-- Dynamic Content Loading -->
    <main>
        <?php 
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
                switch ($page) {
                    case 'dashboard': include 'dashboard.php'; break;
                    case 'locker': include 'add_locker.php'; break;
                    case 'rfid': include 'lockers_control.php'; break;
                    case 'register': include 'register.php'; break;
                    default: include 'home.php'; break;
                }
            } else {
                include 'dashboard.php';
            }
        ?>
    </main>

    <script>
       /* document.addEventListener("DOMContentLoaded", function () {
        // 📌 Dropdown Menu Toggle
        const dropdownBtn = document.querySelector("#dropdownBtn");
        const dropdownMenu = document.querySelector("#dropdownMenu");

        if (dropdownBtn && dropdownMenu) {
            dropdownBtn.addEventListener("click", function (event) {
                dropdownMenu.classList.toggle("show");
                event.stopPropagation();
            });

            document.addEventListener("click", function (event) {
                if (!dropdownBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.remove("show");
                }
            });
        }*/

        // 📌 Dark Mode Toggle (Save to Cookie)
        const toggleSwitch = document.querySelector("#darkModeToggle");
        const body = document.body;

        if (toggleSwitch) {
            toggleSwitch.checked = body.classList.contains("dark-mode");

            toggleSwitch.addEventListener("change", function () {
                let darkModeEnabled = toggleSwitch.checked;
                body.classList.toggle("dark-mode", darkModeEnabled);

                document.cookie = `dark_mode=${darkModeEnabled ? "enabled" : "disabled"}; path=/; expires=Fri, 31 Dec 9999 23:59:59 GMT`;
            });
        }

        // 📌 Fetch Notifications and Display in Table
 /*       function fetchNotifications() {
            fetch("get_notifications.php")
                .then(response => response.json())
                .then(data => {
                    const notifTableBody = document.querySelector("#notifTableBody");
                    const notifCount = document.querySelector("#notifCount");

                    if (!notifTableBody || !notifCount) return;

                    notifTableBody.innerHTML = ""; // Clear table

                    if (data.error) {
                        notifTableBody.innerHTML = `<tr><td colspan="2" class="notif-placeholder">${data.error}</td></tr>`;
                        notifCount.style.display = "none";
                        return;
                    }

                    if (data.length === 0) {
                        notifTableBody.innerHTML = `<tr><td colspan="2" class="notif-placeholder">No new notifications</td></tr>`;
                        notifCount.style.display = "none";
                    } else {
                        data.forEach(notif => {
                            let row = document.createElement("tr");
                            row.innerHTML = `<td>${notif.message}</td><td>${formatDate(notif.created_at)}</td>`;
                            notifTableBody.appendChild(row);
                        });

                        notifCount.innerText = data.length;
                        notifCount.style.display = "inline-block";
                    }
                })
                .catch(error => {
                    console.error("Error fetching notifications:", error);
                    const notifTableBody = document.querySelector("#notifTableBody");
                    if (notifTableBody) {
                        notifTableBody.innerHTML = `<tr><td colspan="2" class="notif-placeholder">Failed to load notifications</td></tr>`;
                    }
                });
        }

        function formatDate(dateStr) {
            let date = new Date(dateStr);
            return date.toLocaleString();
        }

        // 📌 Notification Dropdown Toggle
        const notifBtn = document.querySelector("#notifBtn");
        const notifDropdown = document.querySelector("#notifDropdown");

        if (notifBtn && notifDropdown) {
            notifBtn.addEventListener("click", function (event) {
                notifDropdown.classList.toggle("show");
                event.stopPropagation();
            });

            document.addEventListener("click", function (event) {
                if (!notifBtn.contains(event.target) && !notifDropdown.contains(event.target)) {
                    notifDropdown.classList.remove("show");
                }
            });

            notifDropdown.addEventListener("click", function (event) {
                event.stopPropagation(); // Prevents closing when clicking inside
            });
        }

        // 📌 Fetch notifications every 30 seconds
        fetchNotifications();
        setInterval(fetchNotifications, 30000);
});*/

document.addEventListener("DOMContentLoaded", function () {
    const notifBtn = document.querySelector("#notifBtn");
    const notifDropdown = document.querySelector("#notifDropdown");

    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener("click", function (event) {
            notifDropdown.classList.toggle("show"); // ✅ Toggle dropdown visibility
            event.stopPropagation(); // ✅ Prevents event from bubbling up
        });

        document.addEventListener("click", function (event) {
            if (!notifBtn.contains(event.target) && !notifDropdown.contains(event.target)) {
                notifDropdown.classList.remove("show"); // ✅ Close dropdown when clicking outside
            }
        });

        notifDropdown.addEventListener("click", function (event) {
            event.stopPropagation(); // ✅ Prevent closing when clicking inside dropdown
        });
    }

    function fetchNotifications() {
    fetch("get_subscriptions.php")
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const notifTableBody = document.querySelector("#notifTableBody");
            const notifCount = document.querySelector("#notifCount");

            if (!notifTableBody || !notifCount) return;

            notifTableBody.innerHTML = ""; // ✅ Clear table before adding new rows

            if (data.error) {
                notifTableBody.innerHTML = `<tr><td colspan="3" class="notif-placeholder">${data.error}</td></tr>`;
                notifCount.style.display = "none";
                return;
            }

            if (data.length === 0) {
                notifTableBody.innerHTML = `<tr><td colspan="3" class="notif-placeholder">No new subscriptions</td></tr>`;
                notifCount.style.display = "none";
            } else {
                data.forEach(notif => {
                    let row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${notif.user_id}</td>
                        <td>${notif.locker_no}</td>
                        <td>${formatDate(notif.start_date)}</td>
                    `;
                    notifTableBody.appendChild(row);
                });

                notifCount.innerText = data.length;
                notifCount.style.display = "inline-block";
            }
        })
        .catch(error => {
            console.error("Error fetching notifications:", error);
            document.querySelector("#notifTableBody").innerHTML = `<tr><td colspan="3" class="notif-placeholder">Failed to load notifications</td></tr>`;
        });
}


    function formatDate(dateStr) {
        let date = new Date(dateStr);
        return date.toLocaleDateString();
    }

    // ✅ Fetch notifications when the page loads and every 30 seconds
    fetchNotifications();
    setInterval(fetchNotifications, 30000);
});
    </script>

</body>
</html>

