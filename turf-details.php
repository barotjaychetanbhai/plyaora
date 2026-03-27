<?php
session_start();
require_once 'player/includes/db.php';

// Get Turf ID from URL
$turf_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($turf_id <= 0) {
    header("Location: index.php");
    exit();
}

// Fetch Turf Details with Sport and City names
$query = $conn->prepare("
    SELECT t.*, s.name as sport_name, s.icon as sport_icon, c.name as city_name 
    FROM turfs t 
    JOIN sports s ON t.sport_id = s.id 
    JOIN cities c ON t.city_id = c.id 
    WHERE t.id = ? AND t.status = 'active'
");
$query->bind_param("i", $turf_id);
$query->execute();
$result = $query->get_result();
$turf = $result->fetch_assoc();

if (!$turf) {
    header("Location: index.php");
    exit();
}

// Prepare Images and Amenities
$images = !empty($turf['images']) ? json_decode($turf['images'], true) : [];
if (empty($images) && !empty($turf['images'])) {
    $images = explode(',', $turf['images']); // Fallback to comma separated
}
$amenities = !empty($turf['amenities']) ? json_decode($turf['amenities'], true) : [];
if (empty($amenities) && !empty($turf['amenities'])) {
    $amenities = explode(',', $turf['amenities']); // Fallback to comma separated
}

// Fetch Related Turfs (Same city or same sport)
$related_query = $conn->prepare("
    SELECT t.*, s.name as sport_name, s.icon as sport_icon, c.name as city_name 
    FROM turfs t 
    JOIN sports s ON t.sport_id = s.id 
    JOIN cities c ON t.city_id = c.id 
    WHERE (t.city_id = ? OR t.sport_id = ?) AND t.id != ? AND t.status = 'active'
    LIMIT 3
");
$related_query->bind_param("iii", $turf['city_id'], $turf['sport_id'], $turf_id);
$related_query->execute();
$related_turfs = $related_query->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle redirection for booking
if (isset($_GET['confirm_booking'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_url'] = "turf-details.php?id=" . $turf_id;
        header("Location: auth/login.php");
        exit();
    } else {
        header("Location: player/index.php?page=book&id=" . $turf_id);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($turf['name']) ?> — Playora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        void: '#030304',
                        emerald: { DEFAULT: '#10b981', 400: '#34d399', 500: '#10b981', 600: '#059669' },
                        muted: 'var(--text-muted)',
                    },
                    fontFamily: {
                        display: ['Playfair Display', 'serif'],
                        body: ['Inter', 'sans-serif'],
                    },
                    textColor: {
                        DEFAULT: 'var(--text)',
                        muted: 'var(--text-muted)',
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --emerald: #10b981;
            --void: #030304;

            /* Theme Variables - Default Dark */
            --bg: var(--void);
            --text: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.6);
            --glass-bg: rgba(255, 255, 255, 0.04);
            --glass-border: rgba(255, 255, 255, 0.07);
            --nav-bg: rgba(3, 3, 4, 0.88);
            --card-bg: rgba(255, 255, 255, 0.03);
            --input-bg: rgba(255, 255, 255, 0.05);
            --border-muted: rgba(255, 255, 255, 0.06);
        }

        .light-mode {
            --bg: #f8fafc;
            --text: #0f172a;
            --text-muted: #475569;
            --glass-bg: #ffffff;
            --glass-border: #cbd5e1;
            --nav-bg: rgba(255, 255, 255, 0.98);
            --card-bg: #ffffff;
            --input-bg: #f1f5f9;
            --border-muted: #cbd5e1;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            transition: background 0.3s, color 0.3s;
        }

        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            color: var(--text);
        }

        .turf-gradient {
            background: linear-gradient(to bottom, rgba(16, 185, 129, 0.05), transparent);
        }

        .amenity-chip {
            background: var(--input-bg);
            border: 1px solid var(--border-muted);
            transition: all 0.3s ease;
            color: var(--text);
        }

        .amenity-chip:hover {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
        }
        
        .slot-btn {
            background: var(--input-bg);
            border: 1px solid var(--border-muted);
            transition: all 0.2s ease;
            color: var(--text);
        }
        
        .slot-btn:hover:not(.disabled) {
            background: rgba(16, 185, 129, 0.1);
            border-color: var(--emerald);
        }
        
        .slot-btn.active {
            background: var(--emerald);
            color: white;
            border-color: var(--emerald);
        }
        
        .slot-btn.disabled {
            opacity: 0.3;
            cursor: not-allowed;
            text-decoration: line-through;
        }

        /* Gallery Styles */
        .gallery-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            grid-template-rows: 400px;
            gap: 12px;
        }
        
        @media (max-width: 768px) {
            .gallery-grid {
                grid-template-columns: 1fr;
                grid-template-rows: 250px;
            }
            .gallery-side { display: none; }
        }

        .gallery-main {
            border-radius: 24px;
            overflow: hidden;
            position: relative;
            background: var(--card-bg);
            border: 1px solid var(--border-muted);
        }

        .gallery-side {
            display: grid;
            grid-template-rows: 1fr 1fr;
            gap: 12px;
        }

        .gallery-item {
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            background: var(--card-bg);
            border: 1px solid var(--border-muted);
        }

        .gallery-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-item:hover .gallery-img {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="turf-gradient min-height-screen">

    <!-- Navbar (Simplified from index.php) -->
    <nav class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-6 py-4 glass">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.5)]"></div>
            <span class="font-display font-bold text-xl tracking-tight">Playora<span class="text-emerald-400">.</span></span>
        </div>
        <div class="flex items-center gap-6">
            <div class="hidden md:flex items-center gap-6">
                <a href="index.php" class="text-sm font-medium hover:text-emerald-400 transition-colors">Home</a>
                <a href="index.php#categories" class="text-sm font-medium hover:text-emerald-400 transition-colors">Explore</a>
            </div>
            
            <!-- Theme Toggle -->
            <button onclick="toggleTheme()" class="w-10 h-10 rounded-xl glass flex items-center justify-center hover:bg-white/10 transition-all border border-white/10">
                <span id="theme-icon" class="text-lg">🌙</span>
            </button>

            <a href="auth/login.php" class="px-5 py-2 rounded-xl glass text-sm font-semibold hover:bg-white/10 transition-all border border-white/10">Sign In</a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 pt-28 pb-20">
        
        <!-- Breadcrumb & Back -->
        <div class="mb-8 flex items-center gap-4">
            <a href="index.php" class="text-sm text-muted hover:text-DEFAULT transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Explore
            </a>
            <span class="opacity-20">/</span>
            <span class="text-sm text-emerald-400 font-medium"><?= htmlspecialchars($turf['sport_name']) ?></span>
        </div>

        <!-- Gallery -->
        <div class="gallery-grid mb-12">
            <div class="gallery-main group">
                <?php if(!empty($images[0])): ?>
                    <img src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($turf['name']) ?>" class="gallery-img">
                <?php else: ?>
                    <div class="w-full h-full bg-emerald-900/20 flex items-center justify-center">
                        <span class="text-8xl opacity-20"><?= $turf['sport_icon'] ?: '🏟' ?></span>
                    </div>
                <?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-t from-void/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-8">
                    <span class="text-sm font-medium">View all 12 photos</span>
                </div>
            </div>
            <div class="gallery-side">
                <div class="gallery-item group">
                    <?php if(!empty($images[1])): ?>
                        <img src="<?= htmlspecialchars($images[1]) ?>" alt="Turf View" class="gallery-img">
                    <?php else: ?>
                        <div class="w-full h-full bg-white/5 flex items-center justify-center"><span class="text-4xl opacity-20">🌿</span></div>
                    <?php endif; ?>
                </div>
                <div class="gallery-item group">
                    <?php if(!empty($images[2])): ?>
                        <img src="<?= htmlspecialchars($images[2]) ?>" alt="Turf View" class="gallery-img">
                    <?php else: ?>
                        <div class="w-full h-full bg-white/5 flex items-center justify-center"><span class="text-4xl opacity-20">⚽</span></div>
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-void/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <span class="text-xs font-bold uppercase tracking-widest">+ See More</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <!-- Left Content -->
            <div class="lg:col-span-2 space-y-12">
                
                <!-- Title & Meta -->
                <section>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 text-xs font-bold rounded-full uppercase tracking-wider">
                            Verified Facility
                        </span>
                        <div class="flex items-center gap-1 text-yellow-400 font-bold">
                            <span>★</span> <?= $turf['rating'] ?>
                        </div>
                    </div>
                    <h1 class="font-display text-4xl lg:text-5xl font-bold mb-6">
                        <?= htmlspecialchars($turf['name']) ?>
                    </h1>
                    <div class="flex flex-wrap items-center gap-6 text-muted text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <?= htmlspecialchars($turf['address']) ?>, <?= htmlspecialchars($turf['city_name']) ?>
                        </div>
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500/20"></div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl"><?= $turf['sport_icon'] ?: '🏟' ?></span>
                            <?= htmlspecialchars($turf['sport_name']) ?> Specialist
                        </div>
                    </div>
                </section>

                </section>

                <hr class="opacity-10 border-current">

                <!-- Description -->
                <section>
                    <h3 class="font-display text-2xl font-bold mb-6">About this Facility</h3>
                    <p class="opacity-70 leading-relaxed text-lg whitespace-pre-line">
                        <?= htmlspecialchars($turf['description'] ?: 'No description available for this facility.') ?>
                    </p>
                </section>

                <!-- Amenities -->
                <section>
                    <h3 class="font-display text-xl font-bold mb-6">Amenities</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <?php 
                        $all_amenities = [
                            'Parking' => '🚗', 'Washroom' => '🚿', 'Floodlights' => '💡', 
                            'Drinking Water' => '🚰', 'Changing Room' => '👕', 
                            'Cafeteria' => '☕', 'Equipment Rental' => '👟',
                            'CCTV' => '📹', 'Seating Area' => '🪑'
                        ];
                        foreach ($amenities as $item): 
                            $icon = $all_amenities[$item] ?? '✅';
                        ?>
                        <div class="amenity-chip flex items-center gap-3 px-5 py-3 rounded-2xl">
                            <span class="text-xl"><?= $icon ?></span>
                            <span class="text-sm font-medium"><?= htmlspecialchars($item) ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php if(empty($amenities)): ?>
                            <p class="text-muted text-sm">Amenities details not provided.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <hr class="opacity-10 border-current">

                <!-- Location Map Placeholder -->
                <section>
                    <h3 class="font-display text-xl font-bold mb-6">Location</h3>
                    <div class="w-full h-64 glass rounded-3xl overflow-hidden relative group">
                        <div class="absolute inset-0 bg-void/10 flex items-center justify-center group-hover:bg-void/20 transition-all">
                            <div class="text-center">
                                <div class="text-4xl mb-2">🗺️</div>
                                <span class="text-sm font-semibold text-emerald-400">View on Google Maps</span>
                            </div>
                        </div>
                        <img src="https://api.placeholder.com/800/400" alt="Map View" class="w-full h-full object-cover grayscale opacity-30">
                    </div>
                </section>

            </div>

            <!-- Right Sidebar: Booking -->
            <div class="space-y-6">
                <div class="glass p-8 rounded-[32px] sticky top-28 shadow-2xl">
                    <div class="flex items-end justify-between mb-8">
                        <div>
                            <span class="text-3xl font-bold text-emerald-400">₹<?= number_format($turf['price']) ?></span>
                            <span class="text-muted text-sm ml-1">/hour</span>
                        </div>
                        <div class="text-xs bg-emerald-500/10 text-emerald-400 font-bold px-3 py-1 rounded-full uppercase">
                            Instant Pay
                        </div>
                    </div>

                    <!-- Date Selection -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Select Date</label>
                        <input type="date" class="w-full bg-muted/5 border border-muted/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500/50 transition-all text-DEFAULT" value="<?= date('Y-m-d') ?>">
                    </div>

                    <!-- Time Slots -->
                    <div class="mb-8">
                        <label class="block text-xs font-bold text-muted uppercase tracking-widest mb-3">Available Slots</label>
                        <div class="grid grid-cols-3 gap-2">
                            <?php 
                            $slots = ['06:00 AM', '07:00 AM', '04:00 PM', '05:00 PM', '06:00 PM', '07:00 PM', '08:00 PM', '09:00 PM', '10:00 PM'];
                            foreach($slots as $slot): 
                                $is_booked = (rand(0, 10) > 8); // Simulation
                            ?>
                                <button class="slot-btn text-[10px] font-bold py-2 rounded-lg <?= $is_booked ? 'disabled' : '' ?>">
                                    <?= $slot ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="bg-muted/5 rounded-2xl p-4 space-y-3 mb-8">
                        <div class="flex justify-between text-xs">
                            <span class="text-muted">Base Price (1 hr)</span>
                            <span>₹<?= number_format($turf['price']) ?></span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-muted">Service Fee</span>
                            <span>₹49</span>
                        </div>
                        <hr class="opacity-10 border-current">
                        <div class="flex justify-between font-bold">
                            <span>Total</span>
                            <span class="text-emerald-400">₹<?= number_format($turf['price'] + 49) ?></span>
                        </div>
                    </div>

                    <a href="?id=<?= $turf_id ?>&confirm_booking=1" class="block w-full py-4 bg-emerald-500 hover:bg-emerald-600 text-void font-bold rounded-2xl text-center transition-all shadow-[0_10px_30px_rgba(16,185,129,0.3)]">
                        Confirm Booking
                    </a>
                    <p class="text-[10px] text-center text-muted mt-4 leading-relaxed">
                        By clicking book, you agree to our terms. Cancellations allowed up to 4 hours before.
                    </p>
                </div>

                <!-- Small Info Cards -->
                <div class="glass p-5 rounded-2xl flex items-center gap-4 border-l-4 border-emerald-500">
                    <div class="text-2xl">⚡</div>
                    <div>
                        <div class="text-sm font-bold">Last booked 4h ago</div>
                        <div class="text-xs text-muted">Highly popular in this area</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Related Section -->
        <?php if(!empty($related_turfs)): ?>
        <section class="mt-20">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <span class="text-emerald-400 font-bold uppercase text-xs tracking-widest mb-2 block">Quick Suggestions</span>
                    <h3 class="font-display text-3xl font-bold">Similar Turfs You'll Love</h3>
                </div>
                <a href="index.php" class="text-sm font-bold text-muted hover:text-DEFAULT transition-all">Explore All →</a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($related_turfs as $rt): ?>
                <a href="turf-details.php?id=<?= $rt['id'] ?>" class="group">
                    <div class="glass rounded-3xl overflow-hidden hover:border-emerald-500/30 transition-all">
                        <div class="h-40 bg-emerald-900/20 relative flex items-center justify-center group-hover:bg-emerald-900/40 transition-all">
                            <span class="text-6xl opacity-20"><?= $rt['sport_icon'] ?: '🏟' ?></span>
                        </div>
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold"><?= htmlspecialchars($rt['name']) ?></h4>
                                <span class="text-yellow-400 text-xs font-bold">★ <?= $rt['rating'] ?></span>
                            </div>
                            <div class="text-xs text-muted"><?= htmlspecialchars($rt['city_name']) ?> · <?= htmlspecialchars($rt['sport_name']) ?></div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

    </main>

    <!-- Footer (Same as index.php) -->
    <footer class="bg-void py-20 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <div class="flex items-center justify-center gap-2 mb-8">
                <div class="w-10 h-10 rounded-full bg-emerald-500"></div>
                <span class="font-display font-bold text-3xl tracking-tight">Playora<span class="text-emerald-400">.</span></span>
            </div>
            <p class="text-white/40 text-sm max-w-sm mx-auto mb-12">India's most loved sports turf booking platform. Find, book, and play at the best turfs near you.</p>
            <div class="flex justify-center gap-10 text-xs font-bold text-white/20 uppercase tracking-widest mb-16">
                <a href="#" class="hover:text-emerald-400 transition-colors">Privacy</a>
                <a href="#" class="hover:text-emerald-400 transition-colors">Terms</a>
                <a href="#" class="hover:text-emerald-400 transition-colors">Instagram</a>
                <a href="#" class="hover:text-emerald-400 transition-colors">Twitter</a>
            </div>
            <p class="text-[11px] text-white/10 uppercase tracking-widest">© 2026 Playora Technologies Pvt Ltd. Made in India 🇮🇳</p>
        </div>
    </footer>

    <script>
        // Theme Logic
        const themeIcon = document.getElementById('theme-icon');
        const body = document.body;

        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'dark';
        if (savedTheme === 'light') {
            body.classList.add('light-mode');
            themeIcon.textContent = '☀️';
        }

        function toggleTheme() {
            body.classList.toggle('light-mode');
            const isLight = body.classList.contains('light-mode');
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
            themeIcon.textContent = isLight ? '☀️' : '🌙';
        }

        // Simple slot selection logic
        document.querySelectorAll('.slot-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                if(!btn.classList.contains('disabled')) {
                    document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
