<?php
session_start();
include 'connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  // Use prepared statement for security
  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];

    if ($user['role'] === 'Admin') {
      header("Location: admin/admindash.php");
    } elseif ($user['role'] === 'Therapist') {
      header("Location: therapist/therapihome.php");
    } else {
      header("Location: user/dash.php");
    }
    exit;
  } else {
    $error = "Invalid email or password. Please try again.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - SerenityConnect</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #2e4d3d;
      --primary-dark: #1e3b2b;
      --primary-light: #eaf4ed;
      --secondary: #3a5e4f;
      --accent: #f8c537;
      --text: #333333;
      --text-light: #666666;
      --background: #f8f6f3;
      --white: #ffffff;
      --border: #d9e0d9;
      --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      --shadow-hover: 0 8px 20px rgba(0, 0, 0, 0.12);
      --radius: 8px;
      --radius-lg: 12px;
      --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', 'Georgia', serif;
      background: linear-gradient(135deg, var(--background), #e0dcd9);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 1rem;
    }

    .login-container {
      background-color: var(--white);
      padding: 2.5rem;
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      width: 100%;
      max-width: 420px;
      border: 1px solid var(--border);
      position: relative;
      overflow: hidden;
    }

    .login-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 6px;
      background: linear-gradient(to right, var(--primary), var(--accent));
    }

    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 1.5rem;
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
      transition: var(--transition);
    }

    .back-btn:hover {
      color: var(--primary-dark);
    }

    .logo {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .logo-icon {
      font-size: 2.5rem;
      color: var(--primary);
      margin-bottom: 0.5rem;
    }

    .logo-text {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--primary);
      letter-spacing: 0.5px;
    }

    h2 {
      text-align: center;
      color: var(--primary);
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
      position: relative;
    }

    h2::after {
      content: '';
      position: absolute;
      bottom: -8px;
      left: 50%;
      transform: translateX(-50%);
      width: 50px;
      height: 3px;
      background-color: var(--accent);
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 1.25rem;
    }

    .form-group {
      position: relative;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
      color: var(--primary);
      font-weight: 500;
    }

    .form-control {
      width: 100%;
      padding: 0.9rem 1rem 0.9rem 2.5rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      font-size: 1rem;
      transition: var(--transition);
      background-color: #fefefe;
    }

    .form-control:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 3px rgba(46, 77, 61, 0.1);
    }

    .input-icon {
      position: absolute;
      left: 1rem;
      top: 2.5rem;
      color: var(--text-light);
      font-size: 1rem;
    }

    .btn {
      padding: 0.9rem;
      background-color: var(--primary);
      color: var(--white);
      font-size: 1rem;
      font-weight: 500;
      border: none;
      border-radius: var(--radius);
      cursor: pointer;
      transition: var(--transition);
      margin-top: 0.5rem;
    }

    .btn:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: var(--shadow-hover);
    }

    .error {
      color: #b00020;
      background-color: #fde8e8;
      padding: 0.9rem;
      border-radius: var(--radius);
      text-align: center;
      margin-bottom: 1rem;
      font-size: 0.9rem;
      border: 1px solid #f5c6cb;
    }

    .login-footer {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 0.9rem;
      color: var(--text-light);
    }

    .login-footer a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 500;
      transition: var(--transition);
    }

    .login-footer a:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }

    .divider {
      display: flex;
      align-items: center;
      margin: 1.5rem 0;
      color: var(--text-light);
      font-size: 0.8rem;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      border-bottom: 1px solid var(--border);
    }

    .divider::before {
      margin-right: 1rem;
    }

    .divider::after {
      margin-left: 1rem;
    }

    .social-login {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-top: 1rem;
    }

    .social-btn {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--white);
      border: 1px solid var(--border);
      color: var(--text);
      font-size: 1.1rem;
      transition: var(--transition);
    }

    .social-btn:hover {
      background: var(--primary-light);
      color: var(--primary);
      transform: translateY(-2px);
    }

    @media (max-width: 480px) {
      .login-container {
        padding: 1.5rem;
      }
      
      .logo-text {
        font-size: 1.3rem;
      }
      
      h2 {
        font-size: 1.3rem;
      }
    }
  </style>
</head>
<body>
  
  <div class="login-container">
    <a href="index.html" class="back-btn">
      <i class="fas fa-arrow-left"></i> Back to Home
    </a>

    <div class="logo">
      <div class="logo-icon">
        <i class="fas fa-leaf"></i>
      </div>
      <div class="logo-text">SerenityConnect</div>
    </div>

    <h2>Welcome Back</h2>

    <?php if ($error): ?>
      <div class="error">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="email">Email Address</label>
        <i class="fas fa-envelope input-icon"></i>
        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required />
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <i class="fas fa-lock input-icon"></i>
        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required />
      </div>

      <button type="submit" class="btn">
        <i class="fas fa-sign-in-alt"></i> Login
      </button>
    </form>

    <div class="login-footer">
      <a href="forgot-password.php">Forgot your password?</a>
    </div>

    <div class="divider">or</div>

    <div class="social-login">
      <a href="#" class="social-btn" title="Login with Google">
        <i class="fab fa-google"></i>
      </a>
      <a href="#" class="social-btn" title="Login with Facebook">
        <i class="fab fa-facebook-f"></i>
      </a>
    </div>

    <div class="login-footer">
      Don't have an account? <a href="register.html">Create one</a>
    </div>
  </div>

  <script>
    // Simple animation on load
    document.addEventListener('DOMContentLoaded', () => {
      const loginContainer = document.querySelector('.login-container');
      loginContainer.style.opacity = '0';
      loginContainer.style.transform = 'translateY(20px)';
      
      setTimeout(() => {
        loginContainer.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        loginContainer.style.opacity = '1';
        loginContainer.style.transform = 'translateY(0)';
      }, 100);
    });
  </script>
</body>
</html>