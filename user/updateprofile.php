<?php
session_start();
include('../connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch current user
$stmt = $conn->prepare("SELECT id, name, email, phone, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Split name into first/last
if ($user && !empty($user['name'])) {
    $name_parts = explode(' ', $user['name'], 2);
    $user['first_name'] = $name_parts[0] ?? '';
    $user['last_name'] = $name_parts[1] ?? '';
}

$success = $error = "";

// Update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        $error = "Security token mismatch. Please try again.";
    } else {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        // Validate profile fields
        if (!preg_match('/^[a-zA-Z\s\'.-]{2,25}$/', $first_name)) {
            $error = "Invalid first name.";
        } elseif (!preg_match('/^[a-zA-Z\s\'.-]{2,25}$/', $last_name)) {
            $error = "Invalid last name.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email address.";
        } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
            $error = "Phone number must be 10 digits.";
        } else {
            $full_name = $first_name . " " . $last_name;
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=? WHERE id=?");
            $stmt->bind_param("sssi", $full_name, $email, $phone, $user_id);
            if ($stmt->execute()) {
                $success = "Profile updated successfully!";
                $user['first_name'] = $first_name;
                $user['last_name'] = $last_name;
                $user['email'] = $email;
                $user['phone'] = $phone;
            } else {
                $error = "Error updating profile.";
            }
        }

        // Password update
        // Password update
if (empty($error) && (!empty($_POST['current_password']) || !empty($_POST['new_password']) || !empty($_POST['confirm_password']))) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Re-fetch latest password from DB
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_db_password);
    $stmt->fetch();
    $stmt->close();

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All password fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New password and confirm password do not match.";
    } elseif (!password_verify($current_password, $hashed_db_password)) {
        $error = "Current password is incorrect.";
    } elseif (strlen($new_password) < 10 || 
              !preg_match('/[A-Z]/', $new_password) || 
              !preg_match('/[0-9]/', $new_password) || 
              !preg_match('/[^A-Za-z0-9]/', $new_password)) {
        $error = "New password must be at least 10 characters long and include one uppercase letter, one number, and one special character.";
    } else {
        $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $new_hashed_password, $user_id);
        if ($stmt->execute()) {
            $success .= " Password updated successfully!";
            $user['password'] = $new_hashed_password; // update local user array
        } else {
            $error = "Error updating password.";
        }
    }
}

    }
}

// CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Profile</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    body {font-family:'Segoe UI',sans-serif;background: linear-gradient(135deg, #134d13ff, #1a3f00);

