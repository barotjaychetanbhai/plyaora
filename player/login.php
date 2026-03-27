<?php
require_once 'includes/db.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter email and password';
    } else {
        $stmt = $conn->prepare("SELECT id, password, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows === 1) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                if ($user['status'] === 'active') {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_status'] = $user['status'];
                    header("Location: index.php");
                    exit();
                } else {
                    $error = 'Your account has been suspended.';
                }
            } else {
                $error = 'Invalid credentials';
            }
        } else {
            $error = 'Invalid credentials';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playora - Player Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background-color: #030304; color: #f9f9fa; font-family: 'Inter', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
        .glass { background: rgba(255,255,255,0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.07); }
        .input-line {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 14px 0;
            color: #f9f9fa;
            font-size: 1.1rem;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            outline: none;
            transition: border-color 0.3s;
        }
        .input-line::placeholder { color: rgba(255,255,255,0.15); }
        .input-line:focus { border-bottom-color: #10b981; }
    </style>
</head>
<body class="min-h-screen flex">
    <div class="flex w-full min-h-screen">

        <!-- LEFT: Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center p-8 md:p-14 lg:p-24 overflow-y-auto relative">
            <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-emerald-600/8 rounded-full blur-[160px] pointer-events-none -translate-x-1/3 -translate-y-1/3"></div>

            <div class="max-w-sm w-full mx-auto relative z-10">
                <!-- Logo -->
                <div class="flex items-center gap-3 mb-16">
                    <div class="w-9 h-9 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i data-lucide="dribbble" class="w-5 h-5 text-black"></i>
                    </div>
                    <span class="font-black tracking-tighter text-lg uppercase text-white">Playora</span>
                </div>

                <!-- Heading -->
                <div class="mb-12">
                    <h1 class="font-serif text-7xl font-bold text-white tracking-tighter mb-3 leading-none">Login</h1>
                    <p class="text-gray-500 text-sm">Enter the arena and resume your game.</p>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 mb-8 rounded-xl text-sm flex items-center gap-3">
                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['registered'])): ?>
                    <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 mb-8 rounded-xl text-sm flex items-center gap-3">
                        <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i> Account created! Ready for kick-off.
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-8">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-2">Email Address</label>
                        <input type="email" name="email" required placeholder="max@yahoo.com" class="input-line">
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">Password</label>
                            <a href="../auth/forgot-password.php" class="text-[9px] font-black uppercase tracking-widest text-emerald-500/60 hover:text-emerald-500 transition-colors">Forgot Password?</a>
                        </div>
                        <input type="password" name="password" required placeholder="••••••••••••" class="input-line">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-black py-4 rounded-2xl text-[11px] uppercase tracking-[0.25em] hover:shadow-[0_0_30px_rgba(16,185,129,0.35)] transition-all duration-300 active:scale-[0.98] mt-4">
                        Login
                    </button>

                    <!-- Divider -->
                    <div class="flex items-center gap-4">
                        <div class="flex-1 h-px bg-white/5"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-600">Fast Access</span>
                        <div class="flex-1 h-px bg-white/5"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" class="glass flex items-center justify-center gap-2 py-3 rounded-xl hover:bg-white/5 transition-colors">
                            <i data-lucide="facebook" class="w-4 h-4 text-blue-400"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Facebook</span>
                        </button>
                        <button type="button" class="glass flex items-center justify-center gap-2 py-3 rounded-xl hover:bg-white/5 transition-colors">
                            <i data-lucide="mail" class="w-4 h-4 text-red-400"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Google</span>
                        </button>
                    </div>

                    <div class="pt-4 border-t border-white/5 space-y-3">
                        <p class="text-center text-xs text-gray-600">New to Playora? <a href="register.php" class="text-emerald-400 font-black uppercase tracking-widest ml-1 hover:text-emerald-300 transition-colors">Register Now</a></p>
                        <p class="text-center text-xs text-gray-700">Turf Owner? <a href="../owner/login.php" class="text-gray-500 font-black uppercase tracking-widest ml-1 hover:text-gray-300 transition-colors">Partner Portal →</a></p>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT: Brand Panel -->
        <div class="hidden lg:flex w-1/2 relative overflow-hidden flex-col justify-end p-16" style="background: #070709;">
            <img src="sports_athlete_registration_bg_1773125809268.png" alt="Athlete" class="absolute inset-0 w-full h-full object-cover opacity-25 grayscale mix-blend-luminosity">
            <div class="absolute inset-0 bg-gradient-to-t from-[#030304] via-[#030304]/50 to-transparent pointer-events-none"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#030304]/80 to-transparent pointer-events-none"></div>

            <!-- Decorative star -->
            <div class="absolute top-16 right-16 opacity-20">
                <svg width="72" height="72" viewBox="0 0 100 100" fill="none"><path d="M50 0L54.77 45.23L100 50L54.77 54.77L50 100L45.23 54.77L0 50L45.23 45.23L50 0Z" fill="white"/></svg>
            </div>

            <!-- Bottom text -->
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-8 h-px bg-emerald-500"></span>
                    <span class="text-[10px] font-black uppercase tracking-[0.4em] text-emerald-500">Welcome Back</span>
                </div>
                <h2 class="text-white text-5xl font-black uppercase tracking-tighter leading-none mb-4">Speed. Power.<br>Playora.</h2>
                <p class="text-gray-500 text-sm font-medium leading-relaxed max-w-xs">Your next match is waiting. Book your slot and dominate the field.</p>
                <div class="flex items-center gap-8 mt-10">
                    <div>
                        <p class="text-white text-2xl font-black">500+</p>
                        <p class="text-gray-600 text-[10px] uppercase tracking-widest font-bold mt-1">Turfs Listed</p>
                    </div>
                    <div class="w-px h-10 bg-white/10"></div>
                    <div>
                        <p class="text-white text-2xl font-black">10K+</p>
                        <p class="text-gray-600 text-[10px] uppercase tracking-widest font-bold mt-1">Athletes</p>
                    </div>
                    <div class="w-px h-10 bg-white/10"></div>
                    <div>
                        <p class="text-white text-2xl font-black">50+</p>
                        <p class="text-gray-600 text-[10px] uppercase tracking-widest font-bold mt-1">Cities</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
