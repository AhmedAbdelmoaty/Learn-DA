<?php
$page_title = 'Statistics Page';
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

function getStatisticsOverlayDefaults(): array {
    return ['start' => '#009688', 'end' => '#00695C', 'start_opacity' => 90, 'end_opacity' => 90];
}

$statisticsTopic = null;
$stmt = $pdo->prepare("SELECT * FROM topics WHERE slug = 'statistics' LIMIT 1");
$stmt->execute();
$statisticsTopic = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$statisticsTopic) {
    $defaults = getStatisticsOverlayDefaults();
    $statisticsTopic = [
        'slug' => 'statistics',
        'title_en' => 'Statistics',
        'title_ar' => 'الإحصاء',
        'intro_en' => '',
        'intro_ar' => '',
        'hero_image' => '',
        'hero_overlay_color_start' => $defaults['start'],
        'hero_overlay_color_end' => $defaults['end'],
        'hero_overlay_opacity_start' => $defaults['start_opacity'],
        'hero_overlay_opacity_end' => $defaults['end_opacity'],
        'display_order' => 0,
        'is_tool' => 0,
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_statistics') {
    $overlayDefaults = getStatisticsOverlayDefaults();

    $colorStart = sanitizeColorInput($_POST['hero_overlay_color_start'] ?? null) ?? $overlayDefaults['start'];
    $colorEnd = sanitizeColorInput($_POST['hero_overlay_color_end'] ?? null) ?? $overlayDefaults['end'];
    $opacityStart = normalizeOpacityValue($_POST['hero_overlay_opacity_start'] ?? $overlayDefaults['start_opacity'], $overlayDefaults['start_opacity']);
    $opacityEnd = normalizeOpacityValue($_POST['hero_overlay_opacity_end'] ?? $overlayDefaults['end_opacity'], $overlayDefaults['end_opacity']);
    $displayOrder = (int)($_POST['display_order'] ?? $statisticsTopic['display_order'] ?? 0);

    try {
        if (isset($statisticsTopic['id'])) {
            $stmt = $pdo->prepare("UPDATE topics SET slug = 'statistics', title_en = ?, title_ar = ?, intro_en = ?, intro_ar = ?, hero_image = ?, hero_overlay_color_start = ?, hero_overlay_color_end = ?, hero_overlay_opacity_start = ?, hero_overlay_opacity_end = ?, display_order = ?, is_tool = 0 WHERE id = ?");
            $stmt->execute([
                trim($_POST['title_en'] ?? ''),
                trim($_POST['title_ar'] ?? ''),
                trim($_POST['intro_en'] ?? ''),
                trim($_POST['intro_ar'] ?? ''),
                trim($_POST['hero_image'] ?? ''),
                $colorStart,
                $colorEnd,
                $opacityStart,
                $opacityEnd,
                $displayOrder,
                $statisticsTopic['id'],
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO topics (slug, title_en, title_ar, intro_en, intro_ar, hero_image, hero_overlay_color_start, hero_overlay_color_end, hero_overlay_opacity_start, hero_overlay_opacity_end, display_order, is_tool) VALUES ('statistics', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
            $stmt->execute([
                trim($_POST['title_en'] ?? ''),
                trim($_POST['title_ar'] ?? ''),
                trim($_POST['intro_en'] ?? ''),
                trim($_POST['intro_ar'] ?? ''),
                trim($_POST['hero_image'] ?? ''),
                $colorStart,
                $colorEnd,
                $opacityStart,
                $opacityEnd,
                $displayOrder,
            ]);
        }

        $success = 'Statistics page saved successfully!';

        $stmt = $pdo->prepare("SELECT * FROM topics WHERE slug = 'statistics' LIMIT 1");
        $stmt->execute();
        $statisticsTopic = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Error saving statistics page: ' . $e->getMessage();
    }
}

$overlayDefaults = getStatisticsOverlayDefaults();
$colorStartValue = $statisticsTopic['hero_overlay_color_start'] ?: $overlayDefaults['start'];
$colorEndValue = $statisticsTopic['hero_overlay_color_end'] ?: $overlayDefaults['end'];
$opacityStartValue = isset($statisticsTopic['hero_overlay_opacity_start']) ? (int)$statisticsTopic['hero_overlay_opacity_start'] : (int)$overlayDefaults['start_opacity'];
$opacityEndValue = isset($statisticsTopic['hero_overlay_opacity_end']) ? (int)$statisticsTopic['hero_overlay_opacity_end'] : (int)$overlayDefaults['end_opacity'];
?>

<div class="container-fluid mt-4">
    <div class="content-card">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
            <div>
                <h5 class="mb-1">Statistics Landing Page</h5>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="POST">
                <input type="hidden" name="action" value="save_statistics">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <strong><i class="fas fa-chart-bar"></i> Statistics Page Details</strong>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Statistics Page
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control" name="slug" value="statistics" readonly>
                            <small class="text-muted">Slug remains fixed to keep the page accessible at /statistics.</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Title (English)</label>
                            <input type="text" class="form-control" name="title_en" value="<?php echo htmlspecialchars($statisticsTopic['title_en']); ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Title (Arabic)</label>
                            <input type="text" class="form-control" name="title_ar" value="<?php echo htmlspecialchars($statisticsTopic['title_ar']); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Intro (English)</label>
                            <textarea class="form-control" name="intro_en" rows="3" required><?php echo htmlspecialchars($statisticsTopic['intro_en']); ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Intro (Arabic)</label>
                            <textarea class="form-control" name="intro_ar" rows="3" required><?php echo htmlspecialchars($statisticsTopic['intro_ar']); ?></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Hero Image URL</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="hero_image" id="statistics_hero_image" value="<?php echo htmlspecialchars($statisticsTopic['hero_image']); ?>">
                                <button type="button" class="btn btn-outline-secondary btn-choose-media" onclick="openMediaPicker('statistics_hero_image', 'statistics_hero_preview')">
                                    <i class="fas fa-images"></i> Choose
                                </button>
                            </div>
                            <div id="statistics_hero_preview" class="image-preview-box <?php echo !empty($statisticsTopic['hero_image']) ? 'active' : ''; ?>">
                                <img src="<?php echo htmlspecialchars($statisticsTopic['hero_image']); ?>" alt="Preview">
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" class="form-control" name="display_order" value="<?php echo (int)$statisticsTopic['display_order']; ?>" min="0">
                            <small class="text-muted">Controls ordering where topics are listed on the site.</small>
                        </div>
                    </div>

                    <div class="row align-items-end">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Overlay Color (Top)</label>
                            <input type="color" class="form-control form-control-color w-100" name="hero_overlay_color_start" value="<?php echo htmlspecialchars($colorStartValue); ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Overlay Opacity (Top)</label>
                            <input type="range" class="form-range overlay-opacity-input" min="0" max="100" name="hero_overlay_opacity_start" value="<?php echo $opacityStartValue; ?>" data-target="statistics_opacity_start">
                            <div class="form-text">Opacity: <span id="statistics_opacity_start"></span></div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Overlay Color (Bottom)</label>
                            <input type="color" class="form-control form-control-color w-100" name="hero_overlay_color_end" value="<?php echo htmlspecialchars($colorEndValue); ?>">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Overlay Opacity (Bottom)</label>
                            <input type="range" class="form-range overlay-opacity-input" min="0" max="100" name="hero_overlay_opacity_end" value="<?php echo $opacityEndValue; ?>" data-target="statistics_opacity_end">
                            <div class="form-text">Opacity: <span id="statistics_opacity_end"></span></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const updateOpacity = (input) => {
            const targetId = input.dataset.target;
            if (!targetId) return;
            const target = document.getElementById(targetId);
            if (target) {
                target.textContent = `${input.value}%`;
            }
        };

        document.querySelectorAll('.overlay-opacity-input').forEach((input) => {
            updateOpacity(input);
            input.addEventListener('input', () => updateOpacity(input));
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