;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px;}
    .container {background:#fff;padding:40px;border-radius:20px;box-shadow:0 20px 40px rgba(22, 92, 30, 0.1);width:100%;max-width:500px;}
    .header {text-align:center;margin-bottom:20px;}
    .form-group {margin-bottom:20px;position:relative;}
    .input-wrapper {position:relative;}
    .form-control {width:100%;padding:15px 45px;border-radius:10px;border:2px solid #e1e5e9;font-size:16px;}
    .input-wrapper i.fa-user,.input-wrapper i.fa-envelope,.input-wrapper i.fa-phone,.input-wrapper i.fa-lock {position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#667eea;}
    .toggle-password {position:absolute;right:15px;top:50%;transform:translateY(-50%);cursor:pointer;color:#666;}
    .btn-primary {width:100%;padding:15px;border:none;border-radius:10px;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;font-size:16px;font-weight:600;cursor:pointer;}
    .alert {padding:15px;border-radius:10px;margin-bottom:15px;}
    .alert-success {background:#d1f2eb;color:#0c6846;}
    .alert-error {background:#fdeaea;color:#c53030;}
    #passwordStrength {font-size:13px;margin-left:5px;}
    .input-wrapper {
    position: relative;
}

.input-wrapper .form-control {
    width: 100%;
    padding: 15px 45px 15px 45px; /* 45px left for icon, 45px right for toggle */
    border-radius: 10px;
    border: 2px solid #e1e5e9;
    font-size: 16px;
    box-sizing: border-box;
}

/* left icons (user, email, lock) */
.input-wrapper i.fa-user,
.input-wrapper i.fa-envelope,
.input-wrapper i.fa-phone,
.input-wrapper i.fa-lock {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #1c5b2dff;
}

/* right toggle eye icon */
.input-wrapper .toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #666;
    font-size: 16px;
}

.input-wrapper .toggle-password:hover {
    color: #333;
}

</style>
</head>
<body>
<div class="container">
    <div class="header"><h2><i class="fas fa-user-edit"></i> Update Profile</h2></div>

    <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="POST" onsubmit="return validateForm()">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <div class="form-group">
            <label>First Name</label>
            <div class="input-wrapper"><i class="fas fa-user"></i>
                <input type="text" id="first_name" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Last Name</label>
            <div class="input-wrapper"><i class="fas fa-user"></i>
                <input type="text" id="last_name" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Email</label>
            <div class="input-wrapper"><i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Phone</label>
            <div class="input-wrapper"><i class="fas fa-phone"></i>
                <input type="tel" id="phone" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" maxlength="10" required>
            </div>
        </div>

        <h3><i class="fas fa-lock"></i> Change Password</h3>

        <div class="form-group">
            <label>Current Password</label>
            <div class="input-wrapper"><i class="fas fa-lock"></i>
                <input type="password" id="current_password" name="current_password" class="form-control">
                <i class="fas fa-eye toggle-password" onclick="togglePassword('current_password', this)"></i>
            </div>
        </div>

        <div class="form-group">
            <label>New Password</label>
            <div class="input-wrapper"><i class="fas fa-lock"></i>
                <input type="password" id="new_password" name="new_password" class="form-control">
                <i class="fas fa-eye toggle-password" onclick="togglePassword('new_password', this)"></i>
            </div>
            <small id="passwordStrength"></small>
        </div>

        <div class="form-group">
            <label>Confirm New Password</label>
            <div class="input-wrapper"><i class="fas fa-lock"></i>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password', this)"></i>
            </div>
        </div>

        <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Update</button>
        <button type="button" class="btn-primary" style="margin-top:10px;background:linear-gradient(135deg,#444,#222);" onclick="window.history.back();">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </form>
</div>

<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    if (input.type === "password") {
        input.type = "text"; icon.classList.replace("fa-eye","fa-eye-slash");
    } else {
        input.type = "password"; icon.classList.replace("fa-eye-slash","fa-eye");
    }
}

// Password strength meter
document.getElementById("new_password").addEventListener("input", function() {
    const val = this.value;
    const strength = document.getElementById("passwordStrength");
    let msg="", color="red";

    const hasUpper = /[A-Z]/.test(val);
    const hasNumber = /\d/.test(val);
    const hasSpecial = /[^A-Za-z0-9]/.test(val);

    if (val.length < 6) {
        msg = "Too Short"; color = "red";
    } else if (hasUpper && hasNumber && hasSpecial) {
        msg = "Strong"; color = "green";
    } else if ((hasUpper && hasNumber) || (hasUpper && hasSpecial) || (hasNumber && hasSpecial)) {
        msg = "Medium"; color = "orange";
    } else {
        msg = "Weak"; color = "red";
    }

    strength.textContent = msg;
    strength.style.color = color;
});

function showError(msg,id){alert(msg); if(id) document.getElementById(id).focus(); return false;}

function validateForm() {
    const firstName=document.getElementById("first_name").value.trim();
    const lastName=document.getElementById("last_name").value.trim();
    const email=document.getElementById("email").value.trim();
    const phone=document.getElementById("phone").value.trim();
    const current=document.getElementById("current_password").value;
    const newPass=document.getElementById("new_password").value;
    const confirm=document.getElementById("confirm_password").value;

    const nameRegex=/^[a-zA-Z\s'.-]{2,25}$/;
    const emailRegex=/^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phoneRegex=/^[0-9]{10}$/;
    const passRegex=/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{6,}$/;

    if(!nameRegex.test(firstName)) return showError("Invalid first name","first_name");
    if(!nameRegex.test(lastName)) return showError("Invalid last name","last_name");
    if(!emailRegex.test(email)) return showError("Invalid email","email");
    if(!phoneRegex.test(phone)) return showError("Phone must be 10 digits","phone");

    if(current || newPass || confirm) {
        if(!current) return showError("Enter current password","current_password");
        if(!passRegex.test(newPass)) return showError("Password must be at least 10 characters and include one uppercase, one number, and one special character","new_password");
        if(newPass!==confirm) return showError("Passwords do not match","confirm_password");
    }
    return true;
}
</script>
</body>
</html>
