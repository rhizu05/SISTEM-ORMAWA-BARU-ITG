<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Pengesahan LPJ - {{ $pengajuan->nama_kegiatan }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 20mm 20mm 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* KOP SURAT FORMAL ITG (Monokrom Standar Akademik) */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 0;
        }
        .kop-table td {
            border: none;
            vertical-align: middle;
            padding: 0;
        }
        .kop-logo {
            width: 75px;
            height: auto;
            max-height: 75px;
        }
        .kop-text {
            text-align: center;
            padding: 0 10px;
        }
        .kop-instansi {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
            color: #000;
        }
        .kop-unit {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.2;
            margin-top: 2px;
            color: #000;
        }
        .kop-alamat {
            font-size: 8.5pt;
            line-height: 1.3;
            margin-top: 3px;
            color: #000;
        }

        /* Garis ganda formal khas kop surat ITG */
        .kop-divider {
            border-top: 1px solid #000;
            border-bottom: 2.5px solid #000;
            height: 2px;
            margin-top: 6px;
            margin-bottom: 20px;
        }

        /* Section box judul khas format LPJ ITG */
        .section-header-box {
            border-top: 3px double #000;
            border-bottom: 3px double #000;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 12pt;
            text-align: center;
            margin-top: 10px;
            margin-bottom: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000;
        }

        .doc-nomor {
            text-align: center;
            font-size: 10pt;
            margin-top: -8px;
            margin-bottom: 16px;
            color: #000;
        }

        /* TABEL METADATA KEGIATAN */
        table.meta-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 16px;
        }
        table.meta-table td {
            border: none;
            padding: 3.5px 4px;
            vertical-align: top;
            font-size: 10.5pt;
            color: #000;
        }
        table.meta-table td.label-col {
            width: 160px;
            font-weight: bold;
        }
        table.meta-table td.colon-col {
            width: 15px;
            text-align: center;
        }
        table.meta-table td.value-col {
            text-align: justify;
        }

        .narrative-text {
            font-size: 10.5pt;
            text-align: justify;
            line-height: 1.45;
            margin-bottom: 16px;
            color: #000;
        }

        /* TABEL TANDA TANGAN (Standar Formal Akademik ITG) */
        table.signature-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 14px;
            margin-bottom: 14px;
        }
        table.signature-table td {
            border: none;
            vertical-align: top;
            padding: 0 6px;
            text-align: center;
        }
        .sig-role-header {
            font-weight: bold;
            font-size: 10pt;
            line-height: 1.25;
            color: #000;
        }
        .sig-org-sub {
            font-size: 9pt;
            margin-top: 2px;
            min-height: 22px;
            color: #000;
        }
        .sig-qr-container {
            margin: 6px auto;
            text-align: center;
            min-height: 95px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 10pt;
            margin-top: 4px;
            color: #000;
        }
        .sig-id {
            font-size: 9pt;
            margin-top: 1px;
            color: #000;
        }
        .badge-pending {
            display: inline-block;
            margin-top: 30px;
            margin-bottom: 30px;
            padding: 4px 8px;
            font-size: 8.5pt;
            font-style: italic;
            border: 1px dashed #666;
            color: #444;
        }

        /* FOOTER AUDIT AKADEMIK */
        .audit-footer {
            margin-top: 24px;
            border-top: 1px solid #000;
            padding-top: 6px;
            font-size: 8pt;
            line-height: 1.35;
            color: #333;
        }
        table.audit-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        table.audit-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
            color: #333;
        }
    </style>
