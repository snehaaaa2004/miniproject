<?php
session_start();
include('../connect.php');
include('adminnav.php');

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}

$query = "SELECT t.*, u.name AS therapist_name 
          FROM therapists t 
          JOIN users u ON t.user_id = u.id 
          WHERE t.approved = '1'
          ORDER BY u.name ASC";

$result = mysqli_query($conn, $query);

// Handle query failure
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Approved Therapists - Admin Panel</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
 :root {
  --primary: #114614ff;       /* Main green */
  --primary-dark: #0e4212ff;  /* Darker green */
  --secondary: #123b14ff;     /* Secondary green */
  --accent: #66bb6a;        /* Lighter green */
  --danger: #d32f2f;
  --light: #ffffff;
  --dark: #333333;
  --gray: #6c757d;
  --light-gray: #e9ecef;
  --border-radius: 10px;
  --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  --transition: all 0.3s ease;
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Inter', sans-serif;
  background-color: #f4f9f5;
  color: var(--dark);
  line-height: 1.6;
}

/* 🌿 NAVBAR */

.navbar .logo {
  font-size: 1.2rem;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.navbar ul {
  list-style: none;
  display: flex;
  gap: 1.5rem;
}

.navbar ul li a {
  color: white;
  text-decoration: none;
  font-weight: 500;
  transition: var(--transition);
}

.navbar ul li a:hover,
.navbar ul li a.active {
  color: var(--accent);
}

/* Responsive Navbar */
.navbar .menu-toggle {
  display: none;
  font-size: 1.5rem;
  cursor: pointer;
}

@media (max-width: 768px) {
  .navbar ul {
    display: none;
    flex-direction: column;
    background-color: var(--primary-dark);
    position: absolute;
    top: 60px;
    right: 0;
    width: 200px;
    padding: 1rem;
    border-radius: var(--border-radius);
  }

  .navbar ul.active {
    display: flex;
  }

  .navbar .menu-toggle {
    display: block;
  }
}

/* Existing Admin Panel Styles */
.admin-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 2rem;
}

header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--light-gray);
}

h2 {
  color: var(--primary);
  font-weight: 600;
  font-size: 1.8rem;
}

.admin-actions {
  display: flex;
  gap: 1rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.2rem;
  background-color: var(--primary);
  color: white;
  border: none;
  border-radius: var(--border-radius);
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition);
  text-decoration: none;
  font-size: 0.9rem;
}

.btn:hover {
  background-color: var(--primary-dark);
  transform: translateY(-2px);
}

.btn-outline {
  background-color: transparent;
  border: 1px solid var(--primary);
  color: var(--primary);
}

.btn-outline:hover {
  background-color: var(--primary);
  color: white;
}

/* 🚨 Danger button */
.btn-danger {
  background-color: var(--danger);
  color: white;
  border: none;
}

.btn-danger:hover {
  background-color: #b71c1c;
}

.stats-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: var(--light);
  padding: 1.5rem;
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  text-align: center;
}

.stat-card h3 {
  font-size: 2rem;
  color: var(--primary);
  margin-bottom: 0.5rem;
}

.stat-card p {
  color: var(--gray);
  font-size: 0.9rem;
}

.therapist-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}

.card {
  background: var(--light);
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  overflow: hidden;
  transition: var(--transition);
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.card-img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.card-body {
  padding: 1.5rem;
}

.card-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: var(--primary-dark);
  margin-bottom: 0.5rem;
}

.card-text {
  color: var(--gray);
  font-size: 0.9rem;
  margin-bottom: 0.8rem;
  line-height: 1.5;
}

.card-text strong {
  color: var(--dark);
  font-weight: 500;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  padding: 1rem 1.5rem;
  background-color: var(--light);
  border-top: 1px solid var(--light-gray);
}

.badge {
  display: inline-block;
  padding: 0.3rem 0.6rem;
  background-color: var(--accent);
  color: var(--dark);
  border-radius: 50px;
  font-size: 0.75rem;
  font-weight: 500;
  margin-right: 0.5rem;
  margin-bottom: 0.5rem;
}

.badge-group {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin-bottom: 0.8rem;
}

.availability {
  background-color: #e8f5e9;
  color: #2e7d32;
  padding: 0.5rem;
  border-radius: var(--border-radius);
  margin-top: 0.5rem;
  font-size: 0.85rem;
}

.availability i {
  margin-right: 0.3rem;
}

.no-results {
  text-align: center;
  padding: 3rem;
  background-color: var(--light);
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  grid-column: 1 / -1;
}

