<?php
$conn = new mysqli('localhost', 'root', '', 'db_pengajuan', 3306);
if ($conn->connect_error) die("Koneksi gagal");

$sql = "ALTER TABLE `users` ADD COLUMN `email` VARCHAR(100) NULL AFTER `nama_lengkap`";
if ($conn->query($sql)) {
    echo "Kolom email berhasil ditambahkan.\n";
    // Setup dummy email untuk admin
    $conn->query("UPDATE users SET email = 'admin@yopmail.com' WHERE username = 'admin'");
} else {
    echo "Gagal/Sudah ada: " . $conn->error . "\n";
}