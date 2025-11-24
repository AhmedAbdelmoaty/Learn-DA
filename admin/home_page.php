<?php
$page_title = 'Home Page Management';
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';
$active_tab = $_GET['tab'] ?? 'hero';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    try {
        switch ($action) {
            case 'update_home_hero':
                $title_en = trim($_POST['title_en'] ?? '');
                $title_ar = trim($_POST['title_ar'] ?? '');
                $subtitle_en = trim($_POST['subtitle_en'] ?? '');
                $subtitle_ar = trim($_POST['subtitle_ar'] ?? '');
                $button_text_en = trim($_POST['button_text_en'] ?? '');
                $button_text_ar = trim($_POST['button_text_ar'] ?? '');
                $background_image = trim($_POST['background_image'] ?? '');

                $stmt = $pdo->query("SELECT COUNT(*) FROM hero_section");
                $has_record = $stmt->fetchColumn() > 0;

                if ($has_record) {
                    $stmt = $pdo->prepare("UPDATE hero_section SET title_en = ?, title_ar = ?, subtitle_en = ?, subtitle_ar = ?, button_text_en = ?, button_text_ar = ?, background_image = ? WHERE id = (SELECT MIN(id) FROM hero_section)");
                    $stmt->execute([$title_en, $title_ar, $subtitle_en, $subtitle_ar, $button_text_en, $button_text_ar, $background_image]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO hero_section (title_en, title_ar, subtitle_en, subtitle_ar, button_text_en, button_text_ar, background_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title_en, $title_ar, $subtitle_en, $subtitle_ar, $button_text_en, $button_text_ar, $background_image]);
                }

                $success_message = 'Hero section saved successfully!';
                $active_tab = 'hero';
                break;

            case 'update_why_data':
                $title_en = trim($_POST['title_en'] ?? '');
                $title_ar = trim($_POST['title_ar'] ?? '');
                $body_en = trim($_POST['body_en'] ?? '');
                $body_ar = trim($_POST['body_ar'] ?? '');
                $is_enabled = isset($_POST['is_enabled']) ? 1 : 0;
                $display_order = (int)($_POST['display_order'] ?? 1);

                $stmt = $pdo->prepare("SELECT id FROM page_sections WHERE page_name = 'home' AND section_key = 'why_data' LIMIT 1");
                $stmt->execute();
                $section = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($section) {
                    $stmt = $pdo->prepare("UPDATE page_sections SET title_en = ?, title_ar = ?, body_en = ?, body_ar = ?, is_enabled = ?, display_order = ? WHERE id = ?");
                    $stmt->execute([$title_en, $title_ar, $body_en, $body_ar, $is_enabled, $display_order, $section['id']]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO page_sections (page_name, section_key, title_en, title_ar, body_en, body_ar, is_enabled, display_order) VALUES ('home', 'why_data', ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title_en, $title_ar, $body_en, $body_ar, $is_enabled, $display_order]);
                }

                $success_message = 'Why Data Analysis section updated successfully!';
                $active_tab = 'why_data';
                break;

            case 'add_benefit':
                $stmt = $pdo->prepare("INSERT INTO benefits (icon, title_en, title_ar, description_en, description_ar, display_order) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    trim($_POST['icon'] ?? ''),
                    trim($_POST['title_en'] ?? ''),
                    trim($_POST['title_ar'] ?? ''),
                    trim($_POST['description_en'] ?? ''),
                    trim($_POST['description_ar'] ?? ''),
                    (int)($_POST['display_order'] ?? 0)
                ]);
                $success_message = 'Benefit added successfully!';
                $active_tab = 'benefits';
                break;

            case 'update_benefit':
                $stmt = $pdo->prepare("UPDATE benefits SET icon = ?, title_en = ?, title_ar = ?, description_en = ?, description_ar = ?, display_order = ? WHERE id = ?");
                $stmt->execute([
                    trim($_POST['icon'] ?? ''),
                    trim($_POST['title_en'] ?? ''),
                    trim($_POST['title_ar'] ?? ''),
                    trim($_POST['description_en'] ?? ''),
                    trim($_POST['description_ar'] ?? ''),
                    (int)($_POST['display_order'] ?? 0),
                    (int)($_POST['id'] ?? 0)
                ]);
                $success_message = 'Benefit updated successfully!';
                $active_tab = 'benefits';
                break;

            case 'delete_benefit':
                $stmt = $pdo->prepare("DELETE FROM benefits WHERE id = ?");
                $stmt->execute([(int)($_POST['id'] ?? 0)]);
                $success_message = 'Benefit deleted successfully!';
                $active_tab = 'benefits';
                break;

            case 'add_testimonial':
                $stmt = $pdo->prepare("INSERT INTO testimonials (name_en, name_ar, content_en, content_ar, display_order) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    trim($_POST['name_en'] ?? ''),
                    trim($_POST['name_ar'] ?? ''),
                    trim($_POST['content_en'] ?? ''),
                    trim($_POST['content_ar'] ?? ''),
                    (int)($_POST['display_order'] ?? 0)
                ]);
                $success_message = 'Testimonial added successfully!';
                $active_tab = 'testimonials';
                break;

            case 'update_testimonial':
                $stmt = $pdo->prepare("UPDATE testimonials SET name_en = ?, name_ar = ?, content_en = ?, content_ar = ?, display_order = ? WHERE id = ?");
                $stmt->execute([
                    trim($_POST['name_en'] ?? ''),
                    trim($_POST['name_ar'] ?? ''),
                    trim($_POST['content_en'] ?? ''),
                    trim($_POST['content_ar'] ?? ''),
                    (int)($_POST['display_order'] ?? 0),
                    (int)($_POST['id'] ?? 0)
                ]);
                $success_message = 'Testimonial updated successfully!';
                $active_tab = 'testimonials';
                break;

            case 'delete_testimonial':
                $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
                $stmt->execute([(int)($_POST['id'] ?? 0)]);
                $success_message = 'Testimonial deleted successfully!';
                $active_tab = 'testimonials';
                break;

            default:
                $error_message = 'Unknown action requested.';
                break;
        }
    } catch (PDOException $e) {
        $error_message = 'Database error: ' . $e->getMessage();
    }
}

