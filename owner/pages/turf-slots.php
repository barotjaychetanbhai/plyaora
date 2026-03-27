<?php
$owner_id = $_SESSION['owner_id'];
$turf_id = $_GET['id'] ?? 0;

// Verify owner and get turf info
$stmt = $conn->prepare("SELECT * FROM turfs WHERE id = ? AND owner_id = ?");
$stmt->bind_param("ii", $turf_id, $owner_id);
$stmt->execute();
$turf = $stmt->get_result()->fetch_assoc();

if (!$turf) {
    echo "<script>window.location.href='index.php?page=turfs';</script>";
    exit();
}

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'generate') {
        $start_time = strtotime($_POST['start_time']);
        $end_time = strtotime($_POST['end_time']);
        $duration = (int)$_POST['duration']; // in minutes
        
        if ($start_time && $end_time && $duration > 0 && $start_time < $end_time) {
            // clear existing slots
            $conn->query("DELETE FROM turf_slots WHERE turf_id = $turf_id");
            
            $current = $start_time;
            while ($current < $end_time) {
                $next = $current + ($duration * 60);
                if ($next > $end_time) break;
                
                $slot_string = date("h:i A", $current) . " - " . date("h:i A", $next);
                $stmt = $conn->prepare("INSERT INTO turf_slots (turf_id, slot_time, status) VALUES (?, ?, 'available')");
                $stmt->bind_param("is", $turf_id, $slot_string);
                $stmt->execute();
                
                $current = $next;
            }
        }
        echo "<script>window.location.href='index.php?page=turf-slots&id=$turf_id';</script>";
        exit();
    } elseif ($action === 'status') {
        $slot_id = $_POST['slot_id'] ?? 0;
        $new_status = $_POST['new_status'] ?? 'available';
        
        $stmt = $conn->prepare("UPDATE turf_slots SET status = ? WHERE id = ? AND turf_id = ?");
        $stmt->bind_param("sii", $new_status, $slot_id, $turf_id);
        $stmt->execute();
        echo "<script>window.location.href='index.php?page=turf-slots&id=$turf_id';</script>";
        exit();
    } elseif ($action === 'add') {
        $slot_time = trim($_POST['slot_time'] ?? '');
        if ($slot_time) {
            $stmt = $conn->prepare("INSERT INTO turf_slots (turf_id, slot_time, status) VALUES (?, ?, 'available')");
            $stmt->bind_param("is", $turf_id, $slot_time);
            $stmt->execute();
        }
        echo "<script>window.location.href='index.php?page=turf-slots&id=$turf_id';</script>";
        exit();
    } elseif ($action === 'delete_slot') {
        $slot_id = $_POST['slot_id'] ?? 0;
        $stmt = $conn->prepare("DELETE FROM turf_slots WHERE id = ? AND turf_id = ?");
        $stmt->bind_param("ii", $slot_id, $turf_id);
        $stmt->execute();
        echo "<script>window.location.href='index.php?page=turf-slots&id=$turf_id';</script>";
        exit();
    } elseif ($action === 'delete_all') {
        $stmt = $conn->prepare("DELETE FROM turf_slots WHERE turf_id = ?");
        $stmt->bind_param("i", $turf_id);
        $stmt->execute();
        echo "<script>window.location.href='index.php?page=turf-slots&id=$turf_id';</script>";
        exit();
    }
}

$slots_res = $conn->query("SELECT * FROM turf_slots WHERE turf_id = $turf_id ORDER BY id ASC");
$slots = [];
while($s = $slots_res->fetch_assoc()) $slots[] = $s;
?>

