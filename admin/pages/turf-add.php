<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $city_id = $_POST['city_id'] ?? 0;
    $sport_id = $_POST['sport_id'] ?? 0;
    $owner_id = $_POST['owner_id'] ?? 0;
    $price = $_POST['price'] ?? 0;
    $status = $_POST['status'] ?? 'active';

    if ($name && $city_id && $sport_id && $owner_id && $price) {
        $stmt = $conn->prepare("INSERT INTO turfs (name, city_id, sport_id, owner_id, price, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siiids", $name, $city_id, $sport_id, $owner_id, $price, $status);
        $stmt->execute();
        echo "<script>window.location.href='index.php?page=turfs';</script>";
        exit();
    }
}

$cities = $conn->query("SELECT id, name FROM cities");
$sports = $conn->query("SELECT id, name FROM sports");
$owners = $conn->query("SELECT id, name FROM owners");
?>
<div class="glass-card p-6 border-t-[3px] border-t-cyan-500/50">
    <div class="flex items-center justify-between mb-8 border-b border-white/5 pb-4">
        <div>
            <h3 class="text-2xl font-serif text-white tracking-widest uppercase">Add Turf</h3>
            <p class="text-xs text-gray-400 mt-1">Register a new venue</p>
        </div>
        <a href="index.php?page=turfs" class="text-gray-400 hover:text-white transition-colors hover:bg-white/5 p-2 rounded-lg border border-transparent hover:border-white/10"><i data-lucide="arrow-left" class="w-5 h-5"></i></a>
    </div>

    <form method="POST" action="" class="space-y-6 max-w-2xl">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Turf Name</label>
            <input type="text" name="name" required class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500/50 transition-all shadow-inner">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">City</label>
                <select name="city_id" required class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500/50 transition-all shadow-inner appearance-none">
                    <?php while($c = $cities->fetch_assoc()): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo e($c['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Sport</label>
                <select name="sport_id" required class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500/50 transition-all shadow-inner appearance-none">
                    <?php while($s = $sports->fetch_assoc()): ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo e($s['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Owner</label>
                <select name="owner_id" required class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500/50 transition-all shadow-inner appearance-none">
                    <?php while($o = $owners->fetch_assoc()): ?>
                        <option value="<?php echo $o['id']; ?>"><?php echo e($o['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Price/Hr (₹)</label>
                <input type="number" step="0.01" name="price" required class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500/50 transition-all shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                <select name="status" class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500/50 transition-all shadow-inner appearance-none">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
        <button type="submit" class="bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white px-6 py-3 rounded-lg text-sm font-semibold tracking-wider transition-all shadow-[0_0_20px_rgba(59,130,246,0.2)] mt-4 inline-block active:scale-[0.98]">
            Save New Turf
        </button>
    </form>
</div>
