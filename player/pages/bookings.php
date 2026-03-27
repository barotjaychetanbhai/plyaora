<?php
require_once '../includes/qr-service.php';
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $booking_id = intval($_POST['booking_id'] ?? 0);

    // Cancel Booking
    if ($action === 'cancel') {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status IN ('pending', 'confirmed')");
        $stmt->bind_param("ii", $booking_id, $user_id);
        $stmt->execute();
        echo "<script>window.location.href='index.php?page=bookings';</script>";
        exit();
    }
}

// Fetch current user details
$user_stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$current_user = $user_stmt->get_result()->fetch_assoc();

// Fetch all bookings for player
$query = "
    SELECT b.*, t.name as turf_name, c.name as city_name, s.name as sport_name
    FROM bookings b
    JOIN turfs t ON b.turf_id = t.id
    LEFT JOIN cities c ON t.city_id = c.id
    LEFT JOIN sports s ON t.sport_id = s.id
    WHERE b.user_id = $user_id
    ORDER BY b.created_at DESC
";
$bookings = $conn->query($query);
?>

<div class="mb-10">
    <div class="flex items-center gap-3 mb-2">
        <span class="px-3 py-1 rounded-lg bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/30">
            Player History
        </span>
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
    </div>
    <h2 class="text-4xl font-black text-white tracking-tighter uppercase mb-1">Match <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-cyan-400">Inventory</span></h2>
    <p class="text-sm text-gray-400 font-medium">Your digital entry passes and reservation history</p>
</div>

<?php if ($bookings && $bookings->num_rows > 0): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-20">
        <?php while ($b = $bookings->fetch_assoc()):
        $statusStyles = [
            'pending' => ['bg' => 'bg-amber-500/5', 'border' => 'border-amber-500/20', 'text' => 'text-amber-400', 'badge' => 'bg-amber-500/10'],
            'confirmed' => ['bg' => 'bg-emerald-500/5', 'border' => 'border-emerald-500/30', 'text' => 'text-emerald-400', 'badge' => 'bg-emerald-500/10'],
            'completed' => ['bg' => 'bg-blue-500/5', 'border' => 'border-blue-500/20', 'text' => 'text-blue-400', 'badge' => 'bg-blue-500/10'],
            'cancelled' => ['bg' => 'bg-red-500/5', 'border' => 'border-red-500/20', 'text' => 'text-red-400', 'badge' => 'bg-red-500/10']
        ];
        $style = $statusStyles[$b['status']] ?? $statusStyles['pending'];
