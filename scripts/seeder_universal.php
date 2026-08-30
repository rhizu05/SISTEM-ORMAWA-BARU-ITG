<?php
/**
 * Universal Database Seeder
 * Eksekusi: php scripts/seeder_universal.php
 * Mengisi data pengajuan, dana, peminjaman, dan historinya agar Dashboard semua Role terlihat penuh.
 */
require_once dirname(__DIR__) . '/config.php';

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

echo "=============================================\n";
echo " MULAI UNIVERSAL SEEDER (DASHBOARD & TABEL) \n";
echo "=============================================\n\n";

// 1. Bersihkan tabel operasional (CASCADE di-assume jalan atau urutan delete manual)
echo "1. Membersihkan data lama...\n";
$conn->query("DELETE FROM dana");
$conn->query("DELETE FROM histori_status");
$conn->query("DELETE FROM peminjaman_tempat");
$conn->query("DELETE FROM peminjaman_barang");
$conn->query("DELETE FROM pengajuan");

// 2. Pastikan Ormawa cukup
echo "2. Memastikan user Ormawa cukup...\n";
$ormawas = [
    ['Hima Teknik Informatika ITG', 'hima_tif'],
    ['Hima Sistem Informasi ITG', 'hima_si'],
    ['Hima Teknik Sipil ITG', 'hima_ts'],
    ['Hima Teknik Industri ITG', 'hima_ti'],
    ['Hima Teknik Arsitektur ITG', 'hima_arsi']
];

