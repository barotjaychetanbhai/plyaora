<?php
$owner_id = $_SESSION['owner_id'];
$turf_id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $city_id = $_POST['city_id'] ?? 0;
    $sport_id = $_POST['sport_id'] ?? 0;
    $price = $_POST['price'] ?? 0;
    $status = $_POST['status'] ?? 'active';
    $address = trim($_POST['address'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $amenities = trim($_POST['amenities'] ?? '');

    // check ownership before update
    $check = $conn->query("SELECT id FROM turfs WHERE id = $turf_id AND owner_id = $owner_id");
    if ($check->num_rows > 0 && $name && $city_id && $sport_id && $price) {
        $stmt = $conn->prepare("UPDATE turfs SET name=?, city_id=?, sport_id=?, price=?, status=?, address=?, description=?, amenities=? WHERE id=?");
        $stmt->bind_param("siidssssi", $name, $city_id, $sport_id, $price, $status, $address, $description, $amenities, $turf_id);
        $stmt->execute();
        echo "<script>window.location.href='index.php?page=turfs';</script>";
        exit();
    }
}

$stmt = $conn->prepare("SELECT * FROM turfs WHERE id = ? AND owner_id = ?");
$stmt->bind_param("ii", $turf_id, $owner_id);
$stmt->execute();
$turf = $stmt->get_result()->fetch_assoc();

if (!$turf) {
    echo "<script>window.location.href='index.php?page=turfs';</script>";
    exit();
}

$cities = $conn->query("SELECT id, name FROM cities");
$sports = $conn->query("SELECT id, name FROM sports");
?>
<div class="glass-card p-6 border-t-[3px] border-t-emerald-500/50">
    <div class="flex items-center justify-between mb-8 border-b border-white/5 pb-4">
        <div>
            <h3 class="text-2xl font-serif text-white tracking-widest uppercase">Edit Turf</h3>
            <p class="text-xs text-gray-400 mt-1">Update venue information</p>
        </div>
        <a href="index.php?page=turfs" class="text-gray-400 hover:text-white transition-colors hover:bg-white/5 p-2 rounded-lg border border-transparent hover:border-white/10"><i data-lucide="arrow-left" class="w-5 h-5"></i></a>
    </div>

    <form method="POST" action="" class="space-y-6 max-w-3xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Turf Name *</label>
                <input type="text" name="name" required value="<?php echo e($turf['name']); ?>" class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 transition-all shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">City *</label>
                <select name="city_id" required class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 transition-all shadow-inner appearance-none">
                    <?php while($c = $cities->fetch_assoc()): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $c['id'] == $turf['city_id'] ? 'selected' : ''; ?>><?php echo e($c['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Sport *</label>
                <select name="sport_id" required class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 transition-all shadow-inner appearance-none">
                    <?php while($s = $sports->fetch_assoc()): ?>
                        <option value="<?php echo $s['id']; ?>" <?php echo $s['id'] == $turf['sport_id'] ? 'selected' : ''; ?>><?php echo e($s['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Price/Hr (₹) *</label>
                <input type="number" step="0.01" name="price" required value="<?php echo htmlspecialchars($turf['price']); ?>" class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 transition-all shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                <select name="status" class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 transition-all shadow-inner appearance-none">
                    <option value="active" <?php echo $turf['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $turf['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
        </div>
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Full Address</label>
            <textarea name="address" rows="2" class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 transition-all shadow-inner resize-none"><?php echo e($turf['address'] ?? ''); ?></textarea>
        </div>
        
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</label>
            <textarea name="description" rows="3" class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 transition-all shadow-inner resize-none"><?php echo e($turf['description'] ?? ''); ?></textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Amenities (Comma separated)</label>
            <input type="text" name="amenities" value="<?php echo e($turf['amenities'] ?? ''); ?>" placeholder="e.g. Parking, Washroom, Drinking Water" class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 transition-all shadow-inner">
        </div>

        <button type="submit" class="bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white px-6 py-3 rounded-lg text-sm font-semibold tracking-wider transition-all shadow-[0_0_20px_rgba(16,185,129,0.2)] mt-4 inline-flex items-center active:scale-[0.98]">
            <i data-lucide="check" class="w-4 h-4 mr-2"></i> Update Turf Details
        </button>
    </form>
</div>
