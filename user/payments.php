<?php
session_start();
include('../connect.php');

// Validate user session
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access payments.");
}

$user_id = $_SESSION['user_id'];
$appointment_id = (int)($_POST['appointment_id'] ?? 0);
$feeAmount = 0;
$paymentSuccess = false;
$errorMsg = '';

// Fetch correct amount from appointments table
if ($appointment_id > 0) {
    $result = $conn->query("SELECT amount FROM appointments WHERE id = $appointment_id AND user_id = $user_id LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        $feeAmount = floatval($row['amount']);
    }
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['card_number'])) {
    $card_number = $_POST['card_number'] ?? '';
    $card_expiry = $_POST['card_expiry'] ?? '';
    $card_cvv = $_POST['card_cvv'] ?? '';
    $payment_status = 'confirm';

    // Basic validation
    if ($appointment_id <= 0 || $feeAmount <= 0 || empty($card_number) || empty($card_expiry) || empty($card_cvv)) {
        $errorMsg = 'Invalid input. Please fill all fields.';
    } else {
        // Only store payment info, not card details
        $stmt = $conn->prepare("INSERT INTO payments (appointment_id, amount, payment_status, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("ids", $appointment_id, $feeAmount, $payment_status);
        if ($stmt->execute()) {
            $paymentSuccess = true;
        } else {
            $errorMsg = 'Error: ' . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <style>
        .payment-card { max-width:400px; margin:3rem auto; padding:2rem; background:#fff; border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,0.1); }
        .payment-card h2 { margin-bottom:1rem; color:#333; }
        .payment-info { margin-bottom:1rem; }
        label { display:block; margin-top:1rem; font-weight:bold; }
        input { width:100%; padding:0.5rem; margin-top:0.3rem; border:1px solid #ccc; border-radius:8px; }
        .pay-btn { margin-top:1.5rem; width:100%; padding:0.8rem; background:#16a34a; color:white; border:none; border-radius:8px; font-size:1rem; cursor:pointer; }
        .pay-btn:hover { background:#15803d; }
        .success { background:#f0fff4; color:#16a34a; border:1px solid #22c55e; padding:1rem; border-radius:8px; text-align:center; margin-bottom:1rem; }
        .error { background:#fff0f0; color:#b91c1c; border:1px solid #f87171; padding:1rem; border-radius:8px; text-align:center; margin-bottom:1rem; }
    </style>
</head>
<body>

<div class="payment-card">
    <h2>Payment Details</h2>
    <?php if ($paymentSuccess): ?>
        <div class="success">
            <i class="fas fa-check-circle" style="font-size:2rem;"></i>
            <h3>Payment Successful!</h3>
            <p>Thank you for your payment.<br>Your transaction has been completed and your appointment is confirmed.</p>
            <div style="margin-top:1rem; color:#64748b; font-size:0.95rem;">Redirecting to dashboard...</div>
        </div>
        <script>setTimeout(function(){ window.location.href="user.php"; }, 3000);</script>
    <?php else: ?>
        <?php if ($errorMsg): ?>
            <div class="error"><?php echo $errorMsg; ?></div>
        <?php endif; ?>
        <div class="payment-info">
            <strong>Appointment ID:</strong> <?php echo $appointment_id; ?><br>
            <strong>Amount to Pay:</strong> $<?php echo number_format($feeAmount, 2); ?>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
            <label>Card Number</label>
            <input type="text" name="card_number" maxlength="16" required placeholder="16-digit card number">
            <label>Expiry Date</label>
            <input type="text" name="card_expiry" maxlength="5" required placeholder="MM/YY">
            <label>CVV</label>
            <input type="text" name="card_cvv" maxlength="4" required placeholder="CVV">
            <button type="submit" class="pay-btn">Pay Now</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
