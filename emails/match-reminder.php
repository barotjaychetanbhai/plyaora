<?php
function getMatchReminderEmail($playerName, $turfName, $time, $date) {
    ob_start();
    ?>
    <div style="text-align: center; margin-bottom: 30px;">
        <span style="display: inline-block; padding: 8px 16px; background-color: #fca5a5; color: #b91c1c; border-radius: 8px; font-weight: bold; font-size: 14px; letter-spacing: 1px; text-transform: uppercase;">Match Reminder</span>
    </div>
    
    <h2 style="color: #ffffff; font-size: 20px; margin-bottom: 10px; font-weight: 600;">Hello <?php echo htmlspecialchars($playerName); ?>,</h2>
    <p style="color: #9ca3af; font-size: 15px; margin-bottom: 25px; line-height: 1.5;">Reminder: Your Playora Match Is Coming Up very soon!</p>
    
    <div style="background-color: #1a1a1f; border-radius: 12px; padding: 25px; margin-bottom: 30px;">
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
                <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; margin: 0 0 5px 0;">Time</p>
                <p style="color: #ffffff; font-size: 14px; margin: 0; font-weight: bold;"><?php echo htmlspecialchars($time); ?></p>
            </div>
        </div>
    </div>
    
    <p style="color: #9ca3af; font-size: 14px; text-align: center; margin: 0;">See you on the field.</p>
    <?php
    return ob_get_clean();
}
?>
