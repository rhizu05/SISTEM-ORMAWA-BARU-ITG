<?php
require_once dirname(__DIR__) . '/config.php';

echo "Memulai Database Seeder untuk Dashboard Analytics...\n";

// 1. Bersihkan data lama
$conn->query("DELETE FROM dana");
$conn->query("DELETE FROM pengajuan");

// 2. Pastikan ada minimal 5 Ormawa untuk chart "Top 5"
$ormawas = [
    ['Hima Sipil ITG', 'hima_sipil'],
    ['Hima Elektro ITG', 'hima_elektro'],
    ['Hima Mesin ITG', 'hima_mesin']
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

// 3. Ambil semua ID Ormawa
$res = $conn->query("SELECT id_user, nama_lengkap FROM users WHERE role = 'ormawa'");
$ormawa_ids = [];
while ($row = $res->fetch_assoc()) {
    $ormawa_ids[] = $row['id_user'];
}

// 4. Seeding Data Pengajuan & Dana (Tersebar di 12 bulan terakhir)
$statuses = ['Draft', 'Diajukan Ke BEM', 'Verifikasi BKKH', 'Verifikasi WR3', 'Dana Cair', 'Selesai'];
$total_inserted = 0;

foreach ($ormawa_ids as $id_ormawa) {
    // Tiap ormawa punya 15-25 pengajuan
    $jumlah_pengajuan = rand(15, 25);
    
    for ($i = 0; $i < $jumlah_pengajuan; $i++) {
        $nama_kegiatan = "Kegiatan Mahasiswa " . rand(100, 999) . " - " . date('Y');
        $nominal = rand(10, 100) * 100000; // Rp 1.000.000 - 10.000.000
        
        // Tanggal acak dalam 365 hari terakhir
        $days_ago = rand(1, 360);
        $tgl_pengajuan = date('Y-m-d H:i:s', strtotime("-$days_ago days"));
        
        // Bobot status: 60% Selesai/Cair agar chart pencairan terlihat
        $status = (rand(1, 10) > 4) ? 'Selesai' : $statuses[array_rand($statuses)];
        
        $stmt = $conn->prepare("INSERT INTO pengajuan (id_user_ormawa, nama_kegiatan, dana_diajukan, nominal_pengajuan, status, tanggal_pengajuan) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isddss", $id_ormawa, $nama_kegiatan, $nominal, $nominal, $status, $tgl_pengajuan);
        $stmt->execute();
        $id_pengajuan = $conn->insert_id;
        $total_inserted++;

        // Jika Selesai/Cair, masukkan ke tabel Dana
        if (in_array($status, ['Dana Cair', 'Selesai'])) {
            // Cair 2-14 hari setelah diajukan
            $tgl_cair = date('Y-m-d H:i:s', strtotime($tgl_pengajuan . " +" . rand(2, 14) . " days"));
            $stmt_dana = $conn->prepare("INSERT INTO dana (id_pengajuan, nominal_cair, tanggal_cair) VALUES (?, ?, ?)");
            $stmt_dana->bind_param("ids", $id_pengajuan, $nominal, $tgl_cair);
            $stmt_dana->execute();
        }
    }
}

echo "✅ Berhasil menyuntikkan $total_inserted data pengajuan dan pencairan untuk " . count($ormawa_ids) . " Ormawa.\n";
echo "Silakan cek Dashboard BEM/BPM/Admin dan Ormawa untuk melihat grafiknya!\n";
?>