<?php
require_once __DIR__ . '/../config.php';

$sql1 = "CREATE TABLE IF NOT EXISTS proposal_otomatis (
    id_proposal INT AUTO_INCREMENT PRIMARY KEY,
    id_user_ormawa INT NOT NULL,
    nama_kegiatan VARCHAR(255) NOT NULL,
    latar_belakang TEXT,
    tujuan TEXT,
    sasaran TEXT,
    penutup TEXT,
    tgl_dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user_ormawa) REFERENCES users(id_user) ON DELETE CASCADE
)";

$sql2 = "CREATE TABLE IF NOT EXISTS proposal_rab (
    id_rab INT AUTO_INCREMENT PRIMARY KEY,
    id_proposal INT NOT NULL,
    rincian VARCHAR(255) NOT NULL,
    volume INT NOT NULL,
    satuan VARCHAR(50),
    harga_satuan DECIMAL(15,2) NOT NULL,
    total_harga DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (id_proposal) REFERENCES proposal_otomatis(id_proposal) ON DELETE CASCADE
)";

$sql3 = "CREATE TABLE IF NOT EXISTS proposal_panitia (
    id_panitia INT AUTO_INCREMENT PRIMARY KEY,
    id_proposal INT NOT NULL,
    jabatan VARCHAR(100) NOT NULL,
    nama_mahasiswa VARCHAR(255) NOT NULL,
    nim VARCHAR(20),
    FOREIGN KEY (id_proposal) REFERENCES proposal_otomatis(id_proposal) ON DELETE CASCADE
)";

if ($conn->query($sql1) && $conn->query($sql2) && $conn->query($sql3)) {
    echo "Tabel proposal otomatis berhasil dibuat.\n";
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
