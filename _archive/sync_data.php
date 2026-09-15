<?php
require_once 'config.php';
// Otomatis verifikasi tahap 1 (BKHM) agar muncul di Sarpras
$conn->query("UPDATE peminjaman_tempat SET status_bkhm = 'Diverifikasi' WHERE status_bkhm = 'Pending'");
$conn->query("UPDATE peminjaman_barang SET status_bkhm = 'Diverifikasi' WHERE status_bkhm = 'Pending'");
echo "Sync Completed: Data is now waiting for Sarpras approval.";
?>
