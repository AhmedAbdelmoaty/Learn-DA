<?php
$page_title = 'Manage Course Rounds';
require_once __DIR__ . '/includes/header.php';

$canPublish = canPublishContent();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' || $_POST['action'] === 'update') {
            // Compose start_at from date/time inputs for storage
            $start_at = null;
            if (!empty($_POST['round_date']) && !empty($_POST['round_hour']) && !empty($_POST['round_minute']) && !empty($_POST['round_ampm'])) {
                $hour = intval($_POST['round_hour']);
                $minute = $_POST['round_minute'];
                $ampm = $_POST['round_ampm'];
                
                // Convert to 24-hour format
                if ($ampm === 'PM' && $hour !== 12) {
                    $hour += 12;
                } elseif ($ampm === 'AM' && $hour === 12) {
                    $hour = 0;
                }
                
                $start_at = $_POST['round_date'] . ' ' . sprintf('%02d:%02d:00', $hour, $minute);
            }
            
            if ($_POST['action'] === 'add') {
                try {
                    $stmt = $pdo->prepare("INSERT INTO course_rounds (label_en, label_ar, start_at, published, sort_order) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['label_en'],
                        $_POST['label_ar'],
                        $start_at,
                        resolvePublishFlag($_POST['published'] ?? null),
                        $_POST['sort_order']
                    ]);
                    $success = 'Course round added successfully!';
                } catch(PDOException $e) {
                    $error = 'Error adding course round: ' . $e->getMessage();
                }
            } else {
                try {
                    $stmt = $pdo->prepare("UPDATE course_rounds SET label_en = ?, label_ar = ?, start_at = ?, published = ?, sort_order = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $stmt->execute([
                        $_POST['label_en'],
                        $_POST['label_ar'],
                        $start_at,
                        resolvePublishFlag($_POST['published'] ?? null),
                        $_POST['sort_order'],
                        $_POST['id']
                    ]);
                    $success = 'Course round updated successfully!';
                } catch(PDOException $e) {
                    $error = 'Error updating course round: ' . $e->getMessage();
                }
            }
        } elseif ($_POST['action'] === 'delete') {
            try {
                $stmt = $pdo->prepare("DELETE FROM course_rounds WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $success = 'Course round deleted successfully!';
            } catch(PDOException $e) {
                $error = 'Error deleting course round: ' . $e->getMessage();
            }
        }
    }
}

