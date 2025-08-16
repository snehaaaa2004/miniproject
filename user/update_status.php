<?php
session_start();
include('../connect.php');

// Set header to return JSON response
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

// 1. Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'User not authenticated.';
    echo json_encode($response);
    exit();
}

// 2. Check for required POST data
if (!isset($_POST['appointment_id']) || !isset($_POST['status'])) {
    $response['message'] = 'Missing required data.';
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user_id'];
$appointment_id = $_POST['appointment_id'];
$new_status = $_POST['status'];

// 3. Prepare and execute the update query
$query = "UPDATE appointments SET status = ? WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);

// Check if the prepare statement failed
if ($stmt === false) {
    $response['message'] = 'Database query preparation failed: ' . $conn->error;
    echo json_encode($response);
    exit();
}

$stmt->bind_param("sii", $new_status, $appointment_id, $user_id);

if ($stmt->execute()) {
    // Check if any rows were affected
    if ($stmt->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'Appointment status updated successfully.';
    } else {
        $response['message'] = 'Appointment not found or status is already the same.';
    }
} else {
    $response['message'] = 'Failed to update appointment status: ' . $stmt->error;
}

$stmt->close();
$conn->close();

// 4. Return the final JSON response
echo json_encode($response);
?>