<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_messages");
$messages_count = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM testimonials");
$testimonials_count = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_users");
$admins_count = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
$recent_messages = $stmt->fetchAll();
?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="content-card text-center">
            <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
            <h3><?php echo $messages_count; ?></h3>
            <p class="text-muted mb-0">Contact Messages</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="content-card text-center">
            <i class="fas fa-quote-left fa-3x text-success mb-3"></i>
            <h3><?php echo $testimonials_count; ?></h3>
            <p class="text-muted mb-0">Testimonials</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="content-card text-center">
            <i class="fas fa-users fa-3x text-info mb-3"></i>
            <h3><?php echo $admins_count; ?></h3>
            <p class="text-muted mb-0">Admin Users</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="content-card text-center">
            <i class="fas fa-cog fa-3x text-warning mb-3"></i>
            <h3>CMS</h3>
            <p class="text-muted mb-0">Fully Operational</p>
        </div>
    </div>
</div>

<div class="content-card">
    <h5 class="mb-4"><i class="fas fa-envelope"></i> Recent Contact Messages</h5>
    <?php if (count($recent_messages) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_messages as $msg): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($msg['name']); ?></td>
                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td><?php echo htmlspecialchars(substr($msg['message'], 0, 50)) . '...'; ?></td>
                            <td><?php echo date('M d, Y', strtotime($msg['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="messages.php" class="btn btn-primary btn-sm">View All Messages</a>
    <?php else: ?>
        <p class="text-muted">No messages yet.</p>
    <?php endif; ?>
</div>

<div class="content-card mt-4">
    <h5 class="mb-3"><i class="fas fa-info-circle"></i> Quick Links</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <a href="hero.php" class="btn btn-outline-primary w-100">
                <i class="fas fa-image"></i> Edit Hero Section
            </a>
        </div>
        <div class="col-md-4">
            <a href="course_details.php" class="btn btn-outline-primary w-100">
                <i class="fas fa-book"></i> Edit Course Details
            </a>
        </div>
        <div class="col-md-4">
            <a href="site_settings.php" class="btn btn-outline-primary w-100">
                <i class="fas fa-cog"></i> Site Settings
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
