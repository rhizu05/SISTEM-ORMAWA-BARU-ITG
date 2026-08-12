<?php
require_once __DIR__ . '/../config.php';

$sql1 = "CREATE TABLE IF NOT EXISTS master_ruangan (
  id_ruangan INT AUTO_INCREMENT PRIMARY KEY,
  nama_ruangan VARCHAR(100) NOT NULL,
  kapasitas INT NOT NULL,
  fasilitas TEXT,
  status_aktif ENUM('aktif', 'nonaktif') DEFAULT 'aktif'
)";

$sql2 = "CREATE TABLE IF NOT EXISTS peminjaman_tempat (
  id_peminjaman INT AUTO_INCREMENT PRIMARY KEY,
  id_user_ormawa INT NOT NULL,
  id_ruangan INT NOT NULL,
  tgl_mulai DATE NOT NULL,
  tgl_selesai DATE NOT NULL,
  jam_mulai TIME NOT NULL,
  jam_selesai TIME NOT NULL,
  nama_kegiatan VARCHAR(255) NOT NULL,
  deskripsi_kegiatan TEXT,
  status ENUM('Menunggu BKKH', 'Disetujui', 'Ditolak') DEFAULT 'Menunggu BKKH',
  catatan_penolakan TEXT,
  tgl_pengajuan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_user_ormawa) REFERENCES users(id_user) ON DELETE CASCADE,
  FOREIGN KEY (id_ruangan) REFERENCES master_ruangan(id_ruangan) ON DELETE CASCADE
)";

$sql3 = "INSERT INTO master_ruangan (nama_ruangan, kapasitas, fasilitas) VALUES 
('Aula Gedung Rektorat', 500, 'AC, Sound System, Proyektor, Kursi'),
('Ruang Rapat Mahasiswa', 30, 'AC, Meja Rapat, Papan Tulis, Proyektor'),
('Lapangan Olahraga Utama', 1000, 'Garis Lapangan, Tribun, Lampu Sorot')
ON DUPLICATE KEY UPDATE nama_ruangan=nama_ruangan";

if ($conn->query($sql1) === TRUE) {
    echo "Tabel master_ruangan berhasil dibuat.\n";
} else {
    echo "Error master_ruangan: " . $conn->error . "\n";
}

if ($conn->query($sql2) === TRUE) {
    echo "Tabel peminjaman_tempat berhasil dibuat.\n";
} else {
    echo "Error peminjaman_tempat: " . $conn->error . "\n";
}

if ($conn->query($sql3) === TRUE) {
    echo "Data dummy master_ruangan berhasil dimasukkan.\n";
} else {
    echo "Error insert dummy: " . $conn->error . "\n";
}

$conn->close();
?>
