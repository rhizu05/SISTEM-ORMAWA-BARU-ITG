<?php
/**
 * File: forgot_password.php
 * Halaman untuk request reset password
 */

if (!defined('APP_RUNNING')) {
    require_once dirname(__DIR__, 3) . '/config.php';
}

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    redirect('index.php?page=dashboard');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Implementasi Rate Limit untuk form lupa password (hindari spam email)
    $ip = get_client_ip();
    if (!check_rate_limit($conn, $ip, 'forgot_pw', 3, 3600)) {
        // Max 3 request per IP per jam
        $error = "Terlalu banyak permintaan reset password. Silakan coba lagi dalam 1 jam.";
    } else {
        $email = sanitize_input($conn, $_POST['email']);
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Silakan masukkan alamat email yang valid.";
        } else {
            // Log percobaan request agar tercatat di rate limiter
            log_login_attempt($conn, $ip, 'forgot_pw', false);
            
            // Cari user dengan email tersebut
            $stmt = $conn->prepare("SELECT id_user, nama_lengkap FROM users WHERE email = ? AND status_akun = 'aktif'");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Buat token (berlaku 30 menit)
                $token = create_password_reset_token($conn, $user['id_user'], 30);
                
                // Siapkan link reset
                $reset_link = BASE_URL . "/index.php?page=reset_password&token=" . $token;
                
                // Siapkan isi email
                $subject = "Reset Password Anda - Sistem Keuangan ITG";
                $body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #2563eb;'>Permintaan Reset Password</h2>
                    <p>Halo <b>{$user['nama_lengkap']}</b>,</p>
                    <p>Kami menerima permintaan untuk mereset password akun Sistem Keuangan Anda.</p>
                    <p>Silakan klik tombol di bawah ini untuk membuat password baru. Link ini hanya berlaku selama 30 menit.</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$reset_link}' style='background-color: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Reset Password Saya</a>
                    </div>
                    <p>Jika tombol tidak berfungsi, copy-paste link berikut ke browser Anda:</p>
                    <p style='word-break: break-all; color: #6b7280; font-size: 14px;'>{$reset_link}</p>
                    <br>
                    <p>Jika Anda tidak pernah meminta reset password, silakan abaikan email ini. Akun Anda tetap aman.</p>
                    <hr style='border: 1px solid #e5e7eb; margin-top: 30px;'>
                    <p style='color: #9ca3af; font-size: 12px; text-align: center;'>&copy; " . date('Y') . " BKKH Institut Teknologi Garut</p>
                </div>
                ";
                
                // Kirim email
                if (send_email($email, $user['nama_lengkap'], $subject, $body)) {
                    $success = "Link reset password telah dikirim ke email Anda. Silakan cek Inbox atau folder Spam.";
                } else {
                    $error = "Gagal mengirim email. Pastikan konfigurasi SMTP di server sudah benar.";
                }
            } else {
                // Jangan beri tahu jika email tidak ada di database (Security best practice: hindari email enumeration)
                $success = "Jika email terdaftar di sistem kami, link reset password telah dikirim. Silakan cek Inbox atau folder Spam.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - SI Keuangan ITG</title>
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
        <i class="bi bi-envelope-at-fill text-primary" style="font-size: 3rem;"></i>
        <h4 class="mt-2 text-white">Lupa Password?</h4>
        <p class="text-secondary text-sm">Masukkan alamat email yang terdaftar di akun Anda untuk menerima instruksi reset password.</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger p-3 mb-3 border-0" style="background-color: rgba(239, 68, 68, 0.2); color: #fca5a5;"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="alert alert-success p-3 mb-3 border-0" style="background-color: rgba(34, 197, 94, 0.2); color: #86efac;">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo $success; ?>
        </div>
        <div class="text-center mt-4">
            <a href="index.php?page=login" class="btn btn-outline-secondary">Kembali ke Login</a>
        </div>
    <?php else: ?>
        <form action="index.php?page=forgot_password" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" name="email" placeholder="email@domain.com" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mb-3 fw-bold">Kirim Link Reset</button>
            <div class="text-center">
                <a href="index.php?page=login" class="text-decoration-none text-secondary" style="font-size: 0.9rem;">
                    <i class="bi bi-arrow-left"></i> Kembali ke Login
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
