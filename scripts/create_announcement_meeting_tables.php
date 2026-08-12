<?php
require_once __DIR__ . '/../config.php';

// 1. Create table pengumuman
$sql_pengumuman = "CREATE TABLE IF NOT EXISTS pengumuman (
    id_pengumuman INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    file_lampiran VARCHAR(255),
    tanggal_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_user_upload INT NOT NULL,
    status ENUM('Aktif', 'Arsip') DEFAULT 'Aktif',
    FOREIGN KEY (id_user_upload) REFERENCES users(id_user) ON DELETE CASCADE
)";

// 2. Create table jadwal_rapat
$sql_rapat = "CREATE TABLE IF NOT EXISTS jadwal_rapat (
    id_rapat INT AUTO_INCREMENT PRIMARY KEY,
    judul_rapat VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    tanggal_rapat DATE NOT NULL,
    jam_rapat TIME NOT NULL,
    lokasi VARCHAR(255) NOT NULL,
    link_meeting VARCHAR(255),
    id_penyelenggara INT NOT NULL,
    target_peserta VARCHAR(255), -- Comma separated roles like 'ormawa,bpm'
    status ENUM('Direncanakan', 'Selesai', 'Dibatalkan') DEFAULT 'Direncanakan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_penyelenggara) REFERENCES users(id_user) ON DELETE CASCADE
)";

if ($conn->query($sql_pengumuman) === TRUE) {
    echo "Table pengumuman created successfully.<br>";
} else {
    echo "Error creating table pengumuman: " . $conn->error . "<br>";
}

if ($conn->query($sql_rapat) === TRUE) {
    echo "Table jadwal_rapat created successfully.<br>";
} else {
    echo "Error creating table jadwal_rapat: " . $conn->error . "<br>";
}

$conn->close();
?>
