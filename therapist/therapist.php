<?php
session_start();
include('../auth_check.php');
include('../connect.php');

// Generate custom therapist ID
$check = mysqli_query($conn, "SELECT COUNT(*) AS total FROM therapists");
$countData = mysqli_fetch_assoc($check);
$nextId = $countData['total'] + 1;
$therapistId = 'THR' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $specialization = $_POST['specialization'] ?? '';
    $experience = $_POST['experience'] ?? '';
    $language = $_POST['language'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $availability = $_POST['availability'] ?? '';
    $modes = isset($_POST['mode']) ? implode(',', $_POST['mode']) : '';
    $fees = $_POST['fees'] ?? '';
    $bio = $_POST['bio'] ?? '';
    $user_id = $_SESSION['user_id'];

    // Handle image upload
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imgTmp = $_FILES['image']['tmp_name'];
        $imgExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $imgNewName = $user_id . '_' . time() . '.' . $imgExt;
        $imgDest = '../uploads/' . $imgNewName;

        if (move_uploaded_file($imgTmp, $imgDest)) {
            $imageName = $imgNewName;
        }
    }

    // Save therapist profile
    $stmt = $conn->prepare("INSERT INTO therapists (id, user_id, specialization, experience, language, gender, availability, mode, fees, image, bio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $therapistId, $user_id, $specialization, $experience, $language, $gender, $availability, $modes, $fees, $imageName, $bio);

    if ($stmt->execute()) {
        echo "<script>alert('Profile submitted successfully!'); window.location.href='therapihome.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error submitting profile.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Therapist Profile - SerenityConnect</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #166138ff;
      --primary-dark: #18613aff;
      --secondary: #0d5127ff;
      --accent: #81c3d7;
      --light: #f8f9fa;
      --dark-gray: #6c757d;
      --error: #dc3545;
      --border-radius: 8px;
      --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      --transition: all 0.3s ease;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f5f7fa;
      color: #212529;
    }

    /* Navbar */
    nav {
      background: var(--primary);
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: var(--box-shadow);
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    .logo {
      font-size: 1.5rem;
      font-weight: 700;
    }
    .nav-links {
      display: flex;
      gap: 1.5rem;
    }
    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s;
    }
    .nav-links a:hover {
      color: var(--accent);
    }
    .logout-btn {
      background: white;
      color: var(--primary);
      padding: 0.5rem 1rem;
      border-radius: var(--border-radius);
      font-weight: 600;
      text-decoration: none;
      transition: var(--transition);
    }
    .logout-btn:hover {
      background: var(--accent);
      color: white;
    }
    /* Mobile menu */
    .menu-toggle {
      display: none;
      font-size: 1.5rem;
      cursor: pointer;
    }
    @media (max-width: 768px) {
      .nav-links {
        display: none;
        position: absolute;
        top: 70px;
        right: 0;
        background: var(--primary);
        flex-direction: column;
        width: 200px;
        padding: 1rem;
      }
      .nav-links.active { display: flex; }
      .menu-toggle { display: block; }
    }

    /* Form Container */
    .main-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: calc(100vh - 80px);
      padding: 2rem;
    }
    .register-container {
      background-color: white;
      padding: 2.5rem;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      width: 100%;
      max-width: 700px;
    }
    h2 {
      text-align: center;
      color: var(--secondary);
      margin-bottom: 1.5rem;
      font-size: 2rem;
      font-weight: 600;
    }
    .input-group { margin-bottom: 1.5rem; }
    label { display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--secondary); }
    input, select, textarea {
      width: 100%; padding: 0.75rem; border: 1px solid #ced4da;
      border-radius: var(--border-radius); font-size: 1rem;
      background: var(--light);
    }
    button {
      background-color: var(--primary);
      color: white;
      padding: 0.75rem 1.5rem;
      border: none; border-radius: var(--border-radius);
      font-size: 1rem; cursor: pointer; width: 100%;
      font-weight: 600; margin-top: 1rem;
    }
    button:hover { background-color: var(--primary-dark); }
  </style>
</head>
<body>

<!-- Navbar -->
<nav>
  <div class="logo">SerenityConnect</div>
  <div class="menu-toggle" onclick="document.querySelector('.nav-links').classList.toggle('active')">&#9776;</div>
  <div class="nav-links">
    <a href="therapihome.php">Home</a>
    <a href="therapist.php">Profile</a>
    <a href="appointments.php">Appointments</a>
    <a href="messages.php">Messages</a>
    <a href="../logout.php" class="logout-btn">Logout</a>
  </div>
</nav>

<!-- Main Form -->
<div class="main-container">
  <div class="register-container">
    <h2>Therapist Profile Submission</h2>
    <form id="therapistProfileForm" action="therapist.php" method="POST" enctype="multipart/form-data" novalidate>
      <div class="input-group">
        <label for="specialization">Specialization:</label>
        <select id="specialization" name="specialization" required>
          <option value="" disabled selected>Select specialization</option>
          <option value="Depression">Depression</option>
          <option value="Anxiety">Anxiety</option>
          <option value="Relationship Counseling">Relationship Counseling</option>
          <option value="Family Therapy">Family Therapy</option>
          <option value="Child & Adolescent Therapy">Child & Adolescent Therapy</option>
          <option value="Group Therapy">Group Therapy</option>
          <option value="Trauma & PTSD">Trauma & PTSD</option>
          <option value="Addiction Counseling">Addiction Counseling</option>
        </select>
      </div>
      <div class="input-group">
        <label for="experience">Experience (in years):</label>
        <input type="number" id="experience" name="experience" min="0" max="60" required>
      </div>
      <div class="input-group">
        <label for="language">Languages Known:</label>
        <input type="text" id="language" name="language" placeholder="e.g. English, Hindi" required>
      </div>
      <div class="input-group">
        <label>Gender:</label>
        <label><input type="radio" name="gender" value="Male"> Male</label>
        <label><input type="radio" name="gender" value="Female"> Female</label>
        <label><input type="radio" name="gender" value="Non-binary"> Non-binary</label>
      </div>
      <div class="input-group">
        <label for="availability">Availability:</label>
        <select id="availability" name="availability" required>
          <option value="" disabled selected>Select availability</option>
          <option value="Mornings (8AM-12PM)">Mornings (8AM-12PM)</option>
          <option value="Afternoons (12PM-5PM)">Afternoons (12PM-5PM)</option>
          <option value="Evenings (5PM-9PM)">Evenings (5PM-9PM)</option>
          <option value="Weekends">Weekends</option>
          <option value="Any Time">Any Time</option>
        </select>
      </div>
      <div class="input-group">
        <label>Modes of Consultation:</label>
        <label><input type="checkbox" name="mode[]" value="Video Call"> Video Call</label>
        <label><input type="checkbox" name="mode[]" value="Audio Call"> Audio Call</label>
        <label><input type="checkbox" name="mode[]" value="In-person"> In-person</label>
      </div>
      <div class="input-group">
        <label for="fees">Consultation Fees (USD):</label>
        <input type="number" id="fees" name="fees" min="0" step="0.01" required>
      </div>
      <div class="input-group">
        <label>Upload Profile Image:</label>
        <input type="file" name="image" accept="image/*" required>
      </div>
      <div class="input-group">
        <label for="bio">Bio:</label>
        <textarea id="bio" name="bio" rows="4" required></textarea>
      </div>
      <button type="submit">Complete Profile Submission</button>
    </form>
  </div>
</div>

</body>
</html>
