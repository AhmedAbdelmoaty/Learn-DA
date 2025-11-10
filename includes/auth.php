<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_email']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function login($admin_id, $email, $name) {
    $_SESSION['admin_id'] = $admin_id;
    $_SESSION['admin_email'] = $email;
    $_SESSION['admin_name'] = $name;
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
