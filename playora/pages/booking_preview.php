<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

$turf_id = isset($_GET['turf_id']) ? intval($_GET['turf_id']) : 0;
$slot_id = isset($_GET['slot_id']) ? intval($_GET['slot_id']) : 0;
$date = isset($_GET['date']) ? $_GET['date'] : '';

if (!$turf_id || !$slot_id) {
    echo "<div style='padding: 100px; text-align: center; color: red;'>Invalid parameters.</div>";
    exit;
}

$turf = getTurfById($turf_id);
if (!$turf) {
    echo "<div style='padding: 100px; text-align: center; color: red;'>Turf not found.</div>";
    exit;
}

// Fetch the specific slot
$stmt = $conn->prepare("SELECT * FROM turf_slots WHERE id = ?");
$stmt->bind_param("i", $slot_id);
$stmt->execute();
$slot = $stmt->get_result()->fetch_assoc();

if (!$slot) {
    echo "<div style='padding: 100px; text-align: center; color: red;'>Slot not found.</div>";
    exit;
}
?>

<div style="padding-top: 100px; padding-bottom: 80px; max-width: 600px; margin: 0 auto; width: 90%;">
    <button onclick="loadPage('slot_selection', 'turf_id=<?= $turf_id ?>&date=<?= $date ?>')" style="background:none;border:none;color:var(--brown-600);cursor:pointer;margin-bottom:20px;font-size:16px;">
        ← Back to Slots
    </button>

    <div style="background: var(--white); border-radius: var(--radius); padding: 40px; box-shadow: var(--shadow-md);">
        <h2 style="font-family: 'Playfair Display', serif; color: var(--brown-900); margin-bottom: 30px; text-align: center;">Booking Preview</h2>

        <div style="background: var(--brown-50); border: 1px solid var(--brown-200); border-radius: var(--radius-sm); padding: 20px; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--brown-200); padding-bottom: 10px; margin-bottom: 10px;">
                <span style="color: var(--brown-600);">Turf Name</span>
                <strong style="color: var(--brown-900);"><?= htmlspecialchars($turf['name']) ?></strong>
            </div>

            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--brown-200); padding-bottom: 10px; margin-bottom: 10px;">
                <span style="color: var(--brown-600);">Sport</span>
                <strong style="color: var(--brown-900);"><?= htmlspecialchars($turf['sport_name']) ?></strong>
            </div>

            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--brown-200); padding-bottom: 10px; margin-bottom: 10px;">
                <span style="color: var(--brown-600);">Location</span>
                <strong style="color: var(--brown-900);"><?= htmlspecialchars($turf['city_name']) ?></strong>
            </div>

            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--brown-200); padding-bottom: 10px; margin-bottom: 10px;">
                <span style="color: var(--brown-600);">Date</span>
                <strong style="color: var(--brown-900);"><?= htmlspecialchars($date) ?></strong>
            </div>

            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--brown-200); padding-bottom: 10px; margin-bottom: 10px;">
                <span style="color: var(--brown-600);">Time Slot</span>
                <strong style="color: var(--brown-900);"><?= htmlspecialchars($slot['start_time']) ?> - <?= htmlspecialchars($slot['end_time']) ?></strong>
            </div>

            <div style="display: flex; justify-content: space-between; padding-top: 10px;">
                <span style="color: var(--brown-800); font-weight: bold; font-size: 18px;">Total Price</span>
                <strong style="color: var(--brown-900); font-size: 18px;">₹<?= htmlspecialchars($turf['price_per_hour']) ?></strong>
            </div>
        </div>

        <button onclick="alert('Proceeding to Login (Simulated)')" style="width: 100%; background: var(--brown-800); color: white; border: none; padding: 15px; border-radius: 30px; font-weight: bold; font-size: 16px; cursor: pointer; transition: background 0.3s;">
            Proceed to Login
        </button>
    </div>
</div>