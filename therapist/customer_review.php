<?php
session_start();
include('../connect.php');
include('therapistnav.php');

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

// Step 2.5: Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'], $_POST['reply'])) {
    $review_id = intval($_POST['review_id']);
    $reply = trim($_POST['reply']);

    if (!empty($reply)) {
        $update = $conn->prepare("UPDATE reviews SET reply = ? WHERE id = ? AND therapist_id = ?");
        $update->bind_param("sii", $reply, $review_id, $therapist_id);
        $update->execute();
    }
}

// Step 3: Fetch reviews for this therapist
$sql = "
    SELECT 
        r.id,
        r.rating, 
        r.comment, 
        r.reply,
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
        body { 
    font-family: 'Roboto', sans-serif; 
            
            background: #f0f2f5; 
            color: #333; 
}



.review-container { 
    background: #fff; 
    padding: 26px 24px 18px 24px; 
    margin-bottom: 28px; 
    border-radius: 14px; 
    box-shadow: 0 6px 18px rgba(44,62,80,0.10); 
    border-left: 6px solid #115221ff;
    transition: box-shadow 0.2s, border 0.2s;
}

.review-container:hover {
    box-shadow: 0 10px 32px rgba(44,62,80,0.16);
    border-left: 6px solid #7aa683ff;
}

.review-container h3 { 
    margin: 0 0 6px 0; 
    color: #115215ff;
    font-size: 1.25em;
    font-weight: 600;
}

.stars { 
    color: #fec600; 
    font-size: 1.5em; 
    letter-spacing: 0.15em;
    margin-bottom: 6px;
}

.meta { 
    font-size: 1em; 
    color: #96a0ad; 
    margin-bottom: 10px;
}

.review-container p { 
    font-size: 1.13em;
    color: #38414c;
    margin-top: 0;
}

.reply-box { 
    margin-top: 15px; 
    color:green;
}

textarea { 
    width: 100%; 
    padding: 12px 10px; 
    font-family: 'Roboto', sans-serif;
    border-radius: 7px; 
    border: 1px solid #d0dae6; 
    font-size: 1.06em; 
    transition: border 0.2s;
    background: #f8fafc;
    box-sizing: border-box; 
    margin-bottom: 8px;
}

textarea:focus {
    outline: none;
    border: 1.5px solid #154e26ff;
    background: #f2f8fd;
}

button { 
    margin-top: 5px; 
    padding: 9px 22px; 
    background: linear-gradient(90deg,#67aae7,#376ac3); 
    color: #fff; 
    border: none; 
    border-radius: 7px; 
    font-size: 1em; 
    font-weight: 600;
    cursor: pointer; 
    transition: background 0.2s, transform 0.1s;
    box-shadow: 0 2px 4px #f0f0f0;
}

button:hover, button:focus { 
    background: linear-gradient(90deg,#376ac3,#67aae7); 
    transform: scale(1.045);
}

.reply { 
    background: #f2f6f9; 
    padding: 13px 18px; 
    border-radius: 7px; 
    margin-top: 18px; 
    font-size: 1.05em; 
    color: #0e5a24ff;
    border-left: 4px solid #125b25ff;
    box-shadow: 0 1px 6px rgba(130,150,180,0.07);
}

p { 
    color: #687181;
}

@media (max-width: 600px) {
    .review-container { padding: 13px 5px; border-radius: 7px; }
    body { padding: 10px; }
    h1 { font-size: 1.2em; }
}
h2{
    color:green;
    text-align:center
}
        
    </style>
</head>
<body>

<h2>Reviews from Your Clients</h2>

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

            <?php if (!empty($row['reply'])): ?>
                <div class="reply"><strong>Your Reply:</strong><br><?= nl2br(htmlspecialchars($row['reply'])) ?></div>
            <?php else: ?>
                <div class="reply-box">
                    <form method="post">
                        <input type="hidden" name="review_id" value="<?= $row['id'] ?>">
                        <textarea name="reply" rows="3" placeholder="Write your reply..."></textarea>
                        <button type="submit" style="background-color: green; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
    Reply
</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No reviews have been submitted yet.</p>
<?php endif; ?>

</body>
</html>
