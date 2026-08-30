<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposal - {{ $proposal->nama_kegiatan }}</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }
        .header {
            border-bottom: 3px solid #000;
            margin-bottom: 2px;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
        }
        .header-logo {
            width: 90px;
            height: 90px;
        }
        .header-text {
            text-align: center;
            flex-grow: 1;
        }
        .header-line-1 { font-size: 14pt; }
        .header-line-2 { font-size: 16pt; font-weight: bold; }
        .header-line-3 { font-size: 10pt; }
        .header-line-4 { font-size: 10pt; }
        .double-line {
            border-top: 1px solid #000;
            margin-top: 2px;
            margin-bottom: 20px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .section-title {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        .content {
            text-align: justify;
        }
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
            padding: 5px;
        }
        th {
            background-color: #f2f2f2;
        }
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
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background:#f3f4f6; padding:15px; text-align:center; margin-bottom:20px; font-family:sans-serif;">
        <button onclick="window.print()" style="background:#4f46e5; color:white; border:none; padding:10px 20px; border-radius:5px; font-weight:bold; cursor:pointer; font-size:16px;">
            🖨️ Cetak / Simpan sebagai PDF
        </button>
        <p style="margin-top:10px; font-size:13px; color:#666;">Tekan Ctrl+P (Windows) atau Cmd+P (Mac). Pastikan pengaturan margin ke "Default" atau "None", dan centang "Background graphics" jika perlu.</p>
    </div>

    <!-- KOP SURAT -->
    <div class="header">
        @if(isset($konfig['kop_logo']) && $konfig['kop_logo'])
            <img src="{{ asset('storage/' . $konfig['kop_logo']) }}" class="header-logo" alt="Logo">
        @else
            <div style="width: 90px; height: 90px;"></div>
        @endif
        
        <div class="header-text">
            <div class="header-line-1">{{ $konfig['kop_baris1'] ?? 'KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI' }}</div>
            <div class="header-line-2">{{ $konfig['kop_baris2'] ?? 'INSTITUT TEKNOLOGI GARUT' }}</div>
            <div class="header-line-3">{{ $konfig['kop_baris3'] ?? 'Jalan Mayor Syamsu No. 1 Jayaraga Garut 44151 Telepon/Fax. (0262) 232773' }}</div>
            <div class="header-line-4">{{ $konfig['kop_baris4'] ?? 'Website : www.itg.ac.id | Email : info@itg.ac.id' }}</div>
        </div>
        
        <!-- Logo Ormawa (Kanan) jika ada -->
        @if($proposal->user->logo_ormawa)
            <img src="{{ asset('storage/' . $proposal->user->logo_ormawa) }}" class="header-logo" alt="Logo Ormawa">
        @else
            <div style="width: 90px; height: 90px;"></div>
        @endif
    </div>
    <div class="double-line"></div>

    <!-- JUDUL -->
    <div class="title">
        PROPOSAL KEGIATAN<br>
        {{ $proposal->nama_kegiatan }}<br>
        {{ strtoupper($proposal->user->name) }}
    </div>

    <!-- ISI PROPOSAL -->
    <div class="content">
        <div class="section-title">A. LATAR BELAKANG</div>
        <div style="white-space: pre-wrap;">{{ $proposal->latar_belakang }}</div>

        <div class="section-title">B. TUJUAN KEGIATAN</div>
        <div style="white-space: pre-wrap;">{{ $proposal->tujuan }}</div>

        <div class="section-title">C. SASARAN PESERTA</div>
        <div style="white-space: pre-wrap;">{{ $proposal->sasaran }}</div>

        <div class="section-title">D. SUSUNAN KEPANITIAAN</div>
        <ul style="margin-top: 5px;">
            @foreach($proposal->panitia as $p)
                <li><strong>{{ $p->jabatan }}</strong>: {{ $p->nama_mahasiswa }} {{ $p->nim ? '('.$p->nim.')' : '' }}</li>
            @endforeach
        </ul>

        <div class="section-title" style="page-break-before: auto;">E. RENCANA ANGGARAN BIAYA</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th>Uraian Kebutuhan</th>
                    <th style="width: 10%">Vol</th>
                    <th style="width: 10%">Sat</th>
                    <th style="width: 18%">Harga Satuan</th>
                    <th style="width: 20%">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($proposal->rab as $i => $r)
                    @php $total += $r->total_harga; @endphp
                    <tr>
                        <td class="text-center">{{ $i+1 }}</td>
                        <td>{{ $r->rincian }}</td>
                        <td class="text-center">{{ $r->volume }}</td>
                        <td class="text-center">{{ $r->satuan }}</td>
                        <td class="text-right">Rp {{ number_format($r->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right font-semibold">Rp {{ number_format($r->total_harga, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight: bold;">
                    <td colspan="5" class="text-center">TOTAL ANGGARAN KEGIATAN</td>
                    <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="section-title">F. PENUTUP</div>
        <div style="white-space: pre-wrap;">{{ $proposal->penutup }}</div>
    </div>

    <!-- TANDA TANGAN -->
    <div style="margin-top: 40px; text-align: right;">
        Garut, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
    </div>

    <div class="ttd-container">
        <!-- TTD Kiri -->
        <div class="ttd-box">
            <div>{{ ucwords(str_replace('_', ' ', $proposal->ttd_1_role)) }}</div>
            <div>Panitia Pelaksana</div>
            
            @if($proposal->ttd_1_file)
                <img src="{{ asset('storage/' . $proposal->ttd_1_file) }}" class="ttd-img" alt="TTD">
            @else
                <div style="height: 80px;"></div>
            @endif
            
            <div class="ttd-name">{{ $proposal->ttd_1_nama ?? '..........................' }}</div>
            <div>NPM. {{ $proposal->ttd_1_nim ?? '....................' }}</div>
        </div>

        <!-- TTD Kanan -->
        <div class="ttd-box">
            <div>{{ ucwords(str_replace('_', ' ', $proposal->ttd_2_role)) }}</div>
            <div>{{ $proposal->user->name }}</div>
            
            @if($proposal->ttd_2_file)
                <img src="{{ asset('storage/' . $proposal->ttd_2_file) }}" class="ttd-img" alt="TTD">
            @else
                <div style="height: 80px;"></div>
            @endif
            
            <div class="ttd-name">{{ $proposal->ttd_2_nama ?? '..........................' }}</div>
            <div>NPM. {{ $proposal->ttd_2_nim ?? '....................' }}</div>
        </div>
    </div>

</body>
</html>