<?php
session_start();
include('../connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get appointments with therapist info
$query = "
    SELECT a.*, t.specialization, t.experience, t.language, t.gender,t.fees, u.name AS therapist_name 
    FROM appointments a
    JOIN therapists t ON a.therapist_id = t.id
    JOIN users u ON t.user_id = u.id
    WHERE a.user_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Get reviews for these appointments
$reviewsQuery = "
    SELECT r.*, a.id AS appointment_id
    FROM reviews r
    JOIN appointments a ON r.appointment_id = a.id
    WHERE r.user_id = ?
";
$reviewsStmt = $conn->prepare($reviewsQuery);
$reviewsStmt->bind_param("i", $user_id);
$reviewsStmt->execute();
$reviewsResult = $reviewsStmt->get_result();
$reviews = [];
while ($review = $reviewsResult->fetch_assoc()) {
    $reviews[$review['appointment_id']] = $review;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Appointments</title>
    <style>
    * { box-sizing: border-box; }
    body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; margin: 0; padding: 0; }
    .container { max-width: 1000px; margin: auto; padding: 20px; }
    h2 { text-align: center; color: #333; }
    .appointments-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; margin-top: 30px; }
    .booking-card { background-color: #fff; border-radius: 15px; padding: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); position: relative; transition: transform 0.2s; }
    .booking-card:hover { transform: translateY(-5px); }
    .booking-card.has-review { border: 2px solid #ffc107; }
    .booking-card h3 { margin-top: 0; color: #007bff; }
    .booking-card p { margin: 6px 0; font-size: 14px; color: #555; }
    .status { font-weight: bold; padding: 3px 8px; border-radius: 8px; font-size: 13px; }
    .status.confirmed { background-color: #d4edda; color: #155724; }
    .status.pending { background-color: #fff3cd; color: #856404; }
    .status.cancelled { background-color: #f8d7da; color: #721c24; }
    .status.completed { background-color: #e2e3e5; color: #383d41; }
    .completed-checkbox { position: absolute; top: 15px; right: 20px; }
    .completed-checkbox input { transform: scale(1.2); margin-right: 5px; }
    .review-display, .review-form-container { margin-top: 15px; border-top: 1px solid #ddd; padding-top: 10px; }
    .review-display h4, .review-form-container h4 { margin-bottom: 8px; color: #444; }
    .review-rating i { color: #f8c10c; font-size: 18px; margin-right: 1px; }
    .review-text { font-size: 14px; margin: 10px 0; color: #333; }
    .review-date { font-size: 12px; color: #777; }
    .star-rating { display: flex; flex-direction: row-reverse; justify-content: flex-end; padding: 10px 0; }
    .star-rating input[type="radio"] { display: none; }
    .star-rating label { font-size: 20px; color: #ccc; cursor: pointer; transition: color 0.2s; }
    .star-rating input[type="radio"]:checked ~ label { color: #f8c10c; }
    .star-rating label:hover, .star-rating label:hover ~ label { color: #f8c10c; }
    textarea { width: 100%; min-height: 80px; padding: 10px; border: 1px solid #ccc; border-radius: 8px; margin-top: 10px; resize: vertical; font-size: 14px; font-family: inherit; }
    .submit-review { margin-top: 10px; padding: 8px 15px; background-color: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; transition: background-color 0.3s; font-size: 14px; }
    .submit-review:hover { background-color: #0056b3; }
    .delete-review-btn { background-color: #dc3545; margin-top: 10px; }
    .delete-review-btn:hover { background-color: #c82333; }
    .no-appointments { text-align: center; color: #888; padding: 40px 20px; font-size: 18px; }
    .no-appointments i { font-size: 48px; margin-bottom: 10px; color: #ccc; }
    .review-separator { margin: 15px 0; border: none; border-top: 1px solid #eee; }
    .pay-now-btn { background-color: #28a745; color: white; padding: 8px 15px; border: none; border-radius: 6px; cursor: pointer; margin-top: 10px; }
    .pay-now-btn:hover { background-color: #218838; }
    @media (max-width: 768px) { .appointments-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-calendar-alt"></i> My Appointments</h2>
    <div class="appointments-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()):
                $statusClass = strtolower($row['status']);
                $hasReview = isset($reviews[$row['id']]);
            ?>
                <div class="booking-card <?= $hasReview ? 'has-review' : '' ?>" id="appointment-<?= $row['id'] ?>">

                    <?php if (strtolower($row['status']) !== 'completed'): ?>
                        <div class="completed-checkbox">
                            <input type="checkbox" id="complete-<?= $row['id'] ?>" 
                                   class="complete-checkbox" data-appointment-id="<?= $row['id'] ?>"
                                   <?= $hasReview ? 'checked disabled' : '' ?>>
                            <label for="complete-<?= $row['id'] ?>">
                                <?= $hasReview ? 'Completed' : 'Mark as completed' ?>
                            </label>
                        </div>
                    <?php endif; ?>

                    <h3><i class="fas fa-user-md"></i> <?= htmlspecialchars($row['therapist_name']) ?></h3>
                    <p><strong>Specialization:</strong> <?= htmlspecialchars($row['specialization']) ?></p>
                    <p><strong>Experience:</strong> <?= htmlspecialchars($row['experience']) ?> years</p>
                    <p><strong>Language:</strong> <?= htmlspecialchars($row['language']) ?></p>
                    <p><strong>Gender:</strong> <?= htmlspecialchars($row['gender']) ?></p>
                    <p><strong>Date:</strong> <?= date("F j, Y", strtotime($row['appointment_date'])) ?></p>
                    <p><strong>Time:</strong> <?= date("g:i A", strtotime($row['appointment_time'])) ?></p>
                    <p><strong>Mode:</strong> <?= htmlspecialchars($row['mode']) ?></p>
                    <p><strong>Fees:</strong> $<?= htmlspecialchars($row['fees']) ?></p>
                    <p><strong>Status:</strong> <span class="status <?= $statusClass ?>"><?= htmlspecialchars($row['status']) ?></span></p>
                    <?php if (!empty($row['description'])): ?>
                        <p><strong>Notes:</strong> <?= htmlspecialchars($row['description']) ?></p>
                    <?php endif; ?>
                    <?php if (strtolower($row['status']) === 'confirmed'): ?>
                        <form action="payment.php" method="POST">
                            <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="therapist_id" value="<?= $row['therapist_id'] ?>">
                            <button type="submit" class="pay-now-btn">💳 Pay Now</button>
                        </form>
                    <?php endif; ?>

                    

                    <?php if ($hasReview): ?>
                        <div class="review-display" id="review-<?= $row['id'] ?>">
                            <h4><i class="fas fa-star"></i> Your Review</h4>
                            <div class="review-rating">
                                <?= str_repeat('<i class="fas fa-star"></i>', $reviews[$row['id']]['rating']) ?>
                                <?= str_repeat('<i class="far fa-star"></i>', 5 - $reviews[$row['id']]['rating']) ?>
                            </div>
                            <p class="review-text"><?= htmlspecialchars($reviews[$row['id']]['comment']) ?></p>
                            <p class="review-date">
                                Submitted on <?= date("M j, Y", strtotime($reviews[$row['id']]['created_at'])) ?>
                            </p>
                        </div>
                    <?php elseif (strtolower($row['status']) === 'completed'): ?>
                        <div class="review-form-container" id="review-form-<?= $row['id'] ?>">
                            <hr class="review-separator">
                            <h4>Share Your Experience</h4>
                            <form class="review-form" data-appointment-id="<?= $row['id'] ?>">
                                <div class="star-rating">
                                    <input type="radio" id="star5-<?= $row['id'] ?>" name="rating" value="5" required />
                                    <label for="star5-<?= $row['id'] ?>"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star4-<?= $row['id'] ?>" name="rating" value="4" />
                                    <label for="star4-<?= $row['id'] ?>"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star3-<?= $row['id'] ?>" name="rating" value="3" />
                                    <label for="star3-<?= $row['id'] ?>"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star2-<?= $row['id'] ?>" name="rating" value="2" />
                                    <label for="star2-<?= $row['id'] ?>"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star1-<?= $row['id'] ?>" name="rating" value="1" />
                                    <label for="star1-<?= $row['id'] ?>"><i class="fas fa-star"></i></label>
                                </div>
                                <textarea name="review" placeholder="Share your experience with this therapist..." required minlength="20"></textarea>
                                <input type="hidden" name="therapist_id" value="<?= $row['therapist_id'] ?>">
                                <button type="submit" class="submit-review">Submit Review</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-appointments">
                <i class="far fa-calendar-times"></i>
                <p>You have no appointments scheduled yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Submit review
    document.querySelectorAll('.review-form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const appointmentId = this.dataset.appointmentId;
            const formData = new FormData(this);
            const rating = formData.get('rating');
            const reviewText = formData.get('review');

            if (!rating) {
                alert('Please select a star rating');
                return;
            }
            if (reviewText.length < 20) {
                alert('Please write at least 20 characters for your review');
                return;
            }

            formData.append('appointment_id', appointmentId);

            const submitBtn = this.querySelector('.submit-review');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

            try {
                const response = await fetch('submit_review.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Submission failed');
                }
                location.reload();
            } catch (error) {
                alert(`Error: ${error.message}`);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    });
});
</script>
</body>
</html>
