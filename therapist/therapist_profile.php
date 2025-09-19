<?php
session_start();
include('../auth_check.php');
include '../connect.php';
include('therapistnav.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Therapist') {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM therapists WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$therapist = $result->fetch_assoc();

if (!$therapist) {
    echo "<p>No profile found. Please complete your profile first.</p>";
    exit;
}

// Get image path
$image = !empty($therapist['image']) ? "../" . $therapist['image'] : "../images/default-user.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Therapist Profile - SerenityConnect</title>
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
      font-family: 'Inter', sans-serif;
      background-color: var(--background);
      color: var(--text);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      padding: 0;
    }

    

    .navbar {
      max-width: 1200px;
      
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

    .profile-container {
      max-width: 800px;
      margin: 2rem auto;
      padding: 0 1rem;
      flex: 1;
    }

    .profile-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      padding: 2.5rem;
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
      max-width: 800px;
      margin: 0 auto;
    }

    .profile-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 6px;
      background: linear-gradient(to right, var(--primary), var(--accent));
    }

    .profile-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .profile-header h2 {
      color: var(--primary);
      font-size: 1.8rem;
      margin-bottom: 1rem;
      position: relative;
      padding-bottom: 0.75rem;
    }

    .profile-header h2::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 3px;
      background-color: var(--accent);
      border-radius: 3px;
    }

    .profile-photo {
      width: 160px;
      height: 160px;
      object-fit: cover;
      border-radius: 50%;
      margin: 0 auto 1.5rem;
      border: 4px solid var(--white);
      box-shadow: var(--shadow);
      display: block;
    }

    .profile-details {
      display: flex;
      flex-wrap: wrap;
      gap: 1.5rem;
      margin-top: 1.5rem;
    }

    .detail-group {
      background-color: var(--primary-light);
      border-radius: var(--radius);
      padding: 1.25rem;
      border-left: 4px solid var(--primary);
      flex: 1 1 300px;
      min-width: 260px;
      box-sizing: border-box;
      margin-bottom: 0;
    }

    .detail-group h3 {
      color: var(--primary);
      margin-bottom: 0.75rem;
      font-size: 1.1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .detail-group h3 i {
      color: var(--primary);
    }

    .detail-group p {
      color: var(--text);
      line-height: 1.6;
    }

    .btn-container {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin-top: 2.5rem;
      flex-wrap: wrap;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.8rem 1.5rem;
      background-color: var(--primary);
      color: green;
      border-radius: var(--radius);
      text-decoration: none;
      font-weight: 500;
      transition: var(--transition);
      border: none;
      cursor: pointer;
      font-size: 1rem;
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
      
      .profile-card {
        padding: 1.5rem;
      }
    }

    @media (max-width: 900px) {
      .profile-details {
        flex-direction: column;
        gap: 1rem;
      }
      .profile-card {
        padding: 1.5rem;
      }
    }

    @media (max-width: 600px) {
      .profile-card {
        padding: 0.5rem;
      }
      .profile-header h2 {
        font-size: 1.3rem;
      }
      .profile-photo {
        width: 110px;
        height: 110px;
      }
      .detail-group {
        padding: 0.75rem;
        min-width: 0;
      }
    }

    @media (max-width: 480px) {
      .logo h1 {
        font-size: 1.3rem;
      }
      
      .profile-header h2 {
        font-size: 1.5rem;
      }
      
      .btn-container {
        flex-direction: column;
        gap: 0.75rem;
      }
      
      .btn {
        width: 100%;
        color:var(--white);
        justify-content: center;
      }
      
    }
  </style>
</head>
<body>

  

  <div class="profile-container">
    <div class="profile-card">
      <div class="profile-header">
        <h2>Therapist Profile</h2>
        <img src="<?= htmlspecialchars($image) ?>" alt="Profile Photo" class="profile-photo">
      </div>

      <div class="profile-details">
        <div class="detail-group">
          <h3><i class="fas fa-user-md"></i> Professional Information</h3>
          <p><strong>Specialization:</strong> <?= htmlspecialchars($therapist['specialization']) ?></p>
          <p><strong>Experience:</strong> <?= htmlspecialchars($therapist['experience']) ?> years</p>
        </div>

        <div class="detail-group">
          <h3><i class="fas fa-language"></i> Languages</h3>
          <p><?= htmlspecialchars($therapist['language']) ?></p>
        </div>

        <div class="detail-group">
          <h3><i class="fas fa-user"></i> Personal Details</h3>
          <p><strong>Gender:</strong> <?= htmlspecialchars($therapist['gender']) ?></p>
        </div>

        <div class="detail-group">
          <h3><i class="fas fa-clock"></i> Availability</h3>
          <p><?= htmlspecialchars($therapist['availability']) ?></p>
        </div>

        <div class="detail-group">
          <h3><i class="fas fa-video"></i> Consultation Modes</h3>
          <p><?= htmlspecialchars($therapist['mode']) ?></p>
        </div>
      
      </div>
      <div class="detail-group">
        <h3><i class="fas fa-dollar-sign"></i> Consultation Fees</h3>
        <p>$<?= htmlspecialchars($therapist['fees'])?></p>
        
      </div>
      <div class="detail-group">
        <h3><i class="fas fa-info-circle"></i> Bio</h3>
        <p><?= htmlspecialchars($therapist['bio']) ?></p>
      </div>

      <div class="btn-container">
        <a href="edit_therapistprofile.php" class="btn btn-outline">
          <i class="fas fa-edit"></i> Edit Profile
        </a>
        <a href="therapihome.php" class="btn btn-outline">
          <i class="fas fa-arrow-left"></i> Back to Dashboard
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
      const details = document.querySelectorAll('.detail-group');
      details.forEach((detail, index) => {
        detail.style.opacity = '0';
        detail.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
          detail.style.transition = `opacity 0.4s ease ${index * 0.1}s, transform 0.4s ease ${index * 0.1}s`;
          detail.style.opacity = '1';
          detail.style.transform = 'translateY(0)';
        }, 100);
      });
    });
  </script>
</body>
</html>