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
}
?>