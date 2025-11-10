<?php
$page_title = 'Training Program Management';
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';
$active_tab = $_GET['tab'] ?? 'hero';

// Hero Section Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_hero') {
    $title_en = $_POST['title_en'] ?? '';
    $title_ar = $_POST['title_ar'] ?? '';
    $subtitle_en = $_POST['subtitle_en'] ?? '';
    $subtitle_ar = $_POST['subtitle_ar'] ?? '';
    $hero_image = trim($_POST['hero_image'] ?? '');
    $hero_image_alt = trim($_POST['hero_image_alt'] ?? '');
    $cta_label_en = $_POST['cta_label_en'] ?? '';
    $cta_label_ar = $_POST['cta_label_ar'] ?? '';
    $cta_link = $_POST['cta_link'] ?? '#contact';
    $is_enabled = isset($_POST['is_enabled']) ? 1 : 0;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM training_hero");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        $stmt = $pdo->prepare("UPDATE training_hero SET title_en = ?, title_ar = ?, subtitle_en = ?, subtitle_ar = ?, hero_image = ?, hero_image_alt = ?, cta_label_en = ?, cta_label_ar = ?, cta_link = ?, is_enabled = ? WHERE id = (SELECT MIN(id) FROM training_hero)");
        $stmt->execute([$title_en, $title_ar, $subtitle_en, $subtitle_ar, $hero_image, $hero_image_alt, $cta_label_en, $cta_label_ar, $cta_link, $is_enabled]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO training_hero (title_en, title_ar, subtitle_en, subtitle_ar, hero_image, hero_image_alt, cta_label_en, cta_label_ar, cta_link, is_enabled) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title_en, $title_ar, $subtitle_en, $subtitle_ar, $hero_image, $hero_image_alt, $cta_label_en, $cta_label_ar, $cta_link, $is_enabled]);
    }
    
    $success_message = 'Hero section updated successfully!';
    $active_tab = 'hero';
}

// What You'll Learn Section Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_learn_section') {
    $stmt = $pdo->prepare("UPDATE tp_learn_section SET title_en = ?, title_ar = ?, subtitle_en = ?, subtitle_ar = ?, is_published = ? WHERE id = (SELECT MIN(id) FROM tp_learn_section)");
    $stmt->execute([$_POST['title_en'], $_POST['title_ar'], $_POST['subtitle_en'], $_POST['subtitle_ar'], isset($_POST['is_published']) ? 1 : 0]);
    $success_message = 'Section details updated successfully!';
    $active_tab = 'learn';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_learn_item') {
    $stmt = $pdo->prepare("INSERT INTO training_learn_items (title_en, title_ar, body_en, body_ar, icon_image, sort_order, published) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['title_en'], $_POST['title_ar'], $_POST['body_en'], $_POST['body_ar'], $_POST['icon_image'], intval($_POST['sort_order']), isset($_POST['published']) ? 1 : 0]);
    $success_message = 'Learn item added successfully!';
    $active_tab = 'learn';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_learn_item') {
    $stmt = $pdo->prepare("UPDATE training_learn_items SET title_en = ?, title_ar = ?, body_en = ?, body_ar = ?, icon_image = ?, sort_order = ?, published = ? WHERE id = ?");
    $stmt->execute([$_POST['title_en'], $_POST['title_ar'], $_POST['body_en'], $_POST['body_ar'], $_POST['icon_image'], intval($_POST['sort_order']), isset($_POST['published']) ? 1 : 0, intval($_POST['id'])]);
    $success_message = 'Learn item updated successfully!';
    $active_tab = 'learn';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_learn_item') {
    $stmt = $pdo->prepare("DELETE FROM training_learn_items WHERE id = ?");
    $stmt->execute([intval($_POST['id'])]);
    $success_message = 'Learn item deleted successfully!';
    $active_tab = 'learn';
}

