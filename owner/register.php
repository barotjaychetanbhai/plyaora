<?php
require_once 'includes/db.php';
session_start();

if (isset($_SESSION['owner_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $turf_name = trim($_POST['turf_name'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($name) || empty($email) || empty($phone) || empty($city) || empty($password)) {
        $error = 'Please fill all required fields';
    } else {
        $stmt_check = $conn->prepare("SELECT id FROM owners WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            $error = 'Email is already registered';
        } else {
            $pwd = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO owners (name, email, phone, password, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->bind_param("ssss", $name, $email, $phone, $pwd);
            if ($stmt->execute()) {
                $owner_id = $conn->insert_id;
                $req_stmt = $conn->prepare("INSERT INTO owner_requests (owner_id, request_type, status) VALUES (?, 'Registration', 'pending')");
                $req_stmt->bind_param("i", $owner_id);
                $req_stmt->execute();
                header("Location: login.php?registered=1");
                exit();
            } else {
                $error = 'Failed to register. Please try again.';
            }
        }
    }
}

$cities_res = $conn->query("SELECT name FROM cities ORDER BY name ASC");
$cities_list = [];
while($c = $cities_res->fetch_assoc()) $cities_list[] = $c['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playora - Partner Registration</title>
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
        select.input-line option { background: #0f0f11; color: #f9f9fa; }
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 10px; }
    </style>
</head>
<body class="min-h-screen flex">
    <div class="flex w-full min-h-screen">

        <!-- LEFT: Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center p-8 md:p-14 lg:p-24 overflow-y-auto custom-scrollbar relative">
            <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-emerald-600/8 rounded-full blur-[160px] pointer-events-none -translate-x-1/3 -translate-y-1/3"></div>

            <div class="max-w-sm w-full mx-auto relative z-10">
                <!-- Logo -->
                <div class="flex items-center gap-3 mb-16">
                    <div class="w-9 h-9 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i data-lucide="building-2" class="w-5 h-5 text-black"></i>
                    </div>
                    <span class="font-black tracking-tighter text-lg uppercase text-white">Playora Partners</span>
                </div>

                <!-- Heading (now matches login two-line style) -->
                <div class="mb-12">
                    <h1 class="font-serif text-6xl md:text-7xl font-bold text-white tracking-tighter mb-3 leading-none">Partner<br>Registration</h1>
                    <p class="text-gray-500 text-sm">List your venue and reach thousands of athletes.</p>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-4 mb-8 rounded-xl text-sm flex items-center gap-3">
                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-8">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-2">Full Name</label>
                            <input type="text" name="name" required placeholder="Alex Knight" class="input-line">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-2">Phone</label>
                            <input type="tel" name="phone" required placeholder="+91 99999 99999" class="input-line">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-2">Business Email</label>
                        <input type="email" name="email" required placeholder="alex@venue.com" class="input-line">
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-2">City</label>
                            <select name="city" required class="input-line">
                                <option value="">Select City</option>
                                <?php foreach($cities_list as $c_name): ?>
                                    <option value="<?php echo htmlspecialchars($c_name); ?>"><?php echo htmlspecialchars($c_name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-2">Venue Name <span class="text-gray-700 normal-case">(optional)</span></label>
                            <input type="text" name="turf_name" placeholder="Grand Arena" class="input-line">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 mb-2">Password</label>
                        <input type="password" name="password" required placeholder="••••••••••••" class="input-line">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-black py-4 rounded-2xl text-[11px] uppercase tracking-[0.25em] hover:shadow-[0_0_30px_rgba(16,185,129,0.35)] transition-all duration-300 active:scale-[0.98] mt-4">
                        Submit Application
                    </button>

                    <!-- Bottom links (same structure as login) -->
                    <div class="pt-6 border-t border-white/5 space-y-3">
                        <p class="text-center text-xs text-gray-600">Already a partner? <a href="login.php" class="text-emerald-400 font-black uppercase tracking-widest ml-1 hover:text-emerald-300 transition-colors">Login here</a></p>
                        <p class="text-center text-xs text-gray-700">Looking to play? <a href="../player/register.php" class="text-gray-500 font-black uppercase tracking-widest ml-1 hover:text-gray-300 transition-colors">Player Signup →</a></p>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT: Brand Panel (identical to login) -->
        <div class="hidden lg:flex w-1/2 relative overflow-hidden flex-col justify-end p-16" style="background: #070709;">
            <img src="sports_owner_registration_bg_1773125895149.png" alt="Stadium" class="absolute inset-0 w-full h-full object-cover opacity-25 grayscale mix-blend-luminosity">
            <div class="absolute inset-0 bg-gradient-to-t from-[#030304] via-[#030304]/50 to-transparent pointer-events-none"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-[#030304]/80 to-transparent pointer-events-none"></div>

            <div class="absolute top-16 right-16 opacity-20">
                <svg width="72" height="72" viewBox="0 0 100 100" fill="none"><path d="M50 0L54.77 45.23L100 50L54.77 54.77L50 100L45.23 54.77L0 50L45.23 45.23L50 0Z" fill="white"/></svg>
            </div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-8 h-px bg-emerald-500"></span>
                    <span class="text-[10px] font-black uppercase tracking-[0.4em] text-emerald-500">Venue Management</span>
                </div>
                <h2 class="text-white text-5xl font-black uppercase tracking-tighter leading-none mb-4">Modernize Your<br>Venue Business.</h2>
                <p class="text-gray-500 text-sm font-medium leading-relaxed max-w-xs">Automated bookings, real-time revenue tracking, and a marketplace of thousands of athletes.</p>
                <div class="flex items-center gap-8 mt-10">
                    <div>
                        <p class="text-white text-2xl font-black">₹2Cr+</p>
                        <p class="text-gray-600 text-[10px] uppercase tracking-widest font-bold mt-1">Paid to Owners</p>
                    </div>
                    <div class="w-px h-10 bg-white/10"></div>
                    <div>
                        <p class="text-white text-2xl font-black">500+</p>
                        <p class="text-gray-600 text-[10px] uppercase tracking-widest font-bold mt-1">Active Venues</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>lucide.createIcons();</script>
</body>
</html>