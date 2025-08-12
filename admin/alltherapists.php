<?php
session_start();
include('../connect.php');

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
      --primary: #4361ee;
      --primary-dark: #3a56d4;
      --secondary: #3f37c9;
      --accent: #4895ef;
      --light: #f8f9fa;
      --dark: #212529;
      --gray: #6c757d;
      --light-gray: #e9ecef;
      --border-radius: 8px;
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
      background-color: #f8fafc;
      color: var(--dark);
      line-height: 1.6;
      padding: 0;
    }

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

    h1 {
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

    .stats-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: white;
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
      background: white;
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
      background-color: #e0e7ff;
      color: var(--primary-dark);
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
      background-color: #ecfdf5;
      color: #065f46;
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
      background-color: white;
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
  <div class="admin-container">
    <header>
      <h1>
        <i class="fas fa-user-md"></i> Approved Therapists
      </h1>
      <div class="admin-actions">
        <a href="admindash.php" class="btn btn-outline">
          <i class="fas fa-clock"></i> Pending Approvals
        </a>
        <a href="dashboard.php" class="btn">
          <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
      </div>
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
</body>
</html>