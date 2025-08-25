<?php
session_start();
include('../connect.php');

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access payments.");
}

$user_id = (int)$_SESSION['user_id'];
$appointment_id = (int)($_POST['appointment_id'] ?? $_GET['appointment_id'] ?? 0);

$feeAmount = 0.0;
$errorMsg = '';
$done = false;
$finalStatus = '';

if ($appointment_id > 0) {
    $sql = "
        SELECT t.fees AS fee
        FROM appointments a
        JOIN therapists t ON a.therapist_id = t.id
        WHERE a.id = ? AND a.user_id = ?
        LIMIT 1
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $appointment_id, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $feeAmount = (float)$row['fee'];
    } else {
        $errorMsg = "Invalid appointment.";
    }
    $stmt->close();
} else {
    $errorMsg = "Invalid appointment ID.";
}

if (empty($errorMsg) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'Pay') {
        $card_no = trim($_POST['card_no'] ?? '');
        $expiry  = trim($_POST['expiry'] ?? '');
        $cvv     = trim($_POST['cvv'] ?? '');

        if ($card_no === '' || $expiry === '' || $cvv === '') {
            $errorMsg = "Please enter all card details.";
        } else {
            $finalStatus = 'Completed';
            $ins = $conn->prepare("
                INSERT INTO payments (appointment_id, amount, payment_status, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            $ins->bind_param("ids", $appointment_id, $feeAmount, $finalStatus);
            if ($ins->execute()) {
                $done = true;
            } else {
                $errorMsg = "Failed to record payment. Please try again.";
            }
            $ins->close();
        }
    } elseif ($action === 'Cancel') {
        header("Location: user.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment</title>
<style>
  /*
  1. Base and Utility Styles: Keep base styles clean and use utility classes for
     common patterns to avoid repetition.
  2. Component-based approach: Style components like 'card' and 'button' with
     clear, self-contained rules.
  3. CSS variables: Use custom properties (variables) for colors, spacing, and
     fonts to make it easy to update the design. This is the most significant
     improvement for maintainability.
  4. Better organization: Group related styles together using comments.
  5. Refined aesthetics: Adjust some values for better visual balance and
     modern appeal.
*/

/* Enhanced Base Styles with a more colorful palette */
/* Enhanced Base Styles with a more colorful palette */
/* Enhanced Base Styles with a more colorful palette */
:root {
  /* Vibrant Color Palette */
  --color-primary: #8b5cf6; /* A rich violet */
  --color-primary-light: #a78bfa;
  --color-primary-dark: #6d28d9;

  --color-secondary: #f43f5e; /* A lively rose */
  --color-secondary-light: #fda4af;
  --color-secondary-dark: #e11d48;
  
  --color-accent-1: #34d399; /* A bright green */
  --color-accent-2: #facc15; /* A golden yellow */
  
  --color-neutral-100: #f1f5f9;
  --color-neutral-200: #e2e8f0;
  --color-neutral-500: #94a3b8;
  --color-neutral-700: #1e293b;
  --color-white: #ffffff;

  /* Spacing */
  --space-xs: 6px;
  --space-sm: 10px;
  --space-md: 18px;
  --space-lg: 28px;
  --space-xl: 48px;

  /* Border Radius */
  --radius-sm: 8px;
  --radius-md: 14px;
  --radius-lg: 20px;

  /* Shadows - a little more pronounced */
  --shadow-sm: 0 4px 6px rgba(0, 0, 0, 0.08);
  --shadow-md: 0 10px 15px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 20px 25px rgba(0, 0, 0, 0.15);

  /* Fonts */
  --font-family-base: 'Inter', 'Arial', sans-serif;
  --font-weight-regular: 400;
  --font-weight-medium: 500;
  --font-weight-semibold: 600;
  --font-weight-bold: 700;
  --font-size-sm: 0.875rem;
  --font-size-base: 1rem;
  --font-size-lg: 1.125rem;
  --font-size-xl: 1.5rem;
  --font-size-2xl: 2rem;
}

/* Base Body and Layout Styles */
body {
  font-family: var(--font-family-base);
  background: linear-gradient(135deg, #fce7f3, #e0e7ff);
  margin: 0;
  padding: var(--space-xl);
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  color: var(--color-neutral-700);
}

/* Card Component Styling */
.card {
  background-color: var(--color-white);
  max-width: 460px;
  width: 100%;
  padding: var(--space-lg) var(--space-xl);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md);
  transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-lg);
}

.card-title {
  margin-bottom: var(--space-md);
  color: var(--color-primary-dark);
  font-size: var(--font-size-xl);
  font-weight: var(--font-weight-bold);
  text-align: center;
}

/* Amount Display Styling */
.amount-display {
  background-color: var(--color-neutral-100);
  border: 1px solid var(--color-neutral-200);
  border-radius: var(--radius-md);
  padding: var(--space-sm) var(--space-md);
  margin: var(--space-sm) 0 var(--space-lg);
  font-weight: var(--font-weight-semibold);
  font-size: var(--font-size-lg);
  color: var(--color-secondary);
  text-align: center;
  border-left: 5px solid var(--color-accent-1);
}

/* Form Components Styling */
label {
  display: block;
  font-size: var(--font-size-sm);
  color: var(--color-neutral-500);
  margin-top: var(--space-md);
  margin-bottom: var(--space-xs);
  font-weight: var(--font-weight-medium);
}

input {
  width: 100%;
  padding: var(--space-sm);
  border: 1px solid var(--color-neutral-200);
  border-radius: var(--radius-sm);
  font-size: var(--font-size-base);
  transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

input:focus {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.25);
  outline: none;
}

.form-row {
  display: flex;
  gap: var(--space-md);
}

.form-row .col {
  flex: 1;
}

/* Button Styling */
.button-group {
  display: flex;
  gap: var(--space-md);
  margin-top: var(--space-lg);
}

.btn {
  flex: 1;
  padding: var(--space-sm) var(--space-md);
  border: none;
  border-radius: var(--radius-md);
  font-weight: var(--font-weight-semibold);
  font-size: var(--font-size-base);
  cursor: pointer;
  transition: background-color 0.2s ease-in-out, transform 0.1s ease-in-out, box-shadow 0.2s ease-in-out;
  box-shadow: var(--shadow-sm);
  color: var(--color-white);
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.btn-primary {
  background: linear-gradient(135deg, #f97316, #ea580c);
}

.btn-primary:hover {
  background: linear-gradient(135deg, #c2410c, #f97316);
}

.btn-secondary {
  background: linear-gradient(135deg, #22d3ee, #06b6d4);
}

.btn-secondary:hover {
  background: linear-gradient(135deg, #0e7490, #22d3ee);
}

/* Message Styling */
.message {
  margin-top: var(--space-md);
  padding: var(--space-sm);
  border-radius: var(--radius-md);
  text-align: center;
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-medium);
}

.message.success {
  background-color: var(--color-accent-1);
  color: var(--color-white);
  border: 1px solid var(--color-accent-1);
}

.message.error {
  background-color: var(--color-secondary);
  color: var(--color-white);
  border: 1px solid var(--color-secondary);
}

/* Link Styling */
.link-back {
  display: block;
  text-align: center;
  margin-top: var(--space-md);
  color: var(--color-accent-2);
  font-weight: var(--font-weight-semibold);
  text-decoration: none;
  transition: color 0.2s ease-in-out, text-decoration 0.2s ease-in-out;
}

.link-back:hover {
  color: var(--color-secondary-dark);
  text-decoration: underline;
}

/* Utility Class for Centering Text */
.text-center {
  text-align: center;
}

/* === Responsive Design === */
/* Adjusts layout for screens smaller than 768px (tablets and mobile) */
@media (max-width: 768px) {
  body {
    padding: var(--space-md);
    align-items: flex-start; /* Aligns content to the top on small screens */
  }

  .card {
    padding: var(--space-md) var(--space-lg); /* Reduces card padding */
    box-shadow: var(--shadow-sm); /* Lighter shadow for mobile */
  }

  .card-title {
    font-size: var(--font-size-lg); /* Smaller title on mobile */
  }

  .form-row {
    flex-direction: column; /* Stacks form elements vertically */
    gap: var(--space-sm);
  }

  .button-group {
    flex-direction: column; /* Stacks buttons vertically */
    gap: var(--space-sm);
  }

  .btn {
    width: 100%;
    padding: var(--space-sm);
  }
}




</style>
</head>
<body>
  <div class="card">
    <h2>Payment</h2>

    <?php if ($done && $finalStatus === 'Completed'): ?>
      <div class="msg success">✅ Payment Confirmed<br>Amount: $<?= number_format($feeAmount,2) ?></div>
      <a class="back" href="user.php">Back to Dashboard</a>
      <script>setTimeout(()=>location.href="user.php",2000);</script>
    <?php elseif ($done && $finalStatus === 'Cancelled'): ?>
      <div class="msg error">❎ Payment Cancelled</div>
      <a class="back" href="user.php">Back to Dashboard</a>
    <?php elseif ($errorMsg): ?>
      <div class="msg error"><?= htmlspecialchars($errorMsg) ?></div>
      <a class="back" href="user.php">Back to Dashboard</a>
    <?php else: ?>
      <div class="amount">Amount to Pay: $<?= number_format($feeAmount,2) ?></div>
      <form method="POST" action="payments.php" id="paymentForm">
        <input type="hidden" name="appointment_id" value="<?= (int)$appointment_id ?>">
       <label>Card Number</label>
<input type="text" 
       id="card_no" 
       name="card_no" 
       maxlength="19" 
       placeholder="1234 5678 9012 3456" 
       required>

        <div class="row">
          <div class="col">
            <label>Expiry (MM/YY)</label>
            <input type="text" id="expiry" name="expiry" maxlength="5" pattern="(0[1-9]|1[0-2])\/\d{2}" placeholder="MM/YY" required title="Enter expiry in MM/YY format">
          </div>
          <div class="col">
            <label>CVV</label>
            <input type="password" id="cvv" name="cvv" maxlength="4" pattern="\d{3,4}" placeholder="123" required title="Enter a 3 or 4-digit CVV">
          </div>
        </div>
        <div class="btns">
          <button type="submit" name="action" value="Pay" class="btn pay">Pay</button>
          <button type="submit" name="action" value="Cancel" class="btn cancel">Cancel</button>
        </div>
      </form>
    <?php endif; ?>
  </div>

<script>
const form   = document.getElementById('paymentForm');
const card   = document.getElementById('card_no');
const expiry = document.getElementById('expiry');
const cvv    = document.getElementById('cvv');

function showError(input, msg) {
  input.classList.add("error");
  input.classList.remove("success");
  let err = input.nextElementSibling;
  if (!err || !err.classList.contains("error-text")) {
    err = document.createElement("div");
    err.className = "error-text";
    input.insertAdjacentElement("afterend", err);
  }
  err.innerText = msg;
  err.style.display = "block";
}

function clearError(input) {
  input.classList.remove("error");
  input.classList.add("success");
  let err = input.nextElementSibling;
  if (err && err.classList.contains("error-text")) {
    err.style.display = "none";
  }
}

// Format card number #### #### #### ####
card.addEventListener("input", e => {
  let val = card.value.replace(/\D/g, "").substring(0,16);
  card.value = val.replace(/(.{4})/g, "$1 ").trim();
});

// Format expiry MM/YY
expiry.addEventListener("input", e => {
  let val = expiry.value.replace(/\D/g, "").substring(0,4);
  if (val.length >= 3) {
    expiry.value = val.substring(0,2) + "/" + val.substring(2);
  } else {
    expiry.value = val;
  }
});

// Prevent non-numeric in CVV
cvv.addEventListener("input", e => {
  cvv.value = cvv.value.replace(/\D/g,"").substring(0,4);
});

// Full form validation
form?.addEventListener('submit', function(e) {
  let valid = true;
  const cardNum = card.value.replace(/\s+/g, '');

  // Card check (only length now, no Luhn)
  if (!/^\d{16}$/.test(cardNum)) {
    showError(card, "Card must be 16 digits");
    valid = false;
  } else {
    clearError(card);
  }

  // Expiry check
  const expMatch = expiry.value.trim().match(/^(0[1-9]|1[0-2])\/(\d{2})$/);
  if (!expMatch) {
    showError(expiry, "Enter expiry as MM/YY");
    valid = false;
  } else {
    const month = parseInt(expMatch[1], 10);
    const year = 2000 + parseInt(expMatch[2], 10);
    const now = new Date();
    const exp = new Date(year, month, 0);
    if (exp < now) {
      showError(expiry, "Card expired");
      valid = false;
    } else {
      clearError(expiry);
    }
  }

  // CVV check
  if (!/^\d{3,4}$/.test(cvv.value)) {
    showError(cvv, "Enter 3 or 4 digit CVV");
    valid = false;
  } else {
    clearError(cvv);
  }

  if (!valid) e.preventDefault();
});
</script>

</body>
</html>