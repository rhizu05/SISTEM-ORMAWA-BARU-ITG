<?php
require_once __DIR__ . '/../config.php';

// 1. Tambah kolom organigram & TTD ke tabel users
$sql_users = "ALTER TABLE users 
    ADD COLUMN IF NOT EXISTS nama_ketua VARCHAR(100),
    ADD COLUMN IF NOT EXISTS nama_sekretaris VARCHAR(100),
    ADD COLUMN IF NOT EXISTS nama_bendahara VARCHAR(100),
    ADD COLUMN IF NOT EXISTS ttd_ketua VARCHAR(255),
    ADD COLUMN IF NOT EXISTS ttd_sekretaris VARCHAR(255),
    ADD COLUMN IF NOT EXISTS ttd_bendahara VARCHAR(255)";

// 2. Tambah kolom status & id_user_ormawa ke proposal_otomatis jika belum ada
$sql_proposal = "ALTER TABLE proposal_otomatis 
    ADD COLUMN IF NOT EXISTS status ENUM('Draft', 'Final') DEFAULT 'Draft'";

if ($conn->query($sql_users) && $conn->query($sql_proposal)) {
    echo "Database updated successfully.\n";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
