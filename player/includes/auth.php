<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

function requireAuth() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
        session_destroy();
        header("Location: ../auth/login.php");
        exit();
    }
    if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] !== 'active') {
        echo "<script>alert('Your account is deactivated.'); window.location.href='../auth/login.php';</script>";
        exit();
    }
}

function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>
