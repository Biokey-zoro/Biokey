const socket = new WebSocket('ws://localhost:3000');
const scanBtn = document.getElementById('scanBtn');
const overlay = document.getElementById('overlay');
const countdownDisplay = document.getElementById('countdown');

let selectedLocker = null;

// Function: Start Countdown
function startCountdown(duration = 30) {
    overlay.classList.remove('hidden');
    countdownDisplay.textContent = `Scanning... ${duration}s`;

    let countdownTimer = setInterval(() => {
        duration--;
        countdownDisplay.textContent = `Scanning... ${duration}s`;

        if (duration <= 0) {
            clearInterval(countdownTimer);
            overlay.classList.add('hidden');
            Swal.fire('❗ Time Expired', 'Please try again.', 'warning');
        }
    }, 1000);
}

// Function: Start RFID Scan
$('#scanBtn').click(function () {
    Swal.fire({
        title: 'Scanning...',
        html: `Please scan your RFID.<br><b>Time Left: <span id="countdown">30</span>s</b>`,
        icon: 'info',
        timer: 30000,
        allowOutsideClick: false,
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        didOpen: () => {
            let timeLeft = 30;
            let timerInterval = setInterval(() => {
                timeLeft--;
                $('#countdown').text(timeLeft);

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    Swal.fire('❗ Time Expired', 'Please try again.', 'warning');
                    socket.send('CANCEL_SCANNING');
                }
            }, 1000);
        },
        willClose: () => {
            socket.send('CANCEL_SCANNING');
        }
    });

    socket.send('START_SCANNING');
});

// WebSocket: Handle Incoming Messages
socket.onmessage = (event) => {
    const cleanedData = event.data.trim();

    if (cleanedData.startsWith('SCANNED_TAG:')) {
        let tag = cleanedData.split(':')[1].trim();
        handleScannedTag(tag);
    } else if (cleanedData.startsWith('RFID_TAG:')) {
        let tag = cleanedData.split(':')[1].trim();
        handleRFIDTag(tag);
    }
};


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

// Function: Handle Scanned RFID
function handleScannedTag(tag) {
    Swal.fire({
        title: '✅ Access Granted!',
        text: `Scanned RFID: ${tag}`,
        icon: 'success',
        confirmButtonText: 'OK'
    });
}

// Function: Handle RFID Registration
async function handleRFIDTag(tag) {
    const { value: userName } = await Swal.fire({
        title: '🆕 Register RFID',
        text: `RFID Tag: ${tag}`,
        input: 'text',
        inputPlaceholder: 'Enter Username',
        showCancelButton: true,
        confirmButtonText: 'Save',
        preConfirm: (inputValue) => {
            if (!inputValue) {
                Swal.showValidationMessage('Username cannot be empty!');
            }
        }
    });

    if (userName) {
        $.post('register_rfid.php', { rfid: tag, user: userName }, function (response) {
            Swal.fire({
                title: '✅ RFID Registered!',
                text: `User: ${userName}\nRFID: ${tag}`,
                icon: 'success'
            });
            fetchRFIDList(); // Auto-refresh RFID list
        }).fail(function () {
            Swal.fire('❌ Registration Failed', 'Could not save RFID.', 'error');
        });
    }
}


function fetchLockers() {
    $.ajax({
        url: 'fetch_lockers.php',
        type: 'GET',
        dataType: 'json',
        success: function (lockers) {
            console.log('✅ Lockers Data:', lockers);
            if (!Array.isArray(lockers)) {
                console.error("❌ Invalid response format", lockers);
                return;
            }

            const lockerContainer = $('#locker-container');
            lockerContainer.empty();

            lockers.forEach(locker => {
                const lockerDiv = $(`
                    <div class="locker" data-locker-id="${locker.locker_id}" data-user-id="${locker.user_id}">
                        Locker #${locker.locker_number}
                    </div>
                `);

                lockerDiv.click(function () {
                    $('.locker').removeClass('selected');
                    $(this).addClass('selected');
                    selectedLocker = $(this).data('locker-id');

                    $('#assignBtn, #changeUserBtn, #clearBtn, #updateStatusBtn, #deleteLockerBtn')
                        .prop('disabled', false);
                });

                lockerContainer.append(lockerDiv);
            });
        },
        error: function (xhr, status, error) {
            console.error('❌ AJAX Error:', status, error);
            console.log(xhr.responseText);
        }
    });
}

