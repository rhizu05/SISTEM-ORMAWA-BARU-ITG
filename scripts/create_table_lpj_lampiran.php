<?php
require_once __DIR__ . '/../config.php';

$sql = "CREATE TABLE IF NOT EXISTS lpj_lampiran (
    id_lampiran INT AUTO_INCREMENT PRIMARY KEY,
    id_lpj INT NOT NULL,
    nama_file VARCHAR(255) NOT NULL,
    tipe_lampiran ENUM('Kwitansi', 'Dokumentasi') NOT NULL,
    keterangan VARCHAR(255),
    FOREIGN KEY (id_lpj) REFERENCES lpj_otomatis(id_lpj) ON DELETE CASCADE
)";

if ($conn->query($sql)) {
    echo "Table lpj_lampiran created successfully.\n";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
