<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'user') header("Location: ../player/index.php");
    else header("Location: ../owner/index.php");
    exit();
}

$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // 1. Check in owners table FIRST
    $stmt = $conn->prepare("SELECT id, password, status FROM owners WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $owner = $stmt->get_result()->fetch_assoc();

    if ($owner && password_verify($password, $owner['password'])) {
        $_SESSION['user_id'] = $owner['id'];
        $_SESSION['role'] = 'owner';
        $_SESSION['owner_status'] = $owner['status'];
        header("Location: ../owner/index.php");
        exit();
    }

    // 2. Then check users table
    $stmt = $conn->prepare("SELECT id, password, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = 'user';
        $_SESSION['user_status'] = $user['status'];
        
        $redirect = $_SESSION['redirect_url'] ?? '../player/index.php';
        unset($_SESSION['redirect_url']);
        header("Location: $redirect");
        exit();
    }

    $error = "Invalid email or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Playora</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --void: #030304; --emerald: #10b981; }
        body { font-family: 'Inter', sans-serif; background: var(--void); color: #fff; }
        .font-display { font-family: 'Playfair Display', serif; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .input-glass { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); }
        .input-glass:focus { border-color: var(--emerald); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full glass p-10 rounded-[2.5rem] relative overflow-hidden shadow-2xl">
        <div class="absolute -top-24 -left-24 w-64 h-64 bg-emerald-500/10 rounded-full blur-[80px]"></div>
        
        <div class="text-center mb-10 relative">
            <h1 class="font-display text-4xl font-bold mb-3">Welcome Back</h1>
            <p class="text-gray-500 text-sm">Login to your account to continue</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-xl mb-6 text-sm text-center">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6 relative">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2 ml-1">Email Address</label>
                <input type="email" name="email" required class="w-full input-glass rounded-2xl px-5 py-4 text-white focus:outline-none transition-all placeholder:text-gray-700" placeholder="your@email.com">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2 ml-1">Password</label>
                <input type="password" name="password" required class="w-full input-glass rounded-2xl px-5 py-4 text-white focus:outline-none transition-all placeholder:text-gray-700" placeholder="••••••••">
            </div>
            
            <div class="flex items-center justify-between text-xs px-1">
                <label class="flex items-center gap-2 cursor-pointer text-gray-400 hover:text-white transition-colors">
                    <input type="checkbox" class="rounded border-white/10 bg-white/5 text-emerald-500">
                    Remember me
                </label>
                <a href="forgot-password.php" class="text-emerald-400 hover:text-emerald-300 font-medium tracking-tight">Forgot password?</a>
            </div>

            <button type="submit" class="w-full bg-emerald-500 text-black font-bold py-4 rounded-2xl hover:bg-emerald-400 transition-all active:scale-95 shadow-[0_10px_30px_rgba(16,185,129,0.2)]">Login Now</button>
        </form>

        <p class="text-center text-gray-500 text-sm mt-10">
            Don't have an account? <a href="register.php" class="text-emerald-400 hover:text-emerald-300 font-semibold underline decoration-emerald-400/30 underline-offset-4">Sign Up</a>
        </p>
    </div>
</body>
</html>
