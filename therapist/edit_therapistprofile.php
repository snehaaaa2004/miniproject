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
    $modeString = implode(', ', $modeArray);

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
        $uniqueName = uniqid('therapist_') . "_" . $imageName;
        $targetFile = $targetDir . $uniqueName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $uniqueName;
        }
    }

    // Update DB
    $stmt = $conn->prepare("UPDATE therapists 
        SET specialization=?, experience=?, language=?, gender=?, availability=?, mode=?, image=? 
        WHERE user_id=?");

    $stmt->bind_param("ssssssss", $specialization, $experience, $language, $gender, $availability, $modeString, $imagePath, $user_id);

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
$modes = explode(', ', $therapist['mode'] ?? '');
$image = !empty($therapist['image']) ? "../uploads/" . $therapist['image'] : "../images/default-user.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f8f6;
      margin: 0;
      padding: 0;
    }

    .container {
      width: 80%;
      max-width: 700px;
      background: #fff;
      margin: 2rem auto;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #2e4d3d;
    }

    label {
      display: block;
      margin: 12px 0 6px;
      font-weight: 600;
    }

    input[type="text"],
    input[type="number"],
    select,
    input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-top: 2px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .checkbox-group,
    .radio-group {
      padding-left: 10px;
    }

    .checkbox-group label,
    .radio-group label {
      display: block;
      margin-bottom: 6px;
    }

    .img-preview {
      text-align: center;
      margin-top: 1rem;
    }

    .img-preview img {
      max-width: 150px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    button {
      background-color: #2e4d3d;
      color: white;
      border: none;
      padding: 10px 20px;
      margin-top: 20px;
      cursor: pointer;
      border-radius: 6px;
      font-size: 16px;
    }

    button:hover {
      background-color: #1f3b2a;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Edit Your Profile</h2>
    <form method="POST" enctype="multipart/form-data">
      <label>Specialization</label>
      <input type="text" name="specialization" value="<?= htmlspecialchars($therapist['specialization']) ?>" required>

      <label>Experience (in years)</label>
      <input type="number" name="experience" value="<?= htmlspecialchars($therapist['experience']) ?>" required>

      <label>Languages Known</label>
      <input type="text" name="language" value="<?= htmlspecialchars($therapist['language']) ?>" required>

      <label>Gender</label>
      <div class="radio-group">
        <label><input type="radio" name="gender" value="Male" <?= $therapist['gender'] === 'Male' ? 'checked' : '' ?>> Male</label>
        <label><input type="radio" name="gender" value="Female" <?= $therapist['gender'] === 'Female' ? 'checked' : '' ?>> Female</label>
        <label><input type="radio" name="gender" value="Other" <?= $therapist['gender'] === 'Other' ? 'checked' : '' ?>> Other</label>
      </div>

      <label>Availability</label>
      <select name="availability" required>
        <option value="">Select Availability</option>
        <option value="Any Time" <?= $availability == 'Any Time' ? 'selected' : '' ?>>Any Time</option>
        <option value="Mornings (8AM-12PM)" <?= $availability == 'Mornings (8AM-12PM)' ? 'selected' : '' ?>>Mornings (8AM-12PM)</option>
        <option value="Afternoons (12PM-5PM)" <?= $availability == 'Afternoons (12PM-5PM)' ? 'selected' : '' ?>>Afternoons (12PM-5PM)</option>
        <option value="Evenings (5PM-9PM)" <?= $availability == 'Evenings (5PM-9PM)' ? 'selected' : '' ?>>Evenings (5PM-9PM)</option>
        <option value="Weekends" <?= $availability == 'Weekends' ? 'selected' : '' ?>>Weekends</option>
      </select>

      <label>Modes of Consultation</label>
      <div class="checkbox-group">
        <label><input type="checkbox" name="mode[]" value="Phone" <?= in_array('Phone', $modes) ? 'checked' : '' ?>> Audio Call</label>
        <label><input type="checkbox" name="mode[]" value="Google Meet" <?= in_array('Google Meet', $modes) ? 'checked' : '' ?>> Video Call</label>
        <label><input type="checkbox" name="mode[]" value="Offline" <?= in_array('Offline', $modes) ? 'checked' : '' ?>> In-person</label>
      </div>
      <label>Consultation Fees (in USD)</label>
      <input type="number" name="fees" value="<?= htmlspecialchars($therapist['fees']) ?>" required>

      <label>Profile Photo</label>
      
      <div class="img-preview">
        <img src="<?= htmlspecialchars($image) ?>" alt="Profile Picture">
      </div>

      <button type="submit">Save Changes</button>
    </form>
  </div>
</body>
</html>
