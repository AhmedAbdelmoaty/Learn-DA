<?php
$page_title = 'FAQ Page Management';
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';
$active_tab = $_GET['tab'] ?? 'hero';

// Hero Section Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_hero') {
    $title_en = $_POST['title_en'] ?? '';
    $title_ar = $_POST['title_ar'] ?? '';
    $subtitle_en = $_POST['subtitle_en'] ?? '';
    $subtitle_ar = $_POST['subtitle_ar'] ?? '';
    $hero_image_en = trim($_POST['hero_image'] ?? '');
    $hero_image_alt_en = trim($_POST['hero_image_alt'] ?? '');
    $hero_image_ar = trim($_POST['hero_image_ar'] ?? '');
    $hero_image_alt_ar = trim($_POST['hero_image_alt_ar'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM faq_hero");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        $stmt = $pdo->prepare("UPDATE faq_hero SET title_en = ?, title_ar = ?, subtitle_en = ?, subtitle_ar = ?, hero_image = ?, hero_image_alt = ?, hero_image_ar = ?, hero_image_alt_ar = ?, is_published = ? WHERE id = (SELECT MIN(id) FROM faq_hero)");
        $stmt->execute([$title_en, $title_ar, $subtitle_en, $subtitle_ar, $hero_image_en, $hero_image_alt_en, $hero_image_ar, $hero_image_alt_ar, $is_published]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO faq_hero (title_en, title_ar, subtitle_en, subtitle_ar, hero_image, hero_image_alt, hero_image_ar, hero_image_alt_ar, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title_en, $title_ar, $subtitle_en, $subtitle_ar, $hero_image_en, $hero_image_alt_en, $hero_image_ar, $hero_image_alt_ar, $is_published]);
    }
    
    $success_message = 'Hero section updated successfully!';
    $active_tab = 'hero';
}

// Top Questions Handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_top_question') {
    $stmt = $pdo->prepare("INSERT INTO faq_top_questions (question_en, question_ar, answer_en, answer_ar, sort_order, published) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['question_en'], $_POST['question_ar'], $_POST['answer_en'], $_POST['answer_ar'], intval($_POST['sort_order']), isset($_POST['published']) ? 1 : 0]);
    $success_message = 'Top question added successfully!';
    $active_tab = 'top_questions';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_top_question') {
    $stmt = $pdo->prepare("UPDATE faq_top_questions SET question_en = ?, question_ar = ?, answer_en = ?, answer_ar = ?, sort_order = ?, published = ? WHERE id = ?");
    $stmt->execute([$_POST['question_en'], $_POST['question_ar'], $_POST['answer_en'], $_POST['answer_ar'], intval($_POST['sort_order']), isset($_POST['published']) ? 1 : 0, intval($_POST['id'])]);
    $success_message = 'Top question updated successfully!';
    $active_tab = 'top_questions';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_top_question') {
    $stmt = $pdo->prepare("DELETE FROM faq_top_questions WHERE id = ?");
    $stmt->execute([intval($_POST['id'])]);
    $success_message = 'Top question deleted successfully!';
    $active_tab = 'top_questions';
}

// All Questions Handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_all_question') {
    $stmt = $pdo->prepare("INSERT INTO faq_all_questions (question_en, question_ar, answer_en, answer_ar, sort_order, published) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['question_en'], $_POST['question_ar'], $_POST['answer_en'], $_POST['answer_ar'], intval($_POST['sort_order']), isset($_POST['published']) ? 1 : 0]);
    $success_message = 'Question added successfully!';
    $active_tab = 'all_questions';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_all_question') {
    $stmt = $pdo->prepare("UPDATE faq_all_questions SET question_en = ?, question_ar = ?, answer_en = ?, answer_ar = ?, sort_order = ?, published = ? WHERE id = ?");
    $stmt->execute([$_POST['question_en'], $_POST['question_ar'], $_POST['answer_en'], $_POST['answer_ar'], intval($_POST['sort_order']), isset($_POST['published']) ? 1 : 0, intval($_POST['id'])]);
    $success_message = 'Question updated successfully!';
    $active_tab = 'all_questions';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_all_question') {
    $stmt = $pdo->prepare("DELETE FROM faq_all_questions WHERE id = ?");
    $stmt->execute([intval($_POST['id'])]);
    $success_message = 'Question deleted successfully!';
    $active_tab = 'all_questions';
}

// Load data
$stmt = $pdo->query("SELECT * FROM faq_hero LIMIT 1");
$hero = $stmt->fetch();

