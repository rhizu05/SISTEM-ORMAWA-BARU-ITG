<?php
define('APP_RUNNING', true);
define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/vendor/autoload.php';
require ROOT_PATH . '/app/helpers/twofa.php';

echo "--- MENGUJI GENERASI SECRET 2FA ---\n";
$secret = twofa_generate_secret();
echo "Generated Secret : " . $secret . "\n";
echo "Panjang String   : " . strlen($secret) . " karakter\n";

echo "\n--- MENGUJI GENERASI BACKUP CODES ---\n";
$backups = twofa_generate_backup_codes();
print_r($backups);

echo "\n--- MENGUJI GENERASI URI ---\n";
$uri = twofa_get_qr_code_data_uri($secret, 'admin_testing');
echo "Data URI (Base64 QR Code) tergenerate sepanjang: " . strlen($uri) . " bytes\n";
if (strpos($uri, 'data:image/png;base64,') === 0) {
    echo "✅ Format QR URI Valid.\n";
} else {
    echo "❌ Format QR URI Tidak Valid!\n";
}
