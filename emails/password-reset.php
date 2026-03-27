<?php
function getPasswordResetEmail($name, $resetLink) {
    ob_start();
    ?>
    <div style="text-align: center; margin-bottom: 30px;">
        <span style="display: inline-block; padding: 8px 16px; background-color: #3b82f6; color: #fff; border-radius: 8px; font-weight: bold; font-size: 14px; letter-spacing: 1px; text-transform: uppercase;">Password Reset Request</span>
    </div>
    
    <h2 style="color: #ffffff; font-size: 20px; margin-bottom: 10px; font-weight: 600;">Hello <?php echo htmlspecialchars($name); ?>,</h2>
    <p style="color: #9ca3af; font-size: 15px; margin-bottom: 25px; line-height: 1.5;">We received a request to reset your password for your Playora account. If you did not make this request, you can safely ignore this email.</p>
    
    <div style="background-color: #1a1a1f; border-radius: 12px; padding: 25px; margin-bottom: 30px; text-align: center;">
        <p style="color: #ffffff; font-size: 16px; margin-bottom: 15px;">Click the button below to reset your password:</p>
        <a href="<?php echo htmlspecialchars($resetLink); ?>" style="display: inline-block; padding: 12px 24px; background-color: #10b981; color: #000; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px;">Reset Password</a>
        <p style="color: #6b7280; font-size: 11px; margin-top: 15px; word-break: break-all;">Or copy this link: <br/><a href="<?php echo htmlspecialchars($resetLink); ?>" style="color: #10b981;"><?php echo htmlspecialchars($resetLink); ?></a></p>
    </div>
    
    <p style="color: #9ca3af; font-size: 14px; text-align: center; margin: 0;">This link will expire in 24 hours.</p>
    <?php
    return ob_get_clean();
}
?>
