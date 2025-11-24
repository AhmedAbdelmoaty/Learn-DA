<?php
$page_title = 'Site Settings';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
requireRole([ROLE_SUPER_ADMIN, ROLE_CONTENT_ADMIN]);
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

$stmt = $pdo->query("SELECT * FROM site_settings");
$settings_rows = $stmt->fetchAll();
$settings = [];
foreach ($settings_rows as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$themeDefaults = [
    'primary_color' => '#A8324E',
    'text_dark' => '#333333',
    'text_light' => '#666666',
    'font_family_en' => "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
    'font_family_ar' => "'Tajawal', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
    'font_link_en' => '',
    'font_link_ar' => '',
];

$fontOptionsEn = [
    'segoe' => [
        'label' => 'Segoe UI (Default)',
        'stack' => "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
    ],
    'poppins' => [
        'label' => 'Poppins',
        'stack' => "'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
    ],
    'inter' => [
        'label' => 'Inter',
        'stack' => "'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
    ],
    'roboto' => [
        'label' => 'Roboto',
        'stack' => "'Roboto', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
    ],
];

$fontOptionsAr = [
    'tajawal' => [
        'label' => 'Tajawal (Default)',
        'stack' => "'Tajawal', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
    ],
    'cairo' => [
        'label' => 'Cairo',
        'stack' => "'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
    ],
    'ibm-plex' => [
        'label' => 'IBM Plex Sans Arabic',
        'stack' => "'IBM Plex Sans Arabic', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
    ],
    'almarai' => [
        'label' => 'Almarai',
        'stack' => "'Almarai', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
    ],
];

if (!array_key_exists('admin_email', $settings)) {
    $settings['admin_email'] = '';
}

foreach ($themeDefaults as $key => $value) {
    if (!array_key_exists($key, $settings)) {
        $settings[$key] = $value;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $fields = [
            'site_name_en' => trim($_POST['site_name_en'] ?? ''),
            'site_name_ar' => trim($_POST['site_name_ar'] ?? ''),
            'logo' => trim($_POST['logo'] ?? ''),
            'admin_email' => trim($_POST['admin_email'] ?? ''),
        ];

        $fields['primary_color'] = normalizeHexColor($_POST['primary_color'] ?? null, $themeDefaults['primary_color']);
        $fields['text_dark'] = normalizeHexColor($_POST['text_dark'] ?? null, $themeDefaults['text_dark']);
        $fields['text_light'] = normalizeHexColor($_POST['text_light'] ?? null, $themeDefaults['text_light']);

        $fontChoiceEn = $_POST['font_family_en_choice'] ?? 'custom';
        if ($fontChoiceEn !== 'custom' && isset($fontOptionsEn[$fontChoiceEn])) {
            $fields['font_family_en'] = $fontOptionsEn[$fontChoiceEn]['stack'];
        } else {
            $fields['font_family_en'] = sanitizeFontStack($_POST['font_family_en_custom'] ?? null, $themeDefaults['font_family_en']);
        }

        $fontChoiceAr = $_POST['font_family_ar_choice'] ?? 'custom';
        if ($fontChoiceAr !== 'custom' && isset($fontOptionsAr[$fontChoiceAr])) {
            $fields['font_family_ar'] = $fontOptionsAr[$fontChoiceAr]['stack'];
        } else {
            $fields['font_family_ar'] = sanitizeFontStack($_POST['font_family_ar_custom'] ?? null, $themeDefaults['font_family_ar']);
        }
        $fields['font_link_en'] = filter_var(trim($_POST['font_link_en'] ?? ''), FILTER_VALIDATE_URL) ?: '';
        $fields['font_link_ar'] = filter_var(trim($_POST['font_link_ar'] ?? ''), FILTER_VALIDATE_URL) ?: '';

        $updateStmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
        $insertStmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)");

        foreach ($fields as $key => $value) {
            $updateStmt->execute([$value, $key]);
            if ($updateStmt->rowCount() === 0) {
                $insertStmt->execute([$key, $value]);    
            }
            $settings[$key] = $value;
        }
        
        $success = 'Settings updated successfully!';
    } catch (PDOException $e) {
        $error = 'Error updating settings: ' . $e->getMessage();
    }
}

$selectedFontChoiceEn = 'custom';
foreach ($fontOptionsEn as $key => $option) {
    if (isset($settings['font_family_en']) && strcasecmp($settings['font_family_en'], $option['stack']) === 0) {
        $selectedFontChoiceEn = $key;
        break;
    }
}

