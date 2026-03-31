<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

$city = isset($_GET['city']) ? $_GET['city'] : '';

if (empty($city)) {
    echo "<div style='padding: 100px; text-align: center; color: red;'>Invalid Location.</div>";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM cities WHERE name = ?");
$stmt->bind_param("s", $city);
$stmt->execute();
$city_data = $stmt->get_result()->fetch_assoc();

if (!$city_data) {
    echo "<div style='padding: 100px; text-align: center; color: red;'>Location not found in our database.</div>";
    exit;
}

$turfs = getTurfsByCityName($city);
?>

<div style="padding-top: 100px; padding-bottom: 80px; max-width: 1200px; margin: 0 auto; width: 90%;">
    <button onclick="loadPage('home')" style="background:none;border:none;color:var(--brown-600);cursor:pointer;margin-bottom:20px;font-size:16px;">
        ← Back to Home
    </button>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; border-bottom: 2px solid var(--brown-200); padding-bottom: 15px;">
        <h1 style="font-family: 'Playfair Display', serif; color: var(--brown-900); font-size: 36px; margin: 0;">
            Turfs in <?= htmlspecialchars($city_data['name']) ?>
        </h1>
        <div style="font-size: 24px; color: var(--brown-500);">
            <?= count($turfs) ?> locations available
        </div>
    </div>

    <div class="turfs-grid">
        <?php foreach($turfs as $turf): ?>
            <div class="turf-card" onclick="loadPage('turf_detail', 'id=<?= $turf['id'] ?>')">
                <div class="turf-img">
                    <div class="turf-img-bg" style="background: linear-gradient(135deg, #4c6b5b 0%, #2e4a3c 100%);"></div>
                    <div class="turf-emoji">🏟️</div>
                </div>
                <div class="turf-info">
                    <div class="turf-header-flex">
                        <div class="turf-title"><?= htmlspecialchars($turf['name']) ?></div>
                        <div class="turf-rating">⭐ <?= htmlspecialchars($turf['rating'] ?? 'N/A') ?></div>
                    </div>
                    <div class="turf-meta">📍 <?= htmlspecialchars($turf['city_name']) ?> &nbsp;•&nbsp; 🏏 <?= htmlspecialchars($turf['sport_name']) ?></div>
                    <div class="turf-price-flex">
                        <div class="turf-price">₹<?= htmlspecialchars($turf['price_per_hour']) ?><span>/hr</span></div>
                        <button class="book-btn-sm" onclick="event.stopPropagation(); loadPage('turf_detail', 'id=<?= $turf['id'] ?>')">Book Now</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if(empty($turfs)): ?>
            <div style="grid-column: 1/-1; text-align: center; color: var(--brown-500); padding: 40px; background: var(--white); border-radius: var(--radius);">
                No turfs currently listed in <?= htmlspecialchars($city_data['name']) ?>.
            </div>
        <?php endif; ?>
    </div>
</div>