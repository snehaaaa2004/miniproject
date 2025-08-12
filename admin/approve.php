<?php
include('../connect.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ensure id is valid and safe (prevent SQL injection)
    $id = mysqli_real_escape_string($conn, $id);

    // Approve the specific therapist
    $sql = "UPDATE therapists SET approved = 1 WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Therapist approved successfully!'); window.location.href='admindash.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
$conn->close();
?>