$stmt = $pdo->query("SELECT * FROM faq_top_questions ORDER BY sort_order ASC");
$top_questions = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM faq_all_questions ORDER BY sort_order ASC");
$all_questions = $stmt->fetchAll();
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-question-circle"></i> FAQ Page Management</h2>
            <p class="text-muted">Manage all sections of your FAQ page from one place</p>
        </div>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <ul class="nav nav-tabs" id="faqPageTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $active_tab === 'hero' ? 'active' : ''; ?>" id="hero-tab" data-bs-toggle="tab" data-bs-target="#hero" type="button" role="tab">
                <i class="fas fa-flag"></i> Hero Section
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $active_tab === 'top_questions' ? 'active' : ''; ?>" id="top-questions-tab" data-bs-toggle="tab" data-bs-target="#top-questions" type="button" role="tab">
                <i class="fas fa-star"></i> Top Questions
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?php echo $active_tab === 'all_questions' ? 'active' : ''; ?>" id="all-questions-tab" data-bs-toggle="tab" data-bs-target="#all-questions" type="button" role="tab">
                <i class="fas fa-list"></i> All Questions
            </button>
        </li>
    </ul>

    <div class="tab-content border border-top-0 p-4 bg-white" id="faqPageTabsContent">
        
        <!-- Hero Tab -->
        <div class="tab-pane fade <?php echo $active_tab === 'hero' ? 'show active' : ''; ?>" id="hero" role="tabpanel">
            <h4 class="mb-4">Hero Section</h4>
            <form method="POST" action="?tab=hero">
                <input type="hidden" name="action" value="update_hero">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title (English)</label>
                        <input type="text" class="form-control" name="title_en" value="<?php echo htmlspecialchars($hero['title_en'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title (Arabic)</label>
                        <input type="text" class="form-control" name="title_ar" dir="rtl" value="<?php echo htmlspecialchars($hero['title_ar'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Subtitle (English)</label>
                        <textarea class="form-control" name="subtitle_en" rows="2"><?php echo htmlspecialchars($hero['subtitle_en'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Subtitle (Arabic)</label>
                        <textarea class="form-control" name="subtitle_ar" dir="rtl" rows="2"><?php echo htmlspecialchars($hero['subtitle_ar'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hero Image - English (Optional)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="hero_image" name="hero_image" value="<?php echo htmlspecialchars($hero['hero_image'] ?? ''); ?>" placeholder="Image URL or path">
                            <button class="btn btn-outline-secondary" type="button" onclick="openMediaPicker('hero_image')">
                                <i class="fas fa-images"></i> Choose
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hero Image - Arabic (Optional)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="hero_image_ar" name="hero_image_ar" value="<?php echo htmlspecialchars($hero['hero_image_ar'] ?? ''); ?>" placeholder="Image URL or path" dir="rtl">
                            <button class="btn btn-outline-secondary" type="button" onclick="openMediaPicker('hero_image_ar')">
                                <i class="fas fa-images"></i> Choose
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Image ALT Text - English (Optional)</label>
                        <input type="text" class="form-control" name="hero_image_alt" value="<?php echo htmlspecialchars($hero['hero_image_alt'] ?? ''); ?>" placeholder="Describe the image">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Image ALT Text - Arabic (Optional)</label>
                        <input type="text" class="form-control" name="hero_image_alt_ar" value="<?php echo htmlspecialchars($hero['hero_image_alt_ar'] ?? ''); ?>" placeholder="وصف الصورة" dir="rtl">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_published" id="hero_published" <?php echo (!empty($hero['is_published'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="hero_published">
                            Display Hero Section
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Hero Section
                </button>
            </form>
        </div>

        <!-- Top Questions Tab -->
        <div class="tab-pane fade <?php echo $active_tab === 'top_questions' ? 'show active' : ''; ?>" id="top-questions" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Top Questions (Featured)</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTopQuestionModal">
                    <i class="fas fa-plus"></i> Add Top Question
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Sort</th>
                            <th>Question (EN)</th>
                            <th>Question (AR)</th>
                            <th>Published</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($top_questions as $q): ?>
                            <tr>
                                <td><?php echo $q['sort_order']; ?></td>
                                <td><?php echo htmlspecialchars(mb_substr($q['question_en'], 0, 60)) . (mb_strlen($q['question_en']) > 60 ? '...' : ''); ?></td>
                                <td dir="rtl"><?php echo htmlspecialchars(mb_substr($q['question_ar'], 0, 60)) . (mb_strlen($q['question_ar']) > 60 ? '...' : ''); ?></td>
                                <td>
                                    <?php if ($q['published']): ?>
                                        <span class="badge bg-success">Yes</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editTopQuestion(<?php echo htmlspecialchars(json_encode($q)); ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                        <input type="hidden" name="action" value="delete_top_question">
                                        <input type="hidden" name="id" value="<?php echo $q['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($top_questions)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No top questions added yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- All Questions Tab -->
        <div class="tab-pane fade <?php echo $active_tab === 'all_questions' ? 'show active' : ''; ?>" id="all-questions" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">All Questions</h4>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAllQuestionModal">
                    <i class="fas fa-plus"></i> Add Question
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Sort</th>
                            <th>Question (EN)</th>
                            <th>Question (AR)</th>
                            <th>Published</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_questions as $q): ?>
                            <tr>
                                <td><?php echo $q['sort_order']; ?></td>
                                <td><?php echo htmlspecialchars(mb_substr($q['question_en'], 0, 60)) . (mb_strlen($q['question_en']) > 60 ? '...' : ''); ?></td>
                                <td dir="rtl"><?php echo htmlspecialchars(mb_substr($q['question_ar'], 0, 60)) . (mb_strlen($q['question_ar']) > 60 ? '...' : ''); ?></td>
                                <td>
                                    <?php if ($q['published']): ?>
                                        <span class="badge bg-success">Yes</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editAllQuestion(<?php echo htmlspecialchars(json_encode($q)); ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                        <input type="hidden" name="action" value="delete_all_question">
                                        <input type="hidden" name="id" value="<?php echo $q['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($all_questions)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No questions added yet</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Add Top Question Modal -->
<div class="modal fade" id="addTopQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="?tab=top_questions">
                <input type="hidden" name="action" value="add_top_question">
                <div class="modal-header">
                    <h5 class="modal-title">Add Top Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Question (English)</label>
                        <textarea class="form-control" name="question_en" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Question (Arabic)</label>
                        <textarea class="form-control" name="question_ar" dir="rtl" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer (English)</label>
                        <textarea class="form-control" name="answer_en" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer (Arabic)</label>
                        <textarea class="form-control" name="answer_ar" dir="rtl" rows="4" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" value="1" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="published" id="add_top_published" checked>
                                <label class="form-check-label" for="add_top_published">Published</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Top Question Modal -->
<div class="modal fade" id="editTopQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="?tab=top_questions">
                <input type="hidden" name="action" value="update_top_question">
                <input type="hidden" name="id" id="edit_top_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Top Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Question (English)</label>
                        <textarea class="form-control" name="question_en" id="edit_top_question_en" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Question (Arabic)</label>
                        <textarea class="form-control" name="question_ar" id="edit_top_question_ar" dir="rtl" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer (English)</label>
                        <textarea class="form-control" name="answer_en" id="edit_top_answer_en" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer (Arabic)</label>
                        <textarea class="form-control" name="answer_ar" id="edit_top_answer_ar" dir="rtl" rows="4" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" id="edit_top_sort_order" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="published" id="edit_top_published">
                                <label class="form-check-label" for="edit_top_published">Published</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add All Question Modal -->
<div class="modal fade" id="addAllQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="?tab=all_questions">
                <input type="hidden" name="action" value="add_all_question">
                <div class="modal-header">
                    <h5 class="modal-title">Add Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Question (English)</label>
                        <textarea class="form-control" name="question_en" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Question (Arabic)</label>
                        <textarea class="form-control" name="question_ar" dir="rtl" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer (English)</label>
                        <textarea class="form-control" name="answer_en" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer (Arabic)</label>
                        <textarea class="form-control" name="answer_ar" dir="rtl" rows="4" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" value="1" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="published" id="add_all_published" checked>
                                <label class="form-check-label" for="add_all_published">Published</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit All Question Modal -->
<div class="modal fade" id="editAllQuestionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="?tab=all_questions">
                <input type="hidden" name="action" value="update_all_question">
                <input type="hidden" name="id" id="edit_all_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Question (English)</label>
                        <textarea class="form-control" name="question_en" id="edit_all_question_en" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Question (Arabic)</label>
                        <textarea class="form-control" name="question_ar" id="edit_all_question_ar" dir="rtl" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer (English)</label>
                        <textarea class="form-control" name="answer_en" id="edit_all_answer_en" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Answer (Arabic)</label>
                        <textarea class="form-control" name="answer_ar" id="edit_all_answer_ar" dir="rtl" rows="4" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" class="form-control" name="sort_order" id="edit_all_sort_order" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" name="published" id="edit_all_published">
                                <label class="form-check-label" for="edit_all_published">Published</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editTopQuestion(data) {
    document.getElementById('edit_top_id').value = data.id;
    document.getElementById('edit_top_question_en').value = data.question_en;
    document.getElementById('edit_top_question_ar').value = data.question_ar;
    document.getElementById('edit_top_answer_en').value = data.answer_en;
    document.getElementById('edit_top_answer_ar').value = data.answer_ar;
    document.getElementById('edit_top_sort_order').value = data.sort_order;
    document.getElementById('edit_top_published').checked = data.published == 1;
    new bootstrap.Modal(document.getElementById('editTopQuestionModal')).show();
}

function editAllQuestion(data) {
    document.getElementById('edit_all_id').value = data.id;
    document.getElementById('edit_all_question_en').value = data.question_en;
    document.getElementById('edit_all_question_ar').value = data.question_ar;
    document.getElementById('edit_all_answer_en').value = data.answer_en;
    document.getElementById('edit_all_answer_ar').value = data.answer_ar;
    document.getElementById('edit_all_sort_order').value = data.sort_order;
    document.getElementById('edit_all_published').checked = data.published == 1;
    new bootstrap.Modal(document.getElementById('editAllQuestionModal')).show();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
