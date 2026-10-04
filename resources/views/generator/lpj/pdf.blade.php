@php
    $c = is_string($lpj->content) ? (json_decode($lpj->content, true) ?: []) : (array) $lpj->content;
    $meta = $lpj->metadata ?? [];
    $keuangan = $meta['keuangan_items'] ?? [];
    $daftarHadir = $meta['daftar_hadir_items'] ?? [];
    $fotoDokumentasi = $meta['foto_dokumentasi'] ?? [];
    $buktiStruk = $meta['bukti_struk'] ?? [];

    $pengajuan = $pengajuan ?? null;
    $ormawaUser = $pengajuan?->user ?? $proposal?->user ?? $lpj?->user;
    $tahunAkademik = $c['tahun_akademik'] ?? ($meta['tahun_akademik'] ?? '2024-2025');
    $namaKegiatan = $pengajuan?->nama_kegiatan ?? $proposal?->nama_kegiatan ?? ($lpj?->perihal ? str_replace('Laporan Pertanggungjawaban (LPJ) - ', '', $lpj->perihal) : 'KEGIATAN MAHASISWA');
    $namaOrmawa = $ormawaUser?->name ?? 'HIMPUNAN MAHASISWA INFORMATIKA';

    // Logo Ormawa path
    $ormawaLogoPath = null;
    if ($ormawaUser && $ormawaUser->logo_ormawa && file_exists(public_path('storage/' . $ormawaUser->logo_ormawa))) {
        $ormawaLogoPath = public_path('storage/' . $ormawaUser->logo_ormawa);
    } elseif ($ormawaUser && $ormawaUser->logo_ormawa && file_exists(storage_path('app/public/' . $ormawaUser->logo_ormawa))) {
        $ormawaLogoPath = storage_path('app/public/' . $ormawaUser->logo_ormawa);
    } elseif (file_exists(public_path('images/logos/logo_itg.png'))) {
        $ormawaLogoPath = public_path('images/logos/logo_itg.png');
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kegiatan - {{ $namaKegiatan }}</title>
    <style>
        @page {
            size: A4;
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
        p {
            margin: 4px 0;
            text-align: justify;
        }
        /* Double line box header section khas ITG */
        .section-header-box {
            border-top: 3px double #000;
            border-bottom: 3px double #000;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 11.5pt;
            text-align: center;
            margin-top: 14px;
            margin-bottom: 8px;
            background-color: #fff;
        }
        .page-break {
            page-break-after: always;
        }
        .avoid-break {
            page-break-inside: avoid;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 8px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            font-size: 9.5pt;
        }
        table.data-table th {
            text-align: center;
            font-weight: bold;
        }
        .logo-circle-container {
            text-align: center;
            margin-bottom: 15px;
        }
        .logo-circle {
            width: 85px;
            height: 85px;
            border-radius: 50%;
            border: 1.5px solid #000;
            padding: 4px;
            object-fit: contain;
        }
        .main-title {
            text-align: center;
            font-weight: bold;
            font-size: 13pt;
            letter-spacing: 0.5px;
        }
        .photo-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .photo-box {
            width: 50%;
            height: 220px;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
            padding: 4px;
        }
        .photo-box img {
            max-width: 95%;
            max-height: 200px;
            object-fit: contain;
        }
    </style>
</head>
<body>

    <!-- ================= HALAMAN 1 & BATANG TUBUH LAPORAN ================= -->
    <div class="logo-circle-container">
        @if($ormawaLogoPath)
            <img src="{{ $ormawaLogoPath }}" class="logo-circle" alt="Logo Ormawa">
        @else
            <div style="display:inline-block; width:80px; height:80px; border-radius:50%; border:1.5px solid #000; line-height:80px; font-size:9pt; font-weight:bold;">LOGO ORMAWA</div>
        @endif
    </div>

    <div class="section-header-box" style="margin-top: 0; width: 70%; margin-left: auto; margin-right: auto;">
        LAPORAN
    </div>

    <div class="main-title" style="margin-top: 6px;">
        {{ strtoupper($namaKegiatan) }}<br>
        {{ strtoupper($namaOrmawa) }}<br>
        <span style="font-size: 11pt; font-weight: bold;">TAHUN AKADEMIK {{ strtoupper($tahunAkademik) }}</span>
    </div>

    <!-- Poin A -->
    <div class="section-header-box">A. Nama Kegiatan</div>
    <div style="text-align: center; font-weight: bold; margin: 4px 0 10px 0;">
        {{ strtoupper($namaKegiatan) }}
    </div>

    <!-- Poin B -->
    <div class="section-header-box">B. Waktu dan Tempat Pelaksanaan</div>
    <table style="width: 100%; border: none; font-size: 11pt; margin-top: 4px; border-collapse: collapse;">
        <tr>
            <td style="width: 100px; vertical-align: top; border: none; padding: 2px 0;">Waktu</td>
            <td style="width: 15px; vertical-align: top; border: none; padding: 2px 0;">:</td>
            <td style="border: none; padding: 2px 0;">
                {{ $c['waktu_hari_tanggal'] ?? '-' }}<br>
                {{ $c['waktu_jam'] ?? '' }}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top; border: none; padding: 2px 0;">Tempat</td>
            <td style="vertical-align: top; border: none; padding: 2px 0;">:</td>
            <td style="border: none; padding: 2px 0;">
                {!! nl2br(e($c['tempat'] ?? '-')) !!}
            </td>
        </tr>
    </table>

    <!-- Poin C -->
    <div class="section-header-box">C. Tujuan Kegiatan</div>
    <p>{!! nl2br(e($c['tujuan_kegiatan'] ?? ($c['pendahuluan'] ?? '-'))) !!}</p>

    <!-- Poin D -->
    <div class="section-header-box">D. Sasaran dan Ruang Lingkup Kegiatan</div>
    <p>Sasaran dalam kegiatan ini adalah {!! nl2br(e($c['sasaran_kegiatan'] ?? 'seluruh mahasiswa Institut Teknologi Garut.')) !!}</p>
    <p style="margin-top: 6px;">Adapun ruang lingkup kegiatan ini mencakup pembahasan mengenai:</p>
    <p style="white-space: pre-wrap; margin-left: 10px;">{!! str_replace('▪', '&bull;', nl2br(e($c['ruang_lingkup'] ?? '-'))) !!}</p>

    <!-- Poin E -->
    <div class="section-header-box">E. Metode Kegiatan</div>
    <p>{!! nl2br(e($c['metode_kegiatan'] ?? '-')) !!}</p>
    @if(!empty($c['tahapan_kegiatan']))
        <p style="margin-top: 6px; white-space: pre-wrap;">{!! str_replace('▪', '&bull;', nl2br(e($c['tahapan_kegiatan']))) !!}</p>
    @endif

    <!-- Poin F -->
    <div class="section-header-box">F. Peserta</div>
    <p>{!! nl2br(e($c['peserta_deskripsi'] ?? 'Peserta yang hadir dalam kegiatan ini terlampir pada daftar hadir terlampir.')) !!}</p>

    <!-- Poin G -->
    <div class="section-header-box">G. Jadwal Kegiatan</div>
    <div style="font-family: 'Courier New', Courier, monospace; font-size: 10pt; line-height: 1.5; white-space: pre-wrap; background: #fafafa; padding: 8px; border: 1px dashed #666;">{{ $c['jadwal_kegiatan'] ?? '-' }}</div>

    <!-- Poin H -->
    <div class="section-header-box">H. Indikator Keberhasilan</div>
    <p>Indikator keberhasilan kegiatannya adalah:<br>{!! nl2br(e($c['indikator_keberhasilan'] ?? ($c['hasil_kegiatan'] ?? '-'))) !!}</p>

    <!-- Poin I -->
    <div class="section-header-box">I. Hambatan</div>
    <p>Adapun hambatan-hambatan kegiatan yang terjadi adalah sebagai berikut:</p>
    <p style="white-space: pre-wrap; margin-left: 10px;">{!! nl2br(e($c['hambatan'] ?? '-')) !!}</p>
    <p style="margin-top: 6px;"><strong>Upaya mengatasi:</strong></p>
    <p style="white-space: pre-wrap; margin-left: 10px;">{!! nl2br(e($c['upaya_mengatasi'] ?? ($c['saran'] ?? '-'))) !!}</p>

    <!-- Poin K -->
    <div class="section-header-box">K. Penutup</div>
    <p>{!! nl2br(e($c['penutup'] ?? 'Demikian laporan kegiatan ini dibuat dengan maksud untuk memberikan gambaran pertanggungjawaban di masa yang akan datang demi kelangsungan Institut Teknologi Garut.')) !!}</p>

    <!-- BLOK TANDA TANGAN 4 PIHAK -->
    <div class="avoid-break" style="margin-top: 25px;">
        <div style="text-align: right; margin-bottom: 10px;">
            Garut, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>

        @php
            $signers = $meta['penandatangan_list'] ?? [];
            $ketuaOrmawa = $signers[0] ?? ['nama' => ($ormawaUser->nama_ketua ?? $ormawaUser->name), 'jabatan' => 'Ketua Ormawa', 'nim' => ($ormawaUser->nim_ketua ?? null)];
            $ketuaPelaksana = $signers[1] ?? ['nama' => ($ormawaUser->nama_sekretaris ?? 'Nama Pelaksana'), 'jabatan' => 'Ketua Pelaksana', 'nim' => ($ormawaUser->nim_sekretaris ?? null)];
            if (!empty($signers[1]['nim'])) {
                $ketuaPelaksana['nim'] = $signers[1]['nim'];
            }
            if (!empty($signers[0]['nim'])) {
                $ketuaOrmawa['nim'] = $signers[0]['nim'];
            }

            $sigOrmawa = $pengajuan?->tandaTanganLpjOrmawa()
                ?? $lpj?->tandaTanganDigitals?->firstWhere('role', 'ormawa_lpj')
                ?? ($lpj?->signatures_by_index[0] ?? null);

            $wr3Nama = $konfig['wr3_nama'] ?? 'Dr. Ayu Latifah, S.T., M.T.';
            $wr3Nidn = $konfig['wr3_nidn'] ?? '0421099301';
            $wr3Jabatan = $konfig['wr3_jabatan'] ?? 'Wakil Rektor III';
            $bkhmNama = $konfig['bkhm_nama'] ?? 'Encep Jianul Hayat, M.Kom.';
            $bkhmNidn = $konfig['bkhm_nidn'] ?? '0419089201';
            $bkhmJabatan = $konfig['bkhm_jabatan'] ?? 'Kepala BKHM';

            $sigBkhm = $pengajuan?->tandaTanganLpjBkhm()
                ?? $lpj?->tandaTanganDigitals?->firstWhere('role', 'bkhm_lpj') 
                ?? $lpj?->tandaTanganDigitals?->firstWhere('role', 'bkhm') 
                ?? ($lpj?->signatures_by_index[2] ?? null);
            $sigWr3 = $pengajuan?->tandaTanganLpjWr3()
                ?? $lpj?->tandaTanganDigitals?->firstWhere('role', 'wr3_lpj') 
                ?? $lpj?->tandaTanganDigitals?->firstWhere('role', 'wr3') 
                ?? ($lpj?->signatures_by_index[3] ?? null);
        @endphp

        <!-- Baris 1: Ketua Ormawa & Ketua Pelaksana -->
        <table style="width: 100%; border: none; border-collapse: collapse; text-align: center;">
            <tr>
                <td style="width: 50%; border: none; vertical-align: top; padding: 0 10px;">
                    <div style="font-weight: bold;">{{ $ketuaOrmawa['jabatan'] ?? 'Ketua Ormawa' }}</div>
                    <div style="min-height: 125px; margin-bottom: 6px;">
                        @if($sigOrmawa)
                            @include('generator.partials.qr-verifikasi', ['signature' => $sigOrmawa, 'qrSize' => 65])
                        @elseif(!empty($lpj?->signatures_by_index[0]))
                            @include('generator.partials.qr-verifikasi', ['signature' => $lpj->signatures_by_index[0], 'qrSize' => 65])
                        @else
                            <div style="height: 65px;"></div>
                        @endif
                    </div>
                    <div style="font-weight: bold; text-decoration: underline;">{{ $sigOrmawa?->nama_penandatangan ?? $ketuaOrmawa['nama'] }}</div>
                    <div style="font-size: 9pt;">{{ $sigOrmawa?->identitas_label ?? 'NIM' }}. {{ $sigOrmawa?->nidn_penandatangan ?? $ketuaOrmawa['nim'] ?? '........................' }}</div>
                </td>
                <td style="width: 50%; border: none; vertical-align: top; padding: 0 10px;">
                    <div style="font-weight: bold;">{{ $ketuaPelaksana['jabatan'] ?? 'Ketua Pelaksana' }}</div>
                    <div style="min-height: 125px; margin-bottom: 6px;">
                        @if(!empty($lpj?->signatures_by_index[1]))
                            @include('generator.partials.qr-verifikasi', ['signature' => $lpj->signatures_by_index[1], 'qrSize' => 65])
                        @else
                            <div style="height: 65px;"></div>
                        @endif
                    </div>
                    <div style="font-weight: bold; text-decoration: underline;">{{ $ketuaPelaksana['nama'] }}</div>
                    <div style="font-size: 9pt;">NIM. {{ $ketuaPelaksana['nim'] ?? '........................' }}</div>
                </td>
            </tr>
        </table>

        <!-- Baris 2: Mengetahui BKHM & WR3 -->
        <div style="text-align: center; font-weight: bold; margin-top: 15px; margin-bottom: 5px;">Mengetahui:</div>
        <table style="width: 100%; border: none; border-collapse: collapse; text-align: center;">
            <tr>
                <td style="width: 50%; border: none; vertical-align: top; padding: 0 10px;">
                    <div style="font-weight: bold;">{{ $bkhmJabatan }}</div>
                    <div style="min-height: 125px; margin-bottom: 6px;">
                        @if($sigBkhm)
                            @include('generator.partials.qr-verifikasi', ['signature' => $sigBkhm, 'qrSize' => 65])
                        @else
                            <div style="height: 65px;"></div>
                        @endif
                    </div>
                    <div style="font-weight: bold; text-decoration: underline;">{{ $sigBkhm?->nama_penandatangan ?? $bkhmNama }}</div>
                    <div style="font-size: 9pt;">{{ $sigBkhm?->identitas_label ?? 'NIDN' }}: {{ $sigBkhm?->nidn_penandatangan ?? $bkhmNidn }}</div>
                </td>
                <td style="width: 50%; border: none; vertical-align: top; padding: 0 10px;">
                    <div style="font-weight: bold;">{{ $wr3Jabatan }}</div>
                    <div style="min-height: 125px; margin-bottom: 6px;">
                        @if($sigWr3)
                            @include('generator.partials.qr-verifikasi', ['signature' => $sigWr3, 'qrSize' => 65])
                        @else
                            <div style="height: 65px;"></div>
                        @endif
                    </div>
                    <div style="font-weight: bold; text-decoration: underline;">{{ $sigWr3?->nama_penandatangan ?? $wr3Nama }}</div>
                    <div style="font-size: 9pt;">{{ $sigWr3?->identitas_label ?? 'NIDN' }}: {{ $sigWr3?->nidn_penandatangan ?? $wr3Nidn }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ================= HALAMAN 2: LAMPIRAN DAFTAR HADIR ================= -->
    <div class="page-break"></div>

    <div style="text-align: center; font-weight: bold; font-size: 12pt; margin-bottom: 15px;">
        DAFTAR HADIR {{ strtoupper($namaKegiatan) }}<br>
        {{ strtoupper($namaOrmawa) }}<br>
        Tahun Akademik {{ $tahunAkademik }}
    </div>

    <table style="width: 100%; border: none; font-size: 10.5pt; margin-bottom: 10px;">
        <tr>
            <td style="width: 110px; border: none; padding: 2px 0;">Hari, Tanggal</td>
            <td style="width: 15px; border: none; padding: 2px 0;">:</td>
            <td style="border: none; padding: 2px 0;">{{ $c['waktu_hari_tanggal'] ?? '-' }}</td>
        </tr>
        <tr>
            <td style="border: none; padding: 2px 0;">Tempat</td>
            <td style="border: none; padding: 2px 0;">:</td>
            <td style="border: none; padding: 2px 0;">{{ $c['tempat'] ?? '-' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th>Nama Lengkap</th>
                <th style="width: 150px;">Jabatan</th>
                <th style="width: 110px;">No HP</th>
                <th style="width: 100px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($daftarHadir as $i => $row)
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td>{{ $row['nama'] ?? '' }}</td>
                    <td>{{ $row['jabatan'] ?? '' }}</td>
                    <td>{{ $row['no_hp'] ?? '' }}</td>
                    <td style="text-align: center; height: 26px;"></td>
                </tr>
            @empty
                @for($k = 1; $k <= 10; $k++)
                    <tr>
                        <td style="text-align: center;">{{ $k }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="height: 26px;"></td>
                    </tr>
                @endfor
            @endforelse
        </tbody>
    </table>

    <div class="avoid-break" style="margin-top: 25px; text-align: right; width: 300px; margin-left: auto;">
        <div>Garut, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
        <div style="margin-top: 4px; font-weight: bold;">Ketua Pelaksana</div>
        <div style="height: 65px;"></div>
        <div style="font-weight: bold; text-decoration: underline;">{{ $ketuaPelaksana['nama'] }}</div>
        <div style="font-size: 9pt;">NIM. {{ $ketuaPelaksana['nim'] ?? '........................' }}</div>
    </div>

    <!-- ================= HALAMAN 3: DOKUMENTASI KEGIATAN ================= -->
    <div class="page-break"></div>

    <div style="text-align: center; font-weight: bold; font-size: 13pt; margin-bottom: 20px;">
        Dokumentasi Kegiatan
    </div>

    <table class="photo-grid">
        <tr>
            <td class="photo-box">
                @if(isset($fotoDokumentasi[0]) && file_exists(public_path('storage/' . $fotoDokumentasi[0])))
                    <img src="{{ public_path('storage/' . $fotoDokumentasi[0]) }}" alt="Foto 1">
                @else
                    <span style="color: #999; font-size: 9pt;">[ Foto Dokumentasi 1 ]</span>
                @endif
            </td>
            <td class="photo-box">
                @if(isset($fotoDokumentasi[1]) && file_exists(public_path('storage/' . $fotoDokumentasi[1])))
                    <img src="{{ public_path('storage/' . $fotoDokumentasi[1]) }}" alt="Foto 2">
                @else
                    <span style="color: #999; font-size: 9pt;">[ Foto Dokumentasi 2 ]</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="photo-box">
                @if(isset($fotoDokumentasi[2]) && file_exists(public_path('storage/' . $fotoDokumentasi[2])))
                    <img src="{{ public_path('storage/' . $fotoDokumentasi[2]) }}" alt="Foto 3">
                @else
                    <span style="color: #999; font-size: 9pt;">[ Foto Dokumentasi 3 ]</span>
                @endif
            </td>
            <td class="photo-box">
                @if(isset($fotoDokumentasi[3]) && file_exists(public_path('storage/' . $fotoDokumentasi[3])))
                    <img src="{{ public_path('storage/' . $fotoDokumentasi[3]) }}" alt="Foto 4">
                @else
                    <span style="color: #999; font-size: 9pt;">[ Foto Dokumentasi 4 ]</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="photo-box">
                @if(isset($fotoDokumentasi[4]) && file_exists(public_path('storage/' . $fotoDokumentasi[4])))
                    <img src="{{ public_path('storage/' . $fotoDokumentasi[4]) }}" alt="Foto 5">
                @else
                    <span style="color: #999; font-size: 9pt;">[ Foto Dokumentasi 5 ]</span>
                @endif
            </td>
            <td class="photo-box">
                @if(isset($fotoDokumentasi[5]) && file_exists(public_path('storage/' . $fotoDokumentasi[5])))
                    <img src="{{ public_path('storage/' . $fotoDokumentasi[5]) }}" alt="Foto 6">
                @else
                    <span style="color: #999; font-size: 9pt;">[ Foto Dokumentasi 6 ]</span>
                @endif
            </td>
        </tr>
    </table>

    <!-- ================= HALAMAN 4: LAPORAN KEUANGAN & STRUK ================= -->
    <div class="page-break"></div>

    <div style="text-align: center; font-weight: bold; font-size: 13pt; margin-bottom: 15px;">
        LAPORAN KEUANGAN
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 80px;">Tanggal</th>
                <th>Jenis Kebutuhan</th>
                <th style="width: 95px;">Pemasukan (Rp)</th>
                <th style="width: 95px;">Pengeluaran (Rp)</th>
                <th style="width: 95px;">Sisa (Rp)</th>
                <th style="width: 100px;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totIn = 0;
                $totOut = 0;
            @endphp
            @forelse($keuangan as $row)
                @php
                    $pIn = (int)($row['pemasukan'] ?? 0);
                    $pOut = (int)($row['pengeluaran'] ?? 0);
                    $totIn += $pIn;
                    $totOut += $pOut;
                @endphp
                <tr>
                    <td>{{ $row['tanggal'] ?? '-' }}</td>
                    <td>{{ $row['kebutuhan'] ?? ($row['uraian'] ?? '-') }}</td>
                    <td style="text-align: right;">{{ $pIn > 0 ? number_format($pIn, 0, ',', '.') : '0' }}</td>
                    <td style="text-align: right;">{{ $pOut > 0 ? number_format($pOut, 0, ',', '.') : '0' }}</td>
                    <td style="text-align: right;">{{ number_format((int)($row['sisa'] ?? 0), 0, ',', '.') }}</td>
                    <td>{{ $row['keterangan'] ?? 'Terlampir' }}</td>
                </tr>
            @empty
                <tr>
                    <td>14 Mei 2025</td>
                    <td>Anggaran Kemahasiswaan ITG</td>
                    <td style="text-align: right;">1.000.000</td>
                    <td style="text-align: right;">0</td>
                    <td style="text-align: right;">1.000.000</td>
                    <td>Kuitansi Terlampir</td>
                </tr>
                <tr>
                    <td>18 Mei 2025</td>
                    <td>Cetak Banner &amp; Konsumsi</td>
                    <td style="text-align: right;">0</td>
                    <td style="text-align: right;">150.000</td>
                    <td style="text-align: right;">850.000</td>
                    <td>Struk Terlampir</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Grid Bukti Struk / Kuitansi -->
    <table class="photo-grid" style="margin-top: 15px;">
        <tr>
            <td class="photo-box" style="height: 160px;">
                @if(isset($buktiStruk[0]) && file_exists(public_path('storage/' . $buktiStruk[0])))
                    <img src="{{ public_path('storage/' . $buktiStruk[0]) }}" alt="Struk 1">
                @else
                    <span style="font-size: 8.5pt; color: #888;">&lt;&lt; FOTO STRUK / KUITANSI / NOTA 1 &gt;&gt;</span>
                @endif
            </td>
            <td class="photo-box" style="height: 160px;">
                @if(isset($buktiStruk[1]) && file_exists(public_path('storage/' . $buktiStruk[1])))
                    <img src="{{ public_path('storage/' . $buktiStruk[1]) }}" alt="Struk 2">
                @else
                    <span style="font-size: 8.5pt; color: #888;">&lt;&lt; FOTO STRUK / KUITANSI / NOTA 2 &gt;&gt;</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="photo-box" style="height: 160px;">
                @if(isset($buktiStruk[2]) && file_exists(public_path('storage/' . $buktiStruk[2])))
                    <img src="{{ public_path('storage/' . $buktiStruk[2]) }}" alt="Struk 3">
                @else
                    <span style="font-size: 8.5pt; color: #888;">&lt;&lt; FOTO STRUK / KUITANSI / NOTA 3 &gt;&gt;</span>
                @endif
            </td>
            <td class="photo-box" style="height: 160px;">
                @if(isset($buktiStruk[3]) && file_exists(public_path('storage/' . $buktiStruk[3])))
                    <img src="{{ public_path('storage/' . $buktiStruk[3]) }}" alt="Struk 4">
                @else
                    <span style="font-size: 8.5pt; color: #888;">&lt;&lt; FOTO STRUK / KUITANSI / NOTA 4 &gt;&gt;</span>
                @endif
            </td>
        </tr>
    </table>

    <div class="avoid-break" style="margin-top: 20px; text-align: right; width: 300px; margin-left: auto;">
        <div>Garut, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
        <div style="margin-top: 4px; font-weight: bold;">Ketua Pelaksana</div>
        <div style="height: 60px;"></div>
        <div style="font-weight: bold; text-decoration: underline;">{{ $ketuaPelaksana['nama'] }}</div>
        <div style="font-size: 9pt;">NIM. {{ $ketuaPelaksana['nim'] ?? '........................' }}</div>
    </div>

    <!-- Catatan Resmi Template ITG -->
    <div class="avoid-break" style="margin-top: 20px; font-size: 8.5pt; line-height: 1.4; border-top: 1px solid #000; padding-top: 6px;">
        <strong>CATATAN:</strong><br>
        &bull; File surat/ proposal serta Laporan Kegiatan yang diupload sudah ditandatangan oleh ketua pelaksana, ketua ormawa, atau BEM/BPM dan cap basah.<br>
        &bull; Nama file surat/proposal pengajuan: <em>pengajuan_nama kegiatan</em>.<br>
        &bull; Nama file Laporan Kegiatan: <em>Laporan_nama kegiatan</em>.<br>
        &bull; Tanda tangan Kepala BKKH dan Wakil Rektor akan dilakukan oleh biro kemahasiswaan.
    </div>

</body>
</html>
