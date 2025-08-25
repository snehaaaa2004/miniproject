<?php
session_start();
include('../connect.php');
header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enhanced logging function
function logError($message, $data = null, $error = null) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message";
    
    if ($data !== null) {
        $logEntry .= " | Data: " . json_encode($data);
    }
    
    if ($error !== null) {
        $logEntry .= " | Error: " . $error;
    }
    
    $logEntry .= PHP_EOL;
    
    // Log to file (make sure this directory is writable)
    error_log($logEntry, 3, 'review_errors.log');
    
    // Also log to PHP error log
    error_log($logEntry);
}

// Check if database connection exists
if (!isset($conn) || $conn->connect_error) {
    logError('Database connection failed', ['connect_error' => $conn->connect_error ?? 'Connection object not found']);
    echo json_encode([
        'success' => false, 
        'message' => 'Database connection failed',
        'debug' => 'Check database configuration in connect.php'
    ]);
    exit();
}

// Check session
if (!isset($_SESSION['user_id'])) {
    logError('User not logged in', ['session' => array_keys($_SESSION)]);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logError('Invalid request method', ['method' => $_SERVER['REQUEST_METHOD']]);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

logError('Review request received', [
    'user_id' => $user_id,
    'action' => $action,
    'post_keys' => array_keys($_POST)
]);

// ---------- ADD REVIEW ----------
if ($action === 'add') {
    $therapist_id = trim($_POST['therapist_id'] ?? '');
    $rating = (int) ($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    $appointment_id = (int) ($_POST['appointment_id'] ?? 0);

    logError('Add review attempt', [
        'therapist_id' => $therapist_id,
        'rating' => $rating,
        'comment_length' => strlen($comment),
        'appointment_id' => $appointment_id
    ]);

    // Validation
    $errors = [];
    if (empty($therapist_id)) $errors[] = 'Therapist ID is required';
    if ($appointment_id <= 0) $errors[] = 'Valid appointment ID is required';
    if ($rating < 1 || $rating > 5) $errors[] = 'Rating must be between 1 and 5';
    if (strlen($comment) < 5) $errors[] = 'Comment must be at least 5 characters long';
    
    if (!empty($errors)) {
        logError('Add review validation failed', ['errors' => $errors]);
        echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
        exit();
    }

    try {
        // Check appointment ownership
        $checkSql = "SELECT id FROM appointments WHERE id = ? AND user_id = ? AND therapist_id = ?";
        $check = $conn->prepare($checkSql);
        
        if (!$check) {
            throw new Exception('Failed to prepare appointment check query: ' . $conn->error);
        }
        
        $check->bind_param("iis", $appointment_id, $user_id, $therapist_id);
        
        if (!$check->execute()) {
            throw new Exception('Failed to execute appointment check: ' . $check->error);
        }
        
        $res = $check->get_result();
        if ($res->num_rows === 0) {
            logError('Appointment validation failed', [
                'appointment_id' => $appointment_id,
                'user_id' => $user_id,
                'therapist_id' => $therapist_id
            ]);
            echo json_encode(['success' => false, 'message' => 'Invalid appointment or not authorized']);
            exit();
        }
        $check->close();
        
        // Check if review already exists
        $checkReviewSql = "SELECT id FROM reviews WHERE appointment_id = ?";
        $checkReview = $conn->prepare($checkReviewSql);
        
        if (!$checkReview) {
            throw new Exception('Failed to prepare review check query: ' . $conn->error);
        }
        
        $checkReview->bind_param("i", $appointment_id);
        
        if (!$checkReview->execute()) {
            throw new Exception('Failed to execute review check: ' . $checkReview->error);
        }
        
        $existing = $checkReview->get_result();
        if ($existing->num_rows > 0) {
            logError('Review already exists', ['appointment_id' => $appointment_id]);
            echo json_encode(['success' => false, 'message' => 'Review already exists for this appointment']);
            exit();
        }
        $checkReview->close();
        
        // Insert review
        $insertSql = "INSERT INTO reviews (user_id, therapist_id, appointment_id, rating, comment, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $insert = $conn->prepare($insertSql);
        
        if (!$insert) {
            throw new Exception('Failed to prepare insert query: ' . $conn->error);
        }
        
        $insert->bind_param("isiss", $user_id, $therapist_id, $appointment_id, $rating, $comment);
        
        if (!$insert->execute()) {
            throw new Exception('Failed to execute insert: ' . $insert->error);
        }
        
        $review_id = $conn->insert_id;
        $insert->close();
        
        logError('Review added successfully', ['review_id' => $review_id]);
        echo json_encode([
            'success' => true, 
            'message' => 'Review added successfully',
            'review_id' => $review_id
        ]);
        
    } catch (Exception $e) {
        logError('Add review error', [], $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error saving review',
            'debug' => $e->getMessage()
        ]);
    }
    exit();
}

// ---------- EDIT REVIEW ----------
if ($action === 'edit') {
    $review_id = (int) ($_POST['review_id'] ?? 0);
    $rating = (int) ($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    logError('Edit review attempt', [
        'review_id' => $review_id,
        'rating' => $rating,
        'comment_length' => strlen($comment),
        'user_id' => $user_id
    ]);

    // Validation
    $errors = [];
    if ($review_id <= 0) $errors[] = 'Valid review ID is required';
    if ($rating < 1 || $rating > 5) $errors[] = 'Rating must be between 1 and 5';
    if (strlen($comment) < 5) $errors[] = 'Comment must be at least 5 characters long';
    
    if (!empty($errors)) {
        logError('Edit review validation failed', ['errors' => $errors]);
        echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
        exit();
    }

    try {
        // Check ownership with detailed logging
        $checkSql = "SELECT id, user_id FROM reviews WHERE id = ?";
        $check = $conn->prepare($checkSql);
        
        if (!$check) {
            throw new Exception('Failed to prepare ownership check query: ' . $conn->error);
        }
        
        $check->bind_param("i", $review_id);
        
        if (!$check->execute()) {
            throw new Exception('Failed to execute ownership check: ' . $check->error);
        }
        
        $res = $check->get_result();
        $reviewData = $res->fetch_assoc();
        
        logError('Review ownership check', [
            'review_found' => $reviewData !== null,
            'review_user_id' => $reviewData['user_id'] ?? null,
            'current_user_id' => $user_id,
            'review_id' => $review_id
        ]);
        
        if (!$reviewData) {
            echo json_encode(['success' => false, 'message' => 'Review not found']);
            exit();
        }
        
        if ((int)$reviewData['user_id'] !== $user_id) {
            logError('Review ownership mismatch', [
                'review_user_id' => $reviewData['user_id'],
                'current_user_id' => $user_id
            ]);
            echo json_encode(['success' => false, 'message' => 'Not authorized to edit this review']);
            exit();
        }
        
        $check->close();

        // Update review
        $updateSql = "UPDATE reviews SET rating = ?, comment = ? WHERE id = ? AND user_id = ?";
        $update = $conn->prepare($updateSql);
        
        if (!$update) {
            throw new Exception('Failed to prepare update query: ' . $conn->error);
        }
        
        $update->bind_param("isii", $rating, $comment, $review_id, $user_id);
        
        if (!$update->execute()) {
            throw new Exception('Failed to execute update: ' . $update->error);
        }
        
        $affected_rows = $update->affected_rows;
        $update->close();
        
        logError('Review update completed', [
            'affected_rows' => $affected_rows,
            'review_id' => $review_id
        ]);
        
        if ($affected_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'No changes made or review not found']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Review updated successfully']);
        }
        
    } catch (Exception $e) {
        logError('Edit review error', [], $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Database error occurred',
            'debug' => $e->getMessage()
        ]);
    }
    exit();
}

