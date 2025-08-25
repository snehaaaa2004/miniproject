<?php
session_start();
include('../connect.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header("Location: ../login.html");
    exit();
}

$specializations = isset($_POST['specialization']) ? (array)$_POST['specialization'] : [];
$genders         = isset($_POST['gender']) ? (array)$_POST['gender'] : [];
$language        = isset($_POST['language']) ? trim($_POST['language']) : '';
$modes           = isset($_POST['mode']) ? (array)$_POST['mode'] : [];
$availabilities  = isset($_POST['availability']) ? (array)$_POST['availability'] : [];

$sql = "SELECT t.*, u.name, u.email FROM therapists t JOIN users u ON t.user_id = u.id WHERE t.approved = 1";
$whereConditions = [];

// Filters
if (!empty($specializations)) {
    $specs = array_map(fn($s) => "LOWER(t.specialization) LIKE LOWER('%" . mysqli_real_escape_string($conn, trim($s)) . "%')", $specializations);
    $whereConditions[] = '(' . implode(" OR ", $specs) . ')';
}
if (!empty($genders)) {
    $genderConds = array_map(fn($g) => "LOWER(t.gender) = LOWER('" . mysqli_real_escape_string($conn, trim($g)) . "')", $genders);
    $whereConditions[] = '(' . implode(" OR ", $genderConds) . ')';
}
if (!empty($language)) {
    $escapedLang = mysqli_real_escape_string($conn, $language);
    $whereConditions[] = "LOWER(t.language) LIKE LOWER('%$escapedLang%')";
}
if (!empty($modes)) {
    $modeConds = array_map(fn($m) => "LOWER(t.mode) LIKE LOWER('%" . mysqli_real_escape_string($conn, trim($m)) . "%')", $modes);
    $whereConditions[] = '(' . implode(" OR ", $modeConds) . ')';
}
if (!empty($availabilities)) {
    $availConds = array_map(fn($a) => "LOWER(t.availability) LIKE LOWER('%" . mysqli_real_escape_string($conn, trim($a)) . "%')", $availabilities);
    $whereConditions[] = '(' . implode(" OR ", $availConds) . ')';
}

if (!empty($whereConditions)) {
    $sql .= " AND " . implode(" AND ", $whereConditions);
}

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}

// Function to generate time slots based on availability period
function generateTimeSlots($availability) {
    $timeSlots = [];
    $availability = trim($availability);

    // Try multiple regex patterns to extract time ranges
    $patterns = [
        '/\((\d{1,2}:\d{2}\s*[APMapm]{2})\s*[-–]\s*(\d{1,2}:\d{2}\s*[APMapm]{2})\)/',
        '/(\d{1,2}:\d{2}\s*[APMapm]{2})\s*[-–]\s*(\d{1,2}:\d{2}\s*[APMapm]{2})/',
        '/(\d{1,2}:\d{2}[APMapm]{2})\s*[-–]\s*(\d{1,2}:\d{2}[APMapm]{2})/',
        '/(\d{1,2}[APMapm]{2})\s*[-–]\s*(\d{1,2}[APMapm]{2})/'
    ];

    $startTime = null;
    $endTime = null;

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $availability, $matches)) {
            $startTimeStr = str_replace(' ', '', $matches[1]);
            $endTimeStr = str_replace(' ', '', $matches[2]);

            if (preg_match('/^(\d{1,2})([APMapm]{2})$/', $startTimeStr)) {
                $startTimeStr = preg_replace('/^(\d{1,2})([APMapm]{2})$/', '$1:00 $2', $startTimeStr);
            }
            if (preg_match('/^(\d{1,2})([APMapm]{2})$/', $endTimeStr)) {
                $endTimeStr = preg_replace('/^(\d{1,2})([APMapm]{2})$/', '$1:00 $2', $endTimeStr);
            }

            $startTimeStr = preg_replace('/([APMapm]{2})/', ' $1', $startTimeStr);
            $endTimeStr = preg_replace('/([APMapm]{2})/', ' $1', $endTimeStr);
            $startTimeStr = strtoupper($startTimeStr);
            $endTimeStr = strtoupper($endTimeStr);

            $startTime = strtotime($startTimeStr);
            $endTime = strtotime($endTimeStr);

            if ($startTime && $endTime && $startTime < $endTime) {
                break;
            }
        }
    }

    if (!$startTime || !$endTime || $startTime >= $endTime) {
        $availabilityLower = strtolower($availability);

        if (strpos($availabilityLower, 'morning') !== false) {
            $startTime = strtotime('8:00 AM');
            $endTime = strtotime('12:00 PM');
        } elseif (strpos($availabilityLower, 'afternoon') !== false) {
            $startTime = strtotime('12:00 PM');
            $endTime = strtotime('5:00 PM');
        } elseif (strpos($availabilityLower, 'evening') !== false) {
            $startTime = strtotime('5:00 PM');
            $endTime = strtotime('9:00 PM');
        } elseif (strpos($availabilityLower, 'weekend') !== false) {
            $startTime = strtotime('9:00 AM');
            $endTime = strtotime('6:00 PM');
        } else {
            $startTime = strtotime('9:00 AM');
            $endTime = strtotime('5:00 PM');
        }
    }

    while ($startTime < $endTime) {
        $timeSlots[] = date("g:i A", $startTime);
        $startTime = strtotime("+30 minutes", $startTime);
    }

    return $timeSlots;
}

