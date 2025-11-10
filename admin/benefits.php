<?php
$page_title = 'Key Benefits';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            try {
                $stmt = $pdo->prepare("INSERT INTO benefits (icon, title_en, title_ar, description_en, description_ar, display_order) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['icon'],
                    $_POST['title_en'],
                    $_POST['title_ar'],
                    $_POST['description_en'],
                    $_POST['description_ar'],
                    $_POST['display_order']
                ]);
                $success = 'Benefit added successfully!';
            } catch(PDOException $e) {
                $error = 'Error adding benefit: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'update') {
            try {
                $stmt = $pdo->prepare("UPDATE benefits SET icon = ?, title_en = ?, title_ar = ?, description_en = ?, description_ar = ?, display_order = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['icon'],
                    $_POST['title_en'],
                    $_POST['title_ar'],
                    $_POST['description_en'],
                    $_POST['description_ar'],
                    $_POST['display_order'],
                    $_POST['id']
                ]);
                $success = 'Benefit updated successfully!';
            } catch(PDOException $e) {
                $error = 'Error updating benefit: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'delete') {
            try {
                $stmt = $pdo->prepare("DELETE FROM benefits WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $success = 'Benefit deleted successfully!';
            } catch(PDOException $e) {
                $error = 'Error deleting benefit: ' . $e->getMessage();
            }
        }
    }
}

$stmt = $pdo->query("SELECT * FROM benefits ORDER BY display_order");
$benefits = $stmt->fetchAll();
?>

<div class="content-card mb-4">
    <h5 class="mb-3">Add New Benefit</h5>
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Icon (Font Awesome class)</label>
                <input type="text" class="form-control" name="icon" placeholder="fa-solid fa-laptop-code" required>
                <small class="text-muted">Example: fa-solid fa-star</small>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Title (English)</label>
                <input type="text" class="form-control" name="title_en" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">العنوان (عربي)</label>
                <input type="text" class="form-control" name="title_ar" required dir="rtl">
            </div>
        </div>
        <div class="row">
            <div class="col-md-5 mb-3">
                <label class="form-label">Description (English)</label>
                <textarea class="form-control" name="description_en" rows="2" required></textarea>
            </div>
            <div class="col-md-5 mb-3">
                <label class="form-label">الوصف (عربي)</label>
                <textarea class="form-control" name="description_ar" rows="2" required dir="rtl"></textarea>
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
                    <form method="POST" class="mb-2">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $benefit['id']; ?>">
                        <div class="row">
                            <div class="col-md-2 mb-2">
                                <label class="form-label">Icon</label>
                                <input type="text" class="form-control form-control-sm" name="icon" value="<?php echo htmlspecialchars($benefit['icon']); ?>" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Title (EN)</label>
                                <input type="text" class="form-control form-control-sm" name="title_en" value="<?php echo htmlspecialchars($benefit['title_en']); ?>" required>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">العنوان (AR)</label>
                                <input type="text" class="form-control form-control-sm" name="title_ar" value="<?php echo htmlspecialchars($benefit['title_ar']); ?>" required dir="rtl">
                            </div>
                            <div class="col-md-1 mb-2">
                                <label class="form-label">Order</label>
                                <input type="number" class="form-control form-control-sm" name="display_order" value="<?php echo $benefit['display_order']; ?>" required>
                            </div>
                            <div class="col-md-3 mb-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-save"></i> Update</button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteBenefit(<?php echo $benefit['id']; ?>)"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Description (EN)</label>
                                <textarea class="form-control form-control-sm" name="description_en" rows="2" required><?php echo htmlspecialchars($benefit['description_en']); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الوصف (AR)</label>
                                <textarea class="form-control form-control-sm" name="description_ar" rows="2" required dir="rtl"><?php echo htmlspecialchars($benefit['description_ar']); ?></textarea>
                            </div>
                        </div>
                    </form>
                    <form id="delete-form-<?php echo $benefit['id']; ?>" method="POST" style="display: none;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $benefit['id']; ?>">
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">No benefits added yet.</p>
    <?php endif; ?>
</div>

<script>
function deleteBenefit(id) {
    if (confirm('Are you sure you want to delete this benefit?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