?>
            <!-- Digital Ticket Card -->
            <div class="relative group">
                <div onclick="window.location.href='index.php?page=turf-details&id=<?php echo $b['turf_id']; ?>'" class="glass-card overflow-hidden border <?php echo $style['border']; ?> flex flex-col sm:flex-row relative group-hover:shadow-[0_0_50px_rgba(16,185,129,0.03)] transition-all duration-500 cursor-pointer">
                    <div class="absolute inset-0 <?php echo $style['bg']; ?> opacity-20 pointer-events-none"></div>
                    
                    <!-- Left Section: Sport & Date -->
                    <div class="w-full sm:w-32 bg-white/5 border-b sm:border-b-0 flex flex-col items-center justify-center p-6 sm:p-4 text-center relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-void border border-white/5 flex items-center justify-center <?php echo $style['text']; ?> mb-3 shadow-inner">
                            <i data-lucide="<?php echo($b['sport_name'] === 'Cricket') ? 'award' : 'football'; ?>" class="w-6 h-6"></i>
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1"><?php echo date('M', strtotime($b['booking_date'])); ?></p>
                        <p class="text-3xl font-black text-white leading-none"><?php echo date('d', strtotime($b['booking_date'])); ?></p>
                        <p class="text-[10px] font-bold text-gray-500 mt-1"><?php echo date('Y', strtotime($b['booking_date'])); ?></p>
                    </div>

                    <!-- Perforation / Tear Line -->
                    <div class="hidden sm:block absolute left-32 top-0 bottom-0 w-px border-l-2 border-dashed border-white/10 z-20">
                        <!-- Upper Notch -->
                        <div class="absolute -top-3 -left-[11px] w-5 h-5 bg-[#030304] rounded-full border border-white/10"></div>
                        <!-- Lower Notch -->
                        <div class="absolute -bottom-3 -left-[11px] w-5 h-5 bg-[#030304] rounded-full border border-white/10"></div>
                    </div>

                    <!-- Main Section: Details -->
                    <div class="flex-1 p-6 relative z-10">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-white tracking-tight group-hover:text-emerald-400 transition-colors uppercase leading-tight"><?php echo e($b['turf_name']); ?></h3>
                                <p class="text-xs text-gray-500 font-medium flex items-center mt-1">
                                    <i data-lucide="map-pin" class="w-3 h-3 mr-1 text-cyan-500/50"></i> <?php echo e($b['city_name']); ?>
                                </p>
                            </div>
                            <div class="px-3 py-1 rounded-full <?php echo $style['badge']; ?> <?php echo $style['text']; ?> text-[9px] font-black uppercase tracking-widest border <?php echo $style['border']; ?> shadow-sm">
                                <?php echo e($b['status']); ?>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="space-y-1">
                                <p class="text-[9px] font-black text-gray-600 uppercase tracking-widest">Time Slot</p>
                                <p class="text-[11px] font-mono text-white font-bold tracking-tighter"><?php echo e($b['time_slot']); ?></p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[9px] font-black text-gray-600 uppercase tracking-widest">Booking ID</p>
                                <p class="text-[11px] font-mono <?php echo $style['text']; ?> font-black tracking-widest"><?php echo e($b['booking_id']); ?></p>
                            </div>
                        </div>

                        <!-- Price & Actions -->
                        <div class="flex items-center justify-between border-t border-white/5 pt-5">
                            <div class="flex flex-col">
                                <span class="text-[8px] font-black text-gray-700 uppercase tracking-widest">Fee Paid</span>
                                <span class="text-lg font-mono font-black text-white tracking-tighter">₹<?php echo number_format($b['amount']); ?></span>
                            </div>
                            
                            <div class="flex items-center gap-2" onclick="event.stopPropagation()">
                                <?php if ($b['status'] === 'pending' || $b['status'] === 'confirmed'): ?>
                                    <form method="POST" action="" onsubmit="return confirm('Abort this mission?');">
                                        <input type="hidden" name="action" value="cancel">
                                        <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                        <button type="submit" class="text-[9px] font-black uppercase tracking-widest text-red-500/40 hover:text-red-500 transition-colors py-2 px-3">
                                            Cancel
                                        </button>
                                    </form>
                                <?php
        endif; ?>
                                
                                <?php if ($b['status'] === 'pending' && $b['payment_method'] === 'cash'): ?>
                                    <button type="button" onclick="payExistingBooking(this, <?php echo $b['id']; ?>, <?php echo $b['turf_id']; ?>, '<?php echo $b['booking_date']; ?>', '<?php echo addslashes($b['time_slot']); ?>')" class="bg-emerald-500/10 hover:bg-emerald-500 text-emerald-500 hover:text-void border border-emerald-500/20 px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-lg active:scale-95">
                                        Pay Now
                                    </button>
                                <?php
        endif; ?>
                                
                                <?php if ($b['status'] === 'completed'): ?>
                                    <a href="index.php?page=add-review&turf_id=<?php echo $b['turf_id']; ?>" class="bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-void border border-amber-500/20 px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-lg active:scale-95">
                                        Review
                                    </a>
                                <?php
        endif; ?>
                                
                                <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-gray-500 group-hover:rotate-12 transition-transform shadow-inner">
                                    <i data-lucide="qr-code" class="w-5 h-5 opacity-40"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Section: Stub Detail -->
                    <div class="hidden sm:flex w-32 bg-void border-l border-white/5 flex-col items-center justify-center relative overflow-hidden p-4">
                        <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent opacity-20"></div>
                        <?php
        if (in_array($b['status'], ['pending', 'confirmed']) && !empty($b['ticket_token'])) {
            $qrUrl = generateBookingQR($b['booking_id'], $b['ticket_token']);
?>
                            <div class="bg-white p-1 rounded-lg shadow-[0_0_15px_rgba(255,255,255,0.2)]">
                                <img src="<?php echo htmlspecialchars($qrUrl); ?>" alt="QR" class="w-24 h-24 object-contain">
                            </div>
                            <p class="text-[8px] font-black tracking-widest text-emerald-400 mt-3 text-center uppercase">Scan for Entry</p>
                        <?php
        }
        else { ?>
                            <p class="text-[8px] font-black uppercase tracking-[0.3em] text-gray-800 rotate-90 whitespace-nowrap absolute">PLAYORA PASS</p>
                        <?php
        }?>
                    </div>
                </div>
            </div>
        <?php
    endwhile; ?>
    </div>
