<?php
$owner_id = $_SESSION['owner_id'];

require_once '../includes/mail-service.php';
require_once '../includes/qr-service.php';
require_once '../emails/owner-booking-approved.php';

if (!function_exists('e')) {
    function e($val) {
        return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $booking_id = intval($_POST['booking_id'] ?? 0);
    
    // verify ownership of turf attached to booking
    $stmt = $conn->prepare("SELECT b.id FROM bookings b JOIN turfs t ON b.turf_id = t.id WHERE b.id = ? AND t.owner_id = ?");
    $stmt->bind_param("ii", $booking_id, $owner_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        if ($action === 'accept') {
            $update = $conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ?");
            $update->bind_param("i", $booking_id);
            $update->execute();
            
            // Send confirmation email to user
            $user_info_stmt = $conn->prepare("
                SELECT b.booking_id as tkt, b.ticket_token, b.booking_date, b.time_slot, b.amount, b.payment_method, u.name, u.email, t.name as turf_name 
                FROM bookings b 
                JOIN users u ON b.user_id = u.id 
                JOIN turfs t ON b.turf_id = t.id 
                WHERE b.id = ?
            ");
            $user_info_stmt->bind_param("i", $booking_id);
            $user_info_stmt->execute();
            $user_info = $user_info_stmt->get_result()->fetch_assoc();

            if ($user_info) {
                $qrUrl = null;
                if (!empty($user_info['ticket_token'])) {
                    $qrUrl = generateBookingQR($user_info['tkt'], $user_info['ticket_token']);
                }
                
                $mailHtml = getOwnerBookingApprovedEmail($user_info['name'], $user_info['turf_name'], $user_info['booking_date'], $user_info['time_slot'], $qrUrl, $user_info['tkt'], $user_info['ticket_token']);
                sendMail($user_info['email'], 'Playora Booking Confirmed - ' . $user_info['tkt'], $mailHtml);
            }

        } elseif ($action === 'reject') {
            $update = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
            $update->bind_param("i", $booking_id);
            $update->execute();
            
            // Optional: Notify user about rejection
            $user_info_stmt = $conn->prepare("
                SELECT b.booking_id as tkt, u.name, u.email, t.name as turf_name 
                FROM bookings b 
                JOIN users u ON b.user_id = u.id 
                JOIN turfs t ON b.turf_id = t.id 
                WHERE b.id = ?
            ");
            $user_info_stmt->bind_param("i", $booking_id);
            $user_info_stmt->execute();
            $user_info = $user_info_stmt->get_result()->fetch_assoc();

            if ($user_info) {
                $mailHtml = '
                <div style="text-align: center; margin-bottom: 30px;">
                    <span style="display: inline-block; padding: 8px 16px; background-color: #ef4444; color: #fff; border-radius: 8px; font-weight: bold; font-size: 14px; letter-spacing: 1px; text-transform: uppercase;">BOOKING CANCELLED</span>
                </div>
                <h2 style="color: #ffffff; font-size: 20px; margin-bottom: 10px; font-weight: 600;">Hello ' . htmlspecialchars($user_info['name']) . '</h2>
                <p style="color: #9ca3af; font-size: 15px; margin-bottom: 25px; line-height: 1.5;">We regret to inform you that your booking request for <strong>' . htmlspecialchars($user_info['turf_name']) . '</strong> could not be accepted at this time.</p>
                <div style="background-color: #1a1a1f; border-radius: 12px; padding: 20px;">
                    <p style="color: #6b7280; font-size: 13px; margin: 0;">If you have made any online payment, it will be refunded automatically within 5-7 business days.</p>
                </div>';
                sendMail($user_info['email'], 'Booking Update - ' . $user_info['tkt'], $mailHtml);
            }
        } elseif ($action === 'complete') {
            $update = $conn->prepare("UPDATE bookings SET status = 'completed' WHERE id = ?");
            $update->bind_param("i", $booking_id);
            $update->execute();
        }
    }
    echo "<script>window.location.href='index.php?page=bookings';</script>";
    exit();
}

$query = "
    SELECT b.*, t.name as turf_name, u.name as user_name, u.phone as user_phone, p.payment_status, p.owner_amount 
    FROM bookings b
    JOIN turfs t ON b.turf_id = t.id
    LEFT JOIN users u ON b.user_id = u.id
    LEFT JOIN payments p ON p.booking_id = b.id
    WHERE t.owner_id = ?
    ORDER BY b.created_at DESC
";
$fetchStmt = $conn->prepare($query);
$fetchStmt->bind_param("i", $owner_id);
$fetchStmt->execute();
$bookings = $fetchStmt->get_result();
?>
<div class="glass-card p-6 border-t-[3px] border-t-amber-500/50">
    <div class="flex justify-between items-center mb-8 border-b border-white/10 pb-4">
        <div>
            <h3 class="text-2xl font-serif text-white tracking-widest uppercase">Bookings</h3>
            <p class="text-xs text-gray-400 mt-1">Manage reservations and requests</p>
        </div>
        <div class="relative">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
            <input type="text" placeholder="Search bookings..." class="bg-void/80 border border-white/10 text-white text-sm rounded-lg pl-9 pr-4 py-2 focus:outline-none focus:border-amber-500/50 transition-colors w-64 shadow-inner placeholder-gray-600">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-white/10 text-xs uppercase tracking-widest text-gray-500">
                    <th class="px-6 py-4 font-semibold">TKT / Date</th>
                    <th class="px-6 py-4 font-semibold">User Details</th>
                    <th class="px-6 py-4 font-semibold">Turf / Slot</th>
                    <th class="px-6 py-4 font-semibold">Financials</th>
                    <th class="px-6 py-4 font-semibold text-center">Status</th>
                    <th class="px-6 py-4 font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-white/5">
                <?php if ($bookings && $bookings->num_rows > 0): ?>
                    <?php while($b = $bookings->fetch_assoc()): ?>
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="text-emerald-400 font-mono font-bold tracking-wider"><?php echo e($b['booking_id']); ?></span>
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1"><?php echo date('M d', strtotime($b['created_at'])); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-500/10 flex items-center justify-center text-amber-500 border border-amber-500/20 font-bold">
                                        <?php echo strtoupper(substr($b['user_name'], 0, 1) ?: 'U'); ?>
                                    </div>
                                    <div>
                                        <p class="text-white font-medium"><?php echo e($b['user_name'] ?: 'Unknown User'); ?></p>
                                        <p class="text-xs text-gray-400 font-mono mt-0.5"><i data-lucide="phone" class="w-3 h-3 inline mr-1 text-gray-500"></i><?php echo e($b['user_phone'] ?: 'No Phone'); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-white font-medium tracking-wide"><?php echo e($b['turf_name']); ?></p>
                                <p class="text-xs font-mono text-gray-400 mt-1"><i data-lucide="calendar" class="w-3 h-3 inline mr-1 text-cyan-400"></i> <?php echo e($b['booking_date']); ?></p>
                                <p class="text-[10px] uppercase font-bold tracking-widest text-gray-500 mt-0.5"><i data-lucide="clock" class="w-3 h-3 inline mr-1 text-purple-400"></i> <?php echo e($b['time_slot']); ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-white font-mono flex gap-4"><span class="text-gray-500 text-xs">Total:</span> <span>₹<?php echo number_format($b['amount']); ?></span></p>
                                <p class="text-emerald-400 font-mono mt-1 flex gap-4"><span class="text-emerald-500/50 text-xs uppercase tracking-widest">Net:</span> <span>+₹<?php echo number_format($b['owner_amount'] ?? ($b['amount'] * 0.9)); ?></span></p>
                                <div class="flex gap-2 mt-2">
                                    <span class="text-[9px] px-2 py-0.5 inline-block uppercase font-bold tracking-widest rounded border <?php echo ($b['payment_status'] === 'success') ? 'border-emerald-500/30 text-emerald-400 bg-emerald-500/10' : 'border-amber-500/30 text-amber-500 bg-amber-500/10'; ?>">
                                        P: <?php echo e($b['payment_status'] ?: 'pending'); ?>
                                    </span>
                                    <span class="text-[9px] px-2 py-0.5 inline-block uppercase font-bold tracking-widest rounded border <?php echo ($b['payment_method'] === 'online') ? 'border-cyan-500/30 text-cyan-400 bg-cyan-500/10' : 'border-purple-500/30 text-purple-400 bg-purple-500/10'; ?>">
                                        M: <?php echo e($b['payment_method'] ?: 'online'); ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if($b['status'] === 'confirmed'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 shadow-[0_0_10px_rgba(16,185,129,0.1)]"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Confirmed</span>
                                <?php elseif($b['status'] === 'pending'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-amber-500/10 text-amber-400 border border-amber-500/30 shadow-[0_0_10px_rgba(245,158,11,0.1)]"><span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Pending</span>
                                <?php elseif($b['status'] === 'completed'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-blue-500/10 text-blue-400 border border-blue-500/30"><span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span> Completed</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-red-500/10 text-red-400 border border-red-500/30"><span class="w-1.5 h-1.5 rounded-full bg-red-400"></span> Cancelled</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <?php if($b['status'] === 'pending'): ?>
                                        <form method="POST" action="">
                                            <input type="hidden" name="action" value="accept">
                                            <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                            <button type="submit" class="bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 p-2 rounded-lg border border-emerald-500/30 transition-colors tooltip" title="Accept Booking">
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="">
                                            <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                            <button type="submit" class="bg-red-500/20 hover:bg-red-500/30 text-red-500 p-2 rounded-lg border border-red-500/30 transition-colors tooltip" title="Reject Booking">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    <?php elseif($b['status'] === 'confirmed'): ?>
                                        <form method="POST" action="">
                                            <input type="hidden" name="action" value="complete">
                                            <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                            <button type="submit" class="bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 p-2 text-xs font-semibold px-3 uppercase tracking-widest rounded-lg border border-blue-500/30 transition-colors">
                                                Mark Completed
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-500 font-medium italic">No actions available</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <i data-lucide="calendar-x" class="w-12 h-12 mx-auto text-gray-600 mb-4 opacity-30"></i>
                            <h4 class="text-sm font-semibold tracking-widest uppercase text-gray-400 mb-1">No Bookings Found</h4>
                            <p class="text-xs text-gray-500">Wait for users to reserve your platforms.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
