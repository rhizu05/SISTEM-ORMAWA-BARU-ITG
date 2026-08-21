<?php
/**
 * Simulasi Integrasi Setup 2FA Flow
 */
define('APP_RUNNING', true);
define('ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . '/vendor/autoload.php';
require ROOT_PATH . '/app/helpers/twofa.php';

// Mock $_SESSION
$_SESSION = [];
$user_id = 1;

// 1. User mengakses halaman setup_2fa untuk pertama kali
if (!isset($_SESSION['setup_2fa_secret'])) {
    $_SESSION['setup_2fa_secret'] = twofa_generate_secret();
    $_SESSION['setup_2fa_backups'] = twofa_generate_backup_codes();
}

$secret = $_SESSION['setup_2fa_secret'];
echo "1. Secret Digenerate : " . $secret . "\n";

// 2. Tampilkan QR
$qr_uri = twofa_get_qr_code_data_uri($secret, 'admin');
echo "2. QR URI Digenerate : " . substr($qr_uri, 0, 50) . "...\n";

// 3. User melakukan scan dan memasukkan kode. 
// Karena kita tidak bisa scan sekarang, kita generate valid kode untuk saat ini
$totp = \OTPHP\TOTP::createFromSecret($secret);
$valid_code = $totp->now();
echo "3. User menginput kode : " . $valid_code . "\n";

// 4. Verifikasi Input
if (twofa_verify_code($secret, $valid_code)) {
    echo "4. ✅ VERIFIKASI SUKSES!\n";
    echo "   -> Sistem akan menyimpan secret dan backup codes ke database.\n";
    echo "   -> Backup Codes:\n";
    print_r($_SESSION['setup_2fa_backups']);
} else {
    echo "4. ❌ VERIFIKASI GAGAL!\n";
}

echo "SIMULASI SELESAI.\n";
