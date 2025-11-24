<?php
$rounds_stmt = $pdo->query("SELECT * FROM course_rounds WHERE published = 1 ORDER BY sort_order ASC, id DESC");
$course_rounds = $rounds_stmt->fetchAll();


function get_footer_setting($key, $default = '')
{
    if (isset($GLOBALS['footer_settings']) && is_array($GLOBALS['footer_settings']) && array_key_exists($key, $GLOBALS['footer_settings'])) {
        return $GLOBALS['footer_settings'][$key];
    }

    if (isset($GLOBALS['pdo'])) {
        static $cache = [];
        if (!array_key_exists($key, $cache)) {
            try {
                $stmt = $GLOBALS['pdo']->prepare("SELECT setting_value FROM footer_settings WHERE setting_key = :k LIMIT 1");
                $stmt->execute([':k' => $key]);
                $cache[$key] = ($row = $stmt->fetchColumn()) !== false ? $row : $default;
            } catch (Exception $e) {
                $cache[$key] = $default;
            }
        }
        return $cache[$key];
    }

    return $default;
}

$lang = isset($lang) ? $lang : 'en';

$contact_title = get_footer_setting('contact_title_' . $lang, $lang === 'ar' ? 'ارسل رسالة' : 'Get In Touch');
$contact_intro = get_footer_setting('contact_intro_' . $lang, $lang === 'ar' ? 'نحن دائمًا جاهزون لمساعدتك والإجابة على أسئلتك.' : 'We are always ready to help you and answer your questions');

$uae_address = get_footer_setting('uae_address_' . $lang, '');
$uae_phone   = get_footer_setting('uae_phone', '');
$egy_address = get_footer_setting('egypt_address_' . $lang, '');
$egy_phone   = get_footer_setting('egypt_phone', '');

$contact_email = get_footer_setting('contact_email', isset($settings['contact_email']) ? $settings['contact_email'] : '');

$social_facebook  = get_footer_setting('social_facebook_url', '');
$social_linkedin  = get_footer_setting('social_linkedin_url', '');
$social_x         = get_footer_setting('social_x_url', ''); // X (Twitter)
$social_instagram = get_footer_setting('social_instagram_url', '');

$uae_title = ($lang === 'ar') ? 'الإمارات العربية المتحدة' : 'U.A.E.';
$egy_title = ($lang === 'ar') ? 'جمهورية مصر العربية' : 'Egypt';

$infoOrder = 'order-lg-1';
$formOrder = 'order-lg-2';
?>

<!-- Contact Form Section (appears at bottom of every page) -->
<section id="contact" class="contact-form-section py-5 bg-light">
    <div class="container">
        <div class="row g-5 align-items-start">
                <!-- Info Column -->
                <div class="col-lg-5 col-xl-4.5 <?php echo $infoOrder; ?>">
                    <div class="card shadow-sm border-0 rounded-3 h-100">
                        <div class="card-body p-4 p-lg-4 ps-lg-5">
                            <h2 class="mb-4"><?php echo htmlspecialchars($contact_title); ?></h2>
                            <p class="text-muted mb-4"><?php echo htmlspecialchars($contact_intro); ?></p>

                            <!-- UAE Block -->
                            <div class="mb-4">
                                <div class="d-flex gap-3 mb-2">
                                    <i class="fas fa-map-marker-alt fs-4 mt-1"></i>
                                    <div>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($uae_title); ?></div>
                                        <?php if ($uae_address): ?>
                                            <div class="text-muted"><?php echo nl2br(htmlspecialchars($uae_address)); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if ($uae_phone): ?>
                                    <div class="d-flex gap-3">
                                        <i class="fab fa-whatsapp fs-4 mt-1"></i>
                                        <div class="text-muted text-ltr" dir="ltr"><?php echo htmlspecialchars($uae_phone); ?></div>

                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Egypt Block -->
                            <div class="mb-4">
                                <div class="d-flex gap-3 mb-2">
                                    <i class="fas fa-map-marker-alt fs-4 mt-1"></i>
                                    <div>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($egy_title); ?></div>
                                        <?php if ($egy_address): ?>
                                            <div class="text-muted"><?php echo nl2br(htmlspecialchars($egy_address)); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if ($egy_phone): ?>
                                    <div class="d-flex gap-3">
                                        <i class="fab fa-whatsapp fs-4 mt-1"></i>
                                        <div class="text-muted text-ltr" dir="ltr"><?php echo htmlspecialchars($egy_phone); ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Email -->
                            <?php if ($contact_email): ?>
                            <div class="d-flex gap-3 mb-4">
                                <i class="fas fa-envelope fs-4 mt-1"></i>
                                <a class="text-decoration-none" href="mailto:<?php echo htmlspecialchars($contact_email); ?>">
                                    <?php echo htmlspecialchars($contact_email); ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Form Column -->
                <div class="col-lg-7 col-xl-7.5 <?php echo $formOrder; ?>">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-4">
                            <h2 class="mb-4"><?php echo t('contact_us', $lang); ?></h2>
                            <span id="contact-form"></span>
                            <form action="send_message.php" method="POST" id="contactForm">
                                <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                                <input type="hidden" name="lang" value="<?php echo $lang; ?>">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><?php echo t('name', $lang); ?></label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><?php echo t('email', $lang); ?></label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><?php echo t('phone', $lang); ?></label>
                                    <input type="tel" class="form-control" name="phone">
                                </div>

                                <?php if (count($course_rounds) > 0): ?>
                                    <div class="mb-3">
                                        <label class="form-label"><?php echo t('choose_course_date', $lang); ?></label>
                                        <select class="form-select" name="selected_round_id" required>
                                            <option value="">-- <?php echo t('choose_course_date', $lang); ?> --</option>
                                            <?php foreach ($course_rounds as $round): ?>
                                                <option value="<?php echo $round['id']; ?>">
                                                    <?php echo htmlspecialchars($round['label_' . $lang]); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php else: ?>
                                    <div class="mb-3">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle"></i> <?php echo t('no_rounds_available', $lang); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label"><?php echo t('message', $lang); ?></label>
                                    <textarea class="form-control" name="message" rows="4" required></textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="fas fa-paper-plane"></i> <?php echo t('send_message', $lang); ?>
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
    </div>
</section>
