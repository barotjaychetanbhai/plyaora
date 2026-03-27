<?php
require_once 'includes/db.php';
session_start();

if (isset($_SESSION['admin_id'])) {
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
        $stmt = $conn->prepare("SELECT id, password FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows === 1) {
            $admin = $res->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                header("Location: index.php");
                exit();
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
    <title>Playora - Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], serif: ['Playfair Display', 'serif'] },
                    colors: { void: '#030304', subtle: '#2a2a2d', paper: '#1a1a1c' }
                }
            }
        }
    </script>
    <style>
        body { background-color: #030304; color: #f9f9fa; }
        .glass-card {
            background: rgba(26, 26, 28, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 0.75rem;
        }
    </style>
</head>
<body class="h-screen flex items-center justify-center font-sans antialiased relative overflow-hidden">
    <!-- Abstract dark background -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-600/20 rounded-full blur-[120px] mix-blend-screen pointer-events-none"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-600/20 rounded-full blur-[120px] mix-blend-screen pointer-events-none"></div>
    </div>
    
    <div class="glass-card w-full max-w-md p-8 relative z-10 shadow-2xl">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-serif font-bold tracking-widest mb-2 text-white">PLAYORA</h1>
            <p class="text-gray-400 text-sm tracking-widest uppercase">Admin Portal</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/50 text-red-500 text-sm p-3 rounded-lg mb-6 text-center flex items-center justify-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Email Address</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="email" name="email" required placeholder="admin@playora.com" class="w-full bg-void/80 border border-white/10 rounded-lg pl-12 pr-4 py-3 text-white focus:outline-none focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full bg-void/80 border border-white/10 rounded-lg pl-12 pr-4 py-3 text-white focus:outline-none focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 transition-all">
                    </div>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-lg px-4 py-3 mt-8 hover:shadow-[0_0_20px_rgba(147,51,234,0.3)] transition-all active:scale-[0.98]">
                    Authenticate
                </button>
            </div>
        </form>
    </div>
    
    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
</body>
</html>
