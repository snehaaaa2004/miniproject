<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Self Help Resources - SerenityConnect</title>
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
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', 'Georgia', serif;
      background-color: var(--background);
      color: var(--text);
      line-height: 1.6;
    }

    header {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: var(--white);
      padding: 3rem 1rem;
      text-align: center;
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
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
      transform: rotate(30deg);
    }

    header h1 {
      font-size: 2.2rem;
      margin-bottom: 0.5rem;
      position: relative;
    }

    header p {
      font-size: 1.1rem;
      opacity: 0.9;
      max-width: 600px;
      margin: 0 auto;
    }

    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin: 1.5rem;
      padding: 0.75rem 1.5rem;
      background: var(--primary);
      color: var(--white);
      border-radius: var(--radius);
      text-decoration: none;
      font-weight: 500;
      transition: var(--transition);
    }

    .back-btn:hover {
      background: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: var(--shadow-hover);
    }

    .container {
      max-width: 1200px;
      margin: 3rem auto;
      padding: 0 1.5rem;
    }

    .page-title {
      text-align: center;
      color: var(--primary);
      margin-bottom: 2rem;
      position: relative;
    }

    .page-title::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 3px;
      background-color: var(--accent);
    }

    .article-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
    }

    .article-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow);
      transition: var(--transition);
      border: 1px solid var(--border);
    }

    .article-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-hover);
    }

    .article-image {
      height: 180px;
      width: 100%;
      object-fit: cover;
    }

    .article-content {
      padding: 1.5rem;
    }

    .article-card h3 {
      color: var(--primary);
      margin-bottom: 0.75rem;
      font-size: 1.25rem;
    }

    .article-card p {
      color: var(--text-light);
      font-size: 0.95rem;
      margin-bottom: 1.25rem;
      line-height: 1.6;
    }

    .article-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1rem;
      font-size: 0.85rem;
      color: var(--text-light);
    }

    .read-more {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      color: var(--primary);
      font-weight: 500;
      text-decoration: none;
      transition: var(--transition);
    }

    .read-more:hover {
      color: var(--primary-dark);
      gap: 0.75rem;
    }

    .category-tag {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      background: var(--primary-light);
      color: var(--primary);
      border-radius: 50px;
      font-size: 0.75rem;
      font-weight: 500;
    }

    footer {
      background-color: var(--primary-dark);
      color: var(--white);
      text-align: center;
      padding: 2rem;
      margin-top: 4rem;
    }

    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
    }

    .social-links {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin: 1.5rem 0;
    }

    .social-links a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: rgba(255, 255, 255, 0.1);
      color: var(--white);
      transition: var(--transition);
    }

    .social-links a:hover {
      background-color: var(--accent);
      color: var(--primary);
      transform: translateY(-3px);
    }

    @media (max-width: 768px) {
      header h1 {
        font-size: 1.8rem;
      }
      
      header p {
        font-size: 1rem;
      }
      
      .article-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <header>
    <h1>Self-Help Resources</h1>
    <p>Expert-curated articles to support your mental well-being journey</p>
  </header>

  <a href="javascript:history.back()" class="back-btn">
    <i class="fas fa-arrow-left"></i> Back
  </a>

  <div class="container">
    <h2 class="page-title">Explore Our Articles</h2>
    
    <div class="article-grid">
      <div class="article-card">
        <img src="https://images.unsplash.com/photo-1532094349884-543bc11b234d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Managing Anxiety" class="article-image">
        <div class="article-content">
          <span class="category-tag">Anxiety</span>
          <h3>Managing Anxiety in Daily Life</h3>
          <p>Learn practical strategies to understand and manage anxiety effectively with evidence-based techniques.</p>
          <div class="article-meta">
            <a href="https://www.verywellmind.com/tips-to-reduce-anxiety-2584182" class="read-more" target="_blank">
              Read More <i class="fas fa-arrow-right"></i>
            </a>
            <span>8 min read</span>
          </div>
        </div>
      </div>

      <div class="article-card">
        <img src="https://images.unsplash.com/photo-1494172961521-33799ddd43a5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Self-Esteem" class="article-image">
        <div class="article-content">
          <span class="category-tag">Confidence</span>
          <h3>Building Healthy Self-Esteem</h3>
          <p>Discover practical ways to build confidence and self-worth through positive thinking and action.</p>
          <div class="article-meta">
            <a href="https://www.mind.org.uk/information-support/types-of-mental-health-problems/self-esteem/" class="read-more" target="_blank">
              Read More <i class="fas fa-arrow-right"></i>
            </a>
            <span>6 min read</span>
          </div>
        </div>
      </div>

      <div class="article-card">
        <img src="https://images.unsplash.com/photo-1545205597-3d9d02c29597?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Mindfulness" class="article-image">
        <div class="article-content">
          <span class="category-tag">Mindfulness</span>
          <h3>The Power of Mindfulness Practice</h3>
          <p>Learn how mindfulness meditation helps reduce stress and increases clarity and emotional balance.</p>
          <div class="article-meta">
            <a href="https://www.headspace.com/mindfulness" class="read-more" target="_blank">
              Read More <i class="fas fa-arrow-right"></i>
            </a>
            <span>10 min read</span>
          </div>
        </div>
      </div>

      <div class="article-card">
        <img src="https://images.unsplash.com/photo-1531353826977-0941b4779a1c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Sleep" class="article-image">
        <div class="article-content">
          <span class="category-tag">Sleep Health</span>
          <h3>Sleep & Mental Wellness Connection</h3>
          <p>Explore how quality sleep affects your mood, focus, and overall mental health.</p>
          <div class="article-meta">
            <a href="https://www.sleepfoundation.org/mental-health" class="read-more" target="_blank">
              Read More <i class="fas fa-arrow-right"></i>
            </a>
            <span>7 min read</span>
          </div>
        </div>
      </div>

      <div class="article-card">
        <img src="https://images.unsplash.com/photo-1529333166437-7750a6dd5a70?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Boundaries" class="article-image">
        <div class="article-content">
          <span class="category-tag">Relationships</span>
          <h3>Setting Healthy Personal Boundaries</h3>
          <p>Improve your relationships and reduce stress by learning how to set respectful boundaries.</p>
          <div class="article-meta">
            <a href="https://psychcentral.com/lib/learning-to-say-no-setting-boundaries" class="read-more" target="_blank">
              Read More <i class="fas fa-arrow-right"></i>
            </a>
            <span>9 min read</span>
          </div>
        </div>
      </div>

      <div class="article-card">
        <img src="https://images.unsplash.com/photo-1506126613408-eca07ce68773?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Depression" class="article-image">
        <div class="article-content">
          <span class="category-tag">Depression</span>
          <h3>Understanding and Coping with Depression</h3>
          <p>Recognize depression symptoms and explore healthy ways to cope and seek support.</p>
          <div class="article-meta">
            <a href="https://www.nimh.nih.gov/health/topics/depression" class="read-more" target="_blank">
              Read More <i class="fas fa-arrow-right"></i>
            </a>
            <span>12 min read</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <div class="footer-content">
      <p>More resources coming weekly to support your mental health journey</p>
      <div class="social-links">
        <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
      </div>
      <p>&copy; 2025 SerenityConnect. All rights reserved.</p>
    </div>
  </footer>

</body>
</html>