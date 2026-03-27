<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

/**
 * Generate a QR code for a booking and save it to the tickets directory.
 * Returns the relative path to the generated QR code image.
 */
function generateBookingQR($bookingId, $token) {
    try {
        $filename = 'qr_' . $bookingId . '.svg';
        $ticketsDir = __DIR__ . '/../tickets';
        $filepath = $ticketsDir . '/' . $filename;
        
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $domain = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $publicPath = $protocol . $domain . '/play/tickets/' . $filename;

        // Only generates the QR if it does not already exist
        if (file_exists($filepath)) {
            return $publicPath;
        }
        
        $verifyUrl = $protocol . $domain . "/play/verify-ticket.php?id=" . urlencode($bookingId) . "&token=" . urlencode($token);

        // We use SvgWriter because the server's GD extension is disabled.
        // SVG is actually better for QR codes as it's vector-based (sharper).
        $builder = new Builder(
            writer: new \Endroid\QrCode\Writer\SvgWriter(),
            data: $verifyUrl,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        );

        $result = $builder->build();

        if (!is_dir($ticketsDir)) {
            mkdir($ticketsDir, 0755, true);
        }
        
        $result->saveToFile($filepath);

        return $publicPath;
    } catch (\Throwable $e) {
        error_log("QR Code Error: " . $e->getMessage());
        return null;
    }
}
?>
