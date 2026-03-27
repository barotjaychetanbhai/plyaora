<!-- Navbar -->
<nav class="fixed top-0 left-0 right-0 z-50 py-4 px-6 lg:px-12 backdrop-blur-2xl bg-void/80 dark:bg-void/80 bg-white/80 border-b border-white/10 dark:border-white/10 border-black/5 transition-all">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Logo -->
        <a href="/public/index.php" class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-cyan-500 flex items-center justify-center text-white font-display font-bold text-lg shadow-lg shadow-emerald-500/30">P</div>
            <span class="font-display font-bold text-xl tracking-tight dark:text-white text-black">Playora</span>
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center gap-8">
            <a href="/public/index.php" class="text-sm font-medium dark:text-gray-300 text-gray-600 hover:text-emerald-500 transition-colors">Home</a>
            <a href="#explore" class="text-sm font-medium dark:text-gray-300 text-gray-600 hover:text-emerald-500 transition-colors">Explore</a>
            <a href="#how" class="text-sm font-medium dark:text-gray-300 text-gray-600 hover:text-emerald-500 transition-colors">How it works</a>
        </div>

        <!-- Right Side Actions -->
        <div class="flex items-center gap-4">

            <!-- Location pill (Mock UI for now, logic later) -->
            <button id="loc-btn" class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full glass text-xs font-semibold hover:border-emerald-500/50 transition-colors dark:text-white text-black">
                📍 <span id="loc-text">Detecting...</span>
            </button>

            <!-- Theme Toggle -->
            <button id="theme-toggle" class="w-9 h-9 flex items-center justify-center rounded-xl glass hover:border-emerald-500/50 transition-all dark:text-yellow-400 text-slate-800">
                <span id="theme-icon" class="text-lg">☀️</span>
            </button>

            <a href="/auth/login.php" class="hidden sm:inline-block px-4 py-2 text-sm font-semibold rounded-xl glass hover:bg-white/5 transition-all dark:text-white text-black">Login</a>
            <a href="/auth/register.php" class="px-4 py-2 text-sm font-bold rounded-xl bg-emerald-500 hover:bg-emerald-400 text-black shadow-lg shadow-emerald-500/20 transition-transform active:scale-95">Sign Up</a>
        </div>
    </div>
</nav>

<!-- Push content below fixed navbar -->
<div class="h-20"></div>

<script>
    // Theme Toggle Logic
    const themeBtn = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const htmlEl = document.documentElement;

    function updateIcon() {
        themeIcon.textContent = htmlEl.classList.contains('dark') ? '☀️' : '🌙';
        themeBtn.classList.toggle('text-yellow-400', htmlEl.classList.contains('dark'));
        themeBtn.classList.toggle('text-slate-800', !htmlEl.classList.contains('dark'));
    }

    themeBtn.addEventListener('click', () => {
        htmlEl.classList.toggle('dark');
        const isDark = htmlEl.classList.contains('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        updateIcon();
    });

    updateIcon();
</script>