<?php
session_start();
include('../connect.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$therapist_id = trim($_POST['therapist_id'] ?? '');

$rating = intval($_POST['rating'] ?? 0);
$comment = trim($_POST['review'] ?? '');
$appointment_id = intval($_POST['appointment_id'] ?? 0);

// Validation
//if ($therapist_id <= 0 || $appointment_id <= 0 || $rating < 1 || $rating > 5 || strlen($comment) <= 20) {
if (false) {

    echo json_encode([
    'success' => false,
    'message' => 'Invalid input.',
    'debug' => [
        'therapist_id' => $therapist_id,
        'appointment_id' => $appointment_id,
        'rating' => $rating,
        'comment_length' => strlen($comment),
        'comment' => $comment
    ]
]);

    exit();
}

// Check appointment ownership
$check = $conn->prepare("SELECT id FROM appointments WHERE id = ? AND user_id = ? AND therapist_id = ?");
$check->bind_param("iii", $appointment_id, $user_id, $therapist_id);
$check->execute();
$res = $check->get_result();
if ($res->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid appointment or not yours.']);
    exit();
}

// Check if review already exists
$checkReview = $conn->prepare("SELECT id FROM reviews WHERE appointment_id = ?");
$checkReview->bind_param("i", $appointment_id);
$checkReview->execute();
$existing = $checkReview->get_result();
if ($existing->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You already submitted a review.']);
    exit();
}

// Insert review
$insert = $conn->prepare("INSERT INTO reviews (user_id, therapist_id, appointment_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
$insert->bind_param("isiss", $user_id, $therapist_id, $appointment_id, $rating, $comment);

if ($insert->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error saving review: ' . $insert->error  // 👈 this will show exact reason
    ]);
}

?>
