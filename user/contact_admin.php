<?php
session_start();
include('../connect.php');
include('../auth_check.php'); // ensures user is logged in

// Use session user_id
$user_id = $_SESSION['user_id'] ?? null;
$name = $_SESSION['name'] ?? 'User';

if (!$user_id) {
    die("Unauthorized access.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    if (!empty($message)) {
        // Insert into user_messages table (you might need to create this table)
        $stmt = $conn->prepare("INSERT INTO user_messages (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $message);

        if ($stmt->execute()) {
            $success = "Message sent successfully! We'll get back to you soon.";
        } else {
            $error = "Database error: Unable to send message.";
        }
        $stmt->close();
    } else {
        $error = "Message cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Contact Admin - SerenityConnect</title>
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

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
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
    max-width: 800px;
    margin: 2rem auto;
    background: #ffffff;
    padding: 2rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    flex-grow: 1;
}

.header {
    text-align: center;
    margin-bottom: 2rem;
}

.header h1 {
    color: var(--primary);
    margin-bottom: 0.5rem;
    font-size: 2.2rem;
}

.header p {
    color: #666;
    font-size: 1.1rem;
}

.contact-info {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: var(--radius);
    margin-bottom: 2rem;
    border-left: 4px solid var(--primary);
}

.contact-info h3 {
    color: var(--primary);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.contact-info ul {
    list-style: none;
    padding-left: 0;
}

.contact-info li {
    margin-bottom: 0.8rem;
    display: flex;
    align-items: flex-start;
    gap: 0.8rem;
}

.contact-info i {
    color: var(--primary);
    margin-top: 0.2rem;
}

form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

label {
    font-weight: 600;
    color: var(--primary);
}

textarea {
    resize: none;
    min-height: 150px;
    padding: 1rem;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 1rem;
    font-family: 'Inter', sans-serif;
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
    padding: 1rem 2rem;
    border-radius: var(--radius);
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
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
    padding: 1rem;
    border-radius: var(--radius);
    text-align: center;
    margin-bottom: 1.5rem;
}

.error {
    background: #fdecea;
    color: #c62828;
    border: 1px solid #f5c6c6;
    padding: 1rem;
    border-radius: var(--radius);
    text-align: center;
    margin-bottom: 1.5rem;
}

.message-history {
    margin-top: 3rem;
}

.message-history h3 {
    color: var(--primary);
    margin-bottom: 1rem;
    border-bottom: 2px solid var(--primary);
    padding-bottom: 0.5rem;
}

.messages-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.message {
    padding: 1.2rem;
    border-radius: var(--radius);
    border: 1px solid var(--border);
}

.user-msg {
    background: #e6f3ff;
    border-left: 4px solid #007bff;
}

.admin-msg {
    background: #f0f9f0;
    border-left: 4px solid var(--primary);
}

.message-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.message-sender {
    font-weight: 600;
    color: var(--primary);
}

.message-time {
    color: #666;
    font-size: 0.9rem;
}

.message-content {
    color: #333;
    line-height: 1.5;
}

.no-messages {
    text-align: center;
    color: #666;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: var(--radius);
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--primary);
    text-decoration: none;
    margin-bottom: 1rem;
    font-weight: 500;
}

.back-link:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .container {
        margin: 1rem;
        padding: 1.5rem;
    }
    
    .header h1 {
        font-size: 1.8rem;
    }
}
</style>
</head>
<body>
    <div class="container">
        <a href="dash.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <div class="header">
            <h1><i class="fas fa-headset"></i> Contact Admin Support</h1>
            <p>We're here to help! Send us a message and we'll respond as soon as possible.</p>
        </div>

        <?php if (!empty($error)) echo "<div class='error'>⚠️ $error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='success'>✅ $success</div>"; ?>

        <div class="contact-info">
            <h3><i class="fas fa-info-circle"></i> How can we help you?</h3>
            <ul>
                <li><i class="fas fa-question-circle"></i> <strong>Account Issues:</strong> Login problems, profile updates, or account settings</li>
                <li><i class="fas fa-calendar-times"></i> <strong>Booking Assistance:</strong> Help with appointments, cancellations, or rescheduling</li>
                <li><i class="fas fa-exclamation-triangle"></i> <strong>Technical Support:</strong> Website errors, payment issues, or platform problems</li>
                <li><i class="fas fa-comments"></i> <strong>General Inquiries:</strong> Questions about our services, therapists, or features</li>
            </ul>
        </div>

        <form method="post" action="">
            <div class="form-group">
                <label for="message">Your Message:</label>
                <textarea name="message" id="message" placeholder="Please describe your issue or question in detail. Our support team typically responds within 24 hours." required></textarea>
            </div>
            <button type="submit" class="btn">
                <i class="fas fa-paper-plane"></i> Send Message to Admin
            </button>
        </form>

        <div class="message-history">
            <h3><i class="fas fa-history"></i> Message History</h3>
            <div class="messages-list">
                <?php
                // Fetch message history for this user
                $stmt_messages = $conn->prepare("
                    SELECT message, admin_reply, created_at, replied_at 
                    FROM user_messages 
                    WHERE user_id = ? 
                    ORDER BY created_at DESC
                ");
                $stmt_messages->bind_param("i", $user_id);
                $stmt_messages->execute();
                $result_messages = $stmt_messages->get_result();

                if ($result_messages->num_rows > 0) {
                    while ($row = $result_messages->fetch_assoc()) {
                        // Display user's message
                        echo '<div class="message user-msg">';
                        echo '<div class="message-header">';
                        echo '<span class="message-sender">You</span>';
                        echo '<span class="message-time">' . htmlspecialchars($row['created_at']) . '</span>';
                        echo '</div>';
                        echo '<div class="message-content">' . nl2br(htmlspecialchars($row['message'])) . '</div>';
                        echo '</div>';

                        // Display admin reply if it exists
                        if (!empty($row['admin_reply'])) {
                            echo '<div class="message admin-msg">';
                            echo '<div class="message-header">';
                            echo '<span class="message-sender">Admin Support</span>';
                            echo '<span class="message-time">' . htmlspecialchars($row['replied_at']) . '</span>';
                            echo '</div>';
                            echo '<div class="message-content">' . nl2br(htmlspecialchars($row['admin_reply'])) . '</div>';
                            echo '</div>';
                        }
                    }
                } else {
                    echo '<div class="no-messages">';
                    echo '<i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; color: #ccc;"></i>';
                    echo '<p>No messages yet. Send your first message to get help from our support team!</p>';
                    echo '</div>';
                }
                
                $stmt_messages->close();
                $conn->close();
                ?>
            </div>
        </div>
    </div>
</body>
</html>