<?php
require_once 'includes/db.php';

$lang = isset($_GET['lang']) && $_GET['lang'] === 'ar' ? 'ar' : 'en';
$page_title = $lang === 'en' ? 'Training Program - Learn Data Analysis' : 'البرنامج التدريبي - تعلم تحليل البيانات';

// Load header
require_once 'includes/site_header.php';

// Get Hero section
$stmt = $pdo->query("SELECT * FROM training_hero WHERE is_enabled = 1 LIMIT 1");
$hero = $stmt->fetch();

// Get What You'll Learn items
$stmt = $pdo->query("SELECT * FROM training_learn_items WHERE published = 1 ORDER BY sort_order ASC");
$learn_items = $stmt->fetchAll();

// Get Value/Bonuses
$stmt = $pdo->query("SELECT * FROM training_bonuses WHERE published = 1 ORDER BY sort_order ASC");
$bonuses = $stmt->fetchAll();

// Get Student Outcomes
$stmt = $pdo->query("SELECT * FROM training_outcomes WHERE published = 1 ORDER BY sort_order ASC");
$outcomes = $stmt->fetchAll();

// Get Students Journey Section
$stmt = $pdo->query("SELECT * FROM training_journey_video WHERE is_enabled = TRUE LIMIT 1");
$journey_video = $stmt->fetch();

$stmt = $pdo->query("SELECT * FROM training_journey_cards WHERE published = TRUE ORDER BY sort_order ASC");
$journey_cards = $stmt->fetchAll();

// Get FAQ
$stmt = $pdo->query("SELECT * FROM training_faq WHERE published = 1 ORDER BY sort_order ASC");
$faqs = $stmt->fetchAll();
?>

<!-- Hero Section -->
<?php if ($hero): ?>
<?php
    $img = $hero['hero_image'] ?? '';
    $alt = $hero['title_' . $lang] ?? '';
    $is_rtl = ($lang === 'ar');
?>
<section class="py-5">
    <div class="container">
         <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="display-4 fw-bold mb-4 text-primary"><?php echo htmlspecialchars($hero['title_' . $lang]); ?></h1>
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

<!-- What You'll Learn Section -->
<?php if (count($learn_items) > 0): ?>
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold text-primary"><?php echo t('what_you_learn', $lang); ?></h2>
        <div class="row g-4">
            <?php foreach ($learn_items as $item): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm text-center p-4">
                        <?php if ($item['icon_image']): ?>
                            <div class="mb-3">
                                <img src="<?php echo htmlspecialchars($item['icon_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['title_' . $lang]); ?>" 
                                     style="max-height: 80px; width: auto;">
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <i class="fas fa-check-circle fa-3x text-primary"></i>
                            </div>
                        <?php endif; ?>
                        <h5 class="fw-bold mb-3"><?php echo htmlspecialchars($item['title_' . $lang]); ?></h5>
                        <?php if ($item['body_' . $lang]): ?>
                           <p class="text-muted small card-text-limit"><?php echo nl2br(htmlspecialchars($item['body_' . $lang])); ?></p>

                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Value & Bonuses Section -->
<?php if (count($bonuses) > 0): ?>
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold text-primary"><?php echo t('value_bonuses', $lang); ?></h2>
        <div class="row g-4">
            <?php foreach ($bonuses as $index => $bonus): ?>
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <?php if ($bonus['image']): ?>
                                    <div class="col-md-4 mb-3 mb-md-0 text-center">
                                        <img src="<?php echo htmlspecialchars($bonus['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($bonus['title_' . $lang]); ?>" 
                                             class="img-fluid rounded" style="max-height: 100px;">
                                    </div>
                                    <div class="col-md-8">
                                <?php else: ?>
                                    <div class="col-12">
                                <?php endif; ?>
                                        <h5 class="fw-bold mb-2 text-primary">
                                            <?php echo htmlspecialchars($bonus['title_' . $lang]); ?>
                                        </h5>
                                        <?php if ($bonus['subtitle_' . $lang]): ?>
                                            <h6 class="text-muted mb-3"><?php echo htmlspecialchars($bonus['subtitle_' . $lang]); ?></h6>
                                        <?php endif; ?>
                                        <?php if ($bonus['body_' . $lang]): ?>
                                            <p class="text-muted small mb-0"><?php echo nl2br(htmlspecialchars($bonus['body_' . $lang])); ?></p>
                                        <?php endif; ?>
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

