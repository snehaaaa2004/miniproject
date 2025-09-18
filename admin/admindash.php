<?php
include('../connect.php');

session_start();

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
    include('adminnav.php');
}

// Run the query to get pending therapist profiles
$sql = "SELECT t.*, u.name, u.email 
        FROM therapists t
        JOIN users u ON t.user_id = u.id
        WHERE t.approved = 0";

$result = $conn->query($sql);

// Check for query failure
if (!$result) {
    die("Query Failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>Approve Therapists - SerenityConnect</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
 :root {
  --primary: #154918ff;        /* Main green */
  --primary-light: #e8f5e9;  /* Light green background */
  --primary-dark: #103d13ff;   /* Darker green */
  --secondary: #388e3c;      /* Secondary green */
  --accent: #d32f2f;         /* Red for reject */
  --light: #ffffff;
  --dark: #333333;
  --text: #333333;
  --text-light: #666666;
  --background: #f4f9f5;     /* Soft greenish page bg */
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
  background-color: var(--background);
  color: var(--text);
  line-height: 1.6;
}

/* Container */
.admin-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

/* Header */
header {
  background-color: var(--light);
  padding: 1.5rem;
  margin-bottom: 2rem;
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
}

header h2 {
  color: var(--primary);
  font-weight: 600;
  margin-bottom: 0.5rem;
  font-size: 1.8rem;
}

header p {
  color: var(--text-light);
}

/* Admin Nav */
.admin-nav {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.nav-link {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.8rem 1.2rem;
  background-color: var(--light);
  color: var(--primary);
  border-radius: var(--border-radius);
  text-decoration: none;
  font-weight: 500;
  box-shadow: var(--box-shadow);
  transition: var(--transition);
  border: 1px solid var(--primary-light);
}

.nav-link:hover {
  background-color: var(--primary);
  color: white;
  transform: translateY(-2px);
}

/* Grid */
.therapists-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

/* Cards */
.card {
  background: var(--light);
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  overflow: hidden;
  transition: var(--transition);
  border: 1px solid var(--primary-light);
}

.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
}

/* Card Header */
.card-header {
  display: flex;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid var(--primary-light);
  background-color: var(--primary-light);
}

.profile-img {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  margin-right: 1.5rem;
  border: 3px solid var(--light);
  box-shadow: var(--box-shadow);
}

.profile-info h3 {
  color: var(--primary-dark);
  margin-bottom: 0.25rem;
  font-weight: 600;
}

.profile-info p {
  color: var(--text-light);
  font-size: 0.9rem;
}

/* Card Body */
.card-body {
  padding: 1.5rem;
}

.detail-row {
  display: flex;
  margin-bottom: 0.8rem;
}

.detail-label {
  flex: 0 0 120px;
  font-weight: 500;
  color: var(--dark);
}

.detail-value {
  flex: 1;
  color: var(--text-light);
}

/* Badges */
.badge {
  display: inline-block;
  padding: 0.3rem 0.6rem;
  background-color: var(--primary-light);
  color: var(--primary);
  border-radius: 50px;
  font-size: 0.8rem;
  margin-right: 0.5rem;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

/* Footer Buttons */
.card-footer {
  display: flex;
  justify-content: flex-end;
  padding: 1rem 1.5rem;
  background-color: var(--background);
  border-top: 1px solid var(--primary-light);
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: var(--border-radius);
  font-weight: 500;
  cursor: pointer;
  transition: var(--transition);
  text-decoration: none;
  font-size: 0.9rem;
}

.btn-approve {
  background-color: var(--primary);
  color: white;
}

.btn-approve:hover {
  background-color: var(--primary-dark);
}

.btn-reject {
  background-color: var(--accent);
  color: white;
  margin-left: 0.8rem;
}

.btn-reject:hover {
  background-color: #b71c1c;
}

.btn:hover {
  transform: translateY(-2px);
}

/* No Results */
.no-results {
  text-align: center;
  padding: 3rem;
  background-color: var(--light);
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  grid-column: 1 / -1;
  border: 1px solid var(--primary-light);
}

.no-results i {
  font-size: 2rem;
  color: var(--text-light);
  margin-bottom: 1rem;
}

/* Mobile */
@media (max-width: 768px) {
  .admin-container {
    padding: 1.5rem;
  }
  
  .card-header {
    flex-direction: column;
    text-align: center;
  }
  
  .profile-img {
    margin-right: 0;
    margin-bottom: 1rem;
  }
  
  .detail-row {
    flex-direction: column;
  }
  
  .detail-label {
    margin-bottom: 0.2rem;
  }
}

  </style>
</head>
<body>
  <div class="admin-container">
    <header>
      <h2><i class="fas fa-user-md"></i> Pending Therapist Approvals</h2>
      <p>Review and approve new therapist registrations</p>
    </header>

    <nav class="admin-nav">
      <a href="admindash.php" class="nav-link">
        <i class="fas fa-tachometer-alt"></i> Dashboard
      </a>
      <a href="alltherapists.php" class="nav-link">
        <i class="fas fa-users"></i> Approved Therapists
      </a>
      <a href="all_bookings.php" class="nav-link">
        <i class="fas fa-calendar-check"></i> View Bookings
      </a>
      <a href="view_messages.php" class="nav-link">
        <i class="fas fa-envelope"></i> User Messages
      </a>
      <a href="../logout.php" class="nav-link">
        <i class="fas fa-sign-out-alt"></i> Logout
      </a>
    </nav>

    <div class="therapists-grid">
      <?php if ($result->num_rows > 0) : ?>
        <?php while ($row = $result->fetch_assoc()) : ?>
          <div class="card">
            <div class="card-header">
              <?php
              $imagePath = "../" . htmlspecialchars($row['image']);
              $defaultImage = "../uploads/default.png";
              ?>
              <img src="<?= (!empty($row['image']) && file_exists($imagePath)) ? $imagePath : $defaultImage ?>" 
                   alt="Therapist Photo" class="profile-img">
              
              <div class="profile-info">
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <p><?= htmlspecialchars($row['email']) ?></p>
              </div>
            </div>
            
            <div class="card-body">
              <div class="detail-row">
                <span class="detail-label">Gender:</span>
                <span class="detail-value"><?= htmlspecialchars($row['gender']) ?></span>
              </div>
              
              <div class="detail-row">
                <span class="detail-label">Specialization:</span>
                <span class="detail-value"><?= htmlspecialchars($row['specialization']) ?></span>
              </div>
              
              <div class="detail-row">
                <span class="detail-label">Experience:</span>
                <span class="detail-value"><?= htmlspecialchars($row['experience']) ?> years</span>
              </div>
              
              <div class="detail-row">
                <span class="detail-label">Languages:</span>
                <span class="detail-value">
                  <?php
                  $languages = explode(',', $row['language']);
                  foreach ($languages as $lang) {
                      echo '<span class="badge">' . htmlspecialchars(trim($lang)) . '</span>';
                  }
                  ?>
                </span>
              </div>
              
              <div class="detail-row">
                <span class="detail-label">Availability:</span>
                <span class="detail-value"><?= htmlspecialchars($row['availability']) ?></span>
              </div>
              
              <div class="detail-row">
                <span class="detail-label">Consultation Mode:</span>
                <span class="detail-value">
                  <?php
                  $modes = explode(',', $row['mode']);
                  foreach ($modes as $mode) {
                      $icon = '';
                      if (stripos($mode, 'video') !== false) $icon = 'video';
                      elseif (stripos($mode, 'audio') !== false || stripos($mode, 'phone') !== false) $icon = 'phone-alt';
                      elseif (stripos($mode, 'person') !== false || stripos($mode, 'in-person') !== false) $icon = 'user';
                      else $icon = 'comments';
                      
                      echo '<span class="badge"><i class="fas fa-' . $icon . '"></i> ' . htmlspecialchars(trim($mode)) . '</span>';
                  }
                  ?>
                </span>
              </div>
            </div>
            
            <div class="card-footer">
              <a href="approve.php?id=<?= $row['id'] ?>" class="btn btn-approve">
                <i class="fas fa-check"></i> Approve
              </a>
              <a href="reject.php?id=<?= $row['id'] ?>" class="btn btn-reject">
                <i class="fas fa-times"></i> Reject
              </a>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else : ?>
        <div class="no-results">
          <i class="far fa-check-circle"></i>
          <h3>No Pending Approvals</h3>
          <p>There are currently no therapist profiles waiting for approval.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

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