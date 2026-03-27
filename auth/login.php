<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
if(session_status() === PHP_SESSION_NONE) session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check owners
    $stmt = $conn->prepare("SELECT id, password, status FROM owners WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $owner = $stmt->get_result()->fetch_assoc();

    if ($owner && password_verify($password, $owner['password'])) {
        $_SESSION['user_id'] = $owner['id'];
        $_SESSION['role'] = 'owner';
        $_SESSION['status'] = $owner['status'];
        redirect('/owner/index.php');
    }

    // Check users
    $stmt = $conn->prepare("SELECT id, password, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['status'] === 'unverified') {
            $_SESSION['verify_email'] = $email;
            redirect('verify-otp.php');
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = 'user';
        $_SESSION['status'] = $user['status'];
        redirect('/player/index.php');
    }

    $error = "Invalid email or password.";
}
?>
<?php require_once '../layouts/header.php'; ?>

<div class="min-h-screen flex items-center justify-center py-20 px-4 relative">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-cyan-500/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="glass max-w-md w-full p-10 rounded-3xl relative z-10 shadow-2xl">
        <div class="text-center mb-10">
            <div class="inline-block w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-cyan-500 text-white flex items-center justify-center font-display font-bold text-2xl mb-4 shadow-lg shadow-emerald-500/30">P</div>
            <h1 class="text-3xl font-display font-bold dark:text-white text-slate-900">Welcome Back</h1>
            <p class="text-sm dark:text-gray-400 text-gray-500 mt-2">Login to your account</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-500 p-4 rounded-xl mb-6 text-sm">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email</label>
                <input type="email" name="email" required class="w-full glass border-white/10 rounded-xl px-4 py-3 dark:text-white text-slate-900 outline-none focus:border-emerald-500 transition-colors" placeholder="your@email.com">
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest">Password</label>
                    <a href="#" class="text-xs text-emerald-500 font-bold hover:underline">Forgot?</a>
                </div>
                <input type="password" name="password" required class="w-full glass border-white/10 rounded-xl px-4 py-3 dark:text-white text-slate-900 outline-none focus:border-emerald-500 transition-colors" placeholder="••••••••">
            </div>
            <button class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-bold py-4 rounded-xl shadow-lg shadow-emerald-500/20 active:scale-95 transition-all mt-6">
                Login
            </button>
        </form>

        <div class="mt-8 text-center text-sm dark:text-gray-400 text-gray-500">
            Don't have an account? <a href="register.php" class="text-emerald-500 font-bold hover:underline">Sign up</a>
        </div>
    </div>
</div>

</body>
</html>