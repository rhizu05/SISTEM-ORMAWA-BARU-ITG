<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembaruan Tiket Layanan Mahasiswa ITG</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 15px; margin-bottom: 20px;">
        <h2 style="color: #1e3a8a; margin: 0;">Sistem Informasi Kemahasiswaan (SKIN)</h2>
        <p style="color: #666; margin: 5px 0 0 0; font-size: 13px;">Institut Teknologi Garut</p>
    </div>

    <p>Halo, <strong>{{ $tiket->nama_mahasiswa }}</strong>,</p>

    <p>Terdapat pembaruan status dan tindak lanjut atas tiket Anda dengan Kode: <strong>{{ $tiket->kode_tiket }}</strong> (Kategori: {{ ucfirst($tiket->kategori) }}).</p>

    <div style="background-color: #f3f4f6; border-left: 4px solid #10b981; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <div style="font-size: 13px; color: #4b5563;">Status Terkini: <strong style="color: #047857;">{{ $tiket->status_label }}</strong></div>
        <div style="margin-top: 10px; font-size: 14px; color: #111;">
            <strong>Pesan / Catatan Petugas:</strong>
            <p style="margin: 5px 0 0 0; white-space: pre-line;">{{ $pesanUpdate }}</p>
        </div>
        @if($tiket->jadwal_temu)
            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #d1d5db; font-size: 13px; color: #1e40af;">
                <strong>Jadwal Temu Konseling:</strong> {{ $tiket->jadwal_temu->translatedFormat('l, d F Y - H:i') }} WIB<br>
                @if($tiket->lokasi_atau_link)
                    <strong>Lokasi / Tautan:</strong> {{ $tiket->lokasi_atau_link }}
                @endif
            </div>
        @endif
    </div>

    <div style="text-align: center; margin: 25px 0;">
        <a href="{{ route('layanan.cek-status') }}?kode={{ $tiket->kode_tiket }}&email={{ urlencode($tiket->email) }}" style="background-color: #1e3a8a; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: bold; display: inline-block;">
            Lihat Rincian Tiket
        </a>
    </div>

    <p style="font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 15px; margin-top: 30px;">
        Email ini dikirimkan otomatis oleh SKIN Institut Teknologi Garut.
    </p>
</body>
</html>
