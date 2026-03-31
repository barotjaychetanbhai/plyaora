<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

$turf_id = isset($_GET['turf_id']) ? intval($_GET['turf_id']) : 0;

if (!$turf_id) {
    echo "<div style='padding: 100px; text-align: center; color: red;'>Invalid Turf ID.</div>";
    exit;
}

$turf = getTurfById($turf_id);
if (!$turf) {
    echo "<div style='padding: 100px; text-align: center; color: red;'>Turf not found.</div>";
    exit;
}

$reviews = getReviews($turf_id);
$averageRating = getAverageRating($turf_id);
$totalReviews = getReviewsCount($turf_id);
?>

<div style="padding-top: 100px; padding-bottom: 80px; max-width: 800px; margin: 0 auto; width: 90%;">
    <button onclick="loadPage('turf_detail', 'id=<?= $turf_id ?>')" style="background:none;border:none;color:var(--brown-600);cursor:pointer;margin-bottom:20px;font-size:16px;">
        ← Back to <?= htmlspecialchars($turf['name']) ?>
    </button>

    <div style="background: var(--white); border-radius: var(--radius); padding: 40px; box-shadow: var(--shadow-md);">
        <h2 style="font-family: 'Playfair Display', serif; color: var(--brown-900); margin-bottom: 20px;">Reviews for <?= htmlspecialchars($turf['name']) ?></h2>

        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 40px; padding: 20px; background: var(--brown-50); border-radius: var(--radius-sm); border: 1px solid var(--brown-200);">
            <div style="font-size: 48px; font-weight: bold; color: var(--brown-900);"><?= htmlspecialchars($averageRating) ?></div>
            <div>
                <div style="color: var(--gold); font-size: 24px; margin-bottom: 5px;">
                    <?php
                        $stars = round((float)$averageRating);
                        echo str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
                    ?>
                </div>
                <div style="color: var(--brown-600);"><?= htmlspecialchars($totalReviews) ?> total reviews</div>
            </div>
        </div>

        <div style="display: grid; gap: 20px;">
            <?php foreach($reviews as $review): ?>
                <div style="background: var(--white); padding: 25px; border-radius: var(--radius-sm); border: 1px solid var(--brown-100); box-shadow: var(--shadow-sm);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--brown-200); display: flex; align-items: center; justify-content: center; font-weight: bold; color: var(--brown-800);">
                                <?= strtoupper(substr(htmlspecialchars($review['user_name'] ?? 'U'), 0, 1)) ?>
                            </div>
                            <div>
                                <strong style="color: var(--brown-900); display: block;"><?= htmlspecialchars($review['user_name'] ?? 'Anonymous User') ?></strong>
                                <span style="font-size: 12px; color: var(--brown-500);"><?= htmlspecialchars($review['created_at']) ?></span>
                            </div>
                        </div>
                        <div style="color: var(--gold);">
                            <?php
                                $revStars = round((float)$review['rating']);
                                echo str_repeat('★', $revStars) . str_repeat('☆', 5 - $revStars);
                            ?>
                        </div>
                    </div>
                    <p style="color: var(--brown-700); line-height: 1.6; margin: 0; padding-left: 55px;">
                        <?= nl2br(htmlspecialchars($review['comment'] ?? '')) ?>
                    </p>
                </div>
            <?php endforeach; ?>

            <?php if(empty($reviews)): ?>
                <div style="text-align: center; padding: 40px; background: var(--brown-50); border-radius: var(--radius-sm); color: var(--brown-600);">
                    No reviews yet for this turf. Be the first to play and review!
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>