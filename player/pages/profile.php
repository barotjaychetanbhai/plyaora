<?php
$user_id = $_SESSION['user_id'];
$msg = '';
$err = '';

$cities_res = $conn->query("SELECT name FROM cities ORDER BY name ASC");
$cities_list = [];
while($c = $cities_res->fetch_assoc()) $cities_list[] = $c['name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    if ($name && $phone && $city) {
        $stmt = $conn->prepare("UPDATE users SET name=?, phone=?, city=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $phone, $city, $user_id);
        
        if ($stmt->execute()) {
            $msg = "Profile updated successfully.";
        } else {
            $err = "Could not update profile.";
        }
    } else {
        $err = "Fields cannot be empty.";
    }
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<div class="mb-6 border-b border-white/5 pb-4">
    <h2 class="text-3xl font-serif font-bold text-white mb-1 uppercase tracking-wider">Account Profile</h2>
</div>

<?php if ($msg): ?>
    <div class="bg-emerald-500/10 border border-emerald-500/50 text-emerald-500 text-xs font-semibold p-4 rounded-xl mb-6 flex items-center gap-2">
        <i data-lucide="check-circle" class="w-4 h-4"></i> <?php echo htmlspecialchars($msg); ?>
    </div>
<?php endif; ?>
<?php if ($err): ?>
    <div class="bg-red-500/10 border border-red-500/50 text-red-500 text-xs font-semibold p-4 rounded-xl mb-6 flex items-center gap-2">
        <i data-lucide="alert-circle" class="w-4 h-4"></i> <?php echo htmlspecialchars($err); ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-12 gap-8">
    <div class="md:col-span-4 lg:col-span-3">
        <div class="glass-card p-6 border-t-[3px] border-t-emerald-500/50 rounded-2xl flex flex-col items-center text-center shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-emerald-500/5 to-transparent pointer-events-none"></div>
            
            <div class="w-24 h-24 rounded-full bg-void border border-white/10 flex items-center justify-center text-emerald-400 font-serif text-4xl mb-4 relative shadow-[0_0_30px_rgba(16,185,129,0.2)]">
                <?php echo strtoupper(substr($user['name'], 0, 1) ?: 'U'); ?>
                <div class="absolute -bottom-2 -right-2 bg-emerald-500 text-void rounded-full p-1.5 border-4 border-paper shadow-sm">
                    <i data-lucide="trophy" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            
            <h3 class="text-lg font-bold text-white tracking-wide mb-0.5"><?php echo e($user['name']); ?></h3>
            <p class="text-xs text-gray-500 font-mono tracking-widest uppercase mb-4"><?php echo e($user['city']); ?></p>
            <span class="inline-flex items-center text-[10px] uppercase font-bold tracking-widest text-emerald-400 bg-emerald-500/10 px-3 py-1.5 rounded-full border border-emerald-500/30">Active Player</span>
        </div>
    </div>
    
    <div class="md:col-span-8 lg:col-span-9">
        <div class="glass-card border border-white/5 rounded-2xl p-6 md:p-8 shadow-2xl">
            <form method="POST" action="" class="space-y-6">
                <div>
                    <h4 class="text-sm font-semibold text-white tracking-wider uppercase mb-5 flex items-center"><i data-lucide="user-square" class="w-4 h-4 mr-2 text-emerald-400"></i> Personal Information</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-2">Full Name</label>
                            <input type="text" name="name" required value="<?php echo e($user['name']); ?>" class="w-full bg-void/80 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 transition-all font-sans text-sm shadow-inner placeholder:text-gray-600">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-2">Email Address</label>
                            <input type="email" disabled value="<?php echo e($user['email']); ?>" class="w-full bg-white/5 border border-white/5 rounded-xl px-4 py-3 text-gray-500 cursor-not-allowed shadow-inner text-sm">
                            <p class="text-[9px] text-gray-600 mt-1 uppercase tracking-widest px-2">Registered email cannot be changed</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-2">Phone Number</label>
                            <input type="tel" name="phone" required value="<?php echo e($user['phone'] ?? ''); ?>" class="w-full bg-void/80 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 transition-all font-sans text-sm shadow-inner placeholder:text-gray-600">
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-gray-400 uppercase tracking-widest mb-2">City</label>
                            <select name="city" required class="w-full bg-void/80 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500/50 transition-all font-sans text-sm shadow-inner appearance-none">
                                <option value="">Select City</option>
                                <?php foreach($cities_list as $c_name): ?>
                                    <option value="<?php echo e($c_name); ?>" <?php echo ($user['city'] == $c_name) ? 'selected' : ''; ?>><?php echo e($c_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="pt-6 border-t border-white/5">
                    <h4 class="text-sm font-semibold text-white tracking-wider uppercase mb-5 flex items-center"><i data-lucide="shield-check" class="w-4 h-4 mr-2 text-emerald-400"></i> Account Security</h4>
                    <div class="bg-void/50 border border-white/5 rounded-2xl p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-inner">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mb-1">Password Management</p>
                            <p class="text-xs text-gray-400">To maintain high security, password updates require email verification.</p>
                        </div>
                        <a href="../auth/forgot-password.php" class="inline-flex items-center gap-2 bg-white/5 hover:bg-white/10 text-white text-[10px] font-black uppercase tracking-[0.2em] px-5 py-3 rounded-xl border border-white/10 transition-all hover:border-emerald-500/50">
                            Change Password <i data-lucide="external-link" class="w-3 h-3 text-emerald-500"></i>
                        </a>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-emerald-600 to-teal-500 text-white px-8 py-3 rounded-xl text-xs font-bold uppercase tracking-widest hover:shadow-[0_0_20px_rgba(16,185,129,0.3)] transition-all active:scale-95 flex items-center justify-center">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
