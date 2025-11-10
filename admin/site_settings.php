<?php
$page_title = 'Site Settings';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        foreach ($_POST as $key => $value) {
            if ($key !== 'submit') {
                $stmt = $pdo->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$value, $key]);
            }
        }
        $success = 'Settings updated successfully!';
    } catch(PDOException $e) {
        $error = 'Error updating settings: ' . $e->getMessage();
    }
}

$stmt = $pdo->query("SELECT * FROM site_settings");
$settings_rows = $stmt->fetchAll();
$settings = [];
foreach ($settings_rows as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>

<div class="content-card">
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <h5 class="mb-3">General Settings</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Site Name (English)</label>
                <input type="text" class="form-control" name="site_name_en" value="<?php echo htmlspecialchars($settings['site_name_en'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">اسم الموقع (عربي)</label>
                <input type="text" class="form-control" name="site_name_ar" value="<?php echo htmlspecialchars($settings['site_name_ar'] ?? ''); ?>" required dir="rtl">
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Logo URL</label>
                <div class="input-group">
                    <input type="url" class="form-control" id="logo_url" name="logo" value="<?php echo htmlspecialchars($settings['logo'] ?? ''); ?>">
                    <button type="button" class="btn btn-outline-secondary btn-choose-media" onclick="openMediaPicker('logo_url', 'logo_preview')">
                        <i class="fas fa-images"></i> Choose
                    </button>
                </div>
                <div id="logo_preview" class="image-preview-box <?php echo !empty($settings['logo']) ? 'active' : ''; ?>">
                    <img src="<?php echo !empty($settings['logo']) ? '../' . htmlspecialchars($settings['logo']) : ''; ?>" alt="Logo Preview">
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Primary Color</label>
                <input type="color" class="form-control" name="primary_color" value="<?php echo htmlspecialchars($settings['primary_color'] ?? '#a8324e'); ?>">
            </div>
        </div>
        
        <hr class="my-4">
        
        <h5 class="mb-3">Contact Information</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Contact Email (Display)</label>
                <input type="email" class="form-control" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>">
                <small class="text-muted">Shown in footer</small>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Contact Recipient Email</label>
                <input type="email" class="form-control" name="contact_recipient_email" value="<?php echo htmlspecialchars($settings['contact_recipient_email'] ?? ''); ?>">
                <small class="text-muted">Where form submissions are sent</small>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" name="contact_phone" value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>">
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Address (English)</label>
                <textarea class="form-control" name="contact_address_en" rows="2"><?php echo htmlspecialchars($settings['contact_address_en'] ?? ''); ?></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">العنوان (عربي)</label>
                <textarea class="form-control" name="contact_address_ar" rows="2" dir="rtl"><?php echo htmlspecialchars($settings['contact_address_ar'] ?? ''); ?></textarea>
            </div>
        </div>
        
        <hr class="my-4">
        
        <h5 class="mb-3">Social Media Links</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label"><i class="fab fa-facebook"></i> Facebook URL</label>
                <input type="url" class="form-control" name="facebook_url" value="<?php echo htmlspecialchars($settings['facebook_url'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label"><i class="fab fa-twitter"></i> Twitter URL</label>
                <input type="url" class="form-control" name="twitter_url" value="<?php echo htmlspecialchars($settings['twitter_url'] ?? ''); ?>">
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label"><i class="fab fa-linkedin"></i> LinkedIn URL</label>
                <input type="url" class="form-control" name="linkedin_url" value="<?php echo htmlspecialchars($settings['linkedin_url'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label"><i class="fab fa-instagram"></i> Instagram URL</label>
                <input type="url" class="form-control" name="instagram_url" value="<?php echo htmlspecialchars($settings['instagram_url'] ?? ''); ?>">
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary" name="submit">
            <i class="fas fa-save"></i> Save All Settings
        </button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
