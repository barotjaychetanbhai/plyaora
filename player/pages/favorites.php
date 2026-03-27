<?php
$user_id = $_SESSION['user_id'];

// Get favorite turfs
$query = "
    SELECT t.*, c.name as city_name, s.name as sport_name 
    FROM favorite_turfs ft
    JOIN turfs t ON ft.turf_id = t.id
    LEFT JOIN cities c ON t.city_id = c.id 
    LEFT JOIN sports s ON t.sport_id = s.id 
    WHERE ft.user_id = $user_id AND t.status = 'active'
    ORDER BY ft.id DESC
";

$turfs = $conn->query($query);

// Get user favorite IDs for the heart icon logic (consistent with turfs.php)
$fav_res = $conn->query("SELECT turf_id FROM favorite_turfs WHERE user_id = $user_id");
$user_favs = [];
while($f = $fav_res->fetch_assoc()) $user_favs[] = $f['turf_id'];
?>

<div class="mb-10 animate-in slide-in-from-bottom-4 duration-700">
    <div class="flex items-center gap-3 mb-2">
        <span class="px-3 py-1 rounded-lg bg-red-500/10 text-red-500 text-[10px] font-black uppercase tracking-widest border border-red-500/20">
            Player List
        </span>
        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
    </div>
    <h2 class="text-4xl font-black text-white tracking-tighter uppercase mb-2">My <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-rose-400">Favorites</span></h2>
    <p class="text-sm text-gray-500 font-medium">Turfs you've bookmarked for your next match.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
    <?php if ($turfs && $turfs->num_rows > 0): ?>
        <?php while($t = $turfs->fetch_assoc()): ?>
            <div onclick="window.location.href='index.php?page=turf-details&id=<?php echo $t['id']; ?>'" class="bg-void/60 border border-white/5 rounded-3xl overflow-hidden hover:border-red-500/30 transition-all duration-500 group flex flex-col shadow-2xl relative animate-in zoom-in-95 duration-500 cursor-pointer">
                <div class="h-52 relative overflow-hidden bg-gray-900 shrink-0">
                    <img src="https://images.unsplash.com/photo-1543351611-58f69d7c1781?q=80&w=600&auto=format&fit=crop" class="w-full h-full object-cover group-hover:scale-[1.05] transition-transform duration-1000 ease-in-out" alt="<?php echo e($t['name']); ?>">
                    <div class="absolute inset-0 bg-gradient-to-t from-void via-void/20 to-transparent"></div>
                    
                    <div class="absolute top-4 right-4 flex gap-2" onclick="event.stopPropagation()">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="toggle_favorite">
                            <input type="hidden" name="turf_id" value="<?php echo $t['id']; ?>">
                            <button type="submit" class="w-11 h-11 rounded-full bg-void/80 backdrop-blur-md border border-white/10 flex items-center justify-center transition-all hover:bg-white/10 active:scale-90 group/heart">
                                <i data-lucide="heart" class="w-5 h-5 fill-red-500 text-red-500 transition-colors"></i>
                            </button>
                        </form>
                    </div>

                    <div class="absolute bottom-5 left-5">
                        <span class="px-2 py-1 rounded bg-red-500 text-white text-[9px] font-black uppercase tracking-widest mb-2 block w-fit">
                            <?php echo e($t['sport_name']); ?>
                        </span>
                        <h3 class="text-2xl font-bold tracking-tight text-white leading-tight drop-shadow-lg"><?php echo e($t['name']); ?></h3>
                    </div>
                </div>
                
                <div class="p-6 flex flex-col flex-1 relative z-10">
                    <div class="flex items-center text-[10px] uppercase tracking-[0.2em] text-gray-500 font-bold mb-6">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1.5 text-cyan-400"></i> <?php echo e($t['city_name']); ?>
                    </div>
                    
                    <div class="mt-auto space-y-5">
                        <div class="flex items-center justify-between border-t border-white/5 pt-5">
                            <div>
                                <p class="text-[9px] text-gray-600 uppercase tracking-widest mb-1 italic">Hourly Rate</p>
                                <p class="text-xl font-mono text-emerald-400 font-black">₹<?php echo number_format($t['price']); ?></p>
                            </div>
                            <div class="flex items-center gap-1.5 bg-amber-500/10 px-2 py-1.5 rounded-lg border border-amber-500/20">
                                <i data-lucide="star" class="w-3 h-3 fill-amber-500 text-amber-500"></i>
                                <span class="text-[10px] font-mono font-bold text-amber-400"><?php echo number_format($t['rating'], 1); ?></span>
                            </div>
                        </div>
                        <a href="index.php?page=turf-details&id=<?php echo $t['id']; ?>" class="block w-full text-center bg-white/5 hover:bg-emerald-500 text-gray-400 hover:text-void border border-white/10 hover:border-emerald-500 py-4 rounded-2xl transition-all text-[10px] font-black uppercase tracking-widest active:scale-[0.98] shadow-lg shadow-void">
                            Book Match Now
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-span-full py-32 text-center glass-card border-dashed border-white/10 relative overflow-hidden group">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,rgba(244,63,94,0.05),transparent)]"></div>
            <div class="relative z-10 max-w-sm mx-auto">
                <div class="w-24 h-24 bg-white/5 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6 border border-white/5 shadow-inner transition-transform group-hover:scale-110 duration-700">
                    <i data-lucide="heart-off" class="w-10 h-10 text-gray-800"></i>
                </div>
                <h3 class="text-2xl font-bold tracking-tight text-white mb-3">No Favorites Yet</h3>
                <p class="text-gray-500 text-sm font-medium mb-10 leading-relaxed italic">"You haven't added any turfs to your favorites list. Time to scout some grounds!"</p>
                <a href="index.php?page=turfs" class="inline-flex items-center bg-red-500 hover:bg-red-400 text-white py-4 px-10 rounded-2xl transition-all text-[10px] font-black uppercase tracking-widest shadow-xl shadow-red-500/20 active:scale-95">
                    Explore Marketplace <i data-lucide="chevron-right" class="w-4 h-4 ml-2"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
