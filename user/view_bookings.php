<?php
session_start();
include('../connect.php');
include('../auth_check.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

/* =======================
   Fetch Appointments
   ======================= */
$query = "
    SELECT a.*, t.specialization, t.experience, t.language, t.gender, t.fees, u.name AS therapist_name 
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

/* =======================
   Fetch Reviews (by appointment)
   ======================= */
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

/* =======================
   Fetch Paid Appointments
   ======================= */
$paymentsQuery = "
    SELECT appointment_id 
    FROM payments 
    WHERE appointment_id IN (SELECT id FROM appointments WHERE user_id = ?)
";
$paymentsStmt = $conn->prepare($paymentsQuery);
$paymentsStmt->bind_param("i", $user_id);
$paymentsStmt->execute();
$paymentsResult = $paymentsStmt->get_result();

$paidAppointments = [];
while ($payment = $paymentsResult->fetch_assoc()) {
    $paidAppointments[$payment['appointment_id']] = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Appointments - SerenityConnect</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <style>
    body { font-family: 'Inter', sans-serif; background: #f8fafc; padding: 2rem; }
    .appointments-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem; }
    .booking-card { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 8px rgba(0,0,0,0.05); position: relative; }
    .status { padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; text-transform: capitalize; display: inline-block; }
    .status.pending { background: #fff3cd; color: #856404; }
    .status.confirmed { background: #d1e7dd; color: #0f5132; }
    .status.completed { background: #e2e3e5; color: #41464b; }
    .status.cancelled { background: #f8d7da; color: #842029; }
    .review-display, .review-form-container { margin-top: 1rem; border-top: 1px solid #eee; padding-top: 1rem; }
    .review-rating i { color: #e8b84d; }
    .review-text { font-style: italic; }
    .submit-review, .edit-review-btn, .delete-review-btn, .pay-now-btn {
      padding: 0.5rem 1rem; border: none; border-radius: 6px; cursor: pointer; margin-top: 0.5rem;
    }
    .submit-review { background: #1e3a2e; color: #fff; }
    .edit-review-btn { background: #f59e0b; color: #fff; }
    .delete-review-btn { background: #dc3545; color: #fff; }
    .pay-now-btn { background: #22c55e; color: #fff; }
    /* Modal */
    .edit-review-modal { display: none; position: fixed; top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.4);align-items:center;justify-content:center; z-index: 1000;}
    .edit-review-content { background:#fff;padding:2rem;border-radius:10px;width:400px;max-width:95%; }
    .no-appointments { text-align:center; color:#475569; }
  </style>
</head>
<body>
  <h1 style="text-align:center;margin-bottom:2rem;">My Appointments</h1>

  <div class="appointments-grid">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()):
        $statusClass = strtolower($row['status']);
        $hasReview = isset($reviews[$row['id']]);

        // Nice date/time formatting
        $dateFmt = $row['appointment_date'] ? date("F j, Y", strtotime($row['appointment_date'])) : '';
        $timeFmt = $row['appointment_time'] ? date("g:i A", strtotime($row['appointment_time'])) : '';
      ?>
      <div class="booking-card" id="appointment-<?= (int)$row['id'] ?>">
        <span class="status <?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars($row['status']) ?></span>

        <h3><?= htmlspecialchars($row['therapist_name']) ?></h3>
        <p><strong>Specialization:</strong> <?= htmlspecialchars($row['specialization']) ?></p>
        <p><strong>Experience:</strong> <?= htmlspecialchars($row['experience']) ?> years</p>
        <p><strong>Language:</strong> <?= htmlspecialchars($row['language']) ?></p>
        <p><strong>Gender:</strong> <?= htmlspecialchars($row['gender']) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($dateFmt) ?></p>
        <p><strong>Time:</strong> <?= htmlspecialchars($timeFmt) ?></p>
        <p><strong>Mode:</strong> <?= htmlspecialchars((string)($row['mode'] ?? '')) ?></p>
        <p><strong>Fees:</strong> $<?= htmlspecialchars($row['fees']) ?></p>

        <?php if (strtolower($row['status']) === 'confirmed' && empty($paidAppointments[$row['id']])): ?>
          <form action="payments.php" method="POST">
            <input type="hidden" name="appointment_id" value="<?= (int)$row['id'] ?>">
            <input type="hidden" name="therapist_id" value="<?= (int)$row['therapist_id'] ?>">
            <button type="submit" class="pay-now-btn"><i class="fas fa-credit-card"></i> Pay Now</button>
          </form>
        <?php endif; ?>

        <?php if ($hasReview): 
            $rev = $reviews[$row['id']];
        ?>
          <div class="review-display" id="review-<?= (int)$row['id'] ?>">
            <h4><i class="fas fa-star-half-alt"></i> Your Review</h4>
            <div class="review-rating">
              <?= str_repeat('<i class="fas fa-star"></i>', (int)$rev['rating']) ?>
              <?= str_repeat('<i class="far fa-star"></i>', 5 - (int)$rev['rating']) ?>
            </div>
            <p class="review-text">"<?= htmlspecialchars($rev['comment']) ?>"</p>
            <p><small><?= $rev['created_at'] ? date("M j, Y", strtotime($rev['created_at'])) : '' ?></small></p>

            <!-- Use data-* attributes (safer than inline JS) -->
            <button
              class="edit-review-btn"
              data-review-id="<?= (int)$rev['id'] ?>"
              data-rating="<?= (int)$rev['rating'] ?>"
              data-comment="<?= htmlspecialchars($rev['comment'], ENT_QUOTES) ?>"
            >Edit</button>

            <button
              class="delete-review-btn"
              data-review-id="<?= (int)$rev['id'] ?>"
            >Delete</button>
          </div>
        <?php elseif (strtolower($row['status']) === 'completed'): ?>
          <div class="review-form-container" id="review-form-<?= (int)$row['id'] ?>">
            <h4><i class="fas fa-pen-nib"></i> Write a Review</h4>
            <form action="submit_review.php" method="POST">
              <input type="hidden" name="appointment_id" value="<?= (int)$row['id'] ?>">
              <input type="hidden" name="therapist_id" value="<?= (int)$row['therapist_id'] ?>">
              <label>Rating:</label>
              <select name="rating" required>
                <option value="">Select</option>
                <option value="1">⭐ 1</option>
                <option value="2">⭐ 2</option>
                <option value="3">⭐ 3</option>
                <option value="4">⭐ 4</option>
                <option value="5">⭐ 5</option>
              </select>
              <textarea name="comment" placeholder="Share your experience..." required minlength="20"></textarea>
              <button type="submit" class="submit-review">Submit Review</button>
            </form>
          </div>
        <?php endif; ?>
      </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="no-appointments">
        <i class="far fa-calendar-times"></i>
        <p>You have no appointments.</p>
      </div>
    <?php endif; ?>
  </div>

  <!-- Global Edit Modal -->
  <div class="edit-review-modal" id="editModal" aria-modal="true" role="dialog">
    <div class="edit-review-content">
      <h3>Edit Your Review</h3>
      <form id="editReviewForm">
        <input type="hidden" name="review_id" id="modalReviewId">
        <label>Rating:</label>
        <select name="rating" id="modalRating" required>
          <option value="1">⭐ 1</option>
          <option value="2">⭐ 2</option>
          <option value="3">⭐ 3</option>
          <option value="4">⭐ 4</option>
          <option value="5">⭐ 5</option>
        </select>
        <textarea name="comment" id="modalComment" required minlength="20" placeholder="Update your review..."></textarea>
        <button type="submit" class="submit-review">Save</button>
        <button type="button" onclick="closeEditModal()">Cancel</button>
      </form>
    </div>
  </div>

<script>
function openEditModalWithData(reviewId, rating, comment) {
  document.getElementById('modalReviewId').value = reviewId;
  document.getElementById('modalRating').value = rating;
  document.getElementById('modalComment').value = comment;
  document.getElementById('editModal').style.display = 'flex';
}
function closeEditModal() {
  document.getElementById('editModal').style.display = 'none';
}

/** Safely parse JSON; if HTML/error page comes back, show readable message instead of throwing "Unexpected token <" */
async function parseJsonSafe(response) {
  const ct = response.headers.get('content-type') || '';
  if (ct.includes('application/json')) {
    return await response.json();
  }
  // Fallback: read text to surface server error (HTML/PHP warning)
  const text = await response.text();
  return { success: false, message: text.replace(/<[^>]*>/g, '').trim() || 'Non-JSON response received' };
}

document.addEventListener('DOMContentLoaded', function() {
  // Open modal on "Edit" (using data-* attributes)
  document.querySelectorAll('.edit-review-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      openEditModalWithData(this.dataset.reviewId, this.dataset.rating, this.dataset.comment);
    });
  });

  // Handle edit form (AJAX)
  document.getElementById('editReviewForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    try {
      const res = await fetch('edit_review.php', {
        method:'POST',
        headers: { 'Accept': 'application/json' },
        body: formData
      });
      const data = await parseJsonSafe(res);
      if (data.success) {
        alert("Review updated successfully!");
        location.reload();
      } else {
        alert("Error: " + (data.message || "Failed to update review"));
      }
    } catch(err) {
      alert("Error: " + err.message);
    }
  });

  // Handle delete (AJAX)
  document.querySelectorAll('.delete-review-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
      if (!confirm("Delete this review?")) return;
      const reviewId = this.dataset.reviewId;
      try {
        const res = await fetch('delete_review.php', {
          method:'POST',
          headers: {
            'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8',
            'Accept': 'application/json'
          },
          body: 'review_id=' + encodeURIComponent(reviewId)
        });
        const data = await parseJsonSafe(res);
        if (data.success) {
          alert("Review deleted!");
          location.reload();
        } else {
          alert("Error: " + (data.message || "Failed to delete review"));
        }
      } catch(err) {
        alert("Error: " + err.message);
      }
    });
  });
});
</script>
</body>
</html>
