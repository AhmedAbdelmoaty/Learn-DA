    <?php
require_once 'includes/db.php';

$lang = isset($_GET['lang']) && $_GET['lang'] === 'ar' ? 'ar' : 'en';
$topic_slug = $_GET['topic'] ?? '';
$content_slug = $_GET['slug'] ?? '';

if (!$topic_slug || !$content_slug) {
    header('Location: index.php?lang=' . $lang);
    exit;
}

// Get topic
$stmt = $pdo->prepare("SELECT * FROM topics WHERE slug = ?");
$stmt->execute([$topic_slug]);
$topic = $stmt->fetch();

if (!$topic) {
    header('Location: index.php?lang=' . $lang);
    exit;
}

// Get content item
$stmt = $pdo->prepare("SELECT * FROM content_items WHERE topic_id = ? AND slug = ? AND status = 'published'");
$stmt->execute([$topic['id'], $content_slug]);
$content = $stmt->fetch();

if (!$content) {
    // Show 404-friendly page
    $not_found = true;
} else {
    $not_found = false;
    $page_title = $content['title_' . $lang] . ' - Learn Data Analysis';
    
    // Get related content (from same topic, excluding current item)
    $stmt = $pdo->prepare("SELECT * FROM content_items WHERE topic_id = ? AND id != ? AND status = 'published' ORDER BY display_order LIMIT 3");
    $stmt->execute([$topic['id'], $content['id']]);
    $related_items = $stmt->fetchAll();
}

// Load header
require_once 'includes/site_header.php';
?>

<?php if ($not_found): ?>
    <!-- 404 Not Found -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 text-center">
                    <h1 class="display-1 fw-bold text-primary">404</h1>
                    <h2 class="mb-4"><?php echo $lang === 'en' ? 'Content Not Found' : 'المحتوى غير موجود'; ?></h2>
                    <p class="lead text-muted mb-4">
                        <?php echo $lang === 'en' ? 'Sorry, the content you are looking for doesn\'t exist or has been removed.' : 'عذرًا ، المحتوى الذي تبحث عنه غير موجود أو تمت إزالته.'; ?>
                    </p>
                    <a href="<?php echo htmlspecialchars($topic_slug); ?>.php?lang=<?php echo $lang; ?>" class="btn btn-primary">
                        <?php echo t('back_to', $lang); ?> <?php echo htmlspecialchars($topic['title_' . $lang]); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
<?php else: ?>
    <!-- Hero Section with Background Image and Text Overlay -->
    <?php 
    $hero_img = $lang === 'ar' && !empty($content['hero_image_ar']) 
        ? $content['hero_image_ar'] 
        : $content['hero_image'];
    
    $hero_has_image = !empty($hero_img);
    $hero_section_classes = 'content-hero-section' . ($hero_has_image ? '' : ' no-image');
    $hero_background_style = $hero_has_image
        ? "style=\"background-image: url('" . htmlspecialchars($hero_img) . "');\""
        : '';
    ?>
    <section class="<?php echo $hero_section_classes; ?>" <?php echo $hero_background_style; ?>>
        <div class="content-hero-overlay"></div>
        <div class="container position-relative">
            <div class="row gx-0">
                <div class="col-12 <?php echo $lang === 'ar' ? 'ms-auto text-end' : ''; ?>">
                    
                    <h1 class="content-hero-title"><?php echo htmlspecialchars($content['title_' . $lang]); ?></h1>
                    <?php if ($content['summary_' . $lang]): ?>
                        <p class="content-hero-subtitle"><?php echo htmlspecialchars($content['summary_' . $lang]); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

              <section class="content-body-section">
                <div class="container">
                  <div class="row gx-0"> 
                    <div class="col-12">  
                      <div class="content-body">
                        <?php echo $content['body_' . $lang]; ?>
                      </div>
                    
                    <hr class="my-5">

                    <div class="content-cta-wrapper text-center">
                        <?php $ctaNote = trim($content['cta_note_' . $lang] ?? ''); ?>
                        <?php if ($ctaNote !== ''): ?>
                            <p class="content-cta-note mb-3"><?php echo nl2br(htmlspecialchars($ctaNote)); ?></p>
                        <?php endif; ?>
                        <a href="<?php echo preserveLang('training-program.php', $lang); ?>" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-user-plus"></i> <?php echo t('enroll_now', $lang); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Content -->
    <?php if (count($related_items) > 0): ?>
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center mb-5"><?php echo t('related_content', $lang); ?></h2>
            <div class="row g-4">
                <?php foreach ($related_items as $item): ?>
                    <div class="col-md-4">
                        <a href="content.php?topic=<?php echo urlencode($topic_slug); ?>&slug=<?php echo urlencode($item['slug']); ?>&lang=<?php echo $lang; ?>" 
                           class="text-decoration-none">
                            <div class="content-item-card h-100">
                                <?php if ($item['hero_image']): ?>
                                    <img src="<?php echo htmlspecialchars($item['hero_image']); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($item['title_' . $lang]); ?>"
                                         style="height: 150px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo htmlspecialchars($item['title_' . $lang]); ?></h6>
                                    <?php $relatedSummary = getCardSummaryText($item, $lang, 80); ?>
                                    <p class="card-text text-muted small card-text-limit"><?php echo htmlspecialchars($relatedSummary); ?></p>
                                    <span class="btn btn-sm btn-outline-primary"><?php echo t('read_more', $lang); ?> →</span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
<?php endif; ?>

<?php require_once 'includes/site_footer.php'; ?>
