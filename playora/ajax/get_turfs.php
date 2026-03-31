<?php
require_once '../config/db.php';
header('Content-Type: application/json');

$city = isset($_GET['city']) ? $_GET['city'] : '';
$sport = isset($_GET['sport']) ? $_GET['sport'] : '';
$price = isset($_GET['price']) ? $_GET['price'] : '';

$query = "SELECT t.*, s.name as sport_name, c.name as city_name
          FROM turfs t
          LEFT JOIN sports s ON t.sport_id = s.id
          LEFT JOIN cities c ON t.city_id = c.id
          WHERE t.status = 'active'";

$params = [];
$types = "";

if (!empty($city)) {
    $query .= " AND c.name = ?";
    $params[] = $city;
    $types .= "s";
}

if (!empty($sport)) {
    $query .= " AND t.sport_id = ?";
    $params[] = $sport;
    $types .= "i";
}

if (!empty($price)) {
    if ($price === '0-500') {
        $query .= " AND t.price_per_hour <= 500";
    } elseif ($price === '500-1000') {
        $query .= " AND t.price_per_hour > 500 AND t.price_per_hour <= 1000";
    } elseif ($price === '1000-plus') {
        $query .= " AND t.price_per_hour > 1000";
    }
}

$query .= " ORDER BY t.created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$turfs = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($turfs);
?>