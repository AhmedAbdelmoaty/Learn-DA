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

$stmt = $pdo->query("SELECT * FROM page_sections WHERE page_name = 'home' AND section_key = 'why_data' AND is_enabled = true");
$why_data_section = $stmt->fetch();

$stmt = $pdo->query("SELECT * FROM topics ORDER BY display_order");
$topics = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero-section" style="background-image: var(--primary-hero-gradient), url('<?php echo htmlspecialchars($hero['background_image'] ?? 'https://via.placeholder.com/1920x600'); ?>');">
    <div class="container">
        <div class="row align-items-center min-vh-60">
            <div class="col-lg-8 text-white">
                <h1 class="display-3 fw-bold mb-4"><?php echo htmlspecialchars($hero['title_' . $lang] ?? ''); ?></h1>
                <p class="lead mb-4 fs-4"><?php echo htmlspecialchars($hero['subtitle_' . $lang] ?? ''); ?></p>
                <a href="<?php echo preserveLang('training-program.php', $lang); ?>" class="btn btn-light btn-lg px-5">
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
                            <?php
                                $iconValue = trim($benefit['icon'] ?? '');
                                $iconPath = $iconValue !== '' ? (parse_url($iconValue, PHP_URL_PATH) ?? $iconValue) : '';
                                $isImage = $iconValue !== '' && (strpos($iconValue, '/') !== false || preg_match('/\.(png|jpe?g|gif|svg|webp)$/i', $iconPath));
                                $fallbackIcon = 'fa-solid fa-star';
                            ?>
                            <?php if ($isImage): ?>
                                <img src="<?php echo htmlspecialchars($iconValue); ?>" alt="<?php echo htmlspecialchars($benefit['title_' . $lang] ?? 'Benefit'); ?>" class="img-fluid" style="max-height: 80px; width: auto;">
                            <?php else: ?>
                                <i class="<?php echo htmlspecialchars($iconValue !== '' ? $iconValue : $fallbackIcon); ?> fa-3x"></i>
                            <?php endif; ?>
                        </div>
                        <h5 class="mb-3"><?php echo htmlspecialchars($benefit['title_' . $lang]); ?></h5>
                        <p class="text-muted card-text-limit"><?php echo htmlspecialchars($benefit['description_' . $lang]); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Explore Topics Section -->
<section class="py-5 bg-light">
    <div class="container position-relative">
        <h2 class="section-title text-center mb-5"><?php echo t('explore_topics', $lang); ?></h2>
        <div class="topics-slider">
            <div class="topics-track" id="topicsTrack">
                <?php foreach ($topics as $topic): ?>
                    <?php
                        $topicUrl = $topic['is_tool']
                            ? 'tool.php?slug=' . urlencode($topic['slug'])
                            : $topic['slug'] . '.php';
                    ?>
                    <div class="topic-slide">
                        <a href="<?php echo preserveLang($topicUrl, $lang); ?>" class="text-decoration-none">
                            <div class="topic-card h-100 p-4 text-center">
                                <div class="topic-image mb-3" style="height: 150px; overflow: hidden; border-radius: 10px;">
                                    <img src="<?php echo htmlspecialchars($topic['hero_image']); ?>"
                                         alt="<?php echo htmlspecialchars($topic['title_' . $lang]); ?>"
                                         class="img-fluid w-100 h-100" style="object-fit: cover;">
                                </div>
                                <h5 class="mb-2"><?php echo htmlspecialchars($topic['title_' . $lang]); ?></h5>
                                <p class="text-muted small mb-3 card-text-limit"><?php echo htmlspecialchars(substr($topic['intro_' . $lang], 0, 100)); ?>...</p>
                                <span class="btn btn-primary btn-sm"><?php echo t('read_more', $lang); ?> →</span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="topics-nav-wrapper">
                <button class="topics-nav-btn topics-nav-prev" type="button" aria-label="Scroll left">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="topics-nav-btn topics-nav-next" type="button" aria-label="Scroll right">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const track = document.getElementById('topicsTrack');
    const prevBtn = document.querySelector('.topics-nav-prev');
    const nextBtn = document.querySelector('.topics-nav-next');
    const AUTO_SCROLL_INTERVAL_MS = 4500;

    if (!track || !prevBtn || !nextBtn) return;

    const isRTL = document.documentElement.getAttribute('lang') === 'ar';
    const getScrollAmount = () => {
        const slide = track.querySelector('.topic-slide');
        const slideWidth = slide ? slide.getBoundingClientRect().width : 280;
        const styles = window.getComputedStyle(track);
        const gapValue = styles.columnGap || styles.gap || '0';
        const gap = parseFloat(gapValue) || 0;
        return slideWidth + gap;
    };

    const scrollTrack = (direction) => {
        const amount = getScrollAmount();
        const delta = direction === 'next' ? amount : -amount;
        const offset = isRTL ? -delta : delta;
        track.scrollBy({ left: offset, behavior: 'smooth' });
    };

    let autoScrollTimer = null;

    const startAutoScroll = () => {
        if (autoScrollTimer || AUTO_SCROLL_INTERVAL_MS <= 0) return;
        autoScrollTimer = setInterval(() => scrollTrack('next'), AUTO_SCROLL_INTERVAL_MS);
    };

    const stopAutoScroll = () => {
        if (!autoScrollTimer) return;
        clearInterval(autoScrollTimer);
        autoScrollTimer = null;
    };

    prevBtn.addEventListener('click', () => {
        stopAutoScroll();
        scrollTrack('prev');
        startAutoScroll();
    });

    nextBtn.addEventListener('click', () => {
        stopAutoScroll();
        scrollTrack('next');
        startAutoScroll();
    });

    const userInteractionEvents = ['mouseenter', 'touchstart', 'focusin'];
    const userExitEvents = ['mouseleave', 'touchend', 'focusout'];

    userInteractionEvents.forEach((eventName) => {
        track.addEventListener(eventName, stopAutoScroll);
        prevBtn.addEventListener(eventName, stopAutoScroll);
        nextBtn.addEventListener(eventName, stopAutoScroll);
    });

    userExitEvents.forEach((eventName) => {
        track.addEventListener(eventName, startAutoScroll);
        prevBtn.addEventListener(eventName, startAutoScroll);
        nextBtn.addEventListener(eventName, startAutoScroll);
    });

    startAutoScroll();
});
</script>

<?php require_once 'includes/site_footer.php'; ?>
