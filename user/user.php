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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #1e3a2e;
      --primary-medium: #2e5543;
      --primary-light: #3d7058;
      --accent: #e8b84d;
      --accent-light: #f4d186;
      --success: #4ade80;
      --text-primary: #0f172a;
      --text-secondary: #64748b;
      --text-muted: #94a3b8;
      --background: #fefefe;
      --background-alt: #f8fafc;
      --surface: #ffffff;
      --surface-elevated: #ffffff;
      --border: #e2e8f0;
      --border-light: #f1f5f9;
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      --gradient-primary: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      --gradient-hero: linear-gradient(135deg, #1e3a2e 0%, #2e5543 50%, #3d7058 100%);
      --radius: 12px;
      --radius-lg: 16px;
      --radius-xl: 20px;
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      --transition-bounce: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: var(--background);
      color: var(--text-primary);
      line-height: 1.6;
      overflow-x: hidden;
    }

    /* Background Pattern */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: 
        radial-gradient(circle at 20% 80%, rgba(30, 58, 46, 0.03) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(232, 184, 77, 0.03) 0%, transparent 50%);
      pointer-events: none;
      z-index: -1;
    }

    .main-container {
      min-height: 100vh;
      padding: 2rem 1rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 3rem;
    }

    /* Header Section */
    .hero-header {
      background: var(--gradient-hero);
      color: white;
      text-align: center;
      padding: 4rem 2rem;
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-xl);
      position: relative;
      overflow: hidden;
      width: 100%;
      max-width: 900px;
    }

    .hero-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -30%;
      width: 100%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
      transform: rotate(15deg);
    }

    .hero-header::after {
      content: '';
      position: absolute;
      bottom: -20px;
      left: -20px;
      width: 120px;
      height: 120px;
      background: rgba(232, 184, 77, 0.2);
      border-radius: 50%;
      blur: 40px;
    }

    .hero-title {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2rem, 5vw, 3rem);
      font-weight: 600;
      margin-bottom: 1rem;
      position: relative;
      z-index: 2;
    }

    .hero-subtitle {
      font-size: 1.2rem;
      opacity: 0.9;
      max-width: 600px;
      margin: 0 auto;
      position: relative;
      z-index: 2;
    }

    .hero-icon {
      position: absolute;
      top: 2rem;
      right: 2rem;
      font-size: 3rem;
      opacity: 0.3;
    }

    /* Form Container */
    .form-container {
      max-width: 700px;
      width: 100%;
      background: var(--surface-elevated);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-lg);
      border: 1px solid var(--border-light);
      overflow: hidden;
      position: relative;
    }

    .form-header {
      background: linear-gradient(135deg, var(--background-alt) 0%, var(--surface) 100%);
      padding: 2.5rem 2.5rem 2rem;
      text-align: center;
      position: relative;
    }

    .form-title {
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      color: var(--primary);
      margin-bottom: 0.5rem;
      position: relative;
    }

    .form-subtitle {
      color: var(--text-secondary);
      font-size: 1rem;
      max-width: 400px;
      margin: 0 auto;
    }

    .decorative-line {
      width: 80px;
      height: 3px;
      background: linear-gradient(90deg, var(--accent), var(--accent-light));
      margin: 1.5rem auto 0;
      border-radius: 2px;
    }

    /* Form Content */
    .form-content {
      padding: 2.5rem;
    }

    .form-grid {
      display: grid;
      gap: 2rem;
    }

    .form-group {
      position: relative;
    }

    .form-label {
      display: block;
      margin-bottom: 0.75rem;
      font-weight: 500;
      color: var(--primary);
      font-size: 0.95rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .form-label i {
      color: var(--accent);
      width: 16px;
      text-align: center;
    }

    .form-input,
    .form-select {
      width: 100%;
      padding: 1rem 1.25rem;
      padding-right: 3rem;
      border: 2px solid var(--border);
      border-radius: var(--radius);
      font-size: 1rem;
      background: var(--surface);
      transition: var(--transition);
      font-family: inherit;
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      cursor: pointer;
    }

    .form-input:focus,
    .form-select:focus {
      border-color: var(--primary-light);
      outline: none;
      box-shadow: 0 0 0 3px rgba(30, 58, 46, 0.1);
      transform: translateY(-1px);
    }

    .form-input::placeholder {
      color: var(--text-muted);
    }

    .select-wrapper {
      position: relative;
    }

    .select-wrapper::after {
      content: '\f078';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      position: absolute;
      right: 1.25rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      pointer-events: none;
      transition: var(--transition);
    }

    .select-wrapper:hover::after {
      color: var(--primary-light);
    }

    /* Enhanced Button */
    .btn-primary {
      width: 100%;
      padding: 1.25rem 2rem;
      background: var(--gradient-primary);
      color: white;
      border: none;
      border-radius: var(--radius);
      font-size: 1.1rem;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition-bounce);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      margin-top: 1.5rem;
      position: relative;
      overflow: hidden;
    }

    .btn-primary::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-xl);
      filter: brightness(1.1);
    }

    .btn-primary:hover::before {
      left: 100%;
    }

    .btn-primary:active {
      transform: translateY(0);
    }

    /* Quick Filters */
    .quick-filters {
      margin-top: 1.5rem;
      padding-top: 1.5rem;
      border-top: 1px solid var(--border-light);
    }

    .quick-filters-title {
      font-size: 0.9rem;
      font-weight: 500;
      color: var(--text-secondary);
      margin-bottom: 1rem;
      text-align: center;
    }

    .filter-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 0.75rem;
      justify-content: center;
    }

    .filter-tag {
      padding: 0.6rem 1.2rem;
      background: var(--background-alt);
      color: var(--text-secondary);
      border: 1px solid var(--border);
      border-radius: 50px;
      font-size: 0.85rem;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition-bounce);
      user-select: none;
    }

    .filter-tag:hover {
      background: var(--primary-light);
      color: white;
      border-color: var(--primary-light);
      transform: translateY(-1px);
    }

    .filter-tag.active {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
      box-shadow: var(--shadow);
    }

    /* Responsive Grid */
    @media (min-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr 1fr;
        gap: 2rem 1.5rem;
      }

      .form-group.full-width {
        grid-column: span 2;
      }
    }

    /* Mobile Optimizations */
    @media (max-width: 768px) {
      .main-container {
        padding: 1rem;
        gap: 2rem;
      }

      .hero-header {
        padding: 3rem 1.5rem;
      }

      .form-content {
        padding: 1.5rem;
      }

      .form-header {
        padding: 2rem 1.5rem 1.5rem;
      }

      .filter-tags {
        gap: 0.5rem;
      }

      .filter-tag {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
      }
    }

    /* Loading Animation */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-in {
      animation: fadeInUp 0.6s ease-out forwards;
    }

    /* Floating Elements */
    .floating-icon {
      position: absolute;
      color: rgba(232, 184, 77, 0.2);
      animation: float 6s ease-in-out infinite;
    }

    .floating-icon:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; }
    .floating-icon:nth-child(2) { top: 20%; right: 15%; animation-delay: 2s; }
    .floating-icon:nth-child(3) { bottom: 30%; left: 20%; animation-delay: 4s; }

    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
    }

    /* Focus Indicators */
    .form-group:focus-within .form-label {
      color: var(--primary-light);
    }

    .form-group:focus-within .form-label i {
      color: var(--accent);
      transform: scale(1.1);
    }

    /* Success States */
    .form-input:valid {
      border-color: var(--success);
    }

    .form-select:valid {
      border-color: var(--success);
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>
  <div class="main-container">
    <!-- Hero Header -->
    <header class="hero-header animate-in">
      <i class="fas fa-heart hero-icon"></i>
      <div class="floating-icon"><i class="fas fa-brain fa-2x"></i></div>
      <div class="floating-icon"><i class="fas fa-leaf fa-lg"></i></div>
      <div class="floating-icon"><i class="fas fa-dove fa-lg"></i></div>
      
      <h1 class="hero-title">Find Your Perfect Therapist Match</h1>
      <p class="hero-subtitle">Discover mental health professionals tailored to your unique needs and preferences</p>
    </header>

    <!-- Form Container -->
    <div class="form-container animate-in">
      <div class="form-header">
        <h2 class="form-title">Personalize Your Search</h2>
        <p class="form-subtitle">Tell us what you're looking for, and we'll help you find the right therapist</p>
        <div class="decorative-line"></div>
      </div>

      <div class="form-content">
        <form method="POST" action="filter_result.php">
          <div class="form-grid">
            <div class="form-group">
              <label for="specialization" class="form-label">
                <i class="fas fa-stethoscope"></i>
                Specialization
              </label>
              <div class="select-wrapper">
                <select id="specialization" name="specialization" class="form-select">
                  <option value="">All Specializations</option>
                  <option value="Depression">Depression</option>
                  <option value="Anxiety">Anxiety</option>
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
              <label for="gender" class="form-label">
                <i class="fas fa-user"></i>
                Preferred Gender
              </label>
              <div class="select-wrapper">
                <select id="gender" name="gender" class="form-select">
                  <option value="">No Preference</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Non-binary">Non-binary</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="language" class="form-label">
                <i class="fas fa-language"></i>
                Language Preference
              </label>
              <input type="text" id="language" name="language" class="form-input" 
                     placeholder="English, Hindi, Spanish...">
            </div>

            <div class="form-group">
              <label for="mode" class="form-label">
                <i class="fas fa-video"></i>
                Session Type
              </label>
              <div class="select-wrapper">
                <select id="mode" name="mode" class="form-select">
                  <option value="">Any Session Type</option>
                  <option value="Google meet">Video Call</option>
                  <option value="Phone">Audio Call</option>
                  <option value="Offline">In-Person</option>
                </select>
              </div>
            </div>

            <div class="form-group full-width">
              <label for="availability" class="form-label">
                <i class="fas fa-clock"></i>
                Preferred Availability
              </label>
              <div class="select-wrapper">
                <select id="availability" name="availability" class="form-select">
                  <option value="">Any Time</option>
                  <option value="Mornings (8AM-12PM)">Mornings (8AM-12PM)</option>
                  <option value="Afternoons (12PM-5PM)">Afternoons (12PM-5PM)</option>
                  <option value="Evenings (5PM-9PM)">Evenings (5PM-9PM)</option>
                  <option value="Weekends">Weekends</option>
                </select>
              </div>
            </div>
          </div>

          

          <button type="submit" class="btn-primary">
            <i class="fas fa-search"></i>
            <span>Find My Perfect Match</span>
          </button>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Enhanced animations
    document.addEventListener('DOMContentLoaded', () => {
      // Stagger animation for form elements
      const formGroups = document.querySelectorAll('.form-group');
      formGroups.forEach((group, index) => {
        group.style.opacity = '0';
        group.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
          group.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
          group.style.opacity = '1';
          group.style.transform = 'translateY(0)';
        }, 100 * index);
      });
    });

    // Enhanced tag functionality
    const filterTags = document.querySelectorAll('.filter-tag');
    const selectedTags = new Set();

    filterTags.forEach(tag => {
      tag.addEventListener('click', () => {
        const value = tag.dataset.value;
        
        if (tag.classList.contains('active')) {
          tag.classList.remove('active');
          selectedTags.delete(value);
        } else {
          tag.classList.add('active');
          selectedTags.add(value);
        }

        // Create a hidden input for the selected tags
        let hiddenInput = document.getElementById('selected-tags');
        if (!hiddenInput) {
          hiddenInput = document.createElement('input');
          hiddenInput.type = 'hidden';
          hiddenInput.name = 'tags';
          hiddenInput.id = 'selected-tags';
          document.querySelector('form').appendChild(hiddenInput);
        }
        hiddenInput.value = Array.from(selectedTags).join(',');
      });
    });

    // Form validation enhancement
    const formInputs = document.querySelectorAll('.form-input, .form-select');
    formInputs.forEach(input => {
      input.addEventListener('change', () => {
        if (input.value) {
          input.style.borderColor = 'var(--success)';
        } else {
          input.style.borderColor = 'var(--border)';
        }
      });
    });

    // Button loading state
    const form = document.querySelector('form');
    const submitBtn = document.querySelector('.btn-primary');
    
    form.addEventListener('submit', () => {
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Searching...</span>';
      submitBtn.style.pointerEvents = 'none';
    });

    // Prevent caching
    window.onpageshow = function(event) {
      if (event.persisted) {
        window.location.reload();
      }
    };

    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }
  </script>
</body>
</html>