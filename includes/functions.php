<?php
// Translation function
function t($key, $lang, $settings = []) {
    $translations = [
        'en' => [
            'home' => 'Home',
            'about' => 'About Us',
            'training_program' => 'Training Program',
            'tools' => 'Tools',
            'excel' => 'Excel',
            'power_bi' => 'Power BI',
            'statistics' => 'Statistics',
            'sql' => 'SQL',
            'contact' => 'Contact',
            'contact_us' => 'Contact Us',
            'send_message' => 'Send Message',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'message' => 'Message',
            'submit' => 'Submit',
            'enroll_now' => 'Enroll Now',
            'read_more' => 'Read More',
            'explore_topics' => 'Explore Our Topics',
            'explore_tools' => 'Explore Our Tools',
            'why_data_analysis' => 'Why Data Analysis?',
            'benefits' => 'Key Benefits',
            'testimonials' => 'What Our Students Say',
            'faq' => 'FAQ',
            'site_links' => 'Site Links',
            'follow_us' => 'Follow Us',
            'copyright' => '© ' . date('Y') . ' ' . ($settings['site_name_en'] ?? 'Learn Data Analysis') . '. All rights reserved.',
            'related_content' => 'Related Content',
            'view_tool' => 'Explore Tool',
            'back_to' => 'Back to',
            'choose_course_date' => 'Choose the course date',
            'no_rounds_available' => 'No upcoming rounds available at the moment.',
            'what_you_learn' => 'What You\'ll Learn',
            'value_bonuses' => 'Value & Bonuses',
            'student_outcomes' => 'Student Outcomes',
            'top_questions' => 'Top Questions',
            'all_questions' => 'All Questions',
        ],
        'ar' => [
            'home' => 'الرئيسية',
            'about' => 'من نحن',
            'training_program' => 'البرنامج التدريبي',
            'tools' => 'الأدوات',
            'excel' => 'إكسل',
            'power_bi' => 'باور بي آي',
            'statistics' => 'الإحصاء',
            'sql' => 'SQL',
            'contact' => 'اتصل بنا',
            'contact_us' => 'تواصل معنا',
            'send_message' => 'إرسال رسالة',
            'name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
            'phone' => 'رقم الهاتف',
            'message' => 'الرسالة',
            'submit' => 'إرسال',
            'enroll_now' => 'سجل الآن',
            'read_more' => 'اقرأ المزيد',
            'explore_topics' => 'استكشف مواضيعنا',
            'explore_tools' => 'استكشف الأدوات',
            'why_data_analysis' => 'لماذا تحليل البيانات؟',
            'benefits' => 'الفوائد الرئيسية',
            'testimonials' => 'ماذا يقول طلابنا',
            'faq' => 'الأسئلة الشائعة',
            'site_links' => 'روابط الموقع',
            'follow_us' => 'تابعنا',
            'copyright' => '© ' . date('Y') . ' ' . ($settings['site_name_ar'] ?? 'تعلم تحليل البيانات') . '. جميع الحقوق محفوظة.',
            'related_content' => 'محتوى ذو صلة',
            'view_tool' => 'استكشف الأداة',
            'back_to' => 'العودة إلى',
            'choose_course_date' => 'اختَر موعد الدورة',
            'no_rounds_available' => 'لا توجد مواعيد متاحة حاليًا.',
            'what_you_learn' => 'ماذا ستتعلم',
            'value_bonuses' => 'قيمة مضافة ومزايا',
            'student_outcomes' => 'لماذا هذا البرنامج',
            'top_questions' => 'أهم الأسئلة',
            'all_questions' => 'جميع الأسئلة',
        ]
    ];
    return $translations[$lang][$key] ?? $key;
}

function loadSiteSettings(PDO $pdo): array {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
    $settings = [];

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    return $settings;
}

function loadFooterSettings(PDO $pdo): array {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM footer_settings");
    $settings = [];

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    return $settings;
}

// Get current page name from URL
function getCurrentPage() {
    $page = basename($_SERVER['PHP_SELF'], '.php');
    return $page;
}

