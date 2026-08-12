<?php
require_once __DIR__ . '/../config.php';

$sql = "CREATE TABLE IF NOT EXISTS surat_otomatis (
    id_surat INT AUTO_INCREMENT PRIMARY KEY,
    id_user_ormawa INT NOT NULL,
    jenis_surat ENUM('Undangan', 'Tugas', 'Permohonan', 'Keterangan') NOT NULL,
    nomor_surat VARCHAR(100),
    perihal VARCHAR(255),
    tujuan_surat VARCHAR(255),
    isi_json TEXT NOT NULL, -- Menyimpan data dinamis seperti nama, jabatan, waktu, tempat dalam format JSON
    tgl_dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ttd_key VARCHAR(20) DEFAULT 'ketua',
    ttd_nama_kustom VARCHAR(100),
    ttd_file_kustom VARCHAR(255),
    status ENUM('Draft', 'Final') DEFAULT 'Final',
    FOREIGN KEY (id_user_ormawa) REFERENCES users(id_user) ON DELETE CASCADE
)";

if ($conn->query($sql)) {
    echo "Table surat_otomatis created successfully.\n";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
