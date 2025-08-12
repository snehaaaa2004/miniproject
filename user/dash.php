<?php

    session_start();


// Prevent caching to disable back button access

include('../auth_check.php');



$name = $_SESSION['name'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  


  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
  <title>User Dashboard - SerenityConnect</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #2e4d3d;
      --primary-dark: #1e3b2b;
      --primary-light: #eaf4ed;
      --secondary: #3a5e4f;
      --accent: #f0ad4e;
      --text: #333333;
      --text-light: #666666;
      --background: #f8f6f3;
      --white: #ffffff;
      --border: #e0e0e0;
      --shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      --shadow-hover: 0 10px 15px rgba(0, 0, 0, 0.1);
      --radius: 8px;
      --radius-lg: 12px;
      --transition: all 0.25s cubic-bezier(0.645, 0.045, 0.355, 1);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--background);
      color: var(--text);
      line-height: 1.6;
      padding: 0;
    }

    header {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: var(--white);
      padding: 2rem 1rem;
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
    }

    header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
      transform: rotate(30deg);
    }

    header h2 {
      font-weight: 600;
      font-size: 1.8rem;
      position: relative;
    }

    .container {
      max-width: 1200px;
      margin: -2rem auto 2rem;
      padding: 0 1rem;
    }

    .main-content {
      background: var(--white);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      padding: 2.5rem;
      position: relative;
      z-index: 1;
    }

    .welcome {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 2.5rem;
      padding-bottom: 1.5rem;
      border-bottom: 1px solid var(--border);
    }

    .welcome-text h1 {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--text);
      margin-bottom: 0.5rem;
    }

    .welcome-text p {
      color: var(--text-light);
      font-size: 0.95rem;
    }

    .user-avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary);
      font-size: 1.5rem;
      font-weight: 600;
    }

    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
      margin: 2rem 0;
    }

    .card {
      background: var(--white);
      border-radius: var(--radius);
      padding: 1.75rem;
      box-shadow: var(--shadow);
      border: 1px solid var(--border);
      transition: var(--transition);
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-hover);
      border-color: var(--primary-light);
    }

    .card-icon {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1.25rem;
      font-size: 1.75rem;
      color: var(--primary);
    }

    .card h3 {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 0.75rem;
      color: var(--text);
    }

    .card p {
      color: var(--text-light);
      font-size: 0.9rem;
      margin-bottom: 1.25rem;
    }

    .card a {
      display: inline-block;
      padding: 0.6rem 1.25rem;
      background: var(--primary);
      color: white;
      border-radius: var(--radius);
      text-decoration: none;
      font-weight: 500;
      font-size: 0.9rem;
      transition: var(--transition);
      margin-top: auto;
    }

    .card a:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
    }

    .dashboard-summary {
      background: var(--primary-light);
      padding: 1.5rem;
      border-radius: var(--radius);
      margin: 2.5rem 0;
      border-left: 4px solid var(--primary);
    }

    .dashboard-summary h3 {
      font-size: 1.2rem;
      margin-bottom: 1rem;
      color: var(--primary-dark);
    }

    .dashboard-summary p {
      color: var(--text);
    }

    .logout {
      text-align: center;
      margin-top: 2.5rem;
    }

    .logout a {
      display: inline-flex;
      align-items: center;
      padding: 0.75rem 1.5rem;
      background: transparent;
      color: var(--primary);
      border-radius: var(--radius);
      text-decoration: none;
      font-weight: 500;
      transition: var(--transition);
      border: 1px solid var(--primary);
    }

    .logout a:hover {
      background: var(--primary);
      color: white;
    }

    .logout a::before {
      content: "🚪";
      margin-right: 0.5rem;
    }

    footer {
      text-align: center;
      padding: 1.5rem;
      color: var(--text-light);
      font-size: 0.85rem;
      border-top: 1px solid var(--border);
      margin-top: 3rem;
    }

    @media (max-width: 768px) {
      .main-content {
        padding: 1.5rem;
      }
      
      .welcome {
        flex-direction: column;
        text-align: center;
      }
      
      .user-avatar {
        margin-top: 1rem;
      }
    }
  </style>
</head>


<body>

  <?php include 'navbar.php'; ?>
  


  <header>
    <div class="container">
      <h2>User Dashboard</h2>
    </div>
  </header>

  <div class="container">
    <div class="main-content">
      <div class="welcome">
        <div class="welcome-text">
          <h1>Welcome back, <?php echo htmlspecialchars($name); ?></h1>
          <p>Here's what's happening with your mental wellness journey today</p>
        </div>
        <div class="user-avatar">
          <?php echo strtoupper(substr($name, 0, 1)); ?>
        </div>
      </div>

      <div class="dashboard-grid">
        <div class="card">
          <div class="card-icon">🔍</div>
          <h3>Find Therapists</h3>
          <p>Browse our network of licensed professionals</p>
          <a href="user.php">Get Started</a>
        </div>
        <div class="card">
          <div class="card-icon">📅</div>
          <h3>Your Appointments</h3>
          <p>View and manage your upcoming sessions</p>
          <a href="view_bookings.php">View Schedule</a>
        </div>
        <div class="card">
          <div class="card-icon">📝</div>
          <h3>Wellness Resources</h3>
          <p>Access helpful tools and articles</p>
          <a href="resources.php">Explore</a>
        </div>
        <div class="card">
          <div class="card-icon">⚙️</div>
          <h3>Account Settings</h3>
          <p>Update your profile and preferences</p>
          <a href="updateprofile.php">Manage</a>
        </div>
      </div>

      <div class="dashboard-summary">
        <h3>Your Wellness Journey</h3>
        <p>
          SerenityConnect provides personalized support for your mental health needs. 
          Use this dashboard to connect with therapists, manage appointments, and 
          access valuable resources. We're committed to helping you achieve 
          emotional balance and personal growth.
        </p>
      </div>

      <div class="logout">
        <a href="../logout.php">Logout</a>
      </div>
    </div>
  </div>

  <footer>
    &copy; 2025 SerenityConnect. All rights reserved.
  </footer>
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