$hero_stmt = $pdo->query("SELECT * FROM hero_section ORDER BY id ASC LIMIT 1");
$hero = $hero_stmt->fetch(PDO::FETCH_ASSOC) ?: [];

$why_data_stmt = $pdo->prepare("SELECT * FROM page_sections WHERE page_name = 'home' AND section_key = 'why_data' LIMIT 1");
$why_data_stmt->execute();
$why_data_section = $why_data_stmt->fetch(PDO::FETCH_ASSOC) ?: [];

$benefits_stmt = $pdo->query("SELECT * FROM benefits ORDER BY display_order ASC, id ASC");
$benefits = $benefits_stmt->fetchAll(PDO::FETCH_ASSOC);

$testimonials_stmt = $pdo->query("SELECT * FROM testimonials ORDER BY display_order ASC, id ASC");
$testimonials = $testimonials_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container-fluid mt-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <h2 class="mb-0"><i class="fas fa-home"></i> Home Page Management</h2>
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
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $active_tab === 'hero' ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#hero" type="button" role="tab">
                <i class="fas fa-image"></i> Hero Banner
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $active_tab === 'why_data' ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#why-data" type="button" role="tab">
                <i class="fas fa-chart-line"></i> Why Data Analysis
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $active_tab === 'benefits' ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#benefits" type="button" role="tab">
                <i class="fas fa-star"></i> Key Benefits
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $active_tab === 'testimonials' ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#testimonials" type="button" role="tab">
                <i class="fas fa-quote-left"></i> Client Reviews
            </button>
        </li>
    </ul>

    <div class="tab-content border border-top-0 bg-white p-4">
        <div class="tab-pane fade <?php echo $active_tab === 'hero' ? 'show active' : ''; ?>" id="hero" role="tabpanel">
            <h4 class="mb-4">Hero Banner</h4>
            <form method="POST" action="?tab=hero" data-bulk-save="true" data-section-name="Hero Banner">
                <input type="hidden" name="action" value="update_home_hero">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title (English)</label>
                        <input type="text" class="form-control" name="title_en" value="<?php echo htmlspecialchars($hero['title_en'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title (Arabic)</label>
                        <input type="text" class="form-control" name="title_ar" dir="rtl" value="<?php echo htmlspecialchars($hero['title_ar'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Subtitle (English)</label>
                        <textarea class="form-control" name="subtitle_en" rows="3" required><?php echo htmlspecialchars($hero['subtitle_en'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Subtitle (Arabic)</label>
                        <textarea class="form-control" name="subtitle_ar" rows="3" dir="rtl" required><?php echo htmlspecialchars($hero['subtitle_ar'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Button Text (English)</label>
                        <input type="text" class="form-control" name="button_text_en" value="<?php echo htmlspecialchars($hero['button_text_en'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Button Text (Arabic)</label>
                        <input type="text" class="form-control" name="button_text_ar" dir="rtl" value="<?php echo htmlspecialchars($hero['button_text_ar'] ?? ''); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Background Image</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="home_hero_image" name="background_image" value="<?php echo htmlspecialchars($hero['background_image'] ?? ''); ?>">
                        <button type="button" class="btn btn-outline-secondary btn-choose-media" onclick="openMediaPicker('home_hero_image', 'home_hero_preview')">
                            <i class="fas fa-images"></i> Choose
                        </button>
                    </div>
                    <small class="text-muted">Recommended size: 1920x600px</small>
                    <div id="home_hero_preview" class="image-preview-box <?php echo !empty($hero['background_image']) ? 'active' : ''; ?>">
                        <img src="<?php echo !empty($hero['background_image']) ? htmlspecialchars($hero['background_image']) : ''; ?>" alt="Hero Background Preview">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary bulk-save-hidden"><i class="fas fa-save"></i> Save Hero Section</button>
            </form>
        </div>

        <div class="tab-pane fade <?php echo $active_tab === 'why_data' ? 'show active' : ''; ?>" id="why-data" role="tabpanel">
            <h4 class="mb-4">Why Data Analysis Section</h4>
            <form method="POST" action="?tab=why_data" data-bulk-save="true" data-section-name="Why Data Analysis">
                <input type="hidden" name="action" value="update_why_data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title (English)</label>
                        <input type="text" class="form-control" name="title_en" value="<?php echo htmlspecialchars($why_data_section['title_en'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title (Arabic)</label>
                        <input type="text" class="form-control" name="title_ar" dir="rtl" value="<?php echo htmlspecialchars($why_data_section['title_ar'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Body (English)</label>
                        <textarea class="form-control" name="body_en" rows="5" required><?php echo htmlspecialchars($why_data_section['body_en'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Body (Arabic)</label>
                        <textarea class="form-control" name="body_ar" dir="rtl" rows="5" required><?php echo htmlspecialchars($why_data_section['body_ar'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" class="form-control" name="display_order" value="<?php echo htmlspecialchars($why_data_section['display_order'] ?? 1); ?>">
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_enabled" id="why-data-enabled" <?php echo !empty($why_data_section['is_enabled']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="why-data-enabled">Display this section</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary bulk-save-hidden"><i class="fas fa-save"></i> Save Section</button>
            </form>
        </div>

        <div class="tab-pane fade <?php echo $active_tab === 'benefits' ? 'show active' : ''; ?>" id="benefits" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Key Benefits</h4>
                <span class="text-muted">Displayed on the home page in cards.</span>
            </div>

            <div class="content-card mb-4">
                <h5 class="mb-3">Add New Benefit</h5>
                <form method="POST" action="?tab=benefits">
                    <input type="hidden" name="action" value="add_benefit">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Icon Class or Image</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="icon" id="add_benefit_icon" placeholder="fa-solid fa-star or image URL" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="openMediaPicker('add_benefit_icon')">
                                    <i class="fas fa-images"></i> Choose
                                </button>
                            </div>
                            <div class="form-text">Enter a Font Awesome class or choose/upload an image.</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Title (English)</label>
                            <input type="text" class="form-control" name="title_en" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Title (Arabic)</label>
                            <input type="text" class="form-control" name="title_ar" dir="rtl" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Description (English)</label>
                            <textarea class="form-control" name="description_en" rows="2" required></textarea>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Description (Arabic)</label>
                            <textarea class="form-control" name="description_ar" rows="2" dir="rtl" required></textarea>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-control" name="display_order" value="<?php echo count($benefits) + 1; ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Add Benefit</button>
                </form>
            </div>

            <div class="content-card">
                <h5 class="mb-3">Existing Benefits</h5>
                <?php if (count($benefits) > 0): ?>
                    <?php foreach ($benefits as $benefit): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <form method="POST" action="?tab=benefits" class="row g-3 align-items-end" data-bulk-save="true" data-section-name="Benefit #<?php echo (int)$benefit['id']; ?>">
                                    <input type="hidden" name="action" value="update_benefit">
                                    <input type="hidden" name="id" value="<?php echo (int)$benefit['id']; ?>">
                                    <div class="col-md-2">
                                        <label class="form-label">Icon / Image</label>
                                        <div class="input-group input-group-sm">
                                            <?php $benefitInputId = 'benefit-icon-' . (int)$benefit['id']; ?>
                                            <input type="text" class="form-control" name="icon" id="<?php echo $benefitInputId; ?>" value="<?php echo htmlspecialchars($benefit['icon']); ?>" required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="openMediaPicker('<?php echo $benefitInputId; ?>')">
                                                <i class="fas fa-images"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Title (EN)</label>
                                        <input type="text" class="form-control form-control-sm" name="title_en" value="<?php echo htmlspecialchars($benefit['title_en']); ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Title (AR)</label>
                                        <input type="text" class="form-control form-control-sm" name="title_ar" dir="rtl" value="<?php echo htmlspecialchars($benefit['title_ar']); ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Order</label>
                                        <input type="number" class="form-control form-control-sm" name="display_order" value="<?php echo htmlspecialchars($benefit['display_order']); ?>" required>
                                    </div>
                                    <div class="col-md-2 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm bulk-save-hidden"><i class="fas fa-save"></i> Save</button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="submitDeleteForm('delete-benefit-<?php echo (int)$benefit['id']; ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Description (EN)</label>
                                        <textarea class="form-control form-control-sm" name="description_en" rows="2" required><?php echo htmlspecialchars($benefit['description_en']); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Description (AR)</label>
                                        <textarea class="form-control form-control-sm" name="description_ar" rows="2" dir="rtl" required><?php echo htmlspecialchars($benefit['description_ar']); ?></textarea>
                                    </div>
                                </form>
                                <form id="delete-benefit-<?php echo (int)$benefit['id']; ?>" method="POST" action="?tab=benefits" class="d-none">
                                    <input type="hidden" name="action" value="delete_benefit">
                                    <input type="hidden" name="id" value="<?php echo (int)$benefit['id']; ?>">
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No benefits added yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="tab-pane fade <?php echo $active_tab === 'testimonials' ? 'show active' : ''; ?>" id="testimonials" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Client Reviews</h4>
                <span class="text-muted">Displayed as testimonials on the home page.</span>
            </div>

            <div class="content-card mb-4">
                <h5 class="mb-3">Add New Review</h5>
                <form method="POST" action="?tab=testimonials">
                    <input type="hidden" name="action" value="add_testimonial">
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Name (English)</label>
                            <input type="text" class="form-control" name="name_en" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Name (Arabic)</label>
                            <input type="text" class="form-control" name="name_ar" dir="rtl" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-control" name="display_order" value="<?php echo count($testimonials) + 1; ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Testimonial (English)</label>
                            <textarea class="form-control" name="content_en" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Testimonial (Arabic)</label>
                            <textarea class="form-control" name="content_ar" rows="3" dir="rtl" required></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Add Review</button>
                </form>
            </div>

            <div class="content-card">
                <h5 class="mb-3">Existing Reviews</h5>
                <?php if (count($testimonials) > 0): ?>
                    <?php foreach ($testimonials as $testimonial): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <form method="POST" action="?tab=testimonials" class="row g-3" data-bulk-save="true" data-section-name="Testimonial #<?php echo (int)$testimonial['id']; ?>">
                                    <input type="hidden" name="action" value="update_testimonial">
                                    <input type="hidden" name="id" value="<?php echo (int)$testimonial['id']; ?>">
                                    <div class="col-md-4">
                                        <label class="form-label">Name (EN)</label>
                                        <input type="text" class="form-control form-control-sm" name="name_en" value="<?php echo htmlspecialchars($testimonial['name_en']); ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Name (AR)</label>
                                        <input type="text" class="form-control form-control-sm" name="name_ar" dir="rtl" value="<?php echo htmlspecialchars($testimonial['name_ar']); ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Order</label>
                                        <input type="number" class="form-control form-control-sm" name="display_order" value="<?php echo htmlspecialchars($testimonial['display_order']); ?>" required>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm bulk-save-hidden"><i class="fas fa-save"></i> Save</button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="submitDeleteForm('delete-testimonial-<?php echo (int)$testimonial['id']; ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Content (EN)</label>
                                        <textarea class="form-control form-control-sm" name="content_en" rows="3" required><?php echo htmlspecialchars($testimonial['content_en']); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Content (AR)</label>
                                        <textarea class="form-control form-control-sm" name="content_ar" rows="3" dir="rtl" required><?php echo htmlspecialchars($testimonial['content_ar']); ?></textarea>
                                    </div>
                                </form>
                                <form id="delete-testimonial-<?php echo (int)$testimonial['id']; ?>" method="POST" action="?tab=testimonials" class="d-none">
                                    <input type="hidden" name="action" value="delete_testimonial">
                                    <input type="hidden" name="id" value="<?php echo (int)$testimonial['id']; ?>">
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No testimonials added yet.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script>
function submitDeleteForm(formId) {
    const form = document.getElementById(formId);
    if (!form) {
        return;
    }
    if (confirm('Are you sure you want to delete this item?')) {
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>