$selectedFontChoiceAr = 'custom';
foreach ($fontOptionsAr as $key => $option) {
    if (isset($settings['font_family_ar']) && strcasecmp($settings['font_family_ar'], $option['stack']) === 0) {
        $selectedFontChoiceAr = $key;
        break;
    }
}

$fontFamilyEnCustomValue = $selectedFontChoiceEn === 'custom'
    ? ($settings['font_family_en'] ?? $themeDefaults['font_family_en'])
    : '';
$fontFamilyArCustomValue = $selectedFontChoiceAr === 'custom'
    ? ($settings['font_family_ar'] ?? $themeDefaults['font_family_ar'])
    : '';

$logoValue = $settings['logo'] ?? '';
$logoPreview = '';
if (!empty($logoValue)) {
    if (preg_match('/^https?:\/\//i', $logoValue) || strpos($logoValue, '//') === 0 || strpos($logoValue, '/') === 0) {
        $logoPreview = $logoValue;
    } else {
        $logoPreview = '../' . ltrim($logoValue, '/');
    }
}
?>

<style>
    .font-preview-card {
        border: 1px dashed #dee2e6;
        border-radius: 8px;
        padding: 12px;
        background: #f8f9fa;
        min-height: 72px;
    }
    .font-preview-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
        display: block;
    }
</style>

