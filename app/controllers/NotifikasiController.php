<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

require_once ROOT_PATH . '/app/core/Controller.php';

class NotifikasiController extends Controller {

    /**
     * Endpoint: POST ?page=tandai_notif_terlihat
     * Body JSON: { "ids": [1,2,3] }
     * Menandai notifikasi "Dana Cair" (flag notif_cair_terlihat) sebagai terbaca,
     * khusus untuk pengajuan milik user ormawa.
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
        $this->jsonResponse(['success' => false, 'message' => 'Gagal mengupdate database.'], 500);
    }

    /**
     * Endpoint API: Menandai notifikasi umum (dari tabel `notifikasi`) sebagai terbaca
     */
    public function tandaiBaca() {
        $this->requireLogin();
        
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        $id_notif = isset($data['id_notif']) ? intval($data['id_notif']) : 0;
        
        // Memeriksa jika request memanggil aksi 'read_all' dari app.js
        if (isset($data['action']) && $data['action'] === 'read_all') {
            $stmt = $this->conn->prepare("UPDATE notifikasi SET status_baca = 'sudah' WHERE id_user = ? AND status_baca = 'belum'");
            $stmt->bind_param("i", $_SESSION['user_id']);
        } elseif ($id_notif > 0) {
            // Tandai satu saja
            $stmt = $this->conn->prepare("UPDATE notifikasi SET status_baca = 'sudah' WHERE id_notif = ? AND id_user = ?");
            $stmt->bind_param("ii", $id_notif, $_SESSION['user_id']);
        } else {
            // Jika kosong, tandai semua terbaca
            $stmt = $this->conn->prepare("UPDATE notifikasi SET status_baca = 'sudah' WHERE id_user = ? AND status_baca = 'belum'");
            $stmt->bind_param("i", $_SESSION['user_id']);
        }
        
        if ($stmt->execute()) {
            $this->jsonResponse(['success' => true]);
        }
        $this->jsonResponse(['success' => false], 500);
    }

    /**
     * Mendapatkan daftar notifikasi terbaru (dari tabel `notifikasi`) 
     * untuk dirender di dropdown notifikasi Header via AJAX/SSE.
     */
    public function belumBaca() {
        $this->requireLogin();
        
        $stmt = $this->conn->prepare("SELECT id_notif, pesan, status_baca, waktu FROM notifikasi WHERE id_user = ? ORDER BY waktu DESC LIMIT 15");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $res = $stmt->get_result();
        
        $notifs = [];
        $unread = 0;
        while ($row = $res->fetch_assoc()) {
            if ($row['status_baca'] == 'belum') {
                $unread++;
            }
            $notifs[] = [
                'id'     => $row['id_notif'],
                'pesan'  => $row['pesan'],
                'status' => $row['status_baca'],
                'waktu'  => date('d M H:i', strtotime($row['waktu']))
            ];
        }
        
        $this->jsonResponse([
            'success' => true,
            'count'   => $unread,
            'data'    => $notifs
        ]);
    }
}
?>