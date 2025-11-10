<?php
require_once 'includes/db.php';

$lang = isset($_GET['lang']) && $_GET['lang'] === 'ar' ? 'ar' : 'en';
$page_title = $lang === 'en' ? 'Home - Learn Data Analysis' : 'الرئيسية - تعلم تحليل البيانات';

// Load header
require_once 'includes/site_header.php';

// Get data from database
$stmt = $pdo->query("SELECT * FROM hero_section WHERE id = 1");
$hero = $stmt->fetch();

$stmt = $pdo->query("SELECT * FROM benefits ORDER BY display_order LIMIT 4");
$benefits = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM testimonials ORDER BY display_order LIMIT 3");
$testimonials = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM faq ORDER BY display_order LIMIT 4");
$faqs = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM page_sections WHERE page_name = 'home' AND section_key = 'why_data' AND is_enabled = true");
$why_data_section = $stmt->fetch();

$stmt = $pdo->query("SELECT * FROM topics ORDER BY display_order");
$topics = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero-section" style="background-image: linear-gradient(rgba(168, 50, 78, 0.7), rgba(108, 30, 53, 0.7)), url('<?php echo htmlspecialchars($hero['background_image'] ?? 'https://via.placeholder.com/1920x600'); ?>');">
    <div class="container">
        <div class="row align-items-center min-vh-70">
            <div class="col-lg-8 text-white">
                <h1 class="display-3 fw-bold mb-4"><?php echo htmlspecialchars($hero['title_' . $lang] ?? ''); ?></h1>
                <p class="lead mb-4 fs-4"><?php echo htmlspecialchars($hero['subtitle_' . $lang] ?? ''); ?></p>
                <a href="#contact-form" class="btn btn-light btn-lg px-5">
                    <i class="fas fa-user-plus"></i> <?php echo t('enroll_now', $lang); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Why Data Analysis Section -->
<?php if ($why_data_section): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="section-title mb-4"><?php echo htmlspecialchars($why_data_section['title_' . $lang] ?? ''); ?></h2>
                <p class="lead text-muted"><?php echo nl2br(htmlspecialchars($why_data_section['body_' . $lang] ?? '')); ?></p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Key Benefits Section -->
<?php if (count($benefits) > 0): ?>
<section class="py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5"><?php echo t('benefits', $lang); ?></h2>
        <div class="row g-4">
            <?php foreach ($benefits as $benefit): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="benefit-card text-center p-4 h-100">
                        <div class="benefit-icon mb-3">
                            <i class="<?php echo htmlspecialchars($benefit['icon']); ?> fa-3x"></i>
                        </div>
                        <h5 class="mb-3"><?php echo htmlspecialchars($benefit['title_' . $lang]); ?></h5>
                        <p class="text-muted"><?php echo htmlspecialchars($benefit['description_' . $lang]); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Explore Topics Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title text-center mb-5"><?php echo t('explore_topics', $lang); ?></h2>
        <div class="row g-4">
            <?php foreach ($topics as $topic): ?>
                <div class="col-md-6 col-lg-3">
                    <a href="<?php echo htmlspecialchars($topic['slug']); ?>.php?lang=<?php echo $lang; ?>" class="text-decoration-none">
                        <div class="topic-card h-100 p-4 text-center">
                            <div class="topic-image mb-3" style="height: 150px; overflow: hidden; border-radius: 10px;">
                                <img src="<?php echo htmlspecialchars($topic['hero_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($topic['title_' . $lang]); ?>" 
                                     class="img-fluid w-100 h-100" style="object-fit: cover;">
                            </div>
                            <h5 class="mb-2"><?php echo htmlspecialchars($topic['title_' . $lang]); ?></h5>
                            <p class="text-muted small mb-3"><?php echo htmlspecialchars(substr($topic['intro_' . $lang], 0, 100)); ?>...</p>
                            <span class="btn btn-primary btn-sm"><?php echo t('read_more', $lang); ?> →</span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<?php if (count($testimonials) > 0): ?>
<section class="py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5"><?php echo t('testimonials', $lang); ?></h2>
        <div class="row g-4">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-md-4">
                    <div class="testimonial-card p-4 h-100">
                        <div class="testimonial-quote mb-3">
                            <i class="fas fa-quote-left fa-2x text-primary opacity-25"></i>
                        </div>
                        <p class="mb-4"><?php echo htmlspecialchars($testimonial['content_' . $lang]); ?></p>
                        <div class="testimonial-author">
                            <strong><?php echo htmlspecialchars($testimonial['name_' . $lang]); ?></strong>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FAQ Section -->
<?php if (count($faqs) > 0): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="section-title text-center mb-5"><?php echo t('faq', $lang); ?></h2>
                <div class="accordion" id="faqAccordion">
                    <?php foreach ($faqs as $index => $faq): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                                <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse<?php echo $index; ?>" 
                                        aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                    <?php echo htmlspecialchars($faq['question_' . $lang]); ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $index; ?>" 
                                 class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" 
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <?php echo htmlspecialchars($faq['answer_' . $lang]); ?>
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

<!-- Contact Form at Bottom -->
<?php include 'includes/contact_form_component.php'; ?>

<?php require_once 'includes/site_footer.php'; ?>
