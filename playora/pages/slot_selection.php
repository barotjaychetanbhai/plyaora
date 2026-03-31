<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

$turf_id = isset($_GET['turf_id']) ? intval($_GET['turf_id']) : 0;
$sport_id = isset($_GET['sport_id']) ? intval($_GET['sport_id']) : 0; // Not strictly used for slots table but passed along
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

if (!$turf_id) {
    echo "<div style='padding: 100px; text-align: center; color: red;'>Invalid Turf ID.</div>";
    exit;
}

$turf = getTurfById($turf_id);
if (!$turf) {
    echo "<div style='padding: 100px; text-align: center; color: red;'>Turf not found.</div>";
    exit;
}

$slots = getSlotsByDate($turf_id, $sport_id, $date);
?>

<div style="padding-top: 100px; padding-bottom: 80px; max-width: 800px; margin: 0 auto; width: 90%;">
    <button onclick="loadPage('turf_detail', 'id=<?= $turf_id ?>')" style="background:none;border:none;color:var(--brown-600);cursor:pointer;margin-bottom:20px;font-size:16px;">
        ← Back to <?= htmlspecialchars($turf['name']) ?>
    </button>

    <div style="background: var(--white); border-radius: var(--radius); padding: 30px; box-shadow: var(--shadow-md);">
        <h2 style="font-family: 'Playfair Display', serif; color: var(--brown-900); margin-bottom: 10px;">Select a Slot</h2>
        <p style="color: var(--brown-600); margin-bottom: 20px;">
            Booking for <strong><?= htmlspecialchars($turf['name']) ?></strong> on
            <input type="date" value="<?= htmlspecialchars($date) ?>" onchange="changeDate(this.value)" style="padding: 5px; border: 1px solid var(--brown-200); border-radius: 4px;">
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px;">
            <?php foreach($slots as $slot): ?>
                <button
                    class="slot-btn"
                    data-id="<?= $slot['id'] ?>"
                    onclick="selectSlot(this, <?= $slot['id'] ?>)"
                    style="padding: 15px; border: 2px solid var(--brown-200); border-radius: var(--radius-sm); background: white; cursor: pointer; font-weight: 500; color: var(--brown-800); transition: all 0.2s;">
                    <?= htmlspecialchars($slot['start_time']) ?> - <?= htmlspecialchars($slot['end_time']) ?>
                </button>
            <?php endforeach; ?>

            <?php if(empty($slots)): ?>
                <div style="grid-column: 1/-1; color: var(--brown-500); padding: 20px; background: var(--brown-50); border-radius: var(--radius-sm);">
                    No available slots for this date.
                </div>
            <?php endif; ?>
        </div>

        <div id="booking-action" style="display: none; text-align: right; padding-top: 20px; border-top: 1px solid var(--brown-100);">
            <div style="margin-bottom: 10px; color: var(--brown-800);">
                Total Price: <strong style="font-size: 20px;">₹<?= htmlspecialchars($turf['price_per_hour']) ?></strong>
            </div>
            <button onclick="proceedToPreview()" style="background: var(--brown-800); color: white; border: none; padding: 12px 30px; border-radius: 30px; font-weight: bold; cursor: pointer;">
                Continue to Preview →
            </button>
        </div>
    </div>
</div>

<script>
let currentSlotId = null;

function selectSlot(btn, slotId) {
    // Reset all buttons
    document.querySelectorAll('.slot-btn').forEach(b => {
        b.style.borderColor = 'var(--brown-200)';
        b.style.background = 'white';
        b.style.color = 'var(--brown-800)';
    });

    // Highlight selected
    btn.style.borderColor = 'var(--brown-800)';
    btn.style.background = 'var(--brown-50)';
    currentSlotId = slotId;

    // Show action area
    document.getElementById('booking-action').style.display = 'block';
}

function changeDate(newDate) {
    loadPage('slot_selection', `turf_id=<?= $turf_id ?>&sport_id=<?= $sport_id ?>&date=${newDate}`);
}

function proceedToPreview() {
    if (!currentSlotId) return;
    loadPage('booking_preview', `turf_id=<?= $turf_id ?>&sport_id=<?= $sport_id ?>&date=<?= $date ?>&slot_id=${currentSlotId}`);
}
</script>