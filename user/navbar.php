<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SerenityConnect - Hamburger Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<?php 
// Simulate the current page for demo purposes
$current_page = 'therapihome.php'; 
?>

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
      <a href="dash.php" class="<?= ($current_page == 'dash.php') ? 'active' : '' ?>">
        <i class="fas fa-home"></i>Dashboard
      </a>
      <a href="user.php" class="<?= ($current_page == 'user.php') ? 'active' : '' ?>">
        <i class="fas fa-calendar-alt"></i>Filter Therapists
      </a>
      <a href="view_bookings.php" class="<?= ($current_page == 'view_bookings.php') ? 'active' : '' ?>">
        <i class="fas fa-user"></i>Bookings
      </a>
      <a href="updateprofile.php" class="<?= ($current_page == 'updateprofile.php') ? 'active' : '' ?>">
        <i class="fas fa-star"></i>Profile
      </a>
      <a href="contact_admin.php" class="<?= ($current_page == 'contact_admin.php') ? 'active' : '' ?>">
        <i class="fas fa-star"></i>Contact Support
      </a>
      <a href="/serenity/logout.php" class="btn logout-btn">
        <i class="fas fa-sign-out-alt"></i>Logout
      </a>
    </div>
  </div>
</header>

<!-- Demo content to show the sticky navbar -->

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* --- Header Styles --- */
header {
  position: sticky;
  top: 0;
  z-index: 1000;
}

.navbar {
  background: linear-gradient(135deg,#1e3b2b , #3a5e4d 100%);
  padding: 15px 30px;
  color: white;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  border-bottom: 1px solid rgba(255,255,255,0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
}

.logo {
  display: flex;
  align-items: center;
  gap: 12px;
  transition: transform 0.3s ease;
}

.logo:hover {
  transform: scale(1.02);
}

.logo-icon {
  font-size: 1.6rem;
  color:#f8c537;
  text-shadow: 0 0 8px rgba(168, 230, 207, 0.3);
}

h1 {
  font-weight: 700;
  font-size: 1.5rem;
  color: white;
  letter-spacing: 0.5px;
  margin: 0;
}

/* --- Navigation Links --- */
.nav-links {
  display: flex;
  gap: 25px;
  margin: 0;
  padding: 0;
  align-items: center;
  list-style: none;
}

.nav-links a {
  color: rgba(255,255,255,0.9);
  text-decoration: none;
  font-size: 1rem;
  padding: 10px 15px;
  transition: all 0.3s ease;
  border-radius: 6px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 500;
  white-space: nowrap;
}

.nav-links a:hover {
  color: #fff;
  background: rgba(255,255,255,0.1);
}

.nav-links a.active {
  color: #fff;
  background: rgba(255,255,255,0.15);
  font-weight: 600;
}

.btn {
  background: rgba(255,255,255,0.08) !important;
  padding: 10px 20px !important;
}

.btn:hover {
  background: rgba(255,255,255,0.2) !important;
}

/* --- Mobile Menu Button --- */
.mobile-menu-btn {
  display: none;
  background: none;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 8px;
  border-radius: 4px;
  transition: all 0.3s ease;
}

.mobile-menu-btn:hover {
  background: rgba(255,255,255,0.1);
}

/* --- Mobile Styles --- */
@media (max-width: 992px) {
  .navbar {
    padding: 15px 20px;
  }
  
  .nav-links {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: linear-gradient(135deg, #2e4d3d 0%, #3a5e4d 100%);
    flex-direction: column;
    gap: 0;
    padding: 0;
    border-top: 1px solid rgba(255,255,255,0.1);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  
  .nav-links.active {
    display: flex;
  }
  
  .nav-links a {
    padding: 15px 20px;
    border-radius: 0;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    font-size: 0.95rem;
  }
  
  .nav-links a:last-child {
    border-bottom: none;
  }
  
  .mobile-menu-btn {
    display: block;
  }
  
  .logout-btn {
    background: rgba(255,255,255,0.1) !important;
    margin-top: 10px;
    margin-bottom: 10px;
  }
}

@media (max-width: 768px) {
  .navbar {
    padding: 12px 15px;
  }
  
  h1 {
    font-size: 1.3rem;
  }
  
  .logo-icon {
    font-size: 1.4rem;
  }
  
  .nav-links a {
    padding: 12px 15px;
    font-size: 0.9rem;
  }
}

/* --- Animation for smooth transitions --- */
.nav-links {
  transition: all 0.3s ease-in-out;
}

@media (max-width: 992px) {
  .nav-links {
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
  }
  
  .nav-links.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
  }
}
</style>

<script>
// Mobile menu toggle functionality
document.addEventListener('DOMContentLoaded', function() {
  const menuBtn = document.getElementById('menuBtn');
  const navLinks = document.getElementById('navLinks');
  
  if (menuBtn && navLinks) {
    menuBtn.addEventListener('click', function() {
      navLinks.classList.toggle('active');
      
      // Change icon based on menu state
      const icon = menuBtn.querySelector('i');
      if (navLinks.classList.contains('active')) {
        icon.className = 'fas fa-times';
      } else {
        icon.className = 'fas fa-bars';
      }
    });
    
    // Close mobile menu when clicking on a link
    const navLinksElements = navLinks.querySelectorAll('a');
    navLinksElements.forEach(link => {
      link.addEventListener('click', function() {
        navLinks.classList.remove('active');
        const icon = menuBtn.querySelector('i');
        icon.className = 'fas fa-bars';
      });
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
      if (!navLinks.contains(event.target) && !menuBtn.contains(event.target)) {
        navLinks.classList.remove('active');
        const icon = menuBtn.querySelector('i');
        icon.className = 'fas fa-bars';
      }
    });
  }
</script>