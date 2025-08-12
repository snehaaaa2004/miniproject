<?php
session_start();
include('../connect.php');

// Step 1: Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to view reviews.";
    exit();
}

$user_id = $_SESSION['user_id'];

// Step 2: Get therapist ID for this user
$getTherapist = $conn->prepare("SELECT id FROM therapists WHERE user_id = ?");
$getTherapist->bind_param("i", $user_id);
$getTherapist->execute();
$therapistResult = $getTherapist->get_result();

if ($therapistResult->num_rows === 0) {
    echo "You are not registered as a therapist.";
    exit();
}

$therapist = $therapistResult->fetch_assoc();
$therapist_id = $therapist['id'];

// Step 3: Fetch reviews for this therapist
$sql = "
    SELECT 
        r.rating, 
        r.comment, 
        r.created_at, 
        u.name AS user_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.therapist_id = ?
    ORDER BY r.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $therapist_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Client Reviews</title>
    <style>
        body { font-family: Arial; padding: 30px; background: #f4f4f4; }
        .review-container { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        .stars { color: #f39c12; font-size: 18px; }
        .meta { font-size: 12px; color: gray; }
    </style>
</head>
<body>

<h1>Reviews from Your Clients</h1>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="review-container">
            <h3><?= htmlspecialchars($row['user_name']) ?></h3>
            <div class="stars">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?= $i <= $row['rating'] ? '★' : '☆' ?>
                <?php endfor; ?>
            </div>
            <p><?= nl2br(htmlspecialchars($row['comment'])) ?></p>
            <p class="meta">Reviewed on <?= date("F j, Y", strtotime($row['created_at'])) ?></p>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No reviews have been submitted yet.</p>
<?php endif; ?>

</body>
</html>
