<?php
session_start();
include('../connect.php');
include('therapistnav.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Therapist') {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
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
      --success: #28a745;
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
    .input-group { 
      margin-bottom: 1.5rem;
      position: relative;
    }
    label { display: block; font-weight: 500; margin-bottom: 0.5rem; color: var(--secondary); }
    input, select, textarea {
      width: 100%; padding: 0.75rem; border: 1px solid #ced4da;
      border-radius: var(--border-radius); font-size: 1rem;
      background: var(--light);
      transition: border-color 0.3s;
    }
    input:focus, select:focus, textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(22, 97, 56, 0.1);
    }
    input.error, select.error, textarea.error {
      border-color: var(--error);
      box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    }
    input.success, select.success, textarea.success {
      border-color: var(--success);
    }
    .validation-error {
      color: var(--error);
      font-size: 0.85rem;
      margin-top: 0.5rem;
      display: none;
    }
    .radio-group, .checkbox-group {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      margin-top: 0.5rem;
    }
    .radio-group label, .checkbox-group label {
      display: flex;
      align-items: center;
      font-weight: normal;
      cursor: pointer;
    }
    .radio-group input[type="radio"], 
    .checkbox-group input[type="checkbox"] {
      width: auto;
      margin-right: 0.5rem;
    }
    .char-count {
      font-size: 0.8rem;
      color: var(--dark-gray);
      text-align: right;
      margin-top: 0.25rem;
    }
    .char-count.error {
      color: var(--error);
    }
    button {
      background-color: var(--primary);
      color: white;
      padding: 0.75rem 1.5rem;
      border: none; border-radius: var(--border-radius);
      font-size: 1rem; cursor: pointer; width: 100%;
      font-weight: 600; margin-top: 1rem;
      transition: var(--transition);
    }
    button:hover { 
      background-color: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    button:disabled {
      background-color: var(--dark-gray);
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }
  </style>
</head>
<body>

<!-- Navbar -->

<!-- Main Form -->
<div class="main-container">
  <div class="register-container">
    <h2>Therapist Profile Submission</h2>
    <form id="therapistProfileForm" action="dashboard.php" method="POST" enctype="multipart/form-data" novalidate>
      <div class="input-group">
        <label for="specialization">Specialization:</label>
        <select id="specialization" name="specialization" required>
          <option value="" disabled selected>Select specialization</option>
          <option value="Depression">Depression</option>
          <option value="Anxiety">Anxiety</option>
          <option value="Relationship ">Relationship Counseling</option>
          <option value="Family ">Family Therapy</option>
          <option value="Child ">Child & Adolescent Therapy</option>
          <option value="Group">Group Therapy</option>
          <option value="Trauma ">Trauma & PTSD</option>
          <option value="Addiction ">Addiction Counseling</option>
        </select>
        <div class="validation-error" id="specializationError"></div>
      </div>
      
      <div class="input-group">
        <label for="experience">Experience (in years):</label>
        <input type="number" id="experience" name="experience" min="0" max="60" required>
        <div class="validation-error" id="experienceError"></div>
      </div>
      
      <div class="input-group">
        <label for="language">Languages Known:</label>
        <input type="text" id="language" name="language" placeholder="e.g. English, Hindi" required>
        <div class="validation-error" id="languageError"></div>
      </div>
      
      <div class="input-group">
        <label>Gender:</label>
        <div class="radio-group">
          <label><input type="radio" name="gender" value="Male"> Male</label>
          <label><input type="radio" name="gender" value="Female"> Female</label>
          <label><input type="radio" name="gender" value="Non-binary"> Non-binary</label>
        </div>
        <div class="validation-error" id="genderError"></div>
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
        <div class="validation-error" id="availabilityError"></div>
      </div>
      
      <div class="input-group">
        <label>Modes of Consultation:</label>
        <div class="checkbox-group">
          <label><input type="checkbox" name="mode[]" value="Video Call"> Video Call</label>
          <label><input type="checkbox" name="mode[]" value="Audio Call"> Audio Call</label>
          <label><input type="checkbox" name="mode[]" value="In-person"> In-person</label>
        </div>
        <div class="validation-error" id="modeError"></div>
      </div>
      
      <div class="input-group">
        <label for="fees">Consultation Fees (USD):</label>
        <input type="number" id="fees" name="fees" min="0" step="0.01" required>
        <div class="validation-error" id="feesError"></div>
      </div>
      
      <div class="input-group">
        <label for="image">Upload Profile Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required>
        <div class="validation-error" id="imageError"></div>
      </div>
      
      <div class="input-group">
        <label for="bio">Bio:</label>
        <textarea id="bio" name="bio" rows="4" minlength="50" maxlength="500" required></textarea>
        <div class="char-count" id="bioCharCount">0/500 characters (minimum 50 required)</div>
        <div class="validation-error" id="bioError"></div>
      </div>
      
      <button type="submit" id="submitBtn">Complete Profile Submission</button>
    </form>
  </div>
</div>

<script>
  // Form validation
  const form = document.getElementById('therapistProfileForm');
  const specialization = document.getElementById('specialization');
  const experience = document.getElementById('experience');
  const language = document.getElementById('language');
  const genderRadios = document.querySelectorAll('input[name="gender"]');
  const availability = document.getElementById('availability');
  const modeCheckboxes = document.querySelectorAll('input[name="mode[]"]');
  const fees = document.getElementById('fees');
  const image = document.getElementById('image');
  const bio = document.getElementById('bio');
  const submitBtn = document.getElementById('submitBtn');
  
  // Error elements
  const specializationError = document.getElementById('specializationError');
  const experienceError = document.getElementById('experienceError');
  const languageError = document.getElementById('languageError');
  const genderError = document.getElementById('genderError');
  const availabilityError = document.getElementById('availabilityError');
  const modeError = document.getElementById('modeError');
  const feesError = document.getElementById('feesError');
  const imageError = document.getElementById('imageError');
  const bioError = document.getElementById('bioError');
  const bioCharCount = document.getElementById('bioCharCount');
  
  // Character counter for bio
  bio.addEventListener('input', () => {
    const length = bio.value.length;
    bioCharCount.textContent = `${length}/500 characters (minimum 50 required)`;
    
    if (length > 0 && length < 50) {
      bioCharCount.classList.add('error');
    } else {
      bioCharCount.classList.remove('error');
    }
    
    validateBio();
  });
  
  // Validation functions
  const validateSpecialization = () => {
    if (specialization.value === '' || specialization.value === null) {
      specialization.classList.add('error');
      specialization.classList.remove('success');
      specializationError.textContent = 'Please select your specialization';
      specializationError.style.display = 'block';
      return false;
    } else {
      specialization.classList.remove('error');
      specialization.classList.add('success');
      specializationError.style.display = 'none';
      return true;
    }
  };
  
  const validateExperience = () => {
    const experienceValue = experience.value.trim();
    
    if (experienceValue === '') {
      experience.classList.add('error');
      experience.classList.remove('success');
      experienceError.textContent = 'Experience is required';
      experienceError.style.display = 'block';
      return false;
    } else if (experienceValue < 0 || experienceValue > 60) {
      experience.classList.add('error');
      experience.classList.remove('success');
      experienceError.textContent = 'Experience must be between 0 and 60 years';
      experienceError.style.display = 'block';
      return false;
    } else {
      experience.classList.remove('error');
      experience.classList.add('success');
      experienceError.style.display = 'none';
      return true;
    }
  };
  
  const validateLanguage = () => {
    const languageValue = language.value.trim();
    
    if (languageValue === '') {
      language.classList.add('error');
      language.classList.remove('success');
      languageError.textContent = 'Languages known is required';
      languageError.style.display = 'block';
      return false;
    } else if (!/^[a-zA-Z\s,]+$/.test(languageValue)) {
      language.classList.add('error');
      language.classList.remove('success');
      languageError.textContent = 'Languages should only contain letters, spaces, and commas';
      languageError.style.display = 'block';
      return false;
    } else {
      language.classList.remove('error');
      language.classList.add('success');
      languageError.style.display = 'none';
      return true;
    }
  };
  
  const validateGender = () => {
    let isChecked = false;
    for (const radio of genderRadios) {
      if (radio.checked) {
        isChecked = true;
        break;
      }
    }
    
    if (!isChecked) {
      genderError.textContent = 'Please select your gender';
      genderError.style.display = 'block';
      return false;
    } else {
      genderError.style.display = 'none';
      return true;
    }
  };
  
  const validateAvailability = () => {
    if (availability.value === '' || availability.value === null) {
      availability.classList.add('error');
      availability.classList.remove('success');
      availabilityError.textContent = 'Please select your availability';
      availabilityError.style.display = 'block';
      return false;
    } else {
      availability.classList.remove('error');
      availability.classList.add('success');
      availabilityError.style.display = 'none';
      return true;
    }
  };
  
  const validateMode = () => {
    let isChecked = false;
    for (const checkbox of modeCheckboxes) {
      if (checkbox.checked) {
        isChecked = true;
        break;
      }
    }
    
    if (!isChecked) {
      modeError.textContent = 'Please select at least one consultation mode';
      modeError.style.display = 'block';
      return false;
    } else {
      modeError.style.display = 'none';
      return true;
    }
  };
  
  const validateFees = () => {
    const feesValue = fees.value.trim();
    
    if (feesValue === '') {
      fees.classList.add('error');
      fees.classList.remove('success');
      feesError.textContent = 'Consultation fees is required';
      feesError.style.display = 'block';
      return false;
    } else if (feesValue < 0) {
      fees.classList.add('error');
      fees.classList.remove('success');
      feesError.textContent = 'Fees cannot be negative';
      feesError.style.display = 'block';
      return false;
    } else if (feesValue > 1000) {
      fees.classList.add('error');
      fees.classList.remove('success');
      feesError.textContent = 'Fees cannot exceed $1000';
      feesError.style.display = 'block';
      return false;
    } else {
      fees.classList.remove('error');
      fees.classList.add('success');
      feesError.style.display = 'none';
      return true;
    }
  };
  
  const validateImage = () => {
    if (image.value === '') {
      image.classList.add('error');
      image.classList.remove('success');
      imageError.textContent = 'Profile image is required';
      imageError.style.display = 'block';
      return false;
    } else {
      // Check file type
      const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
      if (!allowedExtensions.exec(image.value)) {
        image.classList.add('error');
        image.classList.remove('success');
        imageError.textContent = 'Please upload an image file (JPEG, PNG, GIF)';
        imageError.style.display = 'block';
        return false;
      }
      
      image.classList.remove('error');
      image.classList.add('success');
      imageError.style.display = 'none';
      return true;
    }
  };
  
  const validateBio = () => {
    const bioValue = bio.value.trim();
    
    if (bioValue === '') {
      bio.classList.add('error');
      bio.classList.remove('success');
      bioError.textContent = 'Bio is required';
      bioError.style.display = 'block';
      return false;
    } else if (bioValue.length < 50) {
      bio.classList.add('error');
      bio.classList.remove('success');
      bioError.textContent = 'Bio must be at least 50 characters long';
      bioError.style.display = 'block';
      return false;
    } else if (bioValue.length > 500) {
      bio.classList.add('error');
      bio.classList.remove('success');
      bioError.textContent = 'Bio cannot exceed 500 characters';
      bioError.style.display = 'block';
      return false;
    } else {
      bio.classList.remove('error');
      bio.classList.add('success');
      bioError.style.display = 'none';
      return true;
    }
  };
  
  // Event listeners for real-time validation
  specialization.addEventListener('change', validateSpecialization);
  experience.addEventListener('input', validateExperience);
  experience.addEventListener('blur', validateExperience);
  language.addEventListener('input', validateLanguage);
  language.addEventListener('blur', validateLanguage);
  for (const radio of genderRadios) {
    radio.addEventListener('change', validateGender);
  }
  availability.addEventListener('change', validateAvailability);
  for (const checkbox of modeCheckboxes) {
    checkbox.addEventListener('change', validateMode);
  }
  fees.addEventListener('input', validateFees);
  fees.addEventListener('blur', validateFees);
  image.addEventListener('change', validateImage);
  bio.addEventListener('input', validateBio);
  bio.addEventListener('blur', validateBio);
  
  // Form submission handler
  form.addEventListener('submit', (e) => {
    // Validate all fields
    const isSpecializationValid = validateSpecialization();
    const isExperienceValid = validateExperience();
    const isLanguageValid = validateLanguage();
    const isGenderValid = validateGender();
    const isAvailabilityValid = validateAvailability();
    const isModeValid = validateMode();
    const isFeesValid = validateFees();
    const isImageValid = validateImage();
    const isBioValid = validateBio();
    
    // If any field is invalid, prevent form submission
    if (!isSpecializationValid || !isExperienceValid || !isLanguageValid || 
        !isGenderValid || !isAvailabilityValid || !isModeValid || 
        !isFeesValid || !isImageValid || !isBioValid) {
      e.preventDefault();
      
      // Focus on first error field
      if (!isSpecializationValid) {
        specialization.focus();
      } else if (!isExperienceValid) {
        experience.focus();
      } else if (!isLanguageValid) {
        language.focus();
      } else if (!isGenderValid) {
        genderRadios[0].focus();
      } else if (!isAvailabilityValid) {
        availability.focus();
      } else if (!isFeesValid) {
        fees.focus();
      } else if (!isImageValid) {
        image.focus();
      } else if (!isBioValid) {
        bio.focus();
      }
    }
  });
</script>

</body>
</html>