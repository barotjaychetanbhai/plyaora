<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = $_POST['name'] ?? '';
        if ($name) {
            $stmt = $conn->prepare("INSERT INTO cities (name, state, country) VALUES (?, 'Unknown', 'Unknown')");
            $stmt->bind_param("s", $name);
            $stmt->execute();
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        if ($id && $name) {
            $stmt = $conn->prepare("UPDATE cities SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
            $stmt->execute();
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM cities WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
    }
    echo "<script>window.location.href='index.php?page=cities';</script>";
    exit();
}

$query = "SELECT c.*, (SELECT COUNT(*) FROM turfs WHERE city_id = c.id) as turf_count FROM cities c ORDER BY c.name ASC";
$cities = $conn->query($query);
?>
<div class="glass-card p-6 border-t-[3px] border-t-purple-500/50">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 border-b border-white/5 pb-4 gap-4">
        <div>
            <h3 class="text-2xl font-serif text-white tracking-widest uppercase">Supported Cities</h3>
            <p class="text-xs text-purple-400 mt-1 uppercase tracking-widest font-semibold flex items-center shadow-lg"><i data-lucide="map" class="w-3 h-3 mr-1.5"></i> Geographic Coverage</p>
        </div>
        <form method="POST" id="addCityForm" class="hidden">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="name" id="addCityName">
        </form>
        <button onclick="let n=prompt('Enter New City Name:'); if(n){document.getElementById('addCityName').value=n; document.getElementById('addCityForm').submit();}" class="bg-gradient-to-r from-purple-600/20 to-fuchsia-500/20 hover:from-purple-500/30 hover:to-fuchsia-400/30 border border-purple-500/50 text-purple-300 px-4 py-2 rounded-lg text-xs font-semibold uppercase tracking-widest transition-all flex items-center">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add City
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <?php if ($cities && $cities->num_rows > 0): ?>
            <?php while($c = $cities->fetch_assoc()): ?>
                <div class="bg-void/40 border border-white/5 rounded-2xl p-5 relative overflow-hidden group hover:border-purple-500/30 transition-all shadow-xl">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-500/10 rounded-full blur-[30px] pointer-events-none group-hover:bg-purple-500/20 transition-colors"></div>
                    
                    <div class="flex items-start justify-between mb-4">
                        <div class="inline-flex items-center justify-center p-2.5 rounded-xl bg-purple-500/10 text-purple-400 border border-purple-500/20 shadow-[0_0_15px_rgba(168,85,247,0.15)] group-hover:scale-110 transition-transform">
                            <i data-lucide="building-2" class="w-5 h-5"></i>
                        </div>
                        <span class="px-2 py-1 text-[10px] font-black uppercase tracking-widest rounded border <?php echo $c['status'] === 'active' ? 'border-emerald-500/30 text-emerald-400 bg-emerald-500/10' : 'border-gray-500/30 text-gray-400 bg-gray-500/10'; ?>">
                            <?php echo e($c['status']); ?>
                        </span>
                    </div>
                    
                    <h4 class="text-xl font-serif text-white mb-0.5 tracking-wider"><?php echo e($c['name']); ?></h4>
                    <p class="text-xs text-gray-500 font-mono tracking-wider mb-4 border-b border-white/5 pb-3">
                        <?php echo e($c['state']); ?>, <?php echo e($c['country']); ?>
                    </p>
                    
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400 flex items-center gap-1.5"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-purple-300"></i> Turfs</span>
                        <span class="font-bold text-white font-mono bg-white/5 px-3 py-1 rounded-md border border-white/5"><?php echo e($c['turf_count']); ?></span>
                    </div>

                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-void to-transparent h-16 translate-y-full group-hover:translate-y-0 transition-transform flex items-end justify-center pb-3 gap-3 relative z-10">
                        <form method="POST" id="editCityForm_<?php echo $c['id']; ?>" class="hidden">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                            <input type="hidden" name="name" id="editCityName_<?php echo $c['id']; ?>">
                        </form>
                        <button onclick="let n=prompt('Edit City Name:', '<?php echo addslashes($c['name']); ?>'); if(n){document.getElementById('editCityName_<?php echo $c['id']; ?>').value=n; document.getElementById('editCityForm_<?php echo $c['id']; ?>').submit();}" class="text-xs text-gray-300 hover:text-white p-1.5 bg-white/5 hover:bg-white/10 rounded-md transition-colors shadow flex items-center gap-1"><i data-lucide="edit" class="w-3 h-3"></i> Edit</button>
                        
                        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this city?');" class="inline">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                            <button type="submit" class="text-xs text-red-400 hover:text-white p-1.5 bg-red-500/10 hover:bg-red-500/30 border border-red-500/20 rounded-md transition-colors shadow flex items-center gap-1"><i data-lucide="trash" class="w-3 h-3"></i> Delete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-20 bg-void/30 border border-dashed border-white/10 rounded-2xl">
                <i data-lucide="globe-2" class="w-16 h-16 mx-auto text-gray-600 mb-4 opacity-30"></i>
                <h4 class="text-lg font-serif tracking-widest uppercase mb-1">No Cities found</h4>
                <p class="text-xs font-medium uppercase tracking-widest text-gray-500">Expand your platform reach</p>
            </div>
        <?php endif; ?>
    </div>
</div>