<?php
else: ?>
    <div class="glass-card py-32 text-center border-dashed border-white/10 bg-void/30 relative overflow-hidden group">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,rgba(16,185,129,0.08),transparent)]"></div>
        <div class="relative space-y-6">
            <div class="w-24 h-24 bg-white/5 rounded-[2.5rem] flex items-center justify-center mx-auto mb-4 border border-white/5 transition-transform group-hover:scale-110 duration-700">
                <i data-lucide="calendar-off" class="w-10 h-10 text-gray-700"></i>
            </div>
            <div>
                <h4 class="text-2xl font-bold text-white mb-2 tracking-tight">Timeline Empty</h4>
                <p class="text-gray-500 font-medium max-w-sm mx-auto text-sm leading-relaxed italic">"You haven't added any bookings yet. Ready to get out there and play?"</p>
            </div>
            <a href="index.php?page=turfs" class="inline-flex items-center bg-emerald-500 hover:bg-emerald-400 text-void py-4 px-10 rounded-2xl transition-all text-[10px] font-black uppercase tracking-widest shadow-xl shadow-emerald-500/20 active:scale-95">
                Explore Arenas <i data-lucide="chevron-right" class="w-4 h-4 ml-2"></i>
            </a>
        </div>
    </div>
<?php
endif; ?>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
async function payExistingBooking(btn, bookingId, turfId, date, timeSlotsStr) {
    const slots = timeSlotsStr.split(',').map(s => s.trim());
    
    btn.innerHTML = '<i data-lucide="loader-2" class="w-3 h-3 mr-1 inline animate-spin"></i> Wait...';
    lucide.createIcons();

    const bookingData = {
        user_id: <?php echo $user_id; ?>,
        turf_id: turfId,
        booking_date: date,
        time_slots: slots,
        existing_booking_id: bookingId,
        payment_method: 'online'
    };

    try {
        const response = await fetch('../payment/create-order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(bookingData)
        });
        
        const orderRes = await response.json();
        if (orderRes.error) throw new Error(orderRes.error);

        const options = {
            key: 'rzp_test_SPAX3rLNKC7AsO',
            amount: orderRes.amount,
            currency: orderRes.currency,
            name: "Playora",
            description: "Pay for Booking",
            order_id: orderRes.order_id,
            handler: async function (response) {
                const verifyData = {
                    ...bookingData,
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_signature: response.razorpay_signature
                };

                const verifyRes = await fetch('../payment/verify-payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(verifyData)
                });

                const result = await verifyRes.json();
                if (result.success) {
                    window.location.href = '../payment/success.php?id=' + result.booking_id + '&nid=' + result.new_id;
                } else {
                    alert('Payment verification failed: ' + result.error);
                    location.reload();
                }
            },
            prefill: {
                name: "<?php echo addslashes($current_user['name'] ?? ''); ?>",
                email: "<?php echo addslashes($current_user['email'] ?? ''); ?>",
                contact: "<?php echo addslashes($current_user['phone'] ?? ''); ?>"
            },
            theme: { color: "#10b981" },
            modal: {
                ondismiss: function() {
                    location.reload();
                }
            }
        };

        const rzp = new Razorpay(options);
        rzp.on('payment.failed', function (res){
            alert('Payment failed: ' + res.error.description);
            location.reload();
        });
        rzp.open();

    } catch (error) {
        alert('Error: ' + error.message);
        location.reload();
    }
}
</script>
