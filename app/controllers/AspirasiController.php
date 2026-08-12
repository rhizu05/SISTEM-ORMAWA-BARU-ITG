<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

require_once ROOT_PATH . '/app/core/Controller.php';

class AspirasiController extends Controller {

    public function submit() {
        if (!isset($_POST['kirim_aspirasi'])) return;

        $nama     = $this->sanitize($_POST['nama']     ?? 'Anonim');
        $email    = $this->sanitize($_POST['email']    ?? '');
        $kategori = $this->sanitize($_POST['kategori']);
        $subjek   = $this->sanitize($_POST['subjek']);
        $isi      = $this->sanitize($_POST['isi']);

        $stmt = $this->conn->prepare(
            "INSERT INTO aspirasi (nama_pelapor, email_pelapor, kategori, subjek, isi_aspirasi) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("sssss", $nama, $email, $kategori, $subjek, $isi);

        if ($stmt->execute()) {
            $this->redirect('index.php?page=aspirasi&status=aspirasi_sukses');
        } else {
            $this->redirect('index.php?page=aspirasi&error=db_gagal');
        }
    }

    public function tanggapi() {
        if (!isset($_POST['tanggapi_aspirasi'])) return;
        $this->requireRole(['bpm']);

        $id        = intval($_POST['id_aspirasi']);
        $tanggapan = $this->sanitize($_POST['tanggapan']);
        $status    = $this->sanitize($_POST['status']);

        $stmt = $this->conn->prepare("UPDATE aspirasi SET tanggapan_bpm = ?, status = ? WHERE id_aspirasi = ?");
        $stmt->bind_param("ssi", $tanggapan, $status, $id);
        $stmt->execute();
        $this->redirect('index.php?page=manage_aspirasi&status=tanggapan_sukses');
    }
}
?>
