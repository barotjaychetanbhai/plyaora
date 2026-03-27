<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'includes/auth.php';

requireAuth();

// Global Favorite Toggle Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_favorite') {
    $fid = intval($_POST['turf_id']);
    $uid = $_SESSION['user_id'];
    
    $check = $conn->prepare("SELECT id FROM favorite_turfs WHERE user_id = ? AND turf_id = ?");
    $check->bind_param("ii", $uid, $fid);
    $check->execute();
    $res = $check->get_result();
    
    if ($res->num_rows > 0) {
        $del = $conn->prepare("DELETE FROM favorite_turfs WHERE user_id = ? AND turf_id = ?");
        $del->bind_param("ii", $uid, $fid);
        $del->execute();
    } else {
        $ins = $conn->prepare("INSERT INTO favorite_turfs (user_id, turf_id) VALUES (?, ?)");
        $ins->bind_param("ii", $uid, $fid);
        $ins->execute();
    }
    // Stay on current page
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<?php require_once 'includes/head.php'; ?>

<!-- Header / Top Nav -->
<?php require_once 'includes/header.php'; ?>

<!-- Main Scrollable Area -->
<main class="flex-1 overflow-y-auto px-4 pt-4 pb-24 md:pb-8 relative z-0 w-full max-w-7xl mx-auto hide-scroll">
    <div class="glass-card min-h-[calc(100vh-10rem)] p-4 md:p-8 transition-all duration-300" id="content">
        <?php
        $allowedPages = ['dashboard', 'turfs', 'turf-details', 'bookings', 'booking-view', 'add-review', 'profile', 'favorites'];
        
        if (in_array($page, $allowedPages)) {
            $file = "pages/" . $page . ".php"; 
            if (file_exists($file)) { 
                include($file); 
            } else {
                echo "<div class='text-center py-16'>
                        <div class='flex justify-center mb-4 text-emerald-500/50'><i data-lucide='hammer' class='w-20 h-20'></i></div>
                        <h3 class='text-2xl text-emerald-400 font-serif tracking-widest mb-2'>Coming Soon</h3>
                        <p class='text-gray-500 text-sm'>The page '".e($page)."' is being crafted.</p>
                      </div>"; 
            }
        } else {
            echo "<div class='text-center py-16 mt-12 bg-red-500/10 rounded-2xl border border-red-500/20 max-w-md mx-auto'>
                    <i data-lucide='alert-triangle' class='w-12 h-12 text-red-500 mx-auto mb-4'></i>
                    <h3 class='text-2xl font-bold text-red-500 tracking-wider mb-2'>403 - Forbidden</h3>
                    <p class='text-gray-400 text-sm'>Invalid or restricted page requested.</p>
                  </div>"; 
        }
        ?>
    </div>
</main>

<!-- Footer / Mobile Nav -->
<?php require_once 'includes/footer.php'; ?>
