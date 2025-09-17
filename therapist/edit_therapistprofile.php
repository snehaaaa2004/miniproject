<?php
session_start();
include('../connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Therapist') {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $specialization = $_POST['specialization'] ?? '';
    $experience = $_POST['experience'] ?? '';
    $language = $_POST['language'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $availability = $_POST['availability'] ?? '';
    $modeArray = $_POST['mode'] ?? [];
    $fees = $_POST['fees'] ?? 0;
    $bio = $_POST['bio'] ?? '';
    $modeString = implode(',', $modeArray);

    // Current image
    $current = mysqli_query($conn, "SELECT image FROM therapists WHERE user_id = '$user_id'");
    $currentRow = mysqli_fetch_assoc($current);
    $currentImage = $currentRow['image'];

    // Upload image
    $imagePath = $currentImage;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $imageName = basename($_FILES['image']['name']);
        $uniqueName = $user_id . '_' . time() . '.' . strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $targetFile = $targetDir . $uniqueName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $uniqueName;
        }
    }

    // Update DB (add bio field)
    $stmt = $conn->prepare("UPDATE therapists 
        SET specialization=?, experience=?, language=?, gender=?, availability=?, mode=?, fees=?, image=?, bio=?
        WHERE user_id=?");

    $stmt->bind_param("ssssssssss", $specialization, $experience, $language, $gender, $availability, $modeString, $fees, $imagePath, $bio, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='therapist_profile.php';</script>";
        exit;
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}

// Fetch data
$result = mysqli_query($conn, "SELECT * FROM therapists WHERE user_id = '$user_id'");
$therapist = mysqli_fetch_assoc($result);

$availability = $therapist['availability'] ?? '';
$modes = explode(',', $therapist['mode'] ?? '');
$image = !empty($therapist['image']) ? "../uploads/" . $therapist['image'] : "../images/default-user.png";
$bio = $therapist['bio'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
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

    body {
      font-family: 'Inter', Arial, sans-serif;
      background: #f5f7fa;
      margin: 0;
      padding: 0;
      color: var(--text);
    }

    .container {
      width: 100%;
      max-width: 700px;
      background: #fff;
      margin: 2rem auto;
      padding: 2.5rem;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
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

    @media (max-width: 768px) {
      .container { padding: 1.5rem; }
      h2 { font-size: 1.5rem; }
    }

    @media (max-width: 500px) {
      .container { padding: 0.5rem; }
      h2 { font-size: 1.1rem; }
      input, select, textarea { font-size: 0.95rem; padding: 0.5rem; }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Edit Your Profile</h2>
    <form method="POST" enctype="multipart/form-data">

      <div class="input-group">
        <label for="specialization" class="required-field">Specialization:</label>
        <select id="specialization" name="specialization" required>
          <option value="" disabled>Select specialization</option>
          <option value="Depression" <?= $therapist['specialization'] == 'Depression' ? 'selected' : '' ?>>Depression</option>
          <option value="Anxiety" <?= $therapist['specialization'] == 'Anxiety' ? 'selected' : '' ?>>Anxiety</option>
          <option value="Relationship Counseling" <?= $therapist['specialization'] == 'Relationship Counseling' ? 'selected' : '' ?>>Relationship Counseling</option>
          <option value="Family Therapy" <?= $therapist['specialization'] == 'Family Therapy' ? 'selected' : '' ?>>Family Therapy</option>
          <option value="Child & Adolescent Therapy" <?= $therapist['specialization'] == 'Child & Adolescent Therapy' ? 'selected' : '' ?>>Child & Adolescent Therapy</option>
          <option value="Group Therapy" <?= $therapist['specialization'] == 'Group Therapy' ? 'selected' : '' ?>>Group Therapy</option>
          <option value="Trauma & PTSD" <?= $therapist['specialization'] == 'Trauma & PTSD' ? 'selected' : '' ?>>Trauma & PTSD</option>
          <option value="Addiction Counseling" <?= $therapist['specialization'] == 'Addiction Counseling' ? 'selected' : '' ?>>Addiction Counseling</option>
        </select>
      </div>

      <div class="input-group">
        <label for="experience" class="required-field">Experience (in years):</label>
        <input type="number" id="experience" name="experience" placeholder="e.g. 5" min="0" max="60" value="<?= htmlspecialchars($therapist['experience']) ?>" required>
      </div>

      <div class="input-group">
        <label for="language" class="required-field">Languages Known:</label>
        <input type="text" id="language" name="language" placeholder="e.g. English, Hindi" value="<?= htmlspecialchars($therapist['language']) ?>" required>
      </div>

      <div class="input-group">
        <label class="required-field">Gender:</label>
        <div class="radio-group">
          <label><input type="radio" name="gender" value="Male" <?= $therapist['gender'] === 'Male' ? 'checked' : '' ?>> Male</label>
          <label><input type="radio" name="gender" value="Female" <?= $therapist['gender'] === 'Female' ? 'checked' : '' ?>> Female</label>
          <label><input type="radio" name="gender" value="Non-binary" <?= $therapist['gender'] === 'Non-binary' ? 'checked' : '' ?>> Non-binary</label>
        </div>
      </div>

      <div class="input-group">
        <label for="availability" class="required-field">Availability:</label>
        <select id="availability" name="availability" required>
          <option value="" disabled>Select availability</option>
          <option value="Mornings (8AM-12PM)" <?= $availability == 'Mornings (8AM-12PM)' ? 'selected' : '' ?>>Mornings (8AM-12PM)</option>
          <option value="Afternoons (12PM-5PM)" <?= $availability == 'Afternoons (12PM-5PM)' ? 'selected' : '' ?>>Afternoons (12PM-5PM)</option>
          <option value="Evenings (5PM-9PM)" <?= $availability == 'Evenings (5PM-9PM)' ? 'selected' : '' ?>>Evenings (5PM-9PM)</option>
          <option value="Weekends" <?= $availability == 'Weekends' ? 'selected' : '' ?>>Weekends</option>
          <option value="Any Time" <?= $availability == 'Any Time' ? 'selected' : '' ?>>Any Time</option>
        </select>
      </div>

      <div class="input-group">
        <label class="required-field">Modes of Consultation:</label>
        <div class="checkbox-group">
          <label><input type="checkbox" name="mode[]" value="Video call" <?= in_array('Video call', $modes) ? 'checked' : '' ?>> Video Call</label>
          <label><input type="checkbox" name="mode[]" value="Audio call" <?= in_array('Audio Call', $modes) ? 'checked' : '' ?>> Audio Call</label>
          <label><input type="checkbox" name="mode[]" value="In-person" <?= in_array('In-person', $modes) ? 'checked' : '' ?>> In-person</label>
        </div>
      </div>

      <div class="input-group">
        <label for="fees" class="required-field">Consultation Fees (in USD):</label>
        <input type="number" id="fees" name="fees" placeholder="e.g. 50" min="0" step="0.01" value="<?= htmlspecialchars($therapist['fees']) ?>" required>
      </div>

      <div class="input-group">
        <label for="bio" class="required-field">Bio:</label>
        <textarea id="bio" name="bio" rows="4" placeholder="Write a short bio about yourself..." required><?= htmlspecialchars($bio) ?></textarea>
      </div>

      <div class="input-group">
        <label class="required-field">Upload Profile Image:</label>
        <input type="file" name="image" accept="image/*">
      </div>

      <div class="img-preview">
        <img src="<?= htmlspecialchars($image) ?>" alt="Profile Picture">
      </div>

      <button type="submit">Save Changes</button>
    </form>
  </div>
</body>
</html>
