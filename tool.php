<?php
require_once 'includes/db.php';

$lang = isset($_GET['lang']) && $_GET['lang'] === 'ar' ? 'ar' : 'en';
$slug = $_GET['slug'] ?? '';
$tools_url = 'tools.php' . ($lang === 'ar' ? '?lang=ar' : '');

if ($slug === '') {
    header('Location: ' . $tools_url);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM topics WHERE slug = ?");
$stmt->execute([$slug]);
$topic = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$topic || !$topic['is_tool']) {
    header('Location: ' . $tools_url);
    exit;
}

$page_title = $topic['title_' . $lang] . ' - Learn Data Analysis';

$stmt = $pdo->prepare("SELECT * FROM content_items WHERE topic_id = ? AND status = 'published' ORDER BY display_order, id");
$stmt->execute([$topic['id']]);
$content_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
require_once 'includes/site_header.php';
$heroGradient = getTopicHeroGradient($topic);
?>

<section class="topic-hero py-5" style="background: <?php echo $heroGradient; ?>, url('<?php echo htmlspecialchars($topic['hero_image'] ?: 'https://via.placeholder.com/1200x400'); ?>'); background-size: cover; background-position: center;">
    <div class="container">
        <div class="row justify-content-center text-center text-white">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4"><?php echo htmlspecialchars($topic['title_' . $lang]); ?></h1>
                <p class="lead fs-4"><?php echo htmlspecialchars($topic['intro_' . $lang]); ?></p>
            </div>
        </div>
    </div>
</section>

<?php if (count($content_items) > 0): ?>
<section class="py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5"><?php echo $lang === 'en' ? 'Learn More' : 'تعلم المزيد'; ?></h2>
        <div class="row g-4">
            <?php foreach ($content_items as $item): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="<?php echo preserveLang('content.php?topic=' . urlencode($topic['slug']) . '&slug=' . urlencode($item['slug']), $lang); ?>"
                       class="text-decoration-none">
                        <div class="content-item-card h-100">
                            <?php if (!empty($item['hero_image'])): ?>
                                <img src="<?php echo htmlspecialchars($item['hero_image']); ?>"
                                     class="card-img-top"
                                     alt="<?php echo htmlspecialchars($item['title_' . $lang]); ?>"
                                     style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['title_' . $lang]); ?></h5>
                                <?php $summaryText = getCardSummaryText($item, $lang); ?>
                                <p class="card-text text-muted card-text-limit"><?php echo htmlspecialchars($summaryText); ?></p>
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

<?php require_once 'includes/site_footer.php'; ?>
