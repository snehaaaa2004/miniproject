<?php
session_start();
include('../connect.php');

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

try {
    // Correct SQL query with proper table relationships
    $sql = "SELECT 
                a.*, 
                u.name AS user_name,
                tu.name AS therapist_name,  
                t.id AS therapist_id,      
                r.rating, r.comment, r.created_at AS review_created_at,
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
    
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Bookings with Reviews - Admin Panel</title>
    <style>
        :root {
            --primary:  #2e4d3d;
            --primary-light: #2ecc71;
            --primary-dark: #32744eff;
            --secondary: #f1c40f;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --text: #34495e;
            --text-light: #7f8c8d;
        }
        
        body {
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: var(--text);
            background-color: #f5f9f6;
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
            background-image: radial-gradient(circle at 10% 20%, rgba(46, 204, 113, 0.05) 0%, rgba(46, 204, 113, 0.05) 90%);
        }
        
        h2 {
            color: var(--primary-dark);
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--primary-light);
            font-size: 28px;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
        }
        
        h2::before {
            content: "📊";
            margin-right: 15px;
            font-size: 1.2em;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 30px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            overflow: hidden;
            background: white;
            transition: all 0.3s ease;
        }
        
        table:hover {
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }
        
        th {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            font-weight: 600;
            text-align: left;
            padding: 16px 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 14px;
        }
        
        th:first-child {
            border-top-left-radius: 12px;
        }
        
        th:last-child {
            border-top-right-radius: 12px;
        }
        
        td {
            padding: 14px 20px;
            border-bottom: 1px solid rgba(47, 123, 79, 0.1);
            background-color: white;
            transition: all 0.2s ease;
        }
        
        tr:hover td {
            background-color: rgba(46, 204, 113, 0.05);
            transform: translateY(-1px);
        }
        
        tr:last-child td:first-child {
            border-bottom-left-radius: 12px;
        }
        
        tr:last-child td:last-child {
            border-bottom-right-radius: 12px;
        }
        
        .review {
            background-color: rgba(46, 204, 113, 0.08);
            padding: 12px;
            border-left: 4px solid var(--primary);
            border-radius: 6px;
            margin-top: 8px;
            transition: all 0.3s ease;
        }
        
        .review:hover {
            background-color: rgba(46, 204, 113, 0.12);
            transform: translateX(2px);
        }
        
        .review strong {
            color: var(--primary-dark);
            display: inline-block;
            margin-bottom: 5px;
        }
        
        .no-review {
            color: var(--text-light);
            font-style: italic;
            padding: 8px;
            display: inline-block;
        }
        
        small {
            color: var(--text-light);
            font-size: 0.85em;
            display: block;
            margin-top: 3px;
        }
        
        /* Star rating */
        .rating {
            color: var(--secondary);
            font-size: 1.1em;
            letter-spacing: 2px;
        }
        
        /* Debug section styling */
        .debug-panel {
            background-color: white !important;
            border-radius: 12px;
            padding: 25px !important;
            margin-top: 40px !important;
            border: 1px solid rgba(46, 204, 113, 0.2) !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .debug-panel h3 {
            color: var(--primary-dark);
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-light);
        }
        
        pre {
            background-color: var(--dark);
            color: var(--light);
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            margin-top: 15px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            body {
                padding: 20px;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
            
            th, td {
                padding: 12px 15px;
            }
        }
        
        /* Fun elements */
        .status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 500;
        }
        
        .status-completed {
            background-color: rgba(56, 125, 84, 0.2);
            color: var(--primary-dark);
        }
        
        .mode {
            font-weight: 500;
            color: var(--primary-dark);
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <h2>All Bookings with Reviews</h2>

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
                            <strong><?= htmlspecialchars($row['therapist_name'] ?? 'N/A') ?></strong>
                            <small>ID: <?= htmlspecialchars($row['therapist_id'] ?? 'N/A') ?></small>
                        </td>
                        <td><?= date('M j, Y', strtotime($row['appointment_date'])) ?></td>
                        <td><?= date('g:i a', strtotime($row['appointment_time'])) ?></td>
                        <td class="mode"><?= htmlspecialchars($row['mode']) ?></td>
                        <td><span class="status status-completed"><?= htmlspecialchars($row['status']) ?></span></td>
                        <td>
                            <?php if (!empty($row['rating'])): ?>
                                <div class="review">
                                    <span class="rating"><?= str_repeat('★', $row['rating']) . str_repeat('☆', 5 - $row['rating']) ?></span>
                                    <p><?= nl2br(htmlspecialchars($row['comment'])) ?></p>
                                    <?php if (!empty($row['reviewer_name'])): ?>
                                        <div style="margin-top:8px;font-size:0.85em;color:var(--text-light);">
                                            — <?= htmlspecialchars($row['reviewer_name']) ?>, 
                                            <?= date('M j, Y', strtotime($row['review_created_at'])) ?>
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
                        🌿 No bookings found in the system
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Debug output (visible when adding ?debug=1 to URL) -->
    <?php if (isset($_GET['debug']) && $result->num_rows > 0): ?>
        <div class="debug-panel">
            <h3>🔍 Debug Information</h3>
            <p><strong>SQL Query:</strong> <?= htmlspecialchars($sql) ?></p>
            <p><strong>First row data:</strong></p>
            <pre><?php 
                $result->data_seek(0); 
                print_r($result->fetch_assoc());
            ?></pre>
        </div>
    <?php endif; ?>
</body>
</html>