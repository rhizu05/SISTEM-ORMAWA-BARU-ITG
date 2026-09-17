@php
    $c = is_string($lpj->content) ? (json_decode($lpj->content, true) ?: []) : (array) $lpj->content;
    $owner = $lpj->user ?? Auth::user();
    $realisasi = $lpj->metadata['realisasi_dana'] ?? 0;
    $danaProposal = $proposal ? $proposal->rab->sum('total_harga') : 0;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LPJ - {{ $proposal->nama_kegiatan ?? 'Kegiatan' }}</title>
    <style>
        @page { size: A4; margin: 18mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; }
        .kop { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 24px; }
        .kop-logo { width: 80px; height: 80px; object-fit: contain; }
        .kop-text { text-align: center; flex-grow: 1; padding: 0 8px; }
        .kop-1 { font-size: 10pt; }
        .kop-2 { font-size: 12pt; font-weight: bold; text-transform: uppercase; }
        .kop-3, .kop-4 { font-size: 9.5pt; font-style: italic; line-height: 1.3; }
        .judul { text-align: center; font-weight: bold; text-decoration: underline; margin: 26px 0; }
        .section { font-weight: bold; margin-top: 16px; }
        .content p { margin: 6px 0; text-align: justify; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
    </style>
</head>
<body>
    @include('generator.partials.kop', ['konfig' => $konfig])

    <div class="judul">
        LAPORAN PERTANGGUNGJAWABAN (LPJ)<br>
        {{ strtoupper($proposal->nama_kegiatan ?? 'KEGIATAN') }}
    </div>

    <div class="content">
        <span class="section">I. Pendahuluan</span>
        <p>{!! nl2br(e($c['pendahuluan'] ?? '-')) !!}</p>

        <span class="section">II. Waktu &amp; Tempat Pelaksanaan</span>
        <p>{!! nl2br(e($c['waktu_tempat'] ?? '-')) !!}</p>

        <span class="section">III. Hasil Kegiatan</span>
        <p>{!! nl2br(e($c['hasil_kegiatan'] ?? '-')) !!}</p>

        <span class="section">IV. Realisasi Anggaran</span>
        <table>
            <thead>
                <tr><th>Keterangan</th><th style="text-align:right;">Jumlah</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>Dana Diajukan (Sesuai Proposal)</td>
                    <td style="text-align:right;">Rp {{ number_format($danaProposal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Total Realisasi Pengeluaran</strong></td>
                    <td style="text-align:right;"><strong>Rp {{ number_format($realisasi, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <span class="section">V. Hambatan</span>
        <p>{!! nl2br(e($c['hambatan'] ?? '-')) !!}</p>

        <span class="section">VI. Saran</span>
        <p>{!! nl2br(e($c['saran'] ?? '-')) !!}</p>

        <span class="section">VII. Penutup</span>
        <p>{!! nl2br(e($c['penutup'] ?? '-')) !!}</p>
    </div>

    <div style="margin-top: 40px; text-align: right;">Garut, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
    <div style="margin-top: 20px;">
        <table style="border: none;">
            <tr>
                <td style="border: none; text-align: center; width: 50%;">
                    <div>Ketua Pelaksana,</div>
                    <div style="height: 70px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{ $owner->nama_ketua ?? $owner->name ?? '..........................' }}</div>
                </td>
                <td style="border: none; text-align: center; width: 50%;">
                    <div>Sekretaris,</div>
                    <div style="height: 70px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">{{ $owner->nama_sekretaris ?? '..........................' }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
