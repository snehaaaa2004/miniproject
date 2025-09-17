<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Self Help Resources - SerenityConnect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #1e4c3a;
      --primary-dark: #163529;
      --primary-light: #f0f7f4;
      --secondary: #2d5a47;
      --accent: #f8c537;
      --accent-light: #dbeafe;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --text: #1f2937;
      --text-secondary: #6b7280;
      --text-muted: #9ca3af;
      --background: #fafbfc;
      --white: #ffffff;
      --border: #e5e7eb;
      --border-light: #f3f4f6;
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
      --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
      --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
      --radius: 8px;
      --radius-lg: 12px;
      --radius-xl: 16px;
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      --gradient-primary: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      --gradient-accent: linear-gradient(135deg, var(--accent) 0%, #6366f1 100%);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background-color: var(--background);
      color: var(--text);
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    /* NAVBAR */
    header {
      background: var(--primary);
      color: var(--white);
      padding: 1rem 0;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: var(--shadow);
    }

    .navbar {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .logo h1 {
      font-size: 1.75rem;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .logo-icon {
      font-size: 1.8rem;
      color: var(--accent);
    }

    .nav-links {
      display: flex;
      gap: 1.5rem;
    }

    .nav-links a {
      color: var(--white);
      text-decoration: none;
      font-size: 1rem;
      font-weight: 500;
      transition: var(--transition);
      padding: 0.5rem 0;
      position: relative;
    }

    .nav-links a::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 0;
      height: 2px;
      background-color: var(--accent);
      transition: var(--transition);
    }

    .nav-links a:hover::after {
      width: 100%;
    }

    .nav-links a:hover {
      color: var(--accent);
    }


    .mobile-menu-btn {
      display: none;
      background: none;
      border: none;
      color: var(--text);
      font-size: 1.5rem;
      cursor: pointer;
      padding: 0.5rem;
      border-radius: var(--radius);
      transition: var(--transition);
    }

    .mobile-menu-btn:hover {
      background: var(--border-light);
    }

    /* PAGE HEADER */
    .page-header {
      background: var(--gradient-primary);
      color: var(--white);
      padding: 8rem 2rem 4rem;
      text-align: center;
      position: relative;
      overflow: hidden;
      margin-top: 60px;
    }

    .page-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,165.3C384,171,480,149,576,133.3C672,117,768,107,864,122.7C960,139,1056,181,1152,186.7C1248,192,1344,160,1392,144L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') bottom center/cover no-repeat;
    }

    .page-header-content {
      max-width: 800px;
      margin: 0 auto;
      position: relative;
      z-index: 2;
    }

    .page-header h1 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2.5rem, 5vw, 3.5rem);
      font-weight: 600;
      margin-bottom: 1rem;
      letter-spacing: -0.025em;
    }

    .page-header p {
      font-size: 1.25rem;
      opacity: 0.9;
      max-width: 600px;
      margin: 0 auto 2rem;
    }

    .stats-row {
      display: flex;
      justify-content: center;
      gap: 3rem;
      margin-top: 3rem;
      flex-wrap: wrap;
    }

    .stat-item {
      text-align: center;
    }

    .stat-number {
      display: block;
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 0.25rem;
    }

    .stat-label {
      font-size: 0.875rem;
      opacity: 0.8;
    }

    /* CONTAINER */
    .container {
      max-width: 1280px;
      margin: 0 auto;
      padding: 4rem 2rem;
    }

    /* SEARCH AND FILTER */
    .search-filter-section {
      background: var(--white);
      padding: 2rem;
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-sm);
      margin-bottom: 3rem;
      border: 1px solid var(--border);
    }

    .search-container {
      position: relative;
      max-width: 500px;
      margin: 0 auto 2rem;
    }

    .search-input {
      width: 100%;
      padding: 1rem 1rem 1rem 3rem;
      border: 2px solid var(--border);
      border-radius: 50px;
      font-size: 1rem;
      background: var(--background);
      transition: var(--transition);
    }

    .search-input:focus {
      outline: none;
      border-color: var(--accent);
      box-shadow: 0 0 0 3px var(--accent-light);
    }

    .search-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      font-size: 1.1rem;
    }

    .filter-tabs {
      display: flex;
      justify-content: center;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .filter-tab {
      padding: 0.75rem 1.5rem;
      background: var(--background);
      border: 2px solid var(--border);
      border-radius: 50px;
      color: var(--text-secondary);
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition);
      font-size: 0.875rem;
    }

    .filter-tab:hover,
    .filter-tab.active {
      background: var(--accent);
      border-color: var(--accent);
      color: white;
    }

    /* FEATURED SECTION */
    .featured-section {
      margin-bottom: 4rem;
    }

    .section-title {
      font-family: 'Playfair Display', serif;
      font-size: 2.25rem;
      color: var(--primary);
      text-align: center;
      margin-bottom: 3rem;
      position: relative;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 3px;
      background: var(--gradient-accent);
      border-radius: 2px;
    }

    .featured-card {
      background: var(--gradient-primary);
      color: white;
      padding: 3rem;
      border-radius: var(--radius-xl);
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 3rem;
      align-items: center;
      position: relative;
      overflow: hidden;
    }

    .featured-card::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 200px;
      height: 200px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      transform: translate(50%, -50%);
    }

    .featured-content h3 {
      font-family: 'Playfair Display', serif;
      font-size: 2rem;
      margin-bottom: 1rem;
      font-weight: 600;
    }

    .featured-content p {
      font-size: 1.125rem;
      opacity: 0.9;
      margin-bottom: 2rem;
      line-height: 1.7;
    }

    .featured-image {
      width: 100%;
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-xl);
    }

    /* ARTICLE GRID */
    .article-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 2rem;
    }

    .article-card {
      background: var(--white);
      border-radius: var(--radius-xl);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
      transition: var(--transition);
      border: 1px solid var(--border);
      position: relative;
    }

    .article-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-lg);
    }

    .article-image {
      height: 200px;
      width: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .article-card:hover .article-image {
      transform: scale(1.05);
    }

    .article-content {
      padding: 1.5rem;
    }

    .article-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1rem;
    }

    .category-tag {
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      padding: 0.375rem 0.75rem;
      border-radius: 50px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }

    .category-anxiety { background: #fee2e2; color: #dc2626; }
    .category-depression { background: #e0e7ff; color: #4338ca; }
    .category-stress { background: #fef3c7; color: #d97706; }
    .category-mindfulness { background: #d1fae5; color: #059669; }
    .category-relationships { background: #fce7f3; color: #be185d; }
    .category-sleep { background: #e0f2fe; color: #0369a1; }
    .category-self-care { background: #f3e8ff; color: #7c3aed; }
    .category-coping { background: #f0fdf4; color: #16a34a; }

    .bookmark-btn {
      background: none;
      border: none;
      color: var(--text-muted);
      font-size: 1.25rem;
      cursor: pointer;
      transition: var(--transition);
      padding: 0.25rem;
    }

    .bookmark-btn:hover,
    .bookmark-btn.bookmarked {
      color: var(--accent);
    }

    .article-card h3 {
      color: var(--primary);
      margin-bottom: 0.75rem;
      font-size: 1.25rem;
      font-weight: 600;
      line-height: 1.4;
    }

    .article-card p {
      color: var(--text-secondary);
      font-size: 0.95rem;
      margin-bottom: 1.5rem;
      line-height: 1.6;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .article-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1rem;
      font-size: 0.875rem;
      color: var(--text-muted);
    }

    .read-time {
      display: flex;
      align-items: center;
      gap: 0.25rem;
    }

    .difficulty-level {
      display: flex;
      gap: 2px;
    }

    .difficulty-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: var(--border);
    }

    .difficulty-dot.active {
      background: var(--accent);
    }

    .read-more {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      color: var(--accent);
      font-weight: 600;
      text-decoration: none;
      transition: var(--transition);
      padding: 0.5rem 1rem;
      border-radius: var(--radius);
      border: 2px solid transparent;
    }

    .read-more:hover {
      background: var(--accent-light);
      border-color: var(--accent);
      gap: 0.75rem;
    }

    /* TOOLS SECTION */
    .tools-section {
      background: var(--primary-light);
      padding: 4rem 2rem;
      margin: 4rem -2rem 0;
      border-radius: var(--radius-xl);
    }

    .tools-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 2rem;
      max-width: 1000px;
      margin: 3rem auto 0;
    }

    .tool-card {
      background: var(--white);
      padding: 2rem;
      border-radius: var(--radius-lg);
      text-align: center;
      box-shadow: var(--shadow-sm);
      transition: var(--transition);
      border: 1px solid var(--border);
    }

    .tool-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow);
    }

    .tool-icon {
      width: 60px;
      height: 60px;
      background: var(--accent-light);
      border-radius: var(--radius-lg);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
      font-size: 1.5rem;
      color: var(--accent);
    }

    .tool-card h4 {
      font-size: 1.125rem;
      color: var(--primary);
      margin-bottom: 0.75rem;
      font-weight: 600;
    }

    .tool-card p {
      color: var(--text-secondary);
      font-size: 0.9rem;
      margin-bottom: 1.5rem;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.75rem 1.5rem;
      background: var(--gradient-accent);
      color: white;
      text-decoration: none;
      border-radius: 50px;
      font-weight: 600;
      font-size: 0.875rem;
      transition: var(--transition);
      border: none;
      cursor: pointer;
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow);
    }

    .btn-outline {
      background: transparent;
      color: var(--accent);
      border: 2px solid var(--accent);
    }

    .btn-outline:hover {
      background: var(--accent);
      color: white;
    }

    /* NEWSLETTER */
    .newsletter-section {
      background: var(--gradient-primary);
      color: white;
      padding: 3rem 2rem;
      margin: 4rem -2rem 0;
      border-radius: var(--radius-xl);
      text-align: center;
    }

    .newsletter-form {
      max-width: 400px;
      margin: 2rem auto 0;
      display: flex;
      gap: 1rem;
    }

    .newsletter-input {
      flex: 1;
      padding: 0.875rem 1rem;
      border: none;
      border-radius: 50px;
      font-size: 0.95rem;
    }

    .newsletter-input:focus {
      outline: none;
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
    }

    /* FOOTER */
    footer {
      background: var(--primary-dark);
      color: rgba(255, 255, 255, 0.9);
      padding: 3rem 2rem 2rem;
      margin-top: 4rem;
      text-align: center;
    }

    .footer-content {
      max-width: 600px;
      margin: 0 auto;
    }

    .social-links {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin: 2rem 0;
    }

    .social-links a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      color: rgba(255, 255, 255, 0.7);
      transition: var(--transition);
      text-decoration: none;
    }

    .social-links a:hover {
      background: var(--accent);
      color: white;
      transform: translateY(-2px);
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
      .featured-card {
        grid-template-columns: 1fr;
        text-align: center;
      }

      .stats-row {
        gap: 2rem;
      }
    }

    @media (max-width: 768px) {
      .nav-links {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid var(--border);
        box-shadow: var(--shadow-lg);
        flex-direction: column;
        padding: 2rem;
        gap: 1.5rem;
      }

      .nav-links.active {
        display: flex;
      }

      .mobile-menu-btn {
        display: block;
      }

      .container {
        padding: 2rem 1rem;
      }

      .page-header {
        padding: 6rem 1rem 3rem;
      }

      .search-filter-section {
        margin: 0 -1rem 2rem;
        border-radius: 0;
      }

      .filter-tabs {
        gap: 0.5rem;
      }

      .filter-tab {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
      }

      .article-grid {
        grid-template-columns: 1fr;
      }

      .tools-section,
      .newsletter-section {
        margin-left: -1rem;
        margin-right: -1rem;
        border-radius: 0;
      }

      .newsletter-form {
        flex-direction: column;
      }

      .stats-row {
        gap: 1.5rem;
      }

      .stat-number {
        font-size: 1.5rem;
      }
    }

    /* ANIMATIONS */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-on-scroll {
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.6s ease-out;
    }

    .animate-on-scroll.animated {
      opacity: 1;
      transform: translateY(0);
    }
  </style>
</head>
<body>

  
<header>
  <div class="navbar">
    <div class="logo">
      <span class="logo-icon"><i class="fas fa-leaf"></i></span>
      <h1>SerenityConnect</h1>
    </div>

    <!-- Desktop Links -->
    <nav class="nav-links" id="navLinks">
      <a href="index.html">Home</a>
      <a href="login.php">Login</a>
      <a href="register.html">Register</a>
      <a href="about.html">About</a>
      <a href="contactus.php">Contact</a>
      <a href="self_help.php" class="active">Self Help</a>
    </nav>

    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" id="menuBtn">
      <i class="fas fa-bars"></i>
    </button>
  </div>
</header>

 

  <!-- PAGE HEADER -->
  <section class="page-header">
    <div class="page-header-content">
      <h1>Self-Help Resources</h1>
      <p>Evidence-based articles, tools, and resources to support your mental wellness journey</p>
      <div class="stats-row">
        <div class="stat-item">
          <span class="stat-number">150+</span>
          <span class="stat-label">Expert Articles</span>
        </div>
        <div class="stat-item">
          <span class="stat-number">25</span>
          <span class="stat-label">Interactive Tools</span>
        </div>
        <div class="stat-item">
          <span class="stat-number">10k+</span>
          <span class="stat-label">People Helped</span>
        </div>
      </div>
    </div>
  </section>

  <!-- MAIN CONTENT -->
  <div class="container">
    
    <!-- SEARCH AND FILTER -->
    <div class="search-filter-section animate-on-scroll">
      <div class="search-container">
        <i class="fas fa-search search-icon"></i>
        <input type="text" class="search-input" placeholder="Search articles, topics, or keywords..." id="searchInput">
      </div>
      <div class="filter-tabs">
        <button class="filter-tab active" data-category="all">All Topics</button>
        <button class="filter-tab" data-category="anxiety">Anxiety</button>
        <button class="filter-tab" data-category="depression">Depression</button>
        <button class="filter-tab" data-category="stress">Stress</button>
        <button class="filter-tab" data-category="mindfulness">Mindfulness</button>
        <button class="filter-tab" data-category="relationships">Relationships</button>
        <button class="filter-tab" data-category="self-care">Self-Care</button>
      </div>
    </div>

    <!-- FEATURED ARTICLE -->
    <section class="featured-section animate-on-scroll">
      <h2 class="section-title">Featured This Week</h2>
      <div class="featured-card">
        <div class="featured-content">
          <h3>The Complete Guide to Managing Anxiety</h3>
          <p>Discover proven techniques and strategies to understand, manage, and overcome anxiety in your daily life. This comprehensive guide covers everything from breathing exercises to cognitive behavioral techniques.</p>
          <a href="#" class="btn btn-outline">
            <span>Read Full Guide</span>
            <i class="fas fa-arrow-right"></i>
          </a>
        </div>
        <img src="https://images.unsplash.com/photo-1544027993-37dbfe43562a?auto=format&fit=crop&w=600&q=80" alt="Managing Anxiety" class="featured-image">
      </div>
    </section>

    <!-- ARTICLE GRID -->
    <section class="animate-on-scroll">
      <h2 class="section-title">Latest Articles</h2>
      <div class="article-grid" id="articleGrid">
        
        <!-- Anxiety Articles -->
        <div class="article-card" data-category="anxiety">
          <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?auto=format&fit=crop&w=600&q=80" alt="Managing Anxiety" class="article-image">
          <div class="article-content">
            <div class="article-header">
              <span class="category-tag category-anxiety">
                <i class="fas fa-heart-pulse"></i>
                Anxiety
              </span>
              <button class="bookmark-btn" onclick="toggleBookmark(this)">
                <i class="far fa-bookmark"></i>
              </button>
            </div>
            <h3>5 Quick Techniques to Calm Anxiety Attacks</h3>
            <p>Learn immediate, practical strategies you can use anywhere to manage sudden anxiety attacks and regain control of your breathing and thoughts.</p>
            <div class="article-meta">
              <div class="read-time">
                <i class="far fa-clock"></i>
                <span>8 min read</span>
              </div>
              <div class="difficulty-level">
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot"></div>
              </div>
            </div>
            <a href="https://www.helpguide.org/articles/anxiety/social-anxiety-disorder.htm" class="read-more" target="_blank">
              Read Article <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>

        <!-- Depression Articles -->
        <div class="article-card" data-category="depression">
          <img src="https://images.unsplash.com/photo-1499209974431-9dddcece7f88?auto=format&fit=crop&w=600&q=80" alt="Depression Support" class="article-image">
          <div class="article-content">
            <div class="article-header">
              <span class="category-tag category-depression">
                <i class="fas fa-brain"></i>
                Depression
              </span>
              <button class="bookmark-btn" onclick="toggleBookmark(this)">
                <i class="far fa-bookmark"></i>
              </button>
            </div>
            <h3>Understanding Depression: Signs, Symptoms & Hope</h3>
            <p>A comprehensive guide to recognizing depression symptoms and understanding that recovery is possible with the right support and treatment approaches.</p>
            <div class="article-meta">
              <div class="read-time">
                <i class="far fa-clock"></i>
                <span>10 min read</span>
              </div>
              <div class="difficulty-level">
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot"></div>
                <div class="difficulty-dot"></div>
              </div>
            </div>
            <a href="https://www.nimh.nih.gov/health/topics/depression" class="read-more" target="_blank">
              Read Article <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>

        <div class="article-card" data-category="depression">
          <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=600&q=80" alt="Daily Habits" class="article-image">
          <div class="article-content">
            <div class="article-header">
              <span class="category-tag category-depression">
                <i class="fas fa-brain"></i>
                Depression
              </span>
              <button class="bookmark-btn" onclick="toggleBookmark(this)">
                <i class="far fa-bookmark"></i>
              </button>
            </div>
            <h3>10 Daily Habits to Combat Depression</h3>
            <p>Simple, evidence-based daily practices that can help lift your mood, increase energy, and create positive momentum in your recovery journey.</p>
            <div class="article-meta">
              <div class="read-time">
                <i class="far fa-clock"></i>
                <span>7 min read</span>
              </div>
              <div class="difficulty-level">
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot"></div>
              </div>
            </div>
            <a href="https://www.mayoclinic.org/diseases-conditions/depression/in-depth/depression/art-20045943" class="read-more" target="_blank">
              Read Article <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>

        <!-- Stress Management Articles -->
        <div class="article-card" data-category="stress">
          <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=600&q=80" alt="Stress Management" class="article-image">
          <div class="article-content">
            <div class="article-header">
              <span class="category-tag category-stress">
                <i class="fas fa-bolt"></i>
                Stress
              </span>
              <button class="bookmark-btn" onclick="toggleBookmark(this)">
                <i class="far fa-bookmark"></i>
              </button>
            </div>
            <h3>Workplace Stress: Strategies for Better Balance</h3>
            <p>Practical techniques to manage work-related stress, set healthy boundaries, and maintain your well-being in demanding professional environments.</p>
            <div class="article-meta">
              <div class="read-time">
                <i class="far fa-clock"></i>
                <span>9 min read</span>
              </div>
              <div class="difficulty-level">
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot"></div>
              </div>
            </div>
            <a href="https://www.apa.org/topics/healthy-workplaces/work-stress" class="read-more" target="_blank">
              Read Article <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>

        <div class="article-card" data-category="stress">
          <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?auto=format&fit=crop&w=600&q=80" alt="Breathing Exercise" class="article-image">
          <div class="article-content">
            <div class="article-header">
              <span class="category-tag category-stress">
                <i class="fas fa-bolt"></i>
                Stress
              </span>
              <button class="bookmark-btn" onclick="toggleBookmark(this)">
                <i class="far fa-bookmark"></i>
              </button>
            </div>
            <h3>The 4-7-8 Breathing Technique for Instant Calm</h3>
            <p>Master this simple yet powerful breathing exercise that can quickly reduce stress and anxiety in just a few minutes, anywhere you are.</p>
            <div class="article-meta">
              <div class="read-time">
                <i class="far fa-clock"></i>
                <span>4 min read</span>
              </div>
              <div class="difficulty-level">
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot"></div>
                <div class="difficulty-dot"></div>
              </div>
            </div>
            <a href="https://www.healthline.com/health/4-7-8-breathing" class="read-more" target="_blank">
              Read Article <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>

        <!-- Mindfulness Articles -->
        <div class="article-card" data-category="mindfulness">
          <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=600&q=80" alt="Meditation" class="article-image">
          <div class="article-content">
            <div class="article-header">
              <span class="category-tag category-mindfulness">
                <i class="fas fa-leaf"></i>
                Mindfulness
              </span>
              <button class="bookmark-btn" onclick="toggleBookmark(this)">
                <i class="far fa-bookmark"></i>
              </button>
            </div>
            <h3>Beginner's Guide to Mindfulness Meditation</h3>
            <p>Start your mindfulness journey with simple, guided practices that require no special equipment or experience. Learn to find calm in everyday moments.</p>
            <div class="article-meta">
              <div class="read-time">
                <i class="far fa-clock"></i>
                <span>12 min read</span>
              </div>
              <div class="difficulty-level">
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot"></div>
                <div class="difficulty-dot"></div>
              </div>
            </div>
            <a href="https://www.mindful.org/how-to-meditate/" class="read-more" target="_blank">
              Read Article <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>

        <div class="article-card" data-category="mindfulness">
          <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?auto=format&fit=crop&w=600&q=80" alt="Present Moment" class="article-image">
          <div class="article-content">
            <div class="article-header">
              <span class="category-tag category-mindfulness">
                <i class="fas fa-leaf"></i>
                Mindfulness
              </span>
              <button class="bookmark-btn" onclick="toggleBookmark(this)">
                <i class="far fa-bookmark"></i>
              </button>
            </div>
            <h3>5-Minute Daily Mindfulness Practices</h3>
            <p>Quick mindfulness exercises you can do during your morning routine, lunch break, or before bed to stay grounded and present throughout your day.</p>
            <div class="article-meta">
              <div class="read-time">
                <i class="far fa-clock"></i>
                <span>6 min read</span>
              </div>
              <div class="difficulty-level">
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot"></div>
                <div class="difficulty-dot"></div>
              </div>
            </div>
            <a href="https://www.headspace.com/mindfulness" class="read-more" target="_blank">
              Read Article <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>

        <!-- Relationships Articles -->
        <div class="article-card" data-category="relationships">
          <img src="https://images.unsplash.com/photo-1516589178581-6cd7833ae3b2?auto=format&fit=crop&w=600&q=80" alt="Communication" class="article-image">
          <div class="article-content">
            <div class="article-header">
              <span class="category-tag category-relationships">
                <i class="fas fa-heart"></i>
                Relationships
              </span>
              <button class="bookmark-btn" onclick="toggleBookmark(this)">
                <i class="far fa-bookmark"></i>
              </button>
            </div>
            <h3>Healthy Communication in Relationships</h3>
            <p>Learn essential communication skills that strengthen relationships, resolve conflicts peacefully, and create deeper emotional connections with loved ones.</p>
            <div class="article-meta">
              <div class="read-time">
                <i class="far fa-clock"></i>
                <span>11 min read</span>
              </div>
              <div class="difficulty-level">
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot"></div>
              </div>
            </div>
            <a href="https://www.gottman.com/about/research/" class="read-more" target="_blank">
              Read Article <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>

        <div class="article-card" data-category="relationships">
          <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&q=80" alt="Setting Boundaries" class="article-image">
          <div class="article-content">
            <div class="article-header">
              <span class="category-tag category-relationships">
                <i class="fas fa-heart"></i>
                Relationships
              </span>
              <button class="bookmark-btn" onclick="toggleBookmark(this)">
                <i class="far fa-bookmark"></i>
              </button>
            </div>
            <h3>Setting Healthy Boundaries: A Complete Guide</h3>
            <p>Understand why boundaries are crucial for mental health and learn how to set and maintain them in personal relationships, work, and family dynamics.</p>
            <div class="article-meta">
              <div class="read-time">
                <i class="far fa-clock"></i>
                <span>13 min read</span>
              </div>
              <div class="difficulty-level">
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot active"></div>
              </div>
            </div>
            <a href="https://www.psychologytoday.com/us/basics/boundaries" class="read-more" target="_blank">
              Read Article <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>

        <!-- Self-Care Articles -->
        <div class="article-card" data-category="self-care">
          <img src="https://images.unsplash.com/photo-1515377905703-c4788e51af15?auto=format&fit=crop&w=600&q=80" alt="Self Care" class="article-image">
          <div class="article-content">
            <div class="article-header">
              <span class="category-tag category-self-care">
                <i class="fas fa-spa"></i>
                Self-Care
              </span>
              <button class="bookmark-btn" onclick="toggleBookmark(this)">
                <i class="far fa-bookmark"></i>
              </button>
            </div>
            <h3>Creating a Sustainable Self-Care Routine</h3>
            <p>Build a personalized self-care practice that fits your lifestyle and actually works. Discover activities that nurture your physical, emotional, and mental well-being.</p>
            <div class="article-meta">
              <div class="read-time">
                <i class="far fa-clock"></i>
                <span>8 min read</span>
              </div>
              <div class="difficulty-level">
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot"></div>
                <div class="difficulty-dot"></div>
              </div>
            </div>
            <a href="https://www.nami.org/Blogs/NAMI-Blog/December-2016/The-Importance-of-Self-Care" class="read-more" target="_blank">
              Read Article <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>

        <!-- Sleep Articles -->
        <div class="article-card" data-category="sleep">
          <img src="https://images.unsplash.com/photo-1541781774459-bb2af2f05b55?auto=format&fit=crop&w=600&q=80" alt="Sleep Hygiene" class="article-image">
          <div class="article-content">
            <div class="article-header">
              <span class="category-tag category-sleep">
                <i class="fas fa-moon"></i>
                Sleep
              </span>
              <button class="bookmark-btn" onclick="toggleBookmark(this)">
                <i class="far fa-bookmark"></i>
              </button>
            </div>
            <h3>Sleep Hygiene: Your Guide to Better Rest</h3>
            <p>Improve your sleep quality with proven strategies for creating the ideal sleep environment and developing healthy bedtime routines for restorative rest.</p>
            <div class="article-meta">
              <div class="read-time">
                <i class="far fa-clock"></i>
                <span>9 min read</span>
              </div>
              <div class="difficulty-level">
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot active"></div>
                <div class="difficulty-dot"></div>
              </div>
            </div>
            <a href="https://www.sleepfoundation.org/sleep-hygiene" class="read-more" target="_blank">
              Read Article <i class="fas fa-arrow-right"></i>
            </a>
          </div>
        </div>

      </div>
    </section>

    <!-- TOOLS SECTION -->
    

    <!-- NEWSLETTER SECTION -->
    <section class="newsletter-section animate-on-scroll">
      <h3>Stay Updated with Weekly Mental Health Tips</h3>
      <p>Get expert advice, new articles, and exclusive resources delivered to your inbox every week.</p>
      <form class="newsletter-form" onsubmit="subscribeNewsletter(event)">
        <input type="email" class="newsletter-input" placeholder="Enter your email address" required>
        <button type="submit" class="btn">
          Subscribe <i class="fas fa-paper-plane"></i>
        </button>
      </form>
    </section>

  </div>

  <!-- FOOTER -->
  <footer>
    <div class="footer-content">
      <h4>SerenityConnect</h4>
      <p>Empowering your journey to better mental health with expert resources and compassionate support.</p>
      <div class="social-links">
        <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
      </div>
      <p>&copy; 2025 SerenityConnect. All rights reserved. | <a href="#" style="color: rgba(255,255,255,0.7);">Privacy Policy</a> | <a href="#" style="color: rgba(255,255,255,0.7);">Terms of Service</a></p>
    </div>
  </footer>

  <script>
    // Mobile menu toggle
    const menuBtn = document.getElementById('menuBtn');
    const navLinks = document.getElementById('navLinks');
    
    menuBtn.addEventListener('click', () => {
      navLinks.classList.toggle('active');
      menuBtn.innerHTML = navLinks.classList.contains('active') ? 
        '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
    });

    // Filter functionality
    const filterTabs = document.querySelectorAll('.filter-tab');
    const articles = document.querySelectorAll('.article-card');
    
    filterTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        // Remove active class from all tabs
        filterTabs.forEach(t => t.classList.remove('active'));
        // Add active class to clicked tab
        tab.classList.add('active');
        
        const category = tab.dataset.category;
        
        // Filter articles
        articles.forEach(article => {
          if (category === 'all' || article.dataset.category === category) {
            article.style.display = 'block';
            setTimeout(() => {
              article.style.opacity = '1';
              article.style.transform = 'translateY(0)';
            }, 100);
          } else {
            article.style.opacity = '0';
            article.style.transform = 'translateY(20px)';
            setTimeout(() => {
              article.style.display = 'none';
            }, 300);
          }
        });
      });
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', (e) => {
      const searchTerm = e.target.value.toLowerCase();
      
      articles.forEach(article => {
        const title = article.querySelector('h3').textContent.toLowerCase();
        const content = article.querySelector('p').textContent.toLowerCase();
        const category = article.querySelector('.category-tag').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || content.includes(searchTerm) || category.includes(searchTerm)) {
          article.style.display = 'block';
          article.style.opacity = '1';
          article.style.transform = 'translateY(0)';
        } else {
          article.style.opacity = '0';
          article.style.transform = 'translateY(20px)';
          setTimeout(() => {
            article.style.display = 'none';
          }, 300);
        }
      });
    });

    // Bookmark functionality
    function toggleBookmark(button) {
      const icon = button.querySelector('i');
      if (icon.classList.contains('far')) {
        icon.classList.remove('far');
        icon.classList.add('fas');
        button.classList.add('bookmarked');
        showToast('Article bookmarked!');
      } else {
        icon.classList.remove('fas');
        icon.classList.add('far');
        button.classList.remove('bookmarked');
        showToast('Bookmark removed');
      }
    }

    // Tool functions
    function openBreathingTool() {
      showToast('Opening breathing exercise tool...');
      // In a real application, this would open a breathing exercise interface
    }

    function openMoodTracker() {
      showToast('Opening mood tracker...');
      // In a real application, this would open a mood tracking interface
    }

    function openGratitudeJournal() {
      showToast('Opening gratitude journal...');
      // In a real application, this would open a journaling interface
    }

    function openThoughtChallenger() {
      showToast('Opening thought challenger...');
      // In a real application, this would open a CBT exercise interface
    }

    // Newsletter subscription
    function subscribeNewsletter(event) {
      event.preventDefault();
      const email = event.target.querySelector('input').value;
      showToast('Thank you for subscribing! You\'ll receive our weekly mental health tips.');
      event.target.reset();
    }

    // Toast notification
    function showToast(message) {
      const toast = document.createElement('div');
      toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--primary);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
        z-index: 1000;
        transition: var(--transition);
        transform: translateX(100%);
      `;
      toast.textContent = message;
      document.body.appendChild(toast);
      
      setTimeout(() => {
        toast.style.transform = 'translateX(0)';
      }, 100);
      
      setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
          document.body.removeChild(toast);
        }, 300);
      }, 3000);
    }

    // Scroll animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animated');
        }
      });
    }, observerOptions);

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
      observer.observe(el);
    });

    // Header scroll effect
    const header = document.querySelector('header');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 100) {
        header.style.boxShadow = 'var(--shadow)';
      } else {
        header.style.boxShadow = 'none';
      }
    });
  </script>

</body>
</html>