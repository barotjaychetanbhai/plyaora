<?php
header('Content-Type: application/json');
require_once '../player/includes/db.php';
require_once '../includes/mail-service.php';
require_once '../includes/qr-service.php';
require_once '../emails/booking-confirmation.php';
require_once '../emails/payment-receipt.php';

// Keys
$keySecret = 'Kx5CCmWJv92z5ylqSut9ETyZ';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$razorpay_payment_id = $data['razorpay_payment_id'];
$razorpay_order_id = $data['razorpay_order_id'];
$razorpay_signature = $data['razorpay_signature'];

$user_id = $data['user_id'];
$turf_id = $data['turf_id'];
$booking_date = $data['booking_date'];
$time_slots = $data['time_slots']; // Array of slots
$existing_booking_id = $data['existing_booking_id'] ?? null;

// 1. Verify Signature
$generated_signature = hash_hmac('sha256', $razorpay_order_id . "|" . $razorpay_payment_id, $keySecret);

if ($generated_signature !== $razorpay_signature) {
    echo json_encode(['success' => false, 'error' => 'Invalid signature']);
    exit;
}

// 2. Fetch Turf and User details
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
$commission = $total_amount * 0.10; // 10% commission
$owner_amount = $total_amount - $commission;
$slots_string = implode(', ', $time_slots);

// 3. Save Booking
if ($existing_booking_id) {
    // 3. Update existing Booking
    $upd_stmt = $conn->prepare("UPDATE bookings SET status = 'confirmed', payment_method = 'online' WHERE id = ?");
    $upd_stmt->bind_param("i", $existing_booking_id);
    $booking_success = $upd_stmt->execute();
    
    $new_id = $existing_booking_id;
    $t_stmt = $conn->query("SELECT booking_id FROM bookings WHERE id = $new_id");
    $ticket_id = $t_stmt->fetch_assoc()['booking_id'];
    
} else {
    $book_stmt = $conn->prepare("INSERT INTO bookings (user_id, turf_id, booking_date, time_slot, amount, commission, status, payment_method) VALUES (?, ?, ?, ?, ?, ?, 'confirmed', 'online')");
    $book_stmt->bind_param("iissdd", $user_id, $turf_id, $booking_date, $slots_string, $total_amount, $commission);
    $booking_success = $book_stmt->execute();

    if ($booking_success) {
        $new_id = $conn->insert_id;
        $ticket_id = 'PLY' . str_pad($new_id, 6, '0', STR_PAD_LEFT);
        $ticket_token = bin2hex(random_bytes(16));
        $conn->query("UPDATE bookings SET booking_id = '$ticket_id', ticket_token = '$ticket_token' WHERE id = $new_id");

        // 3.1 Save slots to booking_slots table
        foreach ($time_slots as $slot) {
            $slot_stmt = $conn->prepare("INSERT INTO booking_slots (booking_id, slot_time) VALUES (?, ?)");
            $slot_stmt->bind_param("is", $new_id, $slot);
            $slot_stmt->execute();
        }
    }
}

if ($booking_success) {
    if (!isset($ticket_token)) {
        // Query if updating an existing booking
        $btk_stmt = $conn->query("SELECT ticket_token FROM bookings WHERE id = $new_id");
        $res = $btk_stmt->fetch_assoc();
        $ticket_token = $res['ticket_token'];
        if (empty($ticket_token)) {
            $ticket_token = bin2hex(random_bytes(16));
            $conn->query("UPDATE bookings SET ticket_token = '$ticket_token' WHERE id = $new_id");
        }
    }
    
    // Generate QR Code
    $qrUrl = generateBookingQR($ticket_id, $ticket_token);

    // 4. Save Payment
    $pay_stmt = $conn->prepare("INSERT INTO payments (booking_id, amount, commission, owner_amount, payment_status) VALUES (?, ?, ?, ?, 'success')");
    $pay_stmt->bind_param("iddd", $new_id, $total_amount, $commission, $owner_amount);
    $pay_stmt->execute();

    // 5. Send Email Ticket to User
    $confirmationHtml = getBookingConfirmationEmail($user['name'], $turf['name'], $booking_date, $slots_string, 'online', $total_amount, $ticket_id, $qrUrl, $ticket_token);
    sendMail($user['email'], 'Playora Booking Confirmed', $confirmationHtml);

    // 6. Send Payment Receipt to User
    $receiptHtml = getPaymentReceiptEmail($user['name'], $ticket_id, $razorpay_payment_id, $total_amount, 'online', $booking_date);
    sendMail($user['email'], 'Payment Receipt - Playora', $receiptHtml);

    // 7. Send Booking Notification to Owner
    if (!empty($turf['owner_email'])) {
        $ownerHtml = "
        <h2 style='color: #10b981;'>New Booking Received!</h2>
        <p>Hello " . htmlspecialchars($turf['owner_name']) . ",</p>
        <p>You have a new booking for <strong>" . htmlspecialchars($turf['name']) . "</strong>.</p>
        <div style='background: rgba(255,255,255,0.05); padding: 20px; border-radius: 10px; margin: 20px 0;'>
            <p><strong>Customer:</strong> " . htmlspecialchars($user['name']) . "</p>
            <p><strong>Date:</strong> " . date('M d, Y', strtotime($booking_date)) . "</p>
            <p><strong>Slots:</strong> " . $slots_string . "</p>
            <p><strong>Amount:</strong> ₹" . number_format($owner_amount) . " (after commission)</p>
        </div>
        <p>Check your dashboard for more details.</p>";

        sendMail($turf['owner_email'], 'New Booking Alert - ' . $turf['name'], $ownerHtml);
    }

    echo json_encode(['success' => true, 'booking_id' => $ticket_id, 'new_id' => $new_id]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>
