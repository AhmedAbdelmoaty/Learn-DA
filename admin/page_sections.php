<?php
$page_title = 'Manage Page Sections';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update') {
            try {
                $stmt = $pdo->prepare("UPDATE page_sections SET title_en = ?, title_ar = ?, subtitle_en = ?, subtitle_ar = ?, body_en = ?, body_ar = ?, image = ?, is_enabled = ?, display_order = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['title_en'],
                    $_POST['title_ar'],
                    $_POST['subtitle_en'],
                    $_POST['subtitle_ar'],
                    $_POST['body_en'],
                    $_POST['body_ar'],
                    $_POST['image'],
                    isset($_POST['is_enabled']) ? 1 : 0,
                    $_POST['display_order'],
                    $_POST['id']
                ]);
                $success = 'Section updated successfully!';
            } catch(PDOException $e) {
                $error = 'Error updating section: ' . $e->getMessage();
            }
        }
    }
}

$stmt = $pdo->query("SELECT * FROM page_sections WHERE page_name = 'home' ORDER BY display_order");
$home_sections = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM page_sections WHERE page_name = 'about' ORDER BY display_order");
$about_sections = $stmt->fetchAll();
?>

<div class="content-card">
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    
    <h5 class="mb-4">Manage Page Sections</h5>
    
    <!-- Home Page Sections -->
    <h6 class="text-primary mt-4 mb-3"><i class="fas fa-home"></i> Home Page Sections</h6>
    <?php foreach ($home_sections as $section): ?>
        <div class="card mb-3">
            <div class="card-header bg-light">
                <strong><?php echo htmlspecialchars($section['section_key']); ?></strong>
                <span class="badge bg-<?php echo $section['is_enabled'] ? 'success' : 'secondary'; ?> float-end">
                    <?php echo $section['is_enabled'] ? 'Enabled' : 'Disabled'; ?>
                </span>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $section['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (English)</label>
                            <input type="text" class="form-control" name="title_en" value="<?php echo htmlspecialchars($section['title_en']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (Arabic)</label>
                            <input type="text" class="form-control" name="title_ar" value="<?php echo htmlspecialchars($section['title_ar']); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (English)</label>
                            <textarea class="form-control" name="body_en" rows="3"><?php echo htmlspecialchars($section['body_en']); ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (Arabic)</label>
                            <textarea class="form-control" name="body_ar" rows="3"><?php echo htmlspecialchars($section['body_ar']); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_enabled" id="enabled_<?php echo $section['id']; ?>" <?php echo $section['is_enabled'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enabled_<?php echo $section['id']; ?>">
                                    Enable this section
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-control" name="display_order" value="<?php echo $section['display_order']; ?>">
                        </div>
                    </div>
                    
                    <input type="hidden" name="subtitle_en" value="">
                    <input type="hidden" name="subtitle_ar" value="">
                    <input type="hidden" name="image" value="">
                    
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Section</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
    
    <!-- About Page Sections -->
    <h6 class="text-primary mt-5 mb-3"><i class="fas fa-info-circle"></i> About Page Sections</h6>
    <?php foreach ($about_sections as $section): ?>
        <div class="card mb-3">
            <div class="card-header bg-light">
                <strong><?php echo htmlspecialchars($section['section_key']); ?></strong>
                <span class="badge bg-<?php echo $section['is_enabled'] ? 'success' : 'secondary'; ?> float-end">
                    <?php echo $section['is_enabled'] ? 'Enabled' : 'Disabled'; ?>
                </span>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $section['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (English)</label>
                            <input type="text" class="form-control" name="title_en" value="<?php echo htmlspecialchars($section['title_en']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (Arabic)</label>
                            <input type="text" class="form-control" name="title_ar" value="<?php echo htmlspecialchars($section['title_ar']); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subtitle (English)</label>
                            <input type="text" class="form-control" name="subtitle_en" value="<?php echo htmlspecialchars($section['subtitle_en']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Subtitle (Arabic)</label>
                            <input type="text" class="form-control" name="subtitle_ar" value="<?php echo htmlspecialchars($section['subtitle_ar']); ?>">
                        </div>
                    </div>
                    
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
                    
                    <div class="mb-3">
                        <label class="form-label">Image URL</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="image" id="section_image_<?php echo $section['id']; ?>" value="<?php echo htmlspecialchars($section['image']); ?>">
                            <button type="button" class="btn btn-outline-secondary btn-choose-media" onclick="openMediaPicker('section_image_<?php echo $section['id']; ?>', 'section_preview_<?php echo $section['id']; ?>')">
                                <i class="fas fa-images"></i> Choose
                            </button>
                        </div>
                        <div id="section_preview_<?php echo $section['id']; ?>" class="image-preview-box <?php echo !empty($section['image']) ? 'active' : ''; ?>">
                            <img src="<?php echo !empty($section['image']) ? htmlspecialchars($section['image']) : ''; ?>" alt="Preview">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_enabled" id="enabled_<?php echo $section['id']; ?>" <?php echo $section['is_enabled'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enabled_<?php echo $section['id']; ?>">
                                    Enable this section
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-control" name="display_order" value="<?php echo $section['display_order']; ?>">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Section</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
