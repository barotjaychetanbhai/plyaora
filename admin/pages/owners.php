<?php
$search = $_GET['search'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $owner_id = $_POST['owner_id'] ?? 0;
    
    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE owners SET status = 'approved' WHERE id = ?");
        $stmt->bind_param("i", $owner_id);
        $stmt->execute();
    } elseif ($action === 'suspend') {
        $stmt = $conn->prepare("UPDATE owners SET status = 'suspended' WHERE id = ?");
        $stmt->bind_param("i", $owner_id);
        $stmt->execute();
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("DELETE FROM owners WHERE id = ? AND status = 'pending'");
        $stmt->bind_param("i", $owner_id);
        $stmt->execute();
    }
    echo "<script>window.location.href='index.php?page=owners';</script>";
    exit();
}

$query = "
    SELECT o.*, 
    (SELECT COUNT(*) FROM turfs WHERE owner_id = o.id) as turf_count,
    (SELECT COALESCE(SUM(owner_amount), 0) FROM payments p JOIN bookings b ON p.booking_id = b.id JOIN turfs t ON b.turf_id = t.id WHERE t.owner_id = o.id AND p.payment_status = 'success') as total_revenue
    FROM owners o
";

if ($search) {
    $searchTerm = "%$search%";
    $query .= " WHERE o.name LIKE ? OR o.email LIKE ?";
    $stmt = $conn->prepare($query . " ORDER BY o.created_at DESC");
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $owners = $stmt->get_result();
} else {
    $owners = $conn->query($query . " ORDER BY o.created_at DESC");
}
?>

<div class="glass-card p-6 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-purple-900/10 to-transparent pointer-events-none"></div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 relative z-10 border-b border-white/5 pb-4">
        <div>
            <h3 class="text-2xl font-serif text-white tracking-widest uppercase shadow-sm">Turf Owners</h3>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-wider">Manage & Authorize Platform Partners</p>
        </div>
        
        <form method="GET" action="index.php" class="relative w-full md:w-72">
            <input type="hidden" name="page" value="owners">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
            <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Search owners..." class="w-full bg-void/50 border border-white/10 rounded-lg pl-10 pr-4 py-2.5 text-sm text-white focus:outline-none focus:border-purple-500/50 focus:bg-void transition-all shadow-inner">
        </form>
    </div>

    <div class="overflow-x-auto relative z-10">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-gradient-to-r from-void to-transparent border-b border-white/10 uppercase text-xs tracking-widest text-gray-400">
                <tr>
                    <th class="px-6 py-4 font-semibold w-1/4">Owner Info</th>
                    <th class="px-6 py-4 font-semibold">Turfs</th>
                    <th class="px-6 py-4 font-semibold">Revenue</th>
                    <th class="px-6 py-4 font-semibold">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if ($owners && $owners->num_rows > 0): ?>
                    <?php while($o = $owners->fetch_assoc()): ?>
                        <tr class="hover:bg-white/[0.03] transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center text-sm font-bold text-white shadow-lg shrink-0">
                                        <?php echo strtoupper(substr($o['name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <p class="text-white font-medium group-hover:text-purple-300 transition-colors"><?php echo e($o['name']); ?></p>
                                        <div class="flex items-center gap-3 mt-1">
                                            <span class="text-xs text-gray-500 flex items-center gap-1"><i data-lucide="mail" class="w-3 h-3"></i> <?php echo e($o['email']); ?></span>
                                            <span class="text-xs text-gray-500 flex items-center gap-1"><i data-lucide="phone" class="w-3 h-3"></i> <?php echo e($o['phone']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-white/5 border border-white/10 px-3 py-1 rounded-full text-gray-300 font-mono text-xs shadow-sm">
                                    <i data-lucide="map" class="w-3 h-3 inline mr-1"></i> <?php echo $o['turf_count']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-emerald-400 font-mono tracking-wide">
                                ₹<?php echo number_format($o['total_revenue'], 2); ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($o['status'] === 'approved'): ?>
                                    <span class="inline-flex items-center justify-center min-w-[90px] gap-1.5 px-2.5 py-1 rounded border border-emerald-500/20 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold uppercase tracking-widest">
                                        Active
                                    </span>
                                <?php elseif($o['status'] === 'pending'): ?>
                                    <span class="inline-flex items-center justify-center min-w-[90px] gap-1.5 px-2.5 py-1 rounded border border-amber-500/20 bg-amber-500/10 text-amber-400 text-[10px] font-bold uppercase tracking-widest">
                                        Pending
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center justify-center min-w-[90px] gap-1.5 px-2.5 py-1 rounded border border-red-500/20 bg-red-500/10 text-red-500 text-[10px] font-bold uppercase tracking-widest">
                                        Suspended
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flexItems-center justify-end gap-2 opacity-70 group-hover:opacity-100 transition-opacity">
                                    <?php if($o['status'] === 'pending'): ?>
                                        <form method="POST" action="" class="inline" onsubmit="return confirm('Approve this turf owner?');">
                                            <input type="hidden" name="owner_id" value="<?php echo $o['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="p-2 text-emerald-400 hover:bg-emerald-400/20 hover:scale-110 rounded-lg transition-all border border-transparent hover:border-emerald-500/30" title="Approve">
                                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="" class="inline" onsubmit="return confirm('Reject this initial application?');">
                                            <input type="hidden" name="owner_id" value="<?php echo $o['id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="p-2 text-red-500 hover:bg-red-500/20 hover:scale-110 rounded-lg transition-all border border-transparent hover:border-red-500/30" title="Reject">
                                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="" class="inline" onsubmit="return confirm('Change status for this owner?');">
                                            <input type="hidden" name="owner_id" value="<?php echo $o['id']; ?>">
                                            <?php if($o['status'] === 'approved'): ?>
                                                <input type="hidden" name="action" value="suspend">
                                                <button type="submit" class="p-2 text-orange-400 hover:bg-orange-400/20 hover:scale-110 rounded-lg transition-all border border-transparent hover:border-orange-500/30" title="Suspend">
                                                    <i data-lucide="pause-circle" class="w-4 h-4"></i>
                                                </button>
                                            <?php else: ?>
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="p-2 text-emerald-400 hover:bg-emerald-400/20 hover:scale-110 rounded-lg transition-all border border-transparent hover:border-emerald-500/30" title="Re-approve">
                                                    <i data-lucide="play-circle" class="w-4 h-4"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-void border border-white/5 mb-4 shadow-inner">
                                <i data-lucide="shield-alert" class="w-6 h-6 text-gray-600"></i>
                            </div>
                            <p class="text-lg disabled:text-gray-400 font-serif tracking-widest uppercase">No Owners Found</p>
                            <p class="text-xs uppercase tracking-widest mt-2">Adjust your search parameters</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
