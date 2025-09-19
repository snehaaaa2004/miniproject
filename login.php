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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Georgia&display=swap" rel="stylesheet">
  <style>
    /*
     * SerenityConnect - Login Page
     *
     * This stylesheet provides a consistent design for the login page, matching the main website.
     */

    /* --- Global Variables & Base Styles (from the main site) --- */
    :root {
      --primary: #2e4d3d;
      --primary-dark: #1e3b2b;
      --primary-light: #eaf4ed;
      --secondary: #3a5e4f;
      --accent: #f8c537;
      --text: #2a2a2a;
      --text-light: #555555;
      --background: #f8f6f3;
      --white: #ffffff;
      --border: #d9e0d9;
      --shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
      --shadow-hover: 0 12px 28px rgba(0, 0, 0, 0.12);
      --radius: 8px;
      --radius-lg: 12px;
      --transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--background);
      color: var(--text);
      line-height: 1.7;
      overflow-x: hidden;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    
    /* --- Header & Navigation (from the main site) --- */
    header {
  background: var(--primary);
  color: var(--white);
  padding: 1rem 0;
  position: sticky;
  top: 0;
  z-index: 100;
  box-shadow: var(--shadow);
}

.navbar {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

    .logo {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.logo h1 {
  font-family: 'Georgia', serif; /* Kept for a touch of elegance */
  font-size: 1.85rem;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.logo-icon {
  font-size: 2rem;
  color: var(--accent);
}

.nav-links {
  display: flex;
  gap: 2rem;
}

    .nav-links a {
  color: var(--white);
  text-decoration: none;
  font-size: 1rem;
  font-weight: 500;
  transition: var(--transition);
  padding: 0.5rem 0;
  position: relative;
}

.nav-links a::after {
  content: '';
  position: absolute;
  bottom: -5px;
  left: 50%;
  transform: translateX(-50%) scaleX(0);
  width: 100%;
  height: 2px;
  background-color: var(--accent);
  transition: transform 0.3s ease;
}

   .nav-links a:hover::after {
  transform: translateX(-50%) scaleX(1);
}

.nav-links a:hover {
  color: var(--accent);
  transform: translateY(-2px); /* Subtle lift on hover */
}

.mobile-menu-btn {
  display: none;
  background: none;
  border: none;
  color: var(--white);
  font-size: 1.75rem;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.mobile-menu-btn:hover {
  transform: scale(1.1);
}

    /* --- Login Page Specific Styles --- */
    .content-wrapper {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
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
      text-align: center;
      animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
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

    h2 {
      text-align: center;
      color: var(--primary);
      margin: 1rem 0 2rem;
      font-size: 1.8rem;
      font-weight: 600;
      position: relative;
      font-family: 'Georgia', serif;
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
      gap: 1.5rem;
      text-align: left;
    }

    .form-group {
      position: relative;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
      color: var(--text);
      font-weight: 500;
    }

    .form-control {
      width: 100%;
      padding: 0.9rem 1rem 0.9rem 2.8rem;
      border: 1px solid var(--border);
      border-radius: 50px;
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
      left: 1.2rem;
      top: 2.5rem;
      color: var(--text-light);
      font-size: 1rem;
    }

    .btn {
      padding: 1rem;
      background-color: var(--primary);
      color: var(--white);
      font-size: 1rem;
      font-weight: 500;
      border: none;
      border-radius: 50px;
      cursor: pointer;
      transition: var(--transition);
      margin-top: 1rem;
    }

    .btn:hover {
      background-color: var(--primary-dark);
      transform: translateY(-3px);
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
      gap: 1.25rem;
      margin-top: 1rem;
    }

    .social-btn {
      width: 45px;
      height: 45px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--white);
      border: 1px solid var(--border);
      color: var(--text);
      font-size: 1.2rem;
      box-shadow: var(--shadow);
      transition: var(--transition);
    }

    .social-btn:hover {
      background: var(--primary-light);
      color: var(--primary);
      transform: translateY(-2px) scale(1.05);
    }

    /* --- Responsive Design (from the main site) --- */
    @media (max-width: 768px) {
      .nav-links {
        display: none;
        position: absolute;
        top: 70px;
        left: 0;
        right: 0;
        background: var(--primary-dark);
        flex-direction: column;
        padding: 2rem;
        gap: 1.5rem;
        box-shadow: var(--shadow);
      }
      
      .nav-links.active {
        display: flex;
      }
      
      .mobile-menu-btn {
        display: block;
      }
    }

    @media (max-width: 480px) {
      .navbar {
        padding: 0 1rem;
      }
      
      .logo h1 {
        font-size: 1.5rem;
      }
      
      .login-container {
        padding: 1.5rem;
      }
      
      h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>

<body>
  <header>
    <div class="navbar">
      <div class="logo">
        <span class="logo-icon"><i class="fas fa-leaf"></i></span>
        <h1>SerenityConnect</h1>
      </div>
      <button class="mobile-menu-btn" id="menuBtn">
        <i class="fas fa-bars"></i>
      </button>
      <div class="nav-links" id="navLinks">
        <a href="index.html">Home</a>
        <a href="#">Login</a>
        <a href="register.html">Register</a>
        <a href="about.html">About</a>
        <a href="contactus.php">Contact</a>
        <a href="self_help.php">Self help</a>
      </div>
    </div>
  </header>

  <div class="content-wrapper">
    <div class="login-container">
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
        Don't have an account? <a href="register.html">Create one</a>
      </div>
    </div>
  </div>

  <script>
    // Mobile menu toggle
    const menuBtn = document.getElementById('menuBtn');
    const navLinks = document.getElementById('navLinks');
    
    menuBtn.addEventListener('click', () => {
      navLinks.classList.toggle('active');
      menuBtn.innerHTML = navLinks.classList.contains('active') ? 
        '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
    });
  </script>
</body>
</html>