<?php
$page_title = 'Contact Messages';
require_once __DIR__ . '/includes/header.php';

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    try {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        $success = 'Message deleted successfully!';
    } catch(PDOException $e) {
        $error = 'Error deleting message: ' . $e->getMessage();
    }
}

$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();
?>

<div class="content-card">
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>
    
    <h5 class="mb-3">Contact Messages (<?php echo count($messages); ?>)</h5>
    <?php if (count($messages) > 0): ?>
        <?php foreach ($messages as $msg): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10">
                            <h6><?php echo htmlspecialchars($msg['name']); ?></h6>
                            <p class="mb-1"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($msg['email']); ?></p>
                            <?php if ($msg['phone']): ?>
                                <p class="mb-2"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($msg['phone']); ?></p>
                            <?php endif; ?>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                            <small class="text-muted">Received: <?php echo date('F d, Y \a\t h:i A', strtotime($msg['created_at'])); ?></small>
                        </div>
                        <div class="col-md-2 d-flex align-items-start justify-content-end">
                            <form method="POST" onsubmit="return confirm('Delete this message?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">No messages received yet.</p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