</head>
<body>

    @php
        // Logo ITG path
        $logoItgPath = null;
        if (file_exists(public_path('images/logos/logo_itg.png'))) {
            $logoItgPath = public_path('images/logos/logo_itg.png');
        } elseif (file_exists(public_path('images/logo_itg.png'))) {
            $logoItgPath = public_path('images/logo_itg.png');
        } elseif (file_exists(public_path('images/logo-skin.png'))) {
            $logoItgPath = public_path('images/logo-skin.png');
        }

        $sigOrmawa = $pengajuan->tandaTanganLpjOrmawa();
        $sigBkhm = $pengajuan->tandaTanganLpjBkhm();
        $sigWr3 = $pengajuan->tandaTanganLpjWr3();

        $tglPelaksanaan = '-';
        if ($pengajuan->tgl_mulai_kegiatan && $pengajuan->tgl_selesai_kegiatan) {
            $tglPelaksanaan = \Carbon\Carbon::parse($pengajuan->tgl_mulai_kegiatan)->translatedFormat('d F Y') . ' s.d. ' . \Carbon\Carbon::parse($pengajuan->tgl_selesai_kegiatan)->translatedFormat('d F Y');
        } elseif ($pengajuan->tgl_mulai_kegiatan) {
            $tglPelaksanaan = \Carbon\Carbon::parse($pengajuan->tgl_mulai_kegiatan)->translatedFormat('d F Y');
        }
    @endphp

    {{-- KOP SURAT FORMAL ITG --}}
    <table class="kop-table">
        <tr>
            <td style="width: 75px; text-align: left;">
                @if($logoItgPath)
                    <img src="{{ $logoItgPath }}" class="kop-logo" alt="Logo ITG">
                @endif
            </td>
            <td class="kop-text">
                <div class="kop-instansi">Institut Teknologi Garut</div>
                <div class="kop-unit">Biro Kemahasiswaan dan Hubungan Masyarakat</div>
                <div class="kop-alamat">
                    Jl. Mayor Syamsu No. 1, Jayaraga, Tarogong Kidul, Garut, Jawa Barat 44151<br>
                    Telepon: (0262) 232773 | Laman: itg.ac.id | Pos-el: bkhm@itg.ac.id
                </div>
            </td>
            <td style="width: 75px;"></td>
        </tr>
    </table>

    <div class="kop-divider"></div>

    {{-- JUDUL LEMBAR PENGESAHAN DENGAN STYLE KHAS LPJ ITG --}}
    <div class="section-header-box">
        Lembar Pengesahan Laporan Pertanggungjawaban (LPJ)
    </div>
    <div class="doc-nomor">
        Nomor Penetapan: {{ $pengajuan->nomor_surat ?? '092/ITG-BKHM/PROP/' . date('m/Y') }}
    </div>

    {{-- METADATA KEGIATAN --}}
    <table class="meta-table">
        <tr>
            <td class="label-col">1. Nama Kegiatan</td>
            <td class="colon-col">:</td>
            <td class="value-col"><strong>{{ $pengajuan->nama_kegiatan }}</strong></td>
        </tr>
        <tr>
            <td class="label-col">2. Organisasi Pelaksana</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $pengajuan->user?->name ?? 'HIMA / Unit Kegiatan Mahasiswa ITG' }}</td>
        </tr>
        <tr>
            <td class="label-col">3. Waktu Pelaksanaan</td>
            <td class="colon-col">:</td>
            <td class="value-col">{{ $tglPelaksanaan }}</td>
        </tr>
        <tr>
            <td class="label-col">4. Total Anggaran Terealisasi</td>
            <td class="colon-col">:</td>
            <td class="value-col">Rp {{ number_format($pengajuan->dana_disetujui ?: $pengajuan->dana_diajukan, 0, ',', '.') }},-</td>
        </tr>
        <tr>
            <td class="label-col">5. Status Pengesahan</td>
            <td class="colon-col">:</td>
            <td class="value-col">
                @if($sigWr3)
                    <strong>Selesai dan Disahkan Lengkap</strong>
                @elseif($sigBkhm)
                    <strong>Telah Dikonfirmasi BKHM (Menunggu WR3)</strong>
                @else
                    <strong>Diajukan Ormawa (Dalam Evaluasi)</strong>
                @endif
            </td>
        </tr>
    </table>

    <div class="narrative-text">
        Laporan Pertanggungjawaban (LPJ) atas pelaksanaan kegiatan di atas telah diperiksa, diverifikasi rincian pertanggungjawaban anggarannya, serta disahkan secara resmi oleh pihak-pihak terkait Institut Teknologi Garut melalui sistem tanda tangan elektronik berbasis token kriptografis di bawah ini:
    </div>

    {{-- TABEL 3 KOLOM TANDA TANGAN DIGITAL RESMI ITG --}}
    <table class="signature-table">
        <tr>
            {{-- KOLOM 1: ORMAWA --}}
            <td style="width: 33.33%;">
                <div class="sig-role-header">Diserahkan Oleh:</div>
                <div class="sig-org-sub">{{ $pengajuan->user?->name }}</div>

                <div class="sig-qr-container">
                    @if($sigOrmawa)
                        @php
                            $qrUriOrmawa = \App\Services\DigitalSignatureService::generateQrCodeDataUri($sigOrmawa->verification_url, 65);
                        @endphp
                        <img src="{{ $qrUriOrmawa }}" width="65" height="65" alt="QR Ormawa" style="display: inline; width: 65px; height: 65px;">
                        <div style="font-family: monospace; font-size: 6.5pt; color: #222; margin-top: 2px;">{{ substr($sigOrmawa->token_verifikasi, 0, 16) }}...</div>
                        <div style="font-size: 6pt; font-weight: bold; text-transform: uppercase;">Ditandatangani Elektronik</div>
                    @else
                        <div class="badge-pending">Belum Ditandatangani</div>
                    @endif
                </div>

                <div class="sig-name">{{ $sigOrmawa?->nama_penandatangan ?? ($pengajuan->user?->nama_ketua ?? 'Ketua Pelaksana') }}</div>
                <div class="sig-id">NIM. {{ $sigOrmawa?->nidn_penandatangan ?? ($pengajuan->user?->nim_ketua ?? '........................') }}</div>
                <div style="font-size: 8pt; margin-top: 1px;">Ketua / Penanggung Jawab</div>
            </td>

            {{-- KOLOM 2: BKHM --}}
            <td style="width: 33.33%;">
                <div class="sig-role-header">Dikonfirmasi Oleh:</div>
                <div class="sig-org-sub">Biro Kemahasiswaan (BKHM)</div>

                <div class="sig-qr-container">
                    @if($sigBkhm)
                        @php
                            $qrUriBkhm = \App\Services\DigitalSignatureService::generateQrCodeDataUri($sigBkhm->verification_url, 65);
                        @endphp
                        <img src="{{ $qrUriBkhm }}" width="65" height="65" alt="QR BKHM" style="display: inline; width: 65px; height: 65px;">
                        <div style="font-family: monospace; font-size: 6.5pt; color: #222; margin-top: 2px;">{{ substr($sigBkhm->token_verifikasi, 0, 16) }}...</div>
                        <div style="font-size: 6pt; font-weight: bold; text-transform: uppercase;">Diverifikasi &amp; Dilegalisir</div>
                    @else
                        <div class="badge-pending">Menunggu Konfirmasi BKHM</div>
                    @endif
                </div>

                <div class="sig-name">{{ $sigBkhm?->nama_penandatangan ?? 'Encep Jianul Hayat, S.T., M.T.' }}</div>
                <div class="sig-id">NIDN. {{ $sigBkhm?->nidn_penandatangan ?? '0419089201' }}</div>
                <div style="font-size: 8pt; margin-top: 1px;">Kepala BKHM ITG</div>
            </td>

            {{-- KOLOM 3: WR3 --}}
            <td style="width: 33.33%;">
                <div class="sig-role-header">Disahkan Oleh:</div>
                <div class="sig-org-sub">Wakil Rektor III ITG</div>

                <div class="sig-qr-container">
                    @if($sigWr3)
                        @php
                            $qrUriWr3 = \App\Services\DigitalSignatureService::generateQrCodeDataUri($sigWr3->verification_url, 65);
                        @endphp
                        <img src="{{ $qrUriWr3 }}" width="65" height="65" alt="QR WR3" style="display: inline; width: 65px; height: 65px;">
                        <div style="font-family: monospace; font-size: 6.5pt; color: #222; margin-top: 2px;">{{ substr($sigWr3->token_verifikasi, 0, 16) }}...</div>
                        <div style="font-size: 6pt; font-weight: bold; text-transform: uppercase;">Disahkan Secara Sah</div>
                    @else
                        <div class="badge-pending">Menunggu Pengesahan WR3</div>
                    @endif
                </div>

                <div class="sig-name">{{ $sigWr3?->nama_penandatangan ?? 'Dr. Ayu Latifah, S.T., M.T.' }}</div>
                <div class="sig-id">NIDN. {{ $sigWr3?->nidn_penandatangan ?? '0421099301' }}</div>
                <div style="font-size: 8pt; margin-top: 1px;">Wakil Rektor III Kemahasiswaan</div>
            </td>
        </tr>
    </table>

    {{-- FOOTER AUDIT RESMI ITG --}}
    <div class="audit-footer">
        <table class="audit-table">
            <tr>
                <td style="width: 72%;">
                    <em>Catatan Keaslian Dokumen:</em> Lembar pengesahan ini merupakan bagian integral resmi dari Laporan Pertanggungjawaban (LPJ). Keabsahan dokumen dan tanda tangan digital dapat diverifikasi dengan memindai kode QR atau melalui sistem informasi resmi: <u>{{ route('dokumen.verifikasi.index') }}</u>.
                </td>
                <td style="width: 28%; text-align: right; font-family: monospace; font-size: 7.5pt;">
                    Ref: LPJ-{{ $pengajuan->id }}-{{ date('Y') }}<br>
                    Dicetak: {{ now()->translatedFormat('d/m/Y H:i') }} WIB
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
