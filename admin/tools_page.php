<?php
$page_title = 'Tools Page Management';
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';

$section_configs = [
    'hero' => [
        'label' => 'Hero Banner',
        'description' => 'Controls the hero title, subtitle, and background image for the Tools landing page.',
        'show_image' => true,
        'show_body' => false,
    ],
    'intro' => [
        'label' => 'Intro Section',
        'description' => 'Short introduction displayed before the list of tool cards.',
        'show_image' => false,
        'show_body' => true,
    ],
];

$active_tab = $_GET['tab'] ?? array_key_first($section_configs);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_tools_section') {
    $section_key = $_POST['section_key'] ?? '';

    if (!isset($section_configs[$section_key])) {
        $error_message = 'Unknown section selected.';
    } else {
        $config = $section_configs[$section_key];
        $title_en = trim($_POST['title_en'] ?? '');
        $title_ar = trim($_POST['title_ar'] ?? '');
        $subtitle_en = $config['show_body'] ? null : trim($_POST['subtitle_en'] ?? '');
        $subtitle_ar = $config['show_body'] ? null : trim($_POST['subtitle_ar'] ?? '');
        $body_en = $config['show_body'] ? trim($_POST['body_en'] ?? '') : null;
        $body_ar = $config['show_body'] ? trim($_POST['body_ar'] ?? '') : null;
        $image = $config['show_image'] ? trim($_POST['image'] ?? '') : null;
        $is_enabled = isset($_POST['is_enabled']) ? 1 : 0;
        $display_order = (int)($_POST['display_order'] ?? 1);

        try {
            $stmt = $pdo->prepare("SELECT id FROM page_sections WHERE page_name = 'tools' AND section_key = ? LIMIT 1");
            $stmt->execute([$section_key]);
            $section = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($section) {
                $stmt = $pdo->prepare("UPDATE page_sections SET title_en = ?, title_ar = ?, subtitle_en = ?, subtitle_ar = ?, body_en = ?, body_ar = ?, image = ?, is_enabled = ?, display_order = ? WHERE id = ?");
                $stmt->execute([$title_en, $title_ar, $subtitle_en, $subtitle_ar, $body_en, $body_ar, $image, $is_enabled, $display_order, $section['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO page_sections (page_name, section_key, title_en, title_ar, subtitle_en, subtitle_ar, body_en, body_ar, image, is_enabled, display_order) VALUES ('tools', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$section_key, $title_en, $title_ar, $subtitle_en, $subtitle_ar, $body_en, $body_ar, $image, $is_enabled, $display_order]);
            }

            $success_message = 'Section saved successfully!';
            $active_tab = $section_key;
        } catch (PDOException $e) {
            $error_message = 'Database error: ' . $e->getMessage();
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM page_sections WHERE page_name = 'tools'");
$stmt->execute();
$tools_sections = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $tools_sections[$row['section_key']] = $row;
}

foreach ($section_configs as $key => $config) {
    if (!isset($tools_sections[$key])) {
        $tools_sections[$key] = [
            'title_en' => '',
            'title_ar' => '',
            'subtitle_en' => '',
            'subtitle_ar' => '',
            'body_en' => '',
            'body_ar' => '',
            'image' => '',
            'is_enabled' => 1,
            'display_order' => 1,
        ];
    }
}
?>

<div class="container-fluid mt-4">
    <div class="content-card">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
            <div>
                <h5 class="mb-1">Tools Landing Page</h5>
                <p class="text-muted mb-0">Control the content that appears on the Tools page before the dynamic tool cards.</p>
            </div>
            <?php renderBulkSaveToolbar(['wrapper_class' => 'mt-3 mt-md-0']); ?>
        </div>
        <?php if ($success_message): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success_message; ?></div>
        <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?></div>
    <?php endif; ?>

    <ul class="nav nav-tabs" role="tablist">
        <?php foreach ($section_configs as $key => $config): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $active_tab === $key ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#tab_<?php echo $key; ?>" type="button" role="tab">
                    <?php echo htmlspecialchars($config['label']); ?>
                </button>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="tab-content p-4 border border-top-0 rounded-bottom bg-white">
        <?php foreach ($section_configs as $key => $config): $section = $tools_sections[$key]; ?>
            <div class="tab-pane fade <?php echo $active_tab === $key ? 'show active' : ''; ?>" id="tab_<?php echo $key; ?>" role="tabpanel">
                <div class="mb-3">
                    <h6 class="mb-1"><?php echo htmlspecialchars($config['label']); ?></h6>
                    <p class="text-muted small mb-4"><?php echo htmlspecialchars($config['description']); ?></p>
                </div>

                <form method="POST" data-bulk-save="true" data-section-name="<?php echo htmlspecialchars($config['label']); ?>">
                    <input type="hidden" name="action" value="update_tools_section">
                    <input type="hidden" name="section_key" value="<?php echo $key; ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (English)</label>
                            <input type="text" class="form-control" name="title_en" value="<?php echo htmlspecialchars($section['title_en']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (Arabic)</label>
                            <input type="text" class="form-control" name="title_ar" value="<?php echo htmlspecialchars($section['title_ar']); ?>" required>
                        </div>
                    </div>

                    <?php if ($config['show_body']): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Body (English)</label>
                                <textarea class="form-control" name="body_en" rows="4"><?php echo htmlspecialchars($section['body_en']); ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Body (Arabic)</label>
                                <textarea class="form-control" name="body_ar" rows="4"><?php echo htmlspecialchars($section['body_ar']); ?></textarea>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Subtitle (English)</label>
                                <textarea class="form-control" name="subtitle_en" rows="3"><?php echo htmlspecialchars($section['subtitle_en']); ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Subtitle (Arabic)</label>
                                <textarea class="form-control" name="subtitle_ar" rows="3"><?php echo htmlspecialchars($section['subtitle_ar']); ?></textarea>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($config['show_image']): ?>
                        <div class="mb-3">
                            <label class="form-label">Background Image</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="image" id="image_<?php echo $key; ?>" value="<?php echo htmlspecialchars($section['image']); ?>">
                                <button type="button" class="btn btn-outline-secondary btn-choose-media" onclick="openMediaPicker('image_<?php echo $key; ?>', 'preview_<?php echo $key; ?>')">
                                    <i class="fas fa-images"></i> Choose
                                </button>
                            </div>
                            <div id="preview_<?php echo $key; ?>" class="image-preview-box <?php echo !empty($section['image']) ? 'active' : ''; ?>">
                                <img src="<?php echo htmlspecialchars($section['image']); ?>" alt="Preview">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-control" name="display_order" value="<?php echo (int)($section['display_order'] ?? 1); ?>">
                        </div>
                        <div class="col-md-4 mb-3 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="is_enabled" id="is_enabled_<?php echo $key; ?>" <?php echo !isset($section['is_enabled']) || $section['is_enabled'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_enabled_<?php echo $key; ?>">
                                    Section enabled
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary bulk-save-hidden">
                        <i class="fas fa-save"></i> Save Section
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