<div class="container-fluid mt-4">
    <div class="content-card">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
            <h5 class="mb-0">Site Settings</h5>
            <?php renderBulkSaveToolbar(['wrapper_class' => 'mt-3 mt-md-0']); ?>
        </div>
        <?php if ($success): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
        <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" data-bulk-save="true" data-section-name="Site Settings">
        <h5 class="mb-3">Site Identity</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Site Name (English)</label>
                <input type="text" class="form-control" name="site_name_en" value="<?php echo htmlspecialchars($settings['site_name_en'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">اسم الموقع (عربي)</label>
                <input type="text" class="form-control" name="site_name_ar" value="<?php echo htmlspecialchars($settings['site_name_ar'] ?? ''); ?>" required dir="rtl">
            </div>
        </div>

        

        <h5 class="mb-3 mt-4">Brand Assets</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Logo</label>
                <div class="input-group">
                    <input type="url" class="form-control" id="logo_url" name="logo" value="<?php echo htmlspecialchars($settings['logo'] ?? ''); ?>">
                    <button type="button" class="btn btn-outline-secondary btn-choose-media" onclick="openMediaPicker('logo_url', 'logo_preview')">
                        <i class="fas fa-images"></i> Choose
                    </button>
                </div>
                <div id="logo_preview" class="image-preview-box <?php echo !empty($logoPreview) ? 'active' : ''; ?> mt-2">
                <?php if (!empty($logoPreview)): ?>
                    <img src="<?php echo htmlspecialchars($logoPreview); ?>" alt="Logo Preview">
                <?php else: ?>
                    <img src="" alt="Logo Preview">
                <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Primary Color</label>
                <input type="color" class="form-control" name="primary_color" value="<?php echo htmlspecialchars($settings['primary_color'] ?? $themeDefaults['primary_color']); ?>">
            </div>
        </div>

        <h5 class="mb-3 mt-4">Theme Colors</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Text Color (Dark)</label>
                <input type="color" class="form-control" name="text_dark" value="<?php echo htmlspecialchars($settings['text_dark'] ?? $themeDefaults['text_dark']); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Text Color (Muted)</label>
                <input type="color" class="form-control" name="text_light" value="<?php echo htmlspecialchars($settings['text_light'] ?? $themeDefaults['text_light']); ?>">
            </div>
        </div>

        <h5 class="mb-3 mt-4">Typography</h5>
        <div class="row">
            <div class="col-md-6 mb-4">
                <label class="form-label">Primary Font (English)</label>
                <select class="form-select font-select" name="font_family_en_choice" data-custom-input="fontFamilyEnCustom" data-preview="fontPreviewEn">
                    <?php foreach ($fontOptionsEn as $key => $option): ?>
                        <option value="<?php echo htmlspecialchars($key); ?>" data-font-stack="<?php echo htmlspecialchars($option['stack']); ?>" <?php echo $selectedFontChoiceEn === $key ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($option['label']); ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="custom" <?php echo $selectedFontChoiceEn === 'custom' ? 'selected' : ''; ?>>Custom</option>
                </select>
                <input type="text" class="form-control mt-2 font-custom-input <?php echo $selectedFontChoiceEn === 'custom' ? '' : 'd-none'; ?>" id="fontFamilyEnCustom" name="font_family_en_custom" value="<?php echo htmlspecialchars($fontFamilyEnCustomValue); ?>" placeholder="e.g. 'Poppins', sans-serif">
                <small class="text-muted">Select from the list or choose "Custom" to enter your own CSS font stack.</small>
                <span class="font-preview-label mt-3">Live preview</span>
                <div class="font-preview-card" id="fontPreviewEn">
                    The quick brown fox jumps over the lazy dog.
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label">Primary Font (Arabic)</label>
                <select class="form-select font-select" name="font_family_ar_choice" data-custom-input="fontFamilyArCustom" data-preview="fontPreviewAr">
                    <?php foreach ($fontOptionsAr as $key => $option): ?>
                        <option value="<?php echo htmlspecialchars($key); ?>" data-font-stack="<?php echo htmlspecialchars($option['stack']); ?>" <?php echo $selectedFontChoiceAr === $key ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($option['label']); ?>
                        </option>
                    <?php endforeach; ?>
                    <option value="custom" <?php echo $selectedFontChoiceAr === 'custom' ? 'selected' : ''; ?>>مخصص</option>
                </select>
                <input type="text" class="form-control mt-2 font-custom-input <?php echo $selectedFontChoiceAr === 'custom' ? '' : 'd-none'; ?>" id="fontFamilyArCustom" name="font_family_ar_custom" value="<?php echo htmlspecialchars($fontFamilyArCustomValue); ?>" placeholder="مثال: 'Tajawal', 'Cairo', sans-serif" dir="rtl">
                <small class="text-muted">اختر من القائمة أو استخدم خيار "مخصص" لكتابة خط CSS خاص بك.</small>
                <span class="font-preview-label mt-3">معاينة مباشرة</span>
                <div class="font-preview-card" id="fontPreviewAr" dir="rtl">
                    هذا نص تجريبي للمعاينة الفورية باللغة العربية.
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Font Import URL (English) <span class="text-muted">Optional</span></label>
                <input type="url" class="form-control" name="font_link_en" value="<?php echo htmlspecialchars($settings['font_link_en'] ?? ''); ?>" placeholder="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Font Import URL (Arabic) <span class="text-muted">Optional</span></label>
                <input type="url" class="form-control" name="font_link_ar" value="<?php echo htmlspecialchars($settings['font_link_ar'] ?? ''); ?>" placeholder="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap">
            </div>
        </div>
        
        <h5 class="mb-3 mt-4">Contact Form Notifications</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Recipient Email</label>
                <input type="email" class="form-control" name="admin_email" value="<?php echo htmlspecialchars($settings['admin_email'] ?? ''); ?>" placeholder="team@company.com">
                <small class="text-muted">Contact form submissions will be emailed to this address.</small>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary bulk-save-hidden" name="submit">
            <i class="fas fa-save"></i> Save Settings
        </button>
    </form>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.font-select').forEach(function (select) {
        var customInputId = select.getAttribute('data-custom-input');
        var previewId = select.getAttribute('data-preview');
        var customInput = customInputId ? document.getElementById(customInputId) : null;
        var preview = previewId ? document.getElementById(previewId) : null;

        function updateCustomVisibility() {
            if (!customInput) {
                return;
            }
            if (select.value === 'custom') {
                customInput.classList.remove('d-none');
            } else {
                customInput.classList.add('d-none');
            }
        }

        function getSelectedFontStack() {
            if (select.value === 'custom') {
                return customInput ? customInput.value : '';
            }
            var option = select.options[select.selectedIndex];
            return option ? option.getAttribute('data-font-stack') || '' : '';
        }

        function updatePreview() {
            if (!preview) {
                return;
            }
            var fontStack = getSelectedFontStack();
            preview.style.fontFamily = fontStack || 'inherit';
        }

        select.addEventListener('change', function () {
            updateCustomVisibility();
            updatePreview();
        });

        if (customInput) {
            customInput.addEventListener('input', updatePreview);
        }

        updateCustomVisibility();
        updatePreview();
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
