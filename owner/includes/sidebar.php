<aside id="sidebar" class="w-64 glass-card m-4 flex flex-col hidden md:flex h-[calc(100vh-2rem)] shrink-0 transition-all duration-300">
    <div class="h-16 flex items-center justify-between px-6 border-b border-[rgba(255,255,255,0.08)]">
        <span class="text-xl font-serif font-bold tracking-wider text-emerald-400">PARTNER</span>
        <button id="close-sidebar-btn" class="md:hidden text-gray-400 hover:text-white">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <?php
        $navItems = [
            'dashboard' => ['icon' => 'layout-dashboard', 'label' => 'Dashboard'],
            'turfs' => ['icon' => 'map-pin', 'label' => 'My Turfs'],
            'add-turf' => ['icon' => 'plus-square', 'label' => 'Add Turf'],
            'bookings' => ['icon' => 'calendar-days', 'label' => 'Bookings'],
            'earnings' => ['icon' => 'banknote', 'label' => 'Earnings'],
            'scan-ticket' => ['icon' => 'scan-line', 'label' => 'Scan Ticket'],
            'profile' => ['icon' => 'user', 'label' => 'Profile']
        ];
        
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

        foreach ($navItems as $key => $item) {
            // treat edit-turf and turf-slots as part of turfs
            $isActive = ($currentPage === $key) || ($key === 'turfs' && in_array($currentPage, ['edit-turf', 'turf-slots']));
            $activeClass = $isActive ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-gray-400 hover:text-white hover:bg-[rgba(255,255,255,0.05)] border border-transparent';
            echo '<a href="index.php?page='.$key.'" class="flex items-center px-3 py-2.5 rounded-lg transition-colors group '.$activeClass.'">
                    <i data-lucide="'.$item['icon'].'" class="w-5 h-5 mr-3"></i>
                    <span class="font-medium text-sm">'.$item['label'].'</span>
                  </a>';
        }
        ?>
    </nav>
    <div class="p-4 border-t border-[rgba(255,255,255,0.08)]">
        <a href="logout.php" class="flex items-center px-3 py-2.5 rounded-lg text-red-400 hover:bg-red-400/10 transition-colors">
            <i data-lucide="log-out" class="w-5 h-5 mr-3"></i>
            <span class="font-medium text-sm">Logout</span>
        </a>
    </div>
</aside>
