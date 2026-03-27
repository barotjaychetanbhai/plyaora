<?php
$owner_id = $_SESSION['owner_id'];

// Total Revenue
$total_revenue = $conn->query("
    SELECT COALESCE(SUM(owner_amount), 0) FROM payments p 
    JOIN bookings b ON p.booking_id = b.id JOIN turfs t ON b.turf_id = t.id 
    WHERE t.owner_id = $owner_id AND p.payment_status = 'success'
")->fetch_row()[0] ?? 0;

// Today's Earnings
$today_earnings = $conn->query("
    SELECT COALESCE(SUM(owner_amount), 0) FROM payments p 
    JOIN bookings b ON p.booking_id = b.id JOIN turfs t ON b.turf_id = t.id 
    WHERE t.owner_id = $owner_id AND p.payment_status = 'success' AND DATE(p.created_at) = CURDATE()
")->fetch_row()[0] ?? 0;

// Weekly Earnings
$weekly_earnings = $conn->query("
    SELECT COALESCE(SUM(owner_amount), 0) FROM payments p 
    JOIN bookings b ON p.booking_id = b.id JOIN turfs t ON b.turf_id = t.id 
    WHERE t.owner_id = $owner_id AND p.payment_status = 'success' AND YEARWEEK(p.created_at, 1) = YEARWEEK(CURDATE(), 1)
")->fetch_row()[0] ?? 0;

// Monthly Earnings
$monthly_earnings = $conn->query("
    SELECT COALESCE(SUM(owner_amount), 0) FROM payments p 
    JOIN bookings b ON p.booking_id = b.id JOIN turfs t ON b.turf_id = t.id 
    WHERE t.owner_id = $owner_id AND p.payment_status = 'success' AND MONTH(p.created_at) = MONTH(CURDATE()) AND YEAR(p.created_at) = YEAR(CURDATE())
")->fetch_row()[0] ?? 0;

// All Earnings History
$query = "
    SELECT p.*, b.id as booking_id, b.booking_date, b.time_slot, t.name as turf_name, u.name as user_name, b.amount as gross_amount, p.commission
    FROM payments p
    JOIN bookings b ON p.booking_id = b.id
    JOIN turfs t ON b.turf_id = t.id
    LEFT JOIN users u ON b.user_id = u.id
    WHERE t.owner_id = $owner_id AND p.payment_status = 'success'
    ORDER BY p.created_at DESC
";
$earnings = $conn->query($query);
?>
<div class="glass-card p-6 border-t-[3px] border-t-emerald-500/50">
    <div class="flex justify-between items-center mb-8 border-b border-white/10 pb-4">
        <div>
            <h3 class="text-2xl font-serif text-white tracking-widest uppercase">Earnings Dashboard</h3>
            <p class="text-xs text-gray-400 mt-1">Track your financial performance</p>
        </div>
        <button class="bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white px-5 py-2.5 rounded-lg text-sm font-semibold tracking-wider transition-all flex items-center shadow-lg shadow-emerald-500/20 active:scale-95">
            <i data-lucide="download" class="w-4 h-4 mr-2"></i> Export Report
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-void/40 border border-white/5 rounded-2xl p-5 hover:border-emerald-500/30 transition-all relative overflow-hidden group shadow-lg">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-[30px] pointer-events-none group-hover:bg-emerald-500/20 transition-colors"></div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-1 flex items-center justify-between">Today <i data-lucide="calendar" class="w-3.5 h-3.5 text-emerald-500"></i></p>
            <h3 class="text-3xl font-mono tracking-wide text-white">₹<?php echo number_format($today_earnings, 2); ?></h3>
        </div>
        <div class="bg-void/40 border border-white/5 rounded-2xl p-5 hover:border-blue-500/30 transition-all relative overflow-hidden group shadow-lg">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/10 rounded-full blur-[30px] pointer-events-none group-hover:bg-blue-500/20 transition-colors"></div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-1 flex items-center justify-between">This Week <i data-lucide="bar-chart-2" class="w-3.5 h-3.5 text-blue-500"></i></p>
            <h3 class="text-3xl font-mono tracking-wide text-white">₹<?php echo number_format($weekly_earnings, 2); ?></h3>
        </div>
        <div class="bg-void/40 border border-white/5 rounded-2xl p-5 hover:border-purple-500/30 transition-all relative overflow-hidden group shadow-lg">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-500/10 rounded-full blur-[30px] pointer-events-none group-hover:bg-purple-500/20 transition-colors"></div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest mb-1 flex items-center justify-between">This Month <i data-lucide="pie-chart" class="w-3.5 h-3.5 text-purple-500"></i></p>
            <h3 class="text-3xl font-mono tracking-wide text-white">₹<?php echo number_format($monthly_earnings, 2); ?></h3>
        </div>
        <div class="bg-gradient-to-br from-emerald-900/40 to-teal-900/40 border border-emerald-500/30 rounded-2xl p-5 relative overflow-hidden shadow-[0_0_30px_rgba(16,185,129,0.15)] flex flex-col justify-end">
            <p class="text-xs font-semibold text-emerald-500/80 uppercase tracking-widest mb-1 flex items-center justify-between">Total Earnings <i data-lucide="wallet" class="w-4 h-4 text-emerald-400"></i></p>
            <h3 class="text-4xl font-mono tracking-wide text-emerald-400 font-bold drop-shadow-lg">₹<?php echo number_format($total_revenue, 2); ?></h3>
        </div>
    </div>

    <h4 class="text-sm font-semibold text-white tracking-wider uppercase mb-4 flex items-center"><i data-lucide="split-square-vertical" class="w-4 h-4 mr-2 text-cyan-500"></i> Transaction History</h4>
    
    <div class="overflow-x-auto bg-void/30 rounded-xl border border-white/5">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-black/30 text-[10px] uppercase tracking-widest text-gray-500">
                    <th class="px-6 py-4 font-semibold">Tnx ID / Date</th>
                    <th class="px-6 py-4 font-semibold">Turf Details</th>
                    <th class="px-6 py-4 font-semibold text-right">Gross Amount</th>
                    <th class="px-6 py-4 font-semibold text-right whitespace-nowrap">Platform Cut</th>
                    <th class="px-6 py-4 font-semibold text-right">Net Earning</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-white/5">
                <?php if ($earnings && $earnings->num_rows > 0): ?>
                    <?php while($e = $earnings->fetch_assoc()): ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-white font-mono font-medium tracking-wider">#<?php echo str_pad($e['id'], 6, '0', STR_PAD_LEFT); ?></span>
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1"><?php echo date('M d, Y h:i A', strtotime($e['created_at'])); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-white font-medium tracking-wide"><?php echo e($e['turf_name']); ?></p>
                                <p class="text-xs text-gray-400 font-mono mt-0.5"><i data-lucide="calendar" class="w-3 h-3 inline mr-1 text-cyan-400"></i> <?php echo e($e['booking_date']); ?> <span class="text-gray-600 mx-1">|</span> <?php echo e($e['user_name'] ?: 'Guest'); ?></p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-mono text-gray-400 tracking-wider">₹<?php echo number_format($e['gross_amount'], 2); ?></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="bg-red-500/10 text-red-400 border border-red-500/20 px-3 py-1 rounded text-xs font-mono shadow-sm tracking-widest">
                                    -₹<?php echo number_format($e['commission'], 2); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-3 py-1 rounded font-mono font-bold shadow-sm tracking-wider">
                                    +₹<?php echo number_format($e['owner_amount'], 2); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <i data-lucide="wallet" class="w-12 h-12 mx-auto text-gray-600 mb-4 opacity-30"></i>
                            <h4 class="text-sm font-semibold tracking-widest uppercase text-gray-400 mb-1">No Earnings Yet</h4>
                            <p class="text-xs text-gray-500">Completed bookings will appear here.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
