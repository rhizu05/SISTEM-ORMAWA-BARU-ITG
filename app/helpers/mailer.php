<?php
defined('APP_RUNNING') or die('Akses langsung tidak diizinkan.');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Mengirim email menggunakan konfigurasi SMTP Brevo
 * 
 * @param string $to_email Email tujuan
 * @param string $to_name Nama tujuan
 * @param string $subject Subjek email
 * @param string $body_html Isi email (HTML)
 * @return bool True jika berhasil, False jika gagal
 */
function send_email(string $to_email, string $to_name, string $subject, string $body_html): bool {
    // === MOCK MODE UNTUK TESTING LOKAL ===
    $log_dir = dirname(__DIR__, 2) . '/storage/logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0777, true);
    }
    
    // Cari URL Reset Password (jika ada di dalam HTML body)
    $matches = [];
    preg_match("/href='([^']+)'/", $body_html, $matches);
    $url = $matches[1] ?? 'TIDAK_ADA_LINK';
    
    $log_message = "[" . date('Y-m-d H:i:s') . "] EMAIL TERKIRIM KE: $to_email\n";
    $log_message .= "Subject: $subject\n";
    $log_message .= "Link Reset: $url\n";
    $log_message .= str_repeat("-", 40) . "\n";
    
    file_put_contents($log_dir . '/email_mock.log', $log_message, FILE_APPEND);
    
    return true; // Pura-pura sukses
    // === AKHIR MOCK MODE ===

    /* Kode PHPMailer Asli Dimentahkan Sementara
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        
        // Disable debug output in production
        $mail->SMTPDebug  = 0; 
        
        // Recipients
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($to_email, $to_name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body_html;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Silently log error (bisa ditaruh ke file log di phase selanjutnya)
        error_log("Email error to $to_email: {$mail->ErrorInfo}");
        return false;
    }
    */
}

/**
 * Membuat Token Unik dan menyimpannya di DB (Di-hash)
 * 
 * @param mysqli $conn
 * @param int $user_id
 * @param int $expiry_minutes Berapa lama token berlaku (default 30)
 * @return string Plain token untuk dikirim via URL email
 */
function create_password_reset_token(mysqli $conn, int $user_id, int $expiry_minutes = 30): string {
    // Generate secure random plain token (misal: hex panjang 64 chars)
    $plain_token = bin2hex(random_bytes(32));
    
    // Hash token sebelum masuk DB (prevent leak if DB compromised)
    $hashed_token = hash('sha256', $plain_token);
    
    // Set Expiry date
    $expiry_time = date('Y-m-d H:i:s', time() + ($expiry_minutes * 60));
    
    // Simpan ke database
    $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE id_user = ?");
    if ($stmt) {
        $stmt->bind_param("ssi", $hashed_token, $expiry_time, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    
    return $plain_token;
}
