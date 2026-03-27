<?php
$owner_id = $_SESSION['owner_id'];

// Total Turfs
$total_turfs = $conn->query("SELECT COUNT(*) FROM turfs WHERE owner_id = $owner_id")->fetch_row()[0] ?? 0;

// Bookings Today
$bookings_today = $conn->query("
    SELECT COUNT(*) FROM bookings b
    JOIN turfs t ON b.turf_id = t.id
    WHERE t.owner_id = $owner_id AND DATE(b.booking_date) = CURDATE()
")->fetch_row()[0] ?? 0;

// Upcoming Bookings
$upcoming_bookings = $conn->query("
    SELECT COUNT(*) FROM bookings b
    JOIN turfs t ON b.turf_id = t.id
    WHERE t.owner_id = $owner_id AND b.booking_date > CURDATE() AND b.status = 'confirmed'
")->fetch_row()[0] ?? 0;

// Total Earnings (Owner's share)
$total_earnings = $conn->query("
    SELECT COALESCE(SUM(owner_amount), 0) FROM payments p
    JOIN bookings b ON p.booking_id = b.id
    JOIN turfs t ON b.turf_id = t.id
    WHERE t.owner_id = $owner_id AND p.payment_status = 'success'
")->fetch_row()[0] ?? 0;

// Pending Booking Requests
$pending_requests = $conn->query("
    SELECT COUNT(*) FROM bookings b
    JOIN turfs t ON b.turf_id = t.id
    WHERE t.owner_id = $owner_id AND b.status = 'pending'
")->fetch_row()[0] ?? 0;
?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="glass-card p-5 border-t-[3px] border-t-emerald-500/50 flex flex-col justify-between">
        <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-2">My Turfs</p>
        <div class="flex items-end justify-between">
            <h3 class="text-4xl font-mono text-white"><?php echo $total_turfs; ?></h3>
            <i data-lucide="map-pin" class="w-8 h-8 text-emerald-500 opacity-20"></i>
        </div>
    </div>
    
    <div class="glass-card p-5 border-t-[3px] border-t-cyan-500/50 flex flex-col justify-between">
        <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-2">Bookings Today</p>
        <div class="flex items-end justify-between">
            <h3 class="text-4xl font-mono text-white"><?php echo $bookings_today; ?></h3>
            <i data-lucide="calendar" class="w-8 h-8 text-cyan-500 opacity-20"></i>
        </div>
    </div>
    
    <div class="glass-card p-5 border-t-[3px] border-t-purple-500/50 flex flex-col justify-between">
        <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-2">Upcoming</p>
        <div class="flex items-end justify-between">
            <h3 class="text-4xl font-mono text-white"><?php echo $upcoming_bookings; ?></h3>
            <i data-lucide="clock" class="w-8 h-8 text-purple-500 opacity-20"></i>
        </div>
    </div>
    
    <div class="glass-card p-5 border-t-[3px] border-t-amber-500/50 flex flex-col justify-between">
        <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-2">Pending Requests</p>
        <div class="flex items-end justify-between">
            <h3 class="text-4xl font-mono text-white"><?php echo $pending_requests; ?></h3>
            <i data-lucide="inbox" class="w-8 h-8 text-amber-500 opacity-20"></i>
        </div>
    </div>
    
    <div class="glass-card p-5 border-t-[3px] border-t-emerald-400/50 flex flex-col justify-between">
        <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-2">Net Earnings</p>
        <div class="flex items-end justify-between">
            <h3 class="text-2xl font-mono text-emerald-400">₹<?php echo number_format($total_earnings, 2); ?></h3>
            <i data-lucide="Indian-Rupee" class="w-8 h-8 text-emerald-400 opacity-20"></i>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="glass-card p-6">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-lg font-serif tracking-widest uppercase text-white">Earnings Trend</h4>
        </div>
        <div class="h-64 relative">
            <canvas id="earningsChart"></canvas>
        </div>
    </div>
    
    <div class="glass-card p-6">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-lg font-serif tracking-widest uppercase text-white">Peak Hours</h4>
        </div>
        <div class="h-64 relative">
            <canvas id="peakChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    Chart.defaults.color = 'rgba(255, 255, 255, 0.5)';
    Chart.defaults.font.family = "'JetBrains Mono', monospace";
    
    const earnCtx = document.getElementById('earningsChart').getContext('2d');
    new Chart(earnCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Earnings (₹)',
                data: [1200, 1900, 1500, 2200, 3100, 4800, 4200], // Mock
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    const peakCtx = document.getElementById('peakChart').getContext('2d');
    new Chart(peakCtx, {
        type: 'bar',
        data: {
            labels: ['6AM', '9AM', '12PM', '3PM', '6PM', '9PM'],
            datasets: [{
                label: 'Bookings',
                data: [5, 12, 18, 15, 30, 25], // Mock
                backgroundColor: 'rgba(8, 145, 178, 0.7)',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(255, 255, 255, 0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
