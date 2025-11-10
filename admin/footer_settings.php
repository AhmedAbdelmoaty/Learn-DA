<?php
$page_title = 'Footer Settings';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        foreach ($_POST as $key => $value) {
            if ($key !== 'submit') {
                $stmt = $pdo->prepare("UPDATE footer_settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$value, $key]);
                
                if ($stmt->rowCount() === 0) {
                    $stmt = $pdo->prepare("INSERT INTO footer_settings (setting_key, setting_value) VALUES (?, ?)");
                    $stmt->execute([$key, $value]);
                }
            }
        }
        $success = 'Footer settings updated successfully!';
    } catch(PDOException $e) {
        $error = 'Error updating footer settings: ' . $e->getMessage();
    }
}

$stmt = $pdo->query("SELECT * FROM footer_settings");
$settings_rows = $stmt->fetchAll();
$footer_settings = [];
foreach ($settings_rows as $row) {
    $footer_settings[$row['setting_key']] = $row['setting_value'];
}
?>

<div class="content-card">
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    
    <h5 class="mb-4">Footer Settings</h5>
    <p class="text-muted">Manage the content that appears in the website footer</p>
    
    <form method="POST">
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
        
        <div class="mt-4">
            <button type="submit" name="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save"></i> Save Footer Settings
            </button>
            <a href="dashboard.php" class="btn btn-secondary btn-lg ms-2">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