$stmt = $pdo->query("SELECT * FROM course_rounds ORDER BY sort_order ASC, id DESC");
$rounds = $stmt->fetchAll();
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
            <h5 class="mb-1">Manage Course Rounds</h5>
            <p class="text-muted mb-0">Add and manage available course dates for the contact form.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoundModal">
            <i class="fas fa-plus"></i> Add New Round
        </button>
    </div>
    
    <?php if (count($rounds) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Label (English)</th>
                        <th>Label (Arabic)</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rounds as $round): ?>
                        <tr>
                            <td><?php echo $round['id']; ?></td>
                            <td><?php echo htmlspecialchars($round['label_en']); ?></td>
                            <td><?php echo htmlspecialchars($round['label_ar']); ?></td>
                            <td>
                                <?php if ($round['published']): ?>
                                    <span class="badge bg-success">Published</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $round['sort_order']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editRoundModal<?php echo $round['id']; ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteRound(<?php echo $round['id']; ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                        
                        <!-- Edit Modal for this round -->
                        <div class="modal fade" id="editRoundModal<?php echo $round['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Course Round</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" id="editForm<?php echo $round['id']; ?>">
                                        <div class="modal-body">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?php echo $round['id']; ?>">
                                            <input type="hidden" name="label_en" id="edit_label_en_<?php echo $round['id']; ?>" value="<?php echo htmlspecialchars($round['label_en']); ?>">
                                            <input type="hidden" name="label_ar" id="edit_label_ar_<?php echo $round['id']; ?>" value="<?php echo htmlspecialchars($round['label_ar']); ?>">
                                            <input type="hidden" name="round_date" id="edit_round_date_<?php echo $round['id']; ?>">
                                            <input type="hidden" name="round_hour" id="edit_round_hour_<?php echo $round['id']; ?>">
                                            <input type="hidden" name="round_minute" id="edit_round_minute_<?php echo $round['id']; ?>">
                                            <input type="hidden" name="round_ampm" id="edit_round_ampm_<?php echo $round['id']; ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Select Date *</label>
                                                <input type="date" class="form-control" id="edit_date_<?php echo $round['id']; ?>" required>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Hour *</label>
                                                    <select class="form-select" id="edit_hour_<?php echo $round['id']; ?>" required>
                                                        <?php for ($h = 1; $h <= 12; $h++): ?>
                                                            <option value="<?php echo $h; ?>"><?php echo $h; ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Minute *</label>
                                                    <select class="form-select" id="edit_minute_<?php echo $round['id']; ?>" required>
                                                        <option value="00">00</option>
                                                        <option value="15">15</option>
                                                        <option value="30">30</option>
                                                        <option value="45">45</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">AM/PM *</label>
                                                    <select class="form-select" id="edit_ampm_<?php echo $round['id']; ?>" required>
                                                        <option value="AM">AM</option>
                                                        <option value="PM">PM</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="alert alert-info">
                                                <strong>Preview:</strong><br>
                                                <div id="edit_preview_en_<?php echo $round['id']; ?>">English: -</div>
                                                <div id="edit_preview_ar_<?php echo $round['id']; ?>">Arabic: -</div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Sort Order *</label>
                                                <input type="number" class="form-control" name="sort_order" value="<?php echo $round['sort_order']; ?>" required>
                                                <small class="text-muted">Lower numbers appear first</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="published" id="published_edit_<?php echo $round['id']; ?>" <?php echo ($canPublish && $round['published']) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="published_edit_<?php echo $round['id']; ?>">
                                                        Published (visible on contact form)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Update Round
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <script>
                        (function() {
                            const id = <?php echo $round['id']; ?>;
                            const dateInput = document.getElementById('edit_date_' + id);
                            const hourInput = document.getElementById('edit_hour_' + id);
                            const minuteInput = document.getElementById('edit_minute_' + id);
                            const ampmInput = document.getElementById('edit_ampm_' + id);
                            const form = document.getElementById('editForm' + id);
                            
                            function updateEditPreview() {
                                const date = new Date(dateInput.value + 'T12:00:00');
                                if (isNaN(date.getTime())) return;
                                
                                const hour = hourInput.value;
                                const minute = minuteInput.value;
                                const ampm = ampmInput.value;
                                
                                // English format
                                const daysEn = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                const dayNameEn = daysEn[date.getDay()];
                                const day = String(date.getDate()).padStart(2, '0');
                                const month = String(date.getMonth() + 1).padStart(2, '0');
                                const year = date.getFullYear();
                                const labelEn = `${dayNameEn} ${day}/${month}/${year} — ${hour}:${minute} ${ampm}`;
                                
                                // Arabic format
                                const daysAr = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
                                const monthsAr = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                                const dayNameAr = daysAr[date.getDay()];
                                const monthNameAr = monthsAr[date.getMonth()];
                                const ampmAr = ampm === 'AM' ? 'صباحًا' : 'مساءً';
                                const labelAr = `${dayNameAr} ${date.getDate()} ${monthNameAr} ${year} – ${hour}:${minute} ${ampmAr}`;
                                
                                document.getElementById('edit_preview_en_' + id).textContent = 'English: ' + labelEn;
                                document.getElementById('edit_preview_ar_' + id).textContent = 'Arabic: ' + labelAr;
                                document.getElementById('edit_label_en_' + id).value = labelEn;
                                document.getElementById('edit_label_ar_' + id).value = labelAr;
                                
                                // Sync hidden fields for submission
                                document.getElementById('edit_round_date_' + id).value = dateInput.value;
                                document.getElementById('edit_round_hour_' + id).value = hour;
                                document.getElementById('edit_round_minute_' + id).value = minute;
                                document.getElementById('edit_round_ampm_' + id).value = ampm;
                            }
                            
                            dateInput.addEventListener('change', updateEditPreview);
                            hourInput.addEventListener('change', updateEditPreview);
                            minuteInput.addEventListener('change', updateEditPreview);
                            ampmInput.addEventListener('change', updateEditPreview);
                            
                            // Initialize from start_at if it exists (parse without timezone conversion)
                            <?php if (!empty($round['start_at'])): ?>
                                // Parse YYYY-MM-DD HH:MM:SS without timezone conversions
                                const startAtStr = '<?php echo $round['start_at']; ?>';
                                const parts = startAtStr.split(' ');
                                const datePart = parts[0]; // YYYY-MM-DD
                                const timePart = parts[1]; // HH:MM:SS
                                
                                dateInput.value = datePart;
                                
                                const timeComponents = timePart.split(':');
                                const hour24 = parseInt(timeComponents[0]);
                                const minute = timeComponents[1];
                                
                                let hour12, ampm;
                                if (hour24 === 0) {
                                    hour12 = 12;
                                    ampm = 'AM';
                                } else if (hour24 < 12) {
                                    hour12 = hour24;
                                    ampm = 'AM';
                                } else if (hour24 === 12) {
                                    hour12 = 12;
                                    ampm = 'PM';
                                } else {
                                    hour12 = hour24 - 12;
                                    ampm = 'PM';
                                }
                                
                                hourInput.value = hour12;
                                minuteInput.value = minute;
                                ampmInput.value = ampm;
                            <?php else: ?>
                                // Default to today if no start_at
                                const today = new Date();
                                dateInput.value = today.toISOString().split('T')[0];
                                hourInput.value = '10';
                                minuteInput.value = '00';
                                ampmInput.value = 'AM';
                            <?php endif; ?>
                            
                            updateEditPreview();
                        })();
                        </script>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No course rounds yet. Click "Add New Round" to create one.
        </div>
    <?php endif; ?>
</div>

<!-- Add Round Modal -->
<div class="modal fade" id="addRoundModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Course Round</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="addForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="label_en" id="add_label_en">
                    <input type="hidden" name="label_ar" id="add_label_ar">
                    <input type="hidden" name="round_date" id="add_round_date">
                    <input type="hidden" name="round_hour" id="add_round_hour">
                    <input type="hidden" name="round_minute" id="add_round_minute">
                    <input type="hidden" name="round_ampm" id="add_round_ampm">
                    
                    <div class="mb-3">
                        <label class="form-label">Select Date *</label>
                        <input type="date" class="form-control" id="add_date" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Hour *</label>
                            <select class="form-select" id="add_hour" required>
                                <?php for ($h = 1; $h <= 12; $h++): ?>
                                    <option value="<?php echo $h; ?>" <?php echo $h === 10 ? 'selected' : ''; ?>><?php echo $h; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Minute *</label>
                            <select class="form-select" id="add_minute" required>
                                <option value="00" selected>00</option>
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="45">45</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">AM/PM *</label>
                            <select class="form-select" id="add_ampm" required>
                                <option value="AM" selected>AM</option>
                                <option value="PM">PM</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <strong>Preview:</strong><br>
                        <div id="add_preview_en">English: -</div>
                        <div id="add_preview_ar">Arabic: -</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Sort Order *</label>
                        <input type="number" class="form-control" name="sort_order" value="0" required>
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="published" id="published_add" <?php echo $canPublish ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="published_add">
                                Published (visible on contact form)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Round
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Form -->
<form id="deleteForm" method="POST" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
// Add Round Label Generator
(function() {
    const dateInput = document.getElementById('add_date');
    const hourInput = document.getElementById('add_hour');
    const minuteInput = document.getElementById('add_minute');
    const ampmInput = document.getElementById('add_ampm');
    const form = document.getElementById('addForm');
    
    function updateAddPreview() {
        const date = new Date(dateInput.value + 'T12:00:00');
        if (isNaN(date.getTime())) return;
        
        const hour = hourInput.value;
        const minute = minuteInput.value;
        const ampm = ampmInput.value;
        
        // English format: Sunday 02/11/2025 — 10:00 AM
        const daysEn = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const dayNameEn = daysEn[date.getDay()];
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const labelEn = `${dayNameEn} ${day}/${month}/${year} — ${hour}:${minute} ${ampm}`;
        
        // Arabic format: الأحد 2 نوفمبر 2025 – 10:00 صباحًا
        const daysAr = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
        const monthsAr = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        const dayNameAr = daysAr[date.getDay()];
        const monthNameAr = monthsAr[date.getMonth()];
        const ampmAr = ampm === 'AM' ? 'صباحًا' : 'مساءً';
        const labelAr = `${dayNameAr} ${date.getDate()} ${monthNameAr} ${year} – ${hour}:${minute} ${ampmAr}`;
        
        document.getElementById('add_preview_en').textContent = 'English: ' + labelEn;
        document.getElementById('add_preview_ar').textContent = 'Arabic: ' + labelAr;
        document.getElementById('add_label_en').value = labelEn;
        document.getElementById('add_label_ar').value = labelAr;
        
        // Sync hidden fields for submission
        document.getElementById('add_round_date').value = dateInput.value;
        document.getElementById('add_round_hour').value = hour;
        document.getElementById('add_round_minute').value = minute;
        document.getElementById('add_round_ampm').value = ampm;
    }
    
    dateInput.addEventListener('change', updateAddPreview);
    hourInput.addEventListener('change', updateAddPreview);
    minuteInput.addEventListener('change', updateAddPreview);
    ampmInput.addEventListener('change', updateAddPreview);
    
    // Set default date to today
    const today = new Date();
    dateInput.value = today.toISOString().split('T')[0];
    updateAddPreview();
})();

function deleteRound(id) {
    if (confirm('Are you sure you want to delete this course round? This action cannot be undone.')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
