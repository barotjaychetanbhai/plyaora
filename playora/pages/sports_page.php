<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

$sport_id = isset($_GET['sport_id']) ? intval($_GET['sport_id']) : 0;

if (!$sport_id) {
    echo "<div style='padding: 100px; text-align: center; color: red;'>Invalid Sport ID.</div>";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM sports WHERE id = ?");
$stmt->bind_param("i", $sport_id);
$stmt->execute();
$sport = $stmt->get_result()->fetch_assoc();

if (!$sport) {
    echo "<div style='padding: 100px; text-align: center; color: red;'>Sport not found.</div>";
    exit;
}

$turfs = getTurfsBySport($sport_id);
?>

<div style="padding-top: 100px; padding-bottom: 80px; max-width: 1200px; margin: 0 auto; width: 90%;">
    <button onclick="loadPage('home')" style="background:none;border:none;color:var(--brown-600);cursor:pointer;margin-bottom:20px;font-size:16px;">
        ← Back to Home
    </button>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; border-bottom: 2px solid var(--brown-200); padding-bottom: 15px;">
        <h1 style="font-family: 'Playfair Display', serif; color: var(--brown-900); font-size: 36px; margin: 0;">
            Play <?= htmlspecialchars($sport['name']) ?>
        </h1>
        <div style="font-size: 24px; color: var(--brown-500);">
            <?= count($turfs) ?> turfs available
        </div>
    </div>

    <div class="turfs-grid">
        <?php foreach($turfs as $turf): ?>
            <div class="turf-card" onclick="loadPage('turf_detail', 'id=<?= $turf['id'] ?>')">
                <div class="turf-img">
                    <div class="turf-img-bg" style="background: linear-gradient(135deg, #a5784c 0%, #8b5e3c 100%);"></div>
                    <div class="turf-emoji">⚽</div>
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
                No turfs found for <?= htmlspecialchars($sport['name']) ?> currently. Please check back later!
            </div>
        <?php endif; ?>
    </div>
</div>