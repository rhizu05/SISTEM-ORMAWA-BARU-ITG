<?php
/**
 * Migrasi: Tambahkan nilai 'Ditolak Bendahara' ke enum kolom `status`
 * pada tabel `pengajuan`.
 *
 * Latar belakang:
 * - BendaharaController.php:36 menetapkan status 'Ditolak Bendahara', namun
 *   nilai tersebut tidak ada di ENUM, sehingga insert berpotensi gagal (strict)
 *   atau tersimpan kosong (non-strict).
 *
 * Cara pakai:
 *   - Pastikan config.php tersedia di root (salin dari config.example.php).
 *   - Jalankan: php scripts/update_enum_ditolak_bendahara.php
 *
 * Catatan audit:
 *   - Sebelum ALTER, script menampilkan jumlah baris dengan status kosong/di luar
 *     enum yang mungkin perlu ditinjau (data historis).
 *   - ALTER ENUM tidak menghapus nilai lain; hanya menambah nilai baru.
 */
require_once __DIR__ . '/../config.php';

$check = $conn->query("SELECT COUNT(*) AS jml FROM pengajuan WHERE TRIM(LOWER(status)) = ''");
$invalid = 0;
if ($check) {
    $invalid = (int) $check->fetch_assoc()['jml'];
}
echo "Baris dengan status kosong (investigasi historis): " . $invalid . PHP_EOL;

$sql = "ALTER TABLE pengajuan MODIFY COLUMN `status` ENUM(
  'Draft',
  'Diajukan Ke BEM',
  'Ditolak BEM',
  'Diajukan Ke BPM',
  'Ditolak BPM',
  'Verifikasi BKKH',
  'Ditolak BKKH',
  'Verifikasi WR3',
  'Ditolak WR3',
  'Disetujui WR3, Siap Diajukan ke Bendahara',
  'Diajukan ke Bendahara',
  'Dana Cair',
  'Ditolak Bendahara',
  'LPJ Diajukan',
  'LPJ Ditolak BKKH',
  'LPJ Diverifikasi',
  'Selesai'
) NOT NULL DEFAULT 'Draft'";

if ($conn->query($sql)) {
    echo "OK: enum kolom status pengajuan diperbarui (menambahkan 'Ditolak Bendahara')." . PHP_EOL;
} else {
    echo "ERROR: " . $conn->error . PHP_EOL;
    exit(1);
}