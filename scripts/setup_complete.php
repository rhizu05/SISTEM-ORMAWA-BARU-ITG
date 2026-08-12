<?php
/**
 * File: setup_complete.php
 * Deskripsi: Setup otomatis Sistem Keuangan.
 *
 * Cara pakai:
 * 1. Salin config.example.php menjadi config.php dan sesuaikan kredensial DB.
 * 2. Akses via browser: http://<domain>/scripts/setup_complete.php
 *    atau CLI: php scripts/setup_complete.php
 *
 * Script ini akan:
 * 1. Membuat database jika belum ada
 * 2. Import db_pengajuan.sql (semua tabel + data demo)
 * 3. Membuat folder uploads yang diperlukan
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$config_path = __DIR__ . '/../config.php';
if (!file_exists($config_path)) {
    die(
        "GAGAL: config.php tidak ditemukan.\n" .
        "Salin config.example.php menjadi config.php dan sesuaikan kredensial.\n"
    );
}

define('APP_RUNNING', true);
define('ROOT_PATH', realpath(__DIR__ . '/..'));

require_once $config_path;

echo "==============================================\n";
echo "  SETUP LENGKAP SISTEM KEUANGAN\n";
echo "==============================================\n\n";

// --- Step 1: Buat database jika belum ada ---
echo "[1/3] Membuat database " . DB_NAME . "...\n";
$conn_init = new mysqli(DB_HOST, DB_USER, DB_PASS, '', DB_PORT);
if ($conn_init->connect_error) {
    die("GAGAL: Tidak bisa konek ke MySQL: " . $conn_init->connect_error . "\n");
}
$conn_init->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
if ($conn_init->error) {
    die("GAGAL membuat database: " . $conn_init->error . "\n");
}
echo "  OK Database " . DB_NAME . " siap.\n\n";
$conn_init->close();

// --- Step 2: Import SQL schema + data demo ---
echo "[2/3] Import schema dan data dari db_pengajuan.sql...\n";
$sql_file = __DIR__ . '/db_pengajuan.sql';
if (!file_exists($sql_file)) {
    die("GAGAL: File db_pengajuan.sql tidak ditemukan di folder scripts/.\n");
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die("GAGAL: " . $conn->connect_error . "\n");
}

$tables_exist = $conn->query("SHOW TABLES LIKE 'users'");
if ($tables_exist->num_rows > 0) {
    echo "  INFO: Tabel sudah ada, import dilewati.\n";
    echo "        Hapus database dan jalankan ulang untuk fresh install.\n\n";
} else {
    $sql_content = file_get_contents($sql_file);
    $conn->multi_query($sql_content);
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());

    if ($conn->error) {
        die("GAGAL saat import SQL: " . $conn->error . "\n");
    }
    echo "  OK Semua tabel dan data demo berhasil di-import.\n\n";
}

// --- Step 3: Buat folder uploads ---
echo "[3/3] Membuat folder uploads...\n";
$folders = [
    'uploads/proposal',
    'uploads/lpj',
    'uploads/profil',
    'uploads/sistem',
    'uploads/pengumuman',
    'uploads/regulasi',
    'uploads/surat',
];
foreach ($folders as $folder) {
    $full_path = ROOT_PATH . '/' . $folder;
    if (!is_dir($full_path)) {
        mkdir($full_path, 0777, true);
        echo "  + Folder $folder dibuat\n";
    } else {
        echo "  - Folder $folder sudah ada\n";
    }
}
echo "\n";

// --- Ringkasan ---
echo "==============================================\n";
echo "  SETUP SELESAI!\n";
echo "==============================================\n\n";

$users = $conn->query("SELECT username, role, status_akun FROM users ORDER BY id_user");
if ($users && $users->num_rows > 0) {
    echo "Daftar user yang tersedia:\n";
    echo str_repeat('-', 55) . "\n";
    echo sprintf("%-22s %-18s %-10s\n", "Username", "Role", "Status");
    echo str_repeat('-', 55) . "\n";
    while ($u = $users->fetch_assoc()) {
        echo sprintf("%-22s %-18s %-10s\n", $u['username'], $u['role'], $u['status_akun']);
    }
    echo str_repeat('-', 55) . "\n";
}

echo "\nPassword semua user demo: password123\n";
echo "\nAkses aplikasi:\n";
echo "  http://" . (($_SERVER['HTTP_HOST'] ?? 'sistem_keuangan.test')) . "/index.php?page=login\n\n";

$conn->close();
?>
