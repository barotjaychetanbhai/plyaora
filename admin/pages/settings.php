<div class="glass-card p-6">
    <div class="flex justify-between items-center mb-8 border-b border-white/5 pb-4">
        <div>
            <h3 class="text-2xl font-serif text-white tracking-widest uppercase shadow-sm">Platform Settings</h3>
            <p class="text-xs text-gray-400 mt-1 uppercase tracking-wider flex items-center">Core configuration</p>
        </div>
    </div>
    
    <div class="max-w-3xl space-y-8">
        <div class="bg-void/40 border border-white/5 p-6 rounded-2xl">
            <h4 class="text-sm font-semibold uppercase tracking-widest text-emerald-400 mb-6 flex items-center border-b border-white/5 pb-3">
                <i data-lucide="percent" class="w-4 h-4 mr-2"></i> Commission Structure
            </h4>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Platform Fee (%)</label>
                    <input type="number" value="10" class="w-full max-w-xs bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white font-mono focus:outline-none focus:border-emerald-500/50 transition-all shadow-inner">
                </div>
                <p class="text-xs text-gray-500 italic">This percentage is deducted from every successful booking payment.</p>
            </div>
        </div>
        
        <div class="bg-void/40 border border-white/5 p-6 rounded-2xl">
            <h4 class="text-sm font-semibold uppercase tracking-widest text-purple-400 mb-6 flex items-center border-b border-white/5 pb-3">
                <i data-lucide="globe" class="w-4 h-4 mr-2"></i> General Info
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Platform Name</label>
                    <input type="text" value="Playora" class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500/50 transition-all shadow-inner">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Support Email</label>
                    <input type="email" value="support@playora.com" class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500/50 transition-all shadow-inner">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Currency</label>
                    <select class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500/50 transition-all shadow-inner appearance-none">
                        <option value="INR">INR (₹)</option>
                        <option value="EUR">EUR (€)</option>
                        <option value="GBP">GBP (£)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Timezone</label>
                    <select class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-purple-500/50 transition-all shadow-inner appearance-none">
                        <option value="UTC">UTC</option>
                        <option value="America/New_York">EST</option>
                    </select>
                </div>
            </div>
        </div>

        <button class="bg-white text-void font-bold uppercase tracking-widest text-sm px-8 py-3.5 rounded-lg hover:bg-gray-200 transition-colors shadow-[0_0_20px_rgba(255,255,255,0.1)] hover:shadow-[0_0_30px_rgba(255,255,255,0.2)]">
            Save Configuration
        </button>
    </div>
</div>