// ---------- DELETE REVIEW ----------
if ($action === 'delete') {
    $review_id = (int) ($_POST['review_id'] ?? 0);

    logError('Delete review attempt', ['review_id' => $review_id, 'user_id' => $user_id]);

    if ($review_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid review ID']);
        exit();
    }

    try {
        // Check ownership
        $checkSql = "SELECT id FROM reviews WHERE id = ? AND user_id = ?";
        $check = $conn->prepare($checkSql);
        
        if (!$check) {
            throw new Exception('Failed to prepare ownership check query: ' . $conn->error);
        }
        
        $check->bind_param("ii", $review_id, $user_id);
        
        if (!$check->execute()) {
            throw new Exception('Failed to execute ownership check: ' . $check->error);
        }
        
        $res = $check->get_result();
        if ($res->num_rows === 0) {
            logError('Delete ownership check failed', ['review_id' => $review_id, 'user_id' => $user_id]);
            echo json_encode(['success' => false, 'message' => 'Review not found or not authorized']);
            exit();
        }
        $check->close();

        // Delete review
        $deleteSql = "DELETE FROM reviews WHERE id = ? AND user_id = ?";
        $delete = $conn->prepare($deleteSql);
        
        if (!$delete) {
            throw new Exception('Failed to prepare delete query: ' . $conn->error);
        }
        
        $delete->bind_param("ii", $review_id, $user_id);
        
        if (!$delete->execute()) {
            throw new Exception('Failed to execute delete: ' . $delete->error);
        }
        
        $affected_rows = $delete->affected_rows;
        $delete->close();
        
        logError('Review delete completed', [
            'affected_rows' => $affected_rows,
            'review_id' => $review_id
        ]);
        
        if ($affected_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Review not found or already deleted']);
        } else {
            echo json_encode(['success' => true, 'message' => 'Review deleted successfully']);
        }
        
    } catch (Exception $e) {
        logError('Delete review error', [], $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Database error occurred',
            'debug' => $e->getMessage()
        ]);
    }
    exit();
}

// Invalid action
logError('Invalid action received', ['action' => $action, 'valid_actions' => ['add', 'edit', 'delete']]);
echo json_encode(['success' => false, 'message' => 'Invalid action specified']);
exit();
?>