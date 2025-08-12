<?php
session_start();
include('../connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current details using prepared statement
$stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle backward compatibility if names are stored in single 'name' field
if (empty($user['first_name']) && empty($user['last_name'])) {
    $stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $temp_user = $result->fetch_assoc();
    
    if ($temp_user && !empty($temp_user['name'])) {
        $name_parts = explode(' ', $temp_user['name'], 2);
        $user['first_name'] = $name_parts[0] ?? '';
        $user['last_name'] = $name_parts[1] ?? '';
        $user['email'] = $temp_user['email'];
        $user['phone'] = $temp_user['phone'];
    }
}

// Initialize messages
$success = $error = "";

// Update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        $error = "Security token mismatch. Please try again.";
    } else {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        // Backend validation
        if (empty($first_name) || empty($last_name) || empty($email) || empty($phone)) {
            $error = "All fields are required.";
        } elseif (strlen($first_name) < 2 || strlen($first_name) > 25) {
            $error = "First name must be between 2 and 25 characters.";
        } elseif (strlen($last_name) < 2 || strlen($last_name) > 25) {
            $error = "Last name must be between 2 and 25 characters.";
        } elseif (!preg_match('/^[a-zA-Z\s\'.-]+$/', $first_name)) {
            $error = "First name can only contain letters, spaces, apostrophes, and hyphens.";
        } elseif (!preg_match('/^[a-zA-Z\s\'.-]+$/', $last_name)) {
            $error = "Last name can only contain letters, spaces, apostrophes, and hyphens.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
            $error = "Phone number must be exactly 10 digits.";
        } else {
            // Use prepared statement for update
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
            
            if ($stmt->execute()) {
                $success = "Profile updated successfully!";
                $user['first_name'] = $first_name;
                $user['last_name'] = $last_name;
                $user['email'] = $email;
                $user['phone'] = $phone;
            } else {
                $error = "Error updating profile. Please try again.";
            }
        }
    }
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile - Your Account</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            font-size: 16px;
        }

        .form-control {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-primary {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-success {
            background: #d1f2eb;
            color: #0c6846;
            border: 1px solid #7dd3b0;
        }

        .alert-error {
            background: #fdeaea;
            color: #c53030;
            border: 1px solid #feb2b2;
        }

        .alert i {
            margin-right: 8px;
        }

        #formError {
            display: none;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #764ba2;
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .header h2 {
                font-size: 24px;
            }
        }

        /* Loading state */
        .btn-primary.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-primary.loading::after {
            content: "";
            width: 16px;
            height: 16px;
            margin: 0 0 0 10px;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: button-loading-spinner 1s ease infinite;
            display: inline-block;
        }

        @keyframes button-loading-spinner {
            from {
                transform: rotate(0turn);
            }
            to {
                transform: rotate(1turn);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-user-edit"></i> Update Profile</h2>
            <p>Keep your information current and secure</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div id="formError" class="alert alert-error"></div>

        <form name="profileForm" method="POST" onsubmit="return handleSubmit(event)" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            
            <div class="form-group">
                <label for="first_name">First Name</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" 
                           id="first_name" 
                           name="first_name" 
                           class="form-control" 
                           value="<?= htmlspecialchars($user['first_name']) ?>" 
                           placeholder="Enter your first name"
                           maxlength="25"
                           required>
                </div>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" 
                           id="last_name" 
                           name="last_name" 
                           class="form-control" 
                           value="<?= htmlspecialchars($user['last_name']) ?>" 
                           placeholder="Enter your last name"
                           maxlength="25"
                           required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope"></i>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control" 
                           value="<?= htmlspecialchars($user['email']) ?>" 
                           placeholder="Enter your email address"
                           required>
                </div>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <div class="input-wrapper">
                    <i class="fas fa-phone"></i>
                    <input type="tel" 
                           id="phone" 
                           name="phone" 
                           class="form-control" 
                           value="<?= htmlspecialchars($user['phone']) ?>" 
                           placeholder="Enter 10-digit phone number"
                           maxlength="10"
                           pattern="[0-9]{10}"
                           required>
                </div>
            </div>

            <button type="submit" class="btn-primary" id="submitBtn">
                <i class="fas fa-save"></i> Update Profile
            </button>
        </form>

        <div class="back-link">
            <a href="../dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>

    <script>
        function showError(message) {
            const errorDiv = document.getElementById('formError');
            errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + message;
            errorDiv.style.display = 'block';
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function hideError() {
            document.getElementById('formError').style.display = 'none';
        }

        function validateForm() {
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();

            hideError();

            if (firstName === '' || lastName === '' || email === '' || phone === '') {
                showError('All fields are required.');
                return false;
            }

            if (firstName.length < 2 || firstName.length > 25) {
                showError('First name must be between 2 and 25 characters.');
                return false;
            }

            if (lastName.length < 2 || lastName.length > 25) {
                showError('Last name must be between 2 and 25 characters.');
                return false;
            }

            const nameRegex = /^[a-zA-Z\s'.-]+$/;
            if (!nameRegex.test(firstName)) {
                showError('First name can only contain letters, spaces, apostrophes, and hyphens.');
                return false;
            }

            if (!nameRegex.test(lastName)) {
                showError('Last name can only contain letters, spaces, apostrophes, and hyphens.');
                return false;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError('Please enter a valid email address.');
                return false;
            }

            const phoneRegex = /^[0-9]{10}$/;
            if (!phoneRegex.test(phone)) {
                showError('Phone number must be exactly 10 digits.');
                return false;
            }

            return true;
        }

        function handleSubmit(event) {
            if (!validateForm()) {
                event.preventDefault();
                return false;
            }

            // Add loading state
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Updating...';
            
            return true;
        }

        // Real-time validation feedback
        document.getElementById('phone').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });

        // Name validation - only allow letters, spaces, apostrophes, and hyphens
        function validateNameInput(e) {
            const value = e.target.value;
            const validChars = /^[a-zA-Z\s'.-]*$/;
            if (!validChars.test(value)) {
                e.target.value = value.replace(/[^a-zA-Z\s'.-]/g, '');
            }
            if (e.target.value.length > 25) {
                e.target.value = e.target.value.substring(0, 25);
            }
        }

        document.getElementById('first_name').addEventListener('input', validateNameInput);
        document.getElementById('last_name').addEventListener('input', validateNameInput);

        // Hide error messages when user starts typing
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', hideError);
        });
    </script>
</body>
</html>