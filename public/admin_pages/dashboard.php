<?php
include '../gen_functions/config.php';
//Rfid Function
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $rfid_tag = isset($_POST['rfid_tag']) ? trim($_POST['rfid_tag']) : '';

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

//Add Locker
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_locker'])) {

  $locker_number = trim($_POST['locker_number']);

  if (!empty($locker_number)) {
      try {
          // Check if locker number already exists
          $stmt = $pdo->prepare("SELECT COUNT(*) FROM lockers WHERE locker_number = ?");
          $stmt->execute([$locker_number]);
          $lockerExists = $stmt->fetchColumn();

          if ($lockerExists) {
              $message = "❌ Locker number already exists!";
          } else {
              // Insert new locker
              $stmt = $pdo->prepare("INSERT INTO lockers (locker_number, status) VALUES (?, 'available')");
              if ($stmt->execute([$locker_number])) {
                  $message = "✅ Locker successfully added!";
              } else {
                  $message = "❌ Failed to add locker.";
              }
          }
      } catch (PDOException $e) {
          $message = "❌ Database error: " . $e->getMessage();
      }
  } else {
      $message = "❌ Please enter a locker number.";
  }

  // Display message using SweetAlert2
  echo "<script>
      document.addEventListener('DOMContentLoaded', function() {
          Swal.fire({ icon: 'info', title: 'Locker Status', text: '$message' });
      });
  </script>";
}

$rfid_cards = $pdo->query("SELECT * FROM registered_rfid")->fetchAll();
$lockers = $pdo->query("SELECT * FROM lockers")->fetchAll();
$users = $pdo->query("SELECT * FROM user_tbl")->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="admin_pages/adminstyle.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="icon" href="../resources/logo.png" type="../resources/png">
  <title>Admin Dashboard</title>
</head>

<body>

<div class="dashboard-container">
    <div class="inner-container7">
    <h2 class="chart-title1">Monthly Subscribers</h2>
        <div class="card large7">
            <canvas id="barChart"></canvas>
        </div>
    </div>

    <div class="inner-container8">
    <h2 class="chart-title1">User Distributions</h2>
        <div class="card large8">
            <canvas id="pieChart"></canvas>
        </div>
    </div>

    <div class="inner-container9">
    <h2 class="chart-title1">Locker Usage</h2>
        <div class="card large9">
            <canvas id="lineChart"></canvas>
        </div>
    </div> 

    <div class="inner-container10">
    <h2 class="chart-title1">Locker Availability</h2>
        <div class="card large10">
            <canvas id="doughnutChart"></canvas>
        </div>
    </div>
</div>


<h1 class="charts-title">Unable Users</h1>
<div class="inner-container5">
    <div class="table-container">
        <table id="subscriptionTable" class="display">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Full Name</th>
                    <th>Locker No</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated here -->
            </tbody>
        </table>
    </div>
</div>

<h1 class="charts-title">Change Locker</h1>
<div class="inner-container6">
    <div class="table-container">
        <table id="changeLockerTable" class="display">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Full Name</th>
                    <th>Current Locker</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated here -->
            </tbody>
        </table>
    </div>
</div>
</div>
</div>

  <?php if (!empty($message)): ?>
    <script>
      Swal.fire({ icon: 'info', title: 'RFID Registration', text: '<?php echo $message; ?>' });
    </script>
  <?php endif; ?>

  <script>
    //Rfid 
    $(document).ready(function() {
      $('#rfidTable').DataTable({
        "paging": true,
        "searching": true,
        "info": true,
        "pageLength": 5
      });
    });
    //Locker
    $(document).ready(function() {
        $('#lockerTable').DataTable({
            "paging": true,
            "searching": true,
            "info": true,
            "pageLength": 5
        });
    });
    //Users
    $(document).ready(function() {
    $('#usersTable').DataTable({
        "paging": true,
        "searching": true,
        "info": true,
        "pageLength": 5,
        "lengthMenu": [5, 10, 25, 50],
        "columnDefs": [
            { "orderable": true, "targets": 0 }, 
            { "orderable": false, "targets": [1, 2, 3] } 
        ]
    });
});

//FREEZE PAUSE
$(document).ready(function () {
    var table = $("#subscriptionTable").DataTable({
        "ajax": "subscription_function.php",
        "columns": [
            { "data": "user_id" },
            { "data": "full_name" },
            { "data": "locker_no" },
            { "data": "status" },
            { "data": "start_date" },
            { "data": "end_date" },
            {
                "data": "sub_id",
                "render": function (data, type, row) {
                    let btnClass = row.status === "Paused" ? "btn-success" : "btn-warning";
                    let btnText = row.status === "Paused" ? "Resume" : "Pause";
                    return `<button class="pause-btn btn ${btnClass}" data-id="${data}" data-status="${row.status}">${btnText}</button>`;
                }
            }
        ]
    });
    // PAUSE BUTTON 
    $("#subscriptionTable tbody").on("click", ".pause-btn", function () {
        var subId = $(this).data("id");
        var currentStatus = $(this).data("status");

        let newStatus = currentStatus === "Paused" ? "Active" : "Paused";
        let actionText = currentStatus === "Paused" ? "resume" : "pause";

        Swal.fire({
            title: `Are you sure you want to ${actionText} this subscription?`,
            text: "This action can be changed later.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#213555",
            cancelButtonColor: "#DC5F00",
            confirmButtonText: `Yes, ${actionText} it!`
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "subscription_function.php",
                    type: "POST",
                    data: { sub_id: subId, status: newStatus },
                    success: function (response) {
                        Swal.fire("Success!", `Subscription has been ${actionText}d.`, "success");
                        table.ajax.reload();
                    },
                    error: function () {
                        Swal.fire("Error!", "Something went wrong.", "error");
                    }
                });
            }
        });
    });
});

