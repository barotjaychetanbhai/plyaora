<?php
$user_id = $_SESSION['user_id'];

// Get user info
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$firstName = explode(' ', trim($user['name']))[0];

// Total Bookings
$total_bookings = $conn->query("SELECT COUNT(*) FROM bookings WHERE user_id = $user_id")->fetch_row()[0] ?? 0;

// Upcoming Bookings
$upcoming_bookings = $conn->query("
    SELECT COUNT(*) FROM bookings 
    WHERE user_id = $user_id AND booking_date >= CURDATE() AND status IN ('pending', 'confirmed')
")->fetch_row()[0] ?? 0;

// Favorite Turfs count
$favorite_turfs = $conn->query("SELECT COUNT(*) FROM favorite_turfs WHERE user_id = $user_id")->fetch_row()[0] ?? 0;

// Next match details
$next_match_query = "
    SELECT b.*, t.name as turf_name, c.name as city_name, s.name as sport_name, t.id as turf_id
    FROM bookings b
    JOIN turfs t ON b.turf_id = t.id
    LEFT JOIN cities c ON t.city_id = c.id
    LEFT JOIN sports s ON t.sport_id = s.id
    WHERE b.user_id = $user_id AND b.booking_date >= CURDATE() AND b.status = 'confirmed'
    ORDER BY b.booking_date ASC, STR_TO_DATE(SUBSTRING_INDEX(b.time_slot, ' - ', 1), '%h:%i %p') ASC
    LIMIT 1
";
$next_match = $conn->query($next_match_query)->fetch_assoc();

// Recent bookings
$recent_bookings_query = "
    SELECT b.*, t.name as turf_name, s.name as sport_name
    FROM bookings b
    JOIN turfs t ON b.turf_id = t.id
    LEFT JOIN sports s ON t.sport_id = s.id
    WHERE b.user_id = $user_id
    ORDER BY b.created_at DESC
    LIMIT 3
";
$recent_bookings = $conn->query($recent_bookings_query);

// Recommended Turfs (Random active turfs)
$recommended_query = "
    SELECT t.*, c.name as city_name, s.name as sport_name 
    FROM turfs t
    LEFT JOIN cities c ON t.city_id = c.id
    LEFT JOIN sports s ON t.sport_id = s.id
    WHERE t.status = 'active'
    ORDER BY RAND()
    LIMIT 2
";
$recommended_turfs = $conn->query($recommended_query);

// Get user favorite IDs
$fav_res = $conn->query("SELECT turf_id FROM favorite_turfs WHERE user_id = $user_id");
$user_favs = [];
while($f = $fav_res->fetch_assoc()) $user_favs[] = $f['turf_id'];
?>

<div class="space-y-12 pb-12 animate-in fade-in duration-700">

    <!-- ========== HERO SECTION – enhanced ========== -->
    <section class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-emerald-950 via-emerald-900 to-void p-10 md:p-14 shadow-2xl border border-emerald-500/10">
        <!-- Animated background blobs -->
        <div class="absolute -right-20 -top-20 h-80 w-80 rounded-full bg-emerald-400/20 blur-[120px] animate-pulse-glow"></div>
        <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-teal-500/10 blur-[100px]"></div>
        <div class="absolute top-40 left-1/3 h-40 w-40 rounded-full bg-emerald-300/5 blur-[80px]"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-12">
            <div class="text-left space-y-6 max-w-2xl">
                <div class="space-y-3">
                    <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-[0.25em] border border-emerald-500/20">Player dashboard</span>
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-white tracking-tight leading-[1.1]">
                        Welcome back,<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-teal-200"><?php echo e($firstName); ?></span>
                    </h1>
                    <p class="text-xl md:text-2xl text-emerald-50/60 font-medium max-w-xl">Ready for your next victory? Book your favourite turf now.</p>
                </div>
                
                <div class="flex flex-wrap gap-5 pt-4">
                    <a href="index.php?page=turfs" class="group bg-white text-void px-10 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-emerald-50 hover:scale-105 transition-all shadow-2xl shadow-emerald-950/30 flex items-center gap-3">
                        <span>Browse Turfs</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <a href="index.php?page=bookings" class="group bg-white/5 backdrop-blur-xl border border-white/10 text-white px-10 py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-white/10 transition-all flex items-center gap-3">
                        <span>View My Matches</span>
                        <i data-lucide="calendar" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
                    </a>
                </div>
            </div>
            
            <div class="hidden lg:block shrink-0 relative">
                <div class="relative z-10 transform rotate-2 hover:rotate-0 transition-transform duration-700">
                    <div class="absolute -inset-4 bg-emerald-500/20 blur-3xl rounded-3xl"></div>
                    <img src="https://images.unsplash.com/photo-1543351611-58f69d7c1781?q=80&w=400&auto=format&fit=crop" alt="Sports" class="relative w-96 h-[28rem] object-cover rounded-3xl shadow-2xl border border-white/20">
                    <!-- Glass badge on image -->
                    <div class="absolute -bottom-8 -left-8 glass-card p-5 flex items-center gap-4 shadow-2xl">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-void shadow-lg">
                            <i data-lucide="trophy" class="w-7 h-7"></i>
                        </div>
                        <div>
                            <p class="text-[9px] text-gray-400 font-black uppercase tracking-[0.3em]">Player Rank</p>
                            <p class="text-white font-black text-xl tracking-tight">Pro League</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== QUICK ACTION BAR – enhanced ========== -->
    <section>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Search Turfs -->
            <a href="index.php?page=turfs" class="group glass-card p-8 flex flex-col items-center justify-center gap-4 hover:translate-y-[-8px] hover:border-emerald-500/50 hover:bg-emerald-500/5 transition-all duration-300 border border-white/5">
                <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-emerald-500 group-hover:text-void transition-all duration-300 shadow-xl group-hover:shadow-emerald-500/30">
                    <i data-lucide="search" class="w-7 h-7"></i>
                </div>
                <span class="text-xs font-black tracking-[0.2em] uppercase text-gray-400 group-hover:text-white">Search Turfs</span>
            </a>
            
            <!-- Book Again -->
            <a href="index.php?page=bookings" class="group glass-card p-8 flex flex-col items-center justify-center gap-4 hover:translate-y-[-8px] hover:border-cyan-500/50 hover:bg-cyan-500/5 transition-all duration-300 border border-white/5">
                <div class="w-16 h-16 rounded-2xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-cyan-500 group-hover:text-void transition-all duration-300 shadow-xl group-hover:shadow-cyan-500/30">
                    <i data-lucide="refresh-cw" class="w-7 h-7"></i>
                </div>
                <span class="text-xs font-black tracking-[0.2em] uppercase text-gray-400 group-hover:text-white">Book Again</span>
            </a>
            
            <!-- Favorites -->
            <a href="index.php?page=favorites" class="group glass-card p-8 flex flex-col items-center justify-center gap-4 hover:translate-y-[-8px] hover:border-purple-500/50 hover:bg-purple-500/5 transition-all duration-300 border border-white/5">
                <div class="w-16 h-16 rounded-2xl bg-purple-500/10 text-purple-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-purple-500 group-hover:text-void transition-all duration-300 shadow-xl group-hover:shadow-purple-500/30">
                    <i data-lucide="heart" class="w-7 h-7"></i>
                </div>
                <span class="text-xs font-black tracking-[0.2em] uppercase text-gray-400 group-hover:text-white">Favorites</span>
            </a>
            
            <!-- My Bookings -->
            <a href="index.php?page=bookings" class="group glass-card p-8 flex flex-col items-center justify-center gap-4 hover:translate-y-[-8px] hover:border-amber-500/50 hover:bg-amber-500/5 transition-all duration-300 border border-white/5">
                <div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-void transition-all duration-300 shadow-xl group-hover:shadow-amber-500/30">
                    <i data-lucide="calendar" class="w-7 h-7"></i>
                </div>
                <span class="text-xs font-black tracking-[0.2em] uppercase text-gray-400 group-hover:text-white">My Bookings</span>
            </a>
        </div>
    </section>

    <!-- ========== STATISTICS CARDS – enhanced ========== -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Upcoming -->
        <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-950/40 to-emerald-900/20 border border-emerald-500/10 p-8 hover:border-emerald-500/30 transition-all duration-500">
            <div class="absolute -right-6 -top-6 opacity-10 group-hover:opacity-20 group-hover:scale-110 transition-all duration-700">
                <i data-lucide="clock" class="w-32 h-32 text-emerald-400"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_12px_rgba(16,185,129,1)]"></span>
                    <p class="text-[10px] font-black text-emerald-400 uppercase tracking-[0.3em]">Upcoming Matches</p>
                </div>
                <h3 class="text-7xl font-mono font-black text-white mb-2"><?php echo $upcoming_bookings; ?></h3>
                <p class="text-sm text-gray-500 font-medium">scheduled</p>
            </div>
        </div>
        
        <!-- Total Matches -->
        <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-cyan-950/40 to-cyan-900/20 border border-cyan-500/10 p-8 hover:border-cyan-500/30 transition-all duration-500">
            <div class="absolute -right-6 -top-6 opacity-10 group-hover:opacity-20 group-hover:scale-110 transition-all duration-700">
                <i data-lucide="activity" class="w-32 h-32 text-cyan-400"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-cyan-400 uppercase tracking-[0.3em] mb-4">Total Matches</p>
                <h3 class="text-7xl font-mono font-black text-white mb-2"><?php echo $total_bookings; ?></h3>
                <p class="text-sm text-gray-500 font-medium">all time</p>
            </div>
        </div>
        
        <!-- Favorite Turfs -->
        <a href="index.php?page=favorites" class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-purple-950/40 to-purple-900/20 border border-purple-500/10 p-8 hover:border-purple-500/30 transition-all duration-500 block">
            <div class="absolute -right-6 -top-6 opacity-10 group-hover:opacity-20 group-hover:scale-110 transition-all duration-700">
                <i data-lucide="heart" class="w-32 h-32 text-purple-400"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-purple-400 uppercase tracking-[0.3em] mb-4">Favorite Turfs</p>
                <h3 class="text-7xl font-mono font-black text-white mb-2"><?php echo $favorite_turfs; ?></h3>
                <p class="text-sm text-gray-500 font-medium">saved venues</p>
            </div>
        </a>
    </section>

    <!-- ========== NEXT MATCH FEATURE – enhanced ========== -->
    <?php if ($next_match): ?>
    <section>
        <div onclick="window.location.href='index.php?page=turf-details&id=<?php echo $next_match['turf_id']; ?>'" class="group relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-emerald-950/40 to-emerald-900/20 border border-emerald-500/10 hover:border-emerald-500/30 transition-all duration-500 cursor-pointer">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-600/10 via-transparent to-transparent pointer-events-none"></div>
            
            <div class="flex flex-col lg:flex-row min-h-[500px]">
                <!-- Left content -->
                <div class="flex-1 p-10 md:p-14 flex flex-col justify-between space-y-8">
                    <div class="space-y-6">
                        <div class="flex flex-wrap items-center gap-4">
                            <span class="px-5 py-2 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-[0.2em] border border-emerald-500/30 shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                                Next Match
                            </span>
                            <?php 
                                $date1 = new DateTime(date('Y-m-d'));
                                $date2 = new DateTime($next_match['booking_date']);
                                $interval = $date1->diff($date2);
                                $days = $interval->days;
                            ?>
                            <div class="flex items-center gap-2 text-gray-400 font-bold text-sm bg-black/20 px-4 py-2 rounded-full">
                                <i data-lucide="bell" class="w-4 h-4 text-emerald-500/70"></i>
                                <span>Next match in: <span class="text-white font-mono"><?php echo $days; ?> days</span></span>
                            </div>
                        </div>
                        
                        <div>
                            <h2 class="text-4xl md:text-5xl font-black text-white mb-4 group-hover:text-emerald-300 transition-colors">
                                <?php echo e($next_match['turf_name']); ?>
                            </h2>
                            <div class="flex flex-wrap gap-6">
                                <span class="flex items-center gap-2.5 text-gray-400">
                                    <div class="p-1.5 rounded-lg bg-white/5"><i data-lucide="medal" class="w-4 h-4 text-emerald-400"></i></div>
                                    <span class="font-medium text-sm"><?php echo e($next_match['sport_name']); ?></span>
                                </span>
                                <span class="flex items-center gap-2.5 text-gray-400">
                                    <div class="p-1.5 rounded-lg bg-white/5"><i data-lucide="map-pin" class="w-4 h-4 text-emerald-400"></i></div>
                                    <span class="font-medium text-sm"><?php echo e($next_match['city_name']); ?></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-px bg-white/10 border border-white/10 rounded-2xl overflow-hidden backdrop-blur-md">
                        <div class="p-6 bg-void/40">
                            <p class="text-[9px] text-emerald-400/60 font-black uppercase tracking-[0.2em] mb-1">Match Date</p>
                            <p class="text-lg md:text-xl font-mono font-bold text-white"><?php echo date('M d, Y', strtotime($next_match['booking_date'])); ?></p>
                        </div>
                        <div class="p-6 bg-void/40">
                            <p class="text-[9px] text-emerald-400/60 font-black uppercase tracking-[0.2em] mb-1">Kick-off Time</p>
                            <p class="text-lg md:text-xl font-mono font-bold text-white"><?php echo e($next_match['time_slot']); ?></p>
                        </div>
                    </div>
                    
                    <div>
                        <a href="index.php?page=turf-details&id=<?php echo $next_match['turf_id']; ?>" class="inline-flex items-center gap-3 bg-emerald-500 hover:bg-emerald-400 text-void px-12 py-6 rounded-2xl font-black uppercase tracking-widest text-xs transition-all shadow-xl shadow-emerald-500/20 active:scale-95">
                            <span>View Turf</span>
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Right image -->
                <div class="lg:w-[450px] xl:w-[550px] relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1543351611-58f69d7c1781?q=80&w=1200&auto=format&fit=crop" alt="Turf" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-[1.8s]">
                    <div class="absolute inset-0 bg-gradient-to-t lg:bg-gradient-to-l from-void/80 via-transparent to-transparent"></div>
                    
                    <!-- Floating overlay -->
                    <div class="absolute top-8 right-8 glass-card p-5 animate-bounce shadow-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,1)] animate-pulse"></div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-emerald-400">Live Status</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========== TWO‑COLUMN LAYOUT ========== -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">

        <!-- LEFT: Recent Activity -->
        <div class="xl:col-span-8 space-y-8">
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-black flex items-center gap-3">
                    <span class="w-1.5 h-8 rounded-full bg-gradient-to-b from-cyan-400 to-blue-500"></span>
                    Recent Activity
                </h3>
                <a href="index.php?page=bookings" class="text-[9px] font-black text-gray-500 hover:text-white uppercase tracking-[0.2em] px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 transition-all">
                    View All
                </a>
            </div>
            
            <div class="space-y-4">
                <?php if ($recent_bookings && $recent_bookings->num_rows > 0): ?>
                    <?php while($rb = $recent_bookings->fetch_assoc()): ?>
                        <div onclick="window.location.href='index.php?page=turf-details&id=<?php echo $rb['turf_id']; ?>'" class="group glass-card p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:bg-white/5 hover:border-white/20 transition-all border border-white/5 cursor-pointer rounded-2xl">
                            <div class="flex items-center gap-6">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gray-900 to-void border border-white/10 flex items-center justify-center text-gray-500 group-hover:text-cyan-400 group-hover:border-cyan-500/30 transition-all transform group-hover:rotate-6 shadow-xl">
                                    <i data-lucide="<?php echo $rb['status'] === 'confirmed' ? 'calendar-check' : ($rb['status'] === 'completed' ? 'flag' : ($rb['status'] === 'pending' ? 'clock' : 'x-circle')); ?>" class="w-8 h-8"></i>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-black text-xl text-white group-hover:text-cyan-300 transition-colors tracking-tight"><?php echo e($rb['turf_name']); ?></h4>
                                    <div class="flex items-center gap-3 text-xs">
                                        <span class="flex items-center gap-1.5 text-gray-500">
                                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i> <?php echo date('M d, Y', strtotime($rb['booking_date'])); ?>
                                        </span>
                                        <span class="w-1 h-1 rounded-full bg-gray-700"></span>
                                        <span class="flex items-center gap-1.5 text-cyan-500/70">
                                            <i data-lucide="dribbble" class="w-3.5 h-3.5"></i> <?php echo e($rb['sport_name']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between md:flex-col md:items-end gap-3">
                                <?php 
                                    $statusClass = 'text-amber-400 bg-amber-400/10 border-amber-400/20';
                                    if($rb['status'] === 'confirmed') $statusClass = 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20 shadow-[0_0_12px_rgba(16,185,129,0.2)]';
                                    elseif($rb['status'] === 'completed') $statusClass = 'text-blue-400 bg-blue-400/10 border-blue-400/20';
                                    elseif($rb['status'] === 'cancelled') $statusClass = 'text-red-400 bg-red-400/10 border-red-400/20';
                                ?>
                                <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-[0.2em] border <?php echo $statusClass; ?>">
                                    <?php echo e($rb['status']); ?>
                                </span>
                                <a href="index.php?page=turf-details&id=<?php echo $rb['turf_id']; ?>" class="text-[9px] font-black text-gray-500 hover:text-white uppercase tracking-widest flex items-center gap-2 px-3 py-1 hover:bg-white/5 rounded-lg transition-all">
                                    <i data-lucide="rotate-cw" class="w-3.5 h-3.5 group-hover:rotate-180 transition-transform duration-500"></i> Rebook
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="glass-card p-16 text-center border-dashed border-white/10 bg-void/30 rounded-3xl">
                        <div class="w-24 h-24 bg-white/5 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-inner">
                            <i data-lucide="inbox" class="w-10 h-10 text-gray-700"></i>
                        </div>
                        <h4 class="text-xl font-black text-white mb-2">No active matches</h4>
                        <p class="text-gray-500 font-medium mb-8 max-w-xs mx-auto text-sm">You haven't booked any turfs recently. Your sports legacy starts here!</p>
                        <a href="index.php?page=turfs" class="inline-flex items-center gap-3 bg-white text-void px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-emerald-400 transition-all">
                            <span>Start Exploring</span>
                            <i data-lucide="compass" class="w-4 h-4"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT: Discover New Turfs -->
        <div class="xl:col-span-4 space-y-8">
            <h3 class="text-2xl font-black flex items-center gap-3">
                <span class="w-1.5 h-8 rounded-full bg-gradient-to-b from-purple-400 to-fuchsia-600"></span>
                Discover New Turfs
            </h3>
            
            <div class="grid grid-cols-1 gap-6">
                <?php if ($recommended_turfs && $recommended_turfs->num_rows > 0): ?>
                    <?php while($rt = $recommended_turfs->fetch_assoc()): ?>
                        <div onclick="window.location.href='index.php?page=turf-details&id=<?php echo $rt['id']; ?>'" class="group relative overflow-hidden rounded-3xl bg-void border border-white/5 hover:border-purple-500/30 transition-all duration-500 cursor-pointer">
                            <div class="h-56 relative overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1543351611-58f69d7c1781?q=80&w=600&auto=format&fit=crop" alt="Turf" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-[1.5s]">
                                <div class="absolute inset-0 bg-gradient-to-t from-void via-void/40 to-transparent"></div>
                                
                                <div class="absolute top-4 right-4 flex gap-2" onclick="event.stopPropagation()">
                                    <form method="POST" action="">
                                        <input type="hidden" name="action" value="toggle_favorite">
                                        <input type="hidden" name="turf_id" value="<?php echo $rt['id']; ?>">
                                        <button type="submit" class="w-10 h-10 rounded-full bg-void/80 backdrop-blur-md border border-white/10 flex items-center justify-center transition-all hover:bg-white/10 active:scale-90 group/heart">
                                            <i data-lucide="heart" class="w-5 h-5 <?php echo in_array($rt['id'], $user_favs) ? 'fill-red-500 text-red-500' : 'text-white/60 group-hover/heart:text-red-400'; ?> transition-colors"></i>
                                        </button>
                                    </form>
                                    <span class="px-3 py-1 rounded-lg bg-void/80 backdrop-blur-md text-white text-[9px] font-black uppercase tracking-widest border border-white/10 flex items-center gap-1">
                                        <i data-lucide="star" class="w-3 h-3 text-amber-400 fill-amber-400"></i> 4.8
                                    </span>
                                </div>

                                <div class="absolute bottom-5 left-6 right-6">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-2 py-0.5 rounded bg-purple-500 text-white text-[9px] font-black uppercase tracking-widest">
                                            <?php echo e($rt['sport_name']); ?>
                                        </span>
                                    </div>
                                    <h4 class="text-2xl font-black text-white tracking-tight"><?php echo e($rt['name']); ?></h4>
                                    <p class="text-xs text-gray-400 flex items-center gap-1.5 mt-2">
                                        <i data-lucide="map-pin" class="w-4 h-4 text-purple-400"></i> <?php echo e($rt['city_name']); ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="p-6 flex items-center justify-between border-t border-white/5 bg-void/80 backdrop-blur-md">
                                <div>
                                    <p class="text-[9px] text-gray-500 font-black uppercase tracking-[0.2em] mb-1">Starting from</p>
                                    <p class="text-xl font-mono font-black text-emerald-400">
                                        ₹<?php echo number_format($rt['price']); ?><span class="text-xs font-sans text-gray-500 ml-1 font-normal">/hr</span>
                                    </p>
                                </div>
                                <a href="index.php?page=turf-details&id=<?php echo $rt['id']; ?>" class="w-14 h-14 bg-white/5 hover:bg-emerald-500 text-gray-400 hover:text-void rounded-2xl flex items-center justify-center transition-all duration-300 border border-white/10 hover:border-emerald-500 group-hover:scale-110">
                                    <i data-lucide="chevron-right" class="w-8 h-8"></i>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="glass-card p-12 text-center border-dashed border-white/10 border-2 rounded-3xl">
                        <i data-lucide="search-x" class="w-12 h-12 text-gray-700 mx-auto mb-4"></i>
                        <p class="text-gray-500 text-sm font-medium">No new turfs matching your profile today. Check back tomorrow!</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Global CTA -->
            <div class="rounded-3xl bg-gradient-to-br from-indigo-900/40 to-purple-900/40 border border-white/10 p-8 relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 opacity-20 group-hover:scale-110 transition-transform duration-700">
                    <i data-lucide="zap" class="w-40 h-40 text-purple-400"></i>
                </div>
                <div class="relative z-10">
                    <h4 class="text-xl font-black text-white mb-2">Explore All Spots</h4>
                    <p class="text-sm text-gray-400 mb-6">Find the perfect turf for your next game night with friends.</p>
                    <a href="index.php?page=turfs" class="text-xs font-black uppercase tracking-widest text-purple-300 hover:text-white flex items-center gap-2 transition-colors">
                        View Marketplace <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>