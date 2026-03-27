    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 py-4 px-6 lg:px-12">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <!-- Logo -->
            <a href="#" class="flex items-center gap-2.5 group">
                <div
                    class="w-9 h-9 rounded-xl btn-em flex items-center justify-center text-white font-display font-bold text-lg">
                    P</div>
                <span class="font-display font-bold text-xl tracking-tight">Playora</span>
            </a>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center gap-8">
                <a href="#" class="text-sm text-gray-400 hover:text-white transition-colors duration-200">Home</a>
                <a href="#categories"
                    class="text-sm text-gray-400 hover:text-white transition-colors duration-200">Explore</a>
                <a href="#how" class="text-sm text-gray-400 hover:text-white transition-colors duration-200">About</a>
                <a href="#footer"
                    class="text-sm text-gray-400 hover:text-white transition-colors duration-200">Contact</a>
            </div>

            <!-- Desktop CTAs -->
            <div class="hidden md:flex items-center gap-4">
                <div class="nav-location" onclick="getLocation()">
                    <span>📍</span>
                    <span id="nav-city">Loading...</span>
                </div>

                <div class="theme-toggle" id="theme-btn" onclick="toggleTheme()">
                    <svg id="theme-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <!-- Sun Icon (default dark) -->
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                </div>

                <a href="partner.php" class="btn-ghost text-sm font-medium px-4 py-2 rounded-xl text-emerald-400">List Your
                    Turf</a>
                <a href="auth/login.php" class="btn-ghost text-sm font-medium px-4 py-2 rounded-xl text-white">Login</a>
                <a href="auth/register.php" class="btn-em text-sm font-semibold px-5 py-2 rounded-xl text-white">Signup</a>
            </div>

            <!-- Mobile hamburger -->
            <button id="hamburger" class="md:hidden w-9 h-9 flex flex-col items-center justify-center gap-1.5"
                onclick="toggleMenu()">
                <span class="w-5 h-0.5 bg-white transition-all duration-300" id="h1"></span>
                <span class="w-5 h-0.5 bg-white transition-all duration-300" id="h2"></span>
                <span class="w-3 h-0.5 bg-white transition-all duration-300 self-start" id="h3"></span>
            </button>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden hidden mt-4 glass-dark rounded-2xl p-5 mx-0">
            <div class="flex flex-col gap-4">
                <a href="#" class="text-sm text-gray-300 hover:text-white">Home</a>
                <a href="#categories" class="text-sm text-gray-300 hover:text-white" onclick="toggleMenu()">Explore</a>
                <a href="#how" class="text-sm text-gray-300 hover:text-white" onclick="toggleMenu()">About</a>
                <a href="#footer" class="text-sm text-gray-300 hover:text-white" onclick="toggleMenu()">Contact</a>
                <div class="flex flex-col gap-2 pt-2 border-t border-white/10">
                    <a href="partner.php"
                        class="btn-ghost text-sm font-medium px-4 py-2.5 rounded-xl text-emerald-400 text-center">List
                        Your Turf</a>
                    <a href="auth/login.php" class="btn-ghost text-sm font-medium px-4 py-2.5 rounded-xl text-white text-center">Login</a>
                    <a href="auth/register.php" class="btn-em text-sm font-semibold px-4 py-2.5 rounded-xl text-white text-center">Signup</a>
                </div>
            </div>
        </div>
    </nav>
