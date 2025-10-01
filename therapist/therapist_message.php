<?php
session_start();
include('../connect.php');
include('../auth_check.php'); // ensures user/therapist is logged in

// Use session user_id as therapist ID
$therapist_id = $_SESSION['user_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!$therapist_id || !$user_id) {
    die("Unauthorized access.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO therapist_messages (therapist_id, user_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $therapist_id, $user_id, $message);

        if ($stmt->execute()) {
            header("Location: therapist_message.php?sent=1");
            exit;
        } else {
            $error = "Database error: Unable to send message.";
        }
    } else {
        $error = "Message cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Therapist Messages - SerenityConnect</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --primary: #2e4d3d;
    --primary-dark: #1e3b2b;
    --background: #e6f4ec;
    --white: #ffffff;
    --border: #d9e0d9;
    --shadow: 0 4px 12px rgba(0,0,0,0.08);
    --radius: 10px;
    --transition: all 0.3s ease;
}

body {
    font-family: 'Inter', sans-serif;
    background: var(--background);
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.container {
    max-width: 700px;
    margin: 2rem auto;
    background: #ffffff;
    padding: 2rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

h2 {
    text-align: center;
    color: var(--primary);
    margin-bottom: 1.5rem;
}

form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

textarea {
    resize: none;
    min-height: 120px;
    padding: 1rem;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 1rem;
    outline: none;
    transition: var(--transition);
}

textarea:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(46, 77, 61, 0.1);
}

.btn {
    background-color: var(--primary);
    color: var(--white);
    border: none;
    padding: 0.9rem 1.5rem;
    border-radius: var(--radius);
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
}

.btn:hover {
    background-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.success {
    background: #e6f7ed;
    color: #2e7d32;
    border: 1px solid #c8e6c9;
    padding: 0.8rem;
    border-radius: var(--radius);
    text-align: center;
    margin-bottom: 1rem;
}

.error {
    background: #fdecea;
    color: #c62828;
    border: 1px solid #f5c6c6;
    padding: 0.8rem;
    border-radius: var(--radius);
    text-align: center;
    margin-bottom: 1rem;
}

/* Chat style */
.messages-list {
    max-height: 400px;
    overflow-y: auto;
    margin-top: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.message {
    padding: 1rem;
    border-radius: var(--radius);
    max-width: 80%;
}

.therapist-msg {
    background: #d9f0e0;
    align-self: flex-end;
    text-align: right;
}

.admin-msg {
    background: #f0f0f0;
    align-self: flex-start;
    text-align: left;
}

.message small {
    display: block;
    color: #555;
    margin-top: 0.3rem;
    font-size: 0.8rem;
}
</style>
</head>
<body>
<div class="container">
<h2><i class="fas fa-paper-plane"></i> Send Message to Admin</h2>

<?php if (!empty($error)) echo "<div class='error'>⚠️ $error</div>"; ?>
<?php if (isset($_GET['sent'])) echo "<div class='success'>✅ Message sent successfully!</div>"; ?>

<form method="post" action="">
    <textarea name="message" placeholder="Type your message here..." required></textarea>
    <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Send</button>
</form>

<div class="messages-list">
<?php
// FIXED QUERY: Join on therapist_id instead of user_id to get the correct therapist name
$stmt_messages = $conn->prepare("
    SELECT tm.message, tm.admin_reply, tm.created_at, tm.replied_at, u.name AS therapist_name
    FROM therapist_messages tm
    JOIN users u ON tm.therapist_id = u.id
    WHERE tm.therapist_id = ?
    ORDER BY tm.created_at ASC
");
$stmt_messages->bind_param("i", $therapist_id);
$stmt_messages->execute();
$result_messages = $stmt_messages->get_result();

if ($result_messages->num_rows > 0) {
    while ($row = $result_messages->fetch_assoc()) {
        // Display therapist's message
        echo '<div class="message therapist-msg">';
        echo '<strong>You:</strong> ' . nl2br(htmlspecialchars($row['message']));
        echo '<small>Sent: ' . htmlspecialchars($row['created_at']) . '</small>';
        echo '</div>';

        // Display admin reply if it exists
        if (!empty($row['admin_reply'])) {
            echo '<div class="message admin-msg">';
            echo '<strong>Admin:</strong> ' . nl2br(htmlspecialchars($row['admin_reply']));
            echo '<small>Replied: ' . htmlspecialchars($row['replied_at']) . '</small>';
            echo '</div>';
        }
    }
} else {
    echo '<p style="text-align: center; color: #666;">No messages yet. Start a conversation with the admin!</p>';
}

$stmt_messages->close();
$conn->close();
?>
</div>
</div>
</body>
</html>