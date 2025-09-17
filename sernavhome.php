<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar">
  <div class="nav-container">
    <!-- Logo -->
    <div class="nav-logo">
      <img src="leaf-icon.png" alt="Logo" class="logo-icon"> 
      SerenityConnect
    </div>

    <!-- Links -->
    <ul class="nav-links">
      <li><a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Home</a></li>
      <li><a href="login.php" class="<?= ($current_page == 'login.php') ? 'active' : '' ?>">Login</a></li>
      <li><a href="register.php" class="<?= ($current_page == 'register.php') ? 'active' : '' ?>">Register</a></li>
      <li><a href="about.php" class="<?= ($current_page == 'about.php') ? 'active' : '' ?>">About</a></li>
      <li><a href="contact.php" class="<?= ($current_page == 'contact.php') ? 'active' : '' ?>">Contact</a></li>
      <li><a href="selfhelp.php" class="<?= ($current_page == 'selfhelp.php') ? 'active' : '' ?>">Self help</a></li>
    </ul>
  </div>
</nav>

<style>
/* Navbar Styling */
.navbar {
  background-color: #2f4f40; /* Dark Green */
  padding: 15px 0;
  color: white;
  position: sticky;
  top: 0;
  z-index: 999;
}

/* Container */
.nav-container {
  width: 90%;
  max-width: 1200px;
  margin: auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* Logo */
.nav-logo {
  font-size: 22px;
  font-weight: bold;
  color: white;
  display: flex;
  align-items: center;
  gap: 8px;
}

.logo-icon {
  width: 22px;
  height: 22px;
}

/* Links */
.nav-links {
  list-style: none;
  display: flex;
  gap: 25px;
}

.nav-links li a {
  color: white;
  text-decoration: none;
  font-size: 16px;
  padding: 6px 10px;
  border-radius: 6px;
  transition: background 0.2s ease, color 0.2s ease;
}

/* Hover Effect */
.nav-links li a:hover {
  background-color: #1fa233; /* Lighter green */
}

/* Active Page */
.nav-links li a.active {
  background-color: white;
  color: #2d714f;
  font-weight: bold;
}
</style>
