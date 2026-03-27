<?php require_once '../layouts/header.php'; ?>
<?php require_once '../layouts/navbar.php'; ?>

<main class="flex-grow container mx-auto px-6 py-12 max-w-7xl">

    <!-- 1. Hero Section -->
    <section class="text-center py-20 relative overflow-hidden glass rounded-[3rem] shadow-2xl mb-24">
        <!-- Abstract BG elements -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-500/20 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-cyan-500/20 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl mx-auto">
            <h1 class="text-5xl md:text-6xl font-display font-black tracking-tight mb-6 dark:text-white text-slate-900 leading-tight">
                Find & Book Turfs <br><span class="grad-text">Near You</span>
            </h1>
            <p class="text-lg md:text-xl dark:text-gray-400 text-gray-600 mb-12">
                Discover the best grounds for Cricket, Football, and more. Instant booking, zero hassle.
            </p>

            <!-- Search Bar -->
            <div class="glass p-3 rounded-3xl flex flex-col md:flex-row gap-3 items-stretch max-w-4xl mx-auto shadow-xl dark:bg-void/60 bg-white/60">
                <div class="flex-1 px-4 py-3 rounded-2xl dark:bg-white/5 bg-black/5 flex items-center gap-3">
                    <span class="text-emerald-500">📍</span>
                    <input type="text" id="search-city" placeholder="City" class="bg-transparent w-full outline-none dark:text-white text-slate-900 placeholder:text-gray-500 font-medium">
                </div>
                <div class="flex-1 px-4 py-3 rounded-2xl dark:bg-white/5 bg-black/5 flex items-center gap-3">
                    <span class="text-emerald-500">⚽</span>
                    <select class="bg-transparent w-full outline-none dark:text-white text-slate-900 font-medium appearance-none">
                        <option value="">Any Sport</option>
                        <option value="cricket">Cricket</option>
                        <option value="football">Football</option>
                        <option value="badminton">Badminton</option>
                    </select>
                </div>
                <div class="flex-1 px-4 py-3 rounded-2xl dark:bg-white/5 bg-black/5 flex items-center gap-3">
                    <span class="text-emerald-500">📅</span>
                    <input type="date" class="bg-transparent w-full outline-none dark:text-white text-slate-900 font-medium appearance-none color-scheme-dark">
                </div>
                <button class="bg-emerald-500 hover:bg-emerald-400 text-black font-bold px-8 py-4 rounded-2xl shadow-lg shadow-emerald-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                    Search <span class="text-xl">→</span>
                </button>
            </div>
        </div>
    </section>

    <!-- 2. Categories -->
    <section id="explore" class="mb-24">
        <h2 class="text-3xl font-display font-bold mb-8 dark:text-white text-slate-900">Browse Categories</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <!-- Cricket -->
            <div class="glass p-8 rounded-3xl text-center hover:border-emerald-500/50 hover:shadow-lg transition-all cursor-pointer group">
                <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">🏏</div>
                <h3 class="font-bold dark:text-white text-slate-900">Cricket</h3>
                <p class="text-sm dark:text-gray-400 text-gray-500 mt-2">120+ Turfs</p>
            </div>
            <!-- Football -->
            <div class="glass p-8 rounded-3xl text-center hover:border-emerald-500/50 hover:shadow-lg transition-all cursor-pointer group">
                <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">⚽</div>
                <h3 class="font-bold dark:text-white text-slate-900">Football</h3>
                <p class="text-sm dark:text-gray-400 text-gray-500 mt-2">80+ Turfs</p>
            </div>
            <!-- Badminton -->
            <div class="glass p-8 rounded-3xl text-center hover:border-emerald-500/50 hover:shadow-lg transition-all cursor-pointer group">
                <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">🏸</div>
                <h3 class="font-bold dark:text-white text-slate-900">Badminton</h3>
                <p class="text-sm dark:text-gray-400 text-gray-500 mt-2">45+ Courts</p>
            </div>
            <!-- Tennis -->
            <div class="glass p-8 rounded-3xl text-center hover:border-emerald-500/50 hover:shadow-lg transition-all cursor-pointer group">
                <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">🎾</div>
                <h3 class="font-bold dark:text-white text-slate-900">Tennis</h3>
                <p class="text-sm dark:text-gray-400 text-gray-500 mt-2">20+ Courts</p>
            </div>
        </div>
    </section>

    <!-- 3. Trending & 4. Nearby -->
    <section class="mb-24">
        <div class="flex items-end justify-between mb-8">
            <h2 class="text-3xl font-display font-bold dark:text-white text-slate-900">Turfs <span class="grad-text">Near You</span></h2>
            <a href="#" class="text-emerald-500 font-semibold text-sm hover:underline">View All</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Mock Turf Card 1 -->
            <div class="glass rounded-3xl overflow-hidden hover:border-emerald-500/30 transition-all cursor-pointer group">
                <div class="h-48 bg-gradient-to-br from-emerald-500/20 to-cyan-500/20 flex items-center justify-center relative">
                    <div class="text-6xl opacity-80 group-hover:scale-110 transition-transform">🏟️</div>
                    <div class="absolute top-4 right-4 bg-void/60 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-yellow-400 flex items-center gap-1">
                        ★ 4.8
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold dark:text-white text-slate-900 mb-2">Green Field Arena</h3>
                    <p class="text-sm dark:text-gray-400 text-gray-500 mb-4 flex items-center gap-2">
                        📍 <span class="loc-dynamic">Detecting...</span> • 1.2 km away
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-lg dark:text-white text-slate-900">₹800 <span class="text-sm font-normal text-gray-500">/hr</span></div>
                        <button class="px-5 py-2 rounded-xl bg-emerald-500/10 text-emerald-500 font-bold hover:bg-emerald-500 hover:text-black transition-colors">Book</button>
                    </div>
                </div>
            </div>

            <!-- Mock Turf Card 2 -->
            <div class="glass rounded-3xl overflow-hidden hover:border-emerald-500/30 transition-all cursor-pointer group">
                <div class="h-48 bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center relative">
                    <div class="text-6xl opacity-80 group-hover:scale-110 transition-transform">⚽</div>
                    <div class="absolute top-4 right-4 bg-void/60 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-yellow-400 flex items-center gap-1">
                        ★ 4.6
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold dark:text-white text-slate-900 mb-2">City Strikers Turf</h3>
                    <p class="text-sm dark:text-gray-400 text-gray-500 mb-4 flex items-center gap-2">
                        📍 <span class="loc-dynamic">Detecting...</span> • 3.5 km away
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-lg dark:text-white text-slate-900">₹1200 <span class="text-sm font-normal text-gray-500">/hr</span></div>
                        <button class="px-5 py-2 rounded-xl bg-emerald-500/10 text-emerald-500 font-bold hover:bg-emerald-500 hover:text-black transition-colors">Book</button>
                    </div>
                </div>
            </div>

            <!-- Mock Turf Card 3 -->
            <div class="glass rounded-3xl overflow-hidden hover:border-emerald-500/30 transition-all cursor-pointer group">
                <div class="h-48 bg-gradient-to-br from-orange-500/20 to-red-500/20 flex items-center justify-center relative">
                    <div class="text-6xl opacity-80 group-hover:scale-110 transition-transform">🏸</div>
                    <div class="absolute top-4 right-4 bg-void/60 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold text-yellow-400 flex items-center gap-1">
                        ★ 4.9
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold dark:text-white text-slate-900 mb-2">Smash Badminton</h3>
                    <p class="text-sm dark:text-gray-400 text-gray-500 mb-4 flex items-center gap-2">
                        📍 <span class="loc-dynamic">Detecting...</span> • 5.0 km away
                    </p>
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-lg dark:text-white text-slate-900">₹400 <span class="text-sm font-normal text-gray-500">/hr</span></div>
                        <button class="px-5 py-2 rounded-xl bg-emerald-500/10 text-emerald-500 font-bold hover:bg-emerald-500 hover:text-black transition-colors">Book</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. How it works -->
    <section id="how" class="mb-24 text-center">
        <h2 class="text-3xl font-display font-bold mb-12 dark:text-white text-slate-900">How It Works</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 max-w-4xl mx-auto">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-3xl mb-6 shadow-[0_0_30px_rgba(16,185,129,0.2)]">🔍</div>
                <h3 class="font-bold text-xl mb-3 dark:text-white text-slate-900">1. Search</h3>
                <p class="dark:text-gray-400 text-gray-500 text-sm">Find turfs near you using our smart location-based search.</p>
            </div>
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 rounded-2xl bg-cyan-500/10 flex items-center justify-center text-3xl mb-6 shadow-[0_0_30px_rgba(6,182,212,0.2)]">📅</div>
                <h3 class="font-bold text-xl mb-3 dark:text-white text-slate-900">2. Book</h3>
                <p class="dark:text-gray-400 text-gray-500 text-sm">Select your preferred time slot and pay securely online.</p>
            </div>
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 rounded-2xl bg-purple-500/10 flex items-center justify-center text-3xl mb-6 shadow-[0_0_30px_rgba(168,85,247,0.2)]">🏃</div>
                <h3 class="font-bold text-xl mb-3 dark:text-white text-slate-900">3. Play</h3>
                <p class="dark:text-gray-400 text-gray-500 text-sm">Show up at the turf, scan your QR code, and enjoy the game!</p>
            </div>
        </div>
    </section>

    <!-- 6. Owner CTA -->
    <section class="glass rounded-[3rem] p-12 text-center relative overflow-hidden mb-12">
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/10 to-cyan-500/10 pointer-events-none"></div>
        <div class="relative z-10 max-w-2xl mx-auto">
            <span class="bg-emerald-500/20 text-emerald-500 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-6 inline-block">For Turf Owners</span>
            <h2 class="text-4xl font-display font-bold mb-6 dark:text-white text-slate-900">Grow Your Turf Business</h2>
            <p class="dark:text-gray-400 text-gray-600 mb-8">Join hundreds of turf owners who have increased their bookings and revenue with Playora. Free to list!</p>
            <a href="/auth/register.php?role=owner" class="inline-block bg-emerald-500 hover:bg-emerald-400 text-black font-bold px-8 py-4 rounded-2xl shadow-lg shadow-emerald-500/20 transition-all active:scale-95">
                List Your Turf Today
            </a>
        </div>
    </section>

</main>

<script>
// Geolocation Logic
document.addEventListener('DOMContentLoaded', () => {
    const locText = document.getElementById('loc-text');
    const searchCity = document.getElementById('search-city');
    const locDynamics = document.querySelectorAll('.loc-dynamic');

    function setCity(city) {
        locText.textContent = city;
        if(searchCity) searchCity.value = city;
        locDynamics.forEach(el => el.textContent = city);
    }

    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            async (position) => {
                try {
                    // Reverse geocoding using Nominatim (OSM)
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
                    const data = await response.json();

                    const city = data.address.city || data.address.town || data.address.village || 'Your Area';
                    setCity(city);
                } catch (error) {
                    console.error("Geocoding failed:", error);
                    setCity('Unknown Area');
                }
            },
            (error) => {
                console.warn("Geolocation denied or failed:", error.message);
                setCity('Allow Location');
            },
            { timeout: 5000 }
        );
    } else {
        setCity('Geolocation unsupported');
    }
});
</script>

<?php require_once '../layouts/footer.php'; ?>