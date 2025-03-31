let model, video, canvas, ctx;

async function loadModel() {
    model = await facemesh.load();
    console.log("Model loaded");
}

async function startCamera() {
    video = document.getElementById('cameraFeed');
    canvas = document.createElement('canvas');
    ctx = canvas.getContext('2d');
    
    const stream = await navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 640 } });
    video.srcObject = stream;

    video.onloadedmetadata = () => {
        video.play();
        detectFace();
    };
}

async function detectFace() {
    if (!model) return;
    const predictions = await model.estimateFaces(video);
    if (predictions.length > 0) {
        const face = predictions[0].boundingBox;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.strokeRect(face.topLeft[0], face.topLeft[1], face.bottomRight[0] - face.topLeft[0], face.bottomRight[1] - face.topLeft[1]);
    }
    requestAnimationFrame(detectFace);
}

async function captureFace() {
    const faceEncoding = await getFaceEncoding();
    sendToServer(faceEncoding);
}

async function getFaceEncoding() {
    // Capture face encoding logic here (example: base64 or a custom encoding)
    return "encoded_face_data";
}

async function sendToServer(faceEncoding) {
    fetch('backend/enroll_face.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ user_id: 1, face_encoding: faceEncoding })
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              alert("Face enrolled successfully!");
          } else {
              alert("Enrollment failed.");
          }
      });
}
