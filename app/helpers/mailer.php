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
    global $conn; // Menggunakan koneksi DB global
    
    // Alih-alih mengirim langsung, masukkan ke antrean (Queue)
    if (isset($conn) && $conn instanceof mysqli) {
        $stmt = $conn->prepare("INSERT INTO email_queue (to_email, to_name, subject, body, status) VALUES (?, ?, ?, ?, 'pending')");
        if ($stmt) {
            $stmt->bind_param("ssss", $to_email, $to_name, $subject, $body_html);
            $result = $stmt->execute();
            $stmt->close();
            
            // Log ke audit bahwa email sedang di-queue
            if (function_exists('log_audit')) {
                log_audit($conn, 'EMAIL_QUEUED', 'email', $to_email, ['subject' => $subject]);
            }
            
            return $result;
        }
    }
    
    // Jika tidak ada koneksi DB, fallback log manual (untuk testing)
    $log_dir = dirname(__DIR__, 2) . '/storage/logs';
    if (!is_dir($log_dir)) mkdir($log_dir, 0777, true);
    
    $log_message = "[" . date('Y-m-d H:i:s') . "] FALLBACK EMAIL KE: $to_email\n";
    $log_message .= "Subject: $subject\n";
    file_put_contents($log_dir . '/email_mock.log', $log_message, FILE_APPEND);
    
    return true;
}

/**
 * Eksekusi antrean email (Dipanggil oleh Cron / CLI Worker)
 * @param mysqli $conn
 */
function process_email_queue(mysqli $conn) {
    // Ambil maks 10 email pending
    $res = $conn->query("SELECT * FROM email_queue WHERE status = 'pending' LIMIT 10");
    if (!$res || $res->num_rows === 0) return;
    
    while ($row = $res->fetch_assoc()) {
        $id = $row['id'];
        // Update status ke sending
        $conn->query("UPDATE email_queue SET status = 'sending' WHERE id = $id");
        
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            
            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($row['to_email'], $row['to_name']);
            
            $mail->isHTML(true);
            $mail->Subject = $row['subject'];
            $mail->Body    = $row['body'];
            
            // Dalam mode lokal, kita tulis ke mock jika password SMTP belum di-set
            if (SMTP_PASS === 'your_smtp_password') {
                $log_dir = dirname(__DIR__, 2) . '/storage/logs';
                if (!is_dir($log_dir)) mkdir($log_dir, 0777, true);
                
                preg_match("/href='([^']+)'/", $row['body'], $matches);
                $url = $matches[1] ?? 'TIDAK_ADA_LINK';
                
                $log = "[" . date('Y-m-d H:i:s') . "] EMAIL MOCK WORKER KE: {$row['to_email']}\n";
                $log .= "Subjek: {$row['subject']}\nURL: $url\n------------------\n";
                file_put_contents($log_dir . '/email_mock.log', $log, FILE_APPEND);
            } else {
                $mail->send(); // Kirim beneran jika SMTP siap
            }
            
            $conn->query("UPDATE email_queue SET status = 'sent', attempts = attempts + 1 WHERE id = $id");
        } catch (Exception $e) {
            $error = $conn->real_escape_string($mail->ErrorInfo);
            $conn->query("UPDATE email_queue SET status = 'failed', error_log = '$error', attempts = attempts + 1 WHERE id = $id");
        }
    }
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
