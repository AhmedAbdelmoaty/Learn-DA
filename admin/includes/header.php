<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
requireLogin();
checkSessionTimeout();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Dashboard'; ?> - CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: #a8324e;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, #6c1e35 100%);
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .sidebar .logo {
            padding: 20px;
            text-align: center;
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: white;
        }
        
        .sidebar .nav-link i {
            width: 25px;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }
        
        .topbar {
            background: white;
            padding: 15px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .content-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 30px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <h4><i class="fas fa-cog"></i> CMS Admin</h4>
            <small>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'hero.php' ? 'active' : ''; ?>" href="hero.php">
                <i class="fas fa-image"></i> Hero Section
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="about.php">
                <i class="fas fa-info-circle"></i> About Course
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'benefits.php' ? 'active' : ''; ?>" href="benefits.php">
                <i class="fas fa-star"></i> Key Benefits
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'course_details.php' ? 'active' : ''; ?>" href="course_details.php">
                <i class="fas fa-book"></i> Course Details
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'testimonials.php' ? 'active' : ''; ?>" href="testimonials.php">
                <i class="fas fa-quote-left"></i> Testimonials
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'faq.php' ? 'active' : ''; ?>" href="faq.php">
                <i class="fas fa-question-circle"></i> FAQ
            </a>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'topics.php' ? 'active' : ''; ?>" href="topics.php">
                <i class="fas fa-graduation-cap"></i> Topics
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'content_items.php' ? 'active' : ''; ?>" href="content_items.php">
                <i class="fas fa-file-alt"></i> Content Items
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'page_sections.php' ? 'active' : ''; ?>" href="page_sections.php">
                <i class="fas fa-th-large"></i> Page Sections
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'course_rounds.php' ? 'active' : ''; ?>" href="course_rounds.php">
                <i class="fas fa-calendar-alt"></i> Course Rounds
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'training_program.php' ? 'active' : ''; ?>" href="training_program.php">
                <i class="fas fa-flag"></i> Training Program
            </a>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'media_upload.php' ? 'active' : ''; ?>" href="media_upload.php">
                <i class="fas fa-upload"></i> Media Upload
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'site_settings.php' ? 'active' : ''; ?>" href="site_settings.php">
                <i class="fas fa-cog"></i> Site Settings
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'footer_settings.php' ? 'active' : ''; ?>" href="footer_settings.php">
                <i class="fas fa-grip-horizontal"></i> Footer Settings
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_admins.php' ? 'active' : ''; ?>" href="manage_admins.php">
                <i class="fas fa-users-cog"></i> Manage Admins
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>" href="messages.php">
                <i class="fas fa-envelope"></i> Contact Messages
            </a>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>
    
    <div class="main-content">
        <div class="topbar d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><?php echo $page_title ?? 'Dashboard'; ?></h5>
            <div>
                <a href="../index.php" class="btn btn-sm btn-outline-primary" target="_blank">
                    <i class="fas fa-eye"></i> View Website
                </a>
            </div>
        </div>
