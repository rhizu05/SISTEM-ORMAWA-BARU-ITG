<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tiket Layanan Mahasiswa ITG</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 15px; margin-bottom: 20px;">
        <h2 style="color: #1e3a8a; margin: 0;">Sistem Informasi Kemahasiswaan (SKIN)</h2>
        <p style="color: #666; margin: 5px 0 0 0; font-size: 13px;">Institut Teknologi Garut</p>
    </div>

    <p>Halo, <strong>{{ $tiket->nama_mahasiswa }}</strong> (NIM: {{ $tiket->nim }}),</p>

    <p>Terima kasih. Pengajuan layanan <strong>{{ strtoupper($tiket->kategori) }}</strong> Anda telah berhasil kami terima dalam sistem.</p>

    <div style="background-color: #f3f4f6; border-left: 4px solid #1e3a8a; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <div style="font-size: 12px; color: #6b7280; text-transform: uppercase;">Kode Tiket Anda:</div>
        <div style="font-size: 22px; font-weight: bold; color: #1e3a8a; letter-spacing: 1px; margin: 5px 0;">{{ $tiket->kode_tiket }}</div>
        <div style="font-size: 13px; color: #4b5563;">Kategori: <strong>{{ ucfirst($tiket->kategori) }}</strong></div>
        <div style="font-size: 13px; color: #4b5563;">Status: <span style="background-color: #fef3c7; color: #92400e; padding: 2px 6px; border-radius: 3px; font-weight: bold;">{{ $tiket->status_label }}</span></div>
    </div>

    @if($tiket->kategori === 'konseling')
        <div style="background-color: #eff6ff; border: 1px solid #bfdbfe; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-size: 13px; color: #1e40af;">
            <strong>Catatan Privasi:</strong> Permohonan konseling Anda ditangani secara tertutup dan rahasia oleh staf BKHM. Anda akan menerima notifikasi jadwal temu atau respons resmi melalui email ini.
        </div>
    @endif

    <p>Anda dapat melacak status penanganan tiket sewaktu-waktu melalui tautan berikut dengan memasukkan <strong>Kode Tiket</strong> dan <strong>Alamat Email</strong> Anda:</p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('layanan.cek-status') }}?kode={{ $tiket->kode_tiket }}&email={{ urlencode($tiket->email) }}" style="background-color: #1e3a8a; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; display: inline-block;">
            Cek Status Tiket Saya
        </a>
    </div>

    <p style="font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 15px; margin-top: 30px;">
        Email ini dibuat secara otomatis oleh SKIN Institut Teknologi Garut. Mohon jangan membalas email ini secara langsung.
    </p>
</body>
</html>
