<html>
<head>
  <script>
    const ws = new WebSocket('ws://localhost:3000');
    const imgElement = document.getElementById('cameraFeed');

    ws.onmessage = (event) => {
      // Convert binary data (image) to base64 and display in an <img> element
      const blob = new Blob([event.data], { type: 'image/jpeg' });
      const url = URL.createObjectURL(blob);
      imgElement.src = url;
    };
  </script>
</head>
<body>
  <h2>Camera Feed</h2>
  <img id="cameraFeed" width="640" height="480" />
</body>
</html>
