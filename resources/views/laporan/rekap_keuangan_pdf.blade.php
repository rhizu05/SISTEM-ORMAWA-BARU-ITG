<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Keuangan Ormawa - ITG</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 12mm 15mm 12mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #111;
        }
        .judul {
            text-align: center;
            font-weight: bold;
            font-size: 13pt;
            text-transform: uppercase;
            margin: 15px 0 5px 0;
        }
        .sub-judul {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 15px;
            color: #333;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data th {
            background-color: #f2f2f2;
            border: 1px solid #444;
            padding: 6px 4px;
            font-size: 8.5pt;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
        }
        table.data td {
            border: 1px solid #666;
            padding: 5px 4px;
            font-size: 8.5pt;
            vertical-align: top;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-semibold { font-weight: bold; }
        .footer {
            margin-top: 30px;
            width: 100%;
        }
    </style>
</head>
<body>
    @include('generator.partials.kop', ['konfig' => $konfig])

    <div class="judul">LAPORAN REKAPITULASI DANA & PENCAIRAN KEGIATAN MAHASISWA</div>
    <div class="sub-judul">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB | Periode: {{ $periodeNama ?? 'Semua Periode' }}</div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 110px;">Nama Ormawa</th>
                <th>Judul Kegiatan</th>
                <th style="width: 65px;">Tgl Pengajuan</th>
                <th style="width: 80px;">Nominal Diajukan</th>
                <th style="width: 80px;">Nominal Disetujui</th>
                <th style="width: 55px;">Termin</th>
                <th style="width: 65px;">Tgl Pencairan</th>
                <th style="width: 85px;">Status LPJ</th>
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
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-semibold">{{ $item['ormawa'] }}</td>
                    <td>{{ $item['judul_kegiatan'] }}</td>
                    <td class="text-center">{{ $item['tgl_pengajuan'] }}</td>
                    <td class="text-right">Rp {{ number_format($item['dana_diajukan'], 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item['dana_disetujui'], 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item['termin'] }}</td>
                    <td class="text-center">{{ $item['tgl_pencairan'] }}</td>
                    <td class="text-center">{{ $item['status_lpj'] }}</td>
                    <td class="text-right">Rp {{ number_format($item['sisa_saldo_pagu'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 15px;">Tidak ada data transaksi keuangan.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($items) > 0)
        <tfoot>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAL</td>
                <td class="text-right">Rp {{ number_format($totalDiajukan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalDisetujui, 0, ',', '.') }}</td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <table class="footer" style="border: none;">
        <tr style="border: none;">
            <td style="border: none; width: 60%;"></td>
            <td style="border: none; width: 40%; text-align: center;">
                <div>Garut, {{ now()->translatedFormat('d F Y') }}</div>
                <div style="margin-top: 5px; font-weight: bold;">Bagian Keuangan / Kemahasiswaan ITG</div>
                <div style="height: 60px;"></div>
                <div style="font-weight: bold; text-decoration: underline;">{{ auth()->user()->name ?? 'Pejabat Berwenang' }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
