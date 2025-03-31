<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Facial Enrollment</title>
</head>
<body>
    <h1>Enroll Facial Recognition</h1>

    <!-- Form for uploading image -->
    <form action="upload_image.php" method="POST" enctype="multipart/form-data">
        <label for="image">Upload Image for Facial Enrollment:</label>
        <input type="file" name="image" id="image" required><br><br>
        <button type="submit">Enroll</button>
    </form>
</body>
</html>
