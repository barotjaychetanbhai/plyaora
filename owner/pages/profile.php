<?php
$owner_id = $_SESSION['owner_id'];
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    if ($name && $phone && $city) {
        $stmt = $conn->prepare("UPDATE owners SET name=?, phone=?, city=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $phone, $city, $owner_id);
        
        if ($stmt->execute()) {
            $msg = "Profile updated successfully.";
        } else {
            $err = "Could not update profile.";
        }
    } else {
        $err = "Fields cannot be empty.";
    }
}

$stmt = $conn->prepare("SELECT * FROM owners WHERE id = ?");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$owner = $stmt->get_result()->fetch_assoc();

$cities_res = $conn->query("SELECT name FROM cities ORDER BY name ASC");
$cities_list = [];
while($c = $cities_res->fetch_assoc()) $cities_list[] = $c['name'];
?>
<div class="glass-card p-6 border-t-[3px] border-t-blue-500/50 max-w-4xl mx-auto mt-4">
    <div class="flex items-center justify-between mb-8 border-b border-white/5 pb-4">
        <div>
            <h3 class="text-2xl font-serif text-white tracking-widest uppercase">My Profile</h3>
            <p class="text-xs text-gray-400 mt-1">Manage personal details and security</p>
        </div>
    </div>

    <?php if ($msg): ?>
        <div class="bg-emerald-500/10 border border-emerald-500/50 text-emerald-500 text-sm p-3 rounded-lg mb-6 flex items-center gap-2 font-medium">
            <i data-lucide="check-circle" class="w-4 h-4"></i> <?php echo htmlspecialchars($msg); ?>
        </div>
    <?php endif; ?>
    <?php if ($err): ?>
        <div class="bg-red-500/10 border border-red-500/50 text-red-500 text-sm p-3 rounded-lg mb-6 flex items-center gap-2 font-medium">
            <i data-lucide="alert-circle" class="w-4 h-4"></i> <?php echo htmlspecialchars($err); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-8">
        <div class="flex flex-col md:flex-row gap-8 items-start pb-8 border-b border-white/5">
            <div class="w-32 h-32 rounded-full border-4 border-void bg-gradient-to-tr from-blue-600 to-cyan-400 flex flex-col items-center justify-center text-white shadow-2xl relative group cursor-pointer overflow-hidden shrink-0">
                <span class="text-4xl font-serif font-bold uppercase tracking-widest group-hover:opacity-0 transition-opacity">
                    <?php echo substr($owner['name'], 0, 1) ?: 'P'; ?>
                </span>
                <div class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm">
                    <i data-lucide="camera" class="w-6 h-6 mb-1"></i>
                    <span class="text-[10px] uppercase tracking-widest font-semibold text-center leading-tight">Upload<br>Photo</span>
                </div>
            </div>
            
            <div class="flex-1 w-full space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Full Name</label>
                        <input type="text" name="name" required value="<?php echo e($owner['name']); ?>" class="w-full bg-void/80 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500/50 transition-all shadow-inner placeholder-gray-600">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Registered Email</label>
                        <input type="email" disabled value="<?php echo e($owner['email']); ?>" class="w-full bg-white/5 border border-white/5 rounded-lg px-4 py-3 text-gray-500 cursor-not-allowed shadow-inner">
                        <p class="text-[10px] text-gray-600 mt-1 uppercase tracking-widest">Email cannot be changed</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Phone Number</label>
                <div class="relative">
                    <i data-lucide="phone" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                    <input type="tel" name="phone" required value="<?php echo e($owner['phone'] ?? ''); ?>" class="w-full bg-void/80 border border-white/10 rounded-lg pl-11 pr-4 py-3 text-white focus:outline-none focus:border-blue-500/50 transition-all shadow-inner">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">City / Location</label>
                <div class="relative">
                    <i data-lucide="map-pin" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                    <select name="city" required class="w-full bg-void/80 border border-white/10 rounded-lg pl-11 pr-4 py-3 text-white focus:outline-none focus:border-blue-500/50 transition-all shadow-inner appearance-none">
                        <option value="">Select City</option>
                        <?php foreach($cities_list as $c_name): ?>
                            <option value="<?php echo e($c_name); ?>" <?php echo (isset($owner['city']) && $owner['city'] == $c_name) ? 'selected' : ''; ?>><?php echo e($c_name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="bg-void/50 border border-white/5 rounded-2xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-inner mt-8">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center border border-blue-500/20">
                    <i data-lucide="shield-check" class="w-5 h-5 text-blue-400"></i>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-white uppercase tracking-wider">Security Protocol</h4>
                    <p class="text-[11px] text-gray-500">To protect your venue assets, password changes require email authorization.</p>
                </div>
            </div>
            <a href="../auth/forgot-password.php" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-3 rounded-xl transition-all shadow-[0_4px_20px_rgba(37,99,235,0.3)]">
                Initialize Reset <i data-lucide="external-link" class="w-3 h-3"></i>
            </a>
        </div>

        <div class="pt-4 flex justify-end">
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white px-8 py-3 rounded-lg text-sm font-semibold tracking-wider transition-all shadow-[0_0_20px_rgba(59,130,246,0.2)] inline-flex items-center active:scale-[0.98]">
                <i data-lucide="save" class="w-4 h-4 mr-2"></i> Save Changes
            </button>
        </div>
    </form>
</div>
