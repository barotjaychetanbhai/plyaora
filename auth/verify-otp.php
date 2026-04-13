<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
if(session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['verify_email'])) {
    redirect('login.php');
}

$email = $_SESSION['verify_email'];
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['otp']);

    $stmt = $conn->prepare("SELECT id, otp_code FROM users WHERE email = ? AND status = 'unverified'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && $user['otp_code'] === $code) {
        $update = $conn->prepare("UPDATE users SET status = 'active', otp_code = NULL WHERE id = ?");
        $update->bind_param("i", $user['id']);
        $update->execute();

        unset($_SESSION['verify_email']);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = 'user';
        $_SESSION['status'] = 'active';
        redirect('/player/index.php');
    } else {
        $error = "Invalid or expired OTP code.";
    }
}
?>
<?php require_once '../layouts/header.php'; ?>

<div class="min-h-screen flex items-center justify-center py-20 px-4 relative">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 right-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="glass max-w-sm w-full p-10 rounded-3xl relative z-10 shadow-2xl">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-display font-bold dark:text-white text-slate-900">Verify Email</h1>
            <p class="text-xs dark:text-gray-400 text-gray-500 mt-2">Enter the 6-digit code sent to <?= e($email) ?></p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-500 p-4 rounded-xl mb-6 text-sm">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <input type="text" name="otp" required maxlength="6" class="w-full glass border-white/10 rounded-xl px-4 py-4 text-center text-2xl font-mono tracking-[0.5em] dark:text-white text-slate-900 outline-none focus:border-emerald-500 transition-colors" placeholder="000000">
            </div>
            <button class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-bold py-4 rounded-xl shadow-lg shadow-emerald-500/20 active:scale-95 transition-all">
                Verify Account
            </button>
        </form>
    </div>
</div>

</body>
</html>