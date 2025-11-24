<?php
$page_title = 'Media Upload';
require_once __DIR__ . '/includes/header.php';

$successMessages = [];
$errorMessages = [];
$allowedImageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
$allowedVideoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'm4v'];
$maxUploadSize = 50 * 1024 * 1024; // 50MB

function formatUploadRecord(array $upload, array $imageExt, array $videoExt): array
{
    $extension = strtolower(pathinfo($upload['filepath'], PATHINFO_EXTENSION));
    $type = in_array($extension, $videoExt, true) ? 'video' : (in_array($extension, $imageExt, true) ? 'image' : 'file');

    return array_merge($upload, [
        'file_type' => $type,
        'url' => '../' . ltrim($upload['filepath'], '/'),
        'uploaded_at_human' => date('M d, Y', strtotime($upload['uploaded_at']))
    ]);
}

function normalizeUploads(array $uploads, array $imageExt, array $videoExt): array
{
    return array_map(function ($upload) use ($imageExt, $videoExt) {
        return formatUploadRecord($upload, $imageExt, $videoExt);
    }, $uploads);
}

function formatFilesArray(array $filesInput): array
{
    $files = [];
    $fileCount = count($filesInput['name']);
    for ($i = 0; $i < $fileCount; $i++) {
        if ($filesInput['name'][$i] === '') {
            continue;
        }
        $files[] = [
            'name' => $filesInput['name'][$i],
            'type' => $filesInput['type'][$i],
            'tmp_name' => $filesInput['tmp_name'][$i],
            'error' => $filesInput['error'][$i],
            'size' => $filesInput['size'][$i]
        ];
    }
    return $files;
}

$isAjaxRequest = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isAjaxRequest = isset($_POST['ajax']) && $_POST['ajax'] === '1';

    if (isset($_POST['action']) && in_array($_POST['action'], ['delete', 'bulk_delete'], true)) {
        $ids = [];
        if ($_POST['action'] === 'bulk_delete' && !empty($_POST['selected_ids'])) {
            $ids = array_map('intval', (array) $_POST['selected_ids']);
        } elseif (!empty($_POST['id'])) {
            $ids = [(int) $_POST['id']];
        }

        if (!empty($ids)) {
            try {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $pdo->prepare("SELECT id, filepath FROM uploads WHERE id IN ($placeholders)");
                $stmt->execute($ids);
                $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($files) {
                    foreach ($files as $file) {
                        $fullPath = '../' . $file['filepath'];
                        if (is_file($fullPath)) {
                            @unlink($fullPath);
                        }
                    }
                }

                $stmt = $pdo->prepare("DELETE FROM uploads WHERE id IN ($placeholders)");
                $stmt->execute($ids);
                $successMessages[] = count($ids) > 1 ? 'Selected media deleted successfully.' : 'Media deleted successfully.';
            } catch (PDOException $e) {
                $errorMessages[] = 'Error deleting media: ' . $e->getMessage();
            }
        } else {
            $errorMessages[] = 'Please select at least one media item to delete.';
        }
    } elseif (isset($_FILES['files'])) {
        $targetDir = "../assets/uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $filesToProcess = formatFilesArray($_FILES['files']);

        if (empty($filesToProcess)) {
            $errorMessages[] = 'Please choose one or more files to upload.';
        }

        foreach ($filesToProcess as $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errorMessages[] = sprintf('Error uploading %s (code %s).', $file['name'], $file['error']);
                continue;
            }

            if ($file['size'] > $maxUploadSize) {
                $errorMessages[] = sprintf('%s exceeds the 50MB limit.', $file['name']);
                continue;
            }

            $filename = basename($file['name']);
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $mediaType = null;

            if (in_array($extension, $allowedImageExtensions, true)) {
                $mediaType = 'image';
                if (@getimagesize($file['tmp_name']) === false) {
                    $errorMessages[] = sprintf('%s is not a valid image.', $filename);
                    continue;
                }
            } elseif (in_array($extension, $allowedVideoExtensions, true)) {
                $mediaType = 'video';
            }

            if ($mediaType === null) {
                $errorMessages[] = sprintf('%s has an unsupported format.', $filename);
                continue;
            }

            $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($filename, PATHINFO_FILENAME));
            $uniqueFilename = $safeName . '_' . uniqid() . '.' . $extension;
            $targetFile = $targetDir . $uniqueFilename;

            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                try {
                    $filepath = str_replace('../', '', $targetFile);
                    $stmt = $pdo->prepare("INSERT INTO uploads (filename, filepath) VALUES (?, ?)");
                    $stmt->execute([$filename, $filepath]);
                    $successMessages[] = sprintf('%s uploaded successfully.', $filename);
                } catch (PDOException $e) {
                    $errorMessages[] = 'Error saving file info: ' . $e->getMessage();
                }
            } else {
                $errorMessages[] = sprintf('Unable to upload %s.', $filename);
            }
        }
    }
}

