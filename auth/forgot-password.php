<?php
require_once '../player/includes/db.php';
require_once '../includes/mail-service.php';
require_once '../emails/password-reset.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = "Please enter your email.";
    } else {
        // Check if it's a user
        $stmt = $conn->prepare("SELECT id, name, 'user' as role FROM users WHERE email = ? UNION SELECT id, name, 'owner' as role FROM owners WHERE email = ?");
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $token = bin2hex(random_bytes(32));
            
            // For this example, let's make sure the table can hold the token
            $table = ($row['role'] === 'user') ? 'users' : 'owners';
            
            $upd = $conn->prepare("UPDATE $table SET reset_token = ? WHERE id = ?");
            $upd->bind_param("si", $token, $row['id']);
            $upd->execute();
            
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
            $domain = $_SERVER['HTTP_HOST'];
            $resetLink = $protocol . $domain . "/play/auth/reset-password.php?token=" . $token . "&type=" . $row['role'];
            
            $html = getPasswordResetEmail($row['name'], $resetLink);
            if(sendMail($email, "Password Reset Request", $html)) {
                $message = "A password reset link has been sent to your email address.";
            } else {
                $error = "Failed to send email. Please try again later.";
            }
        } else {
            // Give unified response for security
            $message = "If an account with that email exists, a password reset link has been sent.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Playora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #030304; color: #f9f9fa; }
        .font-serif { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white/5 border border-white/10 rounded-2xl p-8 backdrop-blur-xl relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-emerald-600/8 rounded-full blur-[160px] pointer-events-none -translate-x-1/3 -translate-y-1/3"></div>

        <div class="flex items-center gap-2 mb-8 border-b border-white/5 pb-4">
            <div class="w-7 h-7 bg-emerald-500 rounded-lg flex items-center justify-center">
                <i data-lucide="key-round" class="w-4 h-4 text-black"></i>
            </div>
            <h2 class="text-lg font-bold text-white tracking-widest uppercase mb-1">Pass Recovery</h2>
        </div>

        <h3 class="text-3xl font-serif font-bold mb-2 text-white">Lost access?</h3>
        <p class="text-gray-500 text-sm mb-10 leading-relaxed font-medium">Enter the email associated with your account and we’ll send you an encrypted recovery link.</p>
        
        <?php if ($message): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl mb-10 text-xs flex items-center gap-3">
                <i data-lucide="mail-check" class="w-4 h-4 shrink-0"></i> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-500 p-4 rounded-xl mb-10 text-xs flex items-center gap-3">
                <i data-lucide="alert-octagon" class="w-4 h-4 shrink-0"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!$message): ?>
        <form method="POST" action="" class="space-y-6">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-3">Login Email</label>
                <input type="email" name="email" required class="w-full bg-[#030304] border border-white/10 rounded-xl px-4 py-4 text-white focus:outline-none focus:border-emerald-500 transition-colors placeholder-gray-700 font-medium" placeholder="max@access.now">
            </div>
            <button type="submit" class="w-full bg-emerald-500 text-black font-black uppercase tracking-[0.2em] py-4 rounded-xl hover:bg-emerald-400 transition-all active:scale-95 shadow-[0_0_20px_rgba(16,185,129,0.2)]">Send Access Link</button>
        </form>
        <?php endif; ?>
        
        <div class="mt-12 text-center pt-8 border-t border-white/5">
            <p class="text-gray-600 text-xs uppercase tracking-widest mb-4">Go back to</p>
            <div class="flex items-center justify-center gap-8">
                <a href="../player/login.php" class="text-gray-400 hover:text-white text-[10px] font-black uppercase tracking-[0.2em] transition-colors">Player Login</a>
                <span class="w-1 h-1 bg-gray-600 rounded-full"></span>
                <a href="../owner/login.php" class="text-gray-400 hover:text-white text-[10px] font-black uppercase tracking-[0.2em] transition-colors">Partner Panel</a>
            </div>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