.section-title {
  font-size: 0.8rem;
  text-transform: uppercase;
  color: var(--gray);
  margin: 1rem 0 0.5rem 0;
  letter-spacing: 0.5px;
}

@media (max-width: 768px) {
  .admin-container {
    padding: 1.5rem;
  }
  
  header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .admin-actions {
    width: 100%;
    flex-direction: column;
  }
  
  .btn {
    width: 100%;
    justify-content: center;
  }
}
  </style>
</head>
<body>

  <!-- 🌿 NAVBAR -->
  

  <div class="admin-container">
    <header>
      <h2>
        <i class="fas fa-user-md"></i> Approved Therapists
      </h2>
      
    </header>

    <div class="stats-cards">
      <?php
      // Get count of approved therapists
      $count_query = "SELECT COUNT(*) AS total FROM therapists WHERE approved = '1'";
      $count_result = mysqli_query($conn, $count_query);
      $count = mysqli_fetch_assoc($count_result)['total'];
      ?>
      <div class="stat-card">
        <h3><?= $count ?></h3>
        <p>Approved Therapists</p>
      </div>
      <div class="stat-card">
        <h3><?= date('M Y') ?></h3>
        <p>Current Month</p>
      </div>
    </div>

    <div class="therapist-grid">
      <?php if (mysqli_num_rows($result) > 0) : ?>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
          <div class="card">
            <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="Therapist Image" class="card-img">
            
            <div class="card-body">
              <h3 class="card-title"><?= htmlspecialchars($row['therapist_name']) ?></h3>
              
              <p class="card-text">
                <strong>Specialization:</strong> <?= htmlspecialchars($row['specialization']) ?>
              </p>
              
              <p class="card-text">
                <strong>Experience:</strong> <?= htmlspecialchars($row['experience']) ?> years
              </p>

              <div class="section-title">Availability</div>
              <div class="availability">
                <i class="fas fa-clock"></i>
                <?= htmlspecialchars($row['availability']) ?>
              </div>

              <div class="section-title">Gender</div>
              <div class="badge-group">
                <span class="badge">
                  <i class="fas fa-<?= strtolower($row['gender']) === 'male' ? 'male' : 'female' ?>"></i> 
                  <?= htmlspecialchars($row['gender']) ?>
                </span>
              </div>

              <div class="section-title">Languages</div>
              <div class="badge-group">
                <?php 
                $languages = explode(',', $row['language']);
                foreach ($languages as $lang): 
                  $lang = trim($lang);
                  if (!empty($lang)): ?>
                    <span class="badge">
                      <i class="fas fa-language"></i> 
                      <?= htmlspecialchars($lang) ?>
                    </span>
                  <?php endif;
                endforeach; ?>
              </div>

              <div class="section-title">Consultation Modes</div>
              <div class="badge-group">
                <?php 
                $modes = explode(',', $row['mode']);
                foreach ($modes as $mode): 
                  $mode = trim($mode);
                  if (!empty($mode)): 
                    $icon = '';
                    if (strpos(strtolower($mode), 'video') !== false) {
                      $icon = 'video';
                    } elseif (strpos(strtolower($mode), 'audio') !== false || strpos(strtolower($mode), 'phone') !== false) {
                      $icon = 'phone';
                    } elseif (strpos(strtolower($mode), 'person') !== false || strpos(strtolower($mode), 'in-person') !== false) {
                      $icon = 'user';
                    } else {
                      $icon = 'comments';
                    }
                    ?>
                    <span class="badge">
                      <i class="fas fa-<?= $icon ?>"></i> 
                      <?= htmlspecialchars($mode) ?>
                    </span>
                  <?php endif;
                endforeach; ?>
              </div>

              <!-- 🚨 Remove Therapist Button -->
              <div style="margin-top: 1rem;">
                <a href="delete_therapist.php?id=<?= $row['id'] ?>" 
                   class="btn btn-outline" 
                   style="background-color:yellow;color:green;border:none;"
                   onclick="return confirm('Are you sure you want to remove this therapist? This action cannot be undone.');">
                   <i class="fas fa-trash"></i> Remove Therapist
                </a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else : ?>
        <div class="no-results">
          <h3><i class="far fa-frown"></i> No Approved Therapists Found</h3>
          <p>There are currently no approved therapists in the system.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

<script>
  // Toggle menu on mobile
  document.querySelector(".menu-toggle").addEventListener("click", function () {
    document.querySelector(".navbar ul").classList.toggle("active");
  });
</script>
</body>
</html>
