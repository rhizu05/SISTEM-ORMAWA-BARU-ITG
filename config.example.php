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
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_pengajuan');
define('DB_PORT', 3306);

// --- Konfigurasi SMTP Email (Brevo) ---
define('SMTP_HOST', 'smtp-relay.brevo.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_brevo_email@domain.com'); // Ganti dengan Login email Brevo
define('SMTP_PASS', 'your_brevo_smtp_key');         // Ganti dengan SMTP Master Password Brevo
define('MAIL_FROM_ADDRESS', 'noreply@itg.ac.id');
define('MAIL_FROM_NAME', 'Sistem Kemahasiswaan ITG');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// --- Phase 4: Session Security Configuration ---
define('SESSION_TIMEOUT_MINUTES', 30);
define('SESSION_COOKIE_LIFETIME', 7200);

require_once ROOT_PATH . '/app/helpers/functions.php';
require_once ROOT_PATH . '/app/helpers/session.php'; // NEW
require_once ROOT_PATH . '/app/helpers/twofa.php'; // NEW 2FA
require_once ROOT_PATH . '/app/helpers/mailer.php'; // NEW Email

// Gunakan secure session initialization
session_start_secure();
?>
