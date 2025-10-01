<?php
// filepath: c:\xampp\htdocs\serenity\admin\admin_usermessages.php
session_start();
include('../connect.php');
include('../auth_check.php'); // Ensure admin is logged in

// Check for unauthorized access early
if (!isset($_SESSION['role']) || strcasecmp(trim($_SESSION['role']), 'admin') !== 0) {
    die("Unauthorized access.");
}

// ===================================================================
// 1. AJAX REPLY HANDLER (Runs ONLY for POST requests from JavaScript)
// ===================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_id'], $_POST['reply_message'])) {
    
    // Set header for JSON response (Crucial for AJAX)
    header('Content-Type: application/json');
    
    $reply_id = intval($_POST['reply_id']);
    $reply_message = trim($_POST['reply_message']);
    
    // Server-side validation
    if (empty($reply_message)) {
        echo json_encode(['success' => false, 'message' => 'Reply message cannot be empty.']);
        $conn->close();
        exit;
    }

    $sql = "UPDATE user_messages 
            SET admin_reply = ?, replied_at = NOW() 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // 's' for string (reply_message), 'i' for integer (reply_id)
        $stmt->bind_param("si", $reply_message, $reply_id); 
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Message not found or already replied to.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Database execution error: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Database prepare error: ' . $conn->error]);
    }
    
    // Close connection and exit immediately after sending JSON response
    $conn->close();
    exit;
}
// ===================================================================
// END AJAX REPLY HANDLER
// ===================================================================


// 2. STANDARD DELETION HANDLER (Uses a full form post and redirect)
if (isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    
    // Prepare statement to securely delete the message
    $stmt_del = $conn->prepare("DELETE FROM user_messages WHERE id = ?");
    $stmt_del->bind_param("i", $delete_id);
    $stmt_del->execute();
    $stmt_del->close();
    
    // Redirect to clear POST data and prevent resubmission on refresh
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit;
}

// 3. FETCH DATA (Runs for standard page load only)
$stmt = $conn->prepare("
    SELECT um.id, um.user_id, um.message, um.admin_reply, um.created_at, um.replied_at, u.name AS user_name, u.email AS user_email
    FROM user_messages um
    JOIN users u ON um.user_id = u.id
    ORDER BY um.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close(); // Close connection after fetching data (before HTML output)
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Messages - Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --primary: #2e4d3d;
    --primary-dark: #1e3b2b;
    --background: #f8f6f3;
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
    padding: 2rem;
}

.container {
    max-width: 1000px;
    margin: auto;
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

.stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--white);
    padding: 1.5rem;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    text-align: center;
    border-left: 4px solid var(--primary);
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary);
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

.message-card {
    background: var(--white);
    border-radius: var(--radius);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow);
    border-left: 5px solid var(--primary);
    transition: var(--transition);
}

.message-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.12);
}

.message-meta {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border);
}

.user-info {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 0.5rem;
}

.user-info strong {
    color: var(--primary);
}

.message-text {
    font-size: 1rem;
    color: #333;
    margin-bottom: 1rem;
    line-height: 1.6;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: var(--radius);
}

.admin-reply {
    background: #e6f7ed;
    padding: 1rem;
    border-radius: var(--radius);
    margin-top: 1rem;
    border-left: 3px solid var(--primary);
}

.admin-reply strong {
    color: var(--primary-dark);
}

.reply-time {
    font-size: 0.8rem;
    color: #666;
    margin-top: 0.5rem;
}

.actions {
    margin-top: 1rem;
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: var(--radius);
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-reply {
    background-color: #007bff;
    color: white;
}

.btn-reply:hover {
    background-color: #0056b3;
    transform: translateY(-1px);
}

.btn-delete {
    background-color: #dc3545;
    color: white;
}

.btn-delete:hover {
    background-color: #b02a37;
    transform: translateY(-1px);
}

.btn-view {
    background-color: #6c757d;
    color: white;
}

.btn-view:hover {
    background-color: #545b62;
    transform: translateY(-1px);
}

.no-messages {
    text-align: center;
    padding: 3rem;
    color: #666;
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
}

.no-messages i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #ccc;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    backdrop-filter: blur(2px);
}

.modal-content {
    background-color: #fefefe;
    margin: 10% auto;
    padding: 2rem;
    border-radius: var(--radius);
    width: 90%;
    max-width: 600px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    position: relative;
}

.modal-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.modal-header h3 {
    color: var(--primary);
    margin: 0;
}

.close-btn {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    background: none;
    border: none;
    padding: 0;
}

.close-btn:hover {
    color: #000;
}

.modal textarea {
    width: 100%;
    min-height: 150px;
    padding: 1rem;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 1rem;
    font-family: 'Inter', sans-serif;
    resize: vertical;
    margin-bottom: 1.5rem;
    outline: none;
    transition: var(--transition);
}

.modal textarea:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(46, 77, 61, 0.1);
}

.modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
}

