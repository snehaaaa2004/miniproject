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
      <a href="admindash.php" class="<?= ($current_page == 'admindash.php') ? 'active' : '' ?>">
        <i class="fas fa-home"></i>Dashboard
      </a>
      <a href="alltherapists.php" class="<?= ($current_page == 'alltherapists.php') ? 'active' : '' ?>">
        <i class="fas fa-user-md"></i>Therapists
      </a>
      <a href="all_bookings.php" class="<?= ($current_page == 'all_bookings.php') ? 'active' : '' ?>">
        <i class="fas fa-calendar-check"></i>Bookings
      </a>
      
      <!-- Messages Dropdown -->
      <div class="dropdown">
        <a href="#" class="dropdown-toggle">
          <i class="fas fa-envelope"></i>Messages
          <i class="fas fa-chevron-down dropdown-arrow"></i>
        </a>
        <div class="dropdown-menu">
          <a href="view_messages.php" class="<?= ($current_page == 'view_messages.php') ? 'active' : '' ?>">
            <i class="fas fa-users"></i>Customer Messages
          </a>
          <a href="view_therapist_messages.php" class="<?= ($current_page == 'view_therapist_messages.php') ? 'active' : '' ?>">
            <i class="fas fa-user-md"></i>Therapist Messages
          </a>
          <a href="user_messages.php" class="<?= ($current_page == 'user_messages.php') ? 'active' : '' ?>">
            <i class="fas fa-user"></i>User Messages
          </a>
        </div>
      </div>
      
      <a href="/serenity/logout.php" class="btn logout-btn">
        <i class="fas fa-sign-out-alt"></i>Logout
      </a>
    </div>
  </div>
</header>

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
  margin: 0;
  padding: 0;
  width: 100%;
}

.navbar {
  margin: 0;
  padding: 12px 20px;
  width: 100%;
  max-width: 100%;
  background: linear-gradient(135deg,#1e3b2b , #3a5e4d 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  border-bottom: 1px solid rgba(255,255,255,0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  transition: transform 0.3s ease;
}

.logo:hover {
  transform: scale(1.02);
}

.logo-icon {
  font-size: 1.5rem;
  color:#f8c537;
  text-shadow: 0 0 8px rgba(168, 230, 207, 0.3);
}

h1 {
  font-weight: 700;
  font-size: 1.4rem;
  color: white;
  letter-spacing: 0.5px;
  margin: 0;
}

/* --- Navigation Links --- */
.nav-links {
  display: flex;
  gap: 15px;
  margin: 0;
  padding: 0;
  align-items: center;
  list-style: none;
}

.nav-links a {
  color: rgba(255,255,255,0.9);
  text-decoration: none;
  font-size: 0.9rem;
  padding: 8px 12px;
  transition: all 0.3s ease;
  border-radius: 6px;
  display: flex;
  align-items: center;
  gap: 6px;
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
  padding: 8px 16px !important;
}

.btn:hover {
  background: rgba(255,255,255,0.2) !important;
}

/* --- Dropdown Styles --- */
.dropdown {
  position: relative;
}

.dropdown-toggle {
  cursor: pointer;
  position: relative;
  padding-right: 30px !important;
}

.dropdown-arrow {
  font-size: 0.7rem;
  margin-left: 5px;
  transition: transform 0.3s ease;
}

.dropdown:hover .dropdown-arrow {
  transform: rotate(180deg);
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  background: linear-gradient(135deg, #2e4d3d 0%, #3a5e4d 100%);
  min-width: 200px;
  border-radius: 6px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.3s ease;
  z-index: 1000;
  border: 1px solid rgba(255,255,255,0.1);
}

.dropdown:hover .dropdown-menu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-menu a {
  display: block;
  padding: 10px 15px;
  border-bottom: 1px solid rgba(255,255,255,0.05);
  font-size: 0.85rem;
  border-radius: 0;
}

.dropdown-menu a:last-child {
  border-bottom: none;
}

.dropdown-menu a:hover {
  background: rgba(255,255,255,0.1);
}

/* --- Mobile Menu Button --- */
.mobile-menu-btn {
  display: none;
  background: none;
  border: none;
  color: white;
  font-size: 1.3rem;
  cursor: pointer;
  padding: 6px;
  border-radius: 4px;
  transition: all 0.3s ease;
}

.mobile-menu-btn:hover {
  background: rgba(255,255,255,0.1);
}

/* --- Mobile Styles --- */
@media (max-width: 1100px) {
  .nav-links {
    gap: 10px;
  }
  
  .nav-links a {
    font-size: 0.85rem;
    padding: 6px 10px;
  }
  
  h1 {
    font-size: 1.3rem;
  }
}

@media (max-width: 992px) {
  .navbar {
    padding: 12px 15px;
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
    z-index: 1000;
  }
  
  .nav-links.active {
    display: flex;
  }
  
  .nav-links a {
    padding: 12px 20px;
    border-radius: 0;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    font-size: 0.95rem;
    width: 100%;
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
  
  /* Mobile dropdown styles */
  .dropdown {
    width: 100%;
  }
  
  .dropdown-toggle {
    width: 100%;
    justify-content: space-between;
  }
  
  .dropdown-menu {
    position: static;
    opacity: 1;
    visibility: visible;
    transform: none;
    box-shadow: none;
    background: rgba(255,255,255,0.05);
    border: none;
    border-radius: 0;
    display: none;
  }
  
  .dropdown.active .dropdown-menu {
    display: block;
  }
  
  .dropdown-menu a {
    padding-left: 35px;
    font-size: 0.9rem;
  }
}

@media (max-width: 768px) {
  .navbar {
    padding: 10px 12px;
  }
  
  h1 {
    font-size: 1.2rem;
  }
  
  .logo-icon {
    font-size: 1.3rem;
  }
  
  .nav-links a {
    padding: 10px 15px;
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
    
    // Mobile dropdown functionality
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
      toggle.addEventListener('click', function(e) {
        if (window.innerWidth <= 992) {
          e.preventDefault();
          const dropdown = this.parentElement;
          dropdown.classList.toggle('active');
        }
      });
    });
    
    // Close mobile menu when clicking on a link (non-dropdown)
    const navLinksElements = navLinks.querySelectorAll('a:not(.dropdown-toggle)');
    navLinksElements.forEach(link => {
      link.addEventListener('click', function() {
        if (window.innerWidth <= 992) {
          navLinks.classList.remove('active');
          const icon = menuBtn.querySelector('i');
          icon.className = 'fas fa-bars';
          
          // Close all dropdowns
          document.querySelectorAll('.dropdown').forEach(dropdown => {
            dropdown.classList.remove('active');
          });
        }
      });
    });
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
      if (window.innerWidth <= 992) {
        if (!navLinks.contains(event.target) && !menuBtn.contains(event.target)) {
          navLinks.classList.remove('active');
          const icon = menuBtn.querySelector('i');
          icon.className = 'fas fa-bars';
          
          // Close all dropdowns
          document.querySelectorAll('.dropdown').forEach(dropdown => {
            dropdown.classList.remove('active');
          });
        }
      }
    });
  }
});
</script>
</body>
</html>