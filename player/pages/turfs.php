<?php
// Filters
$sport_id = $_GET['sport'] ?? '';
$city_id = $_GET['city'] ?? '';
$query = "SELECT t.*, c.name as city_name, s.name as sport_name 
          FROM turfs t 
          LEFT JOIN cities c ON t.city_id = c.id 
          LEFT JOIN sports s ON t.sport_id = s.id 
          WHERE t.status = 'active'";
if ($sport_id) $query .= " AND t.sport_id = " . intval($sport_id);
if ($city_id) $query .= " AND t.city_id = " . intval($city_id);
$query .= " ORDER BY t.created_at DESC";

$turfs = $conn->query($query);
$sports = $conn->query("SELECT * FROM sports WHERE status='active'");
$cities = $conn->query("SELECT * FROM cities WHERE status='active'");

$user_id = $_SESSION['user_id'];
$fav_res = $conn->query("SELECT turf_id FROM favorite_turfs WHERE user_id = $user_id");
$user_favs = [];
while($f = $fav_res->fetch_assoc()) $user_favs[] = $f['turf_id'];
?>

<div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-3xl font-serif font-bold text-white mb-1 tracking-wider uppercase">Discover Turfs</h2>
        <p class="text-sm text-gray-400">Find the perfect ground for your next match</p>
    </div>
</div>

<div class="glass-card p-4 md:p-6 mb-8 border border-white/5">
    <form method="GET" action="index.php" class="flex flex-col md:flex-row gap-4">
        <input type="hidden" name="page" value="turfs">
        <div class="flex-1 relative">
            <i data-lucide="medal" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-emerald-500"></i>
            <select name="sport" class="w-full bg-void/80 border border-white/10 rounded-xl pl-11 pr-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 appearance-none text-sm shadow-inner transition-colors">
                <option value="">All Sports</option>
                <?php while($s = $sports->fetch_assoc()): ?>
                    <option value="<?php echo $s['id']; ?>" <?php echo $sport_id == $s['id'] ? 'selected' : ''; ?>><?php echo e($s['name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="flex-1 relative">
            <i data-lucide="map-pin" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-emerald-500"></i>
            <select name="city" class="w-full bg-void/80 border border-white/10 rounded-xl pl-11 pr-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 appearance-none text-sm shadow-inner transition-colors">
                <option value="">All Cities</option>
                <?php while($c = $cities->fetch_assoc()): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo $city_id == $c['id'] ? 'selected' : ''; ?>><?php echo e($c['name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="flex-1 relative">
            <i data-lucide="filter" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
            <select class="w-full bg-void/80 border border-white/10 rounded-xl pl-11 pr-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 appearance-none text-sm shadow-inner transition-colors">
                <option value="">Sort by: Default</option>
                <option value="price_asc">Price: Low to High</option>
                <option value="price_desc">Price: High to Low</option>
            </select>
        </div>
        <button type="submit" class="bg-gradient-to-r from-emerald-600 to-teal-500 text-white px-6 py-3 rounded-xl text-sm font-semibold tracking-wider hover:shadow-[0_0_15px_rgba(16,185,129,0.2)] transition-all active:scale-95 shrink-0 uppercase">
            Search
        </button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php if ($turfs && $turfs->num_rows > 0): ?>
        <?php while($t = $turfs->fetch_assoc()): ?>
            <div onclick="window.location.href='index.php?page=turf-details&id=<?php echo $t['id']; ?>'" class="bg-void/60 border border-white/5 rounded-2xl overflow-hidden hover:border-white/20 transition-all group flex flex-col shadow-lg relative cursor-pointer">
                <div class="h-48 relative overflow-hidden bg-gray-900 shrink-0">
                    <img src="https://images.unsplash.com/photo-1579952363873-27f3bade9f55?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-in-out" alt="<?php echo e($t['name']); ?>">
                    <div class="absolute inset-0 bg-gradient-to-t from-void via-void/40 to-transparent"></div>
                    <div class="absolute top-3 right-3 flex gap-2" onclick="event.stopPropagation()">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="toggle_favorite">
                            <input type="hidden" name="turf_id" value="<?php echo $t['id']; ?>">
                            <button type="submit" class="w-8 h-8 rounded-full bg-void/80 backdrop-blur-md border border-white/10 flex items-center justify-center transition-all hover:bg-white/10 active:scale-90 group/heart">
                                <i data-lucide="heart" class="w-4 h-4 <?php echo in_array($t['id'], $user_favs) ? 'fill-red-500 text-red-500' : 'text-white/60 group-hover/heart:text-red-400'; ?> transition-colors"></i>
                            </button>
                        </form>
                        <div class="bg-void/80 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/10 flex items-center gap-1">
                            <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400"></i>
                            <span class="text-xs font-mono font-bold text-white"><?php echo number_format($t['rating'], 1); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="p-5 flex flex-col flex-1 relative z-10">
                    <div class="mb-4">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-xl font-bold tracking-wide text-white leading-tight"><?php echo e($t['name']); ?></h3>
                        </div>
                        <div class="flex flex-wrap text-[10px] uppercase tracking-widest text-gray-400 font-semibold gap-2 mb-3">
                            <span class="flex items-center bg-white/5 py-1 px-2 rounded border border-white/5"><i data-lucide="medal" class="w-3.5 h-3.5 mr-1.5 text-emerald-400"></i> <?php echo e($t['sport_name']); ?></span>
                            <span class="flex items-center bg-white/5 py-1 px-2 rounded border border-white/5"><i data-lucide="map-pin" class="w-3.5 h-3.5 mr-1.5 text-cyan-400"></i> <?php echo e($t['city_name']); ?></span>
                        </div>
                    </div>
                    
                    <div class="mt-auto">
                        <div class="flex items-center justify-between mb-4 border-t border-white/5 pt-4">
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest">Price / Hour</p>
                                <p class="text-lg font-mono text-emerald-400 font-bold">₹<?php echo number_format($t['price']); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest">Availability</p>
                                <span class="inline-flex items-center text-[10px] text-emerald-400 mt-1 uppercase tracking-widest font-bold"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse mr-1"></span> Slots Open</span>
                            </div>
                        </div>
                        <a href="index.php?page=turf-details&id=<?php echo $t['id']; ?>" class="block w-full text-center bg-white/5 hover:bg-emerald-500/20 text-white hover:text-emerald-400 border border-white/10 hover:border-emerald-500/30 py-3 rounded-xl transition-colors text-xs font-semibold uppercase tracking-wider active:scale-[0.98]">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-span-full py-20 text-center glass-card">
            <i data-lucide="target" class="w-16 h-16 mx-auto text-gray-600 mb-4 opacity-50"></i>
            <h3 class="text-xl font-serif tracking-widest text-white uppercase mb-2">No Turfs Found</h3>
            <p class="text-gray-400 text-sm">Try adjusting your filters or location settings.</p>
        </div>
    <?php endif; ?>
</div>
