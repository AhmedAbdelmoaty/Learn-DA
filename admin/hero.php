<?php
$page_title = 'Hero Section';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title_en = $_POST['title_en'] ?? '';
    $title_ar = $_POST['title_ar'] ?? '';
    $subtitle_en = $_POST['subtitle_en'] ?? '';
    $subtitle_ar = $_POST['subtitle_ar'] ?? '';
    $button_text_en = $_POST['button_text_en'] ?? '';
    $button_text_ar = $_POST['button_text_ar'] ?? '';
    $background_image = $_POST['background_image'] ?? '';
    
    try {
        $stmt = $pdo->prepare("UPDATE hero_section SET title_en = ?, title_ar = ?, subtitle_en = ?, subtitle_ar = ?, button_text_en = ?, button_text_ar = ?, background_image = ? WHERE id = 1");
        $stmt->execute([$title_en, $title_ar, $subtitle_en, $subtitle_ar, $button_text_en, $button_text_ar, $background_image]);
        $success = 'Hero section updated successfully!';
    } catch(PDOException $e) {
        $error = 'Error updating hero section: ' . $e->getMessage();
    }
}

$stmt = $pdo->query("SELECT * FROM hero_section WHERE id = 1");
$hero = $stmt->fetch();
?>

<div class="content-card">
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <h5 class="mb-3">English Version</h5>
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" class="form-control" name="title_en" value="<?php echo htmlspecialchars($hero['title_en'] ?? ''); ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Subtitle</label>
            <textarea class="form-control" name="subtitle_en" rows="3" required><?php echo htmlspecialchars($hero['subtitle_en'] ?? ''); ?></textarea>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Button Text</label>
            <input type="text" class="form-control" name="button_text_en" value="<?php echo htmlspecialchars($hero['button_text_en'] ?? ''); ?>" required>
        </div>
        
        <hr class="my-4">
        
        <h5 class="mb-3">Arabic Version (النسخة العربية)</h5>
        <div class="mb-3">
            <label class="form-label">العنوان</label>
            <input type="text" class="form-control" name="title_ar" value="<?php echo htmlspecialchars($hero['title_ar'] ?? ''); ?>" required dir="rtl">
        </div>
        
        <div class="mb-3">
            <label class="form-label">العنوان الفرعي</label>
            <textarea class="form-control" name="subtitle_ar" rows="3" required dir="rtl"><?php echo htmlspecialchars($hero['subtitle_ar'] ?? ''); ?></textarea>
        </div>
        
        <div class="mb-3">
            <label class="form-label">نص الزر</label>
            <input type="text" class="form-control" name="button_text_ar" value="<?php echo htmlspecialchars($hero['button_text_ar'] ?? ''); ?>" required dir="rtl">
        </div>
        
        <hr class="my-4">
        
        <h5 class="mb-3">Background Image</h5>
        <div class="mb-3">
            <label class="form-label">Image URL</label>
            <div class="input-group">
                <input type="url" class="form-control" id="background_image" name="background_image" value="<?php echo htmlspecialchars($hero['background_image'] ?? ''); ?>" required>
                <button type="button" class="btn btn-outline-secondary btn-choose-media" onclick="openMediaPicker('background_image', 'bg_preview')">
                    <i class="fas fa-images"></i> Choose
                </button>
            </div>
            <small class="text-muted">Recommended size: 1920x600px</small>
            <div id="bg_preview" class="image-preview-box <?php echo !empty($hero['background_image']) ? 'active' : ''; ?>">
                <img src="<?php echo !empty($hero['background_image']) ? htmlspecialchars($hero['background_image']) : ''; ?>" alt="Background Preview">
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Changes
        </button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
