<?php
require_once __DIR__ . '/../config.php';

$sql = "CREATE TABLE IF NOT EXISTS aspirasi (
    id_aspirasi INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelapor VARCHAR(100) DEFAULT 'Anonim',
    email_pelapor VARCHAR(100),
    kategori ENUM('Fasilitas', 'Layanan Kampus', 'Ormawa', 'Lainnya') NOT NULL,
    subjek VARCHAR(255),
    isi_aspirasi TEXT NOT NULL,
    tanggal_masuk TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Diterima', 'Ditindaklanjuti', 'Selesai') DEFAULT 'Pending',
    tanggapan_bpm TEXT
)";

if ($conn->query($sql)) {
    echo "Table 'aspirasi' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}
?>
