<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Stronger cache prevention headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
   header("Location: /mini%20proj/serenity/login.php"); 
    exit();
}

// Additional security - regenerate session ID periodically
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) { // 30 minutes
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}
?>