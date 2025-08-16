<?php
session_start();
include('../connect.php');
header('Content-Type: application/json');

// Validate user session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Validate POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$payment_status = trim($_POST['payment_status'] ?? ''); // 'confirm' or 'cancelled'

// Fetch latest appointment for this user
$appointment_id = 0;
$amount = 0;
$result = $conn->query("SELECT id, fees FROM appointments WHERE user_id = $user_id ORDER BY id DESC LIMIT 1");
if ($row = $result->fetch_assoc()) {
    $appointment_id = (int)$row['id'];
    $amount = floatval($row['fees']);
}

// Basic validation
if ($appointment_id <= 0 || $amount <= 0 || !in_array($payment_status, ['confirm', 'cancelled'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

// Insert payment record
$stmt = $conn->prepare("INSERT INTO payments (appointment_id, amount, payment_status, created_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("ids", $appointment_id, $amount, $payment_status);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Payment recorded']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
}
?>