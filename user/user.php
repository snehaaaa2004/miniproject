<?php

    session_start();


// Prevent caching to disable back button access

include('../auth_check.php');



$name = $_SESSION['name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Find Your Therapist - SerenityConnect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #2e4d3d;
      --primary-dark: #1e3b2b;
      --primary-light: #eaf4ed;
      --secondary: #3a5e4f;
      --accent: #f8c537;
      --text: #333333;
      --text-light: #666666;
      --background: #f8f6f3;
      --white: #ffffff;
      --border: #d9e0d9;
      --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      --shadow-hover: 0 8px 20px rgba(0, 0, 0, 0.12);
      --radius: 8px;
      --radius-lg: 12px;
      --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', 'Georgia', serif;
      background-color: var(--background);
      color: var(--text);
      padding: 2rem 1rem;
      line-height: 1.6;
    }

    header {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: var(--white);
      text-align: center;
      padding: 2.5rem 1rem;
      border-radius: var(--radius-lg);
      margin-bottom: 2.5rem;
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
    }

    header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(206, 193, 193, 0.1) 0%, rgba(255,255,255,0) 70%);
      transform: rotate(30deg);
    }

    .container {
      max-width: 640px;
      background: var(--white);
      padding: 2.5rem;
      margin: 0 auto;
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      border: 1px solid var(--border);
    }

    h1 {
      text-align: center;
      color: var(--primary);
      margin-bottom: 1.5rem;
      font-size: 1.8rem;
      position: relative;
    }

    h1::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 3px;
      background-color: var(--accent);
    }

    .filter-intro {
      text-align: center;
      color: var(--text-light);
      margin-bottom: 2rem;
      font-size: 1rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--primary);
      font-size: 0.95rem;
    }

    select,
    input[type="text"],
    input[type="number"] {
      width: 100%;
      padding: 0.9rem 1rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      font-size: 1rem;
      background: var(--white);
      transition: var(--transition);
    }

    select:focus,
    input[type="text"]:focus,
    input[type="number"]:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 3px rgba(46, 77, 61, 0.1);
    }

    .select-wrapper {
      position: relative;
    }

    .select-wrapper::after {
      content: '\f078';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-light);
      pointer-events: none;
    }

    .btn {
      width: 100%;
      padding: 1rem;
      background-color: var(--primary);
      color: var(--white);
      border: none;
      border-radius: var(--radius);
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition);
      margin-top: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .btn:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: var(--shadow-hover);
    }

    .filter-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
      margin-bottom: 1.5rem;
    }

    .tag {
      padding: 0.5rem 1rem;
      background-color: var(--primary-light);
      color: var(--primary);
      border-radius: 50px;
      font-size: 0.8rem;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition);
    }

    .tag:hover {
      background-color: var(--primary);
      color: var(--white);
    }

    .tag.active {
      background-color: var(--primary);
      color: var(--white);
    }

    @media (max-width: 768px) {
      .container {
        padding: 1.5rem;
      }
      
      h1 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>

  <?php include 'navbar.php'; ?>

  <header>
    <h1 >Find Your Perfect Therapist Match</h1>
    <p>Filter by your preferences to find the right mental health professional for you</p>
  </header>

  <div class="container">
    <h1>Refine Your Search</h1>
    <p class="filter-intro">Select your preferences to find therapists that match your needs</p>

    <form method="POST" action="filter_result.php">
      <div class="form-group">
        <label for="specialization">Specialization</label>
        <div class="select-wrapper">
          <select id="specialization" name="specialization">
            <option value="">All Specializations</option>
            <option value="Depression">Depression</option>
            <option value="Anxiety">Anxiety </option>
            <option value="Relationship">Relationship Counseling</option>
            <option value="Family">Family Therapy</option>
            <option value="Child">Child & Adolescent Therapy</option>
            <option value="Group">Group Therapy</option>
            <option value="Trauma">Trauma & PTSD</option>
            <option value="Addiction">Addiction Counseling</option>
          </select>
        </div>
      </div>

      

      <div class="form-group">
        <label for="gender">Preferred Gender</label>
        <div class="select-wrapper">
          <select id="gender" name="gender">
            <option value="">No Preference</option>
            <option value="Male">Male </option>
            <option value="Female">Female </option>
            <option value="Non-binary">Non-binary </option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="language">Language</label>
        <input type="text" id="language" name="language" placeholder="English, Hindi, etc.">
      </div>

      <div class="form-group">
        <label for="mode">Session Type</label>
        <div class="select-wrapper">
          <select id="mode" name="mode">
            <option value="">Any Session Type</option>
            <option value="Google meet">Video Call</option>
            <option value="Phone">Audio Call</option>
            <option value="Offline">In-Person</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="availability">Availability</label>
        <div class="select-wrapper">
          <select id="availability" name="availability">
            <option value="">Any Time</option>
            <option value="morning">Mornings (8AM-12PM)</option>
            <option value="afternoon">Afternoons (12PM-5PM)</option>
            <option value="evening">Evenings (5PM-9PM)</option>
            <option value="weekend">Weekends</option>
          </select>
        </div>
      </div>

      <button type="submit" class="btn">
        <i class="fas fa-search"></i> Find Therapists
      </button>
    </form>
  </div>

  <script>
    // Tag selection functionality
    const tags = document.querySelectorAll('.tag');
    const approachInput = document.getElementById('approach');
    
    tags.forEach(tag => {
      tag.addEventListener('click', () => {
        tag.classList.toggle('active');
        updateSelectedApproaches();
      });
    });

    function updateSelectedApproaches() {
      const selectedTags = Array.from(document.querySelectorAll('.tag.active'))
        .map(tag => tag.dataset.value)
        .join(',');
      approachInput.value = selectedTags;
    }

    // Simple animation on load
    document.addEventListener('DOMContentLoaded', () => {
      const container = document.querySelector('.container');
      container.style.opacity = '0';
      container.style.transform = 'translateY(20px)';
      
      setTimeout(() => {
        container.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        container.style.opacity = '1';
        container.style.transform = 'translateY(0)';
      }, 100);
    });
  </script>
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