<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Keuangan Ormawa - ITG</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 15mm 15mm 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.35;
            color: #0f172a;
        }
        .judul {
            text-align: center;
            font-weight: bold;
            font-size: 13pt;
            letter-spacing: 0.5px;
            color: #0b1528;
            text-transform: uppercase;
            margin: 6px 0 3px 0;
        }
        .sub-judul {
            text-align: center;
            font-size: 8.5pt;
            margin-bottom: 14px;
            color: #475569;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        table.data th {
            background-color: #1e3a8a;
            color: #ffffff;
            border: 1px solid #1e3a8a;
            padding: 6px 4px;
            font-size: 8pt;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
            letter-spacing: 0.3px;
        }
        table.data td {
            border: 1px solid #cbd5e1;
            padding: 5px 4px;
            font-size: 8pt;
            vertical-align: middle;
            color: #1e293b;
        }
        table.data tr.even {
            background-color: #f8fafc;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-semibold { font-weight: bold; }
        .nominal {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 600;
        }
        .footer-ttd {
            margin-top: 25px;
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        .footer-ttd td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
    </style>
</head>
<body>
    @include('generator.partials.kop', ['konfig' => $konfig])

    <div class="judul">LAPORAN REKAPITULASI DANA &amp; PENCAIRAN KEGIATAN MAHASISWA</div>
    <div class="sub-judul">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB | Periode: {{ $periodeNama ?? 'Semua Periode' }}</div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 120px;">Nama Ormawa</th>
                <th>Judul Kegiatan</th>
                <th style="width: 65px;">Tgl Pengajuan</th>
                <th style="width: 85px;">Nominal Diajukan</th>
                <th style="width: 85px;">Nominal Disetujui</th>
                <th style="width: 50px;">Termin</th>
                <th style="width: 65px;">Tgl Pencairan</th>
                <th style="width: 75px;">Status LPJ</th>
                <th style="width: 85px;">Sisa Pagu Ormawa</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDiajukan = 0;
                $totalDisetujui = 0;
            @endphp
            @forelse($items as $idx => $item)
                @php
                    $totalDiajukan += (float) $item['dana_diajukan'];
                    $totalDisetujui += (float) $item['dana_disetujui'];
                    $isEven = ($idx % 2 === 1);
                @endphp
                <tr class="{{ $isEven ? 'even' : '' }}">
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-semibold">{{ $item['ormawa'] }}</td>
                    <td>{{ $item['judul_kegiatan'] }}</td>
                    <td class="text-center">{{ $item['tgl_pengajuan'] }}</td>
                    <td class="text-right nominal">Rp {{ number_format($item['dana_diajukan'], 0, ',', '.') }}</td>
                    <td class="text-right nominal">Rp {{ number_format($item['dana_disetujui'], 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item['termin'] }}</td>
                    <td class="text-center">{{ $item['tgl_pencairan'] }}</td>
                    <td class="text-center">
                        <span style="font-size: 7.5pt; font-weight: bold; color: {{ $item['status_lpj'] === 'Selesai LPJ' ? '#166534' : ($item['status_lpj'] === 'Pending LPJ' ? '#b45309' : '#475569') }};">
                            {{ $item['status_lpj'] }}
                        </span>
                    </td>
                    <td class="text-right nominal">Rp {{ number_format($item['sisa_saldo_pagu'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 16px; color: #64748b;">Tidak ada data transaksi keuangan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($items) > 0)
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold; border-top: 2px solid #1e3a8a;">
                <td colspan="4" class="text-right" style="padding: 6px 8px; font-weight: bold;">TOTAL REALISASI</td>
                <td class="text-right nominal" style="color: #0b1528; font-size: 8.5pt;">Rp {{ number_format($totalDiajukan, 0, ',', '.') }}</td>
                <td class="text-right nominal" style="color: #0b1528; font-size: 8.5pt;">Rp {{ number_format($totalDisetujui, 0, ',', '.') }}</td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <table class="footer-ttd">
        <tr>
            <td style="width: 65%;"></td>
            <td style="width: 35%; text-align: center;">
                <div style="font-size: 8.5pt; color: #334155;">Garut, {{ now()->translatedFormat('d F Y') }}</div>
                <div style="margin-top: 4px; font-weight: bold; font-size: 9pt; color: #0b1528;">Bagian Keuangan / Kemahasiswaan ITG</div>
                <div style="height: 55px;"></div>
                <div style="font-weight: bold; text-decoration: underline; font-size: 9.5pt; color: #0b1528;">{{ auth()->user()->name ?? 'Pejabat Berwenang' }}</div>
                <div style="font-size: 8pt; color: #64748b; margin-top: 1px;">Sistem Informasi Keuangan Ormawa ITG</div>
            </td>
        </tr>
    </table>
</body>
</html>
