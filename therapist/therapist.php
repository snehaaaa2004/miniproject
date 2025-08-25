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
    $bio = $_POST['bio'] ?? ''; // Add bio field
    $user_id = $_SESSION['user_id'];

    // Handle image upload
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imgTmp = $_FILES['image']['tmp_name'];
        $imgExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $imgNewName = $user_id . '_' . time() . '.' . $imgExt;
        $imgDest = '../uploads/' . $imgNewName;

        // Move uploaded file
        if (move_uploaded_file($imgTmp, $imgDest)) {
            $imageName = $imgNewName;
        }
    }

    // Save therapist profile to database (add bio column)
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
      --primary: #3a7ca5;
      --primary-dark: #2f6690;
      --secondary: #16425b;
      --accent: #81c3d7;
      --light: #f8f9fa;
      --light-gray: #e9ecef;
      --medium-gray: #ced4da;
      --dark-gray: #6c757d;
      --text: #212529;
      --error: #dc3545;
      --success: #28a745;
      --border-radius: 8px;
      --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      --transition: all 0.3s ease;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f5f7fa;
      color: var(--text);
    }

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

    .input-group {
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      font-weight: 500;
      margin-bottom: 0.5rem;
      color: var(--secondary);
    }

    input, select, textarea {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid var(--medium-gray);
      border-radius: var(--border-radius);
      font-size: 1rem;
      background: var(--light);
      transition: border-color 0.2s;
    }

    input:focus, select:focus, textarea:focus {
      border-color: var(--primary);
      outline: none;
    }

    .radio-group, .checkbox-group {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .radio-group label, .checkbox-group label {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 400;
      cursor: pointer;
    }

    .error-message {
      color: var(--error);
      font-size: 0.8rem;
      margin-top: 0.25rem;
      display: none;
    }

    .required-field::after {
      content: " *";
      color: var(--error);
    }

    button {
      background-color: var(--primary);
      color: white;
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: var(--border-radius);
      font-size: 1rem;
      cursor: pointer;
      transition: var(--transition);
      width: 100%;
      font-weight: 600;
      margin-top: 1rem;
    }

    button:hover {
      background-color: var(--primary-dark);
    }

    .img-preview {
      text-align: center;
      margin-top: 1rem;
    }

    .img-preview img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid var(--primary);
      box-shadow: var(--box-shadow);
    }

    @media (max-width: 768px) {
      .register-container { padding: 1.5rem; }
      h2 { font-size: 1.5rem; }
    }

    @media (max-width: 500px) {
      .main-container { padding: 0.5rem; }
      .register-container { padding: 0.5rem; }
      h2 { font-size: 1.1rem; }
      input, select, textarea { font-size: 0.95rem; padding: 0.5rem; }
    }
  </style>
</head>
<body>

<?php include 'therapistnav.php'; ?>

<div class="main-container">
  <div class="register-container">
    <h2>Therapist Profile Submission</h2>
    <form id="therapistProfileForm" action="therapist.php" method="POST" enctype="multipart/form-data" novalidate>

<div class="input-group">
  <label for="specialization" class="required-field">Specialization:</label>
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
  <div class="error-message" id="specialization-error">Please select a specialization</div>
