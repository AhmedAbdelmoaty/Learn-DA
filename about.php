<?php
require_once 'includes/db.php';

$lang = isset($_GET['lang']) && $_GET['lang'] === 'ar' ? 'ar' : 'en';
$page_title = $lang === 'en' ? 'About Us - Learn Data Analysis' : 'من نحن - تعلم تحليل البيانات';

// Load header
require_once 'includes/site_header.php';

// Get About page sections
$stmt = $pdo->query("SELECT * FROM page_sections WHERE page_name = 'about' AND is_enabled = true ORDER BY display_order");
$sections = $stmt->fetchAll();
?>

<!-- About Page Sections -->
<?php foreach ($sections as $index => $section): ?>
    <section class="py-5 <?php echo $index % 2 === 0 ? 'bg-light' : ''; ?>">
        <div class="container">
            <div class="row align-items-center <?php echo $index % 2 === 1 ? 'flex-row-reverse' : ''; ?>">
                <?php if ($section['image']): ?>
                    <div class="col-md-6 mb-4 mb-md-0">
                        <img src="<?php echo htmlspecialchars($section['image']); ?>" 
                             alt="<?php echo htmlspecialchars($section['title_' . $lang]); ?>" 
                             class="img-fluid rounded shadow">
                    </div>
                <?php endif; ?>
                <div class="col-md-<?php echo $section['image'] ? '6' : '12'; ?>">
                    <h2 class="mb-3"><?php echo htmlspecialchars($section['title_' . $lang]); ?></h2>
                    <?php if ($section['subtitle_' . $lang]): ?>
                        <h4 class="text-primary mb-3"><?php echo htmlspecialchars($section['subtitle_' . $lang]); ?></h4>
                    <?php endif; ?>
                    <p class="lead text-muted"><?php echo nl2br(htmlspecialchars($section['body_' . $lang])); ?></p>
                </div>
            </div>
        </div>
    </section>
<?php endforeach; ?>

<?php if (count($sections) === 0): ?>
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="mb-4"><?php echo t('about', $lang); ?></h2>
                    <p class="lead text-muted"><?php echo $lang === 'en' ? 'Content coming soon. Check back later!' : 'المحتوى قادم قريبا. تحقق مرة أخرى لاحقا!'; ?></p>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Contact Form at Bottom -->
<?php include 'includes/contact_form_component.php'; ?>

<?php require_once 'includes/site_footer.php'; ?>
