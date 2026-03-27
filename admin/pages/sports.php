<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = $_POST['name'] ?? '';
        if ($name) {
            $stmt = $conn->prepare("INSERT INTO sports (name, icon) VALUES (?, 'activity')");
            $stmt->bind_param("s", $name);
            $stmt->execute();
        }
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        if ($id && $name) {
            $stmt = $conn->prepare("UPDATE sports SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
            $stmt->execute();
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM sports WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
    }
    echo "<script>window.location.href='index.php?page=sports';</script>";
    exit();
}

$query = "SELECT s.*, (SELECT COUNT(*) FROM turfs WHERE sport_id = s.id) as turf_count FROM sports s ORDER BY s.name ASC";
$sports = $conn->query($query);
?>
<div class="glass-card p-6 border-t-[3px] border-t-blue-500/50">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 border-b border-white/5 pb-4 gap-4">
        <div>
            <h3 class="text-2xl font-serif text-white tracking-widest uppercase">Sports Categories</h3>
            <p class="text-xs text-blue-400 mt-1 uppercase tracking-widest font-semibold flex items-center shadow-lg"><i data-lucide="activity" class="w-3 h-3 mr-1.5"></i> Platform Activities</p>
        </div>
        <form method="POST" id="addSportForm" class="hidden">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="name" id="addSportName">
        </form>
        <button onclick="let n=prompt('Enter New Sport Name:'); if(n){document.getElementById('addSportName').value=n; document.getElementById('addSportForm').submit();}" class="bg-gradient-to-r from-blue-600/20 to-cyan-500/20 hover:from-blue-500/30 hover:to-cyan-400/30 border border-blue-500/50 text-blue-300 px-4 py-2 rounded-lg text-xs font-semibold uppercase tracking-widest transition-all flex items-center">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add Sport
        </button>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <?php if ($sports && $sports->num_rows > 0): ?>
            <?php while($s = $sports->fetch_assoc()): ?>
                <div class="bg-void/50 border border-white/5 rounded-xl p-6 text-center hover:border-blue-500/30 hover:-translate-y-1 transition-all group overflow-hidden relative cursor-pointer shadow-xl">
                    <div class="absolute inset-0 bg-blue-500/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 mb-4 group-hover:scale-110 transition-transform shadow-[0_0_15px_rgba(59,130,246,0.15)]">
                        <i data-lucide="<?php echo e($s['icon'] ?: 'activity'); ?>" class="w-7 h-7"></i>
                    </div>
                    <h4 class="text-white font-semibold text-lg mb-1 tracking-wide"><?php echo e($s['name']); ?></h4>
                    <p class="text-xs text-gray-400 font-mono tracking-wider"><span class="text-blue-300"><?php echo e($s['turf_count']); ?></span> Turfs</p>
                    
                    <div class="mt-4 pt-3 border-t border-white/5 flex gap-2 justify-center opacity-0 group-hover:opacity-100 transition-opacity relative z-10">
                        <form method="POST" id="editSportForm_<?php echo $s['id']; ?>" class="hidden">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                            <input type="hidden" name="name" id="editSportName_<?php echo $s['id']; ?>">
                        </form>
                        <button onclick="let n=prompt('Edit Sport Name:', '<?php echo addslashes($s['name']); ?>'); if(n){document.getElementById('editSportName_<?php echo $s['id']; ?>').value=n; document.getElementById('editSportForm_<?php echo $s['id']; ?>').submit();}" class="text-xs text-gray-400 hover:text-white p-1 rounded transition-colors tooltip" title="Edit">
                            <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                        </button>
                        
                        <form method="POST" action="" class="inline" onsubmit="return confirm('Are you sure you want to delete this sport?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                            <button type="submit" class="text-xs text-gray-400 hover:text-red-400 p-1 rounded transition-colors tooltip" title="Delete">
                                <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-16 bg-void/30 border border-dashed border-white/10 rounded-xl">
                <i data-lucide="dribbble" class="w-12 h-12 mx-auto text-gray-600 mb-3"></i>
                <p class="text-sm font-medium uppercase tracking-widest text-gray-400">No Sports Configured</p>
            </div>
        <?php endif; ?>
    </div>
</div>
