<?php
$conn = new mysqli('localhost', 'root', '', 'db_pengajuan', 3306);
if ($conn->connect_error) die("Koneksi gagal");

$sql1 = "ALTER TABLE `notifikasi` ADD COLUMN `terkirim_sse` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status_baca`";
$sql2 = "ALTER TABLE `notifikasi` ADD INDEX `idx_terkirim` (`id_user`, `terkirim_sse`)";

if ($conn->query($sql1)) { echo "Kolom terkirim_sse ditambahkan.\n"; } else { echo "Gagal/Sudah ada: " . $conn->error . "\n"; }
if ($conn->query($sql2)) { echo "Index idx_terkirim ditambahkan.\n"; } else { echo "Gagal/Sudah ada: " . $conn->error . "\n"; }