$stmt = $pdo->query("SELECT * FROM uploads ORDER BY uploaded_at DESC");
$uploads = $stmt->fetchAll(PDO::FETCH_ASSOC);
$normalizedUploads = normalizeUploads($uploads, $allowedImageExtensions, $allowedVideoExtensions);

if ($isAjaxRequest) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => empty($errorMessages),
        'messages' => [
            'success' => $successMessages,
            'errors' => $errorMessages
        ],
        'uploads' => $normalizedUploads
    ]);
    exit;
}
?>

<style>
    #media-upload-form .row.align-items-start {
        align-items: flex-start !important;
    }

    #media-upload-form .form-file-col {
        min-width: 0;
    }

    #media-upload-form .upload-action-col {
        display: flex;
        align-items: flex-start;
    }

    .media-card .media-info {
        min-width: 0;
        flex: 1 1 auto;
        padding-right: 0.5rem;
    }

    .media-card .media-filename {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: break-word;
        max-height: 2.75em;
    }

    .media-card .form-check {
        flex-shrink: 0;
    }
</style>

<div class="content-card mb-4">
    <h5 class="mb-3">Upload Media</h5>
    <?php if (!empty($successMessages)): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo implode('<br>', array_map(function ($message) {
                return htmlspecialchars($message, ENT_QUOTES);
            }, $successMessages)); ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($errorMessages)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo implode('<br>', array_map(function ($message) {
                return htmlspecialchars($message, ENT_QUOTES);
            }, $errorMessages)); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="media-upload-form">
        <div class="row g-3 align-items-start">
            <div class="col-lg-9 form-file-col">
                <input type="file" class="form-control" name="files[]" accept="image/*,video/*" multiple required>
            </div>
            <div class="col-lg-3 upload-action-col">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-upload"></i> Upload Media
                </button>
            </div>
        </div>
    </form>
    <div id="media-feedback" class="mt-3"></div>
</div>

