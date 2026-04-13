<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
if(session_status() === PHP_SESSION_NONE) session_start();

$error = "";
$role = $_GET['role'] ?? 'user'; // 'user' or 'owner'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $otp = rand(100000, 999999);

        // Check if email or phone exists
        $check = $conn->prepare("SELECT id FROM users WHERE email=? OR phone=? UNION SELECT id FROM owners WHERE email=? OR phone=?");
        $check->bind_param("ssss", $email, $phone, $email, $phone);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "Email or Phone already registered.";
        } else {
            // Insert
            if ($role === 'owner') {
                $stmt = $conn->prepare("INSERT INTO owners (name, email, phone, password, status) VALUES (?, ?, ?, ?, 'pending')");
                $stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);
            } else {
                $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, status, otp_code) VALUES (?, ?, ?, ?, 'unverified', ?)");
                $stmt->bind_param("sssss", $name, $email, $phone, $hashed_password, $otp);
            }

            if ($stmt->execute()) {
                if ($role === 'user') {
                    // TODO: Send OTP email here via PHPMailer
                    $_SESSION['verify_email'] = $email;
                    redirect('verify-otp.php');
                } else {
                    $success = "Application received! We will contact you soon.";
                }
            } else {
                $error = "Server error. Try again.";
            }
        }
    }
}
?>
<?php require_once '../layouts/header.php'; ?>

<div class="min-h-screen flex items-center justify-center py-20 px-4 relative">
    <!-- Abstract BG -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="glass max-w-md w-full p-10 rounded-3xl relative z-10 shadow-2xl">
        <div class="text-center mb-8">
            <div class="inline-block w-12 h-12 rounded-xl bg-emerald-500 text-black flex items-center justify-center font-display font-bold text-2xl mb-4">P</div>
            <h1 class="text-3xl font-display font-bold dark:text-white text-slate-900">Create Account</h1>
            <p class="text-sm dark:text-gray-400 text-gray-500 mt-2">Join as <?= $role === 'owner' ? 'a Turf Owner' : 'a Player' ?></p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/30 text-red-500 p-4 rounded-xl mb-6 text-sm">
                <?= e($error) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-500 p-4 rounded-xl mb-6 text-sm">
                <?= e($success) ?> <br><a href="login.php" class="underline font-bold mt-2 inline-block">Go to Login</a>
            </div>
        <?php endif; ?>

        <?php if(!isset($success)): ?>
        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Full Name</label>
                <input type="text" name="name" required class="w-full glass border-white/10 rounded-xl px-4 py-3 dark:text-white text-slate-900 outline-none focus:border-emerald-500 transition-colors">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email</label>
                <input type="email" name="email" required class="w-full glass border-white/10 rounded-xl px-4 py-3 dark:text-white text-slate-900 outline-none focus:border-emerald-500 transition-colors">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Phone</label>
                <input type="text" name="phone" required class="w-full glass border-white/10 rounded-xl px-4 py-3 dark:text-white text-slate-900 outline-none focus:border-emerald-500 transition-colors">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Password</label>
                <input type="password" name="password" required class="w-full glass border-white/10 rounded-xl px-4 py-3 dark:text-white text-slate-900 outline-none focus:border-emerald-500 transition-colors">
            </div>
            <button class="w-full bg-emerald-500 hover:bg-emerald-400 text-black font-bold py-3 rounded-xl shadow-lg shadow-emerald-500/20 active:scale-95 transition-all mt-4">
                Sign Up
            </button>
        </form>
        <?php endif; ?>

        <div class="mt-8 text-center text-sm dark:text-gray-400 text-gray-500">
            Already have an account? <a href="login.php" class="text-emerald-500 font-bold hover:underline">Login here</a>
        </div>
    </div>
</div>

</body>
</html>