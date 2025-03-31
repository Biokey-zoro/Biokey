<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locker Control</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    
    <style>
    body {
        background-color: #ecf0f3;
        padding: 24px;
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        margin: 80px auto;
    }
    .h2{
        color: black;
    }
    .main-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        max-width: 900px;
        width: 100%;
        justify-content: center;
        margin-top: 20px;
    }
    .lockers-container {
        flex: 1;
        min-width: 300px;
        max-width: 450px;
    }
    .buttons-container {
        flex: 1;
        min-width: 300px;
        max-width: 400px;
    }
    .container {
        background: #ecf0f3;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 8px 8px 16px #b8bec6, -8px -8px 16px #ffffff;
        margin-bottom: 20px;
    }
    .grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    .locker {
        background: #ecf0f3;
        padding: 16px;
        text-align: center;
        border-radius: 12px;
        box-shadow: 6px 6px 12px #b8bec6, -6px -6px 12px #ffffff;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .locker:hover {
        box-shadow: inset 4px 4px 8px #b8bec6, inset -4px -4px 8px #ffffff;
    }
    .locker.selected {
        background: #d1fae5;
        box-shadow: inset 6px 6px 12px #a3b8c2, inset -6px -6px 12px #ffffff;
    }
    .button {
        display: block;
        width: 100%;
        padding: 12px;
        margin-top: 8px;
        border: none;
        border-radius: 12px;
        background: #ecf0f3;
        box-shadow: 6px 6px 12px #b8bec6, -6px -6px 12px #ffffff;
        color: #333;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        box-shadow: none;
    }
    .button:active {
        box-shadow: inset 6px 6px 12px #b8bec6, inset -6px -6px 12px #ffffff;
    }
    .blue { color: #3b82f6; }
    .green { color: #10b981; }
    .red { color: #ef4444; }
    .gray { color: #6b7280; }
    .black { color: #111827; }
    @media (max-width: 768px) {
        .main-container {
            flex-direction: column;
            align-items: center;
        }
    }
</style>


</head>
<body>
     
<!-- RFID & Locker Management (Now at the Top) -->
<div class="rfid-container">
        <div class="container">
            <h2>RFID & Locker Management</h2>
            <button id="scanBtn" class="button blue">Scan RFID</button>
            <p id="rfidData"></p>
        </div>
    </div>

    <div class="main-container">
        <!-- Locker Container -->
        <div class="lockers-container">
            <div class="container">
                <h2>Locker Control Panel</h2>
                <div id="locker-container" class="grid"></div>
                <p id="status">Select a locker to activate the tools.</p>
            </div>
            <button id="addLockerBtn" class="button blue">➕ Add Locker</button>
        </div>

        <!-- Button Group Container -->
        <div class="buttons-container">
            <!-- Locker Actions -->
            <div class="container">
                <h2>Locker Actions</h2>
                <button id="assignBtn" class="button blue" disabled>➕ Assign</button>
                <button id="changeUserBtn" class="button green" disabled>🔄 Change User</button>
                <button id="clearBtn" class="button red" disabled>🗑️ Clear</button>
                <button id="updateStatusBtn" class="button gray" disabled>🟢/🔴 Update Status</button>
                <button id="deleteLockerBtn" class="button black" disabled>❌ Delete</button>
            </div>
        </div>
    </div>
</body>
<script src="websocket_handler.js"></script>
</html>
