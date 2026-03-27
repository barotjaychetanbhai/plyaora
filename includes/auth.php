<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /plyaora/auth/login.php");
        exit();
    }
}

function requireRole($role) {
    requireLogin();
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        // Redirect based on actual role or to login if role mismatch
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'owner') {
            header("Location: /plyaora/owner/index.php");
        } else if (isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
            header("Location: /plyaora/player/index.php");
        } else {
            header("Location: /plyaora/auth/login.php");
        }
        exit();
    }
}
?>