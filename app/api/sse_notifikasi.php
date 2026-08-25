<?php
/**
 * File: sse_notifikasi.php
 * Endpoint Server-Sent Events (SSE) untuk Notifikasi Real-time
 */

// config.php dan Router.php sudah memanggil ini.
// Kita langsung gunakan object $GLOBALS['conn'] atau $this->conn dari router jika tersedia.
// Di Router.php, $conn adalah var global di dalam method render() atau diteruskan via include.
global $conn;

// Jika session tertutup karena read-close, SSE butuh ini untuk menghindari write lock 
// pada koneksi concurrent. Sangat penting!
session_write_close();

// Headers wajib untuk SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // Membantu menghindari buffering Nginx

// Matikan output buffering PHP (jika ada) untuk stream
if (ob_get_level()) {
    ob_end_clean();
}

$user_id = $_SESSION['user_id'];

// Timeout SSE loop (misal: 60 detik) untuk mencegah proses menggantung selamanya.
// Browser secara otomatis akan reconnect saat koneksi putus.
$startTime = time();
$maxExecutionTime = 50; 

while (time() - $startTime < $maxExecutionTime) {
    // 1. Cek koneksi client, jika putus hentikan loop
    if (connection_aborted()) {
        break;
    }

    // 2. Ambil notifikasi baru yang belum dikirim via SSE (terkirim_sse = 0)
    $stmt = $conn->prepare("SELECT id_notif, pesan, waktu FROM notifikasi WHERE id_user = ? AND terkirim_sse = 0 ORDER BY waktu ASC");
    if (!$stmt) break; // DB connection issue
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $new_notifications = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // 3. Jika ada notif baru, kirim dan tandai sebagai terkirim (hanya SSE)
    if (!empty($new_notifications)) {
        // Tandai sebagai terkirim
        $ids = array_column($new_notifications, 'id_notif');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        
        $updateStmt = $conn->prepare("UPDATE notifikasi SET terkirim_sse = 1 WHERE id_notif IN ($placeholders)");
        if ($updateStmt) {
            $updateStmt->bind_param($types, ...$ids);
            $updateStmt->execute();
            $updateStmt->close();
        }

        // Kirim payload SSE (harus format strict JSON)
        $payload = json_encode($new_notifications);
        echo "event: new_notif\n";
        echo "data: {$payload}\n\n"; // 2 newline menandakan akhir pesan
        flush(); // Dorong output ke client segera
    }

    // 4. Jeda polling untuk mengurangi beban CPU/DB (2 detik)
    sleep(2);
}
// Ketika 50 detik selesai, PHP akan exit normal. Browser akan auto reconnect dalam 3 detik (default).
exit();
?>
