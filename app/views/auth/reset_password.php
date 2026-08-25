<?php
/**
 * File: reset_password.php
 * Halaman untuk input password baru via link email
 */

if (!defined('APP_RUNNING')) {
    require_once dirname(__DIR__, 3) . '/config.php';
}

if (isset($_SESSION['user_id'])) {
    redirect('index.php?page=dashboard');
}

$error = '';
$success = '';
$token_valid = false;
$user_id = null;

// Ambil token dari URL (GET)
$token = $_GET['token'] ?? '';
$hashed_token = '';

if (empty($token)) {
    $error = "Token tidak valid atau tidak ditemukan.";
} else {
    // Verifikasi Token
    $hashed_token = hash('sha256', $token);
    
    // Cek apakah token ada dan belum expired
    $stmt = $conn->prepare("SELECT id_user FROM users WHERE reset_token = ? AND reset_expires_at > NOW()");
    $stmt->bind_param("s", $hashed_token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $token_valid = true;
        $user_id = $result->fetch_assoc()['id_user'];
    } else {
        $error = "Token reset password tidak valid atau sudah kedaluwarsa. Silakan request link baru.";
    }
    $stmt->close();
}

// Proses form ganti password (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $token_valid) {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    if (empty($password) || empty($password_confirm)) {
        $error = "Password tidak boleh kosong.";
    } elseif ($password !== $password_confirm) {
        $error = "Konfirmasi password tidak cocok.";
    } elseif (strlen($password) < 8) {
        $error = "Password minimal harus 8 karakter.";
    } else {
        // Hash password baru
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Update password dan hancurkan token agar tidak bisa dipakai lagi (One-time use)
        // Juga nonaktifkan 2FA (opsional, tapi praktis jika user kehilangan akses semuanya, ATAU tetap aktif. 
        // Best practice: tetap biarkan aktif, jika mereka tidak bisa masuk karena 2FA, hubungi admin).
        
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires_at = NULL WHERE id_user = ?");
        $stmt->bind_param("si", $new_hash, $user_id);
        
        if ($stmt->execute()) {
            $success = "Password Anda berhasil diperbarui! Anda sekarang dapat login dengan password baru.";
            $token_valid = false; // Sembunyikan form
        } else {
            $error = "Gagal mengupdate password di database.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Password Baru - SI Keuangan ITG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #111827; color: #d1d5db; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .reset-card { background: rgba(31, 41, 55, 0.9); border-radius: 1rem; border: 1px solid rgba(255,255,255,0.1); padding: 2.5rem; max-width: 450px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
        .form-control { background-color: #374151; border: 1px solid #4b5563; color: white; }
        .form-control:focus { background-color: #4b5563; color: white; box-shadow: none; border-color: #3b82f6; }
        .btn-primary { background-color: #2563eb; width: 100%; padding: 0.8rem; }
    </style>
</head>
<body>

<div class="reset-card">
    <div class="text-center mb-4">
        <i class="bi bi-key-fill text-primary" style="font-size: 3rem;"></i>
        <h4 class="mt-2 text-white">Buat Password Baru</h4>
        <?php if ($token_valid): ?>
            <p class="text-secondary text-sm">Silakan masukkan password baru yang kuat untuk akun Anda.</p>
        <?php endif; ?>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger p-3 mb-3 border-0" style="background-color: rgba(239, 68, 68, 0.2); color: #fca5a5;">
            <i class="bi bi-exclamation-circle-fill me-2"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="alert alert-success p-3 mb-3 border-0" style="background-color: rgba(34, 197, 94, 0.2); color: #86efac;">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success; ?>
        </div>
        <div class="text-center mt-4">
            <a href="index.php?page=login" class="btn btn-primary fw-bold">Pergi ke Halaman Login</a>
        </div>
    <?php elseif ($token_valid): ?>
        <form action="index.php?page=reset_password&token=<?php echo htmlspecialchars($token); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="mb-3">
                <label class="form-label text-secondary small">Password Baru</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" name="password" required autofocus minlength="8">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label text-secondary small">Konfirmasi Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" name="password_confirm" required minlength="8">
                </div>
            </div>

            <button type="submit" class="btn btn-primary mb-3 fw-bold">Simpan Password Baru</button>
        </form>
    <?php else: ?>
        <div class="text-center mt-4">
            <a href="index.php?page=forgot_password" class="btn btn-outline-secondary">Kirim Ulang Link Reset</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
