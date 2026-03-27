<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/mail-service.php';
require_once '../emails/owner-booking-request.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$user_id = $data['user_id'];
$turf_id = $data['turf_id'];
$booking_date = $data['booking_date'];
$time_slots = $data['time_slots'];

// 1. Double check availability
foreach ($time_slots as $slot) {
    $check = $conn->prepare("SELECT id FROM bookings WHERE turf_id = ? AND booking_date = ? AND time_slot = ? AND status IN ('pending', 'confirmed')");
    $check->bind_param("iss", $turf_id, $booking_date, $slot);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => "Slot $slot is already booked."]);
        exit;
    }
}

// 2. Fetch Details
$turf_stmt = $conn->prepare("
    SELECT t.name, t.price, t.owner_id, o.email as owner_email, o.name as owner_name 
    FROM turfs t 
    JOIN owners o ON t.owner_id = o.id 
    WHERE t.id = ?
");
$turf_stmt->bind_param("i", $turf_id);
$turf_stmt->execute();
$turf = $turf_stmt->get_result()->fetch_assoc();

$user_stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

if (!$turf || !$user) {
    echo json_encode(['success' => false, 'error' => 'Turf or User not found']);
    exit;
}

$total_amount = $turf['price'] * count($time_slots);
$commission = $total_amount * 0.10;
$owner_amount = $total_amount - $commission;
$slots_string = implode(', ', $time_slots);

// 3. Save Booking (status = pending, payment_method = cash)
$book_stmt = $conn->prepare("INSERT INTO bookings (user_id, turf_id, booking_date, time_slot, amount, commission, status, payment_method) VALUES (?, ?, ?, ?, ?, ?, 'pending', 'cash')");
$book_stmt->bind_param("iissdd", $user_id, $turf_id, $booking_date, $slots_string, $total_amount, $commission);

if ($book_stmt->execute()) {
    $new_id = $conn->insert_id;
    $ticket_id = 'PLY' . str_pad($new_id, 6, '0', STR_PAD_LEFT);
    $ticket_token = bin2hex(random_bytes(16));
    $conn->query("UPDATE bookings SET booking_id = '$ticket_id', ticket_token = '$ticket_token' WHERE id = $new_id");

    // 3.1 Save slots
    foreach ($time_slots as $slot) {
        $slot_stmt = $conn->prepare("INSERT INTO booking_slots (booking_id, slot_time) VALUES (?, ?)");
        $slot_stmt->bind_param("is", $new_id, $slot);
        $slot_stmt->execute();
    }

    // 4. Send Notification to User (Request Sent)
    $playerHtml = "
    <h2 style='color: #fff;'>Hello " . htmlspecialchars($user['name']) . "</h2>
    <p>Your booking request has been sent for <strong>" . htmlspecialchars($turf['name']) . "</strong>. The owner will review and confirm your request shortly.</p>
    <div style='background: rgba(255,255,255,0.05); padding: 20px; border-radius: 15px; margin-top: 20px;'>
        <p><strong>Booking ID:</strong> #" . $ticket_id . "</p>
        <p><strong>Date:</strong> " . date('M d, Y', strtotime($booking_date)) . "</p>
        <p><strong>Slots:</strong> " . $slots_string . "</p>
        <p><strong>Total (to be paid at turf):</strong> ₹" . number_format($total_amount) . "</p>
    </div>";

    sendMail($user['email'], 'Booking Request Sent - ' . $ticket_id, $playerHtml);

    // 5. Send Notification to Owner
    if (isset($turf['owner_email'])) {
        $ownerHtml = getOwnerBookingRequestEmail($turf['owner_name'], $user['name'], $turf['name'], $booking_date, $slots_string, $owner_amount);
        sendMail($turf['owner_email'], 'New Booking Request - Action Required', $ownerHtml);
    }

    echo json_encode(['success' => true, 'booking_id' => $ticket_id, 'new_id' => $new_id]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>
