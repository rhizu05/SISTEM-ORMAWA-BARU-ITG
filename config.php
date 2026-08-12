<?php
/**
 * File: config.php
 * Deskripsi: Pusat konfigurasi, inisialisasi, dan bootstrap aplikasi.
 */

define('APP_RUNNING', true);
define('ROOT_PATH', __DIR__);

$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? 80) == 443);
$protocol = $is_https ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base     = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
define('BASE_URL', $protocol . '://' . $host . $base);

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_pengajuan');
define('DB_PORT', 3306);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

require_once ROOT_PATH . '/app/helpers/functions.php';

initialize_session();
?>