.status-badge {
    display: inline-block;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    margin-left: 1rem;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-replied {
    background: #d1edff;
    color: #0c5460;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

@media (max-width: 768px) {
    body {
        padding: 1rem;
    }
    
    .container {
        max-width: 100%;
    }
    
    .stats {
        grid-template-columns: 1fr;
    }
    
    .actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .modal-content {
        margin: 5% auto;
        padding: 1.5rem;
    }
}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1><i class="fas fa-comments"></i> User Messages</h1>
        <p>Manage and respond to user inquiries and support requests</p>
    </div>

    <?php
    // Calculate stats
    $total_messages = count($messages);
    $replied_messages = count(array_filter($messages, function($msg) {
        return !empty($msg['admin_reply']);
    }));
    $pending_messages = $total_messages - $replied_messages;
    ?>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-number"><?php echo $total_messages; ?></div>
            <div class="stat-label">Total Messages</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $replied_messages; ?></div>
            <div class="stat-label">Replied</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $pending_messages; ?></div>
            <div class="stat-label">Pending Reply</div>
        </div>
    </div>

    <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $msg): ?>
            <div class="message-card" data-id="<?= htmlspecialchars($msg['id']) ?>">
                <div class="message-meta">
                    <div class="user-info">
                        <div><strong>User:</strong> <?= htmlspecialchars($msg['user_name']) ?></div>
                        <div><strong>Email:</strong> <?= htmlspecialchars($msg['user_email']) ?></div>
                        <div><strong>User ID:</strong> <?= htmlspecialchars($msg['user_id']) ?></div>
                        <div><strong>Sent:</strong> <?= htmlspecialchars($msg['created_at']) ?></div>
                        <span class="status-badge <?php echo empty($msg['admin_reply']) ? 'status-pending' : 'status-replied'; ?>">
                            <?php echo empty($msg['admin_reply']) ? 'Pending Reply' : 'Replied'; ?>
                        </span>
                    </div>
                </div>
                
                <div class="message-text">
                    <?= nl2br(htmlspecialchars($msg['message'])) ?>
                </div>
                
                <?php if(!empty($msg['admin_reply'])): ?>
                    <div class="admin-reply">
                        <strong><i class="fas fa-reply"></i> Admin Reply:</strong><br>
                        <?= nl2br(htmlspecialchars($msg['admin_reply'])) ?>
                        <div class="reply-time">
                            <small>Replied: <?= htmlspecialchars($msg['replied_at']) ?></small>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="actions">
                    <button class="btn btn-reply" onclick="openReplyModal(<?= $msg['id'] ?>)">
                        <i class="fas fa-reply"></i> <?php echo empty($msg['admin_reply']) ? 'Reply' : 'Update Reply'; ?>
                    </button>
                    <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this message? This action cannot be undone.');">
                        <input type="hidden" name="delete_id" value="<?= htmlspecialchars($msg['id']) ?>">
                        <button type="submit" class="btn btn-delete">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                    <?php if(!empty($msg['admin_reply'])): ?>
                        
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-messages">
            <i class="fas fa-inbox"></i>
            <h3>No Messages Yet</h3>
            <p>User messages will appear here when users contact support.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Reply Modal -->
<div id="replyModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-reply"></i> Reply to User</h3>
            <button class="close-btn" onclick="closeReplyModal()">&times;</button>
        </div>
        <form id="replyForm" onsubmit="submitReply(event)">
            <input type="hidden" id="replyId" name="reply_id">
            
            <div id="userMessagePreview" style="background: #f8f9fa; padding: 1rem; border-radius: var(--radius); margin-bottom: 1rem; font-style: italic; color: #666;">
                <!-- User message will be displayed here -->
            </div>
            
            <textarea name="reply_message" id="replyText" placeholder="Type your reply to the user here..."></textarea>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-view" onclick="closeReplyModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-reply">
                    <i class="fas fa-paper-plane"></i> Send Reply
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentMessageId = null;

function openReplyModal(messageId) {
    currentMessageId = messageId;
    document.getElementById('replyId').value = messageId;
    document.getElementById('replyText').value = '';
    
    // Find the message card and get the user's message
    const messageCard = document.querySelector(`.message-card[data-id="${messageId}"]`);
    const userMessage = messageCard.querySelector('.message-text').textContent;
    const userName = messageCard.querySelector('.user-info div:first-child').textContent.replace('User:', '').trim();
    
    document.getElementById('userMessagePreview').textContent = `User ${userName}: "${userMessage}"`;
    document.getElementById('replyModal').style.display = 'block';
}

function closeReplyModal() {
    document.getElementById('replyModal').style.display = 'none';
    currentMessageId = null;
}

function submitReply(event) {
    event.preventDefault();
    
    const message = document.getElementById('replyText').value.trim();
    const replyId = document.getElementById('replyId').value;
    
    // Client-side validation
    if (!message) {
        alert('Please type a reply before sending.');
        document.getElementById('replyText').focus();
        return;
    }

    if (!replyId) {
        alert('Error: Message ID is missing. Cannot send reply.');
        return;
    }
    
    // Create FormData
    const formData = new FormData();
    formData.append('reply_id', replyId);
    formData.append('reply_message', message);
    
    // Send AJAX request
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        const contentType = response.headers.get("content-type");
        
        if (contentType && contentType.includes("application/json")) {
            return response.json();
        } else {
            return response.text().then(errorText => {
                console.error("Non-JSON Response:", errorText);
                throw new Error('Server returned non-JSON response');
            });
        }
    })
    .then(result => {
        if (result.success) {
            alert('Reply sent successfully! The page will now reload.');
            window.location.reload();
        } else {
            alert('Error: ' + (result.message || 'Could not send reply.'));
        }
    })
    .catch(error => {
        console.error('AJAX error:', error);
        alert('AJAX network error: ' + error.message);
    });
}

function viewUserProfile(userId) {
    // Redirect to user profile page (you can modify this URL as needed)
    window.open(`../admin/view_user.php?id=${userId}`, '_blank');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('replyModal');
    if (event.target === modal) {
        closeReplyModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeReplyModal();
    }
});
</script>
</body>
</html>