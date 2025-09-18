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
            // Store the path with the 'uploads/' prefix
            $imagePath = 'uploads/' . $uniqueName;
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
// Correctly construct the relative path for the image src
$image = !empty($therapist['image']) ? "../" . $therapist['image'] : "../images/default-user.png";
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

    /* Styles for validation */
    .error-message {
      color: var(--error);
      font-size: 0.875rem;
      margin-top: 0.25rem;
      display: none; /* Hidden by default */
    }

    input.invalid, select.invalid, textarea.invalid {
      border-color: var(--error);
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Edit Your Profile</h2>
    <form id="editProfileForm" method="POST" enctype="multipart/form-data" novalidate>

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
        <div id="specializationError" class="error-message"></div>
      </div>

      <div class="input-group">
        <label for="experience" class="required-field">Experience (in years):</label>
        <input type="number" id="experience" name="experience" placeholder="e.g. 5" min="0" max="60" value="<?= htmlspecialchars($therapist['experience']) ?>" required>
        <div id="experienceError" class="error-message"></div>
      </div>

      <div class="input-group">
        <label for="language" class="required-field">Languages Known:</label>
        <input type="text" id="language" name="language" placeholder="e.g. English, Hindi" value="<?= htmlspecialchars($therapist['language']) ?>" required>
        <div id="languageError" class="error-message"></div>
      </div>

      <div class="input-group">
        <label class="required-field">Gender:</label>
        <div class="radio-group">
          <label><input type="radio" name="gender" value="Male" <?= $therapist['gender'] === 'Male' ? 'checked' : '' ?>> Male</label>
          <label><input type="radio" name="gender" value="Female" <?= $therapist['gender'] === 'Female' ? 'checked' : '' ?>> Female</label>
          <label><input type="radio" name="gender" value="Non-binary" <?= $therapist['gender'] === 'Non-binary' ? 'checked' : '' ?>> Non-binary</label>
        </div>
        <div id="genderError" class="error-message"></div>
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
        <div id="availabilityError" class="error-message"></div>
      </div>

      <div class="input-group">
        <label class="required-field">Modes of Consultation:</label>
        <div class="checkbox-group">
          <label><input type="checkbox" name="mode[]" value="Video call" <?= in_array('Video call', $modes) ? 'checked' : '' ?>> Video Call</label>
          <label><input type="checkbox" name="mode[]" value="Audio call" <?= in_array('Audio Call', $modes) ? 'checked' : '' ?>> Audio Call</label>
          <label><input type="checkbox" name="mode[]" value="In-person" <?= in_array('In-person', $modes) ? 'checked' : '' ?>> In-person</label>
        </div>
        <div id="modeError" class="error-message"></div>
      </div>

      <div class="input-group">
        <label for="fees" class="required-field">Consultation Fees (in USD):</label>
        <input type="number" id="fees" name="fees" placeholder="e.g. 50" min="0" step="0.01" value="<?= htmlspecialchars($therapist['fees']) ?>" required>
        <div id="feesError" class="error-message"></div>
      </div>

      <div class="input-group">
        <label for="bio" class="required-field">Bio:</label>
        <textarea id="bio" name="bio" rows="4" placeholder="Write a short bio about yourself..." required><?= htmlspecialchars($bio) ?></textarea>
        <div id="bioError" class="error-message"></div>
      </div>

      <div class="input-group">
        <label>Upload Profile Image:</label>
        <input type="file" id="image" name="image" accept="image/*">
        <div id="imageError" class="error-message"></div>
      </div>

      <div class="img-preview">
        <img src="<?= htmlspecialchars($image) ?>" alt="Profile Picture">
      </div>

      <button type="submit">Save Changes</button>
    </form>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const form = document.getElementById('editProfileForm');
      
      // Input fields
      const specialization = document.getElementById('specialization');
      const experience = document.getElementById('experience');
      const language = document.getElementById('language');
      const genderRadios = document.querySelectorAll('input[name="gender"]');
      const availability = document.getElementById('availability');
      const modeCheckboxes = document.querySelectorAll('input[name="mode[]"]');
      const fees = document.getElementById('fees');
      const bio = document.getElementById('bio');
      const image = document.getElementById('image');

      // Error message elements
      const specializationError = document.getElementById('specializationError');
      const experienceError = document.getElementById('experienceError');
      const languageError = document.getElementById('languageError');
      const genderError = document.getElementById('genderError');
      const availabilityError = document.getElementById('availabilityError');
      const modeError = document.getElementById('modeError');
      const feesError = document.getElementById('feesError');
      const bioError = document.getElementById('bioError');
      const imageError = document.getElementById('imageError');

      const showError = (input, errorElement, message) => {
        input.classList.add('invalid');
        errorElement.textContent = message;
        errorElement.style.display = 'block';
      };

      const hideError = (input, errorElement) => {
        input.classList.remove('invalid');
        errorElement.style.display = 'none';
      };

      const validateSpecialization = () => {
        if (specialization.value.trim() === '') {
          showError(specialization, specializationError, 'Specialization is required.');
          return false;
        }
        hideError(specialization, specializationError);
        return true;
      };

      const validateExperience = () => {
        if (experience.value.trim() === '') {
          showError(experience, experienceError, 'Experience is required.');
          return false;
        }
        if (parseInt(experience.value) < 0 || parseInt(experience.value) > 60) {
          showError(experience, experienceError, 'Experience must be between 0 and 60 years.');
          return false;
        }
        hideError(experience, experienceError);
        return true;
      };

      const validateLanguage = () => {
        const languageRegex = /^[a-zA-Z, ]+$/;
        if (language.value.trim() === '') {
          showError(language, languageError, 'Please enter at least one language.');
          return false;
        }
        if (!languageRegex.test(language.value)) {
            showError(language, languageError, 'Only letters, commas, and spaces are allowed.');
            return false;
        }
        hideError(language, languageError);
        return true;
      };

      const validateGender = () => {
        const checked = document.querySelector('input[name="gender"]:checked');
        if (!checked) {
          showError(genderRadios[0], genderError, 'Please select your gender.');
          return false;
        }
        hideError(genderRadios[0], genderError);
        return true;
      };

      const validateAvailability = () => {
        if (availability.value.trim() === '') {
          showError(availability, availabilityError, 'Availability is required.');
          return false;
        }
        hideError(availability, availabilityError);
        return true;
      };

      const validateMode = () => {
        const checked = Array.from(modeCheckboxes).some(cb => cb.checked);
        if (!checked) {
          showError(modeCheckboxes[0], modeError, 'Please select at least one mode of consultation.');
          return false;
        }
        hideError(modeCheckboxes[0], modeError);
        return true;
      };

      const validateFees = () => {
        if (fees.value.trim() === '') {
          showError(fees, feesError, 'Consultation fee is required.');
          return false;
        }
        if (parseFloat(fees.value) < 0) {
          showError(fees, feesError, 'Fee cannot be negative.');
          return false;
        }
        hideError(fees, feesError);
        return true;
      };

      const validateBio = () => {
        if (bio.value.trim() === '') {
          showError(bio, bioError, 'Bio is required.');
          return false;
        }
        if (bio.value.trim().length < 50) {
          showError(bio, bioError, 'Bio must be at least 50 characters long.');
          return false;
        }
        hideError(bio, bioError);
        return true;
      };

      // Live validation
      specialization.addEventListener('change', validateSpecialization);
      experience.addEventListener('input', validateExperience);
      language.addEventListener('input', validateLanguage);
      genderRadios.forEach(radio => radio.addEventListener('change', validateGender));
      availability.addEventListener('change', validateAvailability);
      modeCheckboxes.forEach(cb => cb.addEventListener('change', validateMode));
      fees.addEventListener('input', validateFees);
      bio.addEventListener('input', validateBio);

      form.addEventListener('submit', (e) => {
        // Run all validations on submit
        const isSpecializationValid = validateSpecialization();
        const isExperienceValid = validateExperience();
        const isLanguageValid = validateLanguage();
        const isGenderValid = validateGender();
        const isAvailabilityValid = validateAvailability();
        const isModeValid = validateMode();
        const isFeesValid = validateFees();
        const isBioValid = validateBio();

        // If any validation fails, prevent form submission
        if (!isSpecializationValid || !isExperienceValid || !isLanguageValid || !isGenderValid || !isAvailabilityValid || !isModeValid || !isFeesValid || !isBioValid) {
          e.preventDefault();
          alert('Please correct the errors before submitting.');
        }
      });
    });
  </script>
</body>
</html>
