<?php
$page_title = 'Course Details';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $heading_en = $_POST['heading_en'] ?? '';
    $heading_ar = $_POST['heading_ar'] ?? '';
    $duration_en = $_POST['duration_en'] ?? '';
    $duration_ar = $_POST['duration_ar'] ?? '';
    $format_en = $_POST['format_en'] ?? '';
    $format_ar = $_POST['format_ar'] ?? '';
    $fee_en = $_POST['fee_en'] ?? '';
    $fee_ar = $_POST['fee_ar'] ?? '';
    $modules_en = $_POST['modules_en'] ?? '';
    $modules_ar = $_POST['modules_ar'] ?? '';
    
    try {
        $stmt = $pdo->prepare("UPDATE course_details SET heading_en = ?, heading_ar = ?, duration_en = ?, duration_ar = ?, format_en = ?, format_ar = ?, fee_en = ?, fee_ar = ?, modules_en = ?, modules_ar = ? WHERE id = 1");
        $stmt->execute([$heading_en, $heading_ar, $duration_en, $duration_ar, $format_en, $format_ar, $fee_en, $fee_ar, $modules_en, $modules_ar]);
        $success = 'Course details updated successfully!';
    } catch(PDOException $e) {
        $error = 'Error updating course details: ' . $e->getMessage();
    }
}

$stmt = $pdo->query("SELECT * FROM course_details WHERE id = 1");
$course = $stmt->fetch();
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
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Heading</label>
                <input type="text" class="form-control" name="heading_en" value="<?php echo htmlspecialchars($course['heading_en'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Duration</label>
                <input type="text" class="form-control" name="duration_en" value="<?php echo htmlspecialchars($course['duration_en'] ?? ''); ?>" required>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Format</label>
                <input type="text" class="form-control" name="format_en" value="<?php echo htmlspecialchars($course['format_en'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Fee</label>
                <input type="text" class="form-control" name="fee_en" value="<?php echo htmlspecialchars($course['fee_en'] ?? ''); ?>" required>
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">Course Modules (one per line)</label>
            <textarea class="form-control" name="modules_en" rows="6" required><?php echo htmlspecialchars($course['modules_en'] ?? ''); ?></textarea>
        </div>
        
        <hr class="my-4">
        
        <h5 class="mb-3">Arabic Version (النسخة العربية)</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">العنوان</label>
                <input type="text" class="form-control" name="heading_ar" value="<?php echo htmlspecialchars($course['heading_ar'] ?? ''); ?>" required dir="rtl">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">المدة</label>
                <input type="text" class="form-control" name="duration_ar" value="<?php echo htmlspecialchars($course['duration_ar'] ?? ''); ?>" required dir="rtl">
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">التنسيق</label>
                <input type="text" class="form-control" name="format_ar" value="<?php echo htmlspecialchars($course['format_ar'] ?? ''); ?>" required dir="rtl">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">الرسوم</label>
                <input type="text" class="form-control" name="fee_ar" value="<?php echo htmlspecialchars($course['fee_ar'] ?? ''); ?>" required dir="rtl">
            </div>
        </div>
        
        <div class="mb-3">
            <label class="form-label">وحدات الدورة (واحد في كل سطر)</label>
            <textarea class="form-control" name="modules_ar" rows="6" required dir="rtl"><?php echo htmlspecialchars($course['modules_ar'] ?? ''); ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Changes
        </button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
