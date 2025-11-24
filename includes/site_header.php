<?php
// This file is included by each public page
// Assumes $lang and $pdo are already set by the calling page
require_once __DIR__ . '/functions.php';

$settings = loadSiteSettings($pdo);
$themeConfig = getThemeConfiguration($settings);
$themeCssVariables = buildThemeCssVariables($themeConfig);
$footer_settings = loadFooterSettings($pdo);

// Get tools for navigation
$stmt = $pdo->query("SELECT slug, title_en, title_ar FROM topics WHERE is_tool = true ORDER BY display_order, title_en");
$nav_tools = $stmt->fetchAll(PDO::FETCH_ASSOC);

$currentPage = getCurrentPage();
$switchToLang = $lang === 'en' ? 'ar' : 'en';
$languageSwitchUrl = preserveLang($_SERVER['REQUEST_URI'] ?? basename($_SERVER['PHP_SELF'] ?? 'index.php'), $switchToLang);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Learn Data Analysis'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php
    $fontLinks = [];
    if (!empty($themeConfig['font_link_en'])) {
        $fontLinks[] = $themeConfig['font_link_en'];
    }
    if (!empty($themeConfig['font_link_ar']) && $themeConfig['font_link_ar'] !== ($themeConfig['font_link_en'] ?? '')) {
        $fontLinks[] = $themeConfig['font_link_ar'];
    }
    foreach ($fontLinks as $fontLink):
    ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($fontLink); ?>">
    <?php endforeach; ?>
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if ($lang === 'ar'): ?>
        <link rel="stylesheet" href="assets/css/rtl.css">
    <?php endif; ?>
    <style>
        :root {
            <?php foreach ($themeCssVariables as $variable => $value): ?>
            <?php echo $variable; ?>: <?php echo $value; ?>;
            <?php endforeach; ?>
            --bs-primary: <?php echo $themeCssVariables['--primary-color']; ?>;
            --bs-primary-rgb: <?php echo $themeCssVariables['--primary-color-rgb']; ?>;
            --bs-link-color: var(--primary-color);
            --bs-link-hover-color: var(--primary-color-dark, var(--primary-color));
            --bs-body-font-family: var(--font-family-base, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif);
            --bs-body-color: var(--text-dark);
        }
        html[lang="en"] {
            --font-family-base: var(--font-family-en);
        }
        html[lang="ar"] {
            --font-family-base: var(--font-family-ar);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php<?php echo $lang === 'ar' ? '?lang=ar' : ''; ?>">
                <?php if (isset($settings['logo']) && $settings['logo']): ?>
                    <img src="<?php echo htmlspecialchars($settings['logo']); ?>" alt="Logo" height="40">
                <?php else: ?>
                    <strong><?php echo htmlspecialchars($settings['site_name_' . $lang] ?? 'Learn Data Analysis'); ?></strong>
                <?php endif; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>" 
                           href="<?php echo preserveLang('index.php', $lang); ?>"><?php echo t('home', $lang); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'about' ? 'active' : ''; ?>" 
                           href="<?php echo preserveLang('about.php', $lang); ?>"><?php echo t('about', $lang); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'training-program' ? 'active' : ''; ?>"
                           href="<?php echo preserveLang('training-program.php', $lang); ?>"><?php echo t('training_program', $lang); ?></a>
                    </li>
                    <?php
                    $isToolsActive = in_array($currentPage, ['tools', 'tool']);
                    $hasNavTools = count($nav_tools) > 0;
                    ?>
                    <li class="nav-item <?php echo $hasNavTools ? 'dropdown tools-nav-item' : ''; ?>">
                        <?php if ($hasNavTools): ?>
                            <div class="nav-link-wrapper">
                                <a class="nav-link <?php echo $isToolsActive ? 'active' : ''; ?>" href="<?php echo preserveLang('tools.php', $lang); ?>">
                                    <?php echo t('tools', $lang); ?>
                                    <span class="dropdown-arrow d-none d-lg-inline">
                                        <i class="fa-solid fa-chevron-down"></i>
                                    </span>
                                </a>
                                <button
                                    class="nav-dropdown-toggle d-lg-none"
                                    type="button"
                                    aria-expanded="false"
                                    aria-haspopup="true"
                                    aria-controls="toolsDropdownMenu"
                                    aria-label="<?php echo $lang === 'ar' ? 'فتح قائمة الأدوات' : 'Toggle tools menu'; ?>">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>
                            </div>
                            <ul id="toolsDropdownMenu" class="dropdown-menu<?php echo $lang === 'ar' ? ' dropdown-menu-end text-end' : ''; ?>">
                                <?php foreach ($nav_tools as $tool): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo preserveLang('tool.php?slug=' . urlencode($tool['slug']), $lang); ?>">
                                            <?php echo htmlspecialchars($tool['title_' . $lang]); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <a class="nav-link <?php echo $isToolsActive ? 'active' : ''; ?>" href="<?php echo preserveLang('tools.php', $lang); ?>">
                                <?php echo t('tools', $lang); ?>
                            </a>
                        <?php endif; ?>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'statistics' ? 'active' : ''; ?>"
                           href="<?php echo preserveLang('statistics.php', $lang); ?>"><?php echo t('statistics', $lang); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'faq' ? 'active' : ''; ?>" 
                           href="<?php echo preserveLang('faq.php', $lang); ?>"><?php echo t('faq', $lang); ?></a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a class="nav-link language-switch text-nowrap"
                           href="<?php echo htmlspecialchars($languageSwitchUrl); ?>">
                            <i class="fa-solid fa-globe"></i>
                            <span><?php echo $lang === 'en' ? 'العربية' : 'English'; ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
