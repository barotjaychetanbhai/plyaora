<?php
$user_id = $_SESSION['user_id'];
$turf_id = $_GET['turf_id'] ?? 0;

$stmt = $conn->prepare("SELECT name FROM turfs WHERE id = ?");
$stmt->bind_param("i", $turf_id);
$stmt->execute();
$turf = $stmt->get_result()->fetch_assoc();

if (!$turf) {
    echo "<script>window.location.href='index.php?page=bookings';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating'] ?? 5);
    $review_text = trim($_POST['review'] ?? '');
    
    // basic check to ensure user actually completed a booking
    $check_stmt = $conn->prepare("SELECT id FROM bookings WHERE user_id = ? AND turf_id = ? AND status = 'completed'");
    $check_stmt->bind_param("ii", $user_id, $turf_id);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        $ins = $conn->prepare("INSERT INTO reviews (user_id, turf_id, rating, review, status) VALUES (?, ?, ?, ?, 'visible')");
        $ins->bind_param("iiis", $user_id, $turf_id, $rating, $review_text);
        $ins->execute();
        
        // update turf average rating
        $conn->query("UPDATE turfs SET rating = (SELECT AVG(rating) FROM reviews WHERE turf_id = $turf_id AND status='visible') WHERE id = $turf_id");
        
        echo "<script>alert('Thank you for your feedback!'); window.location.href='index.php?page=turf-details&id=$turf_id';</script>";
        exit();
    } else {
        echo "<script>alert('You can only review games you have completed.');</script>";
    }
}
?>
<div class="mb-4">
    <a href="index.php?page=bookings" class="text-[10px] text-emerald-400 hover:text-emerald-300 font-bold tracking-widest uppercase transition-colors inline-flex items-center"><i data-lucide="arrow-left" class="w-3 h-3 mr-1"></i> Back to bookings</a>
</div>

<div class="glass-card max-w-lg mx-auto p-6 md:p-8 mt-4 border border-white/5 rounded-3xl relative overflow-hidden shadow-2xl">
    <div class="absolute -right-20 -top-20 w-60 h-60 bg-emerald-500/10 rounded-full blur-[50px] pointer-events-none"></div>

    <div class="text-center mb-8 relative z-10">
        <div class="w-16 h-16 bg-white/5 rounded-full border border-white/10 flex items-center justify-center mx-auto mb-4 text-emerald-400">
            <i data-lucide="star" class="w-8 h-8 font-bold fill-emerald-500/20"></i>
        </div>
        <h2 class="text-2xl font-serif font-bold text-white mb-1 uppercase tracking-wider">Leave a Review</h2>
        <p class="text-xs text-gray-500 tracking-wide font-medium">How was your match at <span class="text-emerald-400 font-bold"><?php echo e($turf['name']); ?></span>?</p>
    </div>

    <form method="POST" action="" class="space-y-6 relative z-10 w-full">
        <div>
            <label class="block text-center text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-4">Select Rating</label>
            <div class="flex justify-center gap-3 rating-stars cursor-pointer flex-row-reverse">
                <!-- flex-row-reverse used for pure CSS hover state logic -->
                <input type="radio" name="rating" value="5" id="star5" class="peer hidden" checked>
                <label for="star5" class="text-gray-600 hover:text-amber-400 peer-checked:text-amber-400 transition-colors cursor-pointer text-4xl"><i data-lucide="star" class="fill-current w-12 h-12"></i></label>
                
                <input type="radio" name="rating" value="4" id="star4" class="peer hidden">
                <label for="star4" class="text-gray-600 hover:text-amber-400 peer-checked:text-amber-400 transition-colors cursor-pointer text-4xl"><i data-lucide="star" class="fill-current w-12 h-12"></i></label>
                
                <input type="radio" name="rating" value="3" id="star3" class="peer hidden">
                <label for="star3" class="text-gray-600 hover:text-amber-400 peer-checked:text-amber-400 transition-colors cursor-pointer text-4xl"><i data-lucide="star" class="fill-current w-12 h-12"></i></label>
                
                <input type="radio" name="rating" value="2" id="star2" class="peer hidden">
                <label for="star2" class="text-gray-600 hover:text-amber-400 peer-checked:text-amber-400 transition-colors cursor-pointer text-4xl"><i data-lucide="star" class="fill-current w-12 h-12"></i></label>
                
                <input type="radio" name="rating" value="1" id="star1" class="peer hidden">
                <label for="star1" class="text-gray-600 hover:text-amber-400 peer-checked:text-amber-400 transition-colors cursor-pointer text-4xl"><i data-lucide="star" class="fill-current w-12 h-12"></i></label>
            </div>
            
            <style>
                /* pure CSS star hover styling */
                .rating-stars label:hover,
                .rating-stars label:hover ~ label,
                .rating-stars input:checked ~ label {
                    color: #fbbf24;
                }
            </style>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Write Review</label>
            <textarea name="review" rows="4" placeholder="Share your experience (condition of the turf, amenities, staff, etc.)" required class="w-full bg-void/80 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 transition-all font-sans text-sm shadow-inner placeholder:text-gray-600 resize-none"></textarea>
        </div>

        <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-500 text-white px-6 py-4 rounded-xl text-xs font-bold uppercase tracking-widest hover:shadow-[0_0_20px_rgba(16,185,129,0.3)] transition-all active:scale-95 flex items-center justify-center">
            <i data-lucide="send" class="w-4 h-4 mr-2"></i> Submit Feedback
        </button>
    </form>
</div>
