<?php
$query = "
    SELECT p.*, b.booking_date, u.name as user_name 
    FROM payments p
    LEFT JOIN bookings b ON p.booking_id = b.id
    LEFT JOIN users u ON b.user_id = u.id
    ORDER BY p.created_at DESC
";
$payments = $conn->query($query);
?>
<div class="glass-card p-6">
    <div class="flex justify-between items-center mb-8 border-b border-white/5 pb-4">
        <div>
            <h3 class="text-2xl font-serif text-white tracking-widest uppercase shadow-sm">Transactions</h3>
            <p class="text-xs text-emerald-500 mt-1 uppercase tracking-wider flex items-center"><i data-lucide="dollar-sign" class="w-3.5 h-3.5 mr-1"></i> Financial Overview</p>
        </div>
    </div>

    <div class="overflow-x-auto relative">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gradient-to-r from-void to-transparent border-b border-white/10 uppercase text-xs tracking-widest text-gray-400">
                <tr>
                    <th class="px-6 py-4 font-semibold">TRX ID</th>
                    <th class="px-6 py-4 font-semibold">User / Booking</th>
                    <th class="px-6 py-4 font-semibold">Platform Fee</th>
                    <th class="px-6 py-4 font-semibold">Owner Payout</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if ($payments && $payments->num_rows > 0): ?>
                    <?php while($p = $payments->fetch_assoc()): ?>
                        <tr class="hover:bg-white/[0.03] transition-colors">
                            <td class="px-6 py-4 font-mono font-bold tracking-widest text-gray-300 flex items-center gap-2">
                                <i data-lucide="hash" class="w-3 h-3 text-emerald-500"></i>
                                <?php echo str_pad($p['id'], 8, '0', STR_PAD_LEFT); ?>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-white font-medium"><?php echo e($p['user_name'] ?: 'Unknown User'); ?></p>
                                <p class="text-xs text-gray-500 mt-1 font-mono hover:text-white transition-colors cursor-pointer" title="View Booking">BOOK-<?php echo str_pad($p['booking_id'], 6, '0', STR_PAD_LEFT); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-3 py-1 rounded font-mono shadow-sm tracking-wider">
                                    +₹<?php echo number_format($p['commission'], 2); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-white/5 text-gray-300 border border-white/10 px-3 py-1 rounded font-mono shadow-sm tracking-wider">
                                    -₹<?php echo number_format($p['owner_amount'], 2); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($p['payment_status'] === 'success'): ?>
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-[0_0_10px_rgba(16,185,129,0.2)] tooltip" title="Success">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </span>
                                <?php elseif($p['payment_status'] === 'failed'): ?>
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-500/10 text-red-500 border border-red-500/20 shadow-[0_0_10px_rgba(239,68,68,0.2)] tooltip" title="Failed">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-500/10 text-amber-500 border border-amber-500/20 shadow-[0_0_10px_rgba(245,158,11,0.2)] tooltip" title="Pending">
                                        <i data-lucide="clock" class="w-4 h-4"></i>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                            <i data-lucide="credit-card" class="w-12 h-12 mx-auto stroke-1 opacity-20 mb-4"></i>
                            <p class="font-serif tracking-widest uppercase text-lg">No Financial Data</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
