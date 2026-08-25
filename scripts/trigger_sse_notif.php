<?php
/**
 * Script untuk memicu notifikasi masuk secara langsung (untuk di-test apakah SSE mem-push ke Browser)
 */
require_once dirname(__DIR__) . '/config.php';

// Pastikan DB ada
if ($conn->connect_error) {
    die("DB Error: " . $conn->connect_error);
}

// Cari id_user pertama
$res = $conn->query("SELECT id_user, username FROM users LIMIT 1");
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $id = $row['id_user'];
    $user = $row['username'];
    
    // Insert pesan ke tabel
    $pesan = "🔥 TEST NOTIF SSE REAL-TIME (" . date('H:i:s') . ") via API!";
    
    $stmt = $conn->prepare("INSERT INTO notifikasi (id_user, pesan, status_baca, terkirim_sse) VALUES (?, ?, 'belum', 0)");
    $stmt->bind_param("is", $id, $pesan);
    
    if ($stmt->execute()) {
        echo "✅ Notifikasi berhasil disuntik ke DB untuk user '$user' (ID: $id).\n";
        echo "Silakan login sebagai '$user' dan lihat apakah muncul pop-up toast secara realtime (dalam 2 detik)!\n";
    } else {
        echo "❌ Gagal insert: " . $stmt->error . "\n";
    }
} else {
    echo "❌ Tidak ada user di database.\n";
}