//CHANGE LOCKER FUNCTION =(
  $(document).ready(function () {
    var table = $("#changeLockerTable").DataTable({
        "ajax": "changelocker_function.php",
        "columns": [
            { "data": "user_id" },
            { "data": "full_name" },
            { "data": "locker_no" },
            { "data": "start_date" },
            { "data": "end_date" },
            {
                "data": "sub_id",
                "render": function (data, type, row) {
                    return `<button class="change-locker-btn btn btn-primary" data-id="${data}" data-locker="${row.locker_no}">Change Locker</button>`;
                }
            }
        ]
    });

    // CHANGE LOCKER BUTTON FUNCTION
    $("#changeLockerTable tbody").on("click", ".change-locker-btn", function () {
        var subId = $(this).data("id");
        var currentLocker = $(this).data("locker");

        Swal.fire({
            title: "Enter new locker number",
            input: "text",
            inputPlaceholder: `Current Locker: ${currentLocker}`,
            showCancelButton: true,
            confirmButtonText: "Update",
            showLoaderOnConfirm: true,
            preConfirm: (newLocker) => {
                return $.ajax({
                    url: "changelocker_function.php",
                    type: "POST",
                    data: { sub_id: subId, locker_no: newLocker },
                    dataType: "json"
                }).then(response => {
                    if (response.success) {
                        Swal.fire("Success!", "Locker number updated.", "success");
                        table.ajax.reload();
                    } else {
                        Swal.fire("Error!", response.error || "Something went wrong.", "error");
                    }
                }).catch(() => {
                    Swal.fire("Error!", "Failed to update locker.", "error");
                });
            }
        });
    });
});

    // Modal Controls
    function openModal(modalId) {
      document.getElementById(modalId).style.display = 'block';
    }

    function closeModal() {
      document.querySelectorAll('.modal').forEach((modal) => {
        modal.style.display = 'none';
      });
    }

    document.addEventListener('DOMContentLoaded', () => {
      document.getElementById('rfidCard').addEventListener('click', () => openModal('rfidModal'));
      document.getElementById('lockerCard').addEventListener('click', () => openModal('lockerModal'));
      document.getElementById('pinCard').addEventListener('click', () => openModal('pinModal'));
      document.getElementById('usersCard').addEventListener('click', () => openModal('usersModal'));
      document.querySelectorAll('.close-btn').forEach((btn) => {
        btn.addEventListener('click', closeModal);
      });
      window.addEventListener('click', (event) => {
        if (event.target.classList.contains('modal')) {
          closeModal();
        }
      });
    });
  </script>

<script>
    const primaryColor = "#213555";
    const secondaryColor = "#3E5879";

    // Bar Chart
    new Chart(document.getElementById("barChart"), {
        type: "bar",
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr"],
            datasets: [{
                label: "Monthly Sales",
                data: [120, 90, 150, 80],
                backgroundColor: primaryColor,
                borderColor: secondaryColor,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: secondaryColor
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: secondaryColor },
                    grid: { color: "rgba(255, 255, 255, 0.2)" }
                },
                y: {
                    ticks: { color: secondaryColor },
                    grid: { color: "rgba(255, 255, 255, 0.2)" }
                }
            }
        }
    });

    // Pie Chart
    new Chart(document.getElementById("pieChart"), {
        type: "pie",
        data: {
            labels: ["User 1", "User 2", "User 3", "User 4"],
            datasets: [{
                data: [40, 30, 20, 10],
                backgroundColor: [primaryColor, "rgba(255, 255, 255, 0.8)"],
                borderColor: secondaryColor,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: secondaryColor
                    }
                }
            }
        }
    });
    

    // Line Chart
    new Chart(document.getElementById("lineChart"), {
        type: "line",
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
            datasets: [{
                label: "Number of users",
                data: [30, 45, 60, 70, 90, 120],
                borderColor: primaryColor,
                backgroundColor: "rgba(255, 255, 255, 0.2)",
                fill: true,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: secondaryColor
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: secondaryColor },
                    grid: { color: "rgba(255, 255, 255, 0.2)" }
                },
                y: {
                    ticks: { color: secondaryColor },
                    grid: { color: "rgba(255, 255, 255, 0.2)" }
                }
            }
        }
    });

    // Doughnut Chart
    new Chart(document.getElementById("doughnutChart"), {
        type: "doughnut",
        data: {
            labels: ["User 1", "User 2", "User 3", "User 4"],
            datasets: [{
                data: [50, 25, 15, 10],
                backgroundColor: [primaryColor, "rgba(255, 255, 255, 0.8)"],
                borderColor: secondaryColor,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: secondaryColor
                    }
                }
            }
        }
    });
</script>

</body>
<footer><?php include 'footer.php'; ?></footer>
</html>

 