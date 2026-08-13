<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

require_once ROOT_PATH . '/app/core/Controller.php';

class UserController extends Controller {

    public function toggleStatus() {
        $this->requireRole(['bkh', 'admin']);
        $id        = intval($_GET['id']);
        $newStatus = $this->sanitize($_GET['new_status']) === 'aktif' ? 'aktif' : 'nonaktif';

        if ($id <= 0) {
            $this->redirect('index.php?page=manage_users&error=invalid_id');
        }

        $stmt = $this->conn->prepare("UPDATE users SET status_akun = ? WHERE id_user = ?");
        if (!$stmt) {
            $this->redirect('index.php?page=manage_users&error=db_prepare_gagal');
        }
        $stmt->bind_param("si", $newStatus, $id);
        if ($stmt->execute()) {
            $this->redirect('index.php?page=manage_users&status=toggle_sukses');
        } else {
            $this->redirect('index.php?page=manage_users&error=toggle_gagal');
        }
    }

    public function tambahUser() {
        $this->requireRole(['bkh', 'admin']);

        if (!isset($_POST['nama_lengkap'])) return;

        $nama     = $this->sanitize($_POST['nama_lengkap']);
        $username = $this->sanitize($_POST['username']);
        $password = $_POST['password'];
        $role     = $this->sanitize($_POST['role']);

        if (empty($nama) || empty($username) || empty($password) || empty($role)) {
            $this->redirect('index.php?page=tambah_user&error=form_kosong');
        }

        $stmt = $this->conn->prepare("SELECT id_user FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $this->redirect('index.php?page=tambah_user&error=username_duplikat');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (nama_lengkap, username, password, role, status_akun) VALUES (?, ?, ?, ?, 'aktif')");
        $stmt->bind_param("ssss", $nama, $username, $hash, $role);

        if ($stmt->execute()) {
            $this->redirect('index.php?page=manage_users&status=tambah_user_sukses');
        } else {
            $this->redirect('index.php?page=manage_users&error=gagal_simpan');
        }
    }

    public function editUser() {
        $this->requireRole(['bkh', 'admin']);

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0) {
            $this->redirect('index.php?page=manage_users&error=invalid_id');
        }

        $nama     = $this->sanitize($_POST['nama_lengkap']);
        $username = $this->sanitize($_POST['username']);
        $password = $_POST['password'];
        $role     = $this->sanitize($_POST['role']);

        if (empty($role)) {
            $this->redirect('index.php?page=edit_user&id=' . $id . '&error=form_kosong');
        }

        $allowedRoles = ['ormawa','bpm','bem','bkh','wr3','bendahara','admin','sarpras','sarpras_barang'];
        if (!in_array($role, $allowedRoles)) {
            $this->redirect('index.php?page=edit_user&id=' . $id . '&error=gagal_simpan&pesan=Role+tidak+valid');
        }

        $stmtCheck = $this->conn->prepare("SELECT id_user FROM users WHERE username = ? AND id_user != ?");
        if (!$stmtCheck) {
            $this->redirect('index.php?page=edit_user&id=' . $id . '&error=db_prepare_gagal');
        }
        $stmtCheck->bind_param("si", $username, $id);
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows > 0) {
            $this->redirect('index.php?page=edit_user&id=' . $id . '&error=username_duplikat');
        }

        if (!empty($password)) {
            $hash        = password_hash($password, PASSWORD_DEFAULT);
            $stmtUpdate  = $this->conn->prepare("UPDATE users SET nama_lengkap = ?, username = ?, password = ?, role = ? WHERE id_user = ?");
            if (!$stmtUpdate) {
                $this->redirect('index.php?page=edit_user&id=' . $id . '&error=db_prepare_gagal');
            }
            $stmtUpdate->bind_param("ssssi", $nama, $username, $hash, $role, $id);
        } else {
            $stmtUpdate = $this->conn->prepare("UPDATE users SET nama_lengkap = ?, username = ?, role = ? WHERE id_user = ?");
            if (!$stmtUpdate) {
                $this->redirect('index.php?page=edit_user&id=' . $id . '&error=db_prepare_gagal');
            }
            $stmtUpdate->bind_param("sssi", $nama, $username, $role, $id);
        }

        if ($stmtUpdate->execute()) {
            $this->redirect('index.php?page=manage_users&status=edit_user_sukses');
        } else {
            $this->redirect('index.php?page=edit_user&id=' . $id . '&error=update_gagal');
        }
    }

    public function aturSaldo() {
        $this->requireRole(['bkh', 'admin']);

        $id    = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0) {
            $this->redirect('index.php?page=manage_saldo&error=invalid_id');
        }

        $saldo = preg_replace('/[^0-9]/', '', $_POST['saldo']);
        if (!is_numeric($saldo) || $saldo < 0) {
            $this->redirect('index.php?page=atur_saldo&id=' . $id . '&error=saldo_invalid');
        }

        $stmt = $this->conn->prepare("UPDATE users SET saldo = ? WHERE id_user = ? AND role IN ('ormawa', 'bem', 'bpm')");
        if (!$stmt) {
            $this->redirect('index.php?page=atur_saldo&id=' . $id . '&error=db_prepare_gagal');
        }
        $stmt->bind_param("di", $saldo, $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $this->redirect('index.php?page=manage_saldo&status=saldo_sukses');
        } else {
            $this->redirect('index.php?page=atur_saldo&id=' . $id . '&error=update_gagal');
        }
    }
}
?>
