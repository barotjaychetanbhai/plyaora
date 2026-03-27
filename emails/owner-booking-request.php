<?php
function getOwnerBookingRequestEmail($ownerName, $playerName, $turfName, $date, $slots, $potentialEarning) {
    ob_start();
    ?>
    <div style="text-align: center; margin-bottom: 30px;">
        <span style="display: inline-block; padding: 8px 16px; background-color: #f59e0b; color: #000; border-radius: 8px; font-weight: bold; font-size: 14px; letter-spacing: 1px; text-transform: uppercase;">New Cash Request</span>
    </div>
    
    <h2 style="color: #ffffff; font-size: 20px; margin-bottom: 10px; font-weight: 600;">Hello <?php echo htmlspecialchars($ownerName); ?>,</h2>
    <p style="color: #9ca3af; font-size: 15px; margin-bottom: 25px; line-height: 1.5;">A player has requested a cash booking for your turf. Please log in to your dashboard to review this request.</p>
    
    <div style="background-color: #1a1a1f; border-radius: 12px; padding: 25px; margin-bottom: 30px;">
        <div style="margin-bottom: 15px;">
            <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; margin: 0 0 5px 0;">Turf Name</p>
            <p style="color: #ffffff; font-size: 16px; margin: 0; font-weight: bold;"><?php echo htmlspecialchars($turfName); ?></p>
        </div>

        <div style="margin-bottom: 15px;">
            <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; margin: 0 0 5px 0;">Player Name</p>
            <p style="color: #ffffff; font-size: 16px; margin: 0; font-weight: bold;"><?php echo htmlspecialchars($playerName); ?></p>
        </div>

        <div style="display: table; width: 100%; margin-bottom: 15px;">
            <div style="display: table-cell; width: 100%;">
                <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; margin: 0 0 5px 0;">Date</p>
                <p style="color: #ffffff; font-size: 14px; margin: 0; font-weight: bold;"><?php echo date('M d, Y', strtotime($date)); ?></p>
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; margin: 0 0 5px 0;">Requested Slots</p>
            <p style="color: #ffffff; font-size: 14px; margin: 0; font-weight: bold;"><?php echo htmlspecialchars($slots); ?></p>
        </div>

        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px; margin-top: 5px;">
            <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; margin: 0 0 5px 0;">Potential Earnings</p>
            <p style="color: #10b981; font-size: 20px; margin: 0; font-weight: bold;">₹<?php echo number_format($potentialEarning); ?></p>
        </div>
    </div>
    
    <p style="color: #9ca3af; font-size: 14px; text-align: center; margin: 0;">Log in to accept or reject this request.</p>
    <?php
    return ob_get_clean();
}
?>
