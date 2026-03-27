<?php
$search = $_GET['search'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = $_POST['user_id'] ?? 0;
    
    if ($action === 'suspend') {
        $stmt = $conn->prepare("UPDATE users SET status = 'suspended' WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    } elseif ($action === 'activate') {
        $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
    // Redirect to prevent form resubmission
    echo "<script>window.location.href='index.php?page=users';</script>";
    exit();
}

$query = "SELECT * FROM users";
if ($search) {
    $searchTerm = "%$search%";
    $stmt = $conn->prepare("SELECT * FROM users WHERE name LIKE ? OR email LIKE ?");
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $users = $stmt->get_result();
} else {
    $users = $conn->query($query);
}
?>

<div class="glass-card p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h3 class="text-xl font-serif text-white tracking-wide">User Management</h3>
        
        <form method="GET" action="index.php" class="relative w-full md:w-64">
            <input type="hidden" name="page" value="users">
            <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"></i>
            <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Search users..." class="w-full bg-void border border-white/10 rounded-lg pl-10 pr-4 py-2 text-sm text-white focus:outline-none focus:border-purple-500/50 transition-colors">
        </form>
    </div>

    <div class="overflow-x-auto border border-white/5 rounded-lg">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-void/50 border-b border-white/5 uppercase text-xs tracking-wider text-gray-400">
                <tr>
                    <th class="px-6 py-4 font-medium">Name</th>
                    <th class="px-6 py-4 font-medium">Email</th>
                    <th class="px-6 py-4 font-medium">Phone</th>
                    <th class="px-6 py-4 font-medium">City</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                    <th class="px-6 py-4 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                <?php if ($users && $users->num_rows > 0): ?>
                    <?php while($u = $users->fetch_assoc()): ?>
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4 text-white font-medium flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-600 to-cyan-600 flex items-center justify-center text-xs font-bold shrink-0">
                                    <?php echo strtoupper(substr($u['name'], 0, 1)); ?>
                                </div>
                                <?php echo e($u['name']); ?>
                            </td>
                            <td class="px-6 py-4 text-gray-400"><?php echo e($u['email']); ?></td>
                            <td class="px-6 py-4 text-gray-400"><?php echo e($u['phone']); ?></td>
                            <td class="px-6 py-4 text-gray-400"><?php echo e($u['city']); ?></td>
                            <td class="px-6 py-4">
                                <?php if($u['status'] === 'active'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-medium tracking-wide uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-medium tracking-wide uppercase bg-red-500/10 text-red-400 border border-red-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Suspended
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="" class="inline" onsubmit="return confirm('Change status for this user?');">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <?php if($u['status'] === 'active'): ?>
                                            <input type="hidden" name="action" value="suspend">
                                            <button type="submit" class="p-1.5 text-orange-400 hover:bg-orange-400/10 rounded-lg transition-colors tooltip" title="Suspend">
                                                <i data-lucide="pause-circle" class="w-4 h-4"></i>
                                            </button>
                                        <?php else: ?>
                                            <input type="hidden" name="action" value="activate">
                                            <button type="submit" class="p-1.5 text-emerald-400 hover:bg-emerald-400/10 rounded-lg transition-colors tooltip" title="Activate">
                                                <i data-lucide="play-circle" class="w-4 h-4"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                    
                                    <form method="POST" action="" class="inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="p-1.5 text-red-500 hover:bg-red-500/10 rounded-lg transition-colors tooltip" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 opacity-20"></i>
                            <p>No users found matching your criteria.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
