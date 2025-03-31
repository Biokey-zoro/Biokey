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



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleSwitch = document.querySelector(".inpt");
            const body = document.body;
            const logoImage = document.getElementById("logoImage");

            toggleSwitch.checked = body.classList.contains("dark-mode");

            updateLogo(toggleSwitch.checked);

            toggleSwitch.addEventListener("change", function() {
                let darkModeEnabled = toggleSwitch.checked;
                body.classList.toggle("dark-mode", darkModeEnabled);
                updateLogo(darkModeEnabled);

                fetch("dark_mode.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "dark_mode=" + (darkModeEnabled ? "enabled" : "disabled"),
                    credentials: "include"
                });
            });

            function updateLogo(darkModeEnabled) {
                if (darkModeEnabled) {
                    logoImage.src = "../resources/logonidark.png";
                } else {
                    logoImage.src = "../resources/logo.png";
                }

                logoImage.onerror = function() {
                    console.error("Failed to load image: " + logoImage.src);
                    logoImage.src = "../resources/logo.png";
                };
            }
        });
    </script>




    <div class="container-profile">
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- <h2>Edit Profile</h2> -->
            <ul>
                <li class="tab-link active" data-tab="general-settings">
                    <i class="fas fa-user"></i> Edit Profile
                </li>
                <li class="tab-link" data-tab="account-settings">
                    <i class="fas fa-lock"></i> Account Management
                </li>

            </ul>

            <ul>
                <li class="tab-link" data-tab="plan-settings">
                    <i class="fas fa-file"></i> Subscription Info
                </li>
                <!-- <li class="tab-link" data-tab="billing-settings">
                    <i class="fas fa-credit-card"></i> Billing details
                </li> -->
            </ul>
        </aside>

        <!-- Profile Settings -->
        <main class="profile-settings tab-content active" id="general-settings">
            <h2 class="h2-profile">Edit Profile</h2>
            <p class="p-profile">Keep your personal details private. Information you add here <br> is visible to anyone who can view your profile.</p>
            <div class="profile-photo">
                <div class="avatar">
                    <?php
                    $profile_img = !empty($profile_img) ? $profile_img : "default-avatar.png";
                    echo '<img src="' . htmlspecialchars($profile_img) . '" alt="Profile Image">';
                    ?>
                </div>
                <div class="profile-info">
                    <span class="profile-label">Profile photo</span>
                    <p class="profile-description">We support PNGs, JPEGs, and GIFs under 10MB.</p>
                    <button class="upload-btn" onclick="openModal()">Upload new picture</button>
                </div>
            </div>

            <!-- Modal -->
            <div id="uploadModal" class="modal-photo">
                <div class="modalp-content">
                    <span class="close">&times;</span>
                    <h2 class="h2-modal">Upload Profile Photo</h2>
                    <p class="p-modal">We support PNGs, JPEGs, and GIFs under 10MB.</p>
                    <form id="uploadForm" enctype="multipart/form-data">
                        <input type="file" id="fileUpload" name="profile_img" accept="image/*">
                        <button type="submit" class="upload-confirm-btn">Upload</button>
                    </form>

                </div>
            </div>

            <div class="inputp-group">
                <div class="form-group">
                    <label>First name</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>">
                </div>
                <div class="form-group">
                    <label>Last name</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Phone</label>
                <div class="phone-box">
                    <input type="text" placeholder="No Phone Number">
                    <button class="add-btn">Add phone number</button>
                </div>
            </div>
        </main>


        <!-- account management -->
        <main class="profile-settings tab-content" id="account-settings">
            <h2 class="h2-acc">Account Management</h2>
            <p class="p-acc">Make changes to your personal information or account type.</p>
            <h3>Your account</h3>
            <div class="form-group">
                <label>Email • Private</label>
                <div class="email-box">
                    <input type="email" value="<?php echo htmlspecialchars($gmail); ?>">
                    <button class="verify-btn">Verify</button>
                    <button class="update-btn">Update</button>
                </div>
            </div>

            <div class="form-group">
                <label>Change Password</label>
                <input type="password" placeholder="Enter new password">
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" placeholder="Confirm new password">
            </div>
            <button class="updatetwo-btn">Update Password</button>
        </main>


        <!-- plan -->
        <main class="profile-settings tab-content" id="plan-settings">
            <h2 class="h2-plan">Your Plan</h2>
            <p class="p-plan">Manage your current plan and upgrade options here.</p>
            <div class="form-group">
                <label>Current Plan</label>
                <input type="text" value="<?php echo htmlspecialchars($plan_name); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Upgrade Plan</label>
                <select>
                    <option>Weekly</option>
                    <option>Monthly</option>
                    <option>Yearly</option>
                </select>
            </div>
            <button class="upgrade-btn">Upgrade</button>
        </main>


        <!-- Circular Progress Bar -->
        <div class="profile-container">
            <h3 class="h3-prog">Complete your profile</h3>

            <div class="progress-circle">
                <svg width="100" height="100">
                    <circle cx="50" cy="50" r="40" stroke="#ddd" stroke-width="8" fill="none" />
                    <circle cx="50" cy="50" r="40" stroke="#28a745" stroke-width="8" fill="none"
                        stroke-dasharray="251.2" stroke-dashoffset="251.2" stroke-linecap="round" id="progress" />
                </svg>
                <div class="progress-text" id="progress-text">0%</div>
            </div>

            <!-- Profile Completion Tasks -->
            <div class="task-list">
                <div class="task"><span><i class="fa-solid fa-check" style="color: #333333;"></i> Edit Profile</span> <span>10%</span></div>
                <div class="task"><span><i class="fa-solid fa-check" style="color: #333333;"></i> Account Management</span> <span>5%</span></div>
                <div class="task"><span><i class="fa-solid fa-check" style="color: #333333;"></i> Edit Profile</span> <span>10%</span></div>
            </div>
        </div>
    </div>


    <!-- percentage of progress -->
    <script>
        let completionPercentage = 40;

        let progressCircle = document.getElementById("progress");
        let progressText = document.getElementById("progress-text");
        let totalLength = 251.2; // Circumference of the circle

        progressCircle.style.strokeDashoffset = totalLength - (completionPercentage / 100 * totalLength);
        progressText.innerText = completionPercentage + "%";
    </script>

    <!-- switching tab -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tabs = document.querySelectorAll(".tab-link");
            const contents = document.querySelectorAll(".tab-content");

            tabs.forEach(tab => {
                tab.addEventListener("click", function() {
                    const targetTab = this.getAttribute("data-tab");

                    tabs.forEach(item => item.classList.remove("active"));
                    contents.forEach(content => {
                        content.classList.remove("active");
                        content.style.opacity = "0";
                    });

                    this.classList.add("active");

                    const targetContent = document.getElementById(targetTab);
                    targetContent.classList.add("active");

                    setTimeout(() => {
                        targetContent.style.opacity = "1";
                    }, 10);
                });
            });
        });
    </script>

    <!-- upload photo -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = document.getElementById("uploadModal");
            const uploadBtn = document.querySelector(".upload-btn");
            const closeBtn = document.querySelector(".close");
            const uploadForm = document.getElementById("uploadForm");
            const fileInput = document.getElementById("fileUpload");
            const avatar = document.querySelector(".avatar"); // Profile avatar

            modal.style.display = "none";

            uploadBtn.addEventListener("click", function() {
                modal.style.display = "flex";
            });

            closeBtn.addEventListener("click", function() {
                modal.style.display = "none";
            });

            window.addEventListener("click", function(e) {
                if (e.target === modal) {
                    modal.style.display = "none";
                }
            });

            uploadForm.addEventListener("submit", function(e) {
                e.preventDefault(); // Prevent form from reloading the page

                const formData = new FormData();
                formData.append("profile_img", fileInput.files[0]);

                fetch("upload_profile.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            avatar.innerHTML = `<img src="${data.image_url}" alt="Profile Image">`; // Update profile pic
                            modal.style.display = "none"; // Close modal after upload
                        } else {
                            alert(data.error);
                        }
                    })
                    .catch(error => console.error("Error:", error));
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = document.getElementById("uploadModal");
            const uploadBtn = document.querySelector(".upload-btn");
            const closeBtn = document.querySelector(".close");

            modal.style.display = "none";

            uploadBtn.addEventListener("click", function() {
                modal.style.display = "flex";
            });

            closeBtn.addEventListener("click", function() {
                modal.style.display = "none";
            });

            window.addEventListener("click", function(e) {
                if (e.target === modal) {
                    modal.style.display = "none";
                }
            });
        });
    </script>
</body>

</html>