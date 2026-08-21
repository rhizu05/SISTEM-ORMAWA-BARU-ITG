<?php
$conn = new mysqli('localhost', 'root', '', 'db_pengajuan', 3306);
if ($conn->connect_error) die("Koneksi gagal");

$sql = "ALTER TABLE `users` 
  ADD COLUMN `twofa_secret` VARCHAR(255) NULL AFTER `role`,
  ADD COLUMN `twofa_enabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `twofa_secret`,
  ADD COLUMN `twofa_backup_codes` JSON NULL AFTER `twofa_enabled`,
  ADD COLUMN `twofa_confirmed_at` DATETIME NULL AFTER `twofa_backup_codes`";

if ($conn->query($sql)) {
    echo "Kolom 2FA berhasil ditambahkan ke tabel users.\n";
} else {
    echo "Gagal/Sudah ada: " . $conn->error . "\n";
}
