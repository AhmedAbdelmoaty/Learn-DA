<?php
$page_title = 'Testimonials';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            try {
                $stmt = $pdo->prepare("INSERT INTO testimonials (name_en, name_ar, content_en, content_ar, display_order) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['name_en'],
                    $_POST['name_ar'],
                    $_POST['content_en'],
                    $_POST['content_ar'],
                    $_POST['display_order']
                ]);
                $success = 'Testimonial added successfully!';
            } catch(PDOException $e) {
                $error = 'Error adding testimonial: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'update') {
            try {
                $stmt = $pdo->prepare("UPDATE testimonials SET name_en = ?, name_ar = ?, content_en = ?, content_ar = ?, display_order = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['name_en'],
                    $_POST['name_ar'],
                    $_POST['content_en'],
                    $_POST['content_ar'],
                    $_POST['display_order'],
                    $_POST['id']
                ]);
                $success = 'Testimonial updated successfully!';
            } catch(PDOException $e) {
                $error = 'Error updating testimonial: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'delete') {
            try {
                $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $success = 'Testimonial deleted successfully!';
            } catch(PDOException $e) {
                $error = 'Error deleting testimonial: ' . $e->getMessage();
            }
        }
    }
}

$stmt = $pdo->query("SELECT * FROM testimonials ORDER BY display_order");
$testimonials = $stmt->fetchAll();
?>

<div class="content-card mb-4">
    <h5 class="mb-3">Add New Testimonial</h5>
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="row">
            <div class="col-md-5 mb-3">
                <label class="form-label">Name (English)</label>
                <input type="text" class="form-control" name="name_en" required>
            </div>
            <div class="col-md-5 mb-3">
                <label class="form-label">الاسم (عربي)</label>
                <input type="text" class="form-control" name="name_ar" required dir="rtl">
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
                <label class="form-label">الشهادة (عربي)</label>
                <textarea class="form-control" name="content_ar" rows="3" required dir="rtl"></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Add Testimonial</button>
    </form>
</div>

<div class="content-card">
    <h5 class="mb-3">Existing Testimonials</h5>
    <?php if (count($testimonials) > 0): ?>
        <?php foreach ($testimonials as $testimonial): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Name (EN)</label>
                                <input type="text" class="form-control form-control-sm" name="name_en" value="<?php echo htmlspecialchars($testimonial['name_en']); ?>" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">الاسم (AR)</label>
                                <input type="text" class="form-control form-control-sm" name="name_ar" value="<?php echo htmlspecialchars($testimonial['name_ar']); ?>" required dir="rtl">
                            </div>
                            <div class="col-md-1 mb-2">
                                <label class="form-label">Order</label>
                                <input type="number" class="form-control form-control-sm" name="display_order" value="<?php echo $testimonial['display_order']; ?>" required>
                            </div>
                            <div class="col-md-3 mb-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-save"></i> Update</button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteTestimonial(<?php echo $testimonial['id']; ?>)"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Content (EN)</label>
                                <textarea class="form-control form-control-sm" name="content_en" rows="3" required><?php echo htmlspecialchars($testimonial['content_en']); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المحتوى (AR)</label>
                                <textarea class="form-control form-control-sm" name="content_ar" rows="3" required dir="rtl"><?php echo htmlspecialchars($testimonial['content_ar']); ?></textarea>
                            </div>
                        </div>
                    </form>
                    <form id="delete-form-<?php echo $testimonial['id']; ?>" method="POST" style="display: none;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">No testimonials added yet.</p>
    <?php endif; ?>
</div>

<script>
function deleteTestimonial(id) {
    if (confirm('Are you sure you want to delete this testimonial?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