</div>

      <div class="input-group">
        <label for="experience" class="required-field">Experience (in years):</label>
        <input type="number" id="experience" name="experience" placeholder="e.g. 5" min="0" max="60" required>
        <div class="error-message" id="experience-error">Please enter a valid number (0-60)</div>
      </div>

      <div class="input-group">
        <label for="language" class="required-field">Languages Known:</label>
        <input type="text" id="language" name="language" placeholder="e.g. English, Hindi" required>
        <div class="error-message" id="language-error">Please enter languages you speak</div>
      </div>

      <div class="input-group">
        <label class="required-field">Gender:</label>
        <div class="radio-group">
          <label><input type="radio" name="gender" value="Male" required> Male</label>
          <label><input type="radio" name="gender" value="Female"> Female</label>
          <label><input type="radio" name="gender" value="Non-binary"> Non-binary</label>
        </div>
        <div class="error-message" id="gender-error">Please select your gender</div>
      </div>

      <div class="input-group">
        <label for="availability" class="required-field">Availability:</label>
        <select id="availability" name="availability" required>
          <option value="" disabled selected>Select availability</option>
          <option value="Mornings (8AM-12PM)">Mornings (8AM-12PM)</option>
          <option value="Afternoons (12PM-5PM)">Afternoons (12PM-5PM)</option>
          <option value="Evenings (5PM-9PM)">Evenings (5PM-9PM)</option>
          <option value="Weekends">Weekends</option>
          <option value="Any Time">Any Time</option>
        </select>
        <div class="error-message" id="availability-error">Please select your availability</div>
      </div>

      <div class="input-group">
        <label class="required-field">Modes of Consultation:</label>
        <div class="checkbox-group">
          <label><input type="checkbox" name="mode[]" value="Google meet"> Video Call</label>
          <label><input type="checkbox" name="mode[]" value="Phone"> Audio Call</label>
          <label><input type="checkbox" name="mode[]" value="Offline"> In-person</label>
        </div>
        <div class="error-message" id="modes-error">Please select at least one mode</div>
      </div>
      <!-- New Fees Field -->
<div class="input-group">
  <label for="fees" class="required-field">Consultation Fees (in USD):</label>
  <input type="number" id="fees" name="fees" placeholder="e.g. 50" min="0" step="0.01" required>
  <div class="error-message" id="fees-error">Please enter a valid fee amount</div>
</div>

      <div class="input-group">
        <label class="required-field">Upload Profile Image:</label>
        <input type="file" name="image" accept="image/*" required>
      </div>

      <div class="input-group">
        <label for="bio" class="required-field">Bio:</label>
        <textarea id="bio" name="bio" rows="4" placeholder="Write a short bio about yourself..." required></textarea>
        <div class="error-message" id="bio-error">Please enter your bio</div>
      </div>

      <button type="submit">Complete Profile Submission</button>
    </form>
  </div>
</div>

<script>
document.getElementById('therapistProfileForm').addEventListener('submit', function(e) {
  const specialization = document.getElementById('specialization');
  const experience = document.getElementById('experience');
  const language = document.getElementById('language');
  const genderRadios = document.querySelectorAll('input[name="gender"]');
  const availability = document.getElementById('availability');
  const modeCheckboxes = document.querySelectorAll('input[name="mode[]"]');
  const bio = document.getElementById('bio');

  let valid = true;

  // Specialization
  if (!specialization.value.trim()) {
    document.getElementById('specialization-error').style.display = 'block';
    valid = false;
  } else {
    document.getElementById('specialization-error').style.display = 'none';
  }

  // Experience
  if (!experience.value || experience.value < 0 || experience.value > 60) {
    document.getElementById('experience-error').style.display = 'block';
    valid = false;
  } else {
    document.getElementById('experience-error').style.display = 'none';
  }

  // Language
  if (!language.value.trim()) {
    document.getElementById('language-error').style.display = 'block';
    valid = false;
  } else {
    document.getElementById('language-error').style.display = 'none';
  }

  // Gender
  if (![...genderRadios].some(r => r.checked)) {
    document.getElementById('gender-error').style.display = 'block';
    valid = false;
  } else {
    document.getElementById('gender-error').style.display = 'none';
  }

  // Availability
  if (!availability.value) {
    document.getElementById('availability-error').style.display = 'block';
    valid = false;
  } else {
    document.getElementById('availability-error').style.display = 'none';
  }

  // Mode
  if (![...modeCheckboxes].some(c => c.checked)) {
    document.getElementById('modes-error').style.display = 'block';
    valid = false;
  } else {
    document.getElementById('modes-error').style.display = 'none';
  }

  // Bio
  if (!bio.value.trim()) {
    document.getElementById('bio-error').style.display = 'block';
    valid = false;
  } else {
    document.getElementById('bio-error').style.display = 'none';
  }

  if (!valid) e.preventDefault();
});
</script>
</body>
</html>
