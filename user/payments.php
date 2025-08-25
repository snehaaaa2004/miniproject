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
  /* Professional Color System */
  --color-primary: #4f46e5; /* Refined indigo */
  --color-primary-50: #eef2ff;
  --color-primary-100: #e0e7ff;
  --color-primary-500: #6366f1;
  --color-primary-600: #4f46e5;
  --color-primary-700: #4338ca;
  --color-primary-900: #312e81;

  --color-secondary: #059669; /* Professional teal */
  --color-secondary-50: #ecfdf5;
  --color-secondary-100: #d1fae5;
  --color-secondary-500: #10b981;
  --color-secondary-600: #059669;
  --color-secondary-700: #047857;

  --color-accent: #f59e0b; /* Warm amber accent */
  --color-accent-50: #fffbeb;
  --color-accent-500: #f59e0b;
  --color-accent-600: #d97706;

  --color-error: #dc2626;
  --color-error-50: #fef2f2;
  --color-success: #16a34a;
  --color-success-50: #f0fdf4;

  /* Neutral Color Scale */
  --color-white: #ffffff;
  --color-gray-25: #fcfcfd;
  --color-gray-50: #f9fafb;
  --color-gray-100: #f3f4f6;
  --color-gray-200: #e5e7eb;
  --color-gray-300: #d1d5db;
  --color-gray-400: #9ca3af;
  --color-gray-500: #6b7280;
  --color-gray-600: #4b5563;
  --color-gray-700: #374151;
  --color-gray-800: #1f2937;
  --color-gray-900: #111827;

  /* Typography Scale */
  --font-family-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
  --font-family-mono: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace;
  
  --font-weight-light: 300;
  --font-weight-normal: 400;
  --font-weight-medium: 500;
  --font-weight-semibold: 600;
  --font-weight-bold: 700;

  --font-size-xs: 0.75rem;   /* 12px */
  --font-size-sm: 0.875rem;  /* 14px */
  --font-size-base: 1rem;    /* 16px */
  --font-size-lg: 1.125rem;  /* 18px */
  --font-size-xl: 1.25rem;   /* 20px */
  --font-size-2xl: 1.5rem;   /* 24px */
  --font-size-3xl: 1.875rem; /* 30px */

  --line-height-tight: 1.25;
  --line-height-normal: 1.5;
  --line-height-relaxed: 1.625;

  /* Spacing System (8px base unit) */
  --space-1: 0.25rem;  /* 4px */
  --space-2: 0.5rem;   /* 8px */
  --space-3: 0.75rem;  /* 12px */
  --space-4: 1rem;     /* 16px */
  --space-5: 1.25rem;  /* 20px */
  --space-6: 1.5rem;   /* 24px */
  --space-8: 2rem;     /* 32px */
  --space-10: 2.5rem;  /* 40px */
  --space-12: 3rem;    /* 48px */
  --space-16: 4rem;    /* 64px */

  /* Border Radius */
  --radius-none: 0px;
  --radius-sm: 0.375rem;  /* 6px */
  --radius-md: 0.5rem;    /* 8px */
  --radius-lg: 0.75rem;   /* 12px */
  --radius-xl: 1rem;      /* 16px */
  --radius-2xl: 1.5rem;   /* 24px */

  /* Professional Shadow System */
  --shadow-xs: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
  --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
  --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
  --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
  --shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);

  /* Focus Ring */
  --focus-ring: 0 0 0 3px rgb(79 70 229 / 0.1);
  --focus-ring-offset: 2px;

  /* Animation */
  --transition-fast: 150ms ease-in-out;
  --transition-normal: 300ms ease-in-out;
  --transition-slow: 500ms ease-in-out;
}

/* Reset and Base Styles */
*, *::before, *::after {
  box-sizing: border-box;
}

body {
  margin: 0;
  padding: var(--space-8);
  font-family: var(--font-family-sans);
  font-size: var(--font-size-base);
  line-height: var(--line-height-normal);
  color: var(--color-gray-900);
  background: linear-gradient(135deg, var(--color-gray-50) 0%, var(--color-primary-50) 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Card Component */
.card {
  background-color: var(--color-white);
  border: 1px solid var(--color-gray-200);
  border-radius: var(--radius-2xl);
  box-shadow: var(--shadow-lg);
  max-width: 28rem; /* 448px */
  width: 100%;
  padding: var(--space-8);
  position: relative;
  overflow: hidden;
  transition: all var(--transition-normal);
}

.card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--color-primary-600), var(--color-secondary-600), var(--color-accent-600));
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-xl);
  border-color: var(--color-gray-300);
}

.card-title {
  margin: 0 0 var(--space-6);
  font-size: var(--font-size-2xl);
  font-weight: var(--font-weight-bold);
  line-height: var(--line-height-tight);
  color: var(--color-gray-900);
  text-align: center;
  letter-spacing: -0.025em;
}

/* Amount Display */
.amount-display {
  background: linear-gradient(135deg, var(--color-primary-50), var(--color-secondary-50));
  border: 2px solid var(--color-primary-100);
  border-radius: var(--radius-xl);
  padding: var(--space-4) var(--space-5);
  margin: var(--space-4) 0 var(--space-6);
  font-size: var(--font-size-xl);
  font-weight: var(--font-weight-semibold);
  color: var(--color-primary-700);
  text-align: center;
  position: relative;
  overflow: hidden;
}

.amount-display::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 4px;
  background: linear-gradient(180deg, var(--color-primary-600), var(--color-secondary-600));
}

/* Form Elements */
.form-group {
  margin-bottom: var(--space-5);
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-4);
}

label {
  display: block;
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-medium);
  color: var(--color-gray-700);
  margin-bottom: var(--space-2);
  letter-spacing: 0.025em;
}

