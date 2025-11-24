<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/bulk_save.php';
requireLogin();
checkSessionTimeout();

$settings = loadSiteSettings($pdo);
$themeConfig = getThemeConfiguration($settings);
$themeCssVariables = buildThemeCssVariables($themeConfig);

$roleLabels = [
    ROLE_SUPER_ADMIN => 'Super Admin',
    ROLE_CONTENT_ADMIN => 'Content Admin',
    ROLE_EDITOR => 'Editor (Draft)',
];
$currentRole = currentUserRole();
$currentRoleLabel = $currentRole && isset($roleLabels[$currentRole]) ? $roleLabels[$currentRole] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Dashboard'; ?> - CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php
    $adminFontLinks = [];
    if (!empty($themeConfig['font_link_en'])) {
        $adminFontLinks[] = $themeConfig['font_link_en'];
    }
    if (!empty($themeConfig['font_link_ar']) && $themeConfig['font_link_ar'] !== ($themeConfig['font_link_en'] ?? '')) {
        $adminFontLinks[] = $themeConfig['font_link_ar'];
    }
    foreach ($adminFontLinks as $fontLink):
    ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($fontLink); ?>">
    <?php endforeach; ?>
    <style>
        :root {
            --sidebar-width: 250px;
            --primary-color: <?php echo $themeCssVariables['--primary-color']; ?>;
            --primary-color-rgb: <?php echo $themeCssVariables['--primary-color-rgb']; ?>;
            --primary-color-light: <?php echo $themeCssVariables['--primary-color-light']; ?>;
            --primary-color-dark: <?php echo $themeCssVariables['--primary-color-dark']; ?>;
            --primary-gradient-start: <?php echo $themeCssVariables['--primary-gradient-start']; ?>;
            --primary-gradient-end: <?php echo $themeCssVariables['--primary-gradient-end']; ?>;
            --primary-hero-gradient: <?php echo $themeCssVariables['--primary-hero-gradient']; ?>;
            --font-family-en: <?php echo $themeCssVariables['--font-family-en']; ?>;
            --font-family-ar: <?php echo $themeCssVariables['--font-family-ar']; ?>;
            --bs-primary: var(--primary-color);
            --bs-primary-rgb: <?php echo $themeCssVariables['--primary-color-rgb']; ?>;
        }

        body {
            background-color: #f8f9fa;
            font-family: <?php echo $themeCssVariables['--font-family-en']; ?>;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);
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

        .bulk-save-toolbar {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 1.25rem;
            background: #fff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        .bulk-save-active .bulk-save-hidden {
            display: none !important;
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
            <small>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?> (<?php echo htmlspecialchars($currentRoleLabel); ?>)</small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'home_page.php' ? 'active' : ''; ?>" href="home_page.php">
                <i class="fas fa-home"></i> Home
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about_page.php' ? 'active' : ''; ?>" href="about_page.php">
                <i class="fas fa-info-circle"></i> About US
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'training_program.php' ? 'active' : ''; ?>" href="training_program.php">
                <i class="fas fa-flag"></i> Training Program
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'faq.php' ? 'active' : ''; ?>" href="faq.php">
                <i class="fas fa-question-circle"></i> FAQ
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'tools_page.php' ? 'active' : ''; ?>" href="tools_page.php">
                <i class="fas fa-toolbox"></i> Tools Page
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'topics.php' ? 'active' : ''; ?>" href="topics.php">
                <i class="fas fa-graduation-cap"></i> Tools
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'statistics_page.php' ? 'active' : ''; ?>" href="statistics_page.php">
                <i class="fas fa-chart-bar"></i> Statistics
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'content_items.php' ? 'active' : ''; ?>" href="content_items.php">
                <i class="fas fa-file-alt"></i> Content Items
            </a>
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'course_rounds.php' ? 'active' : ''; ?>" href="course_rounds.php">
                <i class="fas fa-calendar-alt"></i> Course Rounds
            </a>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'media_upload.php' ? 'active' : ''; ?>" href="media_upload.php">
                <i class="fas fa-upload"></i> Media Upload
            </a>
            <?php if (hasRole([ROLE_SUPER_ADMIN, ROLE_CONTENT_ADMIN])): ?>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'site_settings.php' ? 'active' : ''; ?>" href="site_settings.php">
                    <i class="fas fa-cog"></i> Site Settings
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'footer_settings.php' ? 'active' : ''; ?>" href="footer_settings.php">
                    <i class="fas fa-grip-horizontal"></i> Footer Settings
                </a>
            <?php endif; ?>
            <?php if (hasRole([ROLE_SUPER_ADMIN])): ?>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_admins.php' ? 'active' : ''; ?>" href="manage_admins.php">
                    <i class="fas fa-users-cog"></i> Manage Admins
                </a>
            <?php endif; ?>
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
