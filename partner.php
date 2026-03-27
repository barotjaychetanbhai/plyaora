<?php
session_start();
if (isset($_SESSION['role']) && $_SESSION['role'] === 'owner') {
    header("Location: owner/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become a Partner | Playora for Owners</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root { --void: #030304; --emerald: #10b981; }
        body { font-family: 'Inter', sans-serif; background: var(--void); color: #fff; scroll-behavior: smooth; }
        .font-display { font-family: 'Playfair Display', serif; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .grad-text { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>
</head>
<body class="antialiased">
    <!-- Hero Section -->
    <section class="min-h-screen relative flex items-center justify-center overflow-hidden px-4">
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_50%_-20%,rgba(16,185,129,0.1),transparent_50%)]"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-emerald-500/5 rounded-full blur-[120px]"></div>

        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-16 items-center relative z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold uppercase tracking-widest mb-8">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Partner with Playora
                </div>
                <h1 class="font-display text-6xl lg:text-8xl font-bold mb-8 leading-[1.1]">
                    Manage Your <span class="grad-text">Turf</span> Like a Pro
                </h1>
                <p class="text-gray-400 text-lg mb-10 leading-relaxed max-w-xl">
                    Join India's fastest growing sports community. List your facility, manage bookings, track analytics, and grow your revenue with advanced management tools.
                </p>
                <div class="flex flex-wrap gap-6">
                    <a href="auth/register.php?role=owner" class="px-10 py-5 bg-emerald-500 text-black font-bold rounded-2xl hover:bg-emerald-400 transition-all active:scale-95 shadow-[0_15px_40px_-10px_rgba(16,185,129,0.3)]">
                        Register as Owner
                    </a>
                    <a href="auth/login.php?role=owner" class="px-10 py-5 glass text-white font-bold rounded-2xl hover:bg-white/10 transition-all border-white/10">
                        Login to Dashboard
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="glass p-8 rounded-[2.5rem] mt-12 hover:border-emerald-500/30 transition-all group">
                    <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-500/20 transition-all">
                        <i data-lucide="bar-chart-3" class="text-emerald-400"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Live Analytics</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Track your daily income, footfall, and peak hours in real-time.</p>
                </div>
                <div class="glass p-8 rounded-[2.5rem] hover:border-emerald-500/30 transition-all group">
                    <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-500/20 transition-all">
                        <i data-lucide="calendar-check" class="text-emerald-400"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Easy Booking</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Manage online and offline bookings in one unified interface.</p>
                </div>
                <div class="glass p-8 rounded-[2.5rem] mt-6 hover:border-emerald-500/30 transition-all group">
                    <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-500/20 transition-all">
                        <i data-lucide="zap" class="text-emerald-400"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Growth Tools</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Create discount coupons and seasonal offers to boost demand.</p>
                </div>
                <div class="glass p-8 rounded-[2.5rem] -mt-6 hover:border-emerald-500/30 transition-all group">
                    <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-500/20 transition-all">
                        <i data-lucide="shield-check" class="text-emerald-400"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Secure Payments</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Zero-hassle automated daily settlements to your bank.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Earnings Section -->
    <section class="py-32 px-4 bg-white/[0.01]">
        <div class="max-w-4xl mx-auto glass p-16 rounded-[3rem] text-center border-emerald-500/10 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-emerald-500/30 to-transparent"></div>
            <h2 class="font-display text-4xl font-bold mb-6">How much can you earn?</h2>
            <p class="text-gray-500 text-lg mb-12">Average Playora partners increase their monthly venue utilize by <span class="text-emerald-400 font-bold">40%</span></p>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                   <div class="text-4xl font-bold text-white mb-2">₹1.2L+</div>
                   <div class="text-gray-500 text-xs uppercase tracking-widest font-semibold">Avg. Revenue</div>
                </div>
                <div class="border-x border-white/5">
                   <div class="text-4xl font-bold text-white mb-2">500+</div>
                   <div class="text-gray-500 text-xs uppercase tracking-widest font-semibold">Active Players</div>
                </div>
                <div>
                   <div class="text-4xl font-bold text-white mb-2">15%</div>
                   <div class="text-gray-500 text-xs uppercase tracking-widest font-semibold">Revenue Growth</div>
                </div>
            </div>
            
            <a href="auth/register.php?role=owner" class="inline-block mt-16 px-12 py-5 bg-white text-black font-bold rounded-2xl hover:bg-gray-200 transition-all">
                List My Facility Now
            </a>
        </div>
    </section>

    <footer class="py-12 border-t border-white/5 text-center text-gray-600 text-sm">
        &copy; 2026 Playora Sports Technology. Built for the future of sports.
    </footer>

    <script>lucide.createIcons();</script>
</body>
</html>
