<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

require_once ROOT_PATH . '/app/core/Controller.php';

class BendaharaController extends Controller {

    public function verifikasi() {
        $this->requireRole(['bendahara']);

        $id               = isset($_POST['id_pengajuan']) ? intval($_POST['id_pengajuan']) : 0;
        $danaDisetujui    = preg_replace('/[^0-9]/', '', $_POST['dana_disetujui']);
        $statusVerifikasi = $this->sanitize($_POST['status_verifikasi']);
        $catatan          = $this->sanitize($_POST['catatan']);
        $userId           = $_SESSION['user_id'];

        if ($id <= 0) { $this->redirect('index.php?page=proses&error=invalid_id'); }

        $stmtCheck = $this->conn->prepare("SELECT status FROM pengajuan WHERE id_pengajuan = ?");
        if (!$stmtCheck) { $this->redirect('index.php?page=proses&error=db_prepare_gagal'); }
        $stmtCheck->bind_param("i", $id);
        $stmtCheck->execute();
        $row = $stmtCheck->get_result()->fetch_assoc();

        if (trim($row['status']) !== 'Diajukan ke Bendahara') {
            $this->redirect('index.php?page=proses&error=status_tidak_sesuai');
        }

        if ($statusVerifikasi === 'disetujui') {
            $newStatus      = 'Dana Cair';
            $stmtUpdate     = $this->conn->prepare("UPDATE pengajuan SET status = ?, dana_disetujui = ?, catatan = ? WHERE id_pengajuan = ?");
            if (!$stmtUpdate) { $this->redirect('index.php?page=proses&error=db_prepare_gagal'); }
            $stmtUpdate->bind_param("sssi", $newStatus, $danaDisetujui, $catatan, $id);
            $historyMsg = 'Pengajuan pencairan telah diverifikasi dan disetujui oleh Bendahara. Dana telah dicairkan. Catatan: ' . $catatan;
        } else {
            $newStatus      = 'Ditolak Bendahara';
            $stmtUpdate     = $this->conn->prepare("UPDATE pengajuan SET status = ?, catatan = ? WHERE id_pengajuan = ?");
            if (!$stmtUpdate) { $this->redirect('index.php?page=proses&error=db_prepare_gagal'); }
            $stmtUpdate->bind_param("ssi", $newStatus, $catatan, $id);
            $historyMsg = 'Pengajuan pencairan telah ditolak oleh Bendahara. Catatan: ' . $catatan;
        }

        if ($stmtUpdate->execute()) {
            $this->addHistory($id, $userId, $newStatus, $historyMsg);
            $this->redirect('index.php?page=proses&status=verifikasi_sukses');
        } else {
            $this->redirect('index.php?page=proses&error=verifikasi_gagal');
        }
    }
}
?>
