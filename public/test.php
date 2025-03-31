<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neumorphism Dark Mode Toggle</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #e0e0e0;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            transition: background 0.3s, color 0.3s;
        }

        .toggle-container {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            width: 60px;
            height: 30px;
            background: #e0e0e0;
            border-radius: 30px;
            box-shadow: 8px 8px 16px #bebebe, -8px -8px 16px #ffffff;
            cursor: pointer;
            transition: background 0.3s;
        }

        .toggle-circle {
            position: absolute;
            width: 26px;
            height: 26px;
            background: #ffffff;
            border-radius: 50%;
            box-shadow: 4px 4px 10px #bebebe, -4px -4px 10px #ffffff;
            left: 2px;
            transition: transform 0.3s ease, background 0.3s;
        }

        body.dark {
            background: #212121;
            color: #f1f1f1;
        }

        body.dark .toggle-container {
            background: #333;
            box-shadow: 8px 8px 16px #111, -8px -8px 16px #444;
        }

        body.dark .toggle-circle {
            transform: translateX(30px);
            background: #666;
            box-shadow: 4px 4px 10px #111, -4px -4px 10px #444;
        }
    </style>
</head>
<body>
    <div class="toggle-container" onclick="toggleDarkMode()">
        <div class="toggle-circle"></div>
    </div>

    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark');
        }
    </script>
</body>
</html>
