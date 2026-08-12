<?php
require_once __DIR__ . '/../config.php';

// 1. Tambah Kolom ke peminjaman_tempat untuk alur 2 tahap
$existing = $conn->query("SHOW COLUMNS FROM peminjaman_tempat");
$pt_cols = [];
while ($row = $existing->fetch_assoc()) {
    $pt_cols[] = $row['Field'];
}

if (!in_array('status_bkkh', $pt_cols)) {
    $conn->query("ALTER TABLE peminjaman_tempat ADD COLUMN status_bkkh ENUM('Pending', 'Diverifikasi', 'Ditolak') DEFAULT 'Pending'");
    echo "  + Kolom status_bkkh ditambahkan.\n";
}
if (!in_array('status_sarpras', $pt_cols)) {
    $conn->query("ALTER TABLE peminjaman_tempat ADD COLUMN status_sarpras ENUM('Pending', 'Disetujui', 'Ditolak') DEFAULT 'Pending'");
    echo "  + Kolom status_sarpras ditambahkan.\n";
}
if (!in_array('catatan_sarpras', $pt_cols)) {
    $conn->query("ALTER TABLE peminjaman_tempat ADD COLUMN catatan_sarpras TEXT AFTER catatan_penolakan");
    echo "  + Kolom catatan_sarpras ditambahkan.\n";
}

// 2. Master Barang
$sql_mb = "CREATE TABLE IF NOT EXISTS master_barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    stok_total INT DEFAULT 0,
    stok_tersedia INT DEFAULT 0,
    status_aktif ENUM('aktif', 'nonaktif') DEFAULT 'aktif'
)";
$conn->query($sql_mb);

// 3. Peminjaman Barang
$sql_pb = "CREATE TABLE IF NOT EXISTS peminjaman_barang (
    id_peminjaman_barang INT AUTO_INCREMENT PRIMARY KEY,
    id_user_ormawa INT NOT NULL,
    nama_kegiatan VARCHAR(255) NOT NULL,
    tgl_mulai DATE NOT NULL,
    tgl_selesai DATE NOT NULL,
    kebutuhan_barang TEXT,
    status_bkkh ENUM('Pending', 'Diverifikasi', 'Ditolak') DEFAULT 'Pending',
    status_sarpras ENUM('Pending', 'Disetujui', 'Ditolak') DEFAULT 'Pending',
    catatan_penolakan TEXT,
    tgl_pengajuan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user_ormawa) REFERENCES users(id_user) ON DELETE CASCADE
)";
$conn->query($sql_pb);

echo "Database updated for multi-stage approval and property rental.\n";
$conn->close();
?>