foreach ($ormawas as $o) {
    $nama = $o[0]; $user = $o[1];
    $cek = $conn->query("SELECT id_user FROM users WHERE username = '$user'");
    if ($cek->num_rows == 0) {
        $pw = password_hash('password123', PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users (nama_lengkap, username, password, role, status_akun) 
                      VALUES ('$nama', '$user', '$pw', 'ormawa', 'aktif')");
    }
}

// 3. Ambil IDs
$ormawa_ids = [];
$res = $conn->query("SELECT id_user FROM users WHERE role = 'ormawa'");
while ($row = $res->fetch_assoc()) { $ormawa_ids[] = $row['id_user']; }

$bkh_id = 4; // Default BKKH user ID if exists, fallback to 1 (admin)
$res_bkh = $conn->query("SELECT id_user FROM users WHERE role = 'bkh' LIMIT 1");
if ($res_bkh->num_rows > 0) $bkh_id = $res_bkh->fetch_assoc()['id_user'];

// 4. Seeding Pengajuan (Inti Data Keuangan)
echo "3. Menyuntikkan ratusan riwayat Pengajuan Dana...\n";
$statuses = ['Draft', 'Diajukan Ke BEM', 'Verifikasi BKKH', 'Verifikasi WR3', 'Disetujui WR3, Siap Diajukan ke Bendahara', 'Dana Cair', 'LPJ Diajukan', 'LPJ Diverifikasi', 'Selesai'];

$total_pengajuan = 0;
$total_dana = 0;

foreach ($ormawa_ids as $id_ormawa) {
    // Tiap ormawa punya 20-30 pengajuan dalam 1 tahun terakhir
    $jumlah_pengajuan = rand(20, 30);
    
    for ($i = 0; $i < $jumlah_pengajuan; $i++) {
        $nama_kegiatan = "Kegiatan " . ['Seminar', 'Workshop', 'Lomba', 'Bakti Sosial', 'Studi Banding', 'Pelatihan', 'Dies Natalis'][array_rand(['Seminar', 'Workshop', 'Lomba', 'Bakti Sosial', 'Studi Banding', 'Pelatihan', 'Dies Natalis'])] . " " . rand(1, 99);
        $nominal = rand(15, 150) * 100000; // Rp 1.500.000 - 15.000.000
        
        $days_ago = rand(1, 360);
        $tgl_pengajuan = date('Y-m-d H:i:s', strtotime("-$days_ago days"));
        
        // Buat probabilitas: 50% Selesai/LPJ, 25% Cair, 25% sedang diproses
        $rand_status = rand(1, 100);
        if ($rand_status <= 50) { $status = 'Selesai'; }
        elseif ($rand_status <= 75) { $status = 'Dana Cair'; }
        elseif ($rand_status <= 85) { $status = 'Disetujui WR3, Siap Diajukan ke Bendahara'; }
        else { $status = $statuses[array_rand($statuses)]; }
        
        $stmt = $conn->prepare("INSERT INTO pengajuan (id_user_ormawa, nama_kegiatan, dana_diajukan, nominal_pengajuan, status, tanggal_pengajuan) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isddss", $id_ormawa, $nama_kegiatan, $nominal, $nominal, $status, $tgl_pengajuan);
        $stmt->execute();
        $id_pengajuan = $conn->insert_id;
        $total_pengajuan++;

        // Histori Status simulasi singkat
        $stmt_hist = $conn->prepare("INSERT INTO histori_status (id_pengajuan, status, id_user, catatan, tanggal_update) VALUES (?, ?, ?, ?, ?)");
        $catatan = "Mengajukan proposal " . $nama_kegiatan;
        $stmt_hist->bind_param("isiss", $id_pengajuan, $status, $id_ormawa, $catatan, $tgl_pengajuan);
        $stmt_hist->execute();

        // Data Pencairan Dana (Jika sudah masuk tahap Bendahara)
        if (in_array($status, ['Dana Cair', 'LPJ Diajukan', 'LPJ Diverifikasi', 'Selesai'])) {
            $tgl_cair = date('Y-m-d H:i:s', strtotime($tgl_pengajuan . " +" . rand(2, 7) . " days"));
            $stmt_dana = $conn->prepare("INSERT INTO dana (id_pengajuan, nominal_cair, tanggal_cair) VALUES (?, ?, ?)");
            $stmt_dana->bind_param("ids", $id_pengajuan, $nominal, $tgl_cair);
            $stmt_dana->execute();
            $total_dana++;
        }
    }
}
echo "   -> $total_pengajuan Pengajuan disuntikkan.\n";
echo "   -> $total_dana data pencairan Dana (Bendahara) disuntikkan.\n";


// 5. Seeding Peminjaman Ruangan & Barang
echo "4. Menyuntikkan data Peminjaman Sarpras (Ruangan & Barang)...\n";
$ruangan_id = 1; // Default
$res_ruang = $conn->query("SELECT id_ruangan FROM master_ruangan LIMIT 1");
if ($res_ruang->num_rows > 0) $ruangan_id = $res_ruang->fetch_assoc()['id_ruangan'];

$total_tempat = 0;
$total_barang = 0;

for ($i = 0; $i < 30; $i++) {
    $id_ormawa = $ormawa_ids[array_rand($ormawa_ids)];
    
    // Status dominan Menunggu/Pending agar BKKH dan Sarpras punya kerjaan di tabel
    $stat_tempat = ['Menunggu BKKH', 'Disetujui', 'Ditolak'][rand(0,2)];
    $stat_tempat_bkkh = ['Pending', 'Diverifikasi', 'Ditolak'][rand(0,2)];
    $tgl_mulai = date('Y-m-d', strtotime("+" . rand(1, 30) . " days"));
    $tgl_selesai = $tgl_mulai;
    $nama_kegiatan = "Acara " . rand(10,999);
    
    // Peminjaman Tempat
    $stmt_p = $conn->prepare("INSERT INTO peminjaman_tempat (id_user_ormawa, id_ruangan, tgl_mulai, tgl_selesai, jam_mulai, jam_selesai, nama_kegiatan, status, status_bkkh) VALUES (?, ?, ?, ?, '08:00', '16:00', ?, ?, ?)");
    $stmt_p->bind_param("iisssss", $id_ormawa, $ruangan_id, $tgl_mulai, $tgl_selesai, $nama_kegiatan, $stat_tempat, $stat_tempat_bkkh);
    if($stmt_p->execute()) $total_tempat++;

    // Peminjaman Barang
    $stat_barang_bkkh = ['Pending', 'Diverifikasi'][rand(0,1)];
    $stat_barang_srp = ($stat_barang_bkkh == 'Diverifikasi') ? ['Pending', 'Disetujui'][rand(0,1)] : 'Pending';
    
    $stmt_b = $conn->prepare("INSERT INTO peminjaman_barang (id_user_ormawa, nama_kegiatan, tgl_mulai, tgl_selesai, kebutuhan_barang, status_bkkh, status_sarpras) VALUES (?, ?, ?, ?, 'Proyektor x2, Kursi x50', ?, ?)");
    $stmt_b->bind_param("isssss", $id_ormawa, $nama_kegiatan, $tgl_mulai, $tgl_selesai, $stat_barang_bkkh, $stat_barang_srp);
    if($stmt_b->execute()) $total_barang++;
}

echo "   -> $total_tempat Peminjaman Tempat disuntikkan.\n";
echo "   -> $total_barang Peminjaman Barang disuntikkan.\n";

// 6. Update saldo user Ormawa berdasarkan total pencairan dana
echo "5. Memperbarui saldo setiap Ormawa...\n";
foreach ($ormawa_ids as $id_ormawa) {
    $res_saldo = $conn->query("SELECT IFNULL(SUM(d.nominal_cair), 0) AS total_cair FROM dana d JOIN pengajuan p ON d.id_pengajuan = p.id_pengajuan WHERE p.id_user_ormawa = $id_ormawa");
    $total_cair = $res_saldo->fetch_assoc()['total_cair'];
    $sisa_saldo = 500000000 - $total_cair; // Asumsi alokasi awal Rp 500.000.000 per ormawa
    if ($sisa_saldo < 0) $sisa_saldo = 0;
    
    $stmt_saldo = $conn->prepare("UPDATE users SET saldo = ? WHERE id_user = ?");
    $stmt_saldo->bind_param("di", $sisa_saldo, $id_ormawa);
    $stmt_saldo->execute();
    echo "   -> Saldo Ormawa ID $id_ormawa: Rp " . number_format($sisa_saldo) . " (Total cair: Rp " . number_format($total_cair) . ")\n";
}

echo "\n=============================================\n";
echo " SEEDING SELESAI & SUKSES! \n";
echo " Semua Grafik Dashboard (Ormawa, Admin, BEM) \n";
echo " dan Tabel Verifikasi kini terisi penuh. \n";
echo "=============================================\n";
?>