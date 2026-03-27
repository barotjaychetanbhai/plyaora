<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'owner';
}

function requireAuth() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
        session_destroy();
        header("Location: ../auth/login.php");
        exit();
    }
    if (!isset($_SESSION['owner_status']) || $_SESSION['owner_status'] !== 'approved') {
        echo "<script>alert('Your account is pending approval or suspended.'); window.location.href='../auth/login.php';</script>";
        exit();
    }
}

function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>
