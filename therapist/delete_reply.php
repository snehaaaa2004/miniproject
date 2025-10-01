<?php
// filepath: c:\xampp\htdocs\serenity\therapist\delete_reply.php
session_start();
header('Content-Type: application/json');
include('../connect.php');

// --- Security Check: Ensure a therapist is logged in ---
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

// Get therapist's primary ID to ensure they can only delete their own replies
$stmt = $conn->prepare("SELECT id FROM therapists WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$therapist_data = $stmt->get_result()->fetch_assoc();
$therapist_id = $therapist_data['id'] ?? null;

if (!$therapist_id) {
    echo json_encode(['success' => false, 'message' => 'Therapist profile not found.']);
    exit;
}
// --- End Security Check ---

$file = __DIR__ . '/../admin_replies.json';
$reply_id_to_delete = $_POST['reply_id'] ?? null;

if (!$reply_id_to_delete) {
    echo json_encode(['success' => false, 'message' => 'Invalid request: Reply ID is missing.']);
    exit;
}

$replies = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
if (!is_array($replies)) $replies = [];

$updated_replies = [];
$found = false;

foreach ($replies as $reply) {
    // We only delete the message if the timestamp matches AND it belongs to the logged-in therapist
    // Cast both IDs to integers to ensure a correct comparison
    if ($reply['time'] === $reply_id_to_delete && (int)$reply['therapist_id'] === (int)$therapist_id) {
        $found = true; // Mark as found, but don't add it to the new array
    } else {
        $updated_replies[] = $reply; // Keep this reply
    }
}

if ($found) {
    if (file_put_contents($file, json_encode($updated_replies, JSON_PRETTY_PRINT)) !== false) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update replies file.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Reply not found or you do not have permission to delete it.']);
}
?>