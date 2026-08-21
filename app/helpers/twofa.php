<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

require_once ROOT_PATH . '/vendor/autoload.php';

use OTPHP\TOTP;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use ParagonIE\ConstantTime\Base32;

/**
 * Generate a new random secret for 2FA
 * @return string Base32 encoded secret (32 chars)
 */
function twofa_generate_secret(): string {
    // Generate 20 random bytes and encode to Base32
    $randomBytes = random_bytes(20);
    return trim(Base32::encodeUpper($randomBytes), '=');
}

/**
 * Verifikasi kode TOTP 6-digit dari Google Authenticator
 * @param string $secret
 * @param string $code
 * @return bool
 */
function twofa_verify_code(string $secret, string $code): bool {
    try {
        $totp = TOTP::createFromSecret($secret);
        // Window = 1 berarti mentolerir 1 interval (30 detik) ke depan atau ke belakang
        return $totp->verify($code, null, 1);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Generate QR Code URI (Data URI Base64 PNG)
 * @param string $secret Base32 secret
 * @param string $username Username untuk label
 * @param string $issuer Nama aplikasi
 * @return string Data URI (data:image/png;base64,...)
 */
function twofa_get_qr_code_data_uri(string $secret, string $username, string $issuer = 'SKIN-ITG'): string {
    $totp = TOTP::createFromSecret($secret);
    $totp->setLabel($username);
    $totp->setIssuer($issuer);
    
    $uri = $totp->getProvisioningUri();

    $result = Builder::create()
        ->writer(new PngWriter())
        ->writerOptions([])
        ->data($uri)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::High)
        ->size(300)
        ->margin(10)
        ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
        ->build();

    return $result->getDataUri();
}

/**
 * Generate 8 backup codes (8 karakter alphanumerik)
 * @return array
 */
function twofa_generate_backup_codes(): array {
    $codes = [];
    for ($i = 0; $i < 8; $i++) {
        $codes[] = substr(bin2hex(random_bytes(4)), 0, 8);
    }
    return $codes;
}

/**
 * Verifikasi dan konsumsi (hapus) backup code jika cocok
 * @param mysqli $conn
 * @param int $userId
 * @param string $code Input dari user
 * @return bool
 */
function twofa_verify_backup_code(mysqli $conn, int $userId, string $code): bool {
    $stmt = $conn->prepare("SELECT twofa_backup_codes FROM users WHERE id_user = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if (!$result || empty($result['twofa_backup_codes'])) return false;
    
    $codes = json_decode($result['twofa_backup_codes'], true);
    if (!is_array($codes)) return false;
    
    // Cari code (case-insensitive)
    $codeLower = strtolower(trim($code));
    $foundIndex = -1;
    
    foreach ($codes as $idx => $bc) {
        if (strtolower($bc) === $codeLower) {
            $foundIndex = $idx;
            break;
        }
    }
    
    // Jika ketemu, hapus code tersebut dan update database
    if ($foundIndex !== -1) {
        unset($codes[$foundIndex]);
        $codes = array_values($codes); // Re-index array
        $newJson = json_encode($codes);
        
        $stmtUpdate = $conn->prepare("UPDATE users SET twofa_backup_codes = ? WHERE id_user = ?");
        $stmtUpdate->bind_param("si", $newJson, $userId);
        $stmtUpdate->execute();
        
        return true;
    }
    
    return false;
}
