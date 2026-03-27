<?php
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function formatPrice($amount) {
    return '₹' . number_format($amount);
}
?>