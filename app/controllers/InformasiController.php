<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

require_once ROOT_PATH . '/app/core/Controller.php';

class InformasiController extends Controller {

    public function handlePengumuman() {
        if (isset($_POST['tambah_pengumuman'])) {
            $this->tambahPengumuman();
        } elseif (isset($_POST['hapus_pengumuman'])) {
            $this->hapusPengumuman();
        }
    }

    public function handleJadwalRapat() {
        if (isset($_POST['tambah_rapat'])) {
            $this->tambahRapat();
        } elseif (isset($_POST['hapus_rapat'])) {
            $this->hapusRapat();
        }
    }

    private function tambahPengumuman() {
        $this->requireRole(['bem', 'bpm']);

        $judul  = $this->sanitize($_POST['judul']);
        $isi    = $_POST['isi'];
        $userId = $_SESSION['user_id'];

        $fileName = null;
        if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] == 0) {
            $targetDir = ROOT_PATH . '/uploads/pengumuman/';
            if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }
            $ext      = strtolower(pathinfo($_FILES['lampiran']['name'], PATHINFO_EXTENSION));
            $fileName = "announcement_" . time() . "." . $ext;
            move_uploaded_file($_FILES['lampiran']['tmp_name'], $targetDir . $fileName);
        }

        $stmt = $this->conn->prepare("INSERT INTO pengumuman (judul, isi, file_lampiran, id_user_upload) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $judul, $isi, $fileName, $userId);

        if ($stmt->execute()) {
            $this->redirect('index.php?page=pusat_informasi&status=pengumuman_sukses');
        } else {
            $this->redirect('index.php?page=pusat_informasi&error=db_gagal');
        }
    }

    private function hapusPengumuman() {
        $this->requireRole(['bem']);

        $id   = intval($_POST['id_pengumuman']);
        $stmt = $this->conn->prepare("DELETE FROM pengumuman WHERE id_pengumuman = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $this->redirect('index.php?page=pusat_informasi&status=hapus_sukses');
    }

    private function tambahRapat() {
        $this->requireRole(['bem', 'bpm']);

        $judul    = $this->sanitize($_POST['judul_rapat']);
        $desk     = $this->sanitize($_POST['deskripsi']);
        $tanggal  = $this->sanitize($_POST['tanggal_rapat']);
        $jam      = $this->sanitize($_POST['jam_rapat']);
        $lokasi   = $this->sanitize($_POST['lokasi']);
        $link     = $this->sanitize($_POST['link_meeting']);
        $peserta  = isset($_POST['peserta']) ? implode(',', $_POST['peserta']) : '';
        $userId   = $_SESSION['user_id'];

        $stmt = $this->conn->prepare(
            "INSERT INTO jadwal_rapat (judul_rapat, deskripsi, tanggal_rapat, jam_rapat, lokasi, link_meeting, id_penyelenggara, target_peserta)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssssis", $judul, $desk, $tanggal, $jam, $lokasi, $link, $userId, $peserta);

        if ($stmt->execute()) {
            $this->redirect('index.php?page=jadwal_rapat&status=rapat_sukses');
        } else {
            $this->redirect('index.php?page=jadwal_rapat&error=db_gagal');
        }
    }

    private function hapusRapat() {
        $this->requireRole(['bem', 'bpm']);

        $id   = intval($_POST['id_rapat']);
        $stmt = $this->conn->prepare("DELETE FROM jadwal_rapat WHERE id_rapat = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $this->redirect('index.php?page=jadwal_rapat&status=hapus_sukses');
    }
}
?>
