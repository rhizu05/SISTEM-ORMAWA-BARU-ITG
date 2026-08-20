<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

require_once ROOT_PATH . '/app/core/Controller.php';

class NotifikasiController extends Controller {

    /**
     * Endpoint: POST ?page=tandai_notif_terlihat
     * Body JSON: { "ids": [1,2,3] }
     * Menandai notifikasi "Dana Cair" (flag notif_cair_terlihat) sebagai terbaca,
     * hanya untuk pengajuan milik user yang sedang login (isolasi data).
     */
    public function tandaiTerlihat() {
        $this->requireLogin();

        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        $ids = isset($data['ids']) && is_array($data['ids']) ? array_map('intval', $data['ids']) : [];

        if (empty($ids)) {
            $this->jsonResponse(['success' => true, 'message' => 'Tidak ada notifikasi untuk ditandai.']);
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE pengajuan
                SET notif_cair_terlihat = 1
                WHERE id_pengajuan IN ($placeholders) AND id_user_ormawa = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal menyiapkan query.'], 500);
        }

        $types = str_repeat('i', count($ids));
        $params = array_merge($ids, [$_SESSION['user_id']]);
        $stmt->bind_param($types . 'i', ...$params);

        if ($stmt->execute()) {
            $this->jsonResponse(['success' => true, 'message' => 'Notifikasi telah ditandai terbaca.']);
        }

        $this->jsonResponse(['success' => false, 'message' => 'Gagal menandai notifikasi.'], 500);
    }

    /**
     * Endpoint: GET ?page=api_notifikasi_belum_baca
     * Mengembalikan daftar notifikasi belum dibaca milik user login.
     */
    public function belumBaca() {
        $this->requireLogin();

        $stmt = $this->conn->prepare(
            "SELECT id_notif, pesan, waktu
             FROM notifikasi
             WHERE id_user = ? AND status_baca = 'belum'
             ORDER BY waktu DESC
             LIMIT 50"
        );
        if (!$stmt) {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal menyiapkan query.'], 500);
        }
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = [
                'id_notif' => (int) $row['id_notif'],
                'pesan'    => $row['pesan'],
                'waktu'    => $row['waktu'],
            ];
        }

        $this->jsonResponse([
            'success' => true,
            'data'    => $items,
            'total'   => count($items),
        ]);
    }

    /**
     * Endpoint: GET ?page=tandai_notif_baca
     * Body JSON: { "ids": [1,2,3] }  (id_notif dari tabel `notifikasi`)
     * Menandai notifikasi tabel `notifikasi` sebagai sudah dibaca.
     */
    public function tandaiBaca() {
        $this->requireLogin();

        $raw  = file_get_contents('php://input');
        $data = json_decode($raw, true);
        $ids  = isset($data['ids']) && is_array($data['ids']) ? array_map('intval', $data['ids']) : [];

        if (empty($ids)) {
            $this->jsonResponse(['success' => true, 'message' => 'Tidak ada notifikasi untuk ditandai.']);
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE notifikasi
                SET status_baca = 'sudah'
                WHERE id_notif IN ($placeholders) AND id_user = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            $this->jsonResponse(['success' => false, 'message' => 'Gagal menyiapkan query.'], 500);
        }

        $types  = str_repeat('i', count($ids));
        $params = array_merge($ids, [$_SESSION['user_id']]);
        $stmt->bind_param($types . 'i', ...$params);

        if ($stmt->execute()) {
            $this->jsonResponse(['success' => true, 'message' => 'Notifikasi telah ditandai dibaca.']);
        }
        $this->jsonResponse(['success' => false, 'message' => 'Gagal menandai notifikasi.'], 500);
    }

    /**
     * Endpoint: GET ?page=notifikasi_stream
     * Server-Sent Events (SSE) — push realtime notifikasi untuk user login.
     * Menggunakan loop dengan polling DB digunakan agar kompatibel dengan
     * hosting bersama (Apache) tanpa proses terpisah/WebSocket.
     */
    public function stream() {
        $this->requireLogin();

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        // Lepas kunci session agar request HTTP lain tidak terblokir selama streaming
        session_write_close();

        if (function_exists('set_time_limit')) {
            set_time_limit(0);
        }

        $userId = $_SESSION['user_id'];
        $lastId = 0;

        echo ": connected\n\n";
        if (ob_get_level() > 0) { ob_flush(); }
        flush();

        while (!connection_aborted()) {
            $stmt = $this->conn->prepare(
                "SELECT id_notif, pesan, waktu
                 FROM notifikasi
                 WHERE id_user = ? AND status_baca = 'belum'
                 ORDER BY id_notif ASC
                 LIMIT 10"
            );
            if ($stmt) {
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $id = (int) $row['id_notif'];
                    if ($id <= $lastId) { continue; }
                    $payload = json_encode([
                        'id_notif' => $id,
                        'pesan'    => $row['pesan'],
                        'waktu'    => $row['waktu'],
                    ]);
                    echo "id: {$id}\n";
                    echo "event: notif\n";
                    echo "data: {$payload}\n\n";
                    $lastId = $id;
                }
                $stmt->close();
            }

            if (ob_get_level() > 0) { ob_flush(); }
            flush();

            // Interval polling DB (detik)
            sleep(5);
        }

        exit(0);
    }
}
?>