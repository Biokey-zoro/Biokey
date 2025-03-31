<?php
if (isset($_POST['dark_mode'])) {
    $darkMode = $_POST['dark_mode'] === 'enabled' ? 'enabled' : 'disabled';

    // Delete the existing cookie first
    setcookie('dark_mode', '', time() - 3600, "/"); // Expire the old cookie

    // Set the new cookie
    setcookie('dark_mode', $darkMode, time() + (86400 * 365), "/"); // 1-year expiration

    echo json_encode(["status" => "success", "mode" => $darkMode]); // Send response
}
?>