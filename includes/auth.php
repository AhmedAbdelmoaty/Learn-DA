<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const ROLE_SUPER_ADMIN = 'super_admin';
const ROLE_CONTENT_ADMIN = 'content_admin';
const ROLE_EDITOR = 'editor';

function canPublishContent(): bool {
    return hasRole([ROLE_SUPER_ADMIN, ROLE_CONTENT_ADMIN]);
}

function resolvePublishFlag($input): int {
    return canPublishContent() && !empty($input) ? 1 : 0;
}

function normalizeRole($role) {
    $role = $role ?: '';
    $map = [
        'admin' => ROLE_SUPER_ADMIN,
        'editor' => ROLE_EDITOR,
        ROLE_SUPER_ADMIN => ROLE_SUPER_ADMIN,
        ROLE_CONTENT_ADMIN => ROLE_CONTENT_ADMIN,
        ROLE_EDITOR => ROLE_EDITOR,
    ];

    return $map[$role] ?? ROLE_SUPER_ADMIN;
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_email']);
}

function currentUserRole() {
    return isset($_SESSION['admin_role']) ? normalizeRole($_SESSION['admin_role']) : null;
}

function hasRole($roles) {
    if (!is_array($roles)) {
        $roles = [$roles];
    }

    return isLoggedIn() && currentUserRole() && in_array(currentUserRole(), $roles, true);
}

function requireRole($roles) {
    if (!hasRole($roles)) {
        header('Location: dashboard.php');
        exit;
    }
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function login($admin_id, $email, $name, $role = ROLE_SUPER_ADMIN) {
    $_SESSION['admin_id'] = $admin_id;
    $_SESSION['admin_email'] = $email;
    $_SESSION['admin_name'] = $name;
    $_SESSION['admin_role'] = normalizeRole($role);
    $_SESSION['last_activity'] = time();
}

function logout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

function checkSessionTimeout($timeout = 3600) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        logout();
    }
    $_SESSION['last_activity'] = time();
}
?>
