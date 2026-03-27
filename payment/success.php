<?php
require_once '../player/includes/db.php';
session_start();

$booking_id = $_GET['id'] ?? '';
$new_id = $_GET['nid'] ?? 0;
$status = $_GET['status'] ?? 'success'; // 'success' or 'pending'

if (!$new_id) {
    header('Location: ../player/index.php');
    exit;
}

$stmt = $conn->prepare("
    SELECT b.*, t.name as turf_name 
    FROM bookings b
    JOIN turfs t ON b.turf_id = t.id
    WHERE b.id = ?
");
$stmt->bind_param("i", $new_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    header('Location: ../player/index.php');
    exit;
}

$is_pending = ($status === 'pending');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_pending ? 'Request Sent' : 'Booking Confirmed'; ?> | Playora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        body { background-color: #030304; color: #f9f9fa; font-family: 'Inter', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
        .glass-card { background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.07); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] from-emerald-900/20 via-void to-void">
    <div class="max-w-md w-full relative">
        <!-- Glow Effects -->
        <div class="absolute -top-32 -left-32 w-80 h-80 bg-<?php echo $is_pending ? 'amber' : 'emerald'; ?>-500/10 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-teal-500/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="glass-card p-10 rounded-[2.5rem] relative overflow-hidden text-center">
            <!-- Icon -->
            <div class="w-20 h-20 bg-<?php echo $is_pending ? 'amber' : 'emerald'; ?>-500 rounded-3xl flex items-center justify-center shadow-[0_0_40px_rgba(<?php echo $is_pending ? '245,158,11' : '16,185,129'; ?>,0.4)] mx-auto mb-8 rotate-3 scale-110">
                <i data-lucide="<?php echo $is_pending ? 'clock' : 'check-circle-2'; ?>" class="w-10 h-10 text-black"></i>
            </div>

            <h1 class="font-serif text-4xl font-black text-white tracking-tight mb-2"><?php echo $is_pending ? 'Request Sent' : 'Booking Confirmed'; ?></h1>
            <p class="text-<?php echo $is_pending ? 'amber' : 'emerald'; ?>-400 font-mono text-[10px] uppercase tracking-[0.3em] font-black mb-8"><?php echo $is_pending ? 'Waiting for Approval' : 'Payment Successful'; ?></p>

            <div class="space-y-4 mb-10 text-left">
                <div class="bg-white/5 border border-white/5 rounded-2xl p-4">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Booking ID</span>
                        <span class="text-xs font-mono font-bold text-white">#<?php echo $booking['booking_id']; ?></span>
                    </div>
                </div>
                <div class="bg-white/5 border border-white/5 rounded-2xl p-5 space-y-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">Turf Name</p>
                            <p class="text-sm font-bold text-white"><?php echo htmlspecialchars($booking['turf_name']); ?></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-3 border-t border-white/5">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">Date</p>
                            <p class="text-xs font-semibold text-gray-300"><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">Time Slot</p>
                            <p class="text-xs font-semibold text-gray-300"><?php echo $booking['time_slot']; ?></p>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-white/5">
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1"><?php echo $is_pending ? 'Amount to Pay' : 'Amount Paid'; ?></p>
                        <p class="text-xl font-black text-<?php echo $is_pending ? 'amber' : 'emerald'; ?>-400 font-mono">₹<?php echo number_format($booking['amount']); ?></p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <a href="../player/index.php?page=bookings" class="block w-full bg-gradient-to-r from-<?php echo $is_pending ? 'amber-600 to-orange-500' : 'emerald-600 to-teal-500'; ?> text-white font-black py-5 rounded-2xl text-[11px] uppercase tracking-[0.25em] hover:shadow-[0_0_30px_rgba(<?php echo $is_pending ? '245,158,11' : '16,185,129'; ?>,0.35)] transition-all duration-300 active:scale-[0.98]">
                    View My Bookings
                </a>
                <a href="../player/index.php" class="block w-full border border-white/10 hover:bg-white/5 text-gray-400 font-bold py-4 rounded-2xl text-[10px] uppercase tracking-widest transition-all">
                    Return to Home
                </a>
            </div>

            <p class="mt-8 text-[10px] text-gray-600 font-medium"><?php echo $is_pending ? 'We will notify you once the owner approves your request.' : 'A confirmation email has been sent to your registered email address.'; ?></p>
        </div>
    </div>

    <script>
        lucide.createIcons();
        window.addEventListener('DOMContentLoaded', () => {
            if (!<?php echo $is_pending ? 'true' : 'false'; ?>) {
                confetti({
                    particleCount: 150,
                    spread: 70,
                    origin: { y: 0.6 },
                    colors: ['#10b981', '#14b8a6', '#f59e0b', '#ffffff']
                });
            }
        });
    </script>
</body>
</html>
