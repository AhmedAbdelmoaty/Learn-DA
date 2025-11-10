<?php
$page_title = 'Media Upload';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        try {
            $stmt = $pdo->prepare("SELECT filepath FROM uploads WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $file = $stmt->fetch();
            
            if ($file && file_exists('../' . $file['filepath'])) {
                unlink('../' . $file['filepath']);
            }
            
            $stmt = $pdo->prepare("DELETE FROM uploads WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $success = 'File deleted successfully!';
        } catch(PDOException $e) {
            $error = 'Error deleting file: ' . $e->getMessage();
        }
    } elseif (isset($_FILES['file'])) {
        $target_dir = "../assets/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file = $_FILES['file'];
        $filename = basename($file['name']);
        $target_file = $target_dir . time() . '_' . $filename;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $check = getimagesize($file['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                try {
                    $filepath = str_replace('../', '', $target_file);
                    $stmt = $pdo->prepare("INSERT INTO uploads (filename, filepath) VALUES (?, ?)");
                    $stmt->execute([$filename, $filepath]);
                    $success = 'File uploaded successfully!';
                } catch(PDOException $e) {
                    $error = 'Error saving file info: ' . $e->getMessage();
                }
            } else {
                $error = 'Sorry, there was an error uploading your file.';
            }
        } else {
            $error = 'File is not an image.';
        }
    }
}

$stmt = $pdo->query("SELECT * FROM uploads ORDER BY uploaded_at DESC");
$uploads = $stmt->fetchAll();
?>

<div class="content-card mb-4">
    <h5 class="mb-3">Upload New Image</h5>
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-10">
                <input type="file" class="form-control" name="file" accept="image/*" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-upload"></i> Upload
                </button>
            </div>
        </div>
    </form>
</div>

<div class="content-card">
    <h5 class="mb-3">Uploaded Images</h5>
    <?php if (count($uploads) > 0): ?>
        <div class="row g-3">
            <?php foreach ($uploads as $upload): ?>
                <div class="col-md-3">
                    <div class="card">
                        <img src="../<?php echo htmlspecialchars($upload['filepath']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($upload['filename']); ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <p class="card-text small mb-2"><?php echo htmlspecialchars($upload['filename']); ?></p>
                            <small class="text-muted d-block mb-2"><?php echo date('M d, Y', strtotime($upload['uploaded_at'])); ?></small>
                            <input type="text" class="form-control form-control-sm mb-2" value="<?php echo htmlspecialchars($upload['filepath']); ?>" readonly onclick="this.select();">
                            <form method="POST" onsubmit="return confirm('Delete this image?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $upload['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">No images uploaded yet.</p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
