<aside id="sidebar" class="w-64 glass-card m-4 flex flex-col hidden md:flex h-[calc(100vh-2rem)] shrink-0 transition-all duration-300">
    <div class="h-16 flex items-center justify-between px-6 border-b border-[rgba(255,255,255,0.08)]">
        <span class="text-xl font-serif font-bold tracking-wider text-white">PLAYORA</span>
        <button id="close-sidebar-btn" class="md:hidden text-gray-400 hover:text-white">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        <?php
        $navItems = [
            'dashboard' => ['icon' => 'layout-dashboard', 'label' => 'Dashboard'],
            'users' => ['icon' => 'users', 'label' => 'Users'],
            'owners' => ['icon' => 'user-check', 'label' => 'Turf Owners'],
            'turfs' => ['icon' => 'map-pin', 'label' => 'Turfs'],
            'sports' => ['icon' => 'dribbble', 'label' => 'Sports'],
            'cities' => ['icon' => 'building-2', 'label' => 'Cities'],
            'bookings' => ['icon' => 'calendar-days', 'label' => 'Bookings'],
            'payments' => ['icon' => 'credit-card', 'label' => 'Payments'],
            'reviews' => ['icon' => 'star', 'label' => 'Reviews'],
            'analytics' => ['icon' => 'bar-chart-3', 'label' => 'Analytics'],
            'settings' => ['icon' => 'settings', 'label' => 'Settings']
        ];
        
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

        foreach ($navItems as $key => $item) {
            $isActive = $currentPage === $key;
            $activeClass = $isActive ? 'bg-[rgba(255,255,255,0.1)] text-white' : 'text-gray-400 hover:text-white hover:bg-[rgba(255,255,255,0.05)]';
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
