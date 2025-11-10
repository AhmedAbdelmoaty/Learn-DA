<?php
$page_title = 'Manage Topics';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update') {
            try {
                $stmt = $pdo->prepare("UPDATE topics SET title_en = ?, title_ar = ?, intro_en = ?, intro_ar = ?, hero_image = ?, display_order = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['title_en'],
                    $_POST['title_ar'],
                    $_POST['intro_en'],
                    $_POST['intro_ar'],
                    $_POST['hero_image'],
                    $_POST['display_order'],
                    $_POST['id']
                ]);
                $success = 'Topic updated successfully!';
            } catch(PDOException $e) {
                $error = 'Error updating topic: ' . $e->getMessage();
            }
        }
    }
}

$stmt = $pdo->query("SELECT * FROM topics ORDER BY display_order");
$topics = $stmt->fetchAll();

// Get media files for picker
$stmt = $pdo->query("SELECT * FROM uploads ORDER BY uploaded_at DESC");
$media_files = $stmt->fetchAll();
?>

<div class="content-card">
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    
    <h5 class="mb-4">Manage Learning Topics</h5>
    <p class="text-muted">Manage the 4 main topics: Excel, Power BI, Statistics, and SQL.</p>
    
    <?php foreach ($topics as $topic): ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-book"></i> <?php echo htmlspecialchars($topic['title_en']); ?> / <?php echo htmlspecialchars($topic['title_ar']); ?>
                </h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $topic['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (English)</label>
                            <input type="text" class="form-control" name="title_en" value="<?php echo htmlspecialchars($topic['title_en']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (Arabic)</label>
                            <input type="text" class="form-control" name="title_ar" value="<?php echo htmlspecialchars($topic['title_ar']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Intro (English)</label>
                            <textarea class="form-control" name="intro_en" rows="3" required><?php echo htmlspecialchars($topic['intro_en']); ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Intro (Arabic)</label>
                            <textarea class="form-control" name="intro_ar" rows="3" required><?php echo htmlspecialchars($topic['intro_ar']); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hero Image URL</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="hero_image" id="hero_image_<?php echo $topic['id']; ?>" value="<?php echo htmlspecialchars($topic['hero_image']); ?>">
                                <button type="button" class="btn btn-outline-secondary btn-choose-media" onclick="openMediaPicker('hero_image_<?php echo $topic['id']; ?>', 'hero_preview_<?php echo $topic['id']; ?>')">
                                    <i class="fas fa-images"></i> Choose
                                </button>
                            </div>
                            <div id="hero_preview_<?php echo $topic['id']; ?>" class="image-preview-box <?php echo !empty($topic['hero_image']) ? 'active' : ''; ?>">
                                <img src="<?php echo !empty($topic['hero_image']) ? htmlspecialchars($topic['hero_image']) : ''; ?>" alt="Preview">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-control" name="display_order" value="<?php echo $topic['display_order']; ?>" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Topic
                    </button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