// Value & Bonuses Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_bonus') {
    $stmt = $pdo->prepare("INSERT INTO training_bonuses (title_en, title_ar, subtitle_en, subtitle_ar, body_en, body_ar, image, sort_order, published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['title_en'], $_POST['title_ar'], $_POST['subtitle_en'] ?? '', $_POST['subtitle_ar'] ?? '', $_POST['body_en'], $_POST['body_ar'], $_POST['image'] ?? '', intval($_POST['sort_order']), isset($_POST['published']) ? 1 : 0]);
    $success_message = 'Bonus item added successfully!';
    $active_tab = 'bonuses';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_bonus') {
    $stmt = $pdo->prepare("UPDATE training_bonuses SET title_en = ?, title_ar = ?, subtitle_en = ?, subtitle_ar = ?, body_en = ?, body_ar = ?, image = ?, sort_order = ?, published = ? WHERE id = ?");
    $stmt->execute([$_POST['title_en'], $_POST['title_ar'], $_POST['subtitle_en'] ?? '', $_POST['subtitle_ar'] ?? '', $_POST['body_en'], $_POST['body_ar'], $_POST['image'] ?? '', intval($_POST['sort_order']), isset($_POST['published']) ? 1 : 0, intval($_POST['id'])]);
    $success_message = 'Bonus item updated successfully!';
    $active_tab = 'bonuses';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_bonus') {
    $stmt = $pdo->prepare("DELETE FROM training_bonuses WHERE id = ?");
    $stmt->execute([intval($_POST['id'])]);
    $success_message = 'Bonus item deleted successfully!';
    $active_tab = 'bonuses';
}

// Outcomes Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_outcome') {
    $stmt = $pdo->prepare("INSERT INTO training_outcomes (title_en, title_ar, body_en, body_ar, icon_image, sort_order, published) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['title_en'], $_POST['title_ar'], $_POST['body_en'] ?? '', $_POST['body_ar'] ?? '', $_POST['icon_image'] ?? '', intval($_POST['sort_order']), isset($_POST['published']) ? 1 : 0]);
    $success_message = 'Outcome added successfully!';
    $active_tab = 'outcomes';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_outcome') {
    $stmt = $pdo->prepare("UPDATE training_outcomes SET title_en = ?, title_ar = ?, body_en = ?, body_ar = ?, icon_image = ?, sort_order = ?, published = ? WHERE id = ?");
    $stmt->execute([$_POST['title_en'], $_POST['title_ar'], $_POST['body_en'] ?? '', $_POST['body_ar'] ?? '', $_POST['icon_image'] ?? '', intval($_POST['sort_order']), isset($_POST['published']) ? 1 : 0, intval($_POST['id'])]);
    $success_message = 'Outcome updated successfully!';
    $active_tab = 'outcomes';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_outcome') {
    $stmt = $pdo->prepare("DELETE FROM training_outcomes WHERE id = ?");
    $stmt->execute([intval($_POST['id'])]);
    $success_message = 'Outcome deleted successfully!';
    $active_tab = 'outcomes';
}

// Students Journey Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_journey_video') {
    $video_url = $_POST['video_url'] ?? '';
    $is_enabled = isset($_POST['is_enabled']) ? 1 : 0;
    
    // Check if a record exists
    $check = $pdo->query("SELECT COUNT(*) FROM training_journey_video")->fetchColumn();
    
    if ($check > 0) {
        // Update existing record
        $stmt = $pdo->prepare("UPDATE training_journey_video SET video_url = ?, is_enabled = ? WHERE id = (SELECT MIN(id) FROM training_journey_video)");
        $stmt->execute([$video_url, $is_enabled]);
    } else {
        // Insert new record
        $stmt = $pdo->prepare("INSERT INTO training_journey_video (video_url, is_enabled) VALUES (?, ?)");
        $stmt->execute([$video_url, $is_enabled]);
    }
    
    $success_message = 'Journey video saved successfully!';
    $active_tab = 'journey';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_journey_card') {
    $stmt = $pdo->prepare("INSERT INTO training_journey_cards (name_en, name_ar, quote_en, quote_ar, icon_image, sort_order, published) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['name_en'], $_POST['name_ar'], $_POST['quote_en'], $_POST['quote_ar'], $_POST['icon_image'] ?? '', intval($_POST['sort_order']), isset($_POST['published']) ? 1 : 0]);
    $success_message = 'Journey card added successfully!';
    $active_tab = 'journey';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_journey_card') {
    $stmt = $pdo->prepare("UPDATE training_journey_cards SET name_en = ?, name_ar = ?, quote_en = ?, quote_ar = ?, icon_image = ?, sort_order = ?, published = ? WHERE id = ?");
    $stmt->execute([$_POST['name_en'], $_POST['name_ar'], $_POST['quote_en'], $_POST['quote_ar'], $_POST['icon_image'] ?? '', intval($_POST['sort_order']), isset($_POST['published']) ? 1 : 0, intval($_POST['id'])]);
    $success_message = 'Journey card updated successfully!';
    $active_tab = 'journey';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_journey_card') {
    $stmt = $pdo->prepare("DELETE FROM training_journey_cards WHERE id = ?");
    $stmt->execute([intval($_POST['id'])]);
    $success_message = 'Journey card deleted successfully!';
    $active_tab = 'journey';
}

