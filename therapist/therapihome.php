<?php

    session_start();


// Prevent caching to disable back button access

include('../auth_check.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
  <title>Therapist Dashboard - SerenityConnect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    header {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: var(--white);
      padding: 1rem;
      box-shadow: var(--shadow);
    }

    .navbar {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 1rem;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .logo h1 {
      font-size: 1.5rem;
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

    .dashboard-container {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1rem;
      flex: 1;
    }

    .welcome-banner {
      background: linear-gradient(135deg, var(--primary-light), var(--white));
      border-radius: var(--radius-lg);
      padding: 2.5rem;
      margin-bottom: 2rem;
      text-align: center;
      box-shadow: var(--shadow);
      border: 1px solid var(--border);
      position: relative;
      overflow: hidden;
    }

    .welcome-banner::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 6px;
      background: linear-gradient(to right, var(--primary), var(--accent));
    }

    .welcome-banner h2 {
      color: var(--primary);
      font-size: 1.8rem;
      margin-bottom: 1rem;
      position: relative;
    }

    .welcome-banner h2::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 3px;
      background-color: var(--accent);
    }

    .welcome-banner p {
      color: var(--text-light);
      font-size: 1.1rem;
      max-width: 600px;
      margin: 0 auto 1.5rem;
    }

    .action-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .action-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      padding: 2rem;
      box-shadow: var(--shadow);
      text-align: center;
      transition: var(--transition);
      border: 1px solid var(--border);
    }

    .action-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-hover);
    }

    .action-card i {
      font-size: 2.5rem;
      color: var(--primary);
      margin-bottom: 1rem;
    }

    .action-card h3 {
      color: var(--primary);
      margin-bottom: 1rem;
    }

    .action-card p {
      color: var(--text-light);
      margin-bottom: 1.5rem;
      font-size: 0.95rem;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.8rem 1.5rem;
      background-color: var(--primary);
      color: var(--white);
      border-radius: var(--radius);
      text-decoration: none;
      font-weight: 500;
      transition: var(--transition);
    }

    .btn:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: var(--shadow-hover);
    }

    .btn-outline {
      background: transparent;
      border: 1px solid var(--primary);
      color: var(--primary);
    }

    .btn-outline:hover {
      background: var(--primary);
      color: var(--white);
    }

    .notification-badge {
      position: absolute;
      top: -8px;
      right: -8px;
      background-color: #e74c3c;
      color: white;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: bold;
    }

    .card-icon-container {
      position: relative;
      display: inline-block;
    }

    footer {
      background-color: var(--primary-dark);
      color: var(--white);
      text-align: center;
      padding: 1.5rem;
      margin-top: 3rem;
    }

    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
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
      
      .welcome-banner {
        padding: 1.5rem;
      }
    }

    @media (max-width: 480px) {
      .logo h1 {
        font-size: 1.3rem;
      }
      
      .welcome-banner h2 {
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
        <a href="therapihome.php">Dashboard</a>
        <a href="appointment.php">Appointments</a>
        <a href="therapist_profile.php">Profile</a>
        
        <a href="customer_review.php" class="btn">View All Customer Reviews</a>

        <a href="../logout.php">Logout</a>
      </div>
    </div>
  </header>

  <div class="dashboard-container">
    <div class="welcome-banner">
      <h2>Welcome to Your Therapist Portal</h2>
      <p>Manage your practice, view appointments, and access professional resources all in one place.</p>
    </div>

    <div class="action-cards">
      <div class="action-card">
        <i class="fas fa-user-plus"></i>
        <h3>Complete Your Profile</h3>
        <p>Set up your professional profile to start receiving client referrals and bookings.</p>
        <a href="therapist.php" class="btn">
          <i class="fas fa-edit"></i> Set up Profile
        </a>
      </div>

      <div class="action-card">
        <div class="card-icon-container">
          <i class="fas fa-calendar-plus"></i>
          <span class="notification-badge">3</span>
        </div>
        <h3>New Bookings</h3>
        <p>Review and confirm new appointment requests from clients.</p>
        <a href="appointment.php" class="btn">
          <i class="fas fa-eye"></i> View Requests
        </a>
      </div>

      <div class="action-card">
        <i class="fas fa-calendar-alt"></i>
        <h3>Update profile</h3>
        <p>update your profile</p>
        <a href="edit_therapistprofile.php" class="btn">
          <i class="fas fa-list"></i> View Bookings
        </a>
      </div>

      <div class="action-card">
        <i class="fas fa-chart-line"></i>
        <h3>Client Insights</h3>
        <p>Review your client progress and session history.</p>
        <a href="customer_review.php" class="btn btn-outline">
          <i class="fas fa-chart-bar"></i> View Analytics
        </a>
      </div>
    </div>
  </div>

  <footer>
    <div class="footer-content">
      <p>&copy; 2025 SerenityConnect. All rights reserved.</p>
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

    // Simple animation on load
    document.addEventListener('DOMContentLoaded', () => {
      const cards = document.querySelectorAll('.action-card');
      cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
          card.style.transition = `opacity 0.4s ease ${index * 0.1}s, transform 0.4s ease ${index * 0.1}s`;
          card.style.opacity = '1';
          card.style.transform = 'translateY(0)';
        }, 100);
      });
    });
  </script>
  <script>
// Prevent page from being cached
window.onpageshow = function(event) {
    if (event.persisted) {
        window.location.reload();
    }
};

// Clear browser history
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
</script>
</body>
</html>