<?php
// Get counts for dashboard
$dashboard = [
    'users' => $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0] ?? 0,
    'owners' => $conn->query("SELECT COUNT(*) FROM owners")->fetch_row()[0] ?? 0,
    'turfs' => $conn->query("SELECT COUNT(*) FROM turfs")->fetch_row()[0] ?? 0,
    'bookings_today' => $conn->query("SELECT COUNT(*) FROM bookings WHERE DATE(booking_date) = CURDATE()")->fetch_row()[0] ?? 0,
    'revenue' => $conn->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payment_status = 'success'")->fetch_row()[0] ?? 0,
    'commission' => $conn->query("SELECT COALESCE(SUM(commission), 0) FROM payments WHERE payment_status = 'success'")->fetch_row()[0] ?? 0,
];
?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="p-6 rounded-xl border border-white/5 bg-void/30 flex items-center justify-between group hover:border-white/10 transition-colors">
        <div>
            <p class="text-xs uppercase tracking-widest text-gray-400 mb-2 font-semibold">Total Users</p>
            <h3 class="text-4xl font-light text-white"><?php echo number_format($dashboard['users']); ?></h3>
        </div>
        <div class="h-14 w-14 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-500 shadow-[0_0_15px_rgba(59,130,246,0.2)]">
            <i data-lucide="users" class="w-7 h-7"></i>
        </div>
    </div>
    
    <div class="p-6 rounded-xl border border-white/5 bg-void/30 flex items-center justify-between group hover:border-white/10 transition-colors">
        <div>
            <p class="text-xs uppercase tracking-widest text-gray-400 mb-2 font-semibold">Total Owners</p>
            <h3 class="text-4xl font-light text-white"><?php echo number_format($dashboard['owners']); ?></h3>
        </div>
        <div class="h-14 w-14 rounded-full bg-purple-500/10 flex items-center justify-center text-purple-500 shadow-[0_0_15px_rgba(168,85,247,0.2)]">
            <i data-lucide="user-check" class="w-7 h-7"></i>
        </div>
    </div>
    
    <div class="p-6 rounded-xl border border-white/5 bg-void/30 flex items-center justify-between group hover:border-white/10 transition-colors">
        <div>
            <p class="text-xs uppercase tracking-widest text-gray-400 mb-2 font-semibold">Total Turfs</p>
            <h3 class="text-4xl font-light text-white"><?php echo number_format($dashboard['turfs']); ?></h3>
        </div>
        <div class="h-14 w-14 rounded-full bg-green-500/10 flex items-center justify-center text-green-500 shadow-[0_0_15px_rgba(34,197,94,0.2)]">
            <i data-lucide="map-pin" class="w-7 h-7"></i>
        </div>
    </div>

    <div class="p-6 rounded-xl border border-white/5 bg-void/30 flex items-center justify-between group hover:border-white/10 transition-colors">
        <div>
            <p class="text-xs uppercase tracking-widest text-gray-400 mb-2 font-semibold">Bookings Today</p>
            <h3 class="text-4xl font-light text-white"><?php echo number_format($dashboard['bookings_today']); ?></h3>
        </div>
        <div class="h-14 w-14 rounded-full bg-orange-500/10 flex items-center justify-center text-orange-500 shadow-[0_0_15px_rgba(249,115,22,0.2)]">
            <i data-lucide="calendar" class="w-7 h-7"></i>
        </div>
    </div>
    
    <div class="p-6 rounded-xl border border-white/5 bg-void/30 flex items-center justify-between group hover:border-white/10 transition-colors">
        <div>
            <p class="text-xs uppercase tracking-widest text-gray-400 mb-2 font-semibold">Total Revenue</p>
            <h3 class="text-4xl font-light text-white">₹<?php echo number_format($dashboard['revenue'], 2); ?></h3>
        </div>
        <div class="h-14 w-14 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.2)]">
            <i data-lucide="indian-rupee" class="w-7 h-7"></i>
        </div>
    </div>

    <div class="p-6 rounded-xl border border-white/5 bg-void/30 flex items-center justify-between group hover:border-white/10 transition-colors">
        <div>
            <p class="text-xs uppercase tracking-widest text-gray-400 mb-2 font-semibold">Commission</p>
            <h3 class="text-4xl font-light text-white">₹<?php echo number_format($dashboard['commission'], 2); ?></h3>
        </div>
        <div class="h-14 w-14 rounded-full bg-pink-500/10 flex items-center justify-center text-pink-500 shadow-[0_0_15px_rgba(236,72,153,0.2)]">
            <i data-lucide="pie-chart" class="w-7 h-7"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="rounded-xl border border-white/5 bg-void/30 p-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/5 blur-[50px] pointer-events-none"></div>
        <h4 class="text-sm font-semibold uppercase tracking-wider mb-6 flex items-center text-gray-300">
            <i data-lucide="trending-up" class="w-5 h-5 mr-3 text-purple-400"></i> Top Performing Turfs
        </h4>
        <div class="space-y-4 relative z-10">
            <?php
            $topTurfs = $conn->query("
                SELECT t.name, COUNT(b.id) as b_count 
                FROM turfs t 
                LEFT JOIN bookings b ON t.id = b.turf_id 
                GROUP BY t.id 
                ORDER BY b_count DESC LIMIT 5
            ");
            if ($topTurfs && $topTurfs->num_rows > 0) {
                while($t = $topTurfs->fetch_assoc()) {
                    echo '<div class="flex items-center justify-between text-sm py-3 border-b border-white/5 last:border-0 hover:bg-white/5 px-2 rounded-lg transition-colors -mx-2">';
                    echo '<span class="text-gray-200 font-medium">'.e($t['name']).'</span>';
                    echo '<span class="text-gray-400 bg-white/5 px-3 py-1 rounded-full text-xs border border-white/5">'.e($t['b_count']).' bookings</span>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-sm text-gray-500 italic">No booking data available yet.</p>';
            }
            ?>
        </div>
    </div>
    
    <div class="rounded-xl border border-white/5 bg-void/30 p-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 blur-[50px] pointer-events-none"></div>
        <h4 class="text-sm font-semibold uppercase tracking-wider mb-6 flex items-center text-gray-300">
            <i data-lucide="clock" class="w-5 h-5 mr-3 text-blue-400"></i> Recent Activity
        </h4>
        <div class="space-y-4 relative z-10">
            <?php
            $recent = $conn->query("
                SELECT b.booking_date, u.name as uname, t.name as tname 
                FROM bookings b 
                JOIN users u ON b.user_id = u.id 
                JOIN turfs t ON b.turf_id = t.id 
                ORDER BY b.created_at DESC LIMIT 5
            ");
            if ($recent && $recent->num_rows > 0) {
                while($r = $recent->fetch_assoc()) {
                    echo '<div class="flex items-start text-sm py-3 border-b border-white/5 last:border-0 gap-4 hover:bg-white/5 px-2 rounded-lg transition-colors -mx-2">';
                    echo '<div class="mt-0.5 bg-blue-500/10 p-1.5 rounded-full text-blue-400 border border-blue-500/20"><i data-lucide="check" class="w-3.5 h-3.5"></i></div>';
                    echo '<div>';
                    echo '<p class="text-gray-300"><span class="text-white font-medium">'.e($r['uname']).'</span> booked <span class="text-white font-medium">'.e($r['tname']).'</span></p>';
                    echo '<p class="text-xs text-gray-500 mt-1 uppercase tracking-wider">'.e($r['booking_date']).'</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p class="text-sm text-gray-500 italic">No recent activity.</p>';
            }
            ?>
        </div>
    </div>
</div>
