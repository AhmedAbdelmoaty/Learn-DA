<?php
$page_title = 'Manage Content Items';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            try {
                $stmt = $pdo->prepare("INSERT INTO content_items (topic_id, slug, title_en, title_ar, summary_en, summary_ar, body_en, body_ar, hero_image, status, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['topic_id'],
                    $_POST['slug'],
                    $_POST['title_en'],
                    $_POST['title_ar'],
                    $_POST['summary_en'],
                    $_POST['summary_ar'],
                    $_POST['body_en'],
                    $_POST['body_ar'],
                    $_POST['hero_image'],
                    $_POST['status'],
                    $_POST['display_order']
                ]);
                $success = 'Content item added successfully!';
            } catch(PDOException $e) {
                $error = 'Error adding content item: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'update') {
            try {
                $stmt = $pdo->prepare("UPDATE content_items SET topic_id = ?, slug = ?, title_en = ?, title_ar = ?, summary_en = ?, summary_ar = ?, body_en = ?, body_ar = ?, hero_image = ?, status = ?, display_order = ? WHERE id = ?");
                $stmt->execute([
                    $_POST['topic_id'],
                    $_POST['slug'],
                    $_POST['title_en'],
                    $_POST['title_ar'],
                    $_POST['summary_en'],
                    $_POST['summary_ar'],
                    $_POST['body_en'],
                    $_POST['body_ar'],
                    $_POST['hero_image'],
                    $_POST['status'],
                    $_POST['display_order'],
                    $_POST['id']
                ]);
                $success = 'Content item updated successfully!';
            } catch(PDOException $e) {
                $error = 'Error updating content item: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'delete') {
            try {
                $stmt = $pdo->prepare("DELETE FROM content_items WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $success = 'Content item deleted successfully!';
            } catch(PDOException $e) {
                $error = 'Error deleting content item: ' . $e->getMessage();
            }
        }
    }
}

$stmt = $pdo->query("SELECT * FROM topics ORDER BY display_order");
$topics = $stmt->fetchAll();

$stmt = $pdo->query("SELECT ci.*, t.title_en as topic_title FROM content_items ci JOIN topics t ON ci.topic_id = t.id ORDER BY t.display_order, ci.display_order");
$content_items = $stmt->fetchAll();
?>

<div class="content-card">
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0">Content Items (Explainers)</h5>
            <p class="text-muted mb-0">Manage articles and explainers for each topic</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus"></i> Add Content Item
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Topic</th>
                    <th>Title (EN / AR)</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($content_items as $item): ?>
                    <tr>
                        <td><span class="badge bg-info"><?php echo htmlspecialchars($item['topic_title']); ?></span></td>
                        <td>
                            <strong><?php echo htmlspecialchars($item['title_en']); ?></strong><br>
                            <small class="text-muted"><?php echo htmlspecialchars($item['title_ar']); ?></small>
                        </td>
                        <td><code><?php echo htmlspecialchars($item['slug']); ?></code></td>
                        <td>
                            <span class="badge bg-<?php echo $item['status'] === 'published' ? 'success' : 'warning'; ?>">
                                <?php echo htmlspecialchars($item['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $item['display_order']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="editItem(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add Content Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label class="form-label">Topic</label>
                        <select class="form-select" name="topic_id" required>
                            <?php foreach ($topics as $topic): ?>
                                <option value="<?php echo $topic['id']; ?>"><?php echo htmlspecialchars($topic['title_en']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (English)</label>
                            <input type="text" class="form-control" name="title_en" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (Arabic)</label>
                            <input type="text" class="form-control" name="title_ar" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug" required>
                        <small class="text-muted">URL-friendly (e.g., formulas-functions)</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Summary (English)</label>
                            <textarea class="form-control" name="summary_en" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Summary (Arabic)</label>
                            <textarea class="form-control" name="summary_ar" rows="2"></textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (English)</label>
                            <textarea class="form-control" name="body_en" rows="5"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (Arabic)</label>
                            <textarea class="form-control" name="body_ar" rows="5"></textarea>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Hero Image URL</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="hero_image" id="add_hero_image">
                            <button type="button" class="btn btn-outline-secondary btn-choose-media" onclick="openMediaPicker('add_hero_image', 'add_hero_preview')">
                                <i class="fas fa-images"></i> Choose
                            </button>
                        </div>
                        <div id="add_hero_preview" class="image-preview-box">
                            <img src="" alt="Preview">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-control" name="display_order" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add Content Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Content Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Topic</label>
                        <select class="form-select" name="topic_id" id="edit_topic_id" required>
                            <?php foreach ($topics as $topic): ?>
                                <option value="<?php echo $topic['id']; ?>"><?php echo htmlspecialchars($topic['title_en']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (English)</label>
                            <input type="text" class="form-control" name="title_en" id="edit_title_en" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Title (Arabic)</label>
                            <input type="text" class="form-control" name="title_ar" id="edit_title_ar" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug" id="edit_slug" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Summary (English)</label>
                            <textarea class="form-control" name="summary_en" id="edit_summary_en" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Summary (Arabic)</label>
                            <textarea class="form-control" name="summary_ar" id="edit_summary_ar" rows="2"></textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (English)</label>
                            <textarea class="form-control" name="body_en" id="edit_body_en" rows="5"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Body (Arabic)</label>
                            <textarea class="form-control" name="body_ar" id="edit_body_ar" rows="5"></textarea>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Hero Image URL</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="hero_image" id="edit_hero_image">
                            <button type="button" class="btn btn-outline-secondary btn-choose-media" onclick="openMediaPicker('edit_hero_image', 'edit_hero_preview')">
                                <i class="fas fa-images"></i> Choose
                            </button>
                        </div>
                        <div id="edit_hero_preview" class="image-preview-box">
                            <img src="" alt="Preview">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="edit_status">
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-control" name="display_order" id="edit_display_order">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Content Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editItem(item) {
    document.getElementById('edit_id').value = item.id;
    document.getElementById('edit_topic_id').value = item.topic_id;
    document.getElementById('edit_title_en').value = item.title_en;
    document.getElementById('edit_title_ar').value = item.title_ar;
    document.getElementById('edit_slug').value = item.slug;
    document.getElementById('edit_summary_en').value = item.summary_en || '';
    document.getElementById('edit_summary_ar').value = item.summary_ar || '';
    document.getElementById('edit_body_en').value = item.body_en || '';
    document.getElementById('edit_body_ar').value = item.body_ar || '';
    document.getElementById('edit_hero_image').value = item.hero_image || '';
    document.getElementById('edit_status').value = item.status;
    document.getElementById('edit_display_order').value = item.display_order;
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
