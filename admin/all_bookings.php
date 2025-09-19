<?php
session_start();
include('../connect.php');


// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

try {
    // Fetch Bookings with Reviews
    $sql = "SELECT 
                a.*, 
                u.name AS user_name,
                tu.name AS therapist_name,  
                t.id AS therapist_id,      
                r.rating, 
                r.comment, 
                r.created_at AS review_created_at,
                r.reply AS therapist_reply,
                ru.name AS reviewer_name
            FROM appointments a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN therapists t ON a.therapist_id = t.id
            LEFT JOIN users tu ON t.user_id = tu.id  
            LEFT JOIN reviews r ON r.appointment_id = a.id
            LEFT JOIN users ru ON ru.id = r.user_id
            ORDER BY a.appointment_date DESC";

    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    // Fetch Contact Messages
    $msgResult = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    if (!$msgResult) {
        throw new Exception("Messages query failed: " . $conn->error);
    }

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bookings & Messages - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --primary: #18481aff;
        --primary-dark: #0d3510ff;
        --accent: #265228ff;
        --secondary: #f1c40f;
        --light: #ffffff;
        --dark: #212121;
        --text: #34495e;
        --text-light: #7f8c8d;
        --border-radius: 8px;
        --box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        --transition: all 0.3s ease;
    }

    body {
        font-family: 'Poppins', sans-serif;
        line-height: 1.7;
        color: var(--text);
        background: linear-gradient(120deg, #f5f9f6, #ecfdf5);
        margin: 0;
    }

    /* 🌿 NAVBAR */
    

    
      

    /* 🌿 CONTENT */
    .container {
        max-width: 1400px;
        margin: 30px auto;
        padding: 0 20px;
    }

    h2 {
        color: var(--primary-dark);
        margin-bottom: 20px;
        font-size: 26px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    h2::before {
        font-size: 1.2em;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        margin-bottom: 40px;
    }

    th {
        background: var(--primary);
        color: white;
        font-weight: 600;
        text-align: left;
        padding: 14px 18px;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 1px;
    }

    td {
        padding: 14px 18px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: top;
    }

    tr:hover td {
        background-color: #f9fdf9;
    }

    .review {
        background: #f6fef9;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #e0f2e9;
        margin-top: 6px;
    }

    .reply {
        margin-top: 8px;
        padding: 10px;
        background: #f0fdf4;
        border-radius: 8px;
        border-left: 4px solid var(--accent);
        font-size: 0.95em;
    }

    .no-review {
        color: var(--text-light);
        font-style: italic;
    }

    .rating {
        color: var(--secondary);
        font-size: 1.2em;
        letter-spacing: 2px;
        margin-bottom: 6px;
        display: inline-block;
    }

    small {
        color: var(--text-light);
        font-size: 0.85em;
    }

    .status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 0.8em;
        font-weight: 600;
        text-transform: capitalize;
        background: rgba(60,168,106,0.15);
        color: var(--primary-dark);
    }

    .mode {
        font-weight: 500;
        color: var(--accent);
    }
    </style>
</head>
<body>
<?PHP include('adminnav.php');?>
<!-- 🌿 NAVBAR -->


<div class="container">
    <!-- 🌿 BOOKINGS -->
    <h2>📊 All Bookings with Reviews</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Therapist</th>
                <th>Date</th>
                <th>Time</th>
                <th>Mode</th>
                <th>Status</th>
                <th>Review</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['user_name'] ?? 'N/A') ?></td>
                        <td>
                            <strong><?= htmlspecialchars($row['therapist_name'] ?? 'N/A') ?></strong><br>
                            <small>ID: <?= htmlspecialchars($row['therapist_id'] ?? 'N/A') ?></small>
                        </td>
                        <td><?= !empty($row['appointment_date']) ? date('M j, Y', strtotime($row['appointment_date'])) : 'N/A' ?></td>
                        <td><?= !empty($row['appointment_time']) ? date('g:i a', strtotime($row['appointment_time'])) : 'N/A' ?></td>
                        <td class="mode"><?= htmlspecialchars($row['mode'] ?? 'N/A') ?></td>
                        <td><span class="status"><?= htmlspecialchars($row['status'] ?? '') ?></span></td>
                        <td>
                            <?php if (!empty($row['rating'])): ?>
                                <div class="review">
                                    <span class="rating"><?= str_repeat('★', (int)$row['rating']) . str_repeat('☆', 5 - (int)$row['rating']) ?></span>
                                    <p><?= nl2br(htmlspecialchars($row['comment'] ?? '')) ?></p>
                                    <?php if (!empty($row['reviewer_name'])): ?>
                                        <div style="margin-top:6px;font-size:0.85em;color:var(--text-light);">
                                            — <?= htmlspecialchars($row['reviewer_name']) ?>, 
                                            <?= !empty($row['review_created_at']) ? date('M j, Y', strtotime($row['review_created_at'])) : '' ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($row['therapist_reply'])): ?>
                                        <div class="reply">
                                            <strong>Therapist's Reply:</strong>
                                            <p style="margin:6px 0 0;"><?= nl2br(htmlspecialchars($row['therapist_reply'])) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="no-review">✏️ No review yet</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align:center;padding:30px;color:var(--text-light);">
                        🌿 No bookings found
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    
      
               
<script>
  document.querySelector(".menu-toggle").addEventListener("click", function () {
    document.querySelector(".navbar ul").classList.toggle("active");
  });
</script>
</body>
</html>
