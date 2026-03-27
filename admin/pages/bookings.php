<?php
$query = "
    SELECT b.*, u.name as user_name, t.name as turf_name 
    FROM bookings b
    LEFT JOIN users u ON b.user_id = u.id
    LEFT JOIN turfs t ON b.turf_id = t.id
    ORDER BY b.created_at DESC
";
$bookings = $conn->query($query);
?>
<div class="glass-card p-6">
    <div class="flex justify-between items-center mb-8 border-b border-white/5 pb-4">
        <div>
            <h3 class="text-2xl font-serif text-white tracking-widest uppercase shadow-sm">Reservations</h3>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-wider">Monitor Platform Bookings</p>
        </div>
    </div>

    <div class="overflow-x-auto relative">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gradient-to-r from-void to-transparent border-b border-white/10 uppercase text-xs tracking-widest text-gray-400">
                <tr>
                    <th class="px-6 py-4 font-semibold">Booking ID</th>
                    <th class="px-6 py-4 font-semibold">Details</th>
                    <th class="px-6 py-4 font-semibold">Schedule</th>
                    <th class="px-6 py-4 font-semibold">Financials</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if ($bookings && $bookings->num_rows > 0): ?>
                    <?php while($b = $bookings->fetch_assoc()): ?>
                        <tr class="hover:bg-white/[0.03] transition-colors">
                            <td class="px-6 py-4 text-emerald-400 font-mono font-bold tracking-widest">
                                #<?php echo str_pad($b['id'], 6, '0', STR_PAD_LEFT); ?>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-white font-medium flex items-center gap-2"><i data-lucide="user" class="w-3.5 h-3.5 text-gray-500"></i> <?php echo e($b['user_name']); ?></p>
                                <p class="text-xs text-gray-400 mt-1 flex items-center gap-2"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-cyan-500"></i> <?php echo e($b['turf_name']); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-white font-medium tracking-wider"><i data-lucide="calendar" class="w-3.5 h-3.5 inline mr-1 text-purple-400"></i> <?php echo e($b['booking_date']); ?></p>
                                <p class="text-xs text-gray-400 mt-1 tracking-wider"><i data-lucide="clock" class="w-3.5 h-3.5 inline mr-1 text-orange-400"></i> <?php echo e($b['time_slot']); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-white font-mono flex justify-between gap-4"><span class="text-gray-500 text-xs">Total:</span> <span>₹<?php echo number_format($b['amount'], 2); ?></span></p>
                                <p class="text-emerald-400 font-mono mt-1 flex justify-between gap-4"><span class="text-emerald-500/50 text-xs text-uppercase tracking-widest">Comm:</span> <span>+₹<?php echo number_format($b['commission'], 2); ?></span></p>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($b['status'] === 'confirmed'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] uppercase font-bold tracking-widest">
                                        Confirmed
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-red-500/10 text-red-500 border border-red-500/20 text-[10px] uppercase font-bold tracking-widest">
                                        Cancelled
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                            <i data-lucide="calendar-x" class="w-12 h-12 mx-auto stroke-1 opacity-20 mb-4"></i>
                            <p class="font-serif tracking-widest uppercase text-lg">No Bookings Yet</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