// Preserve language parameter in URLs
function preserveLang($url, $lang) {
    if (!is_string($url) || $url === '') {
        $url = 'index.php';
    }

    $parsed = parse_url($url);

    $path = $parsed['path'] ?? '';
    $queryArray = [];
    if (isset($parsed['query'])) {
        parse_str($parsed['query'], $queryArray);
    }

    $queryArray['lang'] = $lang;
    $queryString = http_build_query($queryArray);
    $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

    if ($queryString !== '') {
        return $path . '?' . $queryString . $fragment;
    }

    return $path . $fragment;
}
function getCardSummaryText(array $item, string $lang, ?int $maxChars = null): string {
    $preferredOrder = [
        'summary_' . $lang,
        'summary_' . ($lang === 'ar' ? 'en' : 'ar'),
        'body_' . $lang,
        'body_' . ($lang === 'ar' ? 'en' : 'ar'),
    ];

    $text = '';
    foreach ($preferredOrder as $field) {
        if (!isset($item[$field])) {
            continue;
        }

        $candidate = is_string($item[$field]) ? trim($item[$field]) : '';
        if ($candidate === '') {
            continue;
        }

        $candidate = strip_tags($candidate);
        if ($candidate !== '') {
            $text = $candidate;
            break;
        }
    }

    if ($text === '') {
        return '';
    }

    $text = preg_replace('/\s+/u', ' ', $text);

    if ($maxChars !== null && $maxChars > 0) {
        $lengthFunc = function_exists('mb_strlen')
            ? fn($str) => mb_strlen($str, 'UTF-8')
            : fn($str) => strlen($str);
        $substrFunc = function_exists('mb_substr')
            ? fn($str, $start, $length) => mb_substr($str, $start, $length, 'UTF-8')
            : fn($str, $start, $length) => substr($str, $start, $length);

        if ($lengthFunc($text) > $maxChars) {
            $text = $substrFunc($text, 0, $maxChars);
            $text = rtrim($text, " \t\n\r\0\x0B,.;:!؟،");
            $text .= '…';
        }
    }

    return $text;
}

function normalizeHexColor(?string $color, string $fallback): string {
    if (is_string($color) && preg_match('/^#([0-9a-fA-F]{6})$/', trim($color))) {
        return strtoupper(trim($color));
    }

    return strtoupper($fallback);
}

function hexColorToRgb(string $color): array {
    $normalized = ltrim($color, '#');

    return [
        'r' => hexdec(substr($normalized, 0, 2)),
        'g' => hexdec(substr($normalized, 2, 2)),
        'b' => hexdec(substr($normalized, 4, 2)),
    ];
}

function adjustColorLightness(string $hexColor, float $ratio): string {
    $ratio = max(-1.0, min(1.0, $ratio));
    $rgb = hexColorToRgb($hexColor);

    $adjust = function (int $component) use ($ratio): int {
        if ($ratio >= 0) {
            return (int)round($component + (255 - $component) * $ratio);
        }

        return (int)round($component * (1 + $ratio));
    };

    $r = max(0, min(255, $adjust($rgb['r'])));
    $g = max(0, min(255, $adjust($rgb['g'])));
    $b = max(0, min(255, $adjust($rgb['b'])));

    return sprintf('#%02X%02X%02X', $r, $g, $b);
}

function clampOpacityValue($value, int $fallback): int {
    if (!is_numeric($value)) {
        return $fallback;
    }

    $intVal = (int)$value;
    if ($intVal < 0) {
        return 0;
    }
    if ($intVal > 100) {
        return 100;
    }

    return $intVal;
}

function rgbaFromHex(string $hexColor, int $opacityPercent): string {
    $rgb = hexColorToRgb($hexColor);
    $alpha = max(0, min(100, $opacityPercent)) / 100;
    $alphaFormatted = rtrim(rtrim(number_format($alpha, 2, '.', ''), '0'), '.');

    return sprintf('rgba(%d, %d, %d, %s)', $rgb['r'], $rgb['g'], $rgb['b'], $alphaFormatted === '' ? '0' : $alphaFormatted);
}

function sanitizeFontStack(?string $value, string $fallback): string {
    if (!is_string($value)) {
        return $fallback;
    }

    $value = trim($value);
    if ($value === '') {
        return $fallback;
    }

    $value = preg_replace('/[^\\w\s,\-"\'\.]/u', '', $value);
    $value = trim($value);

    return $value === '' ? $fallback : $value;
}

