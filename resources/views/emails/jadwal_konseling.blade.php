<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Sesi Konseling Mahasiswa ITG</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; border-bottom: 2px solid #4338ca; padding-bottom: 15px; margin-bottom: 20px;">
        <h2 style="color: #4338ca; margin: 0;">Layanan Konseling Mahasiswa</h2>
        <p style="color: #666; margin: 5px 0 0 0; font-size: 13px;">Biro Kemahasiswaan & Hubungan Masyarakat (BKHM) ITG</p>
    </div>

    <p>Halo, <strong>{{ $tiket->nama_mahasiswa }}</strong>,</p>

    <p>Terima kasih telah mempercayai BKHM ITG. Permohonan konseling Anda (Kode Tiket: <strong>{{ $tiket->kode_tiket }}</strong>) telah ditindaklanjuti oleh konselor/staf BKHM dan telah ditentukan jadwal sesinya.</p>

    <div style="background-color: #f5f3ff; border-left: 4px solid #7c3aed; padding: 16px; margin: 20px 0; border-radius: 6px;">
        <h3 style="margin: 0 0 10px 0; color: #5b21b6; font-size: 16px;">Rincian Jadwal Temu Sesi Konseling</h3>
        
        <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
            @if($tiket->jadwal_temu)
            <tr>
                <td style="padding: 6px 0; color: #6b7280; width: 140px;"><strong>Waktu Sesi:</strong></td>
                <td style="padding: 6px 0; color: #1f2937; font-weight: bold;">
                    {{ $tiket->jadwal_temu->translatedFormat('l, d F Y') }} pukul {{ $tiket->jadwal_temu->format('H:i') }} WIB
                </td>
            </tr>
            @endif
            @if($tiket->lokasi_atau_link)
            <tr>
                <td style="padding: 6px 0; color: #6b7280;"><strong>Lokasi / Media:</strong></td>
                <td style="padding: 6px 0; color: #1f2937; font-weight: bold;">{{ $tiket->lokasi_atau_link }}</td>
            </tr>
            @endif
            <tr>
                <td style="padding: 6px 0; color: #6b7280;"><strong>Metode:</strong></td>
                <td style="padding: 6px 0; color: #1f2937;">{{ ucfirst($tiket->metode_konseling ?? 'tatap_muka') }}</td>
            </tr>
        </table>

        <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed #c4b5fd;">
            <strong style="color: #5b21b6; font-size: 13px;">Pesan & Petunjuk dari Konselor BKHM:</strong>
            <p style="margin: 6px 0 0 0; font-size: 13px; color: #374151; white-space: pre-line;">{{ $pesanBkhm }}</p>
        </div>
    </div>

    <div style="background-color: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 12px 16px; margin: 20px 0; font-size: 13px; color: #92400e;">
        <strong>Konfirmasi Kehadiran:</strong><br>
        Mohon segera konfirmasikan kesediaan hadir Anda melalui tautan di bawah. Jika Anda berhalangan atau membutuhkan penyesuaian waktu, Anda dapat mengajukan permintaan penjadwalan ulang (*reschedule*).
    </div>

    <div style="text-align: center; margin: 25px 0;">
        <a href="{{ route('layanan.cek-status') }}?kode={{ $tiket->kode_tiket }}&email={{ urlencode($tiket->email) }}" style="background-color: #4338ca; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 6px; font-weight: bold; display: inline-block;">
            Buka Tiket & Konfirmasi Kehadiran
        </a>
    </div>

    <p style="font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 15px; margin-top: 30px;">
        Layanan ini bersifat rahasia dan tertutup sesuai kode etik konseling BKHM ITG.
    </p>
</body>
</html>
