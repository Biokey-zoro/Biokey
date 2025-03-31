<?php
require '../gen_functions/config.php'; // Database connection

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rfid_tag = trim($_POST['rfid_tag']);

    // Check if RFID is already registered
    $stmt = $pdo->prepare("SELECT * FROM registered_rfid WHERE rfid_tag = ?");
    $stmt->execute([$rfid_tag]);

    if ($stmt->rowCount() > 0) {
        $message = "❌ RFID Tag already registered!";
    } else {
        // Insert new RFID record
        $stmt = $pdo->prepare("INSERT INTO registered_rfid (rfid_tag) VALUES (?)");
        if ($stmt->execute([$rfid_tag])) {
            $message = "✅ RFID Registered Successfully!";
        } else {
            $message = "❌ Registration Failed!";
        }
    }
}

// Fetch registered RFID cards for display
$rfid_cards = $pdo->query("SELECT * FROM registered_rfid")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin RFID Registration</title>
    <link rel="stylesheet" href="adminstyle.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
body {
    font-family: Arial, sans-serif;
    text-align: center;
    padding: 20px;
    background: #f8f9fa;
    align-items: center;
    justify-content: center;

}

.container {
    max-width: 500px;
    margin: auto;
    justify-content: center;
    align-items: center;
}

.card {
    background: var(--white);
    border-radius: 20px;
    padding: 20px;
    width: 100%; 
    max-width: 700px; 
    margin-top: 20px;
    box-shadow: inset 8px 8px 16px #d1d1d1, inset -8px -8px 16px #ffffff;
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    background-color: var(--white);
    color: var(--matte-black);
}

.inner-container {
    grid-column: span 2;
    height: 500px;
    width: 150%; 
    max-width: 1000px;
    background: var(--white);
    border-radius: 20px;
    padding: 10px;
    margin-top: 50px;
    margin-left: -25%;
    align-items: center;
    box-shadow: inset 8px 8px 16px #d1d1d1, inset -8px -8px 16px #ffffff;
}

h2 {
    font-size: 22px;
    margin-bottom: 15px;
    color: var(--matte-black);
}

form {
    margin-bottom: 20px;
}

input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.btn {
    width: 100%;
    background: var(--navy-blue);
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-size: 18px;
    font-weight: bold;
    color: var(--white);
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    margin-top: 20px;
}

.btn:hover {
    color: var(--white);
}

table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
}

th {
    background-color: #f2f2f2;
}

/* Dark Mode */
.dark-mode .card {
    background: var(--white);
    border-radius: 20px;
    padding: 20px;
    width: 100%; 
    max-width: 700px; 
    margin-top: 20px;
    box-shadow: inset 8px 8px 16px #d1d1d1, inset -8px -8px 16px #ffffff;
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.dark-mode .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    background-color: var(--white);
    color: var(--matte-black);
}

.dark-mode .btn {
    width: 100%;
    background: var(--navy-blue);
    border: none;
    border-radius: 10px;
    padding: 12px;
    font-size: 18px;
    font-weight: bold;
    color: var(--white);
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    margin-top: 20px;
}

.dark-mode h2{
    font-size: 22px;
    margin-bottom: 15px;
    color: var(--navyblue);
}
    </style>
</head>
<body>
    <div class="container">
    <div class="inner-container">
        <div class="card">
            <h2>Admin RFID Registration</h2>
            <form method="POST">
                <label for="rfid_tag">RFID Tag:</label>
                <input type="text" name="rfid_tag" required placeholder="Scan RFID tag">
                <button type="submit" class="btn">Register RFID</button>
            </form>

            <h2>Registered RFID Cards</h2>
            <table>
                <tr>
                    <th>RFID Tag</th>
                    <th>Status</th>
                    <th>Registered At</th>
                </tr>
                <?php foreach ($rfid_cards as $card) { ?>
                    <tr>
                        <td><?php echo $card['rfid_tag']; ?></td>
                        <td><?php echo ucfirst($card['status']); ?></td>
                        <td><?php echo $card['registered_at']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>

    <?php if (!empty($message)): ?>
        <script>
            Swal.fire({ icon: 'info', title: 'RFID Registration', text: '<?php echo $message; ?>' });
        </script>
    <?php endif; ?>
</body>
</html>
