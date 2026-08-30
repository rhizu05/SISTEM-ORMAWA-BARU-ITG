<?php
require_once 'config.php';
// Otomatis verifikasi tahap 1 (BKKH) agar muncul di Sarpras
$conn->query("UPDATE peminjaman_tempat SET status_bkkh = 'Diverifikasi' WHERE status_bkkh = 'Pending'");
$conn->query("UPDATE peminjaman_barang SET status_bkkh = 'Diverifikasi' WHERE status_bkkh = 'Pending'");
echo "Sync Completed: Data is now waiting for Sarpras approval.";
?>
