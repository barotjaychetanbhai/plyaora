<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

$location = isset($_GET['location']) ? $_GET['location'] : '';
$sport_name = isset($_GET['sport_name']) ? $_GET['sport_name'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

// Map sport name to ID for query
$sport_id = '';
if (!empty($sport_name) && $sport_name !== 'Any Sport') {
    $stmt = $conn->prepare("SELECT id FROM sports WHERE name = ?");
    $stmt->bind_param("s", $sport_name);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res) {
        $sport_id = $res['id'];
    }
}

$turfs = getTurfsByFilter($location, $sport_id, $date, $sort);
?>

<div style="padding-top: 100px; padding-bottom: 80px; max-width: 1200px; margin: 0 auto; width: 90%;">
    <button onclick="loadPage('home')" style="background:none;border:none;color:var(--brown-600);cursor:pointer;margin-bottom:20px;font-size:16px;">
        ← Back to Search
    </button>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="font-family: 'Playfair Display', serif; color: var(--brown-900);">Search Results</h2>
        <select id="sort-results" onchange="applySort()" style="padding: 10px; border: 1px solid var(--brown-200); border-radius: var(--radius-sm); outline: none;">
            <option value="">Sort By: Relevance</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="rating_desc" <?= $sort === 'rating_desc' ? 'selected' : '' ?>>Rating: High to Low</option>
        </select>
    </div>

    <div class="turfs-grid">
        <?php foreach($turfs as $turf): ?>
            <div class="turf-card" onclick="loadPage('turf_detail', 'id=<?= $turf['id'] ?>')">
                <div class="turf-img">
                    <div class="turf-img-bg football"></div>
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
                        <button class="book-btn-sm" onclick="event.stopPropagation(); loadPage('turf_detail', 'id=<?= $turf['id'] ?>')">View Slots</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if(empty($turfs)): ?>
            <div style="grid-column: 1/-1; text-align: center; color: var(--brown-500); padding: 40px; background: var(--white); border-radius: var(--radius);">
                No turfs found for your search criteria. Try a different location or sport.
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function applySort() {
    const sort = document.getElementById('sort-results').value;
    const urlParams = new URLSearchParams(window.location.search);

    // Maintain existing filters
    const loc = urlParams.get('location') || '';
    const sport = urlParams.get('sport_name') || '';
    const date = urlParams.get('date') || '';

    let params = `location=${encodeURIComponent(loc)}&sport_name=${encodeURIComponent(sport)}&date=${encodeURIComponent(date)}&sort=${encodeURIComponent(sort)}`;
    loadPage('search_result', params);
}
</script>