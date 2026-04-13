<footer class="mt-auto border-t dark:border-white/10 border-black/10 py-12 px-6">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
        <!-- Brand -->
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white font-display font-bold shadow-lg shadow-emerald-500/20">P</div>
            <span class="font-display font-bold text-lg dark:text-white text-black">Playora</span>
        </div>

        <!-- Links -->
        <div class="flex flex-wrap gap-8 text-sm font-medium dark:text-gray-400 text-gray-600">
            <a href="#" class="hover:text-emerald-500 transition-colors">Privacy Policy</a>
            <a href="#" class="hover:text-emerald-500 transition-colors">Terms of Service</a>
            <a href="/auth/register.php?role=owner" class="hover:text-emerald-500 transition-colors">Partner With Us</a>
        </div>

        <!-- Copyright -->
        <div class="text-xs dark:text-gray-500 text-gray-400">
            &copy; <?= date('Y') ?> Playora. All rights reserved.
        </div>
    </div>
</footer>
</body>
</html>