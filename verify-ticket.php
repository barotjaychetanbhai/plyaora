<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
session_start();
$logged_owner_id = $_SESSION['owner_id'] ?? null;

$booking_id = $_GET['id'] ?? '';
$token = $_GET['token'] ?? '';

$valid = false;
$error = '';
$ticket_data = null;
$already_used = false;

if (empty($booking_id) || empty($token)) {
    $error = "Invalid QR code payload.";
}
else {
    $stmt = $conn->prepare("
        SELECT b.*, u.name as player_name, t.name as turf_name, t.owner_id as turf_owner_id 
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN turfs t ON b.turf_id = t.id
        WHERE b.booking_id = ? AND b.ticket_token = ?
    ");
    $stmt->bind_param("ss", $booking_id, $token);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $ticket_data = $res->fetch_assoc();

        // Check if ticket is valid based on status
        if (in_array($ticket_data['status'], ['pending', 'confirmed'])) {
            $valid = true;

            // Ownership check: If owner is logged in, verify it's THEIR turf
            if ($logged_owner_id && $logged_owner_id != $ticket_data['turf_owner_id']) {
                $valid = false;
                $error = "This ticket belongs to another turf. Not authorized for check-in here.";
            }

            if ($ticket_data['ticket_used']) {
                $already_used = true;
                $valid = false; // It's valid but already used
            }
        }

    }
    else {
        $error = "Ticket not found or token invalid.";
    }
}

// Mark ticket as used if action is triggered (only for owner logic, but let's do it if a proper POST is made)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_used']) && $valid) {
    if (!$ticket_data['ticket_used']) {
        $update = $conn->prepare("UPDATE bookings SET ticket_used = 1 WHERE id = ?");
        $update->bind_param("i", $ticket_data['id']);
        $update->execute();
        $ticket_data['ticket_used'] = 1;
        $already_used = true;
        // It's used now, but we just used it so show a success indicator
        $valid = true;
        $just_marked_used = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Ticket - Playora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #030304; color: #f9f9fa; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white/5 border border-white/10 rounded-2xl p-8 backdrop-blur-xl relative overflow-hidden shadow-2xl">
        <?php if ($error): ?>
            <div class="text-center absolute inset-0 bg-[#030304]/90 backdrop-blur flex flex-col items-center justify-center p-6 z-10">
                <i data-lucide="x-circle" class="w-16 h-16 text-red-500 mb-4 drop-shadow-[0_0_15px_rgba(239,68,68,0.5)]"></i>
                <h2 class="text-2xl font-bold text-white mb-2 tracking-tight">Invalid Ticket</h2>
                <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php
elseif ($ticket_data): ?>
            
            <?php if (isset($just_marked_used)): ?>
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-center">
                    <p class="text-emerald-400 font-bold uppercase tracking-widest text-sm flex items-center justify-center"><i data-lucide="check-circle-2" class="w-4 h-4 mr-2"></i> Ticket Checked In</p>
                </div>
            <?php
    elseif ($already_used): ?>
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-center shadow-[0_0_20px_rgba(239,68,68,0.1)]">
                    <p class="text-red-500 font-bold uppercase tracking-widest text-sm flex items-center justify-center"><i data-lucide="alert-triangle" class="w-4 h-4 mr-2"></i> Ticket Already Used</p>
                </div>
            <?php
    elseif ($valid): ?>
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-center shadow-[0_0_20px_rgba(16,185,129,0.1)]">
                    <p class="text-emerald-400 font-bold uppercase tracking-widest text-xl flex items-center justify-center"><i data-lucide="shield-check" class="w-6 h-6 mr-2"></i> Valid Ticket</p>
                </div>
            <?php
    else: ?>
                <div class="mb-6 p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl text-center shadow-[0_0_20px_rgba(245,158,11,0.1)]">
                    <p class="text-amber-500 font-bold text-sm uppercase tracking-widest flex items-center justify-center"><i data-lucide="alert-circle" class="w-4 h-4 mr-2"></i> Ticket is <?php echo strtoupper($ticket_data['status']); ?></p>
                </div>
            <?php
    endif; ?>

            <div class="space-y-4">
                <div class="border-b border-white/5 pb-4">
                    <p class="text-[10px] text-gray-500 tracking-widest uppercase font-semibold mb-1">Booking ID</p>
                    <p class="text-2xl font-mono font-bold text-white tracking-wider">#<?php echo htmlspecialchars($ticket_data['booking_id']); ?></p>
                </div>
                
                <div class="grid grid-cols-2 gap-4 border-b border-white/5 pb-4">
                    <div>
                        <p class="text-[10px] text-gray-500 tracking-widest uppercase font-semibold mb-1">Player</p>
                        <p class="text-sm font-semibold text-white"><?php echo htmlspecialchars($ticket_data['player_name']); ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 tracking-widest uppercase font-semibold mb-1">Turf</p>
                        <p class="text-sm font-semibold text-white"><?php echo htmlspecialchars($ticket_data['turf_name']); ?></p>
                    </div>
                </div>

                <div class="border-b border-white/5 pb-4">
                    <p class="text-[10px] text-gray-500 tracking-widest uppercase font-semibold mb-1">Schedule</p>
                    <p class="text-sm font-semibold text-white"><?php echo date('M d, Y', strtotime($ticket_data['booking_date'])); ?></p>
                    <p class="text-xs text-emerald-400 font-mono mt-1 font-semibold uppercase"><?php echo htmlspecialchars($ticket_data['time_slot']); ?></p>
                </div>

                <div class="grid grid-cols-2 gap-4 pb-2">
                    <div>
                        <p class="text-[10px] text-gray-500 tracking-widest uppercase font-semibold mb-1">Payment</p>
                        <p class="text-xs uppercase font-bold tracking-widest <?php echo $ticket_data['payment_method'] === 'online' ? 'text-cyan-400' : 'text-purple-400'; ?>"><?php echo htmlspecialchars($ticket_data['payment_method']); ?></p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 tracking-widest uppercase font-semibold mb-1">Status</p>
                        <p class="text-xs uppercase font-bold tracking-widest <?php echo $ticket_data['status'] === 'confirmed' ? 'text-emerald-400' : ($ticket_data['status'] === 'pending' ? 'text-amber-400' : 'text-red-400'); ?>"><?php echo htmlspecialchars($ticket_data['status']); ?></p>
                    </div>
                </div>
            </div>

            <?php if ($valid && !$already_used && !isset($just_marked_used)): ?>
                <form method="POST" action="" class="mt-8">
                    <input type="hidden" name="mark_used" value="1">
                    <button type="submit" class="w-full bg-emerald-500 text-black font-black uppercase tracking-[0.2em] py-4 rounded-xl hover:bg-emerald-400 transition-colors shadow-[0_0_20px_rgba(16,185,129,0.2)] active:scale-95 flex items-center justify-center">
                        <i data-lucide="log-in" class="w-5 h-5 mr-2"></i> Check In Player
                    </button>
                </form>
            <?php
    endif; ?>

        <?php
endif; ?>
        
        <div class="mt-8 text-center border-t border-white/10 pt-6">
            <p class="text-[10px] text-gray-600 uppercase tracking-widest font-black">Playora Ticket System</p>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
