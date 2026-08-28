<?php
/**
 * File: setup_2fa.php
 * Endpoint untuk setup 2FA pertama kali.
 */

if (!defined('APP_RUNNING')) {
    require_once dirname(__DIR__, 3) . '/config.php';
}

check_login();
$user_id = $_SESSION['user_id'];
$error = '';

// Proses POST: Validasi kode dan simpan ke database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = sanitize_input($conn, $_POST['code']);
    
    if (empty($code)) {
        $error = "Silakan masukkan kode dari aplikasi Authenticator Anda.";
    } elseif (!isset($_SESSION['setup_2fa_secret'])) {
        $error = "Sesi setup expired. Silakan refresh halaman ini.";
    } else {
        $secret = $_SESSION['setup_2fa_secret'];
        $backup_codes = $_SESSION['setup_2fa_backups'];
        
        // Verifikasi code yang dimasukkan
        if (twofa_verify_code($secret, $code)) {
            // Sukses! Simpan ke database
            $backup_codes_json = json_encode($backup_codes);
            $stmt = $conn->prepare("UPDATE users SET twofa_secret = ?, twofa_enabled = 1, twofa_backup_codes = ?, twofa_confirmed_at = NOW() WHERE id_user = ?");
            $stmt->bind_param("ssi", $secret, $backup_codes_json, $user_id);
            
            if ($stmt->execute()) {
                // Log audit
                log_audit($conn, 'ENABLE_2FA');
                
                // Hapus temporary session
                unset($_SESSION['setup_2fa_secret']);
                unset($_SESSION['setup_2fa_backups']);
                
                // Set sesi terverifikasi agar tidak diminta login 2FA lagi sekarang
                $_SESSION['twofa_verified'] = true;
                
                redirect('index.php?page=profil&status=2fa_enabled');
            } else {
                $error = "Terjadi kesalahan database. Silakan coba lagi.";
            }
            $stmt->close();
        } else {
            $error = "Kode tidak valid. Pastikan waktu di HP Anda sinkron, lalu coba lagi.";
        }
    }
}

// Proses GET: Generate secret & QR code
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_SESSION['setup_2fa_secret'])) {
    $_SESSION['setup_2fa_secret'] = twofa_generate_secret();
    $_SESSION['setup_2fa_backups'] = twofa_generate_backup_codes();
}

$secret = $_SESSION['setup_2fa_secret'] ?? '';
$backups = $_SESSION['setup_2fa_backups'] ?? [];
$qr_uri = '';

// Ambil username untuk label QR Code
$stmt = $conn->prepare("SELECT username FROM users WHERE id_user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$username = $user_data['username'] ?? 'User';

if ($secret) {
    $qr_uri = twofa_get_qr_code_data_uri($secret, $username);
}

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Setup Keamanan Dua Faktor (2FA)</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=profil">Profil</a></li>
        <li class="breadcrumb-item active">Setup 2FA</li>
    </ol>

    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="bi bi-phone me-2"></i> Konfigurasi Authenticator App</h5>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 text-center border-end">
                            <h6 class="fw-bold mb-3">1. Scan QR Code</h6>
                            <p class="small text-muted">Buka aplikasi seperti Google Authenticator, Authy, atau Microsoft Authenticator, lalu scan QR Code ini.</p>
                            <img src="<?php echo $qr_uri; ?>" alt="QR Code" class="img-fluid border p-2 bg-white rounded" style="max-width: 250px;">
                            
                            <div class="mt-3">
                                <p class="small text-muted mb-1">Atau masukkan kode ini secara manual:</p>
                                <code class="fs-5 bg-light p-2 rounded user-select-all"><?php echo $secret; ?></code>
                            </div>
                        </div>
                        
                        <div class="col-md-6 ps-4">
                            <h6 class="fw-bold mb-3">2. Simpan Backup Codes</h6>
                            <p class="small text-muted">Jika Anda kehilangan HP, kode ini adalah **satu-satunya cara** untuk masuk ke akun Anda. Simpan di tempat yang sangat aman!</p>
                            
                            <div class="bg-light p-3 rounded mb-4" style="font-family: monospace; letter-spacing: 2px;">
                                <?php 
                                    $chunks = array_chunk($backups, 4);
                                    foreach ($chunks as $chunk) {
                                        echo implode("&nbsp;&nbsp;&nbsp;", $chunk) . "<br>";
                                    }
                                ?>
                            </div>
                            
                            <h6 class="fw-bold mb-3">3. Verifikasi Pemasangan</h6>
                            <p class="small text-muted">Masukkan 6-digit angka dari aplikasi Authenticator Anda untuk menyelesaikan setup.</p>
                            
                            <form action="index.php?page=setup_2fa" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                    <input type="text" name="code" class="form-control text-center fs-5" placeholder="000000" maxlength="6" required autocomplete="off">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Aktifkan 2FA Sekarang</button>
                                <a href="index.php?page=profil" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