<div class="content-card">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h5 class="mb-0">Media Library</h5>
        <form method="POST" id="bulk-delete-form" class="d-flex align-items-center gap-2">
            <input type="hidden" name="action" value="bulk_delete">
            <button type="submit" class="btn btn-outline-danger" id="bulk-delete-button">
                <i class="fas fa-trash"></i> Delete Selected
            </button>
        </form>
    </div>
    <?php if (count($normalizedUploads) > 0): ?>
        <div class="row g-3" id="media-grid">
            <?php foreach ($normalizedUploads as $upload): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                    <div class="card h-100 shadow-sm media-card">
                        <div class="ratio ratio-4x3 bg-light rounded-top overflow-hidden">
                            <?php if ($upload['file_type'] === 'video'): ?>
                                <video src="<?php echo htmlspecialchars($upload['url'], ENT_QUOTES); ?>" class="w-100 h-100" controls muted preload="metadata"></video>
                            <?php else: ?>
                                <img src="<?php echo htmlspecialchars($upload['url'], ENT_QUOTES); ?>" class="w-100 h-100" alt="<?php echo htmlspecialchars($upload['filename'], ENT_QUOTES); ?>" style="object-fit: cover;">
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="media-info">
                                    <p class="mb-0 small fw-semibold media-filename" title="<?php echo htmlspecialchars($upload['filename'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($upload['filename'], ENT_QUOTES); ?></p>
                                    <small class="text-muted"><?php echo htmlspecialchars($upload['uploaded_at_human']); ?></small>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input media-checkbox" type="checkbox" name="selected_ids[]" value="<?php echo $upload['id']; ?>" form="bulk-delete-form">
                                </div>
                            </div>
                            <div class="input-group input-group-sm mb-2">
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($upload['filepath'], ENT_QUOTES); ?>" readonly onclick="this.select();">
                                <button class="btn btn-outline-secondary btn-copy-path" type="button" data-path="<?php echo htmlspecialchars($upload['filepath'], ENT_QUOTES); ?>">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <form method="POST" class="single-delete-form mt-2">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $upload['id']; ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 btn-delete-single">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="text-muted mt-3" id="media-empty" style="display: none;">No media uploaded yet.</p>
    <?php else: ?>
        <p class="text-muted" id="media-empty">No media uploaded yet.</p>
        <div class="row g-3" id="media-grid"></div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const uploadForm = document.getElementById('media-upload-form');
    const bulkForm = document.getElementById('bulk-delete-form');
    const bulkDeleteButton = document.getElementById('bulk-delete-button');
    const messageWrapper = document.getElementById('media-feedback');
    const grid = document.getElementById('media-grid');
    const emptyState = document.getElementById('media-empty');

    const escapeHtml = (string) => {
        const div = document.createElement('div');
        div.innerText = string ?? '';
        return div.innerHTML;
    };

    const renderMessages = (messages) => {
        if (!messages) {
            messageWrapper.innerHTML = '';
            return;
        }

        const { success = [], errors = [] } = messages;
        let html = '';

        if (errors.length) {
            html += `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>${errors.map(escapeHtml).join('<br>')}</div>`;
        }

        if (success.length) {
            html += `<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>${success.map(escapeHtml).join('<br>')}</div>`;
        }

        messageWrapper.innerHTML = html;
    };

    const buildMediaCard = (item) => {
        const preview = item.file_type === 'video'
            ? `<video src="${escapeHtml(item.url)}" class="w-100 h-100" controls muted preload="metadata"></video>`
            : `<img src="${escapeHtml(item.url)}" class="w-100 h-100" alt="${escapeHtml(item.filename)}" style="object-fit: cover;">`;

        return `
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card h-100 shadow-sm media-card">
                    <div class="ratio ratio-4x3 bg-light rounded-top overflow-hidden">${preview}</div>
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="media-info">
                                <p class="mb-0 small fw-semibold media-filename" title="${escapeHtml(item.filename)}">${escapeHtml(item.filename)}</p>
                                <small class="text-muted">${escapeHtml(item.uploaded_at_human)}</small>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input media-checkbox" type="checkbox" name="selected_ids[]" value="${escapeHtml(item.id)}" form="bulk-delete-form">
                            </div>
                        </div>
                        <div class="input-group input-group-sm mb-2">
                            <input type="text" class="form-control" value="${escapeHtml(item.filepath)}" readonly onclick="this.select();">
                            <button class="btn btn-outline-secondary btn-copy-path" type="button" data-path="${escapeHtml(item.filepath)}"><i class="fas fa-copy"></i></button>
                        </div>
                        <form method="POST" class="single-delete-form mt-2">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="${escapeHtml(item.id)}">
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100 btn-delete-single">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        `;
    };

    const renderGrid = (items) => {
        if (!Array.isArray(items) || items.length === 0) {
            grid.innerHTML = '';
            emptyState.style.display = 'block';
            return;
        }

        emptyState.style.display = 'none';
        grid.innerHTML = items.map(buildMediaCard).join('');
        attachCardEvents();
    };

    const updateBulkButtonState = () => {
        const anyChecked = document.querySelectorAll('.media-checkbox:checked').length > 0;
        bulkDeleteButton.disabled = !anyChecked;
    };

    const handleAjaxForm = (form, extraValidation) => {
        form.addEventListener('submit', (event) => {
            if (typeof extraValidation === 'function' && !extraValidation()) {
                event.preventDefault();
                return;
            }

            event.preventDefault();
            const formData = new FormData(form);
            formData.append('ajax', '1');

            fetch('media_upload.php', {
                method: 'POST',
                body: formData
            })
                .then((response) => response.json())
                .then((data) => {
                    renderMessages(data.messages);
                    renderGrid(data.uploads);
                    form.reset();
                    updateBulkButtonState();
                })
                .catch(() => {
                    renderMessages({ errors: ['An unexpected error occurred. Please try again.'] });
                });
        });
    };

    const attachCardEvents = () => {
        document.querySelectorAll('.single-delete-form').forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                const formData = new FormData(form);
                formData.append('ajax', '1');

                fetch('media_upload.php', {
                    method: 'POST',
                    body: formData
                })
                    .then((response) => response.json())
                    .then((data) => {
                        renderMessages(data.messages);
                        renderGrid(data.uploads);
                        updateBulkButtonState();
                    })
                    .catch(() => {
                        renderMessages({ errors: ['Unable to delete the selected media.'] });
                    });
            });
        });

        document.querySelectorAll('.media-checkbox').forEach((checkbox) => {
            checkbox.addEventListener('change', updateBulkButtonState);
        });

        document.querySelectorAll('.btn-copy-path').forEach((button) => {
            button.addEventListener('click', async () => {
                const path = button.dataset.path;
                if (!path || !navigator.clipboard) {
                    renderMessages({ errors: ['Clipboard is not available in this browser.'] });
                    return;
                }

                try {
                    await navigator.clipboard.writeText(path);
                    button.classList.add('text-success');
                    setTimeout(() => button.classList.remove('text-success'), 1200);
                } catch (error) {
                    renderMessages({ errors: ['Unable to copy the file path.'] });
                }
            });
        });
    };

    handleAjaxForm(uploadForm, () => {
        const hasFiles = uploadForm.querySelector('input[type="file"]').files.length > 0;
        if (!hasFiles) {
            renderMessages({ errors: ['Please choose at least one file to upload.'] });
        }
        return hasFiles;
    });

    handleAjaxForm(bulkForm, () => {
        const selected = document.querySelectorAll('.media-checkbox:checked');
        if (selected.length === 0) {
            renderMessages({ errors: ['Select at least one media item to delete.'] });
            return false;
        }
        return true;
    });

    attachCardEvents();
    updateBulkButtonState();
});
</script>

<?php require_once 'includes/footer.php'; ?>
