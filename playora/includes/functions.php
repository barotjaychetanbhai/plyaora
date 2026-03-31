<?php
require_once __DIR__ . '/../config/db.php';

function getTurfById($turf_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT t.*, s.name as sport_name, c.name as city_name
                            FROM turfs t
                            LEFT JOIN sports s ON t.sport_id = s.id
                            LEFT JOIN cities c ON t.city_id = c.id
                            WHERE t.id = ?");
    $stmt->bind_param("i", $turf_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getAllTurfs() {
    global $conn;
    $query = "SELECT t.*, s.name as sport_name, c.name as city_name
              FROM turfs t
              LEFT JOIN sports s ON t.sport_id = s.id
              LEFT JOIN cities c ON t.city_id = c.id
              WHERE t.status = 'active'";
    $result = $conn->query($query);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function getTurfsWithLocation() {
    global $conn;
    // For now assuming same as getAllTurfs since city is joined
    return getAllTurfs();
}

function getTurfImages($turf_id) {
    global $conn;
    // Based on original lending.html, assuming there might be an images array or table.
    // Let's assume a generic implementation since schema isn't 100% explicit on this table name.
    // If not found, we will mock or return empty for now
    $query = "SELECT image_url FROM turf_images WHERE turf_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $turf_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

function getTurfSports($turf_id) {
    global $conn;
    // Typically many-to-many, but in base query sport_id is in turfs table.
    // We'll query based on the turf's sport_id just in case, or a join table if exists.
    $stmt = $conn->prepare("SELECT s.* FROM sports s
                            JOIN turfs t ON t.sport_id = s.id
                            WHERE t.id = ?");
    $stmt->bind_param("i", $turf_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getTurfSlots($turf_id, $date) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM turf_slots WHERE turf_id = ? AND status = 'available'");
    $stmt->bind_param("i", $turf_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getReviews($turf_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM reviews WHERE turf_id = ? ORDER BY id DESC LIMIT 10");
    if ($stmt) {
        $stmt->bind_param("i", $turf_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

function getLocation($location_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM cities WHERE id = ?");
    $stmt->bind_param("i", $location_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getTurfsByFilter($location, $sport_id, $date, $sort = '') {
    global $conn;
    $query = "
        SELECT t.*, s.name as sport_name, c.name as city_name
        FROM turfs t
        JOIN sports s ON t.sport_id = s.id
        JOIN cities c ON t.city_id = c.id
        WHERE t.status = 'active'
    ";

    $params = [];
    $types = "";

    if (!empty($location)) {
        $query .= " AND c.name = ?";
        $params[] = $location;
        $types .= "s";
    }

    if (!empty($sport_id)) {
        $query .= " AND t.sport_id = ?";
        $params[] = $sport_id;
        $types .= "i";
    }

    // Since we don't have slots checking in this basic filter query for simplicity
    // we just return based on location/sport. If date was required for availability
    // we would join turf_slots.

    if ($sort === 'price_asc') {
        $query .= " ORDER BY t.price_per_hour ASC";
    } elseif ($sort === 'rating_desc') {
        $query .= " ORDER BY t.rating DESC";
    } else {
        $query .= " ORDER BY t.created_at DESC";
    }

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getSlotsByDate($turf_id, $sport_id, $date) {
    global $conn;
    // Assuming slot structure doesn't deeply rely on sport_id if it's tied to turf
    $stmt = $conn->prepare("
        SELECT * FROM turf_slots
        WHERE turf_id = ? AND status = 'available'
    ");
    // If date filtering is in slots table: AND date = ?
    // Currently schema just shows time ranges and status.
    $stmt->bind_param("i", $turf_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getAverageRating($turf_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE turf_id = ?");
    $stmt->bind_param("i", $turf_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ? number_format($result['avg_rating'], 1) : 0;
}

function getReviewsCount($turf_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM reviews WHERE turf_id = ?");
    $stmt->bind_param("i", $turf_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ? $result['total'] : 0;
}

function getTurfsBySport($sport_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT t.*, s.name as sport_name, c.name as city_name
        FROM turfs t
        JOIN sports s ON t.sport_id = s.id
        JOIN cities c ON t.city_id = c.id
        WHERE t.status = 'active' AND t.sport_id = ?
        ORDER BY t.created_at DESC
    ");
    $stmt->bind_param("i", $sport_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getTurfsByCityName($city_name) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT t.*, s.name as sport_name, c.name as city_name
        FROM turfs t
        JOIN sports s ON t.sport_id = s.id
        JOIN cities c ON t.city_id = c.id
        WHERE t.status = 'active' AND c.name = ?
        ORDER BY t.created_at DESC
    ");
    $stmt->bind_param("s", $city_name);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
