<?php
$query = "
    SELECT r.*, u.name as user_name, t.name as turf_name 
    FROM reviews r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN turfs t ON r.turf_id = t.id
    ORDER BY r.created_at DESC
";
$reviews = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $review_id = $_POST['review_id'] ?? 0;
    
    if ($action === 'toggle') {
        $stmt = $conn->prepare("UPDATE reviews SET status = IF(status='visible', 'hidden', 'visible') WHERE id = ?");
        $stmt->bind_param("i", $review_id);
        $stmt->execute();
    }
    echo "<script>window.location.href='index.php?page=reviews';</script>";
    exit();
}
?>
<div class="glass-card p-6">
    <div class="flex justify-between items-center mb-8 border-b border-white/5 pb-4">
        <div>
            <h3 class="text-2xl font-serif text-white tracking-widest uppercase shadow-sm">Feedback & Ratings</h3>
            <p class="text-xs text-amber-400 mt-1 uppercase tracking-wider flex items-center"><i data-lucide="star" class="w-3.5 h-3.5 mr-1 fill-amber-400"></i> Reputation Management</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if ($reviews && $reviews->num_rows > 0): ?>
            <?php while($r = $reviews->fetch_assoc()): ?>
                <div class="bg-void/40 border border-white/5 rounded-2xl p-5 hover:border-amber-500/20 transition-all group relative overflow-hidden flex flex-col justify-between shadow-2xl">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-gradient-to-br from-amber-500/5 to-transparent blur-2xl pointer-events-none rounded-full"></div>
                    
                    <div>
                        <div class="flex justify-between items-start mb-4 border-b border-white/5 pb-4">
                            <div class="flex gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-amber-600 to-orange-500 flex items-center justify-center text-sm font-bold text-white shadow-lg shrink-0">
                                    <?php echo strtoupper(substr($r['user_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h4 class="text-white font-medium text-sm"><?php echo e($r['user_name']); ?></h4>
                                    <p class="text-xs text-gray-500 mt-0.5 tracking-wider"><?php echo date('M j, Y', strtotime($r['created_at'])); ?></p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 text-[9px] font-black uppercase tracking-widest rounded border <?php echo $r['status'] === 'visible' ? 'border-emerald-500/30 text-emerald-400 bg-emerald-500/10' : 'border-red-500/30 text-red-400 bg-red-500/10'; ?>">
                                <?php echo e($r['status']); ?>
                            </span>
                        </div>
                        
                        <div class="flex items-center gap-1 mb-3">
                            <?php for($i=1; $i<=5; $i++): ?>
                                <i data-lucide="star" class="w-3.5 h-3.5 <?php echo $i <= $r['rating'] ? 'text-amber-400 fill-amber-400' : 'text-white/10 fill-white/5'; ?>"></i>
                            <?php endfor; ?>
                            <span class="text-xs text-cyan-400 font-medium ml-2 border-l border-white/10 pl-2 underline decoration-cyan-400/30 underline-offset-2"><?php echo e($r['turf_name']); ?></span>
                        </div>
                        
                        <p class="text-gray-300 text-sm italic leading-relaxed mb-6">"<?php echo nl2br(e($r['review'])); ?>"</p>
                    </div>

                    <div class="pt-4 border-t border-white/5 mt-auto">
                        <form method="POST" action="" onsubmit="return confirm('Toggle visibility?');">
                            <input type="hidden" name="review_id" value="<?php echo $r['id']; ?>">
                            <input type="hidden" name="action" value="toggle">
                            <button class="w-full bg-white/5 hover:bg-white/10 text-white text-xs font-semibold py-2.5 rounded-lg transition-colors border border-white/5 flex items-center justify-center">
                                <?php if($r['status'] === 'visible'): ?>
                                    <i data-lucide="eye-off" class="w-4 h-4 mr-2 text-gray-400"></i> Hide Review
                                <?php else: ?>
                                    <i data-lucide="eye" class="w-4 h-4 mr-2 text-emerald-400"></i> Make Visible
                                <?php endif; ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-20 text-gray-500 bg-void/30 rounded-2xl border border-dashed border-white/10">
                <i data-lucide="message-square-dashed" class="w-16 h-16 mx-auto stroke-1 opacity-20 mb-4"></i>
                <h4 class="text-xl font-serif tracking-widest uppercase mb-2">No Reviews Yet</h4>
                <p class="text-sm">Feedback will appear here once users rate their bookings.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
