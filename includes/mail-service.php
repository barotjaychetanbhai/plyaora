<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($to, $subject, $html) {
    if (empty($to)) return false;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        // Securely using environment configs or defaults provided
        $mail->Username   = 'jaybarot79979@gmail.com'; 
        $mail->Password   = 'sikh bhox hykm ktja'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('jaybarot79979@gmail.com', 'Playora Platform');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        
        // Wrap content with Playora branding and dark theme
        $body = '
        <div style="background-color: #030304; padding: 20px; font-family: Helvetica, Arial, sans-serif;">
            <div style="max-width: 600px; margin: 0 auto; background-color: #0f0f11; border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <div style="padding: 30px;">
                    ' . $html . '
                </div>
                <div style="background-color: #0a0a0c; padding: 20px; text-align: center; border-top: 1px solid rgba(255,255,255,0.05);">
                    <p style="color: #10b981; margin: 0 0 5px 0; font-weight: bold; font-size: 16px; letter-spacing: 1px;">PLAYORA PLATFORM</p>
                    <p style="color: #6b7280; font-size: 11px; margin: 0; text-transform: uppercase; letter-spacing: 0.5px;">Automated email notification</p>
                </div>
            </div>
        </div>';

        $mail->Body = $body;

        return $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>
