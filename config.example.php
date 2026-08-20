<?php
/**
 * File: config.example.php
 *
 * Cara penggunaan:
 * 1. Salin file ini menjadi config.php
 *    cp config.example.php config.php
 * 2. Sesuaikan nilai DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT
 *    dengan konfigurasi MySQL/MariaDB lokal kamu.
 * 3. Jangan commit config.php ke git (sudah ada di .gitignore).
 */

define('APP_RUNNING', true);
define('ROOT_PATH', __DIR__);

$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? 80) == 443);
$protocol = $is_https ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base     = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
define('BASE_URL', $protocol . '://' . $host . $base);

// --- Konfigurasi Database ---
define('DB_HOST', 'localhost');   // Biasanya 'localhost'
define('DB_USER', 'root');        // Username MySQL kamu
define('DB_PASS', '');            // Password MySQL kamu (kosong jika pakai Laragon default)
define('DB_NAME', 'db_pengajuan');
define('DB_PORT', 3306);          // Port MySQL (3306 default, Laragon bisa 3308)

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// --- Phase 4: Session Security Configuration ---
define('SESSION_TIMEOUT_MINUTES', 30);
define('SESSION_COOKIE_LIFETIME', 7200);

require_once ROOT_PATH . '/app/helpers/functions.php';
require_once ROOT_PATH . '/app/helpers/session.php'; // NEW

// Gunakan secure session initialization
session_start_secure();
?>
