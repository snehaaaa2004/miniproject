<?php
session_start();
// 1. MUST INCLUDE DATABASE CONNECTION
include ('../connect.php'); 

// Path to your messages file
$file = __DIR__ . '/../messages.json';

// Use 'user_id' as established in previous steps
$therapist_id = $_SESSION['user_id'] ?? null; 
$message = trim($_POST['message'] ?? '');

if ($therapist_id && $message !== '') {
    
    // --- DATABASE LOOKUP TO GET THE NAME ---
    $therapist_name = 'Unknown Therapist';
    
    // Prepare statement to look up the name using the user ID
    // Use 's' (string) for bind_param to handle both integer IDs (40) and string IDs (THR017)
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    
    if ($stmt && $stmt->bind_param("s", $therapist_id) && $stmt->execute()) {
        $result = $stmt->get_result();
        if ($user = $result->fetch_assoc()) {
            // The name field exists in the users table, so fetch it here
            $therapist_name = $user['name']; 
        }
        $stmt->close();
    }
    $conn->close(); // Close DB connection immediately after use
    // --- END DATABASE LOOKUP ---

    // Load existing messages
    $messages = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    // Add new message with the fetched name
    $messages[] = [
        'therapist_id' => $therapist_id,
        'therapist_name' => $therapist_name, // <-- NEW FIELD ADDED
        'message' => $message,
        'time' => date("Y-m-d H:i:s")
    ];

    // Save back
    if (file_put_contents($file, json_encode($messages, JSON_PRETTY_PRINT)) !== false) {
        header("Location: therapist_message.php?sent=1");
        exit();
    } else {
        // Error 2: File Write Failure (Permissions)
        header("Location: therapist_message.php?error=2"); 
        exit();
    }
} else {
    // Error 1: Missing ID or Message
    header("Location: therapist_message.php?error=1");
    exit();
}
?>