<?php
$turf_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];
$date = $_GET['date'] ?? date('Y-m-d');

$stmt = $conn->prepare("
    SELECT t.*, c.name as city_name, s.name as sport_name, o.name as owner_name 
    FROM turfs t
    LEFT JOIN cities c ON t.city_id = c.id
    LEFT JOIN sports s ON t.sport_id = s.id
    LEFT JOIN owners o ON t.owner_id = o.id
    WHERE t.id = ? AND t.status = 'active'
");
$stmt->bind_param("i", $turf_id);
$stmt->execute();
$turf = $stmt->get_result()->fetch_assoc();

if (!$turf) {
    echo "<script>window.location.href='index.php?page=turfs';</script>";
    exit();
}

// Fetch user info for pre-fill
$user_stmt = $conn->prepare("SELECT name, email, phone FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$current_user = $user_stmt->get_result()->fetch_assoc();

// Handle favorite toggle (keep if needed, otherwise this block can be cleaned up)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_favorite') {
    $fid = $_POST['turf_id'];
    $check_fav = $conn->query("SELECT id FROM favorite_turfs WHERE user_id = $user_id AND turf_id = $fid");
    if ($check_fav->num_rows > 0) {
        $conn->query("DELETE FROM favorite_turfs WHERE user_id = $user_id AND turf_id = $fid");
    } else {
        $conn->query("INSERT INTO favorite_turfs (user_id, turf_id) VALUES ($user_id, $fid)");
    }
}

// slots
$slots_query = "SELECT * FROM turf_slots WHERE turf_id = $turf_id ORDER BY id ASC";
$all_slots = $conn->query($slots_query);

// booked slots for selected date (including recently initiated online payments - 15 min lock)
$booked_stmt = $conn->prepare("
    SELECT time_slot FROM bookings 
    WHERE turf_id = ? 
    AND booking_date = ? 
    AND (
        status = 'confirmed' 
        OR (status = 'pending' AND payment_method = 'cash')
        OR (status = 'pending' AND payment_method = 'online' AND created_at > NOW() - INTERVAL 15 MINUTE)
    )
");
$booked_stmt->bind_param("is", $turf_id, $date);
$booked_stmt->execute();
$booked_res = $booked_stmt->get_result();
$booked_slots = [];
while($bs = $booked_res->fetch_assoc()) {
    // Handling possible comma separated slots if any (though usually single slot here)
    $stmt_slots = explode(', ', $bs['time_slot']);
    foreach($stmt_slots as $s) $booked_slots[] = trim($s);
}
$booked_slots = array_unique($booked_slots);

// Reviews
$rev_stmt = $conn->query("SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.turf_id = $turf_id AND r.status='visible' ORDER BY r.created_at DESC");

// Amenities formatting
$amenities = array_filter(array_map('trim', explode(',', $turf['amenities'] ?? '')));

// Check if favorite
$is_fav = $conn->query("SELECT id FROM favorite_turfs WHERE user_id = $user_id AND turf_id = $turf_id")->num_rows > 0;
?>
<div class="mb-4">
    <a href="index.php?page=turfs" class="text-[10px] text-emerald-400 hover:text-emerald-300 font-bold tracking-widest uppercase transition-colors inline-flex items-center"><i data-lucide="arrow-left" class="w-3 h-3 mr-1"></i> Back to discovery</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 relative items-start">
    <div class="lg:col-span-8 space-y-8">
        <!-- Gallery -->
        <div class="glass-card overflow-hidden rounded-3xl border border-white/10 p-2">
            <div class="h-64 sm:h-96 w-full rounded-2xl overflow-hidden relative group">
                <img src="https://images.unsplash.com/photo-1579952363873-27f3bade9f55?auto=format&fit=crop&q=80&w=1200" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" alt="Turf Cover">
                <div class="absolute top-4 right-4 flex items-center gap-3">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="toggle_favorite">
                        <input type="hidden" name="turf_id" value="<?php echo $turf_id; ?>">
                        <button type="submit" class="w-11 h-11 rounded-full bg-void/80 backdrop-blur-md border border-white/10 flex items-center justify-center transition-all hover:bg-white/10 active:scale-95 group/heart shadow-2xl">
                            <i data-lucide="heart" class="w-5 h-5 <?php echo $is_fav ? 'fill-red-500 text-red-500' : 'text-white/60 group-hover/heart:text-red-400'; ?> transition-colors"></i>
                        </button>
                    </form>
                    <div class="bg-void/80 backdrop-blur-md px-4 py-2 rounded-full border border-white/10 flex items-center gap-2 shadow-xl">
                        <i data-lucide="star" class="w-4 h-4 fill-amber-400 text-amber-400"></i>
                        <span class="text-sm font-mono font-bold text-white"><?php echo number_format($turf['rating'], 1); ?></span>
                    </div>
                </div>
                <div class="absolute bottom-6 left-6 right-6">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-[10px] uppercase tracking-widest font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2.5 py-1 rounded">Active Ground</span>
                    </div>
                    <h1 class="text-4xl sm:text-5xl font-bold font-serif tracking-wide text-white drop-shadow-lg leading-tight"><?php echo e($turf['name']); ?></h1>
                </div>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-void/40 border border-white/5 rounded-2xl p-4 text-center">
                <i data-lucide="medal" class="w-6 h-6 mx-auto text-emerald-400 mb-2"></i>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Sport</p>
                <p class="text-xs font-bold text-white tracking-wide"><?php echo e($turf['sport_name']); ?></p>
            </div>
            <div class="bg-void/40 border border-white/5 rounded-2xl p-4 text-center">
                <i data-lucide="map-pin" class="w-6 h-6 mx-auto text-cyan-400 mb-2"></i>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">City</p>
                <p class="text-xs font-bold text-white tracking-wide"><?php echo e($turf['city_name']); ?></p>
            </div>
            <div class="bg-void/40 border border-white/5 rounded-2xl p-4 text-center">
                <i data-lucide="tag" class="w-6 h-6 mx-auto text-purple-400 mb-2"></i>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Price/Hr</p>
                <p class="text-xs font-bold font-mono text-emerald-300">₹<?php echo number_format($turf['price']); ?></p>
            </div>
            <div class="bg-void/40 border border-white/5 rounded-2xl p-4 text-center">
                <i data-lucide="user" class="w-6 h-6 mx-auto text-amber-400 mb-2"></i>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Manager</p>
                <p class="text-xs font-bold text-white tracking-wide"><?php echo explode(' ', e($turf['owner_name']))[0]; ?></p>
            </div>
        </div>

        <!-- Description & Amenities -->
        <div class="space-y-6">
            <div>
                <h3 class="text-sm font-semibold text-white tracking-wider uppercase mb-3"><i data-lucide="align-left" class="w-4 h-4 mr-2 inline text-gray-400"></i> About Turf</h3>
                <p class="text-sm text-gray-400 leading-relaxed font-light"><?php echo nl2br(e($turf['description'] ?? 'No description provided for this turf yet.')); ?></p>
            </div>
            
            <?php if (!empty($amenities)): ?>
            <div>
                <h3 class="text-sm font-semibold text-white tracking-wider uppercase mb-3"><i data-lucide="layout-grid" class="w-4 h-4 mr-2 inline text-gray-400"></i> Amenities</h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach($amenities as $amn): ?>
                        <span class="inline-flex items-center bg-white/5 border border-white/10 text-gray-300 text-xs px-3 py-1.5 rounded-lg tracking-wide"><i data-lucide="check" class="w-3 h-3 mr-1.5 text-emerald-500"></i> <?php echo e($amn); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Reviews -->
        <div>
            <h3 class="text-sm font-semibold text-white tracking-wider uppercase mb-4"><i data-lucide="message-square" class="w-4 h-4 mr-2 inline text-gray-400"></i> Player Reviews</h3>
            <?php if ($rev_stmt && $rev_stmt->num_rows > 0): ?>
                <div class="space-y-4">
                    <?php while($rev = $rev_stmt->fetch_assoc()): ?>
                        <div class="bg-void/40 border border-white/5 rounded-2xl p-5">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-500/10 text-amber-500 border border-amber-500/20 flex items-center justify-center font-bold text-xs"><?php echo strtoupper(substr($rev['user_name'], 0, 1)); ?></div>
                                    <div>
                                        <p class="text-xs font-bold text-white tracking-wide"><?php echo e($rev['user_name']); ?></p>
                                        <p class="text-[10px] text-gray-500 font-mono tracking-widest mt-0.5"><?php echo date('M d, Y', strtotime($rev['created_at'])); ?></p>
                                    </div>
                                </div>
                                <div class="flex gap-0.5">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <i data-lucide="star" class="w-3 h-3 <?php echo $i <= $rev['rating'] ? 'fill-amber-400 text-amber-400' : 'text-white/20'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="text-sm text-gray-400 mt-3 font-light leading-relaxed">"<?php echo e($rev['review']); ?>"</p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-xs text-gray-500 italic bg-void/20 p-4 rounded-xl border border-white/5 text-center">No reviews yet. Be the first to play and review!</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Booking Panel (Sticky) -->
    <div class="lg:col-span-4 lg:sticky lg:top-24">
        <div class="glass-card p-6 border-t-[3px] border-t-emerald-500/50 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-[40px] pointer-events-none"></div>
            <h3 class="text-lg font-serif tracking-widest text-white uppercase mb-1">Book Match</h3>
            <p class="text-xs text-gray-400 mb-6 font-mono border-b border-white/10 pb-4">₹<?php echo number_format($turf['price']); ?> <span class="text-[10px] text-gray-500">PER HOUR</span></p>

            <form method="GET" action="index.php" class="mb-6">
                <input type="hidden" name="page" value="turf-details">
                <input type="hidden" name="id" value="<?php echo $turf_id; ?>">
                <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-2">Select Date</label>
                <input type="date" name="date" value="<?php echo e($date); ?>" min="<?php echo date('Y-m-d'); ?>" onchange="this.form.submit()" class="w-full bg-void/80 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 transition-all font-mono shadow-inner [color-scheme:dark] text-sm cursor-pointer hover:bg-white/5">
            </form>

            <div>
                <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-3">Available Slots</label>
                <?php if ($all_slots && $all_slots->num_rows > 0): ?>
                    <form id="bookingForm" class="space-y-6">
                        <input type="hidden" id="turf_id" value="<?php echo $turf_id; ?>">
                        <input type="hidden" id="user_id" value="<?php echo $user_id; ?>">
                        <input type="hidden" id="booking_date" value="<?php echo $date; ?>">
                        <input type="hidden" id="turf_price" value="<?php echo $turf['price']; ?>">
                        
                        <div class="grid grid-cols-2 gap-2 mb-6 max-h-[300px] overflow-y-auto pr-1 hide-scroll" id="slotsGrid">
                            <?php 
                            $has_available = false;
                            while($slot = $all_slots->fetch_assoc()): 
                                $is_booked = in_array($slot['slot_time'], $booked_slots);
                                $is_blocked = ($slot['status'] === 'blocked');
                                $is_disabled = $is_booked || $is_blocked || (strtotime($date . ' ' . explode(' - ', $slot['slot_time'])[0]) < time());
                                
                                if (!$is_disabled) $has_available = true;
                                
                                $bg_class = $is_disabled ? 'bg-void/50 opacity-50 cursor-not-allowed border-white/5 text-gray-500' : 'bg-void/80 border-white/10 text-white hover:border-emerald-500 hover:bg-emerald-500/10 cursor-pointer transition-colors peer-checked:bg-emerald-500/20 peer-checked:border-emerald-500 peer-checked:text-emerald-400';
                            ?>
                                <label class="relative block">
                                    <input type="checkbox" name="time_slots[]" value="<?php echo $slot['slot_time']; ?>" class="peer sr-only slot-checkbox" <?php echo $is_disabled ? 'disabled' : ''; ?> onchange="updateSummary()">
                                    <div class="border rounded-xl px-2 py-3 text-center <?php echo $bg_class; ?> group">
                                        <p class="text-[11px] font-mono font-bold tracking-wider"><?php echo explode(' - ', $slot['slot_time'])[0]; ?></p>
                                        <p class="text-[8px] uppercase tracking-widest mt-0.5 font-bold opacity-80">
                                            <?php 
                                            if ($is_booked) echo 'Booked';
                                            elseif ($is_blocked) echo 'Blocked';
                                            elseif ($is_disabled) echo 'Passed';
                                            else echo 'Available';
                                            ?>
                                        </p>
                                    </div>
                                    <?php if(!$is_disabled): ?>
                                        <i data-lucide="check-circle-2" class="w-4 h-4 absolute top-1.5 right-1.5 text-emerald-500 opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                    <?php endif; ?>
                                </label>
                            <?php endwhile; ?>
                        </div>

                        <!-- Booking Summary -->
                        <div id="bookingSummary" class="hidden bg-white/5 border border-white/10 rounded-2xl p-4 space-y-3 animate-in fade-in slide-in-from-bottom-2 duration-300">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Selected Slots</span>
                                <span id="selectedCount" class="text-xs font-bold text-white">0</span>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t border-white/5">
                                <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Total Amount</span>
                                <span id="totalDisplay" class="text-lg font-black text-emerald-400 font-mono">₹0</span>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="space-y-3">
                            <label class="block text-[10px] font-semibold text-gray-500 uppercase tracking-widest">Payment Method</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative block cursor-pointer group">
                                    <input type="radio" name="payment_method" value="online" class="peer sr-only" checked>
                                    <div class="border border-white/10 bg-void/80 rounded-xl p-3 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 group-hover:bg-white/5">
                                        <i data-lucide="credit-card" class="w-4 h-4 mx-auto mb-1 text-gray-500 peer-checked:text-emerald-400"></i>
                                        <p class="text-[10px] font-bold text-gray-400 peer-checked:text-emerald-400 uppercase tracking-wider">Online</p>
                                    </div>
                                    <i data-lucide="check" class="w-3 h-3 absolute top-2 right-2 text-emerald-500 opacity-0 peer-checked:opacity-100"></i>
                                </label>
                                <label class="relative block cursor-pointer group">
                                    <input type="radio" name="payment_method" value="cash" class="peer sr-only">
                                    <div class="border border-white/10 bg-void/80 rounded-xl p-3 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 group-hover:bg-white/5">
                                        <i data-lucide="banknote" class="w-4 h-4 mx-auto mb-1 text-gray-500 peer-checked:text-emerald-400"></i>
                                        <p class="text-[10px] font-bold text-gray-400 peer-checked:text-emerald-400 uppercase tracking-wider">Cash</p>
                                    </div>
                                    <i data-lucide="check" class="w-3 h-3 absolute top-2 right-2 text-emerald-500 opacity-0 peer-checked:opacity-100"></i>
                                </label>
                            </div>
                        </div>
                        
                        <?php if($has_available): ?>
                            <button type="button" onclick="handleBooking()" id="bookBtn" class="w-full bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-semibold rounded-2xl px-4 py-5 hover:shadow-[0_0_20px_rgba(16,185,129,0.3)] transition-all active:scale-95 text-[11px] uppercase tracking-[0.2em] font-black flex items-center justify-center">
                                Proceed to Booking <i data-lucide="arrow-right" class="w-4 h-4 ml-2"></i>
                            </button>
                        <?php else: ?>
                            <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 text-center">
                                <p class="text-xs text-red-500 font-semibold uppercase tracking-widest">No slots available</p>
                                <p class="text-[10px] text-gray-500 mt-1">Please select another date</p>
                            </div>
                        <?php endif; ?>
                    </form>

                    <!-- Booking & Razorpay Integration -->
                    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
                    <script>
                    function updateSummary() {
                        const checkboxes = document.querySelectorAll('.slot-checkbox:checked');
                        const summary = document.getElementById('bookingSummary');
                        const countDisp = document.getElementById('selectedCount');
                        const totalDisp = document.getElementById('totalDisplay');
                        const price = parseFloat(document.getElementById('turf_price').value);

                        if (checkboxes.length > 0) {
                            summary.classList.remove('hidden');
                            countDisp.textContent = checkboxes.length;
                            totalDisp.textContent = '₹' + (price * checkboxes.length).toLocaleString();
                        } else {
                            summary.classList.add('hidden');
                        }
                    }

                    async function handleBooking() {
                        const selectedSlots = Array.from(document.querySelectorAll('.slot-checkbox:checked')).map(cb => cb.value);
                        if (selectedSlots.length === 0) {
                            alert('Please select at least one time slot.');
                            return;
                        }

                        const method = document.querySelector('input[name="payment_method"]:checked').value;
                        if (method === 'online') {
                            payAndBook(selectedSlots);
                        } else {
                            requestCashBooking(selectedSlots);
                        }
                    }

                    async function payAndBook(slots) {
                        const bookBtn = document.getElementById('bookBtn');
                        const originalBtnContent = bookBtn.innerHTML;
                        bookBtn.disabled = true;
                        bookBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i> Initializing Secure Payment...';
                        lucide.createIcons();

                        const bookingData = {
                            user_id: document.getElementById('user_id').value,
                            turf_id: document.getElementById('turf_id').value,
                            booking_date: document.getElementById('booking_date').value,
                            time_slots: slots,
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
                                description: "Booking for " + orderRes.turf_name + " (" + slots.length + " slots)",
                                order_id: orderRes.order_id,
                                handler: async function (response) {
                                    const verifyData = {
                                        ...bookingData,
                                        existing_booking_id: orderRes.new_booking_id,
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
                                        bookBtn.disabled = false;
                                        bookBtn.innerHTML = originalBtnContent;
                                        lucide.createIcons();
                                    }
                                },
                                prefill: {
                                    name: "<?php echo addslashes($current_user['name'] ?? ''); ?>",
                                    email: "<?php echo addslashes($current_user['email'] ?? ''); ?>",
                                    contact: "<?php echo addslashes($current_user['phone'] ?? ''); ?>"
                                },
                                theme: { color: "#10b981" }
                            };

                            const rzp = new Razorpay(options);
                            rzp.on('payment.failed', function (res){
                                alert('Payment failed: ' + res.error.description);
                                bookBtn.disabled = false;
                                bookBtn.innerHTML = originalBtnContent;
                                lucide.createIcons();
                            });
                            rzp.open();

                        } catch (error) {
                            alert('Error: ' + error.message);
                            bookBtn.disabled = false;
                            bookBtn.innerHTML = originalBtnContent;
                            lucide.createIcons();
                        }
                    }

                    async function requestCashBooking(slots) {
                        const bookBtn = document.getElementById('bookBtn');
                        const originalBtnContent = bookBtn.innerHTML;
                        bookBtn.disabled = true;
                        bookBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin"></i> Submitting Request...';
                        lucide.createIcons();

                        const bookingData = {
                            user_id: document.getElementById('user_id').value,
                            turf_id: document.getElementById('turf_id').value,
                            booking_date: document.getElementById('booking_date').value,
                            time_slots: slots,
                            payment_method: 'cash'
                        };

                        try {
                            const response = await fetch('../payment/process-cash.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify(bookingData)
                            });
                            
                            const result = await response.json();
                            if (result.success) {
                                window.location.href = '../payment/success.php?status=pending&id=' + result.booking_id + '&nid=' + result.new_id;
                            } else {
                                throw new Error(result.error || 'Failed to submit request');
                            }
                        } catch (error) {
                            alert('Error: ' + error.message);
                            bookBtn.disabled = false;
                            bookBtn.innerHTML = originalBtnContent;
                            lucide.createIcons();
                        }
                    }
                    </script>
                <?php else: ?>
                    <div class="bg-void/40 border border-dashed border-white/10 rounded-xl p-6 text-center">
                        <i data-lucide="calendar-off" class="w-8 h-8 mx-auto text-gray-600 mb-2 opacity-50"></i>
                        <p class="text-xs text-gray-500 uppercase tracking-widest">Timings not configured</p>
                    </div>
                <?php endif; ?>


            </div>
        </div>
    </div>
</div>