// FAQ Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_faq') {
    $stmt = $pdo->prepare("INSERT INTO training_faq (question_en, question_ar, answer_en, answer_ar, sort_order, published) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['question_en'], $_POST['question_ar'], $_POST['answer_en'], $_POST['answer_ar'], intval($_POST['sort_order']), isset($_POST['published']) ? 1 : 0]);
    $success_message = 'FAQ added successfully!';
    $active_tab = 'faq';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_faq') {
    $stmt = $pdo->prepare("UPDATE training_faq SET question_en = ?, question_ar = ?, answer_en = ?, answer_ar = ?, sort_order = ?, published = ? WHERE id = ?");
    $stmt->execute([$_POST['question_en'], $_POST['question_ar'], $_POST['answer_en'], $_POST['answer_ar'], intval($_POST['sort_order']), isset($_POST['published']) ? 1 : 0, intval($_POST['id'])]);
    $success_message = 'FAQ updated successfully!';
    $active_tab = 'faq';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_faq') {
    $stmt = $pdo->prepare("DELETE FROM training_faq WHERE id = ?");
    $stmt->execute([intval($_POST['id'])]);
    $success_message = 'FAQ deleted successfully!';
    $active_tab = 'faq';
}

// Fetch data
$stmt = $pdo->query("SELECT * FROM training_hero LIMIT 1");
$hero = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM tp_learn_section LIMIT 1");
$learn_section = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM training_learn_items ORDER BY sort_order ASC");
$learn_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM training_bonuses ORDER BY sort_order ASC");
$bonuses = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM training_outcomes ORDER BY sort_order ASC");
$outcomes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM training_journey_video LIMIT 1");
$journey_video = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM training_journey_cards ORDER BY sort_order ASC");
$journey_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM training_faq ORDER BY sort_order ASC");
$faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-flag"></i> Training Program Management</h2>
            <p class="text-muted">Manage all sections of your Training Program page from one place</p>
        </div>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <ul class="nav nav-tabs" id="trainingProgramTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $active_tab === 'hero' ? 'active' : ''; ?>" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button" role="tab">
                <i class="fas fa-flag"></i> Hero
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $active_tab === 'learn' ? 'active' : ''; ?>" id="learn-tab" data-bs-toggle="tab" data-bs-target="#learn" type="button" role="tab">
                <i class="fas fa-lightbulb"></i> What You'll Learn
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $active_tab === 'bonuses' ? 'active' : ''; ?>" id="bonuses-tab" data-bs-toggle="tab" data-bs-target="#bonuses" type="button" role="tab">
                <i class="fas fa-gift"></i> Value & Bonuses
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $active_tab === 'outcomes' ? 'active' : ''; ?>" id="outcomes-tab" data-bs-toggle="tab" data-bs-target="#outcomes" type="button" role="tab">
                <i class="fas fa-trophy"></i> Outcomes
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $active_tab === 'journey' ? 'active' : ''; ?>" id="journey-tab" data-bs-toggle="tab" data-bs-target="#journey" type="button" role="tab">
                <i class="fas fa-road"></i> Students Journey
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $active_tab === 'faq' ? 'active' : ''; ?>" id="faq-tab" data-bs-toggle="tab" data-bs-target="#faq" type="button" role="tab">
                <i class="fas fa-question"></i> FAQ
            </button>
        </li>
    </ul>

    <div class="tab-content border border-top-0 p-4 bg-white" id="trainingProgramTabsContent">
        
        <!-- Hero Tab -->
        <div class="tab-pane fade <?php echo $active_tab === 'hero' ? 'show active' : ''; ?>" id="hero" role="tabpanel">
            <h4 class="mb-4">Hero Section</h4>
            <form method="POST" action="?tab=hero">
                <input type="hidden" name="action" value="update_hero">
                
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
                        <textarea class="form-control" name="subtitle_en" rows="2"><?php echo htmlspecialchars($hero['subtitle_en'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Subtitle (Arabic)</label>
                        <textarea class="form-control" name="subtitle_ar" dir="rtl" rows="2"><?php echo htmlspecialchars($hero['subtitle_ar'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hero Image</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="hero_image" name="hero_image" value="<?php echo htmlspecialchars($hero['hero_image'] ?? ''); ?>" placeholder="Image URL or path">
                        <button class="btn btn-outline-secondary" type="button" onclick="openMediaPicker('hero_image')">
                            <i class="fas fa-images"></i> Choose from Media
                        </button>
                    </div>
                    <small class="form-text text-muted">Optional - Displays opposite the text content</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Image ALT Text (Optional)</label>
                    <input type="text" class="form-control" name="hero_image_alt" value="<?php echo htmlspecialchars($hero['hero_image_alt'] ?? ''); ?>" placeholder="Describe the image for accessibility">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">CTA Button Label (English)</label>
                        <input type="text" class="form-control" name="cta_label_en" value="<?php echo htmlspecialchars($hero['cta_label_en'] ?? 'Enroll Now'); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">CTA Button Label (Arabic)</label>
                        <input type="text" class="form-control" name="cta_label_ar" dir="rtl" value="<?php echo htmlspecialchars($hero['cta_label_ar'] ?? 'سجل الآن'); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">CTA Button Link</label>
                    <input type="text" class="form-control" name="cta_link" value="<?php echo htmlspecialchars($hero['cta_link'] ?? '#contact'); ?>" placeholder="#contact">
                    <small class="form-text text-muted">Use #contact to scroll to contact form, or enter a full URL</small>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_enabled" id="hero_enabled" <?php echo (!empty($hero['is_enabled'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="hero_enabled">
                            Display Hero Section
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Hero Section
                </button>
            </form>
        </div>

        <!-- What You'll Learn Tab -->
        <div class="tab-pane fade <?php echo $active_tab === 'learn' ? 'show active' : ''; ?>" id="learn" role="tabpanel">
            <h4 class="mb-4">What You'll Learn Section</h4>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Section Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="?tab=learn">
                        <input type="hidden" name="action" value="update_learn_section">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Section Title (English)</label>
                                <input type="text" class="form-control" name="title_en" value="<?php echo htmlspecialchars($learn_section['title_en'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Section Title (Arabic)</label>
                                <input type="text" class="form-control" name="title_ar" dir="rtl" value="<?php echo htmlspecialchars($learn_section['title_ar'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Section Subtitle (English)</label>
                                <textarea class="form-control" name="subtitle_en" rows="2"><?php echo htmlspecialchars($learn_section['subtitle_en'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Section Subtitle (Arabic)</label>
                                <textarea class="form-control" name="subtitle_ar" dir="rtl" rows="2"><?php echo htmlspecialchars($learn_section['subtitle_ar'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_published" id="learn_section_published" <?php echo (!empty($learn_section['is_published'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="learn_section_published">
                                    Display Section
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save"></i> Save Section Details
                        </button>
                    </form>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Learn Items</h5>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addLearnItemModal">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">Sort</th>
                            <th>Title (EN)</th>
                            <th>Title (AR)</th>
                            <th>Icon/Image</th>
                            <th style="width: 100px;">Published</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($learn_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['sort_order']); ?></td>
                            <td><?php echo htmlspecialchars($item['title_en']); ?></td>
                            <td dir="rtl"><?php echo htmlspecialchars($item['title_ar']); ?></td>
                            <td><?php echo htmlspecialchars(substr($item['icon_image'], 0, 30)); ?><?php echo strlen($item['icon_image']) > 30 ? '...' : ''; ?></td>
                            <td><span class="badge bg-<?php echo $item['published'] ? 'success' : 'secondary'; ?>"><?php echo $item['published'] ? 'Yes' : 'No'; ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editLearnItem(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="?tab=learn" style="display:inline;" onsubmit="return confirm('Delete this item?');">
                                    <input type="hidden" name="action" value="delete_learn_item">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Value & Bonuses Tab -->
        <div class="tab-pane fade <?php echo $active_tab === 'bonuses' ? 'show active' : ''; ?>" id="bonuses" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Value & Bonuses</h4>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addBonusModal">
                    <i class="fas fa-plus"></i> Add Bonus
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">Sort</th>
                            <th>Title (EN)</th>
                            <th>Subtitle (EN)</th>
                            <th style="width: 100px;">Published</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bonuses as $bonus): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($bonus['sort_order']); ?></td>
                            <td><?php echo htmlspecialchars($bonus['title_en']); ?></td>
                            <td><?php echo htmlspecialchars($bonus['subtitle_en']); ?></td>
                            <td><span class="badge bg-<?php echo $bonus['published'] ? 'success' : 'secondary'; ?>"><?php echo $bonus['published'] ? 'Yes' : 'No'; ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editBonus(<?php echo htmlspecialchars(json_encode($bonus)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="?tab=bonuses" style="display:inline;" onsubmit="return confirm('Delete this bonus?');">
                                    <input type="hidden" name="action" value="delete_bonus">
                                    <input type="hidden" name="id" value="<?php echo $bonus['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Outcomes Tab -->
        <div class="tab-pane fade <?php echo $active_tab === 'outcomes' ? 'show active' : ''; ?>" id="outcomes" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Student Outcomes</h4>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addOutcomeModal">
                    <i class="fas fa-plus"></i> Add Outcome
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">Sort</th>
                            <th>Title (EN)</th>
                            <th>Title (AR)</th>
                            <th style="width: 100px;">Published</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($outcomes as $outcome): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($outcome['sort_order']); ?></td>
                            <td><?php echo htmlspecialchars($outcome['title_en']); ?></td>
                            <td dir="rtl"><?php echo htmlspecialchars($outcome['title_ar']); ?></td>
                            <td><span class="badge bg-<?php echo $outcome['published'] ? 'success' : 'secondary'; ?>"><?php echo $outcome['published'] ? 'Yes' : 'No'; ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editOutcome(<?php echo htmlspecialchars(json_encode($outcome)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="?tab=outcomes" style="display:inline;" onsubmit="return confirm('Delete this outcome?');">
                                    <input type="hidden" name="action" value="delete_outcome">
                                    <input type="hidden" name="id" value="<?php echo $outcome['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Students Journey Tab -->
        <div class="tab-pane fade <?php echo $active_tab === 'journey' ? 'show active' : ''; ?>" id="journey" role="tabpanel">
            <h4 class="mb-4">Our Students</h4>
            
            <!-- Video Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-video"></i> Journey Video</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="?tab=journey">
                        <input type="hidden" name="action" value="update_journey_video">
                        <div class="mb-3">
                            <label class="form-label">Video URL</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="journey_video_url" name="video_url" value="<?php echo htmlspecialchars($journey_video['video_url'] ?? ''); ?>" placeholder="YouTube URL or direct video file URL">
                                <button class="btn btn-outline-secondary" type="button" onclick="openMediaPicker('journey_video_url')">
                                    <i class="fas fa-images"></i> Media Library
                                </button>
                            </div>
                            <small class="form-text text-muted">Supports YouTube links or direct video file URLs (.mp4, .webm, .ogg)</small>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_enabled" id="journey_video_enabled" <?php echo (!empty($journey_video['is_enabled'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="journey_video_enabled">
                                    Display Video
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save"></i> Save Video Settings
                        </button>
                    </form>
                </div>
            </div>

            <!-- Journey Cards Section -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5><i class="fas fa-id-card"></i> Student Journey Cards</h5>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addJourneyCardModal">
                    <i class="fas fa-plus"></i> Add Card
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">Sort</th>
                            <th>Name (EN)</th>
                            <th>Name (AR)</th>
                            <th>Quote Preview</th>
                            <th style="width: 100px;">Published</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($journey_cards as $card): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($card['sort_order']); ?></td>
                            <td><?php echo htmlspecialchars($card['name_en']); ?></td>
                            <td dir="rtl"><?php echo htmlspecialchars($card['name_ar']); ?></td>
                            <td><?php echo htmlspecialchars(substr($card['quote_en'], 0, 40)); ?>...</td>
                            <td><span class="badge bg-<?php echo $card['published'] ? 'success' : 'secondary'; ?>"><?php echo $card['published'] ? 'Yes' : 'No'; ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editJourneyCard(<?php echo htmlspecialchars(json_encode($card)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="?tab=journey" style="display:inline;" onsubmit="return confirm('Delete this card?');">
                                    <input type="hidden" name="action" value="delete_journey_card">
                                    <input type="hidden" name="id" value="<?php echo $card['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FAQ Tab -->
        <div class="tab-pane fade <?php echo $active_tab === 'faq' ? 'show active' : ''; ?>" id="faq" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Frequently Asked Questions</h4>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                    <i class="fas fa-plus"></i> Add FAQ
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">Sort</th>
                            <th>Question (EN)</th>
                            <th>Question (AR)</th>
                            <th style="width: 100px;">Published</th>
                            <th style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($faqs as $faq): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($faq['sort_order']); ?></td>
                            <td><?php echo htmlspecialchars($faq['question_en']); ?></td>
                            <td dir="rtl"><?php echo htmlspecialchars($faq['question_ar']); ?></td>
                            <td><span class="badge bg-<?php echo $faq['published'] ? 'success' : 'secondary'; ?>"><?php echo $faq['published'] ? 'Yes' : 'No'; ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editFaq(<?php echo htmlspecialchars(json_encode($faq)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="?tab=faq" style="display:inline;" onsubmit="return confirm('Delete this FAQ?');">
                                    <input type="hidden" name="action" value="delete_faq">
                                    <input type="hidden" name="id" value="<?php echo $faq['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Add Learn Item Modal -->
<div class="modal fade" id="addLearnItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Learn Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?tab=learn">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_learn_item">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (English) *</label>
                            <input type="text" class="form-control" name="title_en" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (Arabic) *</label>
                            <input type="text" class="form-control" name="title_ar" dir="rtl" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (English)</label>
                            <textarea class="form-control" name="body_en" rows="3"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (Arabic)</label>
                            <textarea class="form-control" name="body_ar" dir="rtl" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Icon Class or Image URL</label>
                            <input type="text" class="form-control" id="add_learn_icon" name="icon_image" placeholder="fas fa-chart-line or image URL">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sort Order *</label>
                            <input type="number" class="form-control" name="sort_order" value="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="published" id="add_learn_published" checked>
                            <label class="form-check-label" for="add_learn_published">Published</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Learn Item Modal -->
<div class="modal fade" id="editLearnItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Learn Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?tab=learn">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_learn_item">
                    <input type="hidden" name="id" id="edit_learn_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (English) *</label>
                            <input type="text" class="form-control" name="title_en" id="edit_learn_title_en" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (Arabic) *</label>
                            <input type="text" class="form-control" name="title_ar" id="edit_learn_title_ar" dir="rtl" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (English)</label>
                            <textarea class="form-control" name="body_en" id="edit_learn_body_en" rows="3"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (Arabic)</label>
                            <textarea class="form-control" name="body_ar" id="edit_learn_body_ar" dir="rtl" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Icon Class or Image URL</label>
                            <input type="text" class="form-control" name="icon_image" id="edit_learn_icon">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sort Order *</label>
                            <input type="number" class="form-control" name="sort_order" id="edit_learn_sort" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="published" id="edit_learn_published">
                            <label class="form-check-label" for="edit_learn_published">Published</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Bonus Modal -->
<div class="modal fade" id="addBonusModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Bonus Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?tab=bonuses">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_bonus">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (English) *</label>
                            <input type="text" class="form-control" name="title_en" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (Arabic) *</label>
                            <input type="text" class="form-control" name="title_ar" dir="rtl" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subtitle (English)</label>
                            <input type="text" class="form-control" name="subtitle_en">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subtitle (Arabic)</label>
                            <input type="text" class="form-control" name="subtitle_ar" dir="rtl">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (English)</label>
                            <textarea class="form-control" name="body_en" rows="3"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (Arabic)</label>
                            <textarea class="form-control" name="body_ar" dir="rtl" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Image URL</label>
                            <input type="text" class="form-control" id="add_bonus_image" name="image">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sort Order *</label>
                            <input type="number" class="form-control" name="sort_order" value="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="published" id="add_bonus_published" checked>
                            <label class="form-check-label" for="add_bonus_published">Published</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Bonus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Bonus Modal -->
<div class="modal fade" id="editBonusModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Bonus Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?tab=bonuses">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_bonus">
                    <input type="hidden" name="id" id="edit_bonus_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (English) *</label>
                            <input type="text" class="form-control" name="title_en" id="edit_bonus_title_en" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (Arabic) *</label>
                            <input type="text" class="form-control" name="title_ar" id="edit_bonus_title_ar" dir="rtl" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subtitle (English)</label>
                            <input type="text" class="form-control" name="subtitle_en" id="edit_bonus_subtitle_en">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subtitle (Arabic)</label>
                            <input type="text" class="form-control" name="subtitle_ar" id="edit_bonus_subtitle_ar" dir="rtl">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (English)</label>
                            <textarea class="form-control" name="body_en" id="edit_bonus_body_en" rows="3"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (Arabic)</label>
                            <textarea class="form-control" name="body_ar" id="edit_bonus_body_ar" dir="rtl" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Image URL</label>
                            <input type="text" class="form-control" name="image" id="edit_bonus_image">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sort Order *</label>
                            <input type="number" class="form-control" name="sort_order" id="edit_bonus_sort" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="published" id="edit_bonus_published">
                            <label class="form-check-label" for="edit_bonus_published">Published</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Similar modals for Outcomes, Testimonials, and FAQ... -->
<!-- Add Outcome Modal -->
<div class="modal fade" id="addOutcomeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Outcome</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?tab=outcomes">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_outcome">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (English) *</label>
                            <input type="text" class="form-control" name="title_en" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (Arabic) *</label>
                            <input type="text" class="form-control" name="title_ar" dir="rtl" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (English)</label>
                            <textarea class="form-control" name="body_en" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (Arabic)</label>
                            <textarea class="form-control" name="body_ar" dir="rtl" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Icon Class or Image URL</label>
                            <input type="text" class="form-control" name="icon_image">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sort Order *</label>
                            <input type="number" class="form-control" name="sort_order" value="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="published" checked>
                            <label class="form-check-label">Published</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Outcome</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Outcome Modal -->
<div class="modal fade" id="editOutcomeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Outcome</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?tab=outcomes">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_outcome">
                    <input type="hidden" name="id" id="edit_outcome_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (English) *</label>
                            <input type="text" class="form-control" name="title_en" id="edit_outcome_title_en" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (Arabic) *</label>
                            <input type="text" class="form-control" name="title_ar" id="edit_outcome_title_ar" dir="rtl" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (English)</label>
                            <textarea class="form-control" name="body_en" id="edit_outcome_body_en" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (Arabic)</label>
                            <textarea class="form-control" name="body_ar" id="edit_outcome_body_ar" dir="rtl" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Icon Class or Image URL</label>
                            <input type="text" class="form-control" name="icon_image" id="edit_outcome_icon">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sort Order *</label>
                            <input type="number" class="form-control" name="sort_order" id="edit_outcome_sort" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="published" id="edit_outcome_published">
                            <label class="form-check-label" for="edit_outcome_published">Published</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Journey Card Modal -->
<div class="modal fade" id="addJourneyCardModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Student Journey Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?tab=journey">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_journey_card">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Student Name (English) *</label>
                            <input type="text" class="form-control" name="name_en" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Student Name (Arabic) *</label>
                            <input type="text" class="form-control" name="name_ar" dir="rtl" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quote (English) *</label>
                            <textarea class="form-control" name="quote_en" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quote (Arabic) *</label>
                            <textarea class="form-control" name="quote_ar" dir="rtl" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Icon/Image URL</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="add_journey_icon" name="icon_image" placeholder="Image URL or leave empty for default icon">
                                <button class="btn btn-outline-secondary" type="button" onclick="openMediaPicker('add_journey_icon')">
                                    <i class="fas fa-images"></i> Choose Media
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sort Order *</label>
                            <input type="number" class="form-control" name="sort_order" value="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="published" id="add_journey_published" checked>
                            <label class="form-check-label" for="add_journey_published">Published</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Card</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Journey Card Modal -->
<div class="modal fade" id="editJourneyCardModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Student Journey Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?tab=journey">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_journey_card">
                    <input type="hidden" name="id" id="edit_journey_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Student Name (English) *</label>
                            <input type="text" class="form-control" name="name_en" id="edit_journey_name_en" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Student Name (Arabic) *</label>
                            <input type="text" class="form-control" name="name_ar" id="edit_journey_name_ar" dir="rtl" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quote (English) *</label>
                            <textarea class="form-control" name="quote_en" id="edit_journey_quote_en" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quote (Arabic) *</label>
                            <textarea class="form-control" name="quote_ar" id="edit_journey_quote_ar" dir="rtl" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">Icon/Image URL</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="icon_image" id="edit_journey_icon">
                                <button class="btn btn-outline-secondary" type="button" onclick="openMediaPicker('edit_journey_icon')">
                                    <i class="fas fa-images"></i> Choose Media
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sort Order *</label>
                            <input type="number" class="form-control" name="sort_order" id="edit_journey_sort" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="published" id="edit_journey_published">
                            <label class="form-check-label" for="edit_journey_published">Published</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add FAQ Modal -->
<div class="modal fade" id="addFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?tab=faq">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_faq">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Question (English) *</label>
                            <input type="text" class="form-control" name="question_en" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Question (Arabic) *</label>
                            <input type="text" class="form-control" name="question_ar" dir="rtl" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Answer (English) *</label>
                            <textarea class="form-control" name="answer_en" rows="4" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Answer (Arabic) *</label>
                            <textarea class="form-control" name="answer_ar" dir="rtl" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order *</label>
                        <input type="number" class="form-control" name="sort_order" value="0" required style="width: 150px;">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="published" checked>
                            <label class="form-check-label">Published</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit FAQ Modal -->
<div class="modal fade" id="editFaqModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?tab=faq">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_faq">
                    <input type="hidden" name="id" id="edit_faq_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Question (English) *</label>
                            <input type="text" class="form-control" name="question_en" id="edit_faq_question_en" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Question (Arabic) *</label>
                            <input type="text" class="form-control" name="question_ar" id="edit_faq_question_ar" dir="rtl" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Answer (English) *</label>
                            <textarea class="form-control" name="answer_en" id="edit_faq_answer_en" rows="4" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Answer (Arabic) *</label>
                            <textarea class="form-control" name="answer_ar" id="edit_faq_answer_ar" dir="rtl" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order *</label>
                        <input type="number" class="form-control" name="sort_order" id="edit_faq_sort" required style="width: 150px;">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="published" id="edit_faq_published">
                            <label class="form-check-label" for="edit_faq_published">Published</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Media Picker Integration
function openMediaPicker(targetField) {
    window.open('media_upload.php?picker=1&target=' + targetField, 'MediaPicker', 'width=900,height=600');
}

window.selectMedia = function(url, targetField) {
    document.getElementById(targetField).value = url;
};

// Edit Functions
function editLearnItem(item) {
    document.getElementById('edit_learn_id').value = item.id;
    document.getElementById('edit_learn_title_en').value = item.title_en;
    document.getElementById('edit_learn_title_ar').value = item.title_ar;
    document.getElementById('edit_learn_body_en').value = item.body_en;
    document.getElementById('edit_learn_body_ar').value = item.body_ar;
    document.getElementById('edit_learn_icon').value = item.icon_image;
    document.getElementById('edit_learn_sort').value = item.sort_order;
    document.getElementById('edit_learn_published').checked = item.published == 1;
    new bootstrap.Modal(document.getElementById('editLearnItemModal')).show();
}

function editBonus(item) {
    document.getElementById('edit_bonus_id').value = item.id;
    document.getElementById('edit_bonus_title_en').value = item.title_en;
    document.getElementById('edit_bonus_title_ar').value = item.title_ar;
    document.getElementById('edit_bonus_subtitle_en').value = item.subtitle_en;
    document.getElementById('edit_bonus_subtitle_ar').value = item.subtitle_ar;
    document.getElementById('edit_bonus_body_en').value = item.body_en;
    document.getElementById('edit_bonus_body_ar').value = item.body_ar;
    document.getElementById('edit_bonus_image').value = item.image;
    document.getElementById('edit_bonus_sort').value = item.sort_order;
    document.getElementById('edit_bonus_published').checked = item.published == 1;
    new bootstrap.Modal(document.getElementById('editBonusModal')).show();
}

function editOutcome(item) {
    document.getElementById('edit_outcome_id').value = item.id;
    document.getElementById('edit_outcome_title_en').value = item.title_en;
    document.getElementById('edit_outcome_title_ar').value = item.title_ar;
    document.getElementById('edit_outcome_body_en').value = item.body_en || '';
    document.getElementById('edit_outcome_body_ar').value = item.body_ar || '';
    document.getElementById('edit_outcome_icon').value = item.icon_image || '';
    document.getElementById('edit_outcome_sort').value = item.sort_order;
    document.getElementById('edit_outcome_published').checked = item.published == 1;
    new bootstrap.Modal(document.getElementById('editOutcomeModal')).show();
}

function editJourneyCard(item) {
    document.getElementById('edit_journey_id').value = item.id;
    document.getElementById('edit_journey_name_en').value = item.name_en;
    document.getElementById('edit_journey_name_ar').value = item.name_ar;
    document.getElementById('edit_journey_quote_en').value = item.quote_en;
    document.getElementById('edit_journey_quote_ar').value = item.quote_ar;
    document.getElementById('edit_journey_icon').value = item.icon_image || '';
    document.getElementById('edit_journey_sort').value = item.sort_order;
    document.getElementById('edit_journey_published').checked = item.published == 1;
    new bootstrap.Modal(document.getElementById('editJourneyCardModal')).show();
}

function editFaq(item) {
    document.getElementById('edit_faq_id').value = item.id;
    document.getElementById('edit_faq_question_en').value = item.question_en;
    document.getElementById('edit_faq_question_ar').value = item.question_ar;
    document.getElementById('edit_faq_answer_en').value = item.answer_en;
    document.getElementById('edit_faq_answer_ar').value = item.answer_ar;
    document.getElementById('edit_faq_sort').value = item.sort_order;
    document.getElementById('edit_faq_published').checked = item.published == 1;
    new bootstrap.Modal(document.getElementById('editFaqModal')).show();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
