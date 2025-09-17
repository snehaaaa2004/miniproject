<?php
session_start();
include('../connect.php');
include('../auth_check.php');
include('navbar.php');

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - SerenityConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            color: #1e293b;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        
        body.loaded {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Loading Animation */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }
        
        .page-loader.fade-out {
            opacity: 0;
            pointer-events: none;
        }
        
        .loader-logo {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #4f46e5, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .loader-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e2e8f0;
            border-top: 4px solid #4f46e5;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }
        
        .loader-text {
            font-size: 1.1rem;
            color: #64748b;
            font-weight: 500;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
            font-weight: 700;
            color: #1e293b;
            background: linear-gradient(135deg, #4f46e5, #059669);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Grid Layout */
        .appointments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 2rem;
        }
        
        /* Card Styling */
        .booking-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .booking-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4f46e5, #059669, #f59e0b);
        }
        
        .booking-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Status Badges */
        .status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: capitalize;
            margin-bottom: 1rem;
        }
        
        .status.pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }
        
        .status.confirmed {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }
        
        .status.completed {
            background: #e5e7eb;
            color: #374151;
            border: 1px solid #9ca3af;
        }
        
        .status.cancelled {
            background: #fecaca;
            color: #991b1b;
            border: 1px solid #ef4444;
        }
        
        .status.paid {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #22c55e;
            margin-left: 0.5rem;
        }
        
        /* Card Content */
        .therapist-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
        }
        
        .appointment-details {
            display: grid;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .detail-label {
            font-weight: 600;
            color: #64748b;
        }
        
        .detail-value {
            font-weight: 500;
            color: #1e293b;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            margin: 0.25rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #4338ca, #3730a3);
            transform: translateY(-1px);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #047857, #065f46);
            transform: translateY(-1px);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }
        
        .btn-warning:hover {
            background: linear-gradient(135deg, #d97706, #b45309);
            transform: translateY(-1px);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #b91c1c, #991b1b);
            transform: translateY(-1px);
        }
        
        /* Review Section */
        .review-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid #f1f5f9;
        }
        
        .review-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-size: 1.125rem;
            font-weight: 600;
            color: #1e293b;
        }
        
        .review-display {
            background: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #059669;
        }
        
        .review-rating {
            margin-bottom: 0.5rem;
        }
        
        .review-rating i {
            color: #f59e0b;
            margin-right: 2px;
        }
        
        .review-text {
            font-style: italic;
            color: #475569;
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        
        .review-date {
            font-size: 0.75rem;
            color: #94a3b8;
        }
        
        /* Review Form */
        .review-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .form-select,
        .form-textarea {
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.875rem;
            transition: border-color 0.2s ease;
        }
        
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .modal-header {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #1e293b;
        }
        
        .modal-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        /* Empty State */
        .no-appointments {
            text-align: center;
            padding: 4rem 2rem;
            color: #64748b;
        }
        
        .no-appointments i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }
        
        /* Loading State */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .loading .btn {
            cursor: not-allowed;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .appointments-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .booking-card {
                padding: 1.5rem;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .modal-content {
                margin: 1rem;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Page Loader -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-logo">
            <i class="fas fa-brain"></i>
        </div>
        <div class="loader-spinner"></div>
        <div class="loader-text">Loading your appointments...</div>
    </div>

    <div class="container">
        <h1 class="page-title">
            <i class="fas fa-calendar-check"></i>
            My Appointments
        </h1>

        <div class="appointments-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): 
                    $statusClass = strtolower($row['status']);
                    $hasReview = isset($reviews[$row['id']]);
                    $isPaid = isset($paidAppointments[$row['id']]);
                    
                    $dateFmt = $row['appointment_date'] ? date("F j, Y", strtotime($row['appointment_date'])) : 'Not set';
                    $timeFmt = $row['appointment_time'] ? date("g:i A", strtotime($row['appointment_time'])) : 'Not set';
                ?>
                <div class="booking-card" id="appointment-<?= (int)$row['id'] ?>">
                    <!-- Status Badges -->
                    <div class="status-badges">
                        <span class="status <?= htmlspecialchars($statusClass) ?>">
                            <?= htmlspecialchars($row['status']) ?>
                        </span>
                        <?php if ($isPaid): ?>
                            <span class="status paid">
                                <i class="fas fa-check-circle"></i> Paid
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Therapist Name -->
                    <h3 class="therapist-name">
                        <i class="fas fa-user-md"></i>
                        <?= htmlspecialchars($row['therapist_name']) ?>
                    </h3>

                    <!-- Appointment Details -->
                    <div class="appointment-details">
                        <div class="detail-item">
                            <span class="detail-label">
                                <i class="fas fa-brain"></i> Specialization:
                            </span>
                            <span class="detail-value"><?= htmlspecialchars($row['specialization']) ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">
                                <i class="fas fa-medal"></i> Experience:
                            </span>
                            <span class="detail-value"><?= htmlspecialchars($row['experience']) ?> years</span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">
                                <i class="fas fa-language"></i> Language:
                            </span>
                            <span class="detail-value"><?= htmlspecialchars($row['language']) ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">
                                <i class="fas fa-venus-mars"></i> Gender:
                            </span>
                            <span class="detail-value"><?= htmlspecialchars($row['gender']) ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">
                                <i class="fas fa-calendar"></i> Date:
                            </span>
                            <span class="detail-value"><?= htmlspecialchars($dateFmt) ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">
                                <i class="fas fa-clock"></i> Time:
                            </span>
                            <span class="detail-value"><?= htmlspecialchars($timeFmt) ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">
                                <i class="fas fa-video"></i> Mode:
                            </span>
                            <span class="detail-value"><?= htmlspecialchars($row['mode'] ?? 'Online') ?></span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">
                                <i class="fas fa-dollar-sign"></i> Fees:
                            </span>
                            <span class="detail-value">$<?= htmlspecialchars($row['fees']) ?></span>
                        </div>
                    </div>

                    <!-- Payment Button -->
                    <?php if (strtolower($row['status']) === 'confirmed' && !$isPaid): ?>
                        <form action="payments.php" method="POST" style="margin-bottom: 1rem;">
                            <input type="hidden" name="appointment_id" value="<?= (int)$row['id'] ?>">
                            <input type="hidden" name="therapist_id" value="<?= htmlspecialchars($row['therapist_id']) ?>">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-credit-card"></i>
                                Pay Now ($<?= htmlspecialchars($row['fees']) ?>)
                            </button>
                        </form>
                    <?php endif; ?>

                    <!-- Review Section -->
                    <?php if ($hasReview): 
                        $rev = $reviews[$row['id']];
                    ?>
                        <div class="review-section">
                            <div class="review-header">
                                <i class="fas fa-star"></i>
                                Your Review
                            </div>
                            
                            <div class="review-display">
                                <div class="review-rating">
                                    <?= str_repeat('<i class="fas fa-star"></i>', (int)$rev['rating']) ?>
                                    <?= str_repeat('<i class="far fa-star"></i>', 5 - (int)$rev['rating']) ?>
                                    <span style="margin-left: 0.5rem; font-weight: 600;">
                                        <?= (int)$rev['rating'] ?>/5
                                    </span>
                                </div>
                                
                                <p class="review-text">
                                    "<?= htmlspecialchars($rev['comment']) ?>"
                                </p>
                                
                                <p class="review-date">
                                    <?= $rev['created_at'] ? date("F j, Y", strtotime($rev['created_at'])) : '' ?>
                                </p>
                                <?php if (!empty($rev['reply'])): ?>
    <div style="margin-top: 1rem; padding: 1rem; background: #f0fdf4; border-left: 4px solid #22c55e; border-radius: 6px;">
        <strong>Therapist’s Reply:</strong>
        <p style="margin-top: 0.5rem; color: #166534;">
            <?= nl2br(htmlspecialchars($rev['reply'])) ?>
        </p>
    </div>
<?php endif; ?>

                                
                                <div style="margin-top: 1rem;">
                                    <button 
                                        class="btn btn-warning edit-review-btn"
                                        data-review-id="<?= (int)$rev['id'] ?>"
                                        data-rating="<?= (int)$rev['rating'] ?>"
                                        data-comment="<?= htmlspecialchars($rev['comment'], ENT_QUOTES) ?>"
                                    >
                                        <i class="fas fa-edit"></i> Edit Review
                                    </button>
                                    
                                    <button 
                                        class="btn btn-danger delete-review-btn"
                                        data-review-id="<?= (int)$rev['id'] ?>"
                                    >
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php elseif (strtolower($row['status']) === 'completed'): ?>
                        <div class="review-section">
                            <div class="review-header">
                                <i class="fas fa-pen-nib"></i>
                                Write a Review
                            </div>
                            
                            <form class="review-form" data-appointment-id="<?= (int)$row['id'] ?>" data-therapist-id="<?= htmlspecialchars($row['therapist_id']) ?>">
                                <div class="form-group">
                                    <label class="form-label">Rating</label>
                                    <select name="rating" class="form-select" required>
                                        <option value="">Select your rating...</option>
                                        <option value="1">⭐ 1 - Poor</option>
                                        <option value="2">⭐⭐ 2 - Fair</option>
                                        <option value="3">⭐⭐⭐ 3 - Good</option>
                                        <option value="4">⭐⭐⭐⭐ 4 - Very Good</option>
                                        <option value="5">⭐⭐⭐⭐⭐ 5 - Excellent</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Your Review</label>
                                    <textarea 
                                        name="comment" 
                                        class="form-textarea" 
                                        required 
                                        minlength="10" 
                                        placeholder="Share your experience with this therapist. How was the session? Would you recommend them to others?"
                                    ></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                    Submit Review
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-appointments">
                    <i class="far fa-calendar-times"></i>
                    <h2>No Appointments Found</h2>
                    <p>You haven't booked any appointments yet.</p>
                    <a href="../therapists.php" class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-search"></i>
                        Find a Therapist
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Review Modal -->
    <div class="modal" id="editReviewModal">
        <div class="modal-content">
            <div class="modal-header">
                <i class="fas fa-edit"></i>
                Edit Your Review
            </div>
            
            <form id="editReviewForm">
                <input type="hidden" name="review_id" id="editReviewId">
                
                <div class="form-group">
                    <label class="form-label">Rating</label>
                    <select name="rating" id="editReviewRating" class="form-select" required>
                        <option value="1">⭐ 1 - Poor</option>
                        <option value="2">⭐⭐ 2 - Fair</option>
                        <option value="3">⭐⭐⭐ 3 - Good</option>
                        <option value="4">⭐⭐⭐⭐ 4 - Very Good</option>
                        <option value="5">⭐⭐⭐⭐⭐ 5 - Excellent</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Your Review</label>
                    <textarea 
                        name="comment" 
                        id="editReviewComment"
                        class="form-textarea" 
                        required 
                        minlength="10"
                    ></textarea>
                </div>
                
                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Page load animation
        document.addEventListener('DOMContentLoaded', function() {
            // Show page content with animation after a short delay
            setTimeout(function() {
                document.getElementById('pageLoader').classList.add('fade-out');
                document.body.classList.add('loaded');
                
                // Remove loader from DOM after animation completes
                setTimeout(function() {
                    document.getElementById('pageLoader').remove();
                }, 500);
            }, 1000); // Adjust timing as needed
        });

        // Utility Functions
        function showLoading(element, show = true) {
            if (show) {
                element.classList.add('loading');
                const button = element.querySelector('button[type="submit"]');
                if (button) {
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                }
            } else {
                element.classList.remove('loading');
                const button = element.querySelector('button[type="submit"]');
                if (button) {
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Review';
                }
            }
        }

        async function parseJsonSafe(response) {
            try {
                return await response.json();
            } catch (error) {
                const text = await response.text();
                console.error('Non-JSON response:', text);
                return { 
                    success: false, 
                    message: 'Server error: ' + (text.substring(0, 100) || 'Unknown error') 
                };
            }
        }

        // Modal Functions
        function openEditModal(reviewId, rating, comment) {
            document.getElementById('editReviewId').value = reviewId;
            document.getElementById('editReviewRating').value = rating;
            document.getElementById('editReviewComment').value = comment;
            document.getElementById('editReviewModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editReviewModal').style.display = 'none';
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Submit New Review
            document.querySelectorAll('.review-form').forEach(form => {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const appointmentId = this.dataset.appointmentId;
                    const therapistId = this.dataset.therapistId;
                    const rating = this.querySelector('select[name="rating"]').value;
                    const comment = this.querySelector('textarea[name="comment"]').value;
                    
                    if (!rating || !comment.trim() || comment.length < 10) {
                        alert('Please provide a rating and comment (at least 10 characters).');
                        return;
                    }
                    
                    showLoading(this, true);
                    
                    const formData = new FormData();
                    formData.append('action', 'add');
                    formData.append('appointment_id', appointmentId);
                    formData.append('therapist_id', therapistId);
                    formData.append('rating', rating);
                    formData.append('comment', comment.trim());
                    
                    try {
                        const response = await fetch('submit_review.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const data = await parseJsonSafe(response);
                        
                        if (data.success) {
                            alert('Review submitted successfully!');
                            location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Failed to submit review'));
                        }
                    } catch (error) {
                        console.error('Submit error:', error);
                        alert('Error: ' + error.message);
                    } finally {
                        showLoading(this, false);
                    }
                });
            });

            // Edit Review Buttons
            document.querySelectorAll('.edit-review-btn').forEach(button => {
                button.addEventListener('click', function() {
                    openEditModal(
                        this.dataset.reviewId, 
                        this.dataset.rating, 
                        this.dataset.comment
                    );
                });
            });

            // Edit Review Form
            document.getElementById('editReviewForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                formData.append('action', 'edit');
                formData.append('review_id', document.getElementById('editReviewId').value);
                formData.append('rating', document.getElementById('editReviewRating').value);
                formData.append('comment', document.getElementById('editReviewComment').value.trim());
                
                try {
                    const response = await fetch('submit_review.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const data = await parseJsonSafe(response);
                    
                    if (data.success) {
                        alert('Review updated successfully!');
                        closeEditModal();
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to update review'));
                    }
                } catch (error) {
                    console.error('Edit error:', error);
                    alert('Error: ' + error.message);
                }
            });

            // Delete Review Buttons
            document.querySelectorAll('.delete-review-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    if (!confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
                        return;
                    }
                    
                    const reviewId = this.dataset.reviewId;
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('review_id', reviewId);
                    
                    try {
                        const response = await fetch('submit_review.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const data = await parseJsonSafe(response);
                        
                        if (data.success) {
                            alert('Review deleted successfully!');
                            location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Failed to delete review'));
                        }
                    } catch (error) {
                        console.error('Delete error:', error);
                        alert('Error: ' + error.message);
                    }
                });
            });

            // Close modal when clicking outside
            document.getElementById('editReviewModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeEditModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeEditModal();
                }
            });
        });

        // Add some additional styling for secondary button
        const style = document.createElement('style');
        style.textContent = `
            .btn-secondary {
                background: linear-gradient(135deg, #6b7280, #4b5563);
                color: white;
            }
            
            .btn-secondary:hover {
                background: linear-gradient(135deg, #4b5563, #374151);
                transform: translateY(-1px);
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>