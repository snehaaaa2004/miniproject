<?php
session_start();
include('../connect.php');
include('../auth_check.php');


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

// Get payments for these appointments
$paymentsQuery = "SELECT appointment_id FROM payments WHERE appointment_id IN (SELECT id FROM appointments WHERE user_id = ?)";
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - SerenityConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary: #1e3a2e;
            --primary-medium: #2e5543;
            --primary-light: #3d7058;
            --accent: #e8b84d;
            --accent-light: #f4d186;
            --success: #4ade80;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --background: #fefefe;
            --background-alt: #f8fafc;
            --surface: #ffffff;
            --border: #e2e8f0;
            --border-light: #f1f5f9;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius: 12px;
            --radius-lg: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-alt);
            color: var(--text-primary);
            line-height: 1.6;
            padding: 2rem 1rem;
        }
        
        .container {
            max-width: 1100px;
            margin: auto;
            padding: 20px;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .page-title i {
            color: var(--accent);
            margin-right: 0.5rem;
        }

        .appointments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }

        .booking-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-light);
            transition: var(--transition);
            position: relative;
        }
        
        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .booking-card.has-review { 
            border: 2px solid var(--accent-light);
            background: #fdfdf5;
        }

        .booking-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .booking-card p {
            margin: 0.5rem 0;
            font-size: 0.95rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .booking-card p i {
            color: var(--primary-medium);
            font-size: 0.9rem;
            width: 1.25rem;
            text-align: center;
        }
        
        .booking-card p strong {
            color: var(--text-primary);
        }

        .status {
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            text-transform: capitalize;
            white-space: nowrap;
        }
        .status.confirmed { background-color: #f0fdf4; color: #16a34a; }
        .status.pending { background-color: #fffbeb; color: #b45309; }
        .status.cancelled { background-color: #fef2f2; color: #dc2626; }
        .status.completed { background-color: #f1f5f9; color: #475569; }

        .completed-checkbox {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .completed-checkbox input[type="checkbox"] {
            -webkit-appearance: none;
            appearance: none;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid var(--border);
            border-radius: 4px;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }
        
        .completed-checkbox input[type="checkbox"]:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .completed-checkbox input[type="checkbox"]:checked::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: white;
            font-size: 0.8rem;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .completed-checkbox label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            cursor: pointer;
        }

        .review-display, .review-form-container {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-light);
        }
        
        .review-display h4, .review-form-container h4 {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            color: var(--primary);
            margin-bottom: 0.75rem;
        }

        .review-rating i {
            color: var(--accent);
            font-size: 1.1rem;
            margin-right: 0.2rem;
        }

        .review-text {
            font-size: 0.95rem;
            color: var(--text-primary);
            margin: 0.5rem 0;
            font-style: italic;
        }
        
        .review-date {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            margin-bottom: 1rem;
        }
        
        .star-rating input[type="radio"] { display: none; }
        
        .star-rating label {
            font-size: 1.5rem;
            color: var(--border);
            cursor: pointer;
            transition: color 0.2s;
        }
        
        .star-rating label:hover, .star-rating label:hover ~ label {
            color: var(--accent);
        }
        
        .star-rating input[type="radio"]:checked ~ label {
            color: var(--accent);
        }

        textarea {
            width: 100%;
            min-height: 100px;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            margin-bottom: 1rem;
            resize: vertical;
            font-size: 0.9rem;
            font-family: inherit;
            transition: var(--transition);
        }
        
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 58, 46, 0.1);
        }

        .submit-review, .pay-now-btn {
            padding: 0.75rem 1.5rem;
            color: white;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .submit-review { background-color: var(--primary); }
        .submit-review:hover { background-color: var(--primary-medium); }

        .pay-now-btn { background-color: #22c55e; }
        .pay-now-btn:hover { background-color: #16a34a; }
        
        .no-appointments {
            text-align: center;
            color: var(--text-muted);
            padding: 4rem 2rem;
            font-size: 1.1rem;
        }
        
        .no-appointments i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .appointments-grid {
                grid-template-columns: 1fr;
            }
            body { padding: 1rem; }
            .booking-card { padding: 1.5rem; }
            .completed-checkbox { position: static; margin-bottom: 1rem; }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-calendar-check"></i> My Appointments</h1>
        </div>

        <div class="appointments-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()):
                    $statusClass = strtolower($row['status']);
                    $hasReview = isset($reviews[$row['id']]);
                ?>
                    <div class="booking-card <?= $hasReview ? 'has-review' : '' ?>" id="appointment-<?= $row['id'] ?>">
                        <div class="appointment-header">
                            <span class="status <?= $statusClass ?>"><?= htmlspecialchars($row['status']) ?></span>
                            <?php if (strtolower($row['status']) === 'confirmed' && !$hasReview): ?>
                                <div class="completed-checkbox">
                                    
                                </div>
                            <?php endif; ?>
                            <?php if ($hasReview): ?>
                                <div class="completed-checkbox">
                                    
                                    
                                </div>
                            <?php endif; ?>
                        </div>

                        <h3><i class="fas fa-user-md"></i> <?= htmlspecialchars($row['therapist_name']) ?></h3>
                        <p><i class="fas fa-stethoscope"></i> <strong>Specialization:</strong> <?= htmlspecialchars($row['specialization']) ?></p>
                        <p><i class="fas fa-medal"></i> <strong>Experience:</strong> <?= htmlspecialchars($row['experience']) ?> years</p>
                        <p><i class="fas fa-language"></i> <strong>Language:</strong> <?= htmlspecialchars($row['language']) ?></p>
                        <p><i class="fas fa-venus-mars"></i> <strong>Gender:</strong> <?= htmlspecialchars($row['gender']) ?></p>
                        <p><i class="fas fa-calendar-alt"></i> <strong>Date:</strong> <?= date("F j, Y", strtotime($row['appointment_date'])) ?></p>
                        <p><i class="fas fa-clock"></i> <strong>Time:</strong> <?= date("g:i A", strtotime($row['appointment_time'])) ?></p>
                        <p><i class="fas fa-video"></i> <strong>Mode:</strong> <?= htmlspecialchars($row['mode']) ?></p>
                        <p><i class="fas fa-dollar-sign"></i> <strong>Fees:</strong> $<?= htmlspecialchars($row['fees']) ?></p>
                        <?php if (!empty($row['description'])): ?>
                            <p><i class="fas fa-sticky-note"></i> <strong>Notes:</strong> <?= htmlspecialchars($row['description']) ?></p>
                        <?php endif; ?>

                        <?php if (strtolower($row['status']) === 'confirmed' && empty($paidAppointments[$row['id']])): ?>
                            <form action="payments.php" method="POST" style="margin-top:1.5rem;">
                                <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="therapist_id" value="<?= $row['therapist_id'] ?>">
                                <button type="submit" class="pay-now-btn">
                                    <i class="fas fa-credit-card"></i> Pay Now
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($hasReview): ?>
                            <div class="review-display" id="review-<?= $row['id'] ?>">
                                <h4><i class="fas fa-star-half-alt"></i> Your Review</h4>
                                <div class="review-rating">
                                    <?= str_repeat('<i class="fas fa-star"></i>', $reviews[$row['id']]['rating']) ?>
                                    <?= str_repeat('<i class="far fa-star"></i>', 5 - $reviews[$row['id']]['rating']) ?>
                                </div>
                                <p class="review-text">"<?= htmlspecialchars($reviews[$row['id']]['comment']) ?>"</p>
                                <p class="review-date">Submitted on <?= date("M j, Y", strtotime($reviews[$row['id']]['created_at'])) ?></p>
                            </div>
                        <?php elseif (strtolower($row['status']) === 'completed'): ?>
                            <div class="review-form-container" id="review-form-<?= $row['id'] ?>">
                                <h4><i class="fas fa-pen-nib"></i> Share Your Experience</h4>
                                <form class="review-form" data-appointment-id="<?= $row['id'] ?>">
                                    <div class="star-rating">
                                        <input type="radio" id="star5-<?= $row['id'] ?>" name="rating" value="5" required /><label for="star5-<?= $row['id'] ?>"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star4-<?= $row['id'] ?>" name="rating" value="4" /><label for="star4-<?= $row['id'] ?>"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star3-<?= $row['id'] ?>" name="rating" value="3" /><label for="star3-<?= $row['id'] ?>"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star2-<?= $row['id'] ?>" name="rating" value="2" /><label for="star2-<?= $row['id'] ?>"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star1-<?= $row['id'] ?>" name="rating" value="1" /><label for="star1-<?= $row['id'] ?>"><i class="fas fa-star"></i></label>
                                    </div>
                                    <textarea name="comment" placeholder="Share your experience with this therapist..." required minlength="20"></textarea>
                                    <input type="hidden" name="therapist_id" value="<?= $row['therapist_id'] ?>">
                                    <button type="submit" class="submit-review"><i class="fas fa-paper-plane"></i> Submit Review</button>
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
                        const reviewText = formData.get('comment');

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

            // Mark as completed
            document.querySelectorAll('.complete-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', async function() {
                    const appointmentId = this.dataset.appointmentId;
                    if (this.checked) {
                        if (confirm('Are you sure you want to mark this appointment as completed?')) {
                            try {
                                const response = await fetch('update_status.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: `appointment_id=${appointmentId}&status=completed`
                                });
                                const data = await response.json();
                                if (data.success) {
                                    location.reload();
                                } else {
                                    alert('Failed to update status: ' + data.message);
                                    this.checked = false;
                                }
                            } catch (error) {
                                alert('Error updating status: ' + error.message);
                                this.checked = false;
                            }
                        } else {
                            this.checked = false;
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>