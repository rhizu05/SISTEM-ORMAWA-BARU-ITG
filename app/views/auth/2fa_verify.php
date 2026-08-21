<?php
/**
 * File: 2fa_verify.php
 * Verifikasi TOTP / Backup Code setelah password benar
 */

if (!defined('APP_RUNNING')) {
    require_once dirname(__DIR__, 3) . '/config.php';
}

if (!isset($_SESSION['pending_2fa_user_id'])) {
    redirect('index.php?page=login');
}

$error = '';
$mode = $_GET['mode'] ?? 'totp'; // 'totp' or 'backup'

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = sanitize_input($conn, $_POST['code']);
    $user_id = $_SESSION['pending_2fa_user_id'];
    $mode_post = $_POST['mode'];

    if (empty($code)) {
        $error = "Kode tidak boleh kosong!";
    } else {
        // Ambil secret dari database
        $stmt = $conn->prepare("SELECT twofa_secret FROM users WHERE id_user = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $secret = $result['twofa_secret'];

        $verified = false;

        if ($mode_post === 'backup') {
            // Verifikasi Backup Code
            $verified = twofa_verify_backup_code($conn, $user_id, $code);
            if (!$verified) $error = "Backup Code salah atau sudah digunakan.";
        } else {
            // Verifikasi TOTP (Google Authenticator)
            $verified = twofa_verify_code($secret, $code);
            if (!$verified) $error = "Kode Authenticator salah atau expired.";
        }

        if ($verified) {
            // Lanjutkan proses login seutuhnya
            session_regenerate_id_safe();
            
            $_SESSION['user_id'] = $_SESSION['pending_2fa_user_id'];
            $_SESSION['nama_lengkap'] = $_SESSION['pending_2fa_nama'];
            $_SESSION['user_role'] = $_SESSION['pending_2fa_role'];
            $_SESSION['status_akun'] = $_SESSION['pending_2fa_status'];
            $_SESSION['foto_profil'] = $_SESSION['pending_2fa_foto'];
            
            // Konfigurasi sistem
            $konfigurasi_ses = [];
            $result_konfig_ses = $conn->query("SELECT nama_konfigurasi, nilai_konfigurasi FROM konfigurasi");
            if ($result_konfig_ses) {
                while ($row_ses = $result_konfig_ses->fetch_assoc()) {
                    $konfigurasi_ses[$row_ses['nama_konfigurasi']] = $row_ses['nilai_konfigurasi'];
                }
            }
            $_SESSION['konfigurasi'] = $konfigurasi_ses;

            // Hapus session pending
            unset($_SESSION['pending_2fa_user_id']);
            unset($_SESSION['pending_2fa_nama']);
            unset($_SESSION['pending_2fa_role']);
            unset($_SESSION['pending_2fa_status']);
            unset($_SESSION['pending_2fa_foto']);

            redirect('index.php?page=dashboard');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi 2FA - SI Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #111827; color: #d1d5db; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-2fa { background: rgba(31, 41, 55, 0.9); border-radius: 1rem; border: 1px solid rgba(255,255,255,0.1); padding: 2.5rem; max-width: 400px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        .form-control { background-color: #374151; border: 1px solid #4b5563; color: white; text-align: center; letter-spacing: 0.5rem; font-size: 1.5rem; font-weight: bold; }
        .form-control:focus { background-color: #4b5563; color: white; box-shadow: none; border-color: #3b82f6; }
        .btn-primary { background-color: #2563eb; width: 100%; }
        .alert-custom { background-color: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }
    </style>
</head>
<body>

<div class="card-2fa">
    <div class="text-center mb-4">
        <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i>
        <h4 class="mt-2 text-white">Keamanan Dua Faktor</h4>
        <p class="text-secondary text-sm">
            <?php echo $mode === 'backup' ? 'Masukkan 8 karakter Backup Code Anda' : 'Buka aplikasi Authenticator dan masukkan 6-digit kode'; ?>
        </p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-custom text-center p-2 mb-3"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="index.php?page=login_2fa&mode=<?php echo $mode; ?>" method="POST" autocomplete="off">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="mode" value="<?php echo $mode; ?>">
        
        <div class="mb-4">
            <input type="text" class="form-control" name="code" 
                   maxlength="<?php echo $mode === 'backup' ? '8' : '6'; ?>" 
                   placeholder="<?php echo $mode === 'backup' ? 'XXXXXX' : '000000'; ?>" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary btn-lg mb-3">Verifikasi</button>
    </form>

    <div class="text-center mt-3">
        <?php if ($mode === 'totp'): ?>
            <a href="index.php?page=login_2fa&mode=backup" class="text-decoration-none text-secondary" style="font-size: 0.9rem;">
                <i class="bi bi-key"></i> Gunakan Backup Code
            </a>
        <?php else: ?>
            <a href="index.php?page=login_2fa&mode=totp" class="text-decoration-none text-secondary" style="font-size: 0.9rem;">
                <i class="bi bi-phone"></i> Gunakan App Authenticator
            </a>
        <?php endif; ?>
        <br>
        <a href="index.php?page=login" class="text-decoration-none text-danger mt-2 d-inline-block" style="font-size: 0.9rem;">Batal</a>
    </div>
</div>

</body>
</html>
