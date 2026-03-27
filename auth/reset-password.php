<?php
require_once '../player/includes/db.php';

$token = $_GET['token'] ?? '';
$type = $_GET['type'] ?? ''; // 'user' or 'owner'

$error = '';
$success = '';
$validToken = false;

if (empty($token) || empty($type)) {
    $error = "Invalid reset link.";
} else {
    $table = ($type === 'user') ? 'users' : 'owners';
    $stmt = $conn->prepare("SELECT id FROM $table WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows === 1) {
        $validToken = true;
        $row = $res->fetch_assoc();
        $userId = $row['id'];
    } else {
        $error = "This reset link is invalid or has already been used.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE $table SET password = ?, reset_token = NULL WHERE id = ?");
        $upd->bind_param("si", $hashedPassword, $userId);
        
        if ($upd->execute()) {
            $success = "Password has been reset successfully. You can now login.";
            $validToken = false; // Prevent resubmitting
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Playora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #030304; color: #f9f9fa; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white/5 border border-white/10 rounded-2xl p-8 backdrop-blur-xl">
        <h2 class="text-2xl font-bold mb-2 text-white">Reset Password</h2>
        <p class="text-gray-400 text-sm mb-6 uppercase tracking-widest font-bold">New Security Token</p>
        
        <?php if ($success): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
                <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i> <?php echo htmlspecialchars($success); ?>
            </div>
            <a href="<?php echo ($type === 'user') ? '../player/login.php' : '../owner/login.php'; ?>" class="w-full block bg-emerald-500 text-black text-center font-bold py-3 rounded-xl hover:bg-emerald-400 transition-colors uppercase tracking-widest text-sm mt-4">Go to Login</a>
        <?php else: ?>
            
            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($validToken): ?>
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-2">New Password</label>
                    <input type="password" name="password" required class="w-full bg-[#030304] border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors placeholder-gray-600" placeholder="••••••••">
                </div>
                <div class="mb-6">
                    <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-2">Confirm New Password</label>
                    <input type="password" name="confirm_password" required class="w-full bg-[#030304] border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors placeholder-gray-600" placeholder="••••••••">
                </div>
                <button type="submit" class="w-full bg-emerald-500 text-black font-bold py-3 rounded-xl hover:bg-emerald-400 transition-colors uppercase tracking-widest text-sm">Update Password</button>
            </form>
            <?php endif; ?>
            
        <?php endif; ?>
        
        <div class="mt-6 text-center border-t border-white/5 pt-6">
            <a href="<?php echo ($type === 'user') ? '../player/login.php' : '../owner/login.php'; ?>" class="text-gray-500 hover:text-white text-xs font-medium uppercase tracking-widest">Back to Login</a>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
