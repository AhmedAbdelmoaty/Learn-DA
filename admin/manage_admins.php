<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
requireRole([ROLE_SUPER_ADMIN]);

$page_title = 'Manage Admins';
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';
$allowedRoles = [ROLE_SUPER_ADMIN, ROLE_CONTENT_ADMIN, ROLE_EDITOR];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            try {
                $role = in_array($_POST['role'], $allowedRoles, true) ? $_POST['role'] : ROLE_EDITOR;
                $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO admin_users (name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['name'], $_POST['email'], $hashedPassword, $role]);
                $success = 'Admin added successfully!';
            } catch(PDOException $e) {
                $error = 'Error adding admin: ' . $e->getMessage();
            }
        } elseif ($_POST['action'] === 'delete') {
            if ($_POST['id'] == $_SESSION['admin_id']) {
                $error = 'You cannot delete your own account!';
            } else {
                try {
                    $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $success = 'Admin deleted successfully!';
                } catch(PDOException $e) {
                    $error = 'Error deleting admin: ' . $e->getMessage();
                }
            }
        } elseif ($_POST['action'] === 'change_password') {
            try {
                $hashedPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                $stmt->execute([$hashedPassword, $_POST['id']]);
                $success = 'Password changed successfully!';
            } catch(PDOException $e) {
                $error = 'Error changing password: ' . $e->getMessage();
            }
        }
    }
}

$stmt = $pdo->query("SELECT * FROM admin_users ORDER BY created_at DESC");
$admins = $stmt->fetchAll();
?>

<div class="content-card mb-4">
    <h5 class="mb-3">Add New Admin</h5>
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="super_admin">Super Admin</option>
                    <option value="content_admin" selected>Content Admin</option>
                    <option value="editor">Editor (Draft Only)</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Add Admin</button>
    </form>
</div>

<div class="content-card">
    <h5 class="mb-3">Existing Admins</h5>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($admin['name']); ?></td>
                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                        <td>
                            <?php
                                $roleLabels = [
                                    ROLE_SUPER_ADMIN => ['label' => 'Super Admin', 'class' => 'primary'],
                                    ROLE_CONTENT_ADMIN => ['label' => 'Content Admin', 'class' => 'success'],
                                    ROLE_EDITOR => ['label' => 'Editor (Draft)', 'class' => 'secondary'],
                                ];
                                $role = normalizeRole($admin['role'] ?? null);
                                $badge = $roleLabels[$role] ?? ['label' => 'Unknown', 'class' => 'dark'];
                            ?>
                            <span class="badge bg-<?php echo $badge['class']; ?>"><?php echo $badge['label']; ?></span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#passwordModal<?php echo $admin['id']; ?>">
                                <i class="fas fa-key"></i> Change Password
                            </button>
                            <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this admin?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <div class="modal fade" id="passwordModal<?php echo $admin['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Change Password for <?php echo htmlspecialchars($admin['name']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="change_password">
                                        <input type="hidden" name="id" value="<?php echo $admin['id']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="password" class="form-control" name="new_password" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Change Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
