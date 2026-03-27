<?php
session_start();
require_once '../player/includes/db.php';

$role = $_GET['role'] ?? 'user';
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $city = trim($_POST['city']);

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ? UNION SELECT id FROM owners WHERE email = ?");
    $check_stmt->bind_param("ss", $email, $email);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        $error = "This email is already in use.";
    } else {
        if ($role === 'owner') {
            $stmt = $conn->prepare("INSERT INTO owners (name, email, phone, city, password, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param("sssss", $name, $email, $phone, $city, $password);
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, city, password, status) VALUES (?, ?, ?, ?, ?, 'active')");
            $stmt->bind_param("sssss", $name, $email, $phone, $city, $password);
        }

        if ($stmt->execute()) {
            $success = "Registration successful! You can now <a href='login.php' class='underline'>login</a>.";
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
    <title>Join Playora | Register</title>
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
        <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-emerald-500/10 rounded-full blur-[80px]"></div>
        
        <div class="text-center mb-10 relative">
            <h1 class="font-display text-4xl font-bold mb-3">Join the Hub</h1>
            <p class="text-gray-500 text-sm">Create your <?= ($role === 'owner') ? 'partner account' : 'player profile' ?> to start</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-500 p-4 rounded-xl mb-6 text-sm text-center">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl mb-6 text-sm text-center">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5 relative">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2 ml-1">Full Name</label>
                <input type="text" name="name" required class="w-full input-glass rounded-2xl px-5 py-4 text-white focus:outline-none transition-all placeholder:text-gray-700" placeholder="e.g. John Doe">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2 ml-1">Email Address</label>
                <input type="email" name="email" required class="w-full input-glass rounded-2xl px-5 py-4 text-white focus:outline-none transition-all placeholder:text-gray-700" placeholder="john@example.com">
            </div>
            <div class="grid grid-cols-2 gap-4">
               <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2 ml-1">Phone</label>
                  <input type="text" name="phone" required class="w-full input-glass rounded-2xl px-5 py-4 text-white focus:outline-none transition-all placeholder:text-gray-700" placeholder="10-digit number">
               </div>
               <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2 ml-1">City</label>
                  <input type="text" name="city" required class="w-full input-glass rounded-2xl px-5 py-4 text-white focus:outline-none transition-all placeholder:text-gray-700" placeholder="e.g. Mumbai">
               </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2 ml-1">Password</label>
                <input type="password" name="password" required class="w-full input-glass rounded-2xl px-5 py-4 text-white focus:outline-none transition-all placeholder:text-gray-700" placeholder="••••••••">
            </div>
            
            <button type="submit" class="w-full bg-emerald-500 text-black font-bold py-4 rounded-2xl hover:bg-emerald-400 transition-all active:scale-95 shadow-[0_10px_30px_rgba(16,185,129,0.2)]">Sign Up Now</button>
        </form>

        <p class="text-center text-gray-500 text-sm mt-10">
            Already have an account? <a href="login.php" class="text-emerald-400 hover:text-emerald-300 font-semibold underline decoration-emerald-400/30 underline-offset-4">Login</a>
        </p>
    </div>
</body>
</html>
