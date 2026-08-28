<?php
/**
 * Email Queue Worker
 * Eksekusi: php scripts/queue_worker.php
 * Jalankan file ini menggunakan Cronjob (Linux) setiap 1 menit.
 * Contoh Cron: * * * * * /usr/bin/php /var/www/sistem_keuangan/scripts/queue_worker.php
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Proses 10 email per menit agar tidak menabrak batas SMTP provider gratis
echo "[" . date('Y-m-d H:i:s') . "] Menjalankan Email Queue Worker...\n";
process_email_queue($conn);
echo "[" . date('Y-m-d H:i:s') . "] Selesai.\n";
