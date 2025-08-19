<?php
$current_page = basename($_SERVER['PHP_SELF']);
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
      <a href="therapihome.php" class="<?= ($current_page == 'therapihome.php') ? 'active' : '' ?>">Dashboard</a>
      <a href="appointment.php" class="<?= ($current_page == 'appointment.php') ? 'active' : '' ?>">Appointments</a>
      <a href="therapist_profile.php" class="<?= ($current_page == 'therapist_profile.php') ? 'active' : '' ?>">Profile</a>
      <a href="reviews.php" class="<?= ($current_page == 'reviews.php') ? 'active' : '' ?>">Reviews</a>
      <a href="/mini%20proj/serenity/logout.php" class="btn">Logout</a>
    </div>
  </div>
</header>

<style>
.navbar {
  background: linear-gradient(135deg, #2e4d3d 0%, #3a5e4d 100%);
  padding: 0;
  color: white;
  position: sticky;
  top: 0;
  z-index: 999;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  border-bottom: 1px solid rgba(255,255,255,0.1);
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
  color: #a8e6cf;
  text-shadow: 0 0 8px rgba(168, 230, 207, 0.3);
}

h1 {
  font-weight: 700;
  font-size: 1.5rem;
  color: white;
  letter-spacing: 0.5px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.nav-links {
  list-style: none;
  display: flex;
  gap: 25px;
  margin: 0;
  padding: 0;
  align-items: center;
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
  background: rgba(255,255,255,0.08);
  padding: 10px 20px !important;
}

.btn:hover {
  background: rgba(255,255,255,0.2) !important;
}

.mobile-menu-btn {
  display: none;
  background: transparent;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
}

@media (max-width: 992px) {
  .nav-links {
    display: none;
    flex-direction: column;
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: #2e4d3d;
    padding: 15px 0;
    border-top: 1px solid rgba(255,255,255,0.1);
  }
  
  .nav-links.active {
    display: flex;
  }
  
  .nav-links a {
    padding: 10px 12px;
    font-size: 0.9rem;
  }
  
  .mobile-menu-btn {
    display: block;
  }
}
</style>

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