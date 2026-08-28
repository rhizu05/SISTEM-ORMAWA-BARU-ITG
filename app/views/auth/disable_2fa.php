<?php
/**
 * File: disable_2fa.php
 * Endpoint untuk mematikan 2FA dengan verifikasi password.
 */

if (!defined('APP_RUNNING')) {
    require_once dirname(__DIR__, 3) . '/config.php';
}

check_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Memastikan middleware CSRF jalan, seharusnya diproses di Router
    
    $password_confirm = $_POST['password_confirm'] ?? '';
    $user_id = $_SESSION['user_id'];
    
    if (empty($password_confirm)) {
        redirect('index.php?page=profil&error=form_kosong');
    }
    
    // Validasi password saat ini
    $stmt = $conn->prepare("SELECT password FROM users WHERE id_user = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if (password_verify($password_confirm, $result['password'])) {
        // Matikan 2FA
        $stmt_update = $conn->prepare("UPDATE users SET twofa_enabled = 0, twofa_secret = NULL, twofa_backup_codes = NULL, twofa_confirmed_at = NULL WHERE id_user = ?");
        $stmt_update->bind_param("i", $user_id);
        
        if ($stmt_update->execute()) {
            // Log audit
            log_audit($conn, 'DISABLE_2FA');
            
            // Hapus session penanda 2FA
            if (isset($_SESSION['twofa_verified'])) unset($_SESSION['twofa_verified']);
            redirect('index.php?page=profil&success=2fa_disabled');
        } else {
            redirect('index.php?page=profil&error=db_gagal');
        }
    } else {
        redirect('index.php?page=profil&error=password_salah');
    }
} else {
    // Method bukan POST, redirect ke profil
    redirect('index.php?page=profil');
}
