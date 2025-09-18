<?php
session_start();
include('../connect.php');
header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please log in.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$appointment_id = $_POST['appointment_id'] ?? null;

if (!$appointment_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid request: Appointment ID is missing.']);
    exit;
}

// Prepare a statement to update the status to 'Cancelled'
// This also verifies that the appointment belongs to the logged-in user and is in a cancellable state
$stmt = $conn->prepare(
    "UPDATE appointments 
     SET status = 'Cancelled' 
     WHERE id = ? AND user_id = ? AND status IN ('pending', 'confirmed')"
);
$stmt->bind_param("ii", $appointment_id, $user_id);

if ($stmt->execute()) {
    // Check if any row was actually updated
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Appointment cancelled successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not cancel appointment. It may have already been completed or cancelled.']);
    }
} else {
    // Database execution error
    echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
}

$stmt->close();
$conn->close();
?>