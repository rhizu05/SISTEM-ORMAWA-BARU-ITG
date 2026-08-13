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
}
?>
