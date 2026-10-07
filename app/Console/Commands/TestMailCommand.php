<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email : Alamat email penerima uji coba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim email uji coba diagnostik untuk memverifikasi konfigurasi SMTP produksi';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $recipient = trim((string) $this->argument('email'));

        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $this->error("Alamat email tidak valid: {$recipient}");
            return Command::FAILURE;
        }

        $mailer = config('mail.default', 'log');
        $fromAddress = config('mail.from.address', 'noreply@itg.ac.id');
        $fromName = config('mail.from.name', 'SKIN ITG');

        $host = config("mail.mailers.{$mailer}.host", '-');
        $port = config("mail.mailers.{$mailer}.port", '-');
        $encryption = config("mail.mailers.{$mailer}.encryption") ?? config("mail.mailers.{$mailer}.scheme") ?? 'auto/default';
        $username = config("mail.mailers.{$mailer}.username");
        $hasPassword = ! empty(config("mail.mailers.{$mailer}.password"));

        $this->info('===========================================================');
        $this->info('   DIAGNOSTIK PENGIRIMAN EMAIL - SKIN ITG (PRODUCTION)   ');
        $this->info('===========================================================');

        $this->table(
            ['Parameter', 'Nilai Konfigurasi'],
            [
                ['Mailer Driver', $mailer],
                ['SMTP Host', $host],
                ['SMTP Port', $port],
                ['Enkripsi', $encryption],
                ['Username', $username ? (substr($username, 0, 3) . '***' . strstr($username, '@')) : '(kosong)'],
                ['Password', $hasPassword ? 'Terkonfigurasi (Terisi)' : '(kosong)'],
                ['Pengirim (From)', "{$fromName} <{$fromAddress}>"],
                ['Penerima Uji Coba', $recipient],
                ['Waktu Server', now()->format('Y-m-d H:i:s T')],
            ]
        );

        if ($mailer === 'log') {
            $this->warn('Perhatian: MAIL_MAILER saat ini adalah "log". Email tidak akan dikirimkan ke internet, melainkan dicatat di storage/logs/laravel.log.');
        }

        $this->line("Mengirim email uji coba ke <fg=cyan>{$recipient}</>...");

        try {
            $diagnostics = [
                'mailer' => $mailer,
                'host' => $host,
                'port' => $port,
                'encryption' => $encryption,
                'from' => "{$fromName} <{$fromAddress}>",
                'recipient' => $recipient,
                'time' => now()->translatedFormat('l, d F Y H:i:s T'),
            ];

            Mail::to($recipient)->send(new \App\Mail\DiagnosticTestMail($diagnostics, $recipient));

            $this->newLine();
            $this->info("✓ BERHASIL! Email diagnostik berhasil diproses oleh mail driver [{$mailer}].");

            if ($mailer === 'log') {
                $this->comment('Silakan periksa berkas storage/logs/laravel.log untuk melihat salinan pesan yang tercatat.');
            } else {
                $this->comment("Silakan periksa kotak masuk (Inbox) atau folder Spam pada alamat {$recipient}.");
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error('✗ GAGAL! Terjadi kesalahan saat mengirim email:');
            $this->line("<fg=red>{$e->getMessage()}</>");

            $this->newLine();
            $this->warn('Panduan Penyelesaian Masalah (Troubleshooting):');
            $msg = strtolower($e->getMessage());

            if (str_contains($msg, 'connection refused') || str_contains($msg, 'timed out') || str_contains($msg, 'network')) {
                $this->line('- Firewall server kemungkinan memblokir port keluar (outbound port).');
                $this->line('  Solusi: Buka port 587 (TLS) atau port 465 (SSL) di security group hosting/server kampus.');
            } elseif (str_contains($msg, 'authentication') || str_contains($msg, 'username and password not accepted') || str_contains($msg, '535')) {
                $this->line('- Kredensial akun email salah atau ditolak oleh server SMTP.');
                $this->line('  Solusi jika menggunakan Google Workspace/Gmail:');
                $this->line('  1. Aktifkan 2-Step Verification pada akun Gmail.');
                $this->line('  2. Buat "App Password" (Sandi Aplikasi) 16 karakter di https://myaccount.google.com/apppasswords.');
                $this->line('  3. Masukkan 16 karakter tersebut ke MAIL_PASSWORD di berkas .env.');
            } elseif (str_contains($msg, 'certificate') || str_contains($msg, 'ssl') || str_contains($msg, 'tls')) {
                $this->line('- Kesalahan negosiasi enkripsi SSL/TLS.');
                $this->line('  Solusi: Sesuaikan MAIL_ENCRYPTION=tls untuk port 587, atau MAIL_ENCRYPTION=ssl untuk port 465.');
            } else {
                $this->line('- Periksa kembali parameter MAIL_HOST, MAIL_PORT, MAIL_USERNAME, dan MAIL_FROM_ADDRESS pada berkas .env.');
            }

            return Command::FAILURE;
        }
    }
}
