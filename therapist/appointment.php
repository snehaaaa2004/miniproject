<?php
session_start();
include('../connect.php');
include('therapistnav.php');

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

// Fetch appointments for this therapist
$result = mysqli_query($conn, "SELECT a.*, u.name AS user_name 
                               FROM appointments a 
                               JOIN users u ON a.user_id = u.id 
                               WHERE a.therapist_id = '$therapist_id' 
                               ORDER BY a.appointment_date, a.appointment_time");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Therapist Appointments</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f8f8f8; }
        h2 { color: #2f6690; }
        .appointments-container { display: flex; flex-wrap: wrap; gap: 20px; }
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 15px;
            width: 300px;
        }
        .card h3 { margin-top: 0; color: #2f6690; }
        .card p { margin: 5px 0; font-size: 14px; }
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
        }
        .status.confirmed { background: #cce5ff; color: #004085; }
        .status.cancelled { background: #f8d7da; color: #721c24; }
        .status.completed { background: #d4edda; color: #155724; }
        .btn {
            padding: 6px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 5px;
            font-size: 14px;
        }
        .btn-confirm { background: #28a745; color: white; }
        .btn-cancel { background: #dc3545; color: white; }
        .btn-complete { background: #007bff; color: white; }
        form { display: inline; }
    </style>
</head>
<body>
    

<h2>Your Appointment Requests</h2>

<?php if (mysqli_num_rows($result) === 0): ?>
    <p>No appointment requests yet.</p>
<?php else: ?>
    <div class="appointments-container">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
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
                </p>
                <?php if ($row['status'] === 'pending'): ?>
                    <form method="POST">
                        <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="new_status" value="confirmed">
                        <button type="submit" name="update_status" class="btn btn-confirm">Confirm</button>
                    </form>
                    <form method="POST">
                        <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="new_status" value="cancelled">
                        <button type="submit" name="update_status" class="btn btn-cancel">Cancel</button>
                    </form>
                <?php elseif ($row['status'] === 'confirmed'): ?>
                    <form method="POST">
                        <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="new_status" value="completed">
                        <button type="submit" name="update_status" class="btn btn-complete">Mark as Completed</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

</body>
</html>
