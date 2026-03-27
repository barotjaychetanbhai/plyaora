<?php
$owner_id = $_SESSION['owner_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $turf_id = $_POST['turf_id'] ?? 0;
    
    // Verify owner
    $check = $conn->query("SELECT id FROM turfs WHERE id = $turf_id AND owner_id = $owner_id");
    if ($check->num_rows > 0) {
        if ($action === 'delete') {
            $stmt = $conn->prepare("DELETE FROM turfs WHERE id = ?");
            $stmt->bind_param("i", $turf_id);
            $stmt->execute();
        } elseif ($action === 'toggle') {
            $stmt = $conn->prepare("UPDATE turfs SET status = IF(status='active', 'inactive', 'active') WHERE id = ?");
            $stmt->bind_param("i", $turf_id);
            $stmt->execute();
        }
    }
    echo "<script>window.location.href='index.php?page=turfs';</script>";
    exit();
}

$query = "
    SELECT t.*, c.name as city_name, s.name as sport_name 
    FROM turfs t
    LEFT JOIN cities c ON t.city_id = c.id
    LEFT JOIN sports s ON t.sport_id = s.id
    WHERE t.owner_id = $owner_id
    ORDER BY t.created_at DESC
";
$turfs = $conn->query($query);
?>
<div class="glass-card p-6 border-t-[3px] border-t-purple-500/50">
    <div class="flex justify-between items-center mb-8 border-b border-white/10 pb-4">
        <div>
            <h3 class="text-2xl font-serif text-white tracking-widest uppercase">My Turfs</h3>
            <p class="text-xs text-gray-400 mt-1">Manage your active venues</p>
        </div>
        <a href="index.php?page=add-turf" class="bg-gradient-to-r from-purple-600 to-fuchsia-500 hover:from-purple-500 hover:to-fuchsia-400 text-white px-5 py-2.5 rounded-lg text-sm font-semibold tracking-wider transition-all flex items-center shadow-lg shadow-purple-500/20 active:scale-95">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add Turf
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php if ($turfs && $turfs->num_rows > 0): ?>
            <?php while($t = $turfs->fetch_assoc()): ?>
                <div class="bg-void/40 border border-white/5 rounded-2xl p-5 hover:border-white/20 transition-all group relative overflow-hidden flex flex-col justify-between shadow-2xl">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-gradient-to-br from-<?php echo ($t['status'] === 'active' ? 'emerald' : 'gray'); ?>-500/10 to-transparent blur-2xl pointer-events-none rounded-full"></div>
                    
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="text-lg font-bold text-white mb-1 tracking-wide"><?php echo e($t['name']); ?></h4>
                                <div class="flex items-center gap-2 text-xs text-gray-400 font-medium whitespace-nowrap overflow-x-auto pb-1 hide-scrollbar">
                                    <span class="flex items-center shrink-0 bg-white/5 px-2 py-0.5 rounded border border-white/5"><i data-lucide="map-pin" class="w-3 h-3 mr-1 text-cyan-400"></i> <?php echo e($t['city_name'] ?: 'N/A'); ?></span>
                                    <span class="flex items-center shrink-0 bg-white/5 px-2 py-0.5 rounded border border-white/5"><i data-lucide="medal" class="w-3 h-3 mr-1 text-purple-400"></i> <?php echo e($t['sport_name'] ?: 'N/A'); ?></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-widest rounded border <?php echo $t['status'] === 'active' ? 'border-emerald-500/30 text-emerald-400 bg-emerald-500/10' : 'border-red-500/30 text-red-400 bg-red-500/10'; ?>">
                                <?php echo e($t['status']); ?>
                            </span>
                        </div>
                        
                        <div class="space-y-2 text-sm text-gray-300 mb-6 font-mono text-xs">
                            <p class="flex items-center justify-between border-b border-white/5 pb-2 pt-1">
                                <span class="text-gray-500 uppercase tracking-wider">Price/Hr</span>
                                <span class="text-emerald-300 font-bold">₹<?php echo number_format($t['price'], 2); ?></span>
                            </p>
                            <p class="flex items-center justify-between pt-1">
                                <span class="text-gray-500 uppercase tracking-wider">Rating</span>
                                <span class="text-amber-400 flex items-center font-bold"><i data-lucide="star" class="w-3 h-3 fill-amber-400 mr-1"></i> <?php echo number_format($t['rating'], 1); ?></span>
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2 relative z-10 transition-opacity mt-2">
                        <a href="index.php?page=turf-slots&id=<?php echo $t['id']; ?>" class="w-full block text-center bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 text-xs font-semibold py-2.5 rounded-lg border border-cyan-500/20 transition-all">
                            Manage Slots
                        </a>
                        <div class="flex items-center justify-between gap-2">
                            <a href="index.php?page=edit-turf&id=<?php echo $t['id']; ?>" class="flex-1 text-center bg-white/5 hover:bg-white/10 text-white text-xs font-semibold py-2 rounded-lg border border-white/5 inline-block transition-colors">
                                Edit
                            </a>
                            <form method="POST" action="" class="flex-1" onsubmit="return confirm('Toggle status?');">
                                <input type="hidden" name="turf_id" value="<?php echo $t['id']; ?>">
                                <input type="hidden" name="action" value="toggle">
                                <button class="w-full bg-white/5 hover:bg-orange-500/20 text-white text-xs font-semibold py-2 rounded-lg border border-white/5 transition-colors">
                                    Toggle
                                </button>
                            </form>
                            <form method="POST" action="" class="shrink-0" onsubmit="return confirm('Delete this turf?');">
                                <input type="hidden" name="turf_id" value="<?php echo $t['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button class="w-full bg-red-500/10 hover:bg-red-500/30 text-red-500 text-xs py-2 px-3 rounded-lg border border-red-500/20 transition-colors tooltip" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-1 md:col-span-2 xl:col-span-3 text-center py-20 text-gray-500 bg-void/20 rounded-2xl border border-dashed border-white/10">
                <i data-lucide="map" class="w-16 h-16 mx-auto mb-4 opacity-20"></i>
                <h4 class="text-xl font-serif tracking-widest uppercase mb-2">No Turfs Registered</h4>
                <p class="text-sm">You haven't added any facilities yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<style>
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
