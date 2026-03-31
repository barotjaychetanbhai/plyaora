<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
header('Content-Type: application/json');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

$turf = getTurfById($id);
if ($turf) {
    $turf['images'] = getTurfImages($id);
    $turf['sports'] = getTurfSports($id);
    $turf['reviews'] = getReviews($id);
    echo json_encode($turf);
} else {
    echo json_encode(['error' => 'Turf not found']);
}
?>