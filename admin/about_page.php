<?php
$page_title = 'About Page Management';
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';

$section_configs = [
    'intro' => [
        'label' => 'Introduction',
        'description' => 'Top introduction block with supporting image.',
        'show_subtitle' => false,
        'show_image' => true,
    ],
    'mission' => [
        'label' => 'Our Mission',
        'description' => 'Mission statement with optional subtitle and image.',
        'show_subtitle' => true,
        'show_image' => true,
    ],
    'approach' => [
        'label' => 'Our Approach',
        'description' => 'Explains methodology with image support.',
        'show_subtitle' => true,
        'show_image' => true,
    ],
];

$active_tab = $_GET['tab'] ?? array_key_first($section_configs);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_about_section') {
    $section_key = $_POST['section_key'] ?? '';

    if (!isset($section_configs[$section_key])) {
        $error_message = 'Unknown section selected.';
    } else {
        $config = $section_configs[$section_key];
        $title_en = trim($_POST['title_en'] ?? '');
        $title_ar = trim($_POST['title_ar'] ?? '');
        $body_en = trim($_POST['body_en'] ?? '');
        $body_ar = trim($_POST['body_ar'] ?? '');
        $subtitle_en = $config['show_subtitle'] ? trim($_POST['subtitle_en'] ?? '') : null;
        $subtitle_ar = $config['show_subtitle'] ? trim($_POST['subtitle_ar'] ?? '') : null;
        $image = $config['show_image'] ? trim($_POST['image'] ?? '') : null;
        $is_enabled = isset($_POST['is_enabled']) ? 1 : 0;
        $display_order = (int)($_POST['display_order'] ?? 1);

        try {
            $stmt = $pdo->prepare("SELECT id FROM page_sections WHERE page_name = 'about' AND section_key = ? LIMIT 1");
            $stmt->execute([$section_key]);
            $section = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($section) {
                $stmt = $pdo->prepare("UPDATE page_sections SET title_en = ?, title_ar = ?, subtitle_en = ?, subtitle_ar = ?, body_en = ?, body_ar = ?, image = ?, is_enabled = ?, display_order = ? WHERE id = ?");
                $stmt->execute([$title_en, $title_ar, $subtitle_en, $subtitle_ar, $body_en, $body_ar, $image, $is_enabled, $display_order, $section['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO page_sections (page_name, section_key, title_en, title_ar, subtitle_en, subtitle_ar, body_en, body_ar, image, is_enabled, display_order) VALUES ('about', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$section_key, $title_en, $title_ar, $subtitle_en, $subtitle_ar, $body_en, $body_ar, $image, $is_enabled, $display_order]);
            }

            $success_message = 'Section updated successfully!';
            $active_tab = $section_key;
        } catch (PDOException $e) {
            $error_message = 'Database error: ' . $e->getMessage();
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM page_sections WHERE page_name = 'about'");
$stmt->execute();
$about_sections = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $about_sections[$row['section_key']] = $row;
}

// Ensure each configured section has a placeholder record for UI purposes
foreach ($section_configs as $key => $config) {
    if (!isset($about_sections[$key])) {
        $about_sections[$key] = [
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
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <div>
            <h2 class="mb-2"><i class="fas fa-info-circle"></i> About Page Management</h2>
            <p class="text-muted mb-0">Edit every block of the About Us page with tailored forms for each section.</p>
        </div>
        <?php renderBulkSaveToolbar(['wrapper_class' => 'mt-3 mt-md-0']); ?>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <ul class="nav nav-tabs" role="tablist">
        <?php foreach ($section_configs as $key => $config): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?php echo $active_tab === $key ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#tab-<?php echo htmlspecialchars($key); ?>" type="button" role="tab">
                    <?php echo htmlspecialchars($config['label']); ?>
                </button>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="tab-content border border-top-0 bg-white p-4">
        <?php foreach ($section_configs as $key => $config):
            $data = $about_sections[$key];
        ?>
            <div class="tab-pane fade <?php echo $active_tab === $key ? 'show active' : ''; ?>" id="tab-<?php echo htmlspecialchars($key); ?>" role="tabpanel">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 class="mb-1"><?php echo htmlspecialchars($config['label']); ?></h4>
                        <p class="text-muted small mb-0"><?php echo htmlspecialchars($config['description']); ?></p>
                    </div>
                    <span class="badge bg-light text-dark">Section Key: <?php echo htmlspecialchars($key); ?></span>
                </div>

                <form method="POST" action="?tab=<?php echo htmlspecialchars($key); ?>" data-bulk-save="true" data-section-name="<?php echo htmlspecialchars($config['label']); ?>">
                    <input type="hidden" name="action" value="update_about_section">
                    <input type="hidden" name="section_key" value="<?php echo htmlspecialchars($key); ?>">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (English)</label>
                            <input type="text" class="form-control" name="title_en" value="<?php echo htmlspecialchars($data['title_en'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (Arabic)</label>
                            <input type="text" class="form-control" name="title_ar" dir="rtl" value="<?php echo htmlspecialchars($data['title_ar'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <?php if ($config['show_subtitle']): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Subtitle (English)</label>
                                <input type="text" class="form-control" name="subtitle_en" value="<?php echo htmlspecialchars($data['subtitle_en'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Subtitle (Arabic)</label>
                                <input type="text" class="form-control" name="subtitle_ar" dir="rtl" value="<?php echo htmlspecialchars($data['subtitle_ar'] ?? ''); ?>">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (English)</label>
                            <textarea class="form-control" name="body_en" rows="6" required><?php echo htmlspecialchars($data['body_en'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (Arabic)</label>
                            <textarea class="form-control" name="body_ar" dir="rtl" rows="6" required><?php echo htmlspecialchars($data['body_ar'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <?php if ($config['show_image']): ?>
                        <div class="mb-3">
                            <label class="form-label">Section Image</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="about-image-<?php echo htmlspecialchars($key); ?>" name="image" value="<?php echo htmlspecialchars($data['image'] ?? ''); ?>">
                                <button type="button" class="btn btn-outline-secondary btn-choose-media" onclick="openMediaPicker('about-image-<?php echo htmlspecialchars($key); ?>', 'about-preview-<?php echo htmlspecialchars($key); ?>')">
                                    <i class="fas fa-images"></i> Choose
                                </button>
                            </div>
                            <div id="about-preview-<?php echo htmlspecialchars($key); ?>" class="image-preview-box <?php echo !empty($data['image']) ? 'active' : ''; ?>">
                                <img src="<?php echo !empty($data['image']) ? htmlspecialchars($data['image']) : ''; ?>" alt="Section preview">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-control" name="display_order" value="<?php echo htmlspecialchars($data['display_order'] ?? 1); ?>">
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_enabled" id="about-enabled-<?php echo htmlspecialchars($key); ?>" <?php echo !empty($data['is_enabled']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="about-enabled-<?php echo htmlspecialchars($key); ?>">
                                    Display this section
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary bulk-save-hidden"><i class="fas fa-save"></i> Save Changes</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>