<?php
session_start();

// auth_check.php is assumed to handle redirection if not logged in
include('../auth_check.php');

$name = $_SESSION['name'] ?? 'User';

// Include Font Awesome CSS for icons
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>User Dashboard - SerenityConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1e3a2e;
            --primary-medium: #2e5543;
            --primary-light: #3d7058;
            --accent: #e8b84d;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --background: #f8fafc;
            --surface: #ffffff;
            --border: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
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
            background-color: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .dashboard-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-container {
            max-width: 1200px;
            margin: auto;
            padding: 2rem 1rem;
            flex-grow: 1;
        }

        .header-content {
            background: linear-gradient(to right, var(--primary), var(--primary-medium));
            color: var(--surface);
            padding: 3rem 1rem 5rem;
            box-shadow: var(--shadow);
            border-bottom-left-radius: var(--radius-lg);
            border-bottom-right-radius: var(--radius-lg);
            position: relative;
        }
        
        .header-text {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 500;
        }

        .header-text p {
            font-size: 1.1rem;
            margin-top: 0.5rem;
            color: #e2e8f0;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: -3rem;
            position: relative;
            z-index: 10;
        }

        .card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .card-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }
        
        .card-content h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .card-content p {
            font-size: 0.95rem;
            color: var(--text-secondary);
        }
        
        .card a {
            margin-top: 1.5rem;
            display: inline-flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            background: var(--primary);
            color: white;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .card a:hover {
            background: var(--primary-medium);
        }
        
        .card a i {
            margin-left: 0.75rem;
        }

        .card.summary-card {
            grid-column: 1 / -1;
            display: block;
            border-left: 5px solid var(--accent);
            background: #fffdf5;
            padding: 2.5rem;
        }

        .summary-card h3 {
            color: var(--text-primary);
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .summary-card p {
            font-size: 1rem;
            color: var(--text-secondary);
        }

        footer {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
            font-size: 0.85rem;
            border-top: 1px solid var(--border);
            margin-top: 3rem;
        }
        
        @media (max-width: 768px) {
            .header-text h1 {
                font-size: 2rem;
            }
            .main-container {
                padding: 1rem;
            }
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .card.summary-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-wrapper">
        <?php include 'navbar.php'; ?>

        <header>
            <div class="header-content">
                <div class="header-text">
                    <h1>Hello, <?php echo htmlspecialchars($name); ?></h1>
                    <p>Welcome to your SerenityConnect dashboard.</p>
                </div>
            </div>
        </header>

        <main class="main-container">
            <div class="dashboard-grid">
                <div class="card">
                    <div class="card-icon"><i class="fas fa-search"></i></div>
                    <div class="card-content">
                        <h3>Find a Therapist</h3>
                        <p>Browse our directory of licensed professionals and find the right match for you.</p>
                    </div>
                    <a href="user.php">
                        Start Search <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="card-content">
                        <h3>My Appointments</h3>
                        <p>View, manage, and track your past and upcoming therapy sessions.</p>
                    </div>
                    <a href="view_bookings.php">
                        View Schedule <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-icon"><i class="fas fa-book"></i></div>
                    <div class="card-content">
                        <h3>Wellness Resources</h3>
                        <p>Explore articles, guides, and tools to support your mental wellness journey.</p>
                    </div>
                    <a href="resources.php">
                        Explore Resources <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-icon"><i class="fas fa-user-cog"></i></div>
                    <div class="card-content">
                        <h3>Account Settings</h3>
                        <p>Update your profile information, change preferences, and manage your account.</p>
                    </div>
                    <a href="updateprofile.php">
                        Manage Profile <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="card summary-card">
                    <h3>Your Wellness Journey</h3>
                    <p>SerenityConnect is here to provide personalized support for your mental health. Your dashboard is the central hub for connecting with therapists, managing your appointments, and accessing valuable resources to help you achieve emotional balance and personal growth. We're committed to helping you on every step of your journey.</p>
                </div>
            </div>
        </main>
        
        <footer>
            &copy; 2025 SerenityConnect. All rights reserved.
        </footer>
    </div>
    
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