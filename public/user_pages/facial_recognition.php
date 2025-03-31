<?php
session_start();

$userData = [
    "first_name" => isset($_SESSION["first_name"]) ? $_SESSION["first_name"] : "Unknown",
    "user_id" => isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null
];
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

    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/facemesh"></script>
</head>

<body class="<?php echo $darkMode; ?>">

    <div class="facial-container">
        <div class="facial-recognition">
            <h3 class="h3-face">Facial Recognition</h3>
            <p class="p-face">Enable facial recognition for secure login and locker access.</p>

            <img src="../resources/faceIdGif.gif" alt="Facial Recognition Icon" class="face-image">

            <button class="enroll-face-btn" onclick="openFaceModal()">
                <i class="fa-solid fa-camera"></i> Enroll Facial Data
            </button>
        </div>
    </div>

    <!-- Facial Modal -->
    <div id="faceModal" class="modal-face">
        <div class="modalf-content">
            <span class="close" onclick="closeFaceModal()">&times;</span>
            <h2 class="h2-modal">Facial Recognition Enrollment</h2>
            <p class="p-modal">Position your face inside the frame and follow the on-screen instructions.</p>

            <div id="faceInstructions" class="face-instructions">
                <p id="instructionText">Align your face within the frame</p>
            </div>

            <div class="face-scan">
                <!-- Use the ESP32 Camera Feed -->
                <img id="esp32CameraFeed" src="http://192.168.252.236/stream" width="640" height="480" alt="ESP32 Camera Feed">
                <canvas id="faceCanvas"></canvas>
            </div>

            <div class="progress-container">
                <div id="progressBar"></div>
            </div>
        </div>
    </div>

    <script>
        let userName = "<?php echo $userData['first_name']; ?>";
        let userId = "<?php echo $userData['user_id']; ?>"; // Get the user ID from the session
        console.log("Logged-in User:", userName);

        let video = document.getElementById("esp32CameraFeed");
        let canvas = document.getElementById("faceCanvas");
        let ctx = canvas.getContext("2d");
        let instructionText = document.getElementById("instructionText");
        let progressBar = document.getElementById("progressBar");
        let model;
        let progress = 0;

        // Update instructions and progress
        function updateInstructions(message, resetProgress = false) {
            instructionText.textContent = message;
            if (resetProgress) progress = 0;
        }

        // Open the modal for face enrollment
        function openFaceModal() {
            let modal = document.getElementById("faceModal");
            if (modal.style.display !== "flex") {
                modal.style.display = "flex";
                updateInstructions("Align your face within the frame");
                loadModel();
            }
        }

        // Close the modal and stop the camera
        function closeFaceModal() {
            let modal = document.getElementById("faceModal");
            modal.style.display = "none";
            stopCamera();
        }

        // Stop the camera feed
        function stopCamera() {
            // We are using the ESP32 MJPEG stream, so stopping is not necessary for this case.
            // However, you can stop any active camera resources if using another webcam for additional tasks.
        }

        // Load the facial detection model
        async function loadModel() {
            model = await facemesh.load();
            detectFace();
        }

        // Detect the face using the facemesh model
        async function detectFace() {
            if (!model) return;

            let size = Math.min(video.width, video.height);
            canvas.width = size;
            canvas.height = size;

            let detectionInterval = setInterval(async () => {
                let predictions = await model.estimateFaces(video);
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                if (predictions.length > 0) {
                    predictions.sort((a, b) => {
                        let sizeA = (a.boundingBox.bottomRight[0] - a.boundingBox.topLeft[0]) * (a.boundingBox.bottomRight[1] - a.boundingBox.topLeft[1]);
                        let sizeB = (b.boundingBox.bottomRight[0] - b.boundingBox.topLeft[0]) * (b.boundingBox.bottomRight[1] - b.boundingBox.topLeft[1]);
                        return sizeB - sizeA;
                    });

                    let face = predictions[0].boundingBox;
                    let faceX = face.topLeft[0];
                    let faceY = face.topLeft[1];
                    let faceWidth = face.bottomRight[0] - face.topLeft[0];
                    let faceHeight = face.bottomRight[1] - face.topLeft[1];

                    // Center the face detection
                    let centerX = canvas.width / 2;
                    let centerY = canvas.height / 2;
                    let idealFaceSize = canvas.width * 0.5;

                    ctx.beginPath();
                    ctx.lineWidth = 3;
                    ctx.strokeStyle = "#00FF00";
                    ctx.rect(faceX, faceY, faceWidth, faceHeight);
                    ctx.stroke();

                    ctx.font = "24px Arial";
                    ctx.fillStyle = "#FFFFFF";
                    ctx.fillText(userName, faceX + 10, faceY - 20);

                    let threshold = 40;

                    if (faceX + faceWidth / 2 < centerX - threshold) {
                        updateInstructions("Move right →", true);
                    } else if (faceX + faceWidth / 2 > centerX + threshold) {
                        updateInstructions("← Move left", true);
                    } else if (faceY + faceHeight / 2 < centerY - threshold) {
                        updateInstructions("Move down ↓", true);
                    } else if (faceY + faceHeight / 2 > centerY + threshold) {
                        updateInstructions("↑ Move up", true);
                    } else if (faceWidth < idealFaceSize * 0.7) {
                        updateInstructions("Move closer", true);
                    } else if (faceWidth > idealFaceSize * 1.3) {
                        updateInstructions("Move back", true);
                    } else {
                        updateInstructions("Face aligned! Hold still...");
                        progress += 10;
                    }
                } else {
                    updateInstructions("Face not detected. Please center your face.", true);
                }

                progressBar.style.width = progress + "%";

                if (progress >= 100) {
                    clearInterval(detectionInterval);
                    captureFace();
                }
            }, 100);
        }

        // Capture the face and send it to the server
        async function captureFace() {
            let captureCanvas = document.createElement("canvas");
            let captureCtx = captureCanvas.getContext("2d");

            captureCanvas.width = video.width;
            captureCanvas.height = video.height;

            captureCtx.drawImage(video, 0, 0, captureCanvas.width, captureCanvas.height);

            let imageDataURL = captureCanvas.toDataURL("image/png");

            // Save the image in session storage for review
            sessionStorage.setItem("capturedImage", imageDataURL);

            if (model) {
                const predictions = await model.estimateFaces(video, false);
                if (predictions.length > 0) {
                    let faceData = JSON.stringify(predictions[0].mesh);

                    // Send the face data to the backend for enrollment
                    fetch("enroll_face.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({
                            user_id: userId,
                            face_encoding: faceData
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire("Success!", "Facial data enrolled successfully!", "success");
                        } else {
                            Swal.fire("Error!", data.message || "Failed to save facial data.", "error");
                        }
                    })
                    .catch(error => {
                        console.error("Fetch Error:", error);
                        Swal.fire("Error!", "An error occurred while saving facial data.", "error");
                    });
                }
            } else {
                console.error("Model is not loaded.");
                Swal.fire("Error!", "Face detection model is not loaded.", "error");
            }

            // Update modal with the captured image
            let modalContent = document.querySelector(".modalf-content");
            modalContent.innerHTML = `
                <h2 class="h2-modal">Captured Face</h2>
                <p class="p-modal">This is the captured image. You can save it or retake.</p>
                <div class="captured-image-container">
                    <img src="${imageDataURL}" alt="Captured Face" class="captured-image">
                </div>
                <button class="save-btn" onclick="saveCapturedImage()">Save Image</button>
                <button class="retry-btn" onclick="retakeFace()">Retake</button>
            `;
            stopCamera();
        }

        // Save the captured face image
        function saveCapturedImage() {
            let imageDataURL = sessionStorage.getItem("capturedImage");
            if (imageDataURL) {
                let link = document.createElement("a");
                link.href = imageDataURL;
                link.download = "captured-face.png";
                link.click();
            } else {
                console.error("No image to save.");
            }
        }

        // Retake the face enrollment
        function retakeFace() {
            let modalContent = document.querySelector(".modalf-content");

            progress = 0;
            modalContent.innerHTML = `
                <span class="close" onclick="closeFaceModal()">&times;</span>
                <h2 class="h2-modal">Facial Recognition Enrollment</h2>
                <p class="p-modal">Position your face inside the frame and follow the on-screen instructions.</p>
                <div id="faceInstructions" class="face-instructions">
                    <p id="instructionText">Align your face within the frame</p>
                </div>
                <div class="face-scan">
                    <img id="esp32CameraFeed" src="http://<ESP32_IP_ADDRESS>/stream" width="640" height="480" alt="ESP32 Camera Feed">
                    <canvas id="faceCanvas"></canvas>
                </div>
                <div class="progress-container">
                    <div id="progressBar"></div>
                </div>
            `;
            loadModel();
        }
    </script>
</body>
</html>
