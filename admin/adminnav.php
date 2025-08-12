<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar">
  <div class="nav-container">
    <div class="nav-logo">SerenityConnect</div>
    <ul class="nav-links">
      <li><a href="admindash.php" class="<?= ($current_page == 'admindash.php') ? 'active' : '' ?>">Dashboard</a></li>
      <li><a href="user.php" class="<?= ($current_page == 'user.php') ? 'active' : '' ?>">Therapists</a></li>
      <li><a href="view_bookings.php" class="<?= ($current_page == 'view_bookings.php') ? 'active' : '' ?>">Bookings</a></li>
      <li><a href="view_bookings.php" class="<?= ($current_page == 'view_bookings.php') ? 'active' : '' ?>">User messages</a></li>
      <li><a href="/mini%20proj/serenity/logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<style>
.navbar {
  background-color:rgb(51, 100, 73);
  padding: 15px 0;
  color: white;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  position: sticky;
  top: 0;
  z-index: 999;
}

.nav-container {
  width: 90%;
  max-width: 1100px;
  margin: auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.nav-logo {
  font-size: 22px;
  font-weight: bold;
  color: white;
}

.nav-links {
  list-style: none;
  display: flex;
  gap: 20px;
}

.nav-links li a {
  color: white;
  text-decoration: none;
  font-size: 16px;
  padding: 6px 10px;
  border-radius: 6px;
  transition: background 0.2s ease, color 0.2s ease;
}

.nav-links li a:hover {
  background-color:rgb(31, 162, 51);
}

.nav-links li a.active {
  background-color: white;
  color:rgb(45, 113, 79);
  font-weight: bold;
}
</style>
