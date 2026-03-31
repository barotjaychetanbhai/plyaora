<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
header('Content-Type: application/json');

$turf_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

if (!$turf_id) {
    echo json_encode([]);
    exit;
}

$slots = getTurfSlots($turf_id, $date);
echo json_encode($slots);
?>