<?php
require_once __DIR__ . '/../config.php';

$sql = "CREATE TABLE IF NOT EXISTS lpj_otomatis (
    id_lpj INT AUTO_INCREMENT PRIMARY KEY,
    id_user_ormawa INT NOT NULL,
    nama_kegiatan VARCHAR(255) NOT NULL,
    pendahuluan TEXT,
    pelaksanaan_kegiatan TEXT, -- Berisi detail tgl, tempat, jumlah peserta
    hasil_kegiatan TEXT,
    hambatan_kendala TEXT,
    saran_rekomendasi TEXT,
    penutup TEXT,
    tgl_dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ttd_1_key VARCHAR(50) DEFAULT 'ketua',
    ttd_2_key VARCHAR(50) DEFAULT 'sekretaris',
    ttd_3_key VARCHAR(50) DEFAULT 'bendahara',
    ttd_1_file VARCHAR(255),
    ttd_2_file VARCHAR(255),
    ttd_3_file VARCHAR(255),
    status ENUM('Draft', 'Final') DEFAULT 'Final',
    FOREIGN KEY (id_user_ormawa) REFERENCES users(id_user) ON DELETE CASCADE
)";

if ($conn->query($sql)) {
    echo "Table lpj_otomatis created successfully.\n";
} else {
    echo "Error: " . $conn->error;
}

// Table untuk realisasi anggaran di LPJ
$sql2 = "CREATE TABLE IF NOT EXISTS lpj_anggaran (
    id_anggaran INT AUTO_INCREMENT PRIMARY KEY,
    id_lpj INT NOT NULL,
    uraian VARCHAR(255),
    estimasi_dana DECIMAL(15,2),
    realisasi_dana DECIMAL(15,2),
    keterangan VARCHAR(255),
    FOREIGN KEY (id_lpj) REFERENCES lpj_otomatis(id_lpj) ON DELETE CASCADE
)";
$conn->query($sql2);

$conn->close();
?>
