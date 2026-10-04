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
            margin: 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            background: #f0f0f0;
            padding: 20px;
            margin: 0;
        }
        .paper {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
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
            margin: 25px 0 20px 0;
            font-size: 14pt;
            line-height: 1.4;
        }
        .section-title {
            font-weight: bold;
            margin-top: 18px;
            display: block;
            text-decoration: underline;
            page-break-after: avoid;
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
            page-break-inside: auto;
        }
        thead {
            display: table-header-group;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 7px 8px;
            text-align: left;
        }
        th {
            background-color: #eee;
            text-align: center;
        }
        .no-border, .no-border tr, .no-border td { border: none !important; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .avoid-break {
            page-break-inside: avoid;
        }
        
        /* Print styles */
        @media print {
            body {
                background: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .paper {
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                min-height: auto !important;
                padding: 0 !important;
            }
            .no-print { display: none !important; }
            @page {
                size: A4;
                margin: 20mm;
            }
        }

        /* DomPDF Specific Styles */
        @if($pdf ?? false)
            body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .paper {
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                min-height: auto !important;
                padding: 0 !important;
            }
            .header {
                display: table;
                width: 100%;
            }
            .header-logo-cell {
                display: table-cell;
                width: 80px;
                vertical-align: middle;
                text-align: center;
            }
            .header-text-cell {
                display: table-cell;
                vertical-align: middle;
                text-align: center;
            }
        @endif
    </style>
</head>
<body>
    @if(!($pdf ?? false))
    <div class="no-print" style="text-align:center; margin-bottom:20px; background:#fff; padding:15px; border-radius:10px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
        <button onclick="window.print()" style="padding:10px 20px; cursor:pointer; background:#4f46e5; color:white; border:none; border-radius:5px; font-weight:bold;">
            Cetak / Simpan PDF
        </button>
        <a href="{{ route('generator.index') }}" style="text-decoration:none; margin-left:10px; color:#666; font-size:14px;">Kembali ke Daftar</a>
        <p style="margin-top:10px; font-size:13px; color:#666; font-family:sans-serif;">Tekan Ctrl+P. Atur margin ke Default dan centang Background graphics.</p>
    </div>
    @endif

    <div class="paper">
        <!-- KOP SURAT -->
        @if($pdf ?? false)
        <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 0;">
            <tr style="border: none;">
                <td style="width: 80px; vertical-align: middle; text-align: center; border: none; padding: 0;">
                    @php
                        $itgLogoPath = null;
                        if (!empty($konfig['kop_logo']) && file_exists(public_path('storage/' . $konfig['kop_logo']))) {
                            $itgLogoPath = public_path('storage/' . $konfig['kop_logo']);
                        } elseif (file_exists(public_path('images/logos/logo_itg.png'))) {
                            $itgLogoPath = public_path('images/logos/logo_itg.png');
                        } elseif (file_exists(public_path('images/logo_itg.png'))) {
                            $itgLogoPath = public_path('images/logo_itg.png');
                        }
                    @endphp
                    @if($itgLogoPath)
                        <img src="{{ $itgLogoPath }}" style="width: 75px; height: 75px;" alt="Logo ITG">
                    @else
                        <div style="width: 75px;"></div>
                    @endif
                </td>
                <td style="text-align: center; vertical-align: middle; border: none; padding: 0 10px;">
                    <div style="font-size: 10pt;">{{ $konfig['kop_baris1'] ?? 'KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI' }}</div>
                    <div style="font-size: 12pt; font-weight: bold; text-transform: uppercase;">{{ $konfig['kop_baris2'] ?? 'INSTITUT TEKNOLOGI GARUT' }}</div>
                    <div style="font-size: 9.5pt; font-style: italic; line-height: 1.3;">{{ $konfig['kop_baris3'] ?? 'Jalan Mayor Syamsu No. 1 Jayaraga Garut 44151 Telepon/Fax. (0262) 232773' }}</div>
                    <div style="font-size: 9.5pt; line-height: 1.3;">{{ $konfig['kop_baris4'] ?? 'Website : www.itg.ac.id | Email : info@itg.ac.id' }}</div>
                </td>
                <td style="width: 80px; vertical-align: middle; text-align: center; border: none; padding: 0;">
                    @php
                        $ormawaLogoPath = null;
                        if ($proposal->user->logo_ormawa && file_exists(public_path('storage/' . $proposal->user->logo_ormawa))) {
                            $ormawaLogoPath = public_path('storage/' . $proposal->user->logo_ormawa);
                        } elseif ($proposal->user->logo_ormawa && file_exists(storage_path('app/public/' . $proposal->user->logo_ormawa))) {
                            $ormawaLogoPath = storage_path('app/public/' . $proposal->user->logo_ormawa);
                        }
                    @endphp
                    @if($ormawaLogoPath)
                        <img src="{{ $ormawaLogoPath }}" style="width: 75px; height: 75px;" alt="Logo Ormawa">
                    @else
                        <div style="width: 75px;"></div>
                    @endif
                </td>
            </tr>
        </table>
        <!-- Garis Pembatas Ganda Khas Surat Resmi -->
        <div style="border-bottom: 3px double #000; margin-top: 8px; margin-bottom: 20px;"></div>
        @else
        <div class="header">
            @if(isset($konfig['kop_logo']) && $konfig['kop_logo'])
                <img src="{{ asset('storage/' . $konfig['kop_logo']) }}" class="header-logo" alt="Logo">
            @elseif(file_exists(public_path('images/logos/logo_itg.png')))
                <img src="{{ asset('images/logos/logo_itg.png') }}" class="header-logo" alt="Logo ITG">
            @elseif(file_exists(public_path('images/logo_itg.png')))
                <img src="{{ asset('images/logo_itg.png') }}" class="header-logo" alt="Logo ITG">
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
        @endif

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

            @if($proposal->indikator || $proposal->luaran || $proposal->dampak)
            <span class="section-title">INDIKATOR, LUARAN &amp; DAMPAK</span>
            <p><strong>Indikator:</strong> {{ $proposal->indikator ?: '-' }}</p>
            <p><strong>Luaran:</strong> {{ $proposal->luaran ?: '-' }}</p>
            <p><strong>Dampak:</strong> {{ $proposal->dampak ?: '-' }}</p>
            @endif

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
                    <tr style="font-weight: bold; background: #eee; page-break-inside: avoid;">
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

        <!-- TANDA TANGAN -->
        <div class="avoid-break" style="page-break-inside: avoid; margin-top: 30px;">
            <div style="text-align: right;">
                Garut, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </div>
            @include('generator.partials.penandatangan', [
                'penandatanganList' => $proposal->penandatangan_list,
                'signatures' => $proposal->signatures_by_index,
            ])
        </div>
    </div>

</body>
</html>
