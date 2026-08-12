<?php
require_once __DIR__ . '/../config.php';
$sql = "CREATE TABLE IF NOT EXISTS regulasi (
    id_regulasi INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    file_path VARCHAR(255),
    kategori ENUM('Undang-Undang', 'Pengumuman', 'Pedoman') DEFAULT 'Undang-Undang',
    tgl_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_user_upload INT
)";
if ($conn->query($sql)) {
    echo "Table 'regulasi' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}
?>
