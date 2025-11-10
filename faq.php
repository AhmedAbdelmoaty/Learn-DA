<?php
require_once 'includes/db.php';

$lang = isset($_GET['lang']) && $_GET['lang'] === 'ar' ? 'ar' : 'en';
$page_title = $lang === 'en' ? 'FAQ - Learn Data Analysis' : 'الأسئلة الشائعة - تعلم تحليل البيانات';

// Load header
require_once 'includes/site_header.php';

// Get Hero section
$stmt = $pdo->query("SELECT * FROM faq_hero WHERE is_published = 1 LIMIT 1");
$hero = $stmt->fetch();

// Get Top Questions (Featured)
$stmt = $pdo->query("SELECT * FROM faq_top_questions WHERE published = 1 ORDER BY sort_order ASC");
$top_questions = $stmt->fetchAll();

// Get All Questions
$stmt = $pdo->query("SELECT * FROM faq_all_questions WHERE published = 1 ORDER BY sort_order ASC");
$all_questions = $stmt->fetchAll();

$is_rtl = ($lang === 'ar');
?>

<!-- Hero Section -->
<?php if ($hero): ?>
<?php 
    $img = $lang === 'en' ? ($hero['hero_image'] ?? '') : ($hero['hero_image_ar'] ?? $hero['hero_image'] ?? '');
    $alt = $lang === 'en' ? ($hero['hero_image_alt'] ?? '') : ($hero['hero_image_alt_ar'] ?? $hero['hero_image_alt'] ?? '');
?>
<section class="py-5">
    <div class="container">
        <div class="row align-items-center <?php echo $is_rtl ? 'flex-row-reverse' : ''; ?>">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="display-4 fw-bold mb-4" style="color: #a8324e;"><?php echo htmlspecialchars($hero['title_' . $lang]); ?></h1>
                <?php if ($hero['subtitle_' . $lang]): ?>
                    <p class="lead mb-4 text-muted"><?php echo htmlspecialchars($hero['subtitle_' . $lang]); ?></p>
                <?php endif; ?>
            </div>
            <?php if ($img): ?>
                <div class="col-lg-6">
                    <img src="<?php echo htmlspecialchars($img); ?>" 
                         alt="<?php echo htmlspecialchars($alt ?: $hero['title_' . $lang]); ?>" 
                         class="img-fluid" 
                         style="object-fit: contain; max-height: 420px; width: 100%;">
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Top Questions (Featured) Section -->
<?php if (count($top_questions) > 0): ?>
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold" style="color: #a8324e;"><?php echo t('top_questions', $lang); ?></h2>
        <div class="row g-4">
            <?php foreach ($top_questions as $index => $q): ?>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm top-question-card" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px; background-color: rgba(168, 50, 78, 0.1); border-radius: 50%;">
                                        <i class="fas fa-question" style="color: #a8324e;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fw-bold mb-3" style="color: #a8324e; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                                        <?php echo htmlspecialchars($q['question_' . $lang]); ?>
                                    </h5>
                                    <p class="mb-0 text-muted" style="line-height: 1.7; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                                        <?php echo nl2br(htmlspecialchars($q['answer_' . $lang])); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- All Questions Section -->
<?php if (count($all_questions) > 0): ?>
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold" style="color: #a8324e;"><?php echo t('all_questions', $lang); ?></h2>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="accordion" id="allQuestionsAccordion">
                    <?php foreach ($all_questions as $index => $q): ?>
                        <div class="accordion-item border-0 mb-3 shadow-sm" style="border-radius: 8px; overflow: hidden;">
                            <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                <button class="accordion-button collapsed fw-bold" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse<?php echo $index; ?>" 
                                        aria-expanded="false" 
                                        aria-controls="collapse<?php echo $index; ?>"
                                        style="background-color: #fff; color: #333; border: none; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                                    <?php echo htmlspecialchars($q['question_' . $lang]); ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $index; ?>" 
                                 class="accordion-collapse collapse" 
                                 aria-labelledby="heading<?php echo $index; ?>" 
                                 data-bs-parent="#allQuestionsAccordion">
                                <div class="accordion-body bg-light">
                                    <p class="mb-0 text-muted"><?php echo nl2br(htmlspecialchars($q['answer_' . $lang])); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contact Form -->
<?php include 'includes/contact_form_component.php'; ?>

<style>
/* Custom styling for FAQ page */

/* Top Questions Cards */
.top-question-card {
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
}

.top-question-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 .75rem 1.5rem rgba(168, 50, 78, 0.15) !important;
}

/* All Questions Accordion */
.accordion-button:not(.collapsed) {
    background-color: #a8324e !important;
    color: white !important;
}

.accordion-button:not(.collapsed)::after {
    filter: brightness(0) invert(1);
}

.accordion-button:hover {
    background-color: #f8f9fa;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: transparent;
}
</style>

<?php require_once 'includes/site_footer.php'; ?>
