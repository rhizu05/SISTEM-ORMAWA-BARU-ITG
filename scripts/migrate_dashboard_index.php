<?php
$conn = new mysqli('localhost', 'root', '', 'db_pengajuan', 3306);
if ($conn->connect_error) die("Koneksi gagal");

$queries = [
    "ALTER TABLE `pengajuan` ADD INDEX `idx_status_tanggal` (`status`, `tanggal_pengajuan`)",
    "ALTER TABLE `dana` ADD INDEX `idx_tanggal_cair` (`tanggal_cair`)",
    "ALTER TABLE `pengajuan` ADD INDEX `idx_user_status` (`id_user_ormawa`, `status`)"
];

foreach ($queries as $q) {
    if ($conn->query($q)) {
        echo "✅ Query berhasil: $q\n";
    } else {
        echo "❌ Gagal/Sudah ada: " . $conn->error . "\n";
    }
}
