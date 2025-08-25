<?php
session_start();
include('../connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = (int)$_POST['appointment_id'];
    $user_id = $_SESSION['user_id'];

    // Fetch the fee for the appointment
    $stmt = $conn->prepare("SELECT fee FROM appointments WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $appointment_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $amount = $row['fee'];

        // Insert into payments table
        $insert = $conn->prepare("INSERT INTO payments (appointment_id, user_id, amount, payment_status, created_at) VALUES (?, ?, ?, 'confirmed', NOW())");
        $insert->bind_param("iid", $appointment_id, $user_id, $amount);

        if ($insert->execute()) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "not_found";
    }
}
?>
