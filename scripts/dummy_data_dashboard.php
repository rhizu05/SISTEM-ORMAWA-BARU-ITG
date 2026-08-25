<?php
/**
 * Script Injeksi Data Dummy untuk Pengujian Dashboard (Chart.js & Export)
 */
require_once dirname(__DIR__) . '/config.php';

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Hapus data lama yang ada agar bersih
$conn->query("DELETE FROM pengajuan");
$conn->query("DELETE FROM dana");

// 1. Tentukan Ormawa (Ambil id_user ormawa, misal id 7 dan 8)
$ormawa_ids = [7, 8]; 

// 2. Buat Data Pengajuan (Trend beberapa bulan ke belakang)
$statuses = ['Draft', 'Diajukan Ke BEM', 'Disetujui WR3, Siap Diajukan ke Bendahara', 'Dana Cair', 'Selesai'];

$success_count = 0;
for ($i = 0; $i < 40; $i++) {
    $id_ormawa = $ormawa_ids[array_rand($ormawa_ids)];
    $nama_kegiatan = "Kegiatan " . rand(100, 999) . " Dummy " . $i;
    $nominal = rand(500000, 5000000);
    
    // Tanggal acak dalam 6 bulan terakhir
    $days_ago = rand(1, 180);
    $tanggal_pengajuan = date('Y-m-d H:i:s', strtotime("-$days_ago days"));
    
    // Status acak, beri probabilitas lebih tinggi untuk Selesai / Dana Cair
    $status = (rand(1, 10) > 4) ? 'Selesai' : $statuses[array_rand($statuses)];
    
    $stmt = $conn->prepare("INSERT INTO pengajuan (id_user_ormawa, nama_kegiatan, dana_diajukan, nominal_pengajuan, status, tanggal_pengajuan) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isddss", $id_ormawa, $nama_kegiatan, $nominal, $nominal, $status, $tanggal_pengajuan);
    
    if ($stmt->execute()) {
        $insert_id = $conn->insert_id;
        $success_count++;
        
        // 3. Jika status Selesai atau Dana Cair, buatkan data di tabel 'dana'
        if (in_array($status, ['Dana Cair', 'Selesai'])) {
            $nominal_cair = $nominal; // Sesuai pengajuan
            // Tanggal cair acak sedikit setelah tanggal pengajuan
            $cair_days_later = rand(1, 10);
            $tanggal_cair = date('Y-m-d H:i:s', strtotime($tanggal_pengajuan . " +$cair_days_later days"));
            
            $stmt_dana = $conn->prepare("INSERT INTO dana (id_pengajuan, nominal_cair, tanggal_cair) VALUES (?, ?, ?)");
            $stmt_dana->bind_param("ids", $insert_id, $nominal_cair, $tanggal_cair);
            $stmt_dana->execute();
        }
    }
}

echo "✅ Berhasil menyuntikkan $success_count data Pengajuan Dummy (berserta data realisasi dana) untuk Chart.js.\n";