// Function: Auto-update Lockers
$(document).ready(function () {
    let selectedLocker = null;

    fetchLockers();
    
    // Fetch users where role = 'user'
    function fetchUsers(callback) {
        $.ajax({
            url: 'fetch_users.php', // Fetch users from database
            type: 'GET',
            dataType: 'json',
            success: function (users) {
                console.log('✅ Users:', users);
                callback(users);
            },
            error: function (xhr, status, error) {
                console.error('❌ Error fetching users:', status, error);
                console.log(xhr.responseText);
            }
        });
    }

    // Get current user assigned to locker
    function getLockerUser(lockerId, callback) {
        $.ajax({
            url: 'get_locker_user.php', // Fetch assigned user
            type: 'GET',
            data: { locker_id: lockerId },
            dataType: 'json',
            success: function (user) {
                console.log('✅ Current Locker User:', user);
                callback(user);
            },
            error: function (xhr, status, error) {
                console.error('❌ Error fetching locker user:', status, error);
                console.log(xhr.responseText);
            }
        });
    }

    // Assign Locker
    $('#assignBtn').click(function () {
        if (!selectedLocker) return;
        
        fetchUsers(function (users) {
            let userOptions = users.map(user => `<option value="${user.id}">${user.name}</option>`).join('');

            Swal.fire({
                title: 'Assign Locker',
                html: `<select id="userSelect" class="swal2-select">${userOptions}</select>`,
                showCancelButton: true,
                confirmButtonText: 'Assign'
            }).then((result) => {
                if (result.isConfirmed) {
                    let selectedUser = $('#userSelect').val();
                    $.post('assign_locker.php', { locker_id: selectedLocker, user_id: selectedUser }, function (response) {
                        Swal.fire('Assigned!', 'The locker has been assigned.', 'success');
                        fetchLockers();
                    });
                }
            });
        });
    });

    // Change User
    $('#changeUserBtn').click(function () {
        if (!selectedLocker) return;

        getLockerUser(selectedLocker, function (currentUser) {
            fetchUsers(function (users) {
                let userOptions = users.map(user => {
                    let selected = user.id === currentUser.id ? "selected" : "";
                    return `<option value="${user.id}" ${selected}>${user.name}</option>`;
                }).join('');

                Swal.fire({
                    title: 'Change Locker User',
                    html: `<select id="userSelect" class="swal2-select">${userOptions}</select>`,
                    showCancelButton: true,
                    confirmButtonText: 'Change User'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let newUserId = $('#userSelect').val();
                        if (newUserId == currentUser.id) {
                            Swal.fire('No Change', 'User remains the same.', 'info');
                            return;
                        }

                        $.post('change_user.php', { locker_id: selectedLocker, user_id: newUserId }, function (response) {
                            Swal.fire('Changed!', 'Locker user has been updated.', 'success');
                            fetchLockers();
                        });
                    }
                });
            });
        });
    });

    // Clear Locker
    $('#clearBtn').click(function () {
        if (!selectedLocker) return;
        $.post('clear_locker.php', { locker_id: selectedLocker }, function (response) {
            Swal.fire('Cleared!', 'Locker has been cleared.', 'success');
            fetchLockers();
        });
    });

    // Update Status
    $('#updateStatusBtn').click(function () {
        if (!selectedLocker) return;
        $.post('update_status.php', { locker_id: selectedLocker }, function (response) {
            Swal.fire('Updated!', 'Locker status updated.', 'success');
            fetchLockers();
        });
    });

    // Delete Locker
    $('#deleteLockerBtn').click(function () {
        if (!selectedLocker) return;
        $.post('delete_locker.php', { locker_id: selectedLocker }, function (response) {
            Swal.fire('Deleted!', 'Locker has been deleted.', 'success');
            fetchLockers();
        });
    });
});




// Function: Assign User to Locker
$('#assignBtn, #changeUserBtn').click(function () {
    $.ajax({
        url: 'fetch_users.php',
        type: 'GET',
        dataType: 'json',
        success: function (users) {
            Swal.fire({
                title: 'Select a User',
                input: 'select',
                inputOptions: users.reduce((acc, user) => {
                    acc[user.id] = user.username;
                    return acc;
                }, {}),
                showCancelButton: true,
                confirmButtonText: 'Assign'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log(`User ${result.value} assigned to locker ${selectedLocker}`);
                    fetchLockers(); // Refresh locker assignments
                }
            });
        }
    });
});

// Function: Delete Locker
$('#deleteLockerBtn').click(function () {
    $.ajax({
        url: "delete_locker.php",
        type: "POST",
        data: { locker_id: selectedLocker },
        success: function (response) {
            Swal.fire("✅ Locker Deleted!", "", "success");
            fetchLockers(); // Refresh locker list
        },
        error: function () {
            Swal.fire("❌ Deletion Failed", "Locker could not be deleted.", "error");
        }
    });
});

$(document).ready(function() {
    $("#addLockerBtn").click(function() {
        Swal.fire({
            title: "Add New Locker",
            html: `
                <input type="number" id="lockerNumber" class="swal2-input" placeholder="Locker Number">
                <input type="password" id="pinNumber" class="swal2-input" placeholder="PIN Number">
            `,
            showCancelButton: true,
            confirmButtonText: "Save Locker",
            preConfirm: () => {
                const lockerNumber = $("#lockerNumber").val();
                const pinNumber = $("#pinNumber").val();

                if (!lockerNumber || !pinNumber) {
                    Swal.showValidationMessage("Both fields are required!");
                    return false;
                }

                return { lockerNumber, pinNumber };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "add_locker.php",  // PHP script to handle the database insertion
                    type: "POST",
                    data: {
                        locker_number: result.value.lockerNumber,
                        pin_number: result.value.pinNumber
                    },
                    success: function(response) {
                        Swal.fire("Success!", "Locker added successfully!", "success");
                    },
                    error: function() {
                        Swal.fire("Error!", "Failed to add locker.", "error");
                    }
                });
            }
        });
    });
});

// Auto-update RFID List, Lockers, and PINs every 5 seconds
setInterval(() => {

    fetchLockers();

}, 5000);

// Initial Load
fetchLockers();
