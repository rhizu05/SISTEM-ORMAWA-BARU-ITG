<?php
$conn = new mysqli('localhost', 'root', '', 'db_pengajuan', 3306);
if ($conn->connect_error) die("Koneksi gagal");

$sql = "ALTER TABLE `users` 
  ADD COLUMN `reset_token` VARCHAR(255) NULL AFTER `twofa_confirmed_at`,
  ADD COLUMN `reset_expires_at` DATETIME NULL AFTER `reset_token`,
  ADD INDEX `idx_reset_token` (`reset_token`)";

if ($conn->query($sql)) {
    echo "Kolom password reset berhasil ditambahkan.\n";
} else {
    echo "Gagal/Sudah ada: " . $conn->error . "\n";
}
