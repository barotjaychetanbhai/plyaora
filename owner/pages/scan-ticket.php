<?php
if (!isset($_SESSION['owner_id'])) {
    header('Location: login.php');
    exit();
}
?>
<div class="glass-card overflow-hidden border-t-[3px] border-t-cyan-500/50">
    <div class="p-6 border-b border-white/10">
        <h3 class="text-2xl font-serif tracking-widest text-white uppercase mb-1">Pass Scanner</h3>
        <p class="text-xs text-gray-400">Scan digital entry tickets here</p>
    </div>
    
    <div class="p-6 md:p-12 flex flex-col items-center justify-center min-h-[500px] relative">
        <div id="scanner-container" class="w-full max-w-md mx-auto relative rounded-3xl overflow-hidden shadow-[0_0_50px_rgba(6,182,212,0.15)] border-2 border-cyan-500/30">
            <!-- Camera feed will be injected here -->
            <div id="reader" width="100%" class="bg-black"></div>
            
            <div class="absolute inset-0 pointer-events-none border-[1px] border-white/10 z-10 flex flex-col justify-between p-4">
                <div class="flex justify-between">
                    <div class="w-8 h-8 border-l-4 border-t-4 border-cyan-500/80 rounded-tl-xl drop-shadow-[0_0_5px_rgba(6,182,212,0.8)]"></div>
                    <div class="w-8 h-8 border-r-4 border-t-4 border-cyan-500/80 rounded-tr-xl drop-shadow-[0_0_5px_rgba(6,182,212,0.8)]"></div>
                </div>
                <div class="flex justify-between mt-auto">
                    <div class="w-8 h-8 border-l-4 border-b-4 border-cyan-500/80 rounded-bl-xl drop-shadow-[0_0_5px_rgba(6,182,212,0.8)]"></div>
                    <div class="w-8 h-8 border-r-4 border-b-4 border-cyan-500/80 rounded-br-xl drop-shadow-[0_0_5px_rgba(6,182,212,0.8)]"></div>
                </div>
            </div>
            
            <div class="absolute top-1/2 left-0 right-0 h-0.5 bg-cyan-400 shadow-[0_0_15px_rgba(6,182,212,1)] animate-scan z-20 pointer-events-none opacity-50"></div>
        </div>

        <div id="status-message" class="text-center mt-6">
            <p class="text-cyan-400 font-mono text-sm animate-pulse tracking-widest"><i data-lucide="scan-line" class="inline w-4 h-4 mr-1"></i> Awaiting Signal...</p>
        </div>

        <button id="restartBtn" class="hidden mt-6 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 px-6 py-2 rounded-xl text-xs font-bold uppercase tracking-widest transition-colors flex items-center">
            <i data-lucide="refresh-cw" class="w-3 h-3 mr-2"></i> Restart Scanner
        </button>
    </div>
</div>

<style>
@keyframes scanline {
    0% { transform: translateY(-100px); }
    50% { transform: translateY(100px); }
    100% { transform: translateY(-100px); }
}
.animate-scan {
    animation: scanline 2.5s ease-in-out infinite;
}
#reader video {
    object-fit: cover !important;
}
</style>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    lucide.createIcons();
    
    const html5QrCode = new Html5Qrcode("reader");
    const statusMsg = document.getElementById('status-message');
    const restartBtn = document.getElementById('restartBtn');
    
    // Play subtle sound on success
    const beep = new Audio('data:audio/mp3;base64,//NExAAAAANIAAAAAExBTUUzLjEwMKqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq/zRNQAAACwAAAAAABwAAAAAARnQAD/+cAAAAAAAAAABG//NExAAAAANIAAAAAExBTUUzLjEwMKqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq'); 
    
    function onScanSuccess(decodedText, decodedResult) {
        html5QrCode.stop().then((ignore) => {
            beep.play().catch(e => {}); // Ignore error if not interacted
            statusMsg.innerHTML = '<p class="text-emerald-400 font-bold tracking-widest text-sm"><i data-lucide="check-circle" class="inline w-4 h-4 mr-1 text-emerald-500"></i> Signal Acquired. Validating...</p>';
            lucide.createIcons();
            
            // Expected URL: e.g. https://playora.com/verify-ticket.php?id=...
            // If it's a relative URL or contains our path, redirect
            if(decodedText.includes('verify-ticket.php')) {
                // Determine if we need to adjust path for local dev testing or just use the decoded text if it's an absolute URL
                // If it's an absolute url starting with http, just redirect
                if (decodedText.startsWith('http')) {
                    window.location.href = decodedText;
                } else {
                    // Fallback
                    window.location.href = '../../' + decodedText; 
                }
            } else {
                statusMsg.innerHTML = '<p class="text-red-500 font-bold tracking-widest text-sm"><i data-lucide="x-circle" class="inline w-4 h-4 mr-1 text-red-500"></i> Invalid Token Format</p>';
                lucide.createIcons();
                restartBtn.classList.remove('hidden');
            }
        }).catch((err) => {
            console.log(err);
        });
    }

    function onScanFailure(error) {
        // handles fast scanning failure
    }

    function startScan() {
        statusMsg.innerHTML = '<p class="text-cyan-400 font-mono text-sm animate-pulse tracking-widest"><i data-lucide="scan-line" class="inline w-4 h-4 mr-1"></i> Awaiting Signal...</p>';
        lucide.createIcons();
        restartBtn.classList.add('hidden');
        
        const config = { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 };

        // Try to prefer back camera
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
        .catch(err => {
            // fallback to any camera
            html5QrCode.start({ facingMode: "user" }, config, onScanSuccess, onScanFailure).catch(e => {
                statusMsg.innerHTML = '<p class="text-amber-500 font-bold tracking-widest text-sm"><i data-lucide="alert-triangle" class="inline w-4 h-4 mr-1"></i> Camera access denied or unavailable</p>';
                lucide.createIcons();
            });
        });
    }

    startScan();

    restartBtn.addEventListener('click', startScan);
});
</script>
