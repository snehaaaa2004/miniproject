<?php
session_start();
include('../connect.php');

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $therapist_id = $_POST['therapist_id'] ?? null;
    $appointment_date = $_POST['appointment_date'] ?? null;
    $appointment_time = $_POST['appointment_time'] ?? null;
    $mode = $_POST['mode'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $description = $_POST['description'] ?? ''; // optional

    if (!$therapist_id || !$appointment_date || !$appointment_time || !$mode || !$phone) {
        die("All required fields must be filled.");
    }

    // Convert 12-hour time to 24-hour format
    $time24hr = date("H:i:s", strtotime($appointment_time));

    // Default status
    $status = "Pending";

    // Insert appointment
    $stmt = $conn->prepare("INSERT INTO appointments (user_id, therapist_id, phone, appointment_date, appointment_time, mode, status, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $user_id, $therapist_id, $phone, $appointment_date, $time24hr, $mode, $status, $description);


    if ($stmt->execute()) {
        echo "<script>alert('Appointment booked successfully!'); window.location.href='user.php';</script>";
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
