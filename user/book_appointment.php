<?php
session_start();
include('../connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['payment_confirmed'])) {
    $user_id          = $_SESSION['user_id'];
    $therapist_id     = mysqli_real_escape_string($conn, $_POST['therapist_id']);
    $appointment_date = mysqli_real_escape_string($conn, $_POST['appointment_date']);
    $appointment_time = mysqli_real_escape_string($conn, $_POST['appointment_time']);
    $mode             = mysqli_real_escape_string($conn, $_POST['mode']);
    $phone            = mysqli_real_escape_string($conn, $_POST['phone']);
    $description      = mysqli_real_escape_string($conn, $_POST['description']);
    $amount           = mysqli_real_escape_string($conn, $_POST['amount']);

    // By default mark status as "Pending"
    $status = "pending";

    $query = "INSERT INTO appointments 
                (user_id, therapist_id, phone, appointment_date, appointment_time, mode, amount, status, description, created_at) 
              VALUES 
                ('$user_id', '$therapist_id', '$phone', '$appointment_date', '$appointment_time', '$mode', '$amount', '$status', '$description', NOW())";

    if (mysqli_query($conn, $query)) {
        // ✅ Redirect to confirmation or bookings page
        header("Location: view_bookings.php?success=1");
        exit();
    } else {
        die("Database Error: " . mysqli_error($conn));
    }
} else {
    header("Location: search.php");
    exit();
}
?>
