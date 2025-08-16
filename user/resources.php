<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources - SerenityConnect</title>
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
            --border: #e2e8f0;
            --border-light: #f1f5f9;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --radius: 12px;
            --radius-lg: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-alt);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .page-header {
            text-align: center;
            padding: 3rem 1rem 2rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            box-shadow: var(--shadow-lg);
            border-bottom-left-radius: var(--radius-lg);
            border-bottom-right-radius: var(--radius-lg);
        }
        
        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .container {
            max-width: 900px;
            margin: auto;
            padding: 2rem 1rem;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: var(--primary);
            margin-top: 3rem;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--border-light);
            padding-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .section-title i {
            color: var(--accent);
            font-size: 1.5rem;
        }

        .card-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .card {
            background: var(--surface);
            padding: 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: var(--transition);
        }
        
        .card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .card ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .card li {
            margin-bottom: 1rem;
        }
        
        .card li:last-child {
            margin-bottom: 0;
        }
        
        .card a, .helpline-text {
            color: var(--primary-medium);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card a:hover {
            color: var(--accent);
            text-decoration: underline;
        }
        
        .card a i {
            color: var(--accent);
            font-size: 1.1rem;
        }
        
        .card a:hover i {
            color: var(--primary);
        }

        .helpline-number {
            font-weight: 700;
            font-size: 1.1rem;
            color: #ef4444;
            display: block;
            margin-top: 0.5rem;
        }
        
        .helpline-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-bottom: 1px solid var(--border-light);
        }
        
        .helpline-item:last-child {
            border-bottom: none;
        }
        
        .helpline-item .flag {
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="page-header">
        <h1 class="page-title">Mental Health Resources</h1>
        <p class="page-subtitle">A collection of helplines, articles, and guides to support your well-being.</p>
    </div>

    <div class="container">

        <h2 class="section-title">
            <i class="fas fa-phone-alt"></i>
            Helplines
        </h2>
        <div class="card card-container">
            <div class="helpline-item">
                <span class="flag">🇮🇳</span>
                <div>
                    <div class="helpline-text"><strong>India:</strong> AASRA Helpline</div>
                    <span class="helpline-number">+91-9820466726</span>
                </div>
            </div>
            <div class="helpline-item">
                <span class="flag">🇺🇸</span>
                <div>
                    <div class="helpline-text"><strong>USA:</strong> Suicide & Crisis Lifeline</div>
                    <span class="helpline-number">988</span>
                </div>
            </div>
            <div class="helpline-item">
                <span class="flag">🇬🇧</span>
                <div>
                    <div class="helpline-text"><strong>UK:</strong> Samaritans</div>
                    <span class="helpline-number">116 123</span>
                </div>
            </div>
        </div>

        <h2 class="section-title">
            <i class="fas fa-book-open"></i>
            Important Articles
        </h2>
        <div class="card">
            <ul>
                <li>
                    <a href="https://www.who.int/news-room/fact-sheets/detail/mental-health-strengthening-our-response" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        WHO: Strengthening Mental Health Response
                    </a>
                </li>
                <li>
                    <a href="https://www.nimh.nih.gov/health/topics/caring-for-your-mental-health" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        NIMH: Caring for Your Mental Health
                    </a>
                </li>
                <li>
                    <a href="https://www.psychologytoday.com/us/basics/mental-health" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        Psychology Today: Mental Health Basics
                    </a>
                </li>
            </ul>
        </div>

        <h2 class="section-title">
            <i class="fas fa-download"></i>
            Downloadable Guides
        </h2>
        <div class="card">
            <ul>
                <li>
                    <a href="files/self_care_tips.pdf" download>
                        <i class="fas fa-file-download"></i>
                        Self Care Tips (PDF)
                    </a>
                </li>
                <li>
                    <a href="files/stress_management_guide.pdf" download>
                        <i class="fas fa-file-download"></i>
                        Stress Management Guide (PDF)
                    </a>
                </li>
                <li>
                    <a href="files/mindfulness_exercises.pdf" download>
                        <i class="fas fa-file-download"></i>
                        Mindfulness Exercises (PDF)
                    </a>
                </li>
            </ul>
        </div>

    </div>
</body>
</html>