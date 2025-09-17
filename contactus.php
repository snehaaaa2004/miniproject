<?php
include 'connect.php'; // Ensure this connects to your DB

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $message = trim($_POST['message']);

  if ($name && $email && $message) {
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
      $success = "Thank you! Your message has been sent. We'll respond within 24-48 hours.";
    } else {
      $error = "Something went wrong. Please try again later.";
    }
  } else {
    $error = "All fields are required. Please complete the form.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us - SerenityConnect</title>
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
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', 'Georgia', serif;
      background-color: var(--background);
      color: var(--text);
      line-height: 1.7;
    }

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
      font-size: 1.75rem;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .logo-icon {
      font-size: 1.8rem;
      color: var(--accent);
    }

    .nav-links {
      display: flex;
      gap: 1.5rem;
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
      bottom: 0;
      left: 0;
      width: 0;
      height: 2px;
      background-color: var(--accent);
      transition: var(--transition);
    }

    .nav-links a:hover::after {
      width: 100%;
    }

    .nav-links a:hover {
      color: var(--accent);
    }

    .mobile-menu-btn {
      display: none;
      background: none;
      border: none;
      color: var(--white);
      font-size: 1.5rem;
      cursor: pointer;
    }

    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin: 2rem 0 0 2rem;
      padding: 0.75rem 1.5rem;
      background: var(--primary);
      color: var(--white);
      text-decoration: none;
      border-radius: var(--radius);
      font-weight: 500;
      transition: var(--transition);
    }

    .back-btn:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: var(--shadow-hover);
    }

    .contact-container {
      max-width: 1200px;
      margin: 3rem auto;
      padding: 0 2rem;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 3rem;
    }

    .contact-info-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      padding: 2.5rem;
      box-shadow: var(--shadow);
      height: fit-content;
    }

    .contact-info-card h2 {
      font-size: 1.8rem;
      color: var(--primary);
      margin-bottom: 1.5rem;
      position: relative;
    }

    .contact-info-card h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 60px;
      height: 3px;
      background-color: var(--accent);
    }

    .contact-method {
      display: flex;
      align-items: flex-start;
      gap: 1.5rem;
      margin-bottom: 1.5rem;
      padding-bottom: 1.5rem;
      border-bottom: 1px solid var(--border);
    }

    .contact-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary);
      font-size: 1.2rem;
      flex-shrink: 0;
    }

    .contact-details h4 {
      font-size: 1.1rem;
      color: var(--primary);
      margin-bottom: 0.5rem;
    }

    .contact-details p, .contact-details a {
      color: var(--text-light);
      text-decoration: none;
      transition: var(--transition);
    }

    .contact-details a:hover {
      color: var(--primary);
    }

    .contact-form-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      padding: 2.5rem;
      box-shadow: var(--shadow);
    }

    .contact-form-card h2 {
      font-size: 1.8rem;
      color: var(--primary);
      margin-bottom: 1.5rem;
      position: relative;
    }

    .contact-form-card h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 0;
      width: 60px;
      height: 3px;
      background-color: var(--accent);
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--primary);
    }

    .form-control {
      width: 100%;
      padding: 0.9rem 1rem;
      font-size: 1rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background-color: #fefefe;
      transition: var(--transition);
      font-family: 'Inter', sans-serif;
    }

    .form-control:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 3px rgba(46, 77, 61, 0.1);
    }

    textarea.form-control {
      min-height: 150px;
      resize: vertical;
    }

    .btn {
      display: inline-block;
      padding: 0.9rem 2rem;
      background: var(--primary);
      color: var(--white);
      text-decoration: none;
      border-radius: var(--radius);
      font-size: 1rem;
      font-weight: 500;
      transition: var(--transition);
      border: none;
      cursor: pointer;
      font-family: 'Inter', sans-serif;
    }

    .btn:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: var(--shadow-hover);
    }

    .btn-block {
      display: block;
      width: 100%;
    }

    .status-msg {
      padding: 1rem;
      margin-bottom: 1.5rem;
      border-radius: var(--radius);
      font-size: 0.95rem;
      text-align: center;
    }

    .success {
      background-color: #edf7ed;
      color: #1e4620;
      border: 1px solid #c5e1c5;
    }

    .error {
      background-color: #fde8e8;
      color: #b00020;
      border: 1px solid #f5c6cb;
    }

    footer {
      background-color: var(--primary-dark);
      color: var(--white);
      padding: 4rem 2rem 2rem;
      margin-top: 5rem;
    }

    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 3rem;
    }

    .footer-column h4 {
      font-size: 1.25rem;
      margin-bottom: 1.5rem;
      position: relative;
      padding-bottom: 0.75rem;
    }

    .footer-column h4::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 40px;
      height: 2px;
      background-color: var(--accent);
    }

    .footer-column p, .footer-column a {
      color: rgba(255, 255, 255, 0.8);
      margin-bottom: 0.8rem;
      display: block;
      text-decoration: none;
      transition: var(--transition);
    }

    .footer-column a:hover {
      color: var(--accent);
      padding-left: 5px;
    }

    .social-links {
      display: flex;
      gap: 1rem;
      margin-top: 1.5rem;
    }

    .social-links a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: rgba(255, 255, 255, 0.1);
      color: var(--white);
      transition: var(--transition);
    }

    .social-links a:hover {
      background-color: var(--accent);
      color: var(--primary);
      transform: translateY(-3px);
    }

    .copyright {
      text-align: center;
      margin-top: 3rem;
      padding-top: 2rem;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.9rem;
    }

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
      
      .contact-container {
        grid-template-columns: 1fr;
      }
      
      .back-btn {
        margin: 1rem 0 0 1rem;
      }
    }

    @media (max-width: 480px) {
      .navbar {
        padding: 0 1rem;
      }
      
      .logo h1 {
        font-size: 1.5rem;
      }
      
      .contact-info-card,
      .contact-form-card {
        padding: 1.5rem;
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
        <a href="login.php">Login</a>
        <a href="register.html">Register</a>
        <a href="about.html">About</a>
        <a href="contactus.php">Contact</a>
        <a href="self_help.php">Self help</a>
      </div>
    </div>
  </header>

  <a href="javascript:history.back()" class="back-btn">
    <i class="fas fa-arrow-left"></i> Back
  </a>

  <div class="contact-container">
    <div class="contact-info-card">
      <h2>Get In Touch</h2>
      
      <div class="contact-method">
        <div class="contact-icon">
          <i class="fas fa-envelope"></i>
        </div>
        <div class="contact-details">
          <h4>Email Us</h4>
          <a href="mailto:support@serenityconnect.com">support@serenityconnect.com</a>
          <p>Typically responds within 24 hours</p>
        </div>
      </div>
      
      <div class="contact-method">
        <div class="contact-icon">
          <i class="fas fa-phone-alt"></i>
        </div>
        <div class="contact-details">
          <h4>Call Us</h4>
          <a href="tel:+919876543210">+91 98765 43210</a>
          <p>Monday to Friday, 9AM to 6PM</p>
        </div>
      </div>
      
      <div class="contact-method">
        <div class="contact-icon">
          <i class="fas fa-map-marker-alt"></i>
        </div>
        <div class="contact-details">
          <h4>Our Office</h4>
          <p>123 Wellness Avenue<br>Bangalore, Karnataka 560001</p>
          <p>Available online across India</p>
        </div>
      </div>
      
    </div>
    
    <div class="contact-form-card">
      <h2>Send Us a Message</h2>
      
      <?php if ($success): ?>
        <div class="status-msg success"><?php echo $success; ?></div>
      <?php elseif ($error): ?>
        <div class="status-msg error"><?php echo $error; ?></div>
      <?php endif; ?>
      
      <form method="POST">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" required>
        </div>
        
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" required>
        </div>
        
        <div class="form-group">
          <label for="message">Your Message</label>
          <textarea id="message" name="message" class="form-control" placeholder="How can we help you?" required></textarea>
        </div>
        
        <button type="submit" class="btn btn-block">Send Message</button>
      </form>
    </div>
  </div>

  <footer>
    <div class="footer-content">
      <div class="footer-column">
        <h4>SerenityConnect</h4>
        <p>Providing accessible mental health care through innovative technology and compassionate professionals.</p>
        <div class="social-links">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
      
      <div class="footer-column">
        <h4>Quick Links</h4>
        <a href="index.html">Home</a>
        <a href="about.html">About Us</a>
        <a href="self_help.php">Resources</a>
        <a href="contactus.html">Contact</a>
      </div>
      
      <div class="footer-column">
        <h4>Services</h4>
        <a href="#">Individual Therapy</a>
        <a href="#">Couples Counseling</a>
        <a href="#">Family Therapy</a>
        <a href="#">Teen Support</a>
      </div>
      
      <div class="footer-column">
        <h4>Contact Us</h4>
        <p><i class="fas fa-map-marker-alt"></i> 123 Wellness Avenue, Bangalore</p>
        <p><i class="fas fa-phone"></i> +91 98765 43210</p>
        <p><i class="fas fa-envelope"></i> help@serenityconnect.com</p>
      </div>
    </div>
    
    <div class="copyright">
      &copy; 2025 SerenityConnect. All rights reserved.
    </div>
  </footer>

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