<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireAuth();

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<?php require_once 'includes/head.php'; ?>

<!-- Sidebar -->
<?php require_once 'includes/sidebar.php'; ?>

<!-- Main Content wrapper -->
<div class="flex-1 flex flex-col h-screen overflow-hidden relative">
    <!-- Header -->
    <div class="flex">
        <?php require_once 'includes/header.php'; ?>
    </div>
    
    <!-- Main Scrollable Area -->
    <main class="flex-1 overflow-y-auto p-4 md:pt-0 pt-0 relative z-0">
        <div class="glass-card min-h-full p-6 transition-all duration-300" id="content">
            <?php
            $allowedPages = ['dashboard', 'users', 'owners', 'turfs', 'turf-add', 'turf-edit', 'sports', 'cities', 'bookings', 'payments', 'reviews', 'analytics', 'settings'];
            
            if (in_array($page, $allowedPages)) {
                $file = "pages/" . $page . ".php"; 
                if (file_exists($file)) { 
                    include($file); 
                } else {
                    echo "<div class='text-center py-12'>
                            <div class='flex justify-center mb-4 text-gray-500'><i data-lucide='file-question' class='w-16 h-16'></i></div>
                            <h3 class='text-xl text-red-400 font-semibold mb-2'>404 - Page under construction</h3>
                            <p class='text-gray-500'>The page '".e($page)."' is not built yet.</p>
                          </div>"; 
                }
            } else {
                echo "<div class='text-center py-12 mt-12'>
                        <h3 class='text-2xl font-bold text-red-400 mb-2'>403 - Forbidden</h3>
                        <p class='text-gray-500'>Invalid page requested.</p>
                      </div>"; 
            }
            ?>
        </div>
    </main>
</div>

<!-- Scripts -->
<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    const sidebar = document.getElementById('sidebar');
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const closeBtn = document.getElementById('close-sidebar-btn');

    function toggleSidebar() {
        sidebar.classList.toggle('hidden');
        sidebar.classList.toggle('fixed');
        sidebar.classList.toggle('inset-0');
        sidebar.classList.toggle('z-[100]');
        sidebar.classList.toggle('bg-void');
        sidebar.classList.toggle('w-full');
        sidebar.classList.toggle('h-screen');
    }

    mobileBtn?.addEventListener('click', toggleSidebar);
    closeBtn?.addEventListener('click', toggleSidebar);
</script>
</body>
</html>
