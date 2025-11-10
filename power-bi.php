<?php
require_once 'includes/db.php';

$lang = isset($_GET['lang']) && $_GET['lang'] === 'ar' ? 'ar' : 'en';
$topic_slug = 'power-bi';

// Load header
require_once 'includes/site_header.php';

// Get topic information
$stmt = $pdo->prepare("SELECT * FROM topics WHERE slug = ?");
$stmt->execute([$topic_slug]);
$topic = $stmt->fetch();

if (!$topic) {
    header('Location: index.php?lang=' . $lang);
    exit;
}

$page_title = $topic['title_' . $lang] . ' - Learn Data Analysis';

// Get content items for this topic
$stmt = $pdo->prepare("SELECT * FROM content_items WHERE topic_id = ? AND status = 'published' ORDER BY display_order");
$stmt->execute([$topic['id']]);
$content_items = $stmt->fetchAll();
?>

<!-- Topic Hero -->
<section class="topic-hero py-5" style="background: linear-gradient(rgba(255, 152, 0, 0.8), rgba(230, 81, 0, 0.8)), url('<?php echo htmlspecialchars($topic['hero_image']); ?>'); background-size: cover; background-position: center;">
    <div class="container">
        <div class="row justify-content-center text-center text-white">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4"><?php echo htmlspecialchars($topic['title_' . $lang]); ?></h1>
                <p class="lead fs-4"><?php echo htmlspecialchars($topic['intro_' . $lang]); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Content Items Grid -->
<?php if (count($content_items) > 0): ?>
<section class="py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5"><?php echo $lang === 'en' ? 'Learn More' : 'تعلم المزيد'; ?></h2>
        <div class="row g-4">
            <?php foreach ($content_items as $item): ?>
                <div class="col-md-6">
                    <a href="content.php?topic=<?php echo urlencode($topic_slug); ?>&slug=<?php echo urlencode($item['slug']); ?>&lang=<?php echo $lang; ?>" 
                       class="text-decoration-none">
                        <div class="content-item-card h-100">
                            <?php if ($item['hero_image']): ?>
                                <img src="<?php echo htmlspecialchars($item['hero_image']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($item['title_' . $lang]); ?>"
                                     style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['title_' . $lang]); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($item['summary_' . $lang]); ?></p>
                                <span class="btn btn-primary btn-sm"><?php echo t('read_more', $lang); ?> →</span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contact Form at Bottom -->
<?php include 'includes/contact_form_component.php'; ?>

<?php require_once 'includes/site_footer.php'; ?>
