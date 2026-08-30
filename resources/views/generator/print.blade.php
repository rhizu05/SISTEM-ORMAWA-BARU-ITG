<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal - {{ $proposal->nama_kegiatan }}</title>
    <style>
        * { box-sizing: border-box; }
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            background: #f0f0f0;
            padding: 20px;
        }
        .paper {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .header-text {
            text-align: center;
            flex-grow: 1;
            padding: 0 10px;
        }
        .header-line-1 { font-size: 10pt; }
        .header-line-2 { font-size: 12pt; font-weight: bold; text-transform: uppercase; }
        .header-line-3 { font-size: 9.5pt; font-style: italic; line-height: 1.3; }
        .header-line-4 { font-size: 9.5pt; line-height: 1.3; }
        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 30px 0;
            font-size: 14pt;
            line-height: 1.4;
        }
        .section-title {
            font-weight: bold;
            margin-top: 20px;
            display: block;
            text-decoration: underline;
        }
        .content {
            text-align: justify;
        }
        .content p { margin: 8px 0; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #eee;
            text-align: center;
        }
        .no-border, .no-border tr, .no-border td { border: none !important; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .ttd-container {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        .ttd-box {
            text-align: center;
            width: 200px;
        }
        .ttd-img {
            height: 80px;
            object-fit: contain;
            margin: 5px 0;
        }
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
        }
        
        /* Print styles */
        @media print {
            body { background: none; padding: 0; margin: 0; }
            .paper { box-shadow: none; margin: 0; width: 210mm !important; padding: 20mm !important; }
            .no-print { display: none !important; }
            @page { margin: 0; size: A4; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center; margin-bottom:20px; background:#fff; padding:15px; border-radius:10px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <button onclick="window.print()" style="padding:10px 20px; cursor:pointer; background:#4f46e5; color:white; border:none; border-radius:5px; font-weight:bold;">
            🖨️ Cetak / Simpan PDF
        </button>
        <a href="{{ route('generator.index') }}" style="text-decoration:none; margin-left:10px; color:#666; font-size:14px;">Kembali ke Daftar</a>
        <p style="margin-top:10px; font-size:13px; color:#666; font-family:sans-serif;">Tekan Ctrl+P. Atur margin ke Default dan centang Background graphics.</p>
    </div>

    <div class="paper">
        <!-- KOP SURAT -->
        <div class="header">
            @if(isset($konfig['kop_logo']) && $konfig['kop_logo'])
                <img src="{{ asset('storage/' . $konfig['kop_logo']) }}" class="header-logo" alt="Logo">
            @else
                <div style="width: 80px;"></div>
            @endif
            
            <div class="header-text">
                <div class="header-line-1">{{ $konfig['kop_baris1'] ?? 'KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI' }}</div>
                <div class="header-line-2">{{ $konfig['kop_baris2'] ?? 'INSTITUT TEKNOLOGI GARUT' }}</div>
                <div class="header-line-3">{{ $konfig['kop_baris3'] ?? 'Jalan Mayor Syamsu No. 1 Jayaraga Garut 44151 Telepon/Fax. (0262) 232773' }}</div>
                <div class="header-line-4">{{ $konfig['kop_baris4'] ?? 'Website : www.itg.ac.id | Email : info@itg.ac.id' }}</div>
            </div>
            
            @if($proposal->user->logo_ormawa)
                <img src="{{ asset('storage/' . $proposal->user->logo_ormawa) }}" class="header-logo" alt="Logo Ormawa">
            @else
                <div style="width: 80px;"></div>
            @endif
        </div>

        <!-- JUDUL -->
        <div class="title">PROPOSAL KEGIATAN<br>{{ strtoupper($proposal->nama_kegiatan) }}</div>

        <!-- ISI PROPOSAL -->
        <div class="content">
            <span class="section-title">I. LATAR BELAKANG</span>
            <p style="white-space: pre-wrap; text-align: justify;">{{ $proposal->latar_belakang }}</p>

            <span class="section-title">II. TUJUAN KEGIATAN</span>
            <p style="white-space: pre-wrap;">{{ $proposal->tujuan }}</p>

            <span class="section-title">III. SASARAN</span>
            <p>{{ $proposal->sasaran }}</p>

            <span class="section-title">IV. RENCANA ANGGARAN BIAYA (RAB)</span>
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Rincian Kebutuhan</th>
                        <th style="width: 40px;">Vol</th>
                        <th style="width: 60px;">Satuan</th>
                        <th>Harga Satuan</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total_rab = 0; @endphp
                    @foreach($proposal->rab as $idx => $r)
                        @php $total_rab += $r['total_harga']; @endphp
                        <tr>
                            <td class="text-center">{{ $idx+1 }}</td>
                            <td>{{ $r->rincian }}</td>
                            <td class="text-center">{{ $r->volume }}</td>
                            <td class="text-center">{{ $r->satuan }}</td>
                            <td class="text-right">Rp {{ number_format($r['harga_satuan'], 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($r['total_harga'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr style="font-weight: bold; background: #eee;">
                        <td colspan="5" style="text-align: right;">TOTAL ANGGARAN</td>
                        <td class="text-right">Rp {{ number_format($total_rab, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>

            <span class="section-title">V. SUSUNAN PANITIA</span>
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Jabatan</th>
                        <th>Nama Mahasiswa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proposal->panitia as $idx => $p)
                        <tr>
                            <td class="text-center">{{ $idx+1 }}</td>
                            <td>{{ $p->jabatan }}</td>
                            <td>{{ $p->nama_mahasiswa }}{{ $p->nim ? ' ('.$p->nim.')' : '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <span class="section-title">VI. PENUTUP</span>
            <p style="white-space: pre-wrap; text-align: justify;">{{ $proposal->penutup }}</p>
        </div>

        <!-- TANDA TANGAN - Image Only MVP -->
        <div style="margin-top: 40px; text-align: right;">
            Garut, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
        <div class="ttd-container">
            <div class="ttd-box">
                <div style="font-weight:bold;">Ketua Pelaksana</div>
                @if($proposal->ttd_1_file)
                    <img src="{{ asset('storage/' . $proposal->ttd_1_file) }}" class="ttd-img" alt="TTD">
                @else
                    <div style="height: 80px;"></div>
                @endif
                <div class="ttd-name">{{ $proposal->ttd_1_nama ?? '..........................' }}</div>
                <div style="font-size:10pt;">NIM. {{ $proposal->ttd_1_nim ?? '....................' }}</div>
            </div>
            <div class="ttd-box">
                <div style="font-weight:bold;">Sekretaris</div>
                @if($proposal->ttd_2_file)
                    <img src="{{ asset('storage/' . $proposal->ttd_2_file) }}" class="ttd-img" alt="TTD">
                @else
                    <div style="height: 80px;"></div>
                @endif
                <div class="ttd-name">{{ $proposal->ttd_2_nama ?? '..........................' }}</div>
                <div style="font-size:10pt;">NIM. {{ $proposal->ttd_2_nim ?? '....................' }}</div>
            </div>
        </div>
    </div>

</body>
</html>
