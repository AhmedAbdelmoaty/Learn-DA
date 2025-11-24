<?php
$page_title = 'Manage Tools';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

function sanitizeColorInput(?string $value): ?string {
    if ($value === null) {
        return null;
    }

    $value = trim($value);

    if ($value === '') {
        return null;
    }

    if (preg_match('/^#([0-9a-fA-F]{6})$/', $value)) {
        return strtoupper($value);
    }

    return null;
}

function normalizeOpacityValue($value, int $default = 90): int {
    if (!is_numeric($value)) {
        return $default;
    }

    $intVal = (int)$value;

    if ($intVal < 0) {
        return 0;
    }

    if ($intVal > 100) {
        return 100;
    }

    return $intVal;
}

function getDefaultTopicOverlayConfig(?string $slug): array {
    $defaults = [
        'excel' => ['start' => '#2E7D32', 'end' => '#1B5E20', 'start_opacity' => 90, 'end_opacity' => 90],
        'power-bi' => ['start' => '#FFC400', 'end' => '#CC7800', 'start_opacity' => 92, 'end_opacity' => 90],
        'sql' => ['start' => '#3F51B5', 'end' => '#21358C', 'start_opacity' => 90, 'end_opacity' => 90],
        'statistics' => ['start' => '#009688', 'end' => '#00695C', 'start_opacity' => 90, 'end_opacity' => 90],
    ];

    return $defaults[$slug] ?? ['start' => '#A8324E', 'end' => '#6C1E35', 'start_opacity' => 90, 'end_opacity' => 90];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        try {
            $slug = trim($_POST['slug'] ?? '');
            $overlayDefaults = getDefaultTopicOverlayConfig($slug);
            $colorStart = sanitizeColorInput($_POST['hero_overlay_color_start'] ?? null) ?? $overlayDefaults['start'];
            $colorEnd = sanitizeColorInput($_POST['hero_overlay_color_end'] ?? null) ?? $overlayDefaults['end'];
            $opacityStart = normalizeOpacityValue($_POST['hero_overlay_opacity_start'] ?? $overlayDefaults['start_opacity'], $overlayDefaults['start_opacity']);
            $opacityEnd = normalizeOpacityValue($_POST['hero_overlay_opacity_end'] ?? $overlayDefaults['end_opacity'], $overlayDefaults['end_opacity']);

            $stmt = $pdo->prepare("INSERT INTO topics (slug, title_en, title_ar, intro_en, intro_ar, hero_image, hero_overlay_color_start, hero_overlay_color_end, hero_overlay_opacity_start, hero_overlay_opacity_end, display_order, is_tool) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $slug,
                trim($_POST['title_en'] ?? ''),
                trim($_POST['title_ar'] ?? ''),
                trim($_POST['intro_en'] ?? ''),
                trim($_POST['intro_ar'] ?? ''),
                trim($_POST['hero_image'] ?? ''),
                $colorStart,
                $colorEnd,
                $opacityStart,
                $opacityEnd,
                (int)($_POST['display_order'] ?? 0),
                true,
            ]);
            $success = 'Topic created successfully!';
        } catch (PDOException $e) {
            $error = 'Error creating topic: ' . $e->getMessage();
        }
    } elseif ($action === 'update') {
        try {
            $slug = trim($_POST['slug'] ?? '');
            $overlayDefaults = getDefaultTopicOverlayConfig($slug);
            $colorStart = sanitizeColorInput($_POST['hero_overlay_color_start'] ?? null) ?? $overlayDefaults['start'];
            $colorEnd = sanitizeColorInput($_POST['hero_overlay_color_end'] ?? null) ?? $overlayDefaults['end'];
            $opacityStart = normalizeOpacityValue($_POST['hero_overlay_opacity_start'] ?? $overlayDefaults['start_opacity'], $overlayDefaults['start_opacity']);
            $opacityEnd = normalizeOpacityValue($_POST['hero_overlay_opacity_end'] ?? $overlayDefaults['end_opacity'], $overlayDefaults['end_opacity']);

            $stmt = $pdo->prepare("UPDATE topics SET slug = ?, title_en = ?, title_ar = ?, intro_en = ?, intro_ar = ?, hero_image = ?, hero_overlay_color_start = ?, hero_overlay_color_end = ?, hero_overlay_opacity_start = ?, hero_overlay_opacity_end = ?, display_order = ?, is_tool = ? WHERE id = ?");
            $stmt->execute([
                $slug,
                trim($_POST['title_en'] ?? ''),
                trim($_POST['title_ar'] ?? ''),
                trim($_POST['intro_en'] ?? ''),
                trim($_POST['intro_ar'] ?? ''),
                trim($_POST['hero_image'] ?? ''),
                $colorStart,
                $colorEnd,
                $opacityStart,
                $opacityEnd,
                (int)($_POST['display_order'] ?? 0),
                true,
                (int)($_POST['id'] ?? 0),
            ]);
            $success = 'Topic updated successfully!';
        } catch (PDOException $e) {
            $error = 'Error updating topic: ' . $e->getMessage();
        }
    } elseif ($action === 'delete') {
        $topic_id = (int)($_POST['id'] ?? 0);
        try {
            $stmt = $pdo->prepare("SELECT is_tool FROM topics WHERE id = ?");
            $stmt->execute([$topic_id]);
            $topic_row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$topic_row) {
                $error = 'Topic not found.';
            } elseif (filter_var($topic_row['is_tool'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== true) {
                $error = 'Only tool topics can be deleted from this screen.';
            } else {
                $stmt = $pdo->prepare("DELETE FROM topics WHERE id = ?");
                $stmt->execute([$topic_id]);
                $success = 'Topic deleted successfully!';
            }
        } catch (PDOException $e) {
            $error = 'Error deleting topic: ' . $e->getMessage();
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM topics WHERE is_tool = TRUE ORDER BY display_order, title_en");
$stmt->execute();
$topics = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT * FROM uploads ORDER BY uploaded_at DESC");
$media_files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid mt-4">
    <div class="content-card">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
            <div>
                <h5 class="mb-1">Tool Pages</h5>
                <p class="text-muted mb-0">Manage only the tool pages shown on the Tools landing page. Each tool you add here appears as a card in Tools.</p>
            </div>
            <?php renderBulkSaveToolbar(['wrapper_class' => 'mt-3 mt-md-0']); ?>
        </div>
        <?php if ($success): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
        <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>

    <div class="card mb-5">
        <div class="card-header bg-light">
            <strong><i class="fas fa-plus-circle"></i> Add New Tool</strong>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="is_tool" value="1">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Slug (URL)</label>
                        <input type="text" class="form-control" name="slug" placeholder="e.g. python" required>
                        <small class="text-muted">Used in URLs. Use lowercase letters, numbers, and dashes only.</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Title (English)</label>
                        <input type="text" class="form-control" name="title_en" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Title (Arabic)</label>
                        <input type="text" class="form-control" name="title_ar" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Intro (English)</label>
                        <textarea class="form-control" name="intro_en" rows="3" required></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Intro (Arabic)</label>
                        <textarea class="form-control" name="intro_ar" rows="3" required></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
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
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Display Order</label>
                        <input type="number" class="form-control" name="display_order" value="1" min="0">
                    </div>
                </div>
                <?php $newTopicOverlayDefaults = getDefaultTopicOverlayConfig(null); ?>
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Overlay Color (Top)</label>
                        <input type="color" class="form-control form-control-color w-100" name="hero_overlay_color_start" value="<?php echo htmlspecialchars($newTopicOverlayDefaults['start']); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Overlay Opacity (Top)</label>
                        <input type="range" class="form-range overlay-opacity-input" min="0" max="100" name="hero_overlay_opacity_start" value="<?php echo (int)$newTopicOverlayDefaults['start_opacity']; ?>" data-target="add_overlay_opacity_start_value">
                        <div class="form-text">Opacity: <span id="add_overlay_opacity_start_value"></span></div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Overlay Color (Bottom)</label>
                        <input type="color" class="form-control form-control-color w-100" name="hero_overlay_color_end" value="<?php echo htmlspecialchars($newTopicOverlayDefaults['end']); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Overlay Opacity (Bottom)</label>
                        <input type="range" class="form-range overlay-opacity-input" min="0" max="100" name="hero_overlay_opacity_end" value="<?php echo (int)$newTopicOverlayDefaults['end_opacity']; ?>" data-target="add_overlay_opacity_end_value">
                        <div class="form-text">Opacity: <span id="add_overlay_opacity_end_value"></span></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Topic
                </button>
            </form>
        </div>

</div>

    <?php foreach ($topics as $topic): ?>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-book"></i>
                    <?php echo htmlspecialchars($topic['title_en']); ?> / <?php echo htmlspecialchars($topic['title_ar']); ?>
                    <span class="badge bg-info ms-2"><i class="fas fa-toolbox"></i> Tool</span>
                </h6>
                    <form method="POST" onsubmit="return confirm('Delete this tool? This will remove all related content items.');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $topic['id']; ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i> Delete Tool
                        </button>
                    </form>
            </div>
            <div class="card-body">
                <form method="POST" data-bulk-save="true" data-section-name="Topic: <?php echo htmlspecialchars($topic['title_en']); ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo $topic['id']; ?>">
                    <input type="hidden" name="is_tool" value="1">

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Slug (URL)</label>
                            <input type="text" class="form-control" name="slug" value="<?php echo htmlspecialchars($topic['slug']); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Title (English)</label>
                            <input type="text" class="form-control" name="title_en" value="<?php echo htmlspecialchars($topic['title_en']); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
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

                    <?php
                        $overlayDefaults = getDefaultTopicOverlayConfig($topic['slug']);
                        $colorStartValue = $topic['hero_overlay_color_start'] ?: $overlayDefaults['start'];
                        $colorEndValue = $topic['hero_overlay_color_end'] ?: $overlayDefaults['end'];
                        $opacityStartValue = isset($topic['hero_overlay_opacity_start']) ? (int)$topic['hero_overlay_opacity_start'] : (int)$overlayDefaults['start_opacity'];
                        $opacityEndValue = isset($topic['hero_overlay_opacity_end']) ? (int)$topic['hero_overlay_opacity_end'] : (int)$overlayDefaults['end_opacity'];
                    ?>
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
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-control" name="display_order" value="<?php echo (int)$topic['display_order']; ?>" required>
                        </div>
                    </div>

                    <div class="row align-items-end">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Overlay Color (Top)</label>
                            <input type="color" class="form-control form-control-color w-100" name="hero_overlay_color_start" value="<?php echo htmlspecialchars($colorStartValue); ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Overlay Opacity (Top)</label>
                            <input type="range" class="form-range overlay-opacity-input" min="0" max="100" name="hero_overlay_opacity_start" value="<?php echo $opacityStartValue; ?>" data-target="overlay_opacity_start_<?php echo $topic['id']; ?>">
                            <div class="form-text">Opacity: <span id="overlay_opacity_start_<?php echo $topic['id']; ?>"></span></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Overlay Color (Bottom)</label>
                            <input type="color" class="form-control form-control-color w-100" name="hero_overlay_color_end" value="<?php echo htmlspecialchars($colorEndValue); ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Overlay Opacity (Bottom)</label>
                            <input type="range" class="form-range overlay-opacity-input" min="0" max="100" name="hero_overlay_opacity_end" value="<?php echo $opacityEndValue; ?>" data-target="overlay_opacity_end_<?php echo $topic['id']; ?>">
                            <div class="form-text">Opacity: <span id="overlay_opacity_end_<?php echo $topic['id']; ?>"></span></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary bulk-save-hidden">
                        <i class="fas fa-save"></i> Update Topic
                    </button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</div>

<script>
    const mediaFiles = <?php echo json_encode($media_files); ?>;

    document.addEventListener('DOMContentLoaded', () => {
        const updateOpacityOutput = (input) => {
            const targetId = input.dataset.target;
            if (!targetId) {
                return;
            }

            const target = document.getElementById(targetId);
            if (target) {
                target.textContent = `${input.value}%`;
            }
        };

        document.querySelectorAll('.overlay-opacity-input').forEach((input) => {
            updateOpacityOutput(input);
            input.addEventListener('input', () => updateOpacityOutput(input));
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