// Handle booking form submission (no payment popup, just confirm booking)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_appointment'])) {
    $therapist_id = $_POST['therapist_id'] ?? null;
    $appointment_time = $_POST['appointment_time'] ?? null;
    $mode = $_POST['mode'] ?? null;
    $appointment_date = $_POST['appointment_date'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $description = $_POST['description'] ?? '';

    // Fetch therapist fee using prepared statement
    $feeAmount = 0;
    $stmtFee = $conn->prepare("SELECT fees FROM therapists WHERE id = ? LIMIT 1");
    $stmtFee->bind_param("s", $therapist_id);
    $stmtFee->execute();
    $feeResult = $stmtFee->get_result();
    if ($feeRow = $feeResult->fetch_assoc()) {
        $feeAmount = floatval($feeRow['fees']);
    }
    $stmtFee->close();

    // Insert appointment with amount
    $stmt = $conn->prepare("INSERT INTO appointments (user_id, therapist_id, appointment_date, appointment_time, mode, phone, description, status, amount) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', ?)");
    $stmt->bind_param("issssssd", $userId, $therapist_id, $appointment_date, $appointment_time, $mode, $phone, $description, $feeAmount);

    if ($stmt->execute()) {
        echo "<script>alert('Appointment booked successfully!'); window.location.href='view_bookings.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error booking appointment.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Filtered Therapists - SerenityConnect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            padding: 2rem 1rem;
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

        .page-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }

        .therapists-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .therapist-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-light);
            transition: var(--transition);
            position: relative;
        }

        .therapist-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .therapist-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: var(--background-alt);
        }

        .card-content {
            padding: 1.5rem;
        }

        .therapist-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .therapist-info {
            margin-bottom: 1rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .info-item i {
            width: 20px;
            color: var(--accent);
            margin-right: 0.75rem;
            font-size: 0.8rem;
        }

        .info-item strong {
            margin-right: 0.5rem;
            color: var(--text-primary);
        }

        .info-item span {
            color: var(--text-secondary);
        }

        .fees-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            color: var(--primary);
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .book-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            padding: 1rem;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .book-btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
        }

        .booking-form {
            display: none;
            margin-top: 1.5rem;
            padding: 1.5rem;
            background: var(--background-alt);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--primary);
            font-size: 0.9rem;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.9rem;
            background: var(--surface);
            transition: var(--transition);
        }
        
        .form-input.error,
        .form-select.error {
            border-color: #ef4444; /* red-500 */
        }

        .form-input:focus,
        .form-select:focus {
            border-color: var(--primary-light);
            outline: none;
            box-shadow: 0 0 0 3px rgba(30, 58, 46, 0.1);
        }

        .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.9rem;
            background: var(--surface);
            transition: var(--transition);
            resize: vertical;
            min-height: 80px;
        }

        .submit-btn {
            width: 100%;
            background: var(--success);
            color: white;
            border: none;
            padding: 0.8rem;
            border-radius: var(--radius);
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 0.5rem;
        }

        .submit-btn:hover {
            background: #22c55e;
            transform: translateY(-1px);
        }

        .no-results {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }

        .no-results i {
            font-size: 4rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--surface);
            color: var(--primary);
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            transition: var(--transition);
            margin-bottom: 2rem;
        }

        .back-btn:hover {
            background: var(--background-alt);
            transform: translateX(-2px);
        }
        
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        /* Payment Popup Styles */
        .payment-popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .payment-popup-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .payment-popup-content {
            background: #fff;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.15);
            min-width: 320px;
            max-width: 90vw;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .payment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .payment-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        .payment-close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.2s;
        }
        
        .payment-close-btn:hover {
            color: var(--text-primary);
        }
        
        .payment-form .form-group {
            margin-bottom: 1rem;
        }
        
        .payment-form .form-label {
            color: var(--text-secondary);
        }
        
        .payment-form .form-input {
            margin-top: 0.25rem;
        }
        
        .payment-details {
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
            color: var(--text-primary);
        }

        .payment-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
        }

        .pay-now-btn {
            background: var(--success);
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .pay-now-btn:hover {
            background: #22c55e;
        }
        
        .cancel-btn {
            background: var(--border);
            color: var(--text-secondary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .cancel-btn:hover {
            background: #d1d5db;
        }

        @media (max-width: 768px) {
            .therapists-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            body {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="page-header">
        <a href="javascript:history.back()" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            Back to Search
        </a>
        <h1 class="page-title">Your Therapist Matches</h1>
        <p class="page-subtitle">Browse through therapists that match your preferences</p>
    </div>

    <div class="therapists-grid">
        <?php
        $therapistCount = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $therapistCount++;
            $therapistId = $row['id'];
            $imagePath = "../uploads/" . htmlspecialchars($row['image']);
            $defaultImage = "../uploads/default.png";
            $finalImage = (!empty($row['image']) && file_exists($imagePath)) ? $imagePath : $defaultImage;
            $timeSlots = generateTimeSlots($row['availability']);

            // Fetch reviews for this therapist
            $reviewStmt = $conn->prepare("SELECT r.rating, r.comment, u.name AS reviewer FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.therapist_id = ?");
            $reviewStmt->bind_param("s", $therapistId);
            $reviewStmt->execute();
            $reviewsResult = $reviewStmt->get_result();
            $reviews = [];
            while ($reviewRow = $reviewsResult->fetch_assoc()) {
                $reviews[] = $reviewRow;
            }
            $reviewStmt->close();
        ?>
            <div class="therapist-card">
                <img src="<?php echo $finalImage; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="therapist-image">
                
                <div class="card-content">
                    <h3 class="therapist-name"><?php echo htmlspecialchars($row['name']); ?></h3>
                    
                    <div class="therapist-info">
                        <div class="info-item">
                            <i class="fas fa-venus-mars"></i>
                            <strong>Gender:</strong>
                            <span><?php echo htmlspecialchars($row['gender']); ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-stethoscope"></i>
                            <strong>Specialization:</strong>
                            <span><?php echo htmlspecialchars($row['specialization']); ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-medal"></i>
                            <strong>Experience:</strong>
                            <span><?php echo htmlspecialchars($row['experience']); ?> years</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-language"></i>
                            <strong>Languages:</strong>
                            <span><?php echo htmlspecialchars($row['language']); ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-video"></i>
                            <strong>Session Types:</strong>
                            <span><?php echo htmlspecialchars($row['mode']); ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <strong>Available:</strong>
                            <span><?php echo ucfirst(htmlspecialchars($row['availability'])); ?></span>
                        </div>
                    </div>
                    
                    <div class="fees-badge">
                        <i class="fas fa-dollar-sign"></i>
                        <span class="therapist-fees"><?php echo htmlspecialchars($row['fees']); ?></span> per session
                    </div>

                    <button class="book-btn" 
                            data-therapist-id="<?php echo $therapistId; ?>"
                            data-therapist-name="<?php echo htmlspecialchars($row['name']); ?>"
                            data-therapist-fees="<?php echo htmlspecialchars($row['fees']); ?>"
                            onclick="toggleForm('form_<?php echo $therapistId; ?>')">
                        <i class="fas fa-calendar-plus"></i>
                        Book Appointment
                    </button>

                    <div class="booking-form" id="form_<?php echo $therapistId; ?>">
                        <form id="bookingForm_<?php echo $therapistId; ?>" method="POST">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-clock"></i>
                                    Preferred Time
                                </label>
                                <select name="appointment_time" class="form-select" required>
                                    <option value="">Select a time slot</option>
                                    <?php
                                    if (!empty($timeSlots)) {
                                        foreach ($timeSlots as $slot) {
                                            echo "<option value='" . htmlspecialchars($slot) . "'>" . htmlspecialchars($slot) . "</option>";
                                        }
                                    } else {
                                        echo "<option disabled>No available slots</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-video"></i>
                                    Session Type
                                </label>
                                <select name="mode" class="form-select" required>
                                    <option value="">Choose session type</option>
                                    <?php
                                    $modesAvailable = array_map('trim', explode(',', $row['mode']));
                                    foreach ($modesAvailable as $modeOption) {
                                        $cleaned = htmlspecialchars($modeOption);
                                        echo "<option value='$cleaned'>$cleaned</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar"></i>
                                    Appointment Date
                                </label>
                                <input type="date" name="appointment_date" class="form-input" required 
                                       min="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone"></i>
                                    Phone Number
                                </label>
                                <input type="tel" name="phone" class="form-input" required 
                                       placeholder="Enter your phone number">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-comment"></i>
                                    Additional Notes (Optional)
                                </label>
                                <textarea name="description" class="form-textarea" 
                                          placeholder="Any specific concerns or requests..."></textarea>
                            </div>

                            <input type="hidden" name="therapist_id" value="<?php echo $therapistId; ?>">
                            <button type="submit" name="book_appointment" class="submit-booking-btn">
                                <i class="fas fa-check"></i>
                                Confirm Booking
                            </button>
                        </form>
                    </div>

                    <!-- Therapist Reviews Section -->
                    <div class="therapist-reviews" style="margin-top:1.5rem;">
                        <h4 style="color:var(--primary);margin-bottom:0.5rem;"><i class="fas fa-star"></i> Reviews</h4>
                        <?php if (count($reviews) > 0): ?>
                            <?php foreach ($reviews as $review): ?>
                                <div style="background:var(--background-alt);border-radius:8px;padding:0.75rem 1rem;margin-bottom:0.5rem;">
                                    <div style="font-size:1.1rem;color:var(--accent);font-weight:600;">
                                        <?php echo str_repeat('★', intval($review['rating'])); ?>
                                        <?php echo str_repeat('☆', 5-intval($review['rating'])); ?>
                                        <span style="color:var(--text-secondary);font-size:0.95rem;">by <?php echo htmlspecialchars($review['reviewer']); ?></span>
                                    </div>
                                    <div style="margin-top:0.3rem;color:var(--text-primary);font-size:0.98rem;">
                                        <?php echo htmlspecialchars($review['comment']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="color:var(--text-muted);font-size:0.95rem;">No reviews yet for this therapist.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php if ($therapistCount == 0): ?>
        <div class="no-results">
            <i class="fas fa-search"></i>
            <h3>No therapists found matching your criteria</h3>
            <p>Try adjusting your filters to see more results</p>
            <a href="javascript:history.back()" class="back-btn" style="margin-top: 1rem;">
                <i class="fas fa-arrow-left"></i>
                Modify Search
            </a>
        </div>
    <?php endif; ?>

    <script>
        function toggleForm(id) {
            const form = document.getElementById(id);
            const isVisible = form.style.display === "block";
            
            document.querySelectorAll('.booking-form').forEach(f => {
                if (f.id !== id) f.style.display = "none";
            });
            
            form.style.display = isVisible ? "none" : "block";
            
            if (!isVisible) {
                form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    </script>
</body>
</html>