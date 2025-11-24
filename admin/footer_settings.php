<?php
$page_title = 'Footer Settings';
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
requireRole([ROLE_SUPER_ADMIN, ROLE_CONTENT_ADMIN]);
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

$stmt = $pdo->query("SELECT * FROM footer_settings");
$settings_rows = $stmt->fetchAll();
$footer_settings = [];
foreach ($settings_rows as $row) {
    $footer_settings[$row['setting_key']] = $row['setting_value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $allowed_keys = [
            'footer_about_en',
            'footer_about_ar',
            'contact_title_en',
            'contact_title_ar',
            'contact_intro_en',
            'contact_intro_ar',
            'uae_address_en',
            'uae_address_ar',
            'uae_phone',
            'egypt_address_en',
            'egypt_address_ar',
            'egypt_phone',
            'contact_email',
            'social_facebook_url',
            'social_linkedin_url',
            'social_instagram_url',
            'social_x_url',
        ];

        $updateStmt = $pdo->prepare("UPDATE footer_settings SET setting_value = ? WHERE setting_key = ?");
        $insertStmt = $pdo->prepare("INSERT INTO footer_settings (setting_key, setting_value) VALUES (?, ?)");

        foreach ($allowed_keys as $key) {
            $value = isset($_POST[$key]) ? trim($_POST[$key]) : '';
            $updateStmt->execute([$value, $key]);

            if ($updateStmt->rowCount() === 0) {
                $insertStmt->execute([$key, $value]);
            }
            $footer_settings[$key] = $value;
        }
        $success = 'Footer settings updated successfully!';
    } catch (PDOException $e) {
        $error = 'Error updating footer settings: ' . $e->getMessage();
    }
}
?>

<div class="container-fluid mt-4">
    <div class="content-card">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
            <div>
                <h5 class="mb-1">Footer & Contact Settings</h5>
                <p class="text-muted mb-0">Control all footer content and the contact information card displayed next to the contact form.</p>
            </div>
            <?php renderBulkSaveToolbar(['wrapper_class' => 'mt-3 mt-md-0']); ?>
        </div>
        <?php if ($success): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
        <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" data-bulk-save="true" data-section-name="Footer Settings">
        <!-- About Section -->
        <h6 class="mb-3 text-primary"><i class="fas fa-info-circle"></i> About IMP Section</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">About Text (English)</label>
                <textarea class="form-control" name="footer_about_en" rows="4"><?php echo htmlspecialchars($footer_settings['footer_about_en'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">About Text (Arabic)</label>
                <textarea class="form-control" name="footer_about_ar" rows="4" dir="rtl"><?php echo htmlspecialchars($footer_settings['footer_about_ar'] ?? ''); ?></textarea>
            </div>
        </div>
        <hr class="my-4">

        <!-- Contact Info Card (next to form) -->
        <h6 class="mb-3 text-primary"><i class="fas fa-address-card"></i> Contact Card Content</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Card Title (English)</label>
                <input type="text" class="form-control" name="contact_title_en" value="<?php echo htmlspecialchars($footer_settings['contact_title_en'] ?? ''); ?>" placeholder="Get In Touch">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">عنوان البطاقة (عربي)</label>
                <input type="text" class="form-control" name="contact_title_ar" value="<?php echo htmlspecialchars($footer_settings['contact_title_ar'] ?? ''); ?>" dir="rtl" placeholder="تواصل معنا">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Intro Paragraph (English)</label>
                <textarea class="form-control" name="contact_intro_en" rows="3" placeholder="We are always ready to help you and answer your questions."><?php echo htmlspecialchars($footer_settings['contact_intro_en'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">الفقرة التعريفية (عربي)</label>
                <textarea class="form-control" name="contact_intro_ar" rows="3" dir="rtl" placeholder="نحن دائمًا جاهزون لمساعدتك والإجابة على أسئلتك."><?php echo htmlspecialchars($footer_settings['contact_intro_ar'] ?? ''); ?></textarea>
            </div>
        </div>
        <hr class="my-4">
        
        <!-- U.A.E. Contact Information -->
        <h6 class="mb-3 text-primary"><i class="fas fa-map-marker-alt"></i> U.A.E. Location</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">U.A.E. Address (English)</label>
                <textarea class="form-control" name="uae_address_en" rows="3"><?php echo htmlspecialchars($footer_settings['uae_address_en'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">U.A.E. Address (Arabic)</label>
                <textarea class="form-control" name="uae_address_ar" rows="3" dir="rtl"><?php echo htmlspecialchars($footer_settings['uae_address_ar'] ?? ''); ?></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">U.A.E. Phone Number</label>
                <input type="text" class="form-control" name="uae_phone" value="<?php echo htmlspecialchars($footer_settings['uae_phone'] ?? ''); ?>" placeholder="+971 50 418 0021">
            </div>
        </div>
        
        <hr class="my-4">
        
        <!-- Egypt Contact Information -->
        <h6 class="mb-3 text-primary"><i class="fas fa-map-marker-alt"></i> Egypt Location</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Egypt Address (English)</label>
                <textarea class="form-control" name="egypt_address_en" rows="3"><?php echo htmlspecialchars($footer_settings['egypt_address_en'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Egypt Address (Arabic)</label>
                <textarea class="form-control" name="egypt_address_ar" rows="3" dir="rtl"><?php echo htmlspecialchars($footer_settings['egypt_address_ar'] ?? ''); ?></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Egypt Phone Number</label>
                <input type="text" class="form-control" name="egypt_phone" value="<?php echo htmlspecialchars($footer_settings['egypt_phone'] ?? ''); ?>" placeholder="+20 10 32244125">
            </div>
        </div>
        
        <hr class="my-4">
        
        <!-- General Contact -->
        <h6 class="mb-3 text-primary"><i class="fas fa-envelope"></i> General Contact</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" name="contact_email" value="<?php echo htmlspecialchars($footer_settings['contact_email'] ?? ''); ?>" placeholder="marketing@imanagementpro.com">
            </div>
        </div>
        <hr class="my-4">

        <!-- Social Media Links -->
        <h6 class="mb-3 text-primary"><i class="fas fa-share-alt"></i> Social Media Links</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label"><i class="fab fa-facebook"></i> Facebook URL</label>
                <input type="url" class="form-control" name="social_facebook_url" value="<?php echo htmlspecialchars($footer_settings['social_facebook_url'] ?? ''); ?>" placeholder="https://facebook.com/your-page">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label"><i class="fab fa-linkedin"></i> LinkedIn URL</label>
                <input type="url" class="form-control" name="social_linkedin_url" value="<?php echo htmlspecialchars($footer_settings['social_linkedin_url'] ?? ''); ?>" placeholder="https://linkedin.com/company/your-company">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label"><i class="fab fa-instagram"></i> Instagram URL</label>
                <input type="url" class="form-control" name="social_instagram_url" value="<?php echo htmlspecialchars($footer_settings['social_instagram_url'] ?? ''); ?>" placeholder="https://instagram.com/your-profile">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label"><i class="fab fa-x-twitter"></i> X (Twitter) URL</label>
                <input type="url" class="form-control" name="social_x_url" value="<?php echo htmlspecialchars($footer_settings['social_x_url'] ?? ''); ?>" placeholder="https://x.com/your-handle">
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" name="submit" class="btn btn-primary btn-lg bulk-save-hidden">
                <i class="fas fa-save"></i> Save Footer Settings
            </button>
            <a href="dashboard.php" class="btn btn-secondary btn-lg ms-2">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
