<div class="glass-card p-6">
    <div class="flex justify-between items-center mb-8 border-b border-white/5 pb-4">
        <div>
            <h3 class="text-2xl font-serif text-white tracking-widest uppercase shadow-sm">Platform Intelligence</h3>
            <p class="text-xs text-blue-400 mt-1 uppercase tracking-wider flex items-center"><i data-lucide="bar-chart-2" class="w-3.5 h-3.5 mr-1"></i> Data Analytics</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-void/40 border border-white/5 rounded-2xl p-6 relative overflow-hidden group">
            <div class="absolute inset-0 bg-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
            <h4 class="text-sm font-semibold uppercase tracking-widest mb-6 flex items-center border-b border-white/5 pb-3 font-mono">
                <i data-lucide="trending-up" class="w-4 h-4 mr-2 text-blue-400"></i> Revenue Trends
            </h4>
            <div class="h-64 flex flex-col justify-end relative">
                <div class="absolute inset-0 border-b border-l border-white/10"></div>
                <!-- Fake Data Bars for visual representation -->
                <div class="flex justify-between items-end h-[90%] pl-2 w-full gap-2 relative z-10">
                    <?php 
                    $heights = [30, 45, 25, 60, 80, 55, 90]; 
                    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                    foreach($heights as $idx => $h): 
                    ?>
                        <div class="w-full relative group/bar flex flex-col justify-end h-full">
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-full opacity-0 group-hover/bar:opacity-100 bg-void text-[10px] font-mono border border-white/10 px-2 py-1 rounded text-white mb-2 transition-all w-max">₹<?php echo $h * 20; ?></div>
                            <div class="w-full bg-gradient-to-t from-blue-600 to-cyan-400 rounded-t-sm transition-all duration-1000 origin-bottom hover:brightness-125" style="height: <?php echo $h; ?>%;"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- X axis labels -->
                <div class="flex justify-between items-center pt-3 pl-2 text-[10px] font-mono text-gray-500 w-full pr-1">
                    <?php foreach($days as $day): ?>
                        <span><?php echo $day; ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="bg-void/40 border border-white/5 rounded-2xl p-6 relative overflow-hidden group">
            <div class="absolute inset-0 bg-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
            <h4 class="text-sm font-semibold uppercase tracking-widest mb-6 flex items-center border-b border-white/5 pb-3 font-mono">
                <i data-lucide="users" class="w-4 h-4 mr-2 text-purple-400"></i> User Growth
            </h4>
            <div class="h-64 flex flex-col justify-end relative">
                <div class="absolute inset-0 border-b border-l border-white/10"></div>
                <!-- Fake Area chart line using overlapping divs -->
                <div class="w-full h-[90%] relative z-10 overflow-hidden">
                    <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                        <defs>
                            <linearGradient id="purpleGlow" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="rgba(168,85,247,0.5)" />
                                <stop offset="100%" stop-color="rgba(168,85,247,0)" />
                            </linearGradient>
                        </defs>
                        <path d="M0,100 L0,70 L15,65 L30,50 L45,60 L60,40 L75,30 L90,15 L100,5 L100,100 Z" fill="url(#purpleGlow)" />
                        <polyline points="0,70 15,65 30,50 45,60 60,40 75,30 90,15 100,5" fill="none" stroke="#a855f7" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <!-- X axis labels -->
                <div class="flex justify-between items-center pt-3 pl-2 text-[10px] font-mono text-gray-500 w-full">
                    <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-void/40 border border-white/5 rounded-2xl p-5 shadow-inner">
            <h4 class="text-xs font-semibold uppercase tracking-widest mb-4 flex justify-between items-center text-gray-400">
                <span>Peak Booking Hours</span>
                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
            </h4>
            <div class="space-y-3 font-mono">
                <div class="flex justify-between items-center text-sm"><span class="text-white">18:00 - 20:00</span> <span class="bg-purple-500/20 text-purple-400 px-2 rounded">42%</span></div>
                <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden"><div class="h-full bg-purple-500 w-[42%]"></div></div>
                
                <div class="flex justify-between items-center text-sm pt-2"><span class="text-white">20:00 - 22:00</span> <span class="bg-blue-500/20 text-blue-400 px-2 rounded">31%</span></div>
                <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden"><div class="h-full bg-blue-500 w-[31%]"></div></div>
                
                <div class="flex justify-between items-center text-sm pt-2"><span class="text-white">08:00 - 10:00</span> <span class="bg-emerald-500/20 text-emerald-400 px-2 rounded">15%</span></div>
                <div class="h-1 w-full bg-white/5 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 w-[15%]"></div></div>
            </div>
        </div>
        
        <div class="bg-void/40 border border-white/5 rounded-2xl p-5 shadow-inner">
            <h4 class="text-xs font-semibold uppercase tracking-widest mb-4 flex justify-between items-center text-gray-400">
                <span>City Performance</span>
                <i data-lucide="map" class="w-3.5 h-3.5"></i>
            </h4>
            <div class="space-y-4 text-sm mt-6">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    <span class="flex-1 text-white">New York</span>
                    <span class="font-mono text-gray-400 text-xs">4.2k bookings</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-fuchsia-500"></div>
                    <span class="flex-1 text-white">Los Angeles</span>
                    <span class="font-mono text-gray-400 text-xs">3.8k bookings</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <span class="flex-1 text-white">Chicago</span>
                    <span class="font-mono text-gray-400 text-xs">2.1k bookings</span>
                </div>
            </div>
        </div>
        
        <div class="bg-void/40 border border-white/5 rounded-2xl p-5 shadow-inner">
            <h4 class="text-xs font-semibold uppercase tracking-widest mb-4 flex justify-between items-center text-gray-400">
                <span>Sport Popularity</span>
                <i data-lucide="dribbble" class="w-3.5 h-3.5"></i>
            </h4>
            <div class="flex justify-center mt-6 relative h-32 w-32 mx-auto">
                <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="rgba(255,255,255,0.05)" stroke-width="20" />
                    <!-- Football 60% -->
                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="#3b82f6" stroke-width="20" stroke-dasharray="251.2" stroke-dashoffset="100.48" />
                    <!-- Cricket 25% -->
                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="#10b981" stroke-width="20" stroke-dasharray="251.2" stroke-dashoffset="188.4" transform="rotate(216 50 50)" />
                    <!-- Basketball 15% -->
                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="#f59e0b" stroke-width="20" stroke-dasharray="251.2" stroke-dashoffset="213.52" transform="rotate(306 50 50)" />
                </svg>
            </div>
        </div>
    </div>
</div>
