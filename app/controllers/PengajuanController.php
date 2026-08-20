<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

require_once ROOT_PATH . '/app/core/Controller.php';

class PengajuanController extends Controller {

    public function tambah() {
        $this->requireRole(['ormawa', 'bem', 'bpm']);

        $userId          = $_SESSION['user_id'];
        $userRole        = $_SESSION['user_role'];
        $namaKegiatan    = $this->sanitize($_POST['nama_kegiatan']);
        $tanggal         = $this->sanitize($_POST['tanggal_pengajuan']);
        $dana            = (float) preg_replace('/[^0-9]/', '', $_POST['dana_diajukan']);

        if (empty($namaKegiatan) || empty($tanggal) || empty($dana)) {
            $this->redirect('index.php?page=tambah&error=form_kosong');
        }

        $stmtSaldo = $this->conn->prepare("SELECT saldo FROM users WHERE id_user = ?");
        $stmtSaldo->bind_param("i", $userId);
        $stmtSaldo->execute();
        $totalSaldo = $stmtSaldo->get_result()->fetch_assoc()['saldo'] ?? 0;

        $stmtTerpakai = $this->conn->prepare(
            "SELECT SUM(dana_diajukan) AS total FROM pengajuan 
             WHERE id_user_ormawa = ? AND status NOT IN ('Ditolak BEM', 'Ditolak BKKH', 'Ditolak WR3', 'Ditolak Bendahara')"
        );
        $stmtTerpakai->bind_param("i", $userId);
        $stmtTerpakai->execute();
        $saldoTerpakai = $stmtTerpakai->get_result()->fetch_assoc()['total'] ?? 0;

        if ($dana > ($totalSaldo - $saldoTerpakai)) {
            $this->redirect('index.php?page=tambah&error=saldo_tidak_cukup');
        }

        if (!isset($_FILES['file_proposal']) || $_FILES['file_proposal']['error'] != 0) {
            $this->redirect('index.php?page=tambah&error=file_kosong');
        }

        $targetDir = ROOT_PATH . '/uploads/proposal/';
        if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }

        $ext = strtolower(pathinfo($_FILES['file_proposal']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $this->redirect('index.php?page=tambah&error=bukan_pdf');
        }
        if (!is_valid_pdf($_FILES['file_proposal']['tmp_name'])) {
            $this->redirect('index.php?page=tambah&error=bukan_pdf');
        }

        $fileName   = "proposal_{$userId}_" . time() . ".{$ext}";
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['file_proposal']['tmp_name'], $targetFile)) {
            $status = ($userRole === 'bem' || $userRole === 'bpm') ? 'Verifikasi BKKH' : 'Diajukan Ke BEM';
            $stmt   = $this->conn->prepare(
                "INSERT INTO pengajuan (id_user_ormawa, nama_kegiatan, dana_diajukan, tanggal_pengajuan, file_proposal, status)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            if (!$stmt) { unlink($targetFile); $this->redirect('index.php?page=tambah&error=db_prepare_gagal'); }
            $stmt->bind_param("isdsss", $userId, $namaKegiatan, $dana, $tanggal, $fileName, $status);
            if ($stmt->execute()) {
                $this->addHistory($this->conn->insert_id, $userId, $status, 'Proposal awal telah diajukan oleh Ormawa.');

                if ($status === 'Diajukan Ke BEM') {
                    notify_role($this->conn, 'bem', 'Pengajuan baru: "' . $namaKegiatan . '" menunggu verifikasi Anda.');
                } else {
                    notify_role($this->conn, 'bkh', 'Pengajuan baru: "' . $namaKegiatan . '" menunggu verifikasi Anda.');
                }

                $this->redirect('index.php?page=riwayat&status=tambah_sukses');
            } else {
                unlink($targetFile);
                $this->redirect('index.php?page=tambah&error=db_gagal');
            }
        } else {
            $this->redirect('index.php?page=tambah&error=upload_gagal');
        }
    }

    public function edit() {
        $this->requireRole(['ormawa', 'bem', 'bpm']);

        $id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $userId = $_SESSION['user_id'];

        if ($id <= 0) {
            $this->redirect('index.php?page=riwayat&error=invalid_id');
        }

        $stmtOld = $this->conn->prepare("SELECT file_proposal, status FROM pengajuan WHERE id_pengajuan = ? AND id_user_ormawa = ?");
        if (!$stmtOld) { $this->redirect('index.php?page=riwayat&error=db_prepare_gagal'); }
        $stmtOld->bind_param("ii", $id, $userId);
        $stmtOld->execute();
        $oldData = $stmtOld->get_result()->fetch_assoc();
        $stmtOld->close();

        if (!$oldData) { $this->redirect('index.php?page=riwayat&error=unauthorized'); }
        if (strpos(strtolower($oldData['status']), 'ditolak') === false) {
            $this->redirect('index.php?page=riwayat&error=edit_disallowed');
        }

        $namaKegiatan = $this->sanitize($_POST['nama_kegiatan']);
        $tanggal      = $this->sanitize($_POST['tanggal_pengajuan']);
        $dana         = preg_replace('/[^0-9]/', '', $_POST['dana_diajukan']);

        if (empty($namaKegiatan) || empty($tanggal) || empty($dana)) {
            $this->redirect('index.php?page=edit&id=' . $id . '&error=form_kosong');
        }

        $fileName = $oldData['file_proposal'];

        if (isset($_FILES['file_proposal']) && $_FILES['file_proposal']['error'] == 0) {
            $targetDir = ROOT_PATH . '/uploads/proposal/';
            $ext       = strtolower(pathinfo($_FILES['file_proposal']['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf' || !is_valid_pdf($_FILES['file_proposal']['tmp_name'])) { $this->redirect('index.php?page=edit&id=' . $id . '&error=bukan_pdf'); }

            $newFileName = "proposal_{$userId}_" . time() . ".{$ext}";
            $targetFile  = $targetDir . $newFileName;
            if (move_uploaded_file($_FILES['file_proposal']['tmp_name'], $targetFile)) {
                if (!empty($fileName) && file_exists($targetDir . $fileName)) { unlink($targetDir . $fileName); }
                $fileName = $newFileName;
            } else {
                $this->redirect('index.php?page=edit&id=' . $id . '&error=upload_gagal');
            }
        }

        $statusMap = [
            'Ditolak BEM'  => 'Diajukan Ke BEM',
            'Ditolak BPM'  => 'Diajukan Ke BPM',
            'Ditolak BKKH' => 'Verifikasi BKKH',
            'Ditolak WR3'  => 'Verifikasi WR3',
        ];
        $newStatus = $statusMap[$oldData['status']] ?? 'Diajukan Ke BEM';

        $stmtUpdate = $this->conn->prepare(
            "UPDATE pengajuan SET nama_kegiatan = ?, dana_diajukan = ?, tanggal_pengajuan = ?, file_proposal = ?, status = ?, catatan_revisi = NULL WHERE id_pengajuan = ?"
        );
        if (!$stmtUpdate) { $this->redirect('index.php?page=edit&id=' . $id . '&error=db_prepare_gagal'); }
        $stmtUpdate->bind_param("sdsssi", $namaKegiatan, $dana, $tanggal, $fileName, $newStatus, $id);

        if ($stmtUpdate->execute()) {
            $this->addHistory($id, $userId, $newStatus, 'Proposal telah direvisi dan diajukan kembali.');
            $this->redirect('index.php?page=riwayat&status=edit_sukses');
        } else {
            $this->redirect('index.php?page=edit&id=' . $id . '&error=db_gagal');
        }
    }

    public function followup() {
        $this->requireRole(['ormawa']);

        $idPengajuan = (int) $this->sanitize($_POST['id_pengajuan']);
        $pesan       = $this->sanitize($_POST['pesan_followup']);

        if (empty($idPengajuan) || empty($pesan)) {
            $this->redirect('index.php?page=detail&id=' . $idPengajuan . '&error=form_kosong');
        }

        $stmtCheck = $this->conn->prepare("SELECT status FROM pengajuan WHERE id_pengajuan = ?");
        $stmtCheck->bind_param("i", $idPengajuan);
        $stmtCheck->execute();
        $status = $stmtCheck->get_result()->fetch_assoc()['status'] ?? null;

        if (!$status) {
            $this->redirect('index.php?page=detail&id=' . $idPengajuan . '&error=pengajuan_tidak_ditemukan');
        }

        $statusLower = strtolower(trim($status));

        $statusToRole = [
            'diajukan ke bem'           => 'bem',
            'verifikasi bem'            => 'bem',
            'diajukan ke bpm'           => 'bpm',
            'verifikasi bpm'            => 'bpm',
            'verifikasi bkkh'           => 'bkkh',
            'verifikasi wr3'            => 'wr3',
            'diajukan ke bendahara'     => 'bendahara',
            'lpj diajukan'              => 'bkkh',
        ];

        $targetRole = $statusToRole[$statusLower] ?? null;

        if (!$targetRole) {
            $this->redirect('index.php?page=detail&id=' . $idPengajuan . '&error=status_tidak_memungkinkan_followup');
        }

        notify_role($this->conn, $targetRole, "Follow-up: Ormawa menanyakan status pengajuan <strong>" . htmlspecialchars($status) . "</strong>.<br>Pesan: " . htmlspecialchars($pesan));

        $this->redirect('index.php?page=detail&id=' . $idPengajuan . '&status=followup_sukses');
    }
}
?>
