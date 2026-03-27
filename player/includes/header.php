<header class="sticky top-0 z-50 glass-card mx-4 mt-4 mb-2 flex items-center justify-between px-6 py-3 shadow-lg">
    <!-- Left: Logo + Name inside glass container -->
    <div class="flex items-center gap-2 glass px-4 py-2 rounded-xl border border-white/5">
        <i data-lucide="dribbble" class="w-5 h-5 text-emerald-400"></i>
        <h1 class="text-lg font-serif font-bold tracking-widest text-white uppercase">Playora</h1>
    </div>

    <!-- Middle: Blank spacer -->
    <div class="flex-1"></div>

    <!-- Right: Navigation -->
    <div class="flex items-center gap-6">
        <!-- Text links -->
        <a href="index.php?page=dashboard" class="text-sm font-medium uppercase tracking-wide text-gray-300 hover:text-emerald-400 transition-colors">Dashboard</a>
        <a href="index.php?page=turfs" class="text-sm font-medium uppercase tracking-wide text-gray-300 hover:text-emerald-400 transition-colors">Discover</a>
        <!-- <a href="index.php?page=turfs" class="text-sm font-medium uppercase tracking-wide text-gray-300 hover:text-emerald-400 transition-colors">Book Now</a> -->

        <!-- Icon circles (My Bookings, Favorites, Profile) -->
        <a href="index.php?page=bookings" class="w-8 h-8 rounded-full bg-black/20 border border-white/10 flex items-center justify-center text-gray-400 hover:text-emerald-400 hover:border-emerald-400/30 transition-all" title="My Bookings">
            <i data-lucide="calendar" class="w-4 h-4"></i>
        </a>
        <a href="index.php?page=favorites" class="w-8 h-8 rounded-full bg-black/20 border border-white/10 flex items-center justify-center text-gray-400 hover:text-emerald-400 hover:border-emerald-400/30 transition-all" title="Favorites">
            <i data-lucide="heart" class="w-4 h-4"></i>
        </a>
        <a href="index.php?page=profile" class="w-8 h-8 rounded-full bg-black/20 border border-white/10 flex items-center justify-center text-gray-400 hover:text-emerald-400 hover:border-emerald-400/30 transition-all" title="Profile">
            <i data-lucide="user" class="w-4 h-4"></i>
        </a>
    </div>
</header>