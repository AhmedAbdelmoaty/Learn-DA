<?php
$page_title = 'About Course';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $heading_en = $_POST['heading_en'] ?? '';
    $heading_ar = $_POST['heading_ar'] ?? '';
    $content_en = $_POST['content_en'] ?? '';
    $content_ar = $_POST['content_ar'] ?? '';
    
    try {
        $stmt = $pdo->prepare("UPDATE about_section SET heading_en = ?, heading_ar = ?, content_en = ?, content_ar = ? WHERE id = 1");
        $stmt->execute([$heading_en, $heading_ar, $content_en, $content_ar]);
        $success = 'About section updated successfully!';
    } catch(PDOException $e) {
        $error = 'Error updating about section: ' . $e->getMessage();
    }
}

$stmt = $pdo->query("SELECT * FROM about_section WHERE id = 1");
$about = $stmt->fetch();
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
            <label class="form-label">Heading</label>
            <input type="text" class="form-control" name="heading_en" value="<?php echo htmlspecialchars($about['heading_en'] ?? ''); ?>" required>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea class="form-control" name="content_en" rows="5" required><?php echo htmlspecialchars($about['content_en'] ?? ''); ?></textarea>
        </div>
        
        <hr class="my-4">
        
        <h5 class="mb-3">Arabic Version (النسخة العربية)</h5>
        <div class="mb-3">
            <label class="form-label">العنوان</label>
            <input type="text" class="form-control" name="heading_ar" value="<?php echo htmlspecialchars($about['heading_ar'] ?? ''); ?>" required dir="rtl">
        </div>
        
        <div class="mb-3">
            <label class="form-label">المحتوى</label>
            <textarea class="form-control" name="content_ar" rows="5" required dir="rtl"><?php echo htmlspecialchars($about['content_ar'] ?? ''); ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Changes
        </button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
