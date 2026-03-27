<?php
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!-- Mobile Bottom Navigation -->
<nav class="md:hidden fixed bottom-0 inset-x-0 glass-card mx-2 mb-2 pb-safe-area shadow-[0_-5px_20px_rgba(0,0,0,0.5)] z-50 rounded-2xl flex items-center justify-around p-3 backdrop-blur-xl border border-white/10 bg-void/80">
    <a href="index.php?page=dashboard" class="flex flex-col items-center p-2 rounded-lg transition-colors <?php echo ($currentPage === 'dashboard') ? 'text-emerald-400' : 'text-gray-500 hover:text-white'; ?>">
        <i data-lucide="layout-grid" class="w-5 h-5 mb-1"></i>
        <span class="text-[10px] font-semibold tracking-wide">Home</span>
    </a>
    <a href="index.php?page=turfs" class="flex flex-col items-center p-2 rounded-lg transition-colors <?php echo ($currentPage === 'turfs' || $currentPage === 'turf-details') ? 'text-emerald-400' : 'text-gray-500 hover:text-white'; ?>">
        <i data-lucide="search" class="w-5 h-5 mb-1"></i>
        <span class="text-[10px] font-semibold tracking-wide">Explore</span>
    </a>
    <a href="index.php?page=bookings" class="flex flex-col items-center p-2 rounded-lg transition-colors <?php echo ($currentPage === 'bookings') ? 'text-emerald-400' : 'text-gray-500 hover:text-white'; ?>">
        <i data-lucide="calendar-check-2" class="w-5 h-5 mb-1"></i>
        <span class="text-[10px] font-semibold tracking-wide">Bookings</span>
    </a>
    <a href="index.php?page=profile" class="flex flex-col items-center p-2 rounded-lg transition-colors <?php echo ($currentPage === 'profile') ? 'text-emerald-400' : 'text-gray-500 hover:text-white'; ?>">
        <i data-lucide="user" class="w-5 h-5 mb-1"></i>
        <span class="text-[10px] font-semibold tracking-wide">Profile</span>
    </a>
</nav>

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
</body>
</html>
