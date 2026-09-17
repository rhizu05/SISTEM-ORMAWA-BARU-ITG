<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Peringatan - {{ $sp->nomor_surat }}</title>
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
        table.meta td { padding: 2px 8px 2px 0; vertical-align: top; }
        .isi { text-align: justify; line-height: 1.6; }
    </style>
</head>
<body>
    @include('generator.partials.kop', ['konfig' => $konfig])

    <div class="judul">SURAT PERINGATAN ({{ $sp->tingkat }})</div>

    <div style="margin-bottom: 18px;">Nomor: {{ $sp->nomor_surat }}</div>

    <div class="isi" style="margin-bottom: 12px;">
        Berdasarkan hasil pengawasan, dengan ini disampaikan surat peringatan kepada:
    </div>

    <table class="meta" style="margin-bottom: 12px;">
        <tr><td>Nama Organisasi</td><td>: {{ $sp->target->name ?? '-' }}</td></tr>
        <tr><td>Perihal</td><td>: {{ $sp->perihal }}</td></tr>
        <tr><td>Alasan</td><td>: {{ $sp->alasan_singkat }}</td></tr>
    </table>

    <div class="isi" style="margin-bottom: 12px;">{!! nl2br(e($sp->deskripsi)) !!}</div>

    <div class="isi" style="margin-bottom: 12px;"><strong>Sanksi:</strong> {{ $sp->sanksi }}</div>

    <div class="isi">Demikian surat peringatan ini dibuat untuk diperhatikan dan dilaksanakan sebagaimana mestinya.</div>

    <div style="margin-top: 50px; text-align: right;">Garut, {{ $sp->tanggal_surat->translatedFormat('d F Y') }}</div>
    <div style="margin-top: 20px;">
        <div style="float: right; text-align: center; width: 250px;">
            <div>Penandatangan,</div>
            <div style="height: 70px;"></div>
            <div style="font-weight: bold; text-decoration: underline;">{{ $sp->penandatangan }}</div>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
