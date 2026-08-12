<?php
/**
 * File: logout.php
 * Deskripsi: Menghancurkan session dan mengeluarkan pengguna dari sistem.
 */

if (!defined('APP_RUNNING')) {
    require_once dirname(__DIR__, 3) . '/config.php';
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Menghapus semua variabel session
$_SESSION = array();

// Menghancurkan session
session_destroy();

// Mengarahkan pengguna kembali ke halaman login
redirect('index.php?page=login');
?>