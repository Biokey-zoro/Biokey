<?php
require '../gen_functions/config.php'; // Database connection

$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle RFID tag selection and user assignment
    if (isset($_POST['assign_rfid'])) {
        $rfid_tag = trim($_POST['rfid_tag']);
        $user_id = trim($_POST['user_id']);

        // Check if RFID is available
        $stmt = $pdo->prepare("SELECT * FROM registered_rfid WHERE rfid_tag = ? AND status = 'available'");
        $stmt->execute([$rfid_tag]);
        $rfid = $stmt->fetch();

        if (!$rfid) {
            $message = "❌ RFID is not registered or already assigned!";
        } else {
            // Assign RFID to user
            $stmt = $pdo->prepare("INSERT INTO user_rfid (rfid_tag, user_id) VALUES (?, ?)");
            if ($stmt->execute([$rfid_tag, $user_id])) {
                // Update RFID status
                $pdo->prepare("UPDATE registered_rfid SET status = 'assigned' WHERE rfid_tag = ?")->execute([$rfid_tag]);
                $message = "✅ RFID Assigned Successfully!";
            } else {
                $message = "❌ Assignment Failed!";
            }
        }
    }
}

// Fetch users dynamically
$users = [];
$stmt = $pdo->query("SELECT user_id, first_name FROM user_tbl");
while ($row = $stmt->fetch()) {
    $users[] = $row;
}

// Fetch available RFID cards dynamically
$rfid_cards = [];
$stmt = $pdo->query("SELECT rfid_tag FROM registered_rfid WHERE status = 'available'");
while ($row = $stmt->fetch()) {
    $rfid_cards[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locker System</title>
    <script src="https://kit.fontawesome.com/77ff7e1fdc.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="style.css"> <!-- Add custom CSS -->

</head>
<body>
    
    <div class="hero-containers">
    <section id="features" class="features-section">
        <div class="locker-containers">
            <!-- Features Info -->
            <div class="locker-infos">
                <h2>Steps on How to Register</h2>
            </div>
            
                <div class="locker-sliders">
                    <div class="locker-cards">
                        <h1><img src="../image/Select.png"></h1>
                        
                        <h3>Select they're Name</h3>
                        <p>Users needs to select they're name.</p>
                    </div>
                    <div class="locker-cards">
                        <h1><img src="../image/locknum.png"></h1>
                        
                        <h3>Accessing Locker Number</h3>
                        <p>Users choose what Locker Number they choose. </p>
                    </div>
                    <div class="locker-cards">
                        <h1><img src="../image/Done.png"></h1>
                        <h3>Done Registering</h3>
                        <p>They can tap now they're RFID card.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="containers">
    <h2>Assign RFID to User</h2>
    <form method="POST">
        <label for="user_id">Select User</label>
        <select name="user_id" required>
            <option value="">User</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= htmlspecialchars($user['user_id']); ?>">
                    <?= htmlspecialchars($user['first_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="user_id">Select Locker Number</label>
        <select name="user_id" required>
            <option value="">Locker Number</option>
            <?php foreach ($users as $user): ?>
                <option value="<?= htmlspecialchars($user['user_id']); ?>">
                    <?= htmlspecialchars($user['first_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="assign_rfid" class="btn">Assign RFID</button>
    </form>
</div>


<?php if (!empty($message)): ?>
    <script>
        Swal.fire({ icon: 'info', title: 'RFID Assignment', text: '<?= htmlspecialchars($message); ?>' });
    </script>
<?php endif; ?>

</body>
</html>
