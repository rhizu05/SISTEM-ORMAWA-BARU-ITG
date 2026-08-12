<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

require_once ROOT_PATH . '/app/core/Controller.php';

class ProfilController extends Controller {

    public function update() {
        check_login();

        $userId      = $_SESSION['user_id'];
        $namaLengkap = $this->sanitize($_POST['nama_lengkap']);

        if (empty($namaLengkap)) {
            $this->redirect('index.php?page=profil&error=form_kosong');
        }

        $namaKetua      = $this->sanitize($_POST['nama_ketua']      ?? '');
        $namaSekretaris = $this->sanitize($_POST['nama_sekretaris'] ?? '');
        $namaBendahara  = $this->sanitize($_POST['nama_bendahara']  ?? '');
        $alamat         = $this->sanitize($_POST['alamat']          ?? '');
        $telepon        = $this->sanitize($_POST['telepon']         ?? '');

        $stmtOld = $this->conn->prepare("SELECT foto_profil, logo_ormawa, ttd_ketua, ttd_sekretaris, ttd_bendahara FROM users WHERE id_user = ?");
        $stmtOld->bind_param("i", $userId);
        $stmtOld->execute();
        $oldData = $stmtOld->get_result()->fetch_assoc();
        $stmtOld->close();

        $targetDir = ROOT_PATH . '/uploads/profil/';
        if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }

        $handleUpload = function ($fileKey, $oldFile) use ($userId, $targetDir) {
            if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] == 0) {
                $ext = strtolower(pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    if (!empty($oldFile) && file_exists($targetDir . $oldFile)) { unlink($targetDir . $oldFile); }
                    $newName = $fileKey . "_" . $userId . "_" . time() . '.' . $ext;
                    if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetDir . $newName)) {
                        return $newName;
                    }
                }
            }
            return $oldFile;
        };

        $newPhoto = $handleUpload('foto_profil',    $oldData['foto_profil']    ?? null);
        $newLogo  = $handleUpload('logo_ormawa',    $oldData['logo_ormawa']    ?? null);
        $newTtdK  = $handleUpload('ttd_ketua',      $oldData['ttd_ketua']      ?? null);
        $newTtdS  = $handleUpload('ttd_sekretaris', $oldData['ttd_sekretaris'] ?? null);
        $newTtdB  = $handleUpload('ttd_bendahara',  $oldData['ttd_bendahara']  ?? null);

        $stmtUpdate = $this->conn->prepare(
            "UPDATE users SET nama_lengkap = ?, foto_profil = ?, logo_ormawa = ?, nama_ketua = ?,
             nama_sekretaris = ?, nama_bendahara = ?, ttd_ketua = ?, ttd_sekretaris = ?,
             ttd_bendahara = ?, alamat = ?, telepon = ? WHERE id_user = ?"
        );
        $stmtUpdate->bind_param(
            "sssssssssssi",
            $namaLengkap, $newPhoto, $newLogo, $namaKetua,
            $namaSekretaris, $namaBendahara, $newTtdK, $newTtdS,
            $newTtdB, $alamat, $telepon, $userId
        );

        if ($stmtUpdate->execute()) {
            $_SESSION['nama_lengkap'] = $namaLengkap;
            $_SESSION['foto_profil']  = $newPhoto;
            $this->redirect('index.php?page=profil&status=update_sukses');
        } else {
            $this->redirect('index.php?page=profil&error=db_gagal');
        }
    }
}
?>
