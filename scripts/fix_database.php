<?php
require_once __DIR__ . '/../config.php';

echo "Auditing Database...\n";

// 1. Audit peminjaman_tempat
$res = $conn->query("SHOW COLUMNS FROM peminjaman_tempat");
$cols = [];
while($f = $res->fetch_assoc()) $cols[] = $f['Field'];

if (!in_array('status_bkkh', $cols)) {
    echo "Fixing peminjaman_tempat: Adding status_bkkh\n";
    $conn->query("ALTER TABLE peminjaman_tempat ADD COLUMN status_bkkh ENUM('Pending', 'Diverifikasi', 'Ditolak') DEFAULT 'Pending'");
}
if (!in_array('status_sarpras', $cols)) {
    echo "Fixing peminjaman_tempat: Adding status_sarpras\n";
    $conn->query("ALTER TABLE peminjaman_tempat ADD COLUMN status_sarpras ENUM('Pending', 'Disetujui', 'Ditolak') DEFAULT 'Pending'");
}

// 2. Sync 'status' column with stage status if needed
// This ensures old code that looks at 'status' still works
$conn->query("UPDATE peminjaman_tempat SET status = 'Disetujui' WHERE status_sarpras = 'Disetujui'");
$conn->query("UPDATE peminjaman_tempat SET status = 'Ditolak' WHERE status_bkkh = 'Ditolak' OR status_sarpras = 'Ditolak'");
$conn->query("UPDATE peminjaman_tempat SET status = 'Menunggu BKKH' WHERE status_bkkh = 'Pending'");

// 3. Ensure tables exist
$conn->query("CREATE TABLE IF NOT EXISTS master_barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    stok_total INT DEFAULT 0,
    stok_tersedia INT DEFAULT 0,
    status_aktif ENUM('aktif', 'nonaktif') DEFAULT 'aktif'
)");

$conn->query("CREATE TABLE IF NOT EXISTS peminjaman_barang (
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
)");

// 4. Update users role ENUM if it's an ENUM (check first)
$res_u = $conn->query("SHOW COLUMNS FROM users LIKE 'role'");
$u_role = $res_u->fetch_assoc();
if (strpos($u_role['Type'], 'enum') !== false) {
    echo "Updating users role enum...\n";
    $conn->query("ALTER TABLE users MODIFY COLUMN role ENUM('admin','ormawa','bem','bpm','bkh','wr3','bendahara','sarpras','sarpras_barang')");
}

echo "Database Fix Completed.\n";
$conn->close();
?>
