<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    echo "<div style='padding: 100px; text-align: center; color: red;'>Invalid Turf ID.</div>";
    exit;
}

$turf = getTurfById($id);
if (!$turf) {
    echo "<div style='padding: 100px; text-align: center; color: red;'>Turf not found.</div>";
    exit;
}

// Ensure sports or any other relationships are loaded
$sports = getTurfSports($id);
$reviews = getReviews($id);
$images = getTurfImages($id);
?>

<div class="turf-detail-page" style="padding-top: 100px; padding-bottom: 80px; max-width: 1200px; margin: 0 auto; width: 90%;">
    <button onclick="loadPage('home')" style="background:none;border:none;color:var(--brown-600);cursor:pointer;margin-bottom:20px;font-size:16px;">
        ← Back to Home
    </button>

    <div style="background: var(--white); border-radius: var(--radius); padding: 30px; box-shadow: var(--shadow-md);">
        <!-- Basic Info -->
        <h1 style="color: var(--brown-900); font-family: 'Playfair Display', serif; font-size: 36px; margin-bottom: 10px;">
            <?= htmlspecialchars($turf['name']) ?>
        </h1>

        <div style="display: flex; gap: 20px; color: var(--brown-600); margin-bottom: 20px;">
            <span>📍 <?= htmlspecialchars($turf['city_name'] ?? 'Location unknown') ?></span>
            <span>⭐ <?= htmlspecialchars($turf['rating'] ?? 'N/A') ?> (<?= count($reviews) ?> reviews)</span>
        </div>

        <p style="color: var(--brown-700); line-height: 1.6; margin-bottom: 30px;">
            <?= nl2br(htmlspecialchars($turf['description'] ?? 'No description available for this turf.')) ?>
        </p>

        <!-- Map & Price Section -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px;">
            <div>
                <h3 style="margin-bottom: 15px; color: var(--brown-900);">Location</h3>
                <div style="background: var(--brown-50); padding: 20px; border-radius: var(--radius-sm); border: 1px solid var(--brown-200);">
                    <p>Address: <?= htmlspecialchars($turf['address'] ?? 'Not specified') ?></p>
                    <p style="margin-top:10px;">
                        <a href="https://maps.google.com/?q=<?= urlencode($turf['address'] ?? $turf['name'] . ' ' . ($turf['city_name'] ?? '')) ?>" target="_blank" style="color: var(--brown-500); text-decoration: none; font-weight: bold;">
                            🗺️ View on Map
                        </a>
                    </p>
                </div>
            </div>

            <div>
                <h3 style="margin-bottom: 15px; color: var(--brown-900);">Pricing</h3>
                <div style="background: var(--brown-50); padding: 20px; border-radius: var(--radius-sm); border: 1px solid var(--brown-200); font-size: 24px; font-weight: bold; color: var(--brown-800);">
                    ₹<?= htmlspecialchars($turf['price_per_hour'] ?? 0) ?> <span style="font-size: 16px; font-weight: normal; color: var(--brown-500);">/ hour</span>
                </div>
            </div>
        </div>

        <!-- Available Slots -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
            <h3 style="color: var(--brown-900); margin:0;">Available Slots (Today)</h3>

            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="date" id="custom-date" value="<?= date('Y-m-d') ?>" style="padding: 10px; border: 1px solid var(--brown-300); border-radius: 20px;">
                <button onclick="const d = document.getElementById('custom-date').value; loadPage('slot_selection', 'turf_id=<?= $id ?>&sport_id=<?= $turf['sport_id'] ?>&date=' + d)" style="background: var(--brown-800); color: white; border: none; padding: 10px 20px; border-radius: 20px; font-weight: bold; cursor: pointer;">Select Slot</button>
            </div>

        </div>
        <div id="slots-container" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 40px;">
            Loading slots...
        </div>

        <!-- Reviews -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
            <h3 style="color: var(--brown-900); margin:0;">Recent Reviews</h3>
            <button onclick="loadPage('reviews_page', 'turf_id=<?= $id ?>')" style="background: none; color: var(--brown-600); border: 1px solid var(--brown-300); padding: 8px 15px; border-radius: 20px; font-weight: 500; cursor: pointer;">View All Reviews</button>
        </div>
        <div style="display: grid; gap: 20px;">
            <?php foreach($reviews as $review): ?>
                <div style="background: var(--brown-50); padding: 20px; border-radius: var(--radius-sm); border: 1px solid var(--brown-100);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <strong><?= htmlspecialchars($review['user_name'] ?? 'Anonymous') ?></strong>
                        <span>⭐ <?= htmlspecialchars($review['rating']) ?></span>
                    </div>
                    <p style="color: var(--brown-700);"><?= nl2br(htmlspecialchars($review['comment'] ?? '')) ?></p>
                </div>
            <?php endforeach; ?>
            <?php if(empty($reviews)): ?>
                <p style="color: var(--brown-400);">No reviews yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function fetchSlots(turfId) {
    const date = new Date().toISOString().split('T')[0]; // Today's date
    const container = document.getElementById('slots-container');

    fetch(`ajax/get_slots.php?id=${turfId}&date=${date}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                container.innerHTML = '<span style="color: var(--brown-400);">No slots available for today.</span>';
                return;
            }

            let html = '';
            data.forEach(slot => {
                html += `
                    <div onclick="loadPage('slot_selection', 'turf_id=<?= $id ?>&sport_id=<?= $turf['sport_id'] ?>&date=${date}')" style="cursor:pointer; padding: 10px 20px; border: 1px solid var(--brown-300); border-radius: 99px; background: white; color: var(--brown-800); font-weight: 500; transition: background 0.2s;">
                        ${slot.start_time} - ${slot.end_time}
                    </div>
                `;
            });
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching slots:', error);
            container.innerHTML = '<span style="color: red;">Failed to load slots.</span>';
        });
}

fetchSlots(<?= $id ?>);
</script>