<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Uji Coba Diagnostik SMTP - SKIN ITG</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 24px; background-color: #f8fafc;">
    <div style="background-color: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="text-align: center; border-bottom: 2px solid #0284c7; padding-bottom: 16px; margin-bottom: 20px;">
            <h2 style="color: #0f172a; margin: 0; font-size: 20px;">Sistem Informasi & Keuangan Ormawa (SKIN)</h2>
            <p style="color: #64748b; margin: 4px 0 0 0; font-size: 13px;">Institut Teknologi Garut — Verifikasi Layanan Mail Server</p>
        </div>

        <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 14px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center;">
                <span style="font-size: 16px; color: #065f46; font-weight: bold;">✓ Koneksi Mail Server Berhasil!</span>
            </div>
            <p style="color: #047857; margin: 6px 0 0 0; font-size: 13px;">
                Jika pesan ini masuk ke kotak masuk (inbox) Anda, konfigurasi SMTP pada server produksi telah aktif dan siap melayani pengiriman notifikasi serta tiket mahasiswa.
            </p>
        </div>

        <h3 style="color: #334155; font-size: 14px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Detail Parameter Diagnostik:</h3>
        
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 24px;">
            <tbody>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 0; color: #64748b; width: 40%;">Waktu Pengiriman</td>
                    <td style="padding: 8px 0; color: #0f172a; font-weight: 600;">{{ $diagnostics['timestamp'] ?? now()->format('Y-m-d H:i:s T') }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 0; color: #64748b;">Mailer Driver</td>
                    <td style="padding: 8px 0; color: #0f172a; font-weight: 600;">{{ $diagnostics['mailer'] ?? '-' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 0; color: #64748b;">SMTP Host</td>
                    <td style="padding: 8px 0; color: #0f172a; font-weight: 600;">{{ $diagnostics['host'] ?? '-' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 0; color: #64748b;">SMTP Port</td>
                    <td style="padding: 8px 0; color: #0f172a; font-weight: 600;">{{ $diagnostics['port'] ?? '-' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 0; color: #64748b;">Enkripsi Transport</td>
                    <td style="padding: 8px 0; color: #0f172a; font-weight: 600;">{{ $diagnostics['encryption'] ?? '-' }}</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 0; color: #64748b;">Akun Pengirim (From)</td>
                    <td style="padding: 8px 0; color: #0f172a; font-weight: 600;">{{ $diagnostics['from'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b;">Penerima Uji Coba</td>
                    <td style="padding: 8px 0; color: #0284c7; font-weight: 600;">{{ $recipientEmail }}</td>
                </tr>
            </tbody>
        </table>

        <p style="font-size: 13px; color: #475569; margin: 0 0 16px 0;">
            Email ini dihasilkan oleh perintah terminal <code>php artisan mail:test</code> untuk validasi sebelum peluncuran resmi.
        </p>

        <div style="border-top: 1px solid #e2e8f0; padding-top: 14px; text-align: center;">
            <p style="font-size: 11px; color: #94a3b8; margin: 0;">
                Biro Kemahasiswaan & Hubungan Masyarakat (BKHM) &bull; Institut Teknologi Garut<br>
                Jl. Mayor Syamsu No. 1 Jayaraga, Garut 44151
            </p>
        </div>
    </div>
</body>
</html>
