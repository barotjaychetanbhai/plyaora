<header class="h-16 glass-card m-4 mb-0 ml-0 md:ml-0 md:mr-4 flex items-center justify-between px-6 shrink-0 w-full md:w-auto md:flex-1">
    <div class="flex items-center">
        <button id="mobile-menu-btn" class="md:hidden text-gray-400 hover:text-white mr-4">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <h2 class="text-xl font-semibold capitalize tracking-wide text-white">
            <?php echo isset($_GET['page']) ? e(str_replace('-', ' ', $_GET['page'])) : 'Dashboard'; ?>
        </h2>
    </div>
    <div class="flex items-center gap-4">
        <button class="text-gray-400 hover:text-white transition-colors relative">
            <i data-lucide="bell" class="w-5 h-5"></i>
            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-[#1a1a1c]"></span>
        </button>
        <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-500 flex items-center justify-center text-sm font-bold shadow-lg text-white border border-white/10 cursor-pointer">
            P
        </div>
    </div>
</header>
