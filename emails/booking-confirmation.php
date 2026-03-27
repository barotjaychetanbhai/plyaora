<?php
function getBookingConfirmationEmail($playerName, $turfName, $date, $slots, $paymentMethod, $amount, $ticketId, $qrUrl = null, $token = null) {
    ob_start();
    ?>
    <div style="text-align: center; margin-bottom: 30px;">
        <span style="display: inline-block; padding: 8px 16px; background-color: #10b981; color: #000; border-radius: 8px; font-weight: bold; font-size: 14px; letter-spacing: 1px; text-transform: uppercase;">Booking Confirmed</span>
    </div>
    
    <h2 style="color: #ffffff; font-size: 20px; margin-bottom: 10px; font-weight: 600;">Hello <?php echo htmlspecialchars($playerName); ?>,</h2>
    <p style="color: #9ca3af; font-size: 15px; margin-bottom: 25px; line-height: 1.5;">Your turf booking has been confirmed. Get ready for the game!</p>
    
    <?php if ($qrUrl): 
        $emailQrUrl = $qrUrl;
        // fallback to remote PNG generator for emails because SVG support is low in mail clients
        if (strpos($qrUrl, '.svg') !== false && $token) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
            $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $verifyUrl = $protocol . $domain . "/play/verify-ticket.php?id=" . urlencode($ticketId) . "&token=" . urlencode($token);
            $emailQrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($verifyUrl);
        }
    ?>
    <div style="text-align: center; margin-bottom: 30px; background-color: #1a1a1f; padding: 20px; border-radius: 12px;">
        <p style="color: #ffffff; font-size: 14px; margin-bottom: 15px; font-weight: bold;">Show this QR code at the turf entrance</p>
        <img src="<?php echo htmlspecialchars($emailQrUrl); ?>" alt="Ticket QR Code" style="max-width: 200px; height: auto; border-radius: 8px; border: 4px solid #fff;">
    </div>
    <?php endif; ?>
    
    <div style="background-color: #1a1a1f; border-radius: 12px; padding: 25px; margin-bottom: 30px;">
        <div style="margin-bottom: 15px;">
            <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; margin: 0 0 5px 0;">Booking ID</p>
            <p style="color: #ffffff; font-size: 16px; margin: 0; font-family: monospace; font-weight: bold;">#<?php echo htmlspecialchars($ticketId); ?></p>
        </div>
        
        <div style="margin-bottom: 15px;">
            <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; margin: 0 0 5px 0;">Turf Name</p>
            <p style="color: #ffffff; font-size: 16px; margin: 0; font-weight: bold;"><?php echo htmlspecialchars($turfName); ?></p>
        </div>

        <div style="display: table; width: 100%; margin-bottom: 15px;">
            <div style="display: table-cell; width: 50%;">
                <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; margin: 0 0 5px 0;">Date</p>
                <p style="color: #ffffff; font-size: 14px; margin: 0; font-weight: bold;"><?php echo date('M d, Y', strtotime($date)); ?></p>
            </div>
            <div style="display: table-cell; width: 50%;">
                <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; margin: 0 0 5px 0;">Payment</p>
                <p style="color: #ffffff; font-size: 14px; margin: 0; font-weight: bold; text-transform: capitalize;"><?php echo htmlspecialchars($paymentMethod); ?></p>
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; margin: 0 0 5px 0;">Slots</p>
            <p style="color: #ffffff; font-size: 14px; margin: 0; font-weight: bold;"><?php echo htmlspecialchars($slots); ?></p>
        </div>

        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px; margin-top: 5px;">
            <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; margin: 0 0 5px 0;">Amount Paid</p>
            <p style="color: #10b981; font-size: 20px; margin: 0; font-weight: bold;">₹<?php echo number_format($amount); ?></p>
        </div>
    </div>
    
    <p style="color: #9ca3af; font-size: 14px; text-align: center; margin: 0;">Thank you for playing with Playora.</p>
    <?php
    return ob_get_clean();
}
?>
