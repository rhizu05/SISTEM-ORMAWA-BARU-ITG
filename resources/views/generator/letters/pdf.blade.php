@php
    $m = $letter->metadata ?? [];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat - {{ $letter->perihal }}</title>
    <style>
        @page { size: A4; margin: 18mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; }
        .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .kop-logo { width: 75px; height: 75px; object-fit: contain; }
        .kop-text { text-align: center; padding: 0 8px; }
        .kop-1 { font-size: 10pt; }
        .kop-2 { font-size: 13pt; font-weight: bold; text-transform: uppercase; }
        .kop-3, .kop-4 { font-size: 9pt; font-style: italic; line-height: 1.3; }
        .judul { text-align: center; font-weight: bold; text-decoration: underline; margin: 26px 0; }
        table.meta { line-height: 1.8; }
        table.meta td { padding: 0 8px 0 0; vertical-align: top; }
        .isi { text-align: justify; line-height: 1.6; }
    </style>
</head>
<body>
    @include('generator.partials.kop', ['konfig' => $konfig])

    <div class="judul">
        @if($letter->type === 'undangan') SURAT UNDANGAN
        @elseif($letter->type === 'tugas') SURAT TUGAS / MANDAT
        @elseif($letter->type === 'permohonan') SURAT PERMOHONAN
        @elseif($letter->type === 'keterangan_aktif') SURAT KETERANGAN AKTIF
        @else SURAT @endif
    </div>

    <div style="margin-bottom: 20px;">
        Nomor: {{ $letter->nomor_surat ?? '___/___/___' }}<br>
        Perihal: {{ $letter->perihal }}
    </div>

    <div style="margin-bottom: 20px;">
        Yth. {{ $m['tujuan'] ?? '..........................' }}<br>
        Di Tempat
    </div>

    @if($letter->type === 'undangan')
        <div class="isi">{{ $m['kalimat_pembuka'] ?? $letter->content }}</div>
        <table class="meta" style="margin-top:16px;">
            <tr><td>Nama Acara</td><td>: {{ $m['nama_acara'] ?? '-' }}</td></tr>
            <tr><td>Hari / Tanggal</td><td>: {{ $m['hari_tanggal'] ?? '-' }}</td></tr>
            <tr><td>Waktu</td><td>: {{ $m['waktu'] ?? '-' }}</td></tr>
            <tr><td>Tempat</td><td>: {{ $m['tempat'] ?? '-' }}</td></tr>
        </table>
        <div class="isi" style="margin-top:16px;">Demikian undangan ini kami sampaikan, atas perhatian dan kehadirannya kami ucapkan terima kasih.</div>
    @elseif($letter->type === 'tugas')
        <div class="isi">Dengan ini memberikan tugas kepada:</div>
        <table class="meta" style="margin-top:12px;">
            <tr><td>Nama</td><td>: {{ $m['nama_petugas'] ?? '-' }}</td></tr>
            <tr><td>NIM</td><td>: {{ $m['nim'] ?? '-' }}</td></tr>
            <tr><td>Uraian Tugas</td><td>: {{ $m['uraian_tugas'] ?? '-' }}</td></tr>
            <tr><td>Tanggal Pelaksanaan</td><td>: {{ $m['tanggal_pelaksanaan'] ?? '-' }}</td></tr>
        </table>
        <div class="isi" style="margin-top:12px;">Untuk melaksanakan tugas tersebut dengan penuh tanggung jawab. Surat tugas ini dibuat untuk dipergunakan sebagaimana mestinya.</div>
    @elseif($letter->type === 'permohonan')
        <div class="isi">Bersama ini kami mengajukan permohonan peminjaman:</div>
        <table class="meta" style="margin-top:12px;">
            <tr><td>Nama Alat / Tempat</td><td>: {{ $m['nama_alat_tempat'] ?? '-' }}</td></tr>
            <tr><td>Waktu Penggunaan</td><td>: {{ $m['waktu_penggunaan'] ?? '-' }}</td></tr>
            <tr><td>Alasan / Tujuan</td><td>: {{ $m['alasan_tujuan'] ?? '-' }}</td></tr>
        </table>
        <div class="isi" style="margin-top:12px;">Demikian permohonan ini kami ajukan, atas perkenannya kami ucapkan terima kasih.</div>
    @elseif($letter->type === 'keterangan_aktif')
        <div class="isi">Yang bertanda tangan di bawah ini menerangkan bahwa:</div>
        <table class="meta" style="margin-top:12px;">
            <tr><td>Nama</td><td>: {{ $m['nama_mahasiswa'] ?? '-' }}</td></tr>
            <tr><td>NIM</td><td>: {{ $m['nim'] ?? '-' }}</td></tr>
            <tr><td>Jabatan di Organisasi</td><td>: {{ $m['jabatan'] ?? '-' }}</td></tr>
            <tr><td>Keperluan</td><td>: {{ $m['keperluan'] ?? '-' }}</td></tr>
        </table>
        <div class="isi" style="margin-top:12px;">Adalah benar mahasiswa/anggota aktif pada organisasi kami. Surat keterangan ini dibuat untuk keperluan {{ $m['keperluan'] ?? '..........................' }}.</div>
    @else
        <div class="isi" style="white-space: pre-wrap;">{{ $letter->content }}</div>
    @endif

    <div style="margin-top: 50px; text-align: right;">Garut, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>

    @include('generator.partials.penandatangan', [
        'penandatanganList' => $letter->penandatangan_list,
        'signatures' => $letter->signatures_by_index,
    ])
</body>
</html>
