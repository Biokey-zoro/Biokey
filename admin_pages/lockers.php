<?php
require '../gen_functions/config.php';

// ADD PIN NUMBER
if (isset($_POST['add_pin'])) {
    $newPin = mysqli_real_escape_string($conn, $_POST['new_pin']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $insertQuery = "INSERT INTO lockers_pin_hw (pin_number, status) VALUES ('$newPin', '$status')";
    if (!mysqli_query($conn, $insertQuery)) {
        echo "Error: " . mysqli_error($conn);
        exit;
    }
    header("Location: view_pins.php");
    exit;
}

// UPDATE PIN STATUS
if (isset($_POST['update_pin'])) {
    $pinId = mysqli_real_escape_string($conn, $_POST['lphw_id']);
    $newStatus = mysqli_real_escape_string($conn, $_POST['status']);

    $updateQuery = "UPDATE lockers_pin_hw SET status='$newStatus' WHERE lphw_id='$pinId'";
    if (!mysqli_query($conn, $updateQuery)) {
        echo "Error: " . mysqli_error($conn);
        exit;
    }
    header("Location: view_pins.php");
    exit;
}

// DELETE PIN NUMBER
if (isset($_GET['delete_pin'])) {
    $pinId = mysqli_real_escape_string($conn, $_GET['delete_pin']);

    $deleteQuery = "DELETE FROM lockers_pin_hw WHERE lphw_id='$pinId'";
    if (!mysqli_query($conn, $deleteQuery)) {
        echo "Error: " . mysqli_error($conn);
        exit;
    }
    header("Location: view_pins.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID & Locker Management</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>

<style>/* General Styles */
:root {
    --white: #ffffff;
    --matte-black: #212121;
    --blue: #213555;
    --gray-bg: #f8f9fa;
}

body {
    font-family: Arial, sans-serif;
    text-align: center;
    background: var(--gray-bg);
}

.main-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* Creates two equal columns */
    gap: 20px;
    justify-content: center;
    width: 80%; /* Adjust as needed */
    max-width: 1200px; /* Prevents excessive stretching on large screens */
    margin: auto;
}

.container1 {
    background: var(--white);
    border-radius: 20px;
    padding: 20px;
    box-shadow: inset 8px 8px 16px #d1d1d1, inset -8px -8px 16px #ffffff;
    width: 80%;
    height: 40%;
    margin-top: 60%;
    margin-left: -20%;
    text-align: center;
    align-items: center;
}

.container {
    background: var(--white);
    border-radius: 20px;
    padding: 30px;
    box-shadow: inset 8px 8px 16px #d1d1d1, inset -8px -8px 16px #ffffff;
    width: 140%;
    height: 100%;
    margin-top: 15%;
    margin-left: -23%;
    text-align: center;
}

.container1:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.container:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.inner-container1 {
    background: var(--white);
    border-radius: 20px;
    padding: 20px;
    box-shadow: inset 8px 8px 16px #d1d1d1, inset -8px -8px 16px #ffffff;
    width: 80%;
    height: 40%;
    margin-top: 60%;
    margin-left: -20%;
    text-align: center;
    align-items: center;
}

.inner-container {
    background: var(--white);
    border-radius: 20px;
    padding: 30px;
    box-shadow: inset 8px 8px 16px #d1d1d1, inset -8px -8px 16px #ffffff;
    width: 140%;
    height: 100%;
    margin-top: 15%;
    margin-left: -23%;
    text-align: center;
}

h1, h2 {
    color: var(--matte-black);
    margin-bottom: 15px;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button {
    width: auto; /* Allows the button to take natural width */
    padding: 10px 20px;
    display: block; /* Ensures it respects margin auto */
    margin: 10px auto; /* Centers the button horizontally */
    text-align: center;
}


button:hover {
    background: #1a2a44;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid #ccc;
    text-align: center;
    padding: 10px;
}

th {
    background: var(--blue);
    color: white;
}

td {
    background: var(--white);
}

/* Lockers Management */
.subtitle {
    margin-top: 40px;
}

.grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 10px;
    width: 80%;
    margin: auto;
}

/* Modal */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

.modal {
    background: var(--white);
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.close-btn {
    background: red;
    margin-bottom: 10px;
}
</style>

<div class="main-container">
    <!-- First Container (RFID & Locker Management) -->
    <div clss="inner-container1">
    <div class="container1">
        <h1 class="title">RFID & Locker Management</h1>
        <div class="section">
            <button id="scanBtn" class="btn">Scan RFID</button>
            <p id="rfidData" class="output-text"></p>
        </div>
    </div>
    </div>

    <!-- Second Container (PIN Management) -->
    <div clss="inner-container">
    <div class="container">
        <h1 class="title">Pin Numbers - Availability</h1>
        <form method="POST" class="form-group">
            <input type="text" name="new_pin" placeholder="New Pin Number" required>
            <select name="status" required>
                <option value="available">Available</option>
                <option value="assigned">Assigned</option>
            </select>
            <button type="submit" name="add_pin" class="btn">➕ Add Pin</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Pin Number</th>
                    <th>Availability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM lockers_pin_hw";
                $result = mysqli_query($conn, $query);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $statusColor = ($row['status'] === 'available') ? 'style="color:green;font-weight:bold;"' : 'style="color:red;font-weight:bold;"';
                        echo "
                        <tr>
                            <td>{$row['pin_number']}</td>
                            <td $statusColor>{$row['status']}</td>
                            <td>
                                <form method='POST' class='inline-form'>
                                    <input type='hidden' name='lphw_id' value='{$row['lphw_id']}'>
                                    <select name='status'>
                                        <option value='available'>Available</option>
                                        <option value='assigned'>Assigned</option>
                                    </select>
                                    <button type='submit' name='update_pin' class='btn'>✏️ Update</button>
                                </form>
                                <a href='?delete_pin={$row['lphw_id']}' class='btn delete-btn'>❌ Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No pin numbers found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="center">
            <a href="index.php" class="btn">🔙 Back to Home</a>
        </div>
    </div>
</div>
</div>
<div class="container">
        <h2>Locker Control Panel</h2>
        
        <div id="locker-container" class="grid">
            <!-- Lockers will be dynamically added here -->
        </div>

        <div>
            <button id="unlock-btn" class="button green" disabled>Unlock</button>
            <button id="lock-btn" class="button red" disabled>Lock</button>
            <button id="register-btn" class="button blue" disabled>Register</button>
            <button id="delete-btn" class="button gray" disabled>Delete</button>
        </div>
        
        <p id="status">Select a locker to activate the tools.</p>
    </div>

    <div class="container">
        <h2>RFID & Locker Management</h2>
        <button id="scanBtn" class="button blue">Scan RFID</button>
        <p id="rfidData"></p>
    </div>


<!-- Modal for Adding Locker -->
<div class="modal-overlay" id="add-locker-modal">
    <div class="modal">
        <button id="close-add-locker-modal" class="btn close-btn">✖</button>
        <h3 class="modal-title">Add Locker</h3>
        <input type="text" id="locker-number" placeholder="Locker Number" class="input">
        <select id="pin-number-dropdown" class="input"></select>
        <button id="confirm-add-locker" class="btn">Add Locker</button>
    </div>
</div>

    <script>

const socket = new WebSocket('ws://localhost:8080');
const scanBtn = document.getElementById('scanBtn');
const rfidData = document.getElementById('rfidData');
const overlay = document.getElementById('overlay');
const countdownDisplay = document.getElementById('countdown');

let countdownTimer;
let scannedTag = '';


$(document).ready(function() {
            const lockerContainer = $('#locker-container');
            for (let i = 1; i <= 9; i++) {
                lockerContainer.append(`
                    <div class="locker">
                        <p>Locker ${i}</p>
                        <div class='button-group'>
                            <button class='button blue assign-btn' data-locker-id="${i}">➕ Assign</button>
                            <button class='button green change-user-btn' data-locker-id="${i}">🔄 Change User</button>
                            <button class='button red clear-btn' data-locker-id="${i}">🗑️ Clear</button>
                            <button class='button gray update-status-btn' data-locker-id="${i}">🟢/🔴 Update Status</button>
                            <button class='button black delete-locker-btn' data-locker-id="${i}">❌ Delete</button>
                        </div>
                    </div>
                `);
            }

            $('.assign-btn').click(function() {
                const lockerId = $(this).data('locker-id');
                $('#status').text(`Locker ${lockerId} assigned.`);
            });

            $('.change-user-btn').click(function() {
                const lockerId = $(this).data('locker-id');
                $('#status').text(`User for Locker ${lockerId} changed.`);
            });

            $('.clear-btn').click(function() {
                const lockerId = $(this).data('locker-id');
                $('#status').text(`Locker ${lockerId} cleared.`);
            });

            $('.update-status-btn').click(function() {
                const lockerId = $(this).data('locker-id');
                $('#status').text(`Status for Locker ${lockerId} updated.`);
            });

            $('.delete-locker-btn').click(function() {
                const lockerId = $(this).data('locker-id');
                $('#status').text(`Locker ${lockerId} deleted.`);
            });

            $('#scanBtn').click(function() {
                Swal.fire({
                    title: 'Scanning...',
                    text: 'Please scan your RFID tag.',
                    icon: 'info',
                    timer: 3000,
                    showConfirmButton: false
                });
                
                setTimeout(() => {
                    const fakeRFID = '123456ABC'; // Simulating RFID data
                    $('#rfidData').text(`RFID Scanned: ${fakeRFID}`);
                }, 3000);
            });
        });
        

// Display Countdown Overlay
function startCountdown(duration = 30) {
    overlay.classList.remove('hidden');
    countdownDisplay.textContent = `Scanning... ${duration}s`;

    countdownTimer = setInterval(() => {
        duration--;
        countdownDisplay.textContent = `Scanning... ${duration}s`;

        if (duration <= 0) {
            clearInterval(countdownTimer);
            overlay.classList.add('hidden');
            Swal.fire('❗ Time Expired', 'Please try again.', 'warning');
        }
    }, 1000);
}

// Start RFID Scanning
scanBtn.addEventListener('click', () => {
    if (socket.readyState === WebSocket.OPEN) {
        socket.send('START_SCANNING');
        startCountdown(30);
    } else {
        Swal.fire('❌ Error', 'WebSocket connection failed. Please refresh the page.', 'error');
    }
});

// WebSocket Message Handling
socket.onmessage = async (event) => {
    const cleanedData = event.data.trim();
    console.log('🔍 Raw data received:', cleanedData);

    if (cleanedData === 'SCANNING_ACTIVE') return;

    const rfidPattern = /^[A-F0-9]{8}$/;
    if (rfidPattern.test(cleanedData)) {
        scannedTag = cleanedData;
        rfidData.innerText = `Scanned RFID: ${scannedTag}`;
        clearInterval(countdownTimer);
        overlay.classList.add('hidden');
        await showUserSelectionModal(scannedTag);
        return;
    }
};

socket.onerror = () => {
    Swal.fire('❗ Connection Error', 'WebSocket connection failed.', 'error');
};

// Fetch and Populate User Dropdown
async function populateUserDropdown() {
    try {
        const response = await fetch('fetch_user.php');
        if (!response.ok) throw new Error(`Failed to fetch users. Status: ${response.status}`);

        const data = await response.json();
        if (!Array.isArray(data) || data.length === 0) return '<p class="text-red-500">❗ No users found.</p>';

        return `
            <select id="userDropdown" class="swal2-select w-full">
                <option value="">-- Select User --</option>
                ${data.map(user => `<option value="${user.user_id}">${user.first_name} ${user.last_name}</option>`).join('')}
            </select>
        `;
    } catch (error) {
        return `<p class="text-red-500">❗ Failed to load users. Error: ${error.message}</p>`;
    }
}

// Show User Selection Modal
async function showUserSelectionModal(scannedTag) {
    const userDropdown = await populateUserDropdown();

    Swal.fire({
        title: 'New RFID Detected',
        html: `<p>Scanned RFID: <strong>${scannedTag}</strong></p><label>Assign RFID to:</label>${userDropdown}`,
        showCancelButton: true,
        confirmButtonText: '✅ Save RFID',
        cancelButtonText: '❌ Reject RFID',
        preConfirm: async () => {
            const selectedUserId = document.getElementById('userDropdown').value;
            if (!selectedUserId) {
                Swal.showValidationMessage('❗ Please select a user.');
                return false;
            }

            try {
                const response = await fetch('register_rfid.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ rfidTag: scannedTag, userId: selectedUserId })
                });

                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.message || 'RFID registration failed.');

                return result;
            } catch (error) {
                Swal.fire({ icon: 'error', title: '❌ Error', text: error.message });
                return false;
            }
        }
    }).then((result) => {
        if (result.isConfirmed && result.value?.success) {
            Swal.fire('✅ Success', result.value.message, 'success');
        }
    });
}

$(document).ready(function () {
    $('.assign-btn').click(function () {
        const lockerId = $(this).data('locker');

        $.ajax({
            url: 'fetch_user.php',
            type: 'GET',
            dataType: 'json',
            success: function (users) {
                let userOptions = users.map(user => `<option value="${user.id}">${user.username}</option>`).join('');

                Swal.fire({
                    title: 'Assign Locker',
                    html: `
                        <select id="userSelect" class="swal2-select">
                            <option value="">-- Select User --</option>
                            ${userOptions}
                        </select>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Assign',
                    preConfirm: () => {
                        const selectedUser = document.getElementById('userSelect').value;
                        if (!selectedUser) {
                            Swal.showValidationMessage('Please select a user');
                            return false;
                        }
                        return selectedUser;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'assign_locker.php',
                            type: 'POST',
                            data: { locker_id: lockerId, user_id: result.value },
                            success: function (response) {
                                Swal.fire('Success', 'Locker assigned successfully!', 'success')
                                    .then(() => location.reload());
                            },
                            error: function () {
                                Swal.fire('Error', 'Failed to assign locker', 'error');
                            }
                        });
                    }
                });
            },
            error: function () {
                Swal.fire('Error', 'Failed to load users', 'error');
            }
        });
    });
});


// Locker Management Functions
$(document).ready(() => {
    loadLockers();
    loadPins();
});

function loadLockers() {
    $.get('fetch_lockers.php', function (data) {
        $('#locker-container').html(data.trim() ? data : '<p class="text-gray-500 w-full">❗ No lockers available.</p>');
    });
}

function loadPins() {
    $.get('fetch_pins.php', function (data) {
        try {
            const pins = JSON.parse(data);
            $('#pin-number-dropdown').empty().append(pins.length ? '<option value="">Select Pin Number</option>' : '<option value="">❗ No available pins</option>');
            pins.forEach(pin => $('#pin-number-dropdown').append(`<option value="${pin.pin_number}">${pin.pin_number}</option>`));
        } catch (error) {
            console.error('JSON Parse Error:', error, '\nReceived Data:', data);
        }
    });
}

// Add Locker Event
$('#add-locker-btn').on('click', () => $('#add-locker-modal').removeClass('hidden').addClass('flex'));
$('#close-add-locker-modal').on('click', () => $('#add-locker-modal').addClass('hidden'));

$('#confirm-add-locker').on('click', () => {
    const lockerNumber = $('#locker-number').val();
    const selectedPin = $('#pin-number-dropdown').val();

    if (!lockerNumber || !selectedPin) return alert('❗ Please enter a locker number and select a pin number.');

    $.post('actions.php', { action: 'add', locker_number: lockerNumber, pin_number: selectedPin }, function (response) {
        alert(response);
        $('#add-locker-modal').addClass('hidden');
        loadLockers();
        loadPins();
    });
});

// Locker Actions (Assign, Change User, Clear, Update Status, Delete)
$(document).on('click', '.assign-btn, .change-user-btn, .clear-btn, .update-status-btn, .delete-locker-btn', function () {
    const action = $(this).attr('class').split('-')[0];
    const lockerId = $(this).data('locker-id');

    if (action === 'delete' && !confirm('Are you sure you want to delete this locker?')) return;

    $.post('actions.php', { action, locker_id: lockerId }, function (response) {
        alert(response);
        loadLockers();
    });
});


    </script>
</body>
</html>
