<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

require_once ROOT_PATH . '/app/core/Controller.php';

class VerifikasiController extends Controller {

    public function ajukanPencairan() {
        $this->requireRole(['bkh', 'admin']);

        $id     = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $userId = $_SESSION['user_id'];

        if ($id <= 0) {
            $this->redirect('index.php?page=dashboard&error=invalid_id');
        }

        $stmt = $this->conn->prepare("SELECT status FROM pengajuan WHERE id_pengajuan = ?");
        if (!$stmt) { $this->redirect('index.php?page=dashboard&error=db_prepare_gagal'); }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $this->redirect('index.php?page=dashboard&error=not_found');
        }

        $pengajuan = $result->fetch_assoc();
        if (trim($pengajuan['status']) !== 'Disetujui WR3, Siap Diajukan ke Bendahara') {
            $this->redirect('index.php?page=dashboard&error=status_salah');
        }

        $newStatus   = 'Diajukan ke Bendahara';
        $stmtUpdate  = $this->conn->prepare("UPDATE pengajuan SET status = ? WHERE id_pengajuan = ?");
        if (!$stmtUpdate) { $this->redirect('index.php?page=dashboard&error=db_prepare_gagal'); }
        $stmtUpdate->bind_param("si", $newStatus, $id);

        if ($stmtUpdate->execute()) {
            $this->addHistory($id, $userId, $newStatus, 'Pengajuan pencairan dana telah diajukan ke Bendahara.');
            $this->redirect('index.php?page=dashboard&success=bendahara_sukses');
        } else {
            $this->redirect('index.php?page=dashboard&error=db_gagal');
        }
    }

    public function simpanNomorSurat() {
        $this->requireRole(['bkh', 'admin']);

        $id          = isset($_POST['id_pengajuan']) ? intval($_POST['id_pengajuan']) : 0;
        $nomorSurat  = $this->sanitize($_POST['nomor_surat']);

        if ($id <= 0 || empty($nomorSurat)) {
            $this->redirect('index.php?page=arsip_surat&error=form_kosong');
        }

        $stmt = $this->conn->prepare("UPDATE pengajuan SET nomor_surat = ? WHERE id_pengajuan = ?");
        $stmt->bind_param("si", $nomorSurat, $id);

        if ($stmt->execute()) {
            $this->redirect('index.php?page=arsip_surat&status=nomor_sukses');
        } else {
            $this->redirect('index.php?page=arsip_surat&error=nomor_gagal');
        }
    }

    /**
     * Verifikasi proposal berjenjang (BEM → BPM → BKKH → WR3).
     * Dipanggil via POST ?page=verifikasi&id=<id> dengan field `aksi` & `catatan`.
     * Status sekarang diverifikasi sesuai peran (menghindari state corruption),
     * dan form dilindungi token CSRF.
     */
    public function verifikasiProposal() {
        $this->requireRole(['bem', 'bpm', 'bkh', 'wr3']);
        csrf_verify();

        $id      = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $userId  = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];

        if ($id <= 0) {
            $this->redirect('index.php?page=verifikasi&id=' . $id . '&error=aksi_invalid');
        }

        $aksi   = $this->sanitize($_POST['aksi'] ?? '');
        $catatan = $this->sanitize($_POST['catatan'] ?? '');

        if (empty($aksi) || !in_array($aksi, ['setuju', 'tolak'])) {
            $this->redirect("index.php?page=verifikasi&id=$id&error=aksi_invalid");
        }
        if ($aksi === 'tolak' && empty($catatan)) {
            $this->redirect("index.php?page=verifikasi&id=$id&error=catatan_kosong");
        }

        // Ambil status saat ini dan pastikan sesuai tahap role ini
        $stmt = $this->conn->prepare("SELECT status FROM pengajuan WHERE id_pengajuan = ?");
        if (!$stmt) { $this->redirect("index.php?page=verifikasi&id=$id&error=db_prepare_gagal"); }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            $this->redirect("index.php?page=verifikasi&id=$id&error=status_error");
        }

        $expected = [
            'bem' => 'diajukan ke bem',
            'bpm' => 'diajukan ke bpm',
            'bkh' => 'verifikasi bkkh',
            'wr3' => 'verifikasi wr3',
        ];
        if (trim(strtolower($row['status'])) !== ($expected[$userRole] ?? '')) {
            $this->redirect("index.php?page=verifikasi&id=$id&error=status_error");
        }

        // Tentukan status & pesan histori sesuai peran dan aksi
        $newStatus = '';
        $historyMessage = '';
        $prefixSetuju = [
            'bem' => 'Diajukan Ke BPM',
            'bpm' => 'Verifikasi BKKH',
            'bkh' => 'Verifikasi WR3',
            'wr3' => 'Disetujui WR3, Siap Diajukan ke Bendahara',
        ];
        $prefixTolak = [
            'bem' => 'Ditolak BEM',
            'bpm' => 'Ditolak BPM',
            'bkh' => 'Ditolak BKKH',
            'wr3' => 'Ditolak WR3',
        ];

        if ($aksi === 'setuju') {
            $newStatus = $prefixSetuju[$userRole];
            if ($userRole === 'wr3') {
                $historyMessage = 'Proposal disetujui oleh WR3. Menunggu diteruskan ke Bendahara oleh BKKH.' . ($catatan ? ' Catatan: ' . $catatan : ' Catatan: -');
            } else {
                $historyMessage = 'Disetujui oleh ' . strtoupper($userRole) . '.' . ($catatan ? ' Catatan: ' . $catatan : ' Catatan: -');
            }
        } else {
            $newStatus = $prefixTolak[$userRole];
            $historyMessage = 'Ditolak oleh ' . strtoupper($userRole) . '. Catatan: ' . $catatan;
        }

        if (empty($newStatus)) {
            $this->redirect("index.php?page=verifikasi&id=$id&error=status_error");
        }

        $catatanUpdate = ($aksi === 'tolak') ? $catatan : null;
        $stmtUpdate = $this->conn->prepare("UPDATE pengajuan SET status = ?, catatan_revisi = ? WHERE id_pengajuan = ?");
        if (!$stmtUpdate) { $this->redirect("index.php?page=verifikasi&id=$id&error=db_prepare_gagal"); }
        $stmtUpdate->bind_param("ssi", $newStatus, $catatanUpdate, $id);

        if ($stmtUpdate->execute()) {
            $this->addHistory($id, $userId, $newStatus, $historyMessage);
            $this->redirect('index.php?page=dashboard&status=verifikasi_sukses');
        }

        $this->redirect("index.php?page=verifikasi&id=$id&error=update_gagal");
    }

    /**
     * Verifikasi LPJ (status awal 'LPJ Diajukan').
     * Setuju → 'Selesai'; Tolak → 'LPJ Ditolak BKKH' (catatan wajib).
     * Dipanggil via POST ?page=verifikasi_lpj&id=<id>. Dilindungi CSRF.
     */
    public function verifikasiLpj() {
        $this->requireRole(['bkh', 'wr3', 'admin']);
        csrf_verify();

        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $userId = $_SESSION['user_id'];

        if ($id <= 0) {
            $this->redirect('index.php?page=dashboard');
        }

        $aksi    = $this->sanitize($_POST['aksi'] ?? '');
        $catatan = $this->sanitize($_POST['catatan'] ?? '');

        if ($aksi === 'tolak' && empty($catatan)) {
            $this->redirect("index.php?page=verifikasi_lpj&id=$id&error=catatan_kosong");
        }

        if ($aksi === 'setuju') {
            $newStatus = 'Selesai';
            $historyMessage = 'LPJ telah diverifikasi dan disetujui oleh BKKH. Proses pengajuan telah selesai.' . ($catatan ? ' Catatan: ' . $catatan : '');
        } elseif ($aksi === 'tolak') {
            $newStatus = 'LPJ Ditolak BKKH';
            $historyMessage = 'LPJ ditolak oleh BKKH. Catatan: ' . $catatan;
        } else {
            $this->redirect("index.php?page=verifikasi_lpj&id=$id&error=aksi_invalid");
        }

        // Pastikan status saat ini masih 'LPJ Diajukan' (hindari proses ganda)
        $stmt = $this->conn->prepare("SELECT status FROM pengajuan WHERE id_pengajuan = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if (!$row || trim($row['status']) !== 'LPJ Diajukan') {
                $this->redirect("index.php?page=verifikasi_lpj&id=$id&error=status_error");
            }
        }

        $catatanUpdate = ($aksi === 'tolak') ? $catatan : null;
        $stmtUpdate = $this->conn->prepare("UPDATE pengajuan SET status = ?, catatan_revisi = ? WHERE id_pengajuan = ?");
        if (!$stmtUpdate) {
            $this->redirect("index.php?page=verifikasi_lpj&id=$id&error=update_gagal");
        }
        $stmtUpdate->bind_param("ssi", $newStatus, $catatanUpdate, $id);

        if ($stmtUpdate->execute()) {
            $this->addHistory($id, $userId, $newStatus, $historyMessage);
            $this->redirect('index.php?page=dashboard&status=verifikasi_lpj_sukses');
        }

        $this->redirect("index.php?page=verifikasi_lpj&id=$id&error=update_gagal");
    }
}
?>