input[type="text"],
input[type="number"],
input[type="email"] {
  width: 100%;
  padding: var(--space-3) var(--space-4);
  border: 2px solid var(--color-gray-300);
  border-radius: var(--radius-lg);
  font-size: var(--font-size-base);
  font-family: inherit;
  background-color: var(--color-white);
  transition: all var(--transition-fast);
  appearance: none;
}

input:focus {
  outline: none;
  border-color: var(--color-primary-600);
  box-shadow: var(--focus-ring);
  background-color: var(--color-white);
}

input:hover:not(:focus) {
  border-color: var(--color-gray-400);
}

input::placeholder {
  color: var(--color-gray-400);
}

/* Button System */
.button-group {
  display: flex;
  gap: var(--space-3);
  margin-top: var(--space-8);
}

.btn {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: var(--space-3) var(--space-6);
  border: 2px solid transparent;
  border-radius: var(--radius-lg);
  font-size: var(--font-size-base);
  font-weight: var(--font-weight-semibold);
  font-family: inherit;
  text-decoration: none;
  cursor: pointer;
  transition: all var(--transition-fast);
  position: relative;
  min-height: 44px; /* Accessibility: minimum touch target */
}

.btn:focus {
  outline: none;
  box-shadow: var(--focus-ring);
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

.btn-primary {
  background: linear-gradient(135deg, var(--color-primary-600), var(--color-primary-700));
  color: var(--color-white);
  box-shadow: var(--shadow-sm);
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, var(--color-primary-700), var(--color-primary-900));
  box-shadow: var(--shadow-md);
  transform: translateY(-1px);
}

.btn-secondary {
  background: var(--color-white);
  color: var(--color-gray-700);
  border-color: var(--color-gray-300);
  box-shadow: var(--shadow-xs);
}

.btn-secondary:hover:not(:disabled) {
  background: var(--color-gray-50);
  border-color: var(--color-gray-400);
  box-shadow: var(--shadow-sm);
  transform: translateY(-1px);
}

.btn-success {
  background: linear-gradient(135deg, var(--color-secondary-600), var(--color-secondary-700));
  color: var(--color-white);
  box-shadow: var(--shadow-sm);
}

.btn-success:hover:not(:disabled) {
  background: linear-gradient(135deg, var(--color-secondary-700), var(--color-secondary-600));
  box-shadow: var(--shadow-md);
  transform: translateY(-1px);
}

/* Message Components */
.message {
  margin-top: var(--space-5);
  padding: var(--space-4);
  border-radius: var(--radius-lg);
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-medium);
  text-align: center;
  border: 1px solid transparent;
}

.message.success {
  background-color: var(--color-success-50);
  color: var(--color-secondary-700);
  border-color: var(--color-secondary-600);
}

.message.error {
  background-color: var(--color-error-50);
  color: var(--color-error);
  border-color: var(--color-error);
}

.message.info {
  background-color: var(--color-primary-50);
  color: var(--color-primary-700);
  border-color: var(--color-primary-600);
}

/* Link Styling */
.link-back {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-top: var(--space-6);
  color: var(--color-primary-600);
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-medium);
  text-decoration: none;
  transition: color var(--transition-fast);
  width: 100%;
}

.link-back:hover {
  color: var(--color-primary-700);
  text-decoration: underline;
  text-underline-offset: 4px;
}

/* Utility Classes */
.text-center { text-align: center; }
.text-left { text-align: left; }
.text-right { text-align: right; }

.font-light { font-weight: var(--font-weight-light); }
.font-normal { font-weight: var(--font-weight-normal); }
.font-medium { font-weight: var(--font-weight-medium); }
.font-semibold { font-weight: var(--font-weight-semibold); }
.font-bold { font-weight: var(--font-weight-bold); }

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

/* Responsive Design */
@media (max-width: 640px) {
  body {
    padding: var(--space-4);
    align-items: flex-start;
    padding-top: var(--space-8);
  }

  .card {
    padding: var(--space-6);
    border-radius: var(--radius-xl);
    max-width: none;
    box-shadow: var(--shadow-md);
  }

  .card-title {
    font-size: var(--font-size-xl);
    margin-bottom: var(--space-5);
  }

  .form-row {
    grid-template-columns: 1fr;
    gap: var(--space-3);
  }

  .button-group {
    flex-direction: column;
    gap: var(--space-3);
    margin-top: var(--space-6);
  }

  .btn {
    min-height: 48px; /* Larger touch targets on mobile */
  }

  .amount-display {
    font-size: var(--font-size-lg);
    padding: var(--space-3) var(--space-4);
  }
}

@media (max-width: 480px) {
  body {
    padding: var(--space-3);
  }

  .card {
    padding: var(--space-4);
  }
}

/* Accessibility Enhancements */
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }

  .card:hover {
    transform: none;
  }

  .btn:hover:not(:disabled) {
    transform: none;
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .card {
    border: 2px solid var(--color-gray-900);
  }

  input {
    border: 2px solid var(--color-gray-900);
  }

  .btn {
    border: 2px solid currentColor;
  }
}

/* Dark mode support (basic implementation) */
@media (prefers-color-scheme: dark) {
  :root {
    --color-gray-900: #f9fafb;
    --color-gray-800: #f3f4f6;
    --color-gray-700: #e5e7eb;
    --color-gray-600: #d1d5db;
    --color-gray-500: #9ca3af;
    --color-gray-400: #6b7280;
    --color-gray-300: #4b5563;
    --color-gray-200: #374151;
    --color-gray-100: #1f2937;
    --color-gray-50: #111827;
    --color-white: #1f2937;
  }

  body {
    background: linear-gradient(135deg, var(--color-gray-900) 0%, var(--color-gray-800) 100%);
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