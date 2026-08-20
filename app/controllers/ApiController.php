<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

require_once ROOT_PATH . '/app/core/Controller.php';

class ApiController extends Controller {

    /**
     * Endpoint: GET ?page=api_kalender_peminjaman
     * Data kalender peminjaman tempat (FullCalendar events) untuk semua Ormawa
     * yang tidak ditolak. Format mengikuti pola dashboard (events_calendar).
     */
    public function kalenderPeminjaman() {
        $this->requireLogin();

        $sql = "SELECT p.*, r.nama_ruangan, u.nama_lengkap AS nama_ormawa
                FROM peminjaman_tempat p
                JOIN master_ruangan r ON p.id_ruangan = r.id_ruangan
                JOIN users u ON p.id_user_ormawa = u.id_user
                WHERE p.status != 'Ditolak'
                ORDER BY p.tgl_mulai ASC";
        $result = $this->conn->query($sql);

        $events = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $color = ($row['status'] == 'Disetujui') ? '#198754' : '#ffc107';
                $textColor = ($row['status'] == 'Disetujui') ? '#fff' : '#000';

                $events[] = [
                    'id'          => (int) $row['id_peminjaman'],
                    'title'       => $row['nama_ruangan'] . ' - ' . $row['nama_ormawa'],
                    'start'       => $row['tgl_mulai'] . 'T' . $row['jam_mulai'],
                    'end'         => $row['tgl_selesai'] . 'T' . $row['jam_selesai'],
                    'color'       => $color,
                    'textColor'   => $textColor,
                    'description' => $row['nama_kegiatan'],
                    'status'      => $row['status'],
                ];
            }
        }

        $this->jsonResponse([
            'success' => true,
            'data'    => $events,
            'total'   => count($events),
        ]);
    }
}
?>