function getThemeConfiguration(array $settings): array {
    $defaults = [
        'primary_color' => '#A8324E',
        'text_dark' => '#333333',
        'text_light' => '#666666',
        'font_family_en' => "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
        'font_family_ar' => "'Tajawal', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
        'font_link_en' => '',
        'font_link_ar' => '',
    ];

    $theme = [];

    $theme['primary_color'] = normalizeHexColor($settings['primary_color'] ?? null, $defaults['primary_color']);
    $theme['text_dark'] = normalizeHexColor($settings['text_dark'] ?? null, $defaults['text_dark']);
    $theme['text_light'] = normalizeHexColor($settings['text_light'] ?? null, $defaults['text_light']);
    $theme['font_family_en'] = sanitizeFontStack($settings['font_family_en'] ?? null, $defaults['font_family_en']);
    $theme['font_family_ar'] = sanitizeFontStack($settings['font_family_ar'] ?? null, $defaults['font_family_ar']);
    $theme['font_link_en'] = filter_var($settings['font_link_en'] ?? '', FILTER_VALIDATE_URL) ?: '';
    $theme['font_link_ar'] = filter_var($settings['font_link_ar'] ?? '', FILTER_VALIDATE_URL) ?: '';

    return $theme;
}

function buildThemeCssVariables(array $themeConfig): array {
    $primaryRgb = hexColorToRgb($themeConfig['primary_color']);
    $primaryLight = adjustColorLightness($themeConfig['primary_color'], 0.12);
    $primaryDark = adjustColorLightness($themeConfig['primary_color'], -0.18);
    $gradientStart = $primaryLight;
    $gradientEnd = $primaryDark;

    return [
        '--primary-color' => $themeConfig['primary_color'],
        '--text-dark' => $themeConfig['text_dark'],
        '--text-light' => $themeConfig['text_light'],
        '--primary-color-rgb' => sprintf('%d, %d, %d', $primaryRgb['r'], $primaryRgb['g'], $primaryRgb['b']),
        '--primary-color-light' => $primaryLight,
        '--primary-color-dark' => $primaryDark,
        '--primary-gradient-start' => $gradientStart,
        '--primary-gradient-end' => $gradientEnd,
        '--primary-hero-gradient' => 'linear-gradient(' . $gradientStart . ', ' . $gradientEnd . ')',
        '--font-family-en' => $themeConfig['font_family_en'],
        '--font-family-ar' => $themeConfig['font_family_ar'],
    ];
}

function getTopicHeroGradient(array $topic): string {
    $slug = $topic['slug'] ?? '';

    $defaults = [
        'excel' => ['start' => '#2E7D32', 'end' => '#1B5E20', 'start_opacity' => 90, 'end_opacity' => 90],
        'power-bi' => ['start' => '#FFC400', 'end' => '#CC7800', 'start_opacity' => 92, 'end_opacity' => 90],
        'sql' => ['start' => '#3F51B5', 'end' => '#21358C', 'start_opacity' => 90, 'end_opacity' => 90],
        'statistics' => ['start' => '#009688', 'end' => '#00695C', 'start_opacity' => 90, 'end_opacity' => 90],
        'default' => ['start' => '#A8324E', 'end' => '#6C1E35', 'start_opacity' => 90, 'end_opacity' => 90],
    ];

    $config = $defaults[$slug] ?? $defaults['default'];

    $colorStart = normalizeHexColor($topic['hero_overlay_color_start'] ?? null, $config['start']);
    $colorEnd = normalizeHexColor($topic['hero_overlay_color_end'] ?? null, $config['end']);
    $opacityStart = clampOpacityValue($topic['hero_overlay_opacity_start'] ?? $config['start_opacity'], $config['start_opacity']);
    $opacityEnd = clampOpacityValue($topic['hero_overlay_opacity_end'] ?? $config['end_opacity'], $config['end_opacity']);

    return 'linear-gradient(' . rgbaFromHex($colorStart, $opacityStart) . ', ' . rgbaFromHex($colorEnd, $opacityEnd) . ')';
}
?>
