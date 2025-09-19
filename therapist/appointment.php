<?php
session_start();
include('../connect.php');
include('../auth_check.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Therapist') {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

// Get therapist's ID
$therapist_query = mysqli_query($conn, "SELECT id FROM therapists WHERE user_id = '$user_id'");
$therapist_data = mysqli_fetch_assoc($therapist_query);
$therapist_id = $therapist_data['id'] ?? '';

if (!$therapist_id) {
    die("Therapist profile not found.");
}

// Update status if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['new_status'];

    $update = mysqli_query($conn, "UPDATE appointments SET status='$new_status' WHERE id='$appointment_id' AND therapist_id='$therapist_id'");
    if (!$update) {
        echo "Error updating status: " . mysqli_error($conn);
    }
}

// Fetch appointments for this therapist, including payment status
$stmt = $conn->prepare("
    SELECT a.*, u.name AS user_name, p.payment_status
    FROM appointments a
    JOIN users u ON a.user_id = u.id
    LEFT JOIN payments p ON a.id = p.appointment_id AND p.payment_status = 'completed'
    WHERE a.therapist_id = ?
    ORDER BY a.appointment_date, a.appointment_time
");
$stmt->bind_param("s", $therapist_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Therapist Appointments</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
        :root {
      --primary: #2e4d3d;
      --primary-dark: #1e3b2b;
      --primary-light: #eaf4ed;
      --secondary: #3a5e4f;
      --accent: #f8c537;
      --text: #333333;
      --text-light: #666666;
      --background: #f8f6f3;
      --white: #ffffff;
      --border: #d9e0d9;
      --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      --shadow-hover: 0 8px 20px rgba(0, 0, 0, 0.12);
      --radius: 8px;
      --radius-lg: 12px;
      --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }
        
        * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
        body { 
            font-family: 'Roboto', sans-serif; 
            
            background: #f0f2f5; 
            color: #333;
        }
        h2 { 
            color: #1a535c; 
            text-align: center;
            margin-bottom: 30px;
            font-weight: 500;
        }
        .appointments-container { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px; 
            justify-content: center;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 25px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.12);
        }
        .card h3 { 
            margin-top: 0; 
            color: #1a535c;
            font-size: 20px;
            font-weight: 500;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .card p { 
            margin: 8px 0; 
            font-size: 15px; 
            line-height: 1.5;
        }
        .card p strong {
            color: #555;
            font-weight: 500;
        }
        .status {
            padding: 6px 12px;
            border-radius: 20px;
            display: inline-block;
            font-weight: 500;
            font-size: 13px;
        }
        .status.pending { background: #ffe6cc; color: #cc6600; }
        .status.confirmed { background: #f7e665ff; color: #1a535c; }
        .status.cancelled { background: #fcd9df; color: #b73244; }
        .status.completed { background: #d6f5e3; color: #1f8a70; }
        .status.paid { background: #c8e6c9; color: #2e7d32; } /* New style for Paid status */
        .button-group {
            margin-top: 20px;
        }
        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-right: 8px;
            font-size: 14px;
            font-weight: 500;
            color: white;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-confirm {  background-color: #28a745;   /* fresh green */
    color: #145428ff;                /* white text for contrast */
    border: none;
    border-radius: 8px;
    padding: 10px 18px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(15, 20, 16, 0.3);}
        .btn-confirm:hover { background: #218838; }

        .btn-cancel { background: #dc3545;
        color: red; }
        .btn-cancel:hover { background: #c82333; }

        .btn-complete {
    background-color: #28a745;   /* fresh green */
    color: #145428ff;                /* white text for contrast */
    border: none;
    border-radius: 8px;
    padding: 10px 18px;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(15, 20, 16, 0.3);
}

.btn-complete:hover {
    background-color: #1c4726ff;   /* darker green */
    color: #0e4316ff;                /* keep white for better readability */
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(40, 167, 69, 0.4);
}

.btn-complete:active {
    transform: scale(0.97);
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
}

.btn-complete:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.5);
}

        
        form { display: inline; }

        @media (max-width: 768px) {
            .appointments-container {
                grid-template-columns: 1fr;
            }
        }
         @media (max-width: 768px) {
      .nav-links {
        display: none;
        position: absolute;
        top: 70px;
        left: 0;
        right: 0;
        background: var(--primary-dark);
        flex-direction: column;
        padding: 2rem;
        gap: 1.5rem;
        box-shadow: var(--shadow);
      }
      .logo-icon {
      font-size: 1.8rem;
      color: var(--accent);
    }

    </style>
</head>
<body>
<?php include('therapistnav.php'); ?>
    <h2>Your Appointment Requests 🗓️</h2>
    
    <?php if (mysqli_num_rows($result) === 0): ?>
        <p style="text-align: center; font-size: 1.1em; color: #666;">No appointment requests yet. Check back later!</p>
    <?php else: ?>
        <div class="appointments-container">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php
                    // Check if the appointment has a completed payment
                    $isPaid = !empty($row['payment_status']) && $row['payment_status'] === 'completed';
                ?>
                <div class="card">
                    <h3><?= htmlspecialchars($row['user_name']) ?></h3>
                    <p><strong>Date:</strong> <?= htmlspecialchars($row['appointment_date']) ?></p>
                    <p><strong>Time:</strong> <?= htmlspecialchars($row['appointment_time']) ?></p>
                    <p><strong>Mode:</strong> <?= htmlspecialchars($row['mode']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($row['phone']) ?></p>
                    <p><strong>Description:</strong> <?= htmlspecialchars($row['description']) ?></p>
                    <p><strong>Status:</strong> 
                        <span class="status <?= strtolower($row['status']) ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                        <?php if ($isPaid): ?>
                            <span class="status paid">Paid</span>
                        <?php endif; ?>
                    </p>
                    
                    <!-- Button Logic -->
                    <div class="button-group">
                        <?php if ($row['status'] === 'pending'): ?>
                            <!-- Confirm/Cancel for pending appointments -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="new_status" value="confirmed">
                                <button type="submit" name="update_status" class="btn btn-confirm">Confirm</button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="new_status" value="cancelled">
                                <button type="submit" name="update_status" class="btn btn-cancel">Cancel</button>
                            </form>
                        <?php elseif ($row['status'] === 'confirmed' && $isPaid): ?>
                            <!-- Mark as Completed only if confirmed AND paid -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="new_status" value="completed">
                                <button type="submit" name="update_status" class="btn btn-complete">Mark as Completed</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</body>
</html>