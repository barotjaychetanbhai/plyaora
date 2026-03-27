<?php
header('Content-Type: application/json');
require_once '../player/includes/db.php';

// Keys
$keyId = 'rzp_test_SPAX3rLNKC7AsO';
$keySecret = 'Kx5CCmWJv92z5ylqSut9ETyZ';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$user_id = $data['user_id'];
$turf_id = $data['turf_id'];
$booking_date = $data['booking_date'];
$time_slots = $data['time_slots']; // Array of slots
$existing_booking_id = $data['existing_booking_id'] ?? null;

if (empty($time_slots)) {
    echo json_encode(['error' => 'No slots selected']);
    exit;
}

// 1. Double check availability for all selected slots
foreach ($time_slots as $slot) {
    $check_query = "
        SELECT id FROM bookings 
        WHERE turf_id = ? 
        AND booking_date = ? 
        AND time_slot LIKE ? 
        AND (
            status = 'confirmed' 
            OR (status = 'pending' AND payment_method = 'cash')
            OR (status = 'pending' AND payment_method = 'online' AND created_at > NOW() - INTERVAL 15 MINUTE)
        )
    ";
    
    if ($existing_booking_id) {
        $check_query .= " AND id != ?";
        $check = $conn->prepare($check_query);
        $slot_pattern = "%$slot%";
        $check->bind_param("isssi", $turf_id, $booking_date, $slot_pattern, $existing_booking_id);
    } else {
        $check = $conn->prepare($check_query);
        $slot_pattern = "%$slot%";
        $check->bind_param("isss", $turf_id, $booking_date, $slot_pattern);
    }
    
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['error' => "Slot $slot is already booked or being processed. Please choose another one."]);
        exit;
    }
}

// 2. Fetch turf price from DB to prevent spoofing
$stmt = $conn->prepare("SELECT price, name FROM turfs WHERE id = ?");
$stmt->bind_param("i", $turf_id);
$stmt->execute();
$turf = $stmt->get_result()->fetch_assoc();

if (!$turf) {
    echo json_encode(['error' => 'Turf not found']);
    exit;
}

$total_price = $turf['price'] * count($time_slots);
$amount = (int)($total_price * 100); // Amount in paise

$order_data = [
    'receipt'         => 'rcpt_' . time(),
    'amount'          => $amount,
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];

$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, $keyId . ":" . $keySecret);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($order_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo json_encode(['error' => 'Curl Error: ' . $err]);
} else {
    $res = json_decode($response, true);
    if (isset($res['id'])) {
        // LOCK SLOTS: Save booking as 'pending' with 'online' method
        // This prevents others from booking these slots while payment is in progress
        $slots_string = implode(', ', $time_slots);
        $commission = $total_price * 0.10;
        
        $book_stmt = $conn->prepare("INSERT INTO bookings (user_id, turf_id, booking_date, time_slot, amount, commission, status, payment_method) VALUES (?, ?, ?, ?, ?, ?, 'pending', 'online')");
        $book_stmt->bind_param("iissdd", $user_id, $turf_id, $booking_date, $slots_string, $total_price, $commission);
        
        if ($book_stmt->execute()) {
            $new_booking_id = $conn->insert_id;
            
            // Generate temporary IDs but we'll finalize them in verify-payment
            $ticket_id = 'PLY' . str_pad($new_booking_id, 6, '0', STR_PAD_LEFT);
            $ticket_token = bin2hex(random_bytes(16));
            $conn->query("UPDATE bookings SET booking_id = '$ticket_id', ticket_token = '$ticket_token' WHERE id = $new_booking_id");

            // Save slots
            foreach ($time_slots as $slot) {
                $slot_stmt = $conn->prepare("INSERT INTO booking_slots (booking_id, slot_time) VALUES (?, ?)");
                $slot_stmt->bind_param("is", $new_booking_id, $slot);
                $slot_stmt->execute();
            }

            echo json_encode([
                'order_id' => $res['id'],
                'amount' => $amount,
                'currency' => 'INR',
                'turf_name' => $turf['name'],
                'new_booking_id' => $new_booking_id
            ]);
        } else {
            echo json_encode(['error' => 'Failed to lock slots']);
        }
    } else {
        echo json_encode(['error' => 'Razorpay Error: ' . ($res['error']['description'] ?? 'Unknown error')]);
    }
}
?>