<div class="space-y-8 pb-10 animate-in fade-in slide-in-from-bottom-5 duration-700">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 px-1">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <div class="px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-cyan-400">Venue Control</span>
                </div>
                <div class="flex items-center gap-1.5 opacity-60">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                    <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Live System</span>
                </div>
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-tighter leading-none mb-2">
                Manage <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-400 to-indigo-400">Time Slots</span>
            </h1>
            <p class="text-gray-400 font-medium flex items-center gap-2">
                <i data-lucide="map-pin" class="w-4 h-4 text-cyan-500"></i>
                <span class="text-white"><?php echo e($turf['name']); ?></span>
            </p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="index.php?page=turfs" class="bg-white/5 hover:bg-white/10 text-white px-6 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest border border-white/10 transition-all flex items-center group shadow-xl">
                <i data-lucide="chevron-left" class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        <!-- Sidebar Controls -->
        <div class="xl:col-span-4 space-y-6">
            <!-- Manual Add Card -->
            <div class="glass-card group relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="p-6 relative">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400 shadow-inner">
                            <i data-lucide="plus" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white tracking-tight">Add Single Slot</h3>
                    </div>
                    
                    <form method="POST" action="" class="space-y-4">
                        <input type="hidden" name="action" value="add">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Example: 06:00 PM - 07:00 PM</label>
                            <input type="text" name="slot_time" required placeholder="HH:MM AM/PM - HH:MM AM/PM" class="w-full bg-void/60 border border-white/5 rounded-xl px-4 py-3.5 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500/50 transition-all font-mono text-sm placeholder:text-gray-700">
                        </div>
                        <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-400 text-void font-black text-[10px] uppercase tracking-[0.2em] py-4 rounded-xl transition-all shadow-xl shadow-emerald-500/10 active:scale-[0.98]">
                            Add Custom Entry
                        </button>
                    </form>
                </div>
            </div>

            <!-- Auto Gen Card -->
            <div class="glass-card group relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="p-6 relative">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/20 flex items-center justify-center text-cyan-400 shadow-inner">
                            <i data-lucide="zap" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white tracking-tight">Smart Generator</h3>
                    </div>
                    <p class="text-xs text-gray-500 mb-6 font-medium leading-relaxed">System will <span class="text-red-400 font-bold uppercase tracking-tighter">overwrite</span> all current data.</p>
                    
                    <form method="POST" action="" class="space-y-5">
                        <input type="hidden" name="action" value="generate">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Open</label>
                                <input type="time" name="start_time" required class="w-full bg-void/60 border border-white/5 rounded-xl px-4 py-3.5 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500/50 transition-all [color-scheme:dark] font-mono text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Close</label>
                                <input type="time" name="end_time" required class="w-full bg-void/60 border border-white/5 rounded-xl px-4 py-3.5 text-white focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500/50 transition-all [color-scheme:dark] font-mono text-sm">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest ml-1">Split Duration</label>
                            <div class="relative">
                                <select name="duration" required class="w-full bg-void/60 border border-white/5 rounded-xl px-4 py-3.5 text-white focus:outline-none focus:border-cyan-500/50 transition-all appearance-none font-mono text-sm">
                                    <option value="30">30 Minutes</option>
                                    <option value="60" selected>1 Hour</option>
                                    <option value="90">1.5 Hours</option>
                                    <option value="120">2 Hours</option>
                                </select>
                                <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none"></i>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-black text-[10px] uppercase tracking-[0.2em] py-4 rounded-xl transition-all shadow-xl shadow-cyan-500/10 active:scale-[0.98] flex items-center justify-center gap-2">
                            <i data-lucide="sparkles" class="w-4 h-4 group-hover:rotate-12 transition-transform"></i> Build Schedule
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Quick Tip -->
            <div class="glass-card p-5 bg-void/20 border-dashed border-white/10">
                <div class="flex gap-4">
                    <div class="shrink-0 p-2.5 rounded-xl bg-orange-500/10 text-orange-400">
                        <i data-lucide="lightbulb" class="w-5 h-5"></i>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-relaxed font-medium">Use <span class="text-white">"Block"</span> to hide slots during cleaning or rainy days without deleting them.</p>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="xl:col-span-8 flex flex-col">
            <!-- Table Header -->
            <div class="glass-card p-6 mb-6 flex flex-col md:flex-row items-center justify-between gap-6 border-b border-white/5">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                            <i data-lucide="calendar" class="w-6 h-6"></i>
                        </div>
                        <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-indigo-500 flex items-center justify-center text-[10px] font-black text-white border-2 border-void">
                            <?php echo count($slots); ?>
                        </span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white tracking-tight">Active Timeline</h3>
                        <p class="text-[10px] uppercase tracking-widest font-black text-gray-500">Live Availability Status</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Status Legends -->
                    <div class="hidden sm:flex items-center gap-4 h-10 px-4 rounded-xl bg-void/40 border border-white/5">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500/80">Available</span>
                        </div>
                        <div class="w-px h-3 bg-white/10"></div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-red-500"></div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-red-500/80">Booked</span>
                        </div>
                        <div class="w-px h-3 bg-white/10"></div>
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-gray-600"></div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Locked</span>
                        </div>
                    </div>

                    <button type="button" onclick="if(confirm('Nuke all slots? This cannot be undone.')) { const f = document.createElement('form'); f.method = 'POST'; const i = document.createElement('input'); i.type = 'hidden'; i.name = 'action'; i.value = 'delete_all'; f.appendChild(i); document.body.appendChild(f); f.submit(); }" class="h-10 px-4 rounded-xl bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-void border border-red-500/20 transition-all font-black text-[10px] uppercase tracking-widest flex items-center gap-2 group">
                        <i data-lucide="trash-2" class="w-4 h-4 group-hover:shake transition-transform"></i> Clear
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 min-h-[500px]">
                <?php if (empty($slots)): ?>
                    <div class="glass-card h-full flex flex-col items-center justify-center p-12 text-center relative overflow-hidden group">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,rgba(99,102,241,0.08),transparent)]"></div>
                        <div class="relative space-y-6">
                            <div class="w-24 h-24 bg-white/5 rounded-[2.5rem] flex items-center justify-center mx-auto mb-4 border border-white/5 transform transition-transform group-hover:scale-110 duration-700">
                                <i data-lucide="clock-4" class="w-10 h-10 text-gray-700"></i>
                            </div>
                            <div>
                                <h4 class="text-2xl font-bold text-white mb-2">Schedule is Empty</h4>
                                <p class="text-gray-500 font-medium max-w-sm mx-auto text-sm leading-relaxed italic">"A journey of a thousand bookings begins with a single time slot."</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-indigo-500/5 border border-indigo-500/10 inline-block">
                                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Use the controls on the left to start</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach($slots as $slot): 
                            $statusConfig = [
                                'available' => [
                                    'card' => 'border-emerald-500/20 bg-emerald-500/5 hover:border-emerald-500/40',
                                    'badge' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                    'icon' => 'text-emerald-500'
                                ],
                                'booked' => [
                                    'card' => 'border-red-500/10 bg-red-500/5 opacity-80 cursor-not-allowed',
                                    'badge' => 'bg-red-500/10 text-red-400 border-red-500/20',
                                    'icon' => 'text-red-500/40'
                                ],
                                'blocked' => [
                                    'card' => 'border-white/5 bg-white/5 opacity-50',
                                    'badge' => 'bg-gray-800 text-gray-500 border-white/10',
                                    'icon' => 'text-gray-700'
                                ]
                            ];
                            $cfg = $statusConfig[$slot['status']] ?? $statusConfig['available'];
                        ?>
                            <div class="glass-card p-5 group transition-all duration-300 relative flex flex-col justify-between <?php echo $cfg['card']; ?>">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="w-10 h-10 rounded-xl bg-void border border-white/5 flex items-center justify-center <?php echo $cfg['icon']; ?> shadow-inner group-hover:scale-105 transition-transform">
                                        <i data-lucide="clock" class="w-5 h-5"></i>
                                    </div>
                                    <button type="button" onclick="if(confirm('Remove this slot?')) { const f = document.createElement('form'); f.method = 'POST'; const a = document.createElement('input'); a.type = 'hidden'; a.name = 'action'; a.value = 'delete_slot'; f.appendChild(a); const s = document.createElement('input'); s.type = 'hidden'; s.name = 'slot_id'; s.value = '<?php echo $slot['id']; ?>'; f.appendChild(s); document.body.appendChild(f); f.submit(); }" class="p-2.5 rounded-xl bg-white/5 hover:bg-red-500/20 text-gray-600 hover:text-red-400 transition-all sm:opacity-0 group-hover:opacity-100">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                                
                                <div class="space-y-3 mb-6">
                                    <p class="text-lg font-mono font-black text-white leading-tight tracking-tighter"><?php echo e($slot['slot_time']); ?></p>
                                    <div class="flex">
                                        <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border <?php echo $cfg['badge']; ?>">
                                            <?php echo e($slot['status']); ?>
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 pt-4 border-t border-white/5">
                                    <?php if($slot['status'] !== 'available'): ?>
                                        <form method="POST" action="" class="flex-1">
                                            <input type="hidden" name="action" value="status">
                                            <input type="hidden" name="slot_id" value="<?php echo $slot['id']; ?>">
                                            <input type="hidden" name="new_status" value="available">
                                            <button type="submit" class="w-full text-[9px] font-black uppercase tracking-widest py-2.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500 text-emerald-400 hover:text-void border border-emerald-500/20 transition-all">
                                                Activate
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if($slot['status'] !== 'blocked'): ?>
                                        <form method="POST" action="" class="flex-1">
                                            <input type="hidden" name="action" value="status">
                                            <input type="hidden" name="slot_id" value="<?php echo $slot['id']; ?>">
                                            <input type="hidden" name="new_status" value="blocked">
                                            <button type="submit" class="w-full text-[9px] font-black uppercase tracking-widest py-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-gray-500 hover:text-white border border-white/5 transition-all">
                                                Lock
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes shake {
    0%, 100% { transform: rotate(0); }
    25% { transform: rotate(5deg); }
    75% { transform: rotate(-5deg); }
}
.group:hover .group-hover\:shake {
    animation: shake 0.5s ease-in-out infinite;
}
</style>
