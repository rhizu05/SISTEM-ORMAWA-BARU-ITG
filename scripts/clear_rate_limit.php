<?php
$conn = new mysqli('localhost', 'root', '', 'db_pengajuan', 3306);
if ($conn->connect_error) die("Koneksi gagal");

// Hapus log percobaan gagal
if ($conn->query("DELETE FROM login_attempts WHERE success = 0")) {
    echo "Blokir rate limit berhasil dihapus! Anda bebas login.\n";
} else {
    echo "Gagal: " . $conn->error;
}