<!-- Student Outcomes Section -->
<?php if (count($outcomes) > 0): ?>
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold text-primary"><?php echo t('student_outcomes', $lang); ?></h2>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row g-4">
                    <?php foreach ($outcomes as $outcome): ?>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <?php if ($outcome['icon_image']): ?>
                                    <div class="flex-shrink-0 me-3">
                                        <img src="<?php echo htmlspecialchars($outcome['icon_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($outcome['title_' . $lang]); ?>" 
                                             style="max-height: 50px; width: auto;">
                                    </div>
                                <?php else: ?>
                                    <div class="flex-shrink-0 me-3">
                                        <i class="fas fa-star fa-2x text-primary"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($outcome['title_' . $lang]); ?></h5>
                                    <?php if ($outcome['body_' . $lang]): ?>
                                        <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($outcome['body_' . $lang])); ?></p>
                                    <?php endif; ?>
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

<!-- Students Journey Section -->
<?php if ($journey_video || count($journey_cards) > 0): ?>
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold text-primary">
            <?php echo $lang === 'en' ? 'Hear From Our Graduates' : 'استمع إلى خريجينا'; ?>
        </h2>
        <div class="row align-items-center g-4">
            <!-- Video Block -->
            <?php if ($journey_video && !empty($journey_video['video_url'])): ?>
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="ratio ratio-16x9 shadow-sm rounded overflow-hidden">
                        <?php if (strpos($journey_video['video_url'], 'youtube.com') !== false || strpos($journey_video['video_url'], 'youtu.be') !== false): ?>
                            <?php 
                            // Extract YouTube video ID
                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $journey_video['video_url'], $match);
                            $youtube_id = $match[1] ?? '';
                            ?>
                            <?php if ($youtube_id): ?>
                                <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($youtube_id); ?>" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen>
                                </iframe>
                            <?php endif; ?>
                        <?php elseif (preg_match('/\.(mp4|webm|ogg)$/i', $journey_video['video_url'])): ?>
                            <video controls class="w-100 h-100">
                                <source src="<?php echo htmlspecialchars($journey_video['video_url']); ?>" type="video/<?php echo pathinfo($journey_video['video_url'], PATHINFO_EXTENSION); ?>">
                                Your browser does not support the video tag.
                            </video>
                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-light h-100">
                                <a href="<?php echo htmlspecialchars($journey_video['video_url']); ?>" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-play-circle me-2"></i> <?php echo $lang === 'en' ? 'Watch Video' : 'شاهد الفيديو'; ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Journey Cards -->
            <?php if (count($journey_cards) > 0): ?>
                <div class="col-lg-6">
                    <div class="row g-3">
                        <?php foreach ($journey_cards as $card): ?>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <?php if ($card['icon_image']): ?>
                                            <div class="mb-2 text-center">
                                                <img src="<?php echo htmlspecialchars($card['icon_image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($card['name_' . $lang]); ?>" 
                                                     style="max-height: 50px; width: auto;">
                                            </div>
                                        <?php else: ?>
                                            <div class="mb-2 text-center">
                                                <i class="fas fa-user-graduate fa-2x text-primary"></i>
                                            </div>
                                        <?php endif; ?>
                                        <h6 class="fw-bold text-center mb-2 text-primary">
                                            <?php echo htmlspecialchars($card['name_' . $lang]); ?>
                                        </h6>
                                        <p class="text-muted small mb-0 text-center">
                                            "<?php echo htmlspecialchars($card['quote_' . $lang]); ?>"
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FAQ Section -->
<?php if (count($faqs) > 0): ?>
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold text-primary"><?php echo t('faq', $lang); ?></h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="trainingFaqAccordion">
                    <?php foreach ($faqs as $index => $faq): ?>
                        <div class="accordion-item border-0 shadow-sm mb-3">
                            <h2 class="accordion-header" id="faqHeading<?php echo $faq['id']; ?>">
                                <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#faqCollapse<?php echo $faq['id']; ?>" 
                                        aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>" 
                                        aria-controls="faqCollapse<?php echo $faq['id']; ?>">
                                    <strong><?php echo htmlspecialchars($faq['question_' . $lang]); ?></strong>
                                </button>
                            </h2>
                            <div id="faqCollapse<?php echo $faq['id']; ?>" 
                                 class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" 
                                 aria-labelledby="faqHeading<?php echo $faq['id']; ?>" 
                                 data-bs-parent="#trainingFaqAccordion">
                                <div class="accordion-body">
                                    <?php echo nl2br(htmlspecialchars($faq['answer_' . $lang])); ?>
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

<!-- Smooth Scroll Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const smoothScrollLinks = document.querySelectorAll('.smooth-scroll');
    smoothScrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php require_once 'includes/site_footer.php'; ?>
