@php
    $c = is_string($lpj->content) ? (json_decode($lpj->content, true) ?: []) : (array) $lpj->content;
    $meta = $lpj->metadata ?? [];
    $keuangan = $meta['keuangan_items'] ?? [];
    $daftarHadir = $meta['daftar_hadir_items'] ?? [];
    $fotoDokumentasi = $meta['foto_dokumentasi'] ?? [];
    $buktiStruk = $meta['bukti_struk'] ?? [];

    $ormawaUser = $proposal->user ?? $lpj->user;
    $tahunAkademik = $c['tahun_akademik'] ?? ($meta['tahun_akademik'] ?? '2024-2025');
    $namaKegiatan = $proposal->nama_kegiatan ?? ($lpj->perihal ? str_replace('Laporan Pertanggungjawaban (LPJ) - ', '', $lpj->perihal) : 'KEGIATAN MAHASISWA');
    $namaOrmawa = $ormawaUser->name ?? 'HIMPUNAN MAHASISWA INFORMATIKA';
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Preview Laporan Kegiatan (LPJ)') }}
        </h2>
    </x-slot>

    <style>
        @media print {
            /* Sembunyikan elemen non-cetak aplikasi */
            nav, header, aside, #main-sidebar, .no-print, [role="navigation"], button, a, #modal-panduan {
                display: none !important;
            }

            /* Reset overflow dashboard dan latar belakang abu-abu */
            html, body {
                background: #ffffff !important;
                background-color: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
                min-height: auto !important;
                overflow: visible !important;
            }

            div, main, section {
                overflow: visible !important;
            }

            .min-h-screen, .h-screen, .flex-1, main#main-content, .py-12 {
                height: auto !important;
                min-height: auto !important;
                padding: 0 !important;
                margin: 0 !important;
                background: #ffffff !important;
            }

            .max-w-4xl, .max-w-7xl, .container, .mx-auto, .sm\:px-6, .lg\:px-8 {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Lembar A4 LPJ */
            .lpj-sheet {
                width: 100% !important;
                max-width: 100% !important;
                min-height: auto !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Pengaturan halaman cetak A4 */
            @page {
                size: A4 portrait;
                margin: 15mm 20mm 20mm 20mm;
            }

            /* Aturan pemisahan halaman & lampiran */
            .avoid-break, tr, table {
                page-break-inside: avoid;
            }

            .page-break {
                page-break-before: always;
                break-before: page;
                margin: 0 !important;
                padding: 0 !important;
                height: 0 !important;
            }

            .lampiran-divider {
                display: none !important;
            }
        }
    </style>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6 no-print">
                <a href="{{ route('archive.index') }}" class="inline-flex items-center gap-2 bg-gray-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700 transition">
                    &larr; Kembali ke Arsip
                </a>
                <div class="flex gap-3">
                    <a href="{{ route('generator.lpj.pdf', $lpj) }}" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-700 transition shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unduh PDF Resmi
                    </a>
                    <button onclick="window.print()" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Cetak Laporan
                    </button>
                </div>
            </div>

            <!-- LEMBAR KERTAS A4 SIMULASI RESMI -->
            <div class="lpj-sheet bg-white p-12 shadow-2xl mx-auto rounded-sm border border-gray-200" style="width: 210mm; min-height: 297mm; font-family: 'Times New Roman', Times, serif; color: #000; line-height: 1.5;">
                
                <!-- COVER & LOGO ORMAWA -->
                <div class="text-center mb-4">
                    @if(isset($ormawaUser) && $ormawaUser->logo_ormawa)
                        <img src="{{ asset('storage/' . $ormawaUser->logo_ormawa) }}" style="width: 85px; height: 85px; border-radius: 50%; border: 1.5px solid #000; padding: 4px; object-fit: contain; margin: 0 auto;" alt="Logo Ormawa">
                    @else
                        <div style="width: 85px; height: 85px; border-radius: 50%; border: 1.5px solid #000; line-height: 85px; margin: 0 auto; font-weight: bold; font-size: 10pt;">LOGO ORMAWA</div>
                    @endif
                </div>

                <div style="border-top: 3px double #000; border-bottom: 3px double #000; padding: 4px; font-weight: bold; font-size: 12pt; text-align: center; width: 65%; margin: 0 auto 10px auto;">
                    LAPORAN
                </div>

                <div style="text-align: center; font-weight: bold; font-size: 13pt; margin-bottom: 25px;">
                    {{ strtoupper($namaKegiatan) }}<br>
                    {{ strtoupper($namaOrmawa) }}<br>
                    <span style="font-size: 11.5pt;">TAHUN AKADEMIK {{ strtoupper($tahunAkademik) }}</span>
                </div>

                <!-- POIN A -->
                <div style="border-top: 3px double #000; border-bottom: 3px double #000; padding: 3px 6px; font-weight: bold; text-align: center; margin: 15px 0 6px 0;">
                    A. Nama Kegiatan
                </div>
                <div style="text-align: center; font-weight: bold; margin-bottom: 12px;">
                    {{ strtoupper($namaKegiatan) }}
                </div>

                <!-- POIN B -->
                <div style="border-top: 3px double #000; border-bottom: 3px double #000; padding: 3px 6px; font-weight: bold; text-align: center; margin: 15px 0 6px 0;">
                    B. Waktu dan Tempat Pelaksanaan
                </div>
                <div style="display: grid; grid-template-columns: 120px 15px 1fr; margin-bottom: 12px; font-size: 11pt;">
                    <div>Waktu</div>
                    <div>:</div>
                    <div>
                        {{ $c['waktu_hari_tanggal'] ?? '-' }}<br>
                        {{ $c['waktu_jam'] ?? '' }}
                    </div>
                    <div>Tempat</div>
                    <div>:</div>
                    <div>{{ $c['tempat'] ?? '-' }}</div>
                </div>

                <!-- POIN C -->
                <div style="border-top: 3px double #000; border-bottom: 3px double #000; padding: 3px 6px; font-weight: bold; text-align: center; margin: 15px 0 6px 0;">
                    C. Tujuan Kegiatan
                </div>
                <p style="text-align: justify; margin-bottom: 12px;">{!! nl2br(e($c['tujuan_kegiatan'] ?? ($c['pendahuluan'] ?? '-'))) !!}</p>

                <!-- POIN D -->
                <div style="border-top: 3px double #000; border-bottom: 3px double #000; padding: 3px 6px; font-weight: bold; text-align: center; margin: 15px 0 6px 0;">
                    D. Sasaran dan Ruang Lingkup Kegiatan
                </div>
                <p style="text-align: justify;">Sasaran dalam kegiatan ini adalah {!! nl2br(e($c['sasaran_kegiatan'] ?? 'seluruh mahasiswa Institut Teknologi Garut.')) !!}</p>
                <p style="margin-top: 4px;">Adapun ruang lingkup kegiatan ini mencakup pembahasan mengenai:</p>
                <div style="margin-left: 12px; white-space: pre-wrap; margin-bottom: 12px;">{!! nl2br(e($c['ruang_lingkup'] ?? '-')) !!}</div>

                <!-- POIN E -->
                <div style="border-top: 3px double #000; border-bottom: 3px double #000; padding: 3px 6px; font-weight: bold; text-align: center; margin: 15px 0 6px 0;">
                    E. Metode Kegiatan
                </div>
                <p style="text-align: justify;">{!! nl2br(e($c['metode_kegiatan'] ?? '-')) !!}</p>
                @if(!empty($c['tahapan_kegiatan']))
                    <div style="margin-top: 4px; white-space: pre-wrap; margin-bottom: 12px;">{!! nl2br(e($c['tahapan_kegiatan'])) !!}</div>
                @endif

                <!-- POIN F -->
                <div style="border-top: 3px double #000; border-bottom: 3px double #000; padding: 3px 6px; font-weight: bold; text-align: center; margin: 15px 0 6px 0;">
                    F. Peserta
                </div>
                <p style="text-align: justify; margin-bottom: 12px;">{!! nl2br(e($c['peserta_deskripsi'] ?? 'Peserta yang hadir dalam kegiatan ini terlampir pada daftar hadir terlampir.')) !!}</p>

                <!-- POIN G -->
                <div style="border-top: 3px double #000; border-bottom: 3px double #000; padding: 3px 6px; font-weight: bold; text-align: center; margin: 15px 0 6px 0;">
                    G. Jadwal Kegiatan
                </div>
                <div style="font-family: monospace; font-size: 10pt; line-height: 1.5; white-space: pre-wrap; background: #f8fafc; padding: 10px; border: 1px dashed #94a3b8; margin-bottom: 12px;">{{ $c['jadwal_kegiatan'] ?? '-' }}</div>

                <!-- POIN H -->
                <div style="border-top: 3px double #000; border-bottom: 3px double #000; padding: 3px 6px; font-weight: bold; text-align: center; margin: 15px 0 6px 0;">
                    H. Indikator Keberhasilan
                </div>
                <p style="text-align: justify; margin-bottom: 12px;">Indikator keberhasilan kegiatannya adalah:<br>{!! nl2br(e($c['indikator_keberhasilan'] ?? ($c['hasil_kegiatan'] ?? '-'))) !!}</p>

                <!-- POIN I -->
                <div style="border-top: 3px double #000; border-bottom: 3px double #000; padding: 3px 6px; font-weight: bold; text-align: center; margin: 15px 0 6px 0;">
                    I. Hambatan
                </div>
                <p style="margin-bottom: 4px;">Adapun hambatan-hambatan kegiatan yang terjadi adalah sebagai berikut:</p>
                <div style="margin-left: 12px; white-space: pre-wrap;">{!! nl2br(e($c['hambatan'] ?? '-')) !!}</div>
                <p style="margin-top: 6px; font-weight: bold;">Upaya mengatasi:</p>
                <div style="margin-left: 12px; white-space: pre-wrap; margin-bottom: 12px;">{!! nl2br(e($c['upaya_mengatasi'] ?? ($c['saran'] ?? '-'))) !!}</div>

                <!-- POIN K -->
                <div style="border-top: 3px double #000; border-bottom: 3px double #000; padding: 3px 6px; font-weight: bold; text-align: center; margin: 15px 0 6px 0;">
                    K. Penutup
                </div>
                <p style="text-align: justify; margin-bottom: 25px;">{!! nl2br(e($c['penutup'] ?? 'Demikian laporan kegiatan ini dibuat demi kemajuan Institut Teknologi Garut.')) !!}</p>

                <!-- TANDA TANGAN 4 PIHAK -->
                <div style="margin-top: 30px;">
                    <div style="text-align: right; margin-bottom: 15px;">Garut, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
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
                    @endphp
                    
                    <!-- Baris 1: Ketua Ormawa & Ketua Pelaksana -->
                    <table style="width: 100%; border: none; border-collapse: collapse; text-align: center; margin-bottom: 25px;">
                        <tr>
                            <td style="width: 50%; border: none; vertical-align: top; padding: 0 15px;">
                                <div style="font-weight: bold; margin-bottom: 6px;">{{ $ketuaOrmawa['jabatan'] ?? 'Ketua Ormawa' }}</div>
                                <div style="min-height: 135px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                                    @if(!empty($lpj->signatures_by_index[0]))
                                        @include('generator.partials.qr-verifikasi', ['signature' => $lpj->signatures_by_index[0], 'qrSize' => 65])
                                    @endif
                                </div>
                                <div style="font-weight: bold; text-decoration: underline;">{{ $ketuaOrmawa['nama'] }}</div>
                                <div style="font-size: 9.5pt; color: #334155; margin-top: 2px;">NIM. {{ $ketuaOrmawa['nim'] ?? '........................' }}</div>
                            </td>
                            <td style="width: 50%; border: none; vertical-align: top; padding: 0 15px;">
                                <div style="font-weight: bold; margin-bottom: 6px;">{{ $ketuaPelaksana['jabatan'] ?? 'Ketua Pelaksana' }}</div>
                                <div style="min-height: 135px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                                    @if(!empty($lpj->signatures_by_index[1]))
                                        @include('generator.partials.qr-verifikasi', ['signature' => $lpj->signatures_by_index[1], 'qrSize' => 65])
                                    @endif
                                </div>
                                <div style="font-weight: bold; text-decoration: underline;">{{ $ketuaPelaksana['nama'] }}</div>
                                <div style="font-size: 9.5pt; color: #334155; margin-top: 2px;">NIM. {{ $ketuaPelaksana['nim'] ?? '........................' }}</div>
                            </td>
                        </tr>
                    </table>

                    @php
                        $wr3Nama = $konfig['wr3_nama'] ?? 'Dr. Ayu Latifah, S.T., M.T.';
                        $wr3Nidn = $konfig['wr3_nidn'] ?? '0421099301';
                        $wr3Jabatan = $konfig['wr3_jabatan'] ?? 'Wakil Rektor III';
                        $bkhmNama = $konfig['bkhm_nama'] ?? 'Encep Jianul Hayat, M.Kom.';
                        $bkhmNidn = $konfig['bkhm_nidn'] ?? '0419089201';
                        $bkhmJabatan = $konfig['bkhm_jabatan'] ?? 'Kepala BKHM';

                        $sigBkhm = $lpj->tandaTanganDigitals->firstWhere('role', 'bkhm_lpj') 
                            ?? $lpj->tandaTanganDigitals->firstWhere('role', 'bkhm') 
                            ?? ($lpj->signatures_by_index[2] ?? null);
                        $sigWr3 = $lpj->tandaTanganDigitals->firstWhere('role', 'wr3_lpj') 
                            ?? $lpj->tandaTanganDigitals->firstWhere('role', 'wr3') 
                            ?? ($lpj->signatures_by_index[3] ?? null);
                    @endphp

                    <div style="text-align: center; font-weight: bold; margin-bottom: 12px;">Mengetahui:</div>
                    <table style="width: 100%; border: none; border-collapse: collapse; text-align: center;">
                        <tr>
                            <td style="width: 50%; border: none; vertical-align: top; padding: 0 15px;">
                                <div style="font-weight: bold; margin-bottom: 6px;">{{ $bkhmJabatan }}</div>
                                <div style="min-height: 135px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                                    @if($sigBkhm)
                                        @include('generator.partials.qr-verifikasi', ['signature' => $sigBkhm, 'qrSize' => 65])
                                    @else
                                        <div style="height: 70px;"></div>
                                    @endif
                                </div>
                                <div style="font-weight: bold; text-decoration: underline;">{{ $sigBkhm?->nama_penandatangan ?? $bkhmNama }}</div>
                                <div style="font-size: 9.5pt; color: #334155; margin-top: 2px;">NIDN: {{ $sigBkhm?->nidn_penandatangan ?? $bkhmNidn }}</div>
                            </td>
                            <td style="width: 50%; border: none; vertical-align: top; padding: 0 15px;">
                                <div style="font-weight: bold; margin-bottom: 6px;">{{ $wr3Jabatan }}</div>
                                <div style="min-height: 135px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                                    @if($sigWr3)
                                        @include('generator.partials.qr-verifikasi', ['signature' => $sigWr3, 'qrSize' => 65])
                                    @else
                                        <div style="height: 70px;"></div>
                                    @endif
                                </div>
                                <div style="font-weight: bold; text-decoration: underline;">{{ $sigWr3?->nama_penandatangan ?? $wr3Nama }}</div>
                                <div style="font-size: 9.5pt; color: #334155; margin-top: 2px;">NIDN: {{ $sigWr3?->nidn_penandatangan ?? $wr3Nidn }}</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- PEMISAH LAMPIRAN 1 -->
                <div class="page-break"></div>
                <div class="lampiran-divider" style="border-top: 2px dashed #999; margin: 50px 0 30px 0; padding-top: 15px; text-align: center; color: #666; font-size: 10pt;">
                    --- Lembar Lampiran 1: Daftar Hadir ---
                </div>

                <div style="text-align: center; font-weight: bold; font-size: 12pt; margin-bottom: 15px;">
                    DAFTAR HADIR {{ strtoupper($namaKegiatan) }}<br>
                    {{ strtoupper($namaOrmawa) }}<br>
                    Tahun Akademik {{ $tahunAkademik }}
                </div>

                <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 10pt;">
                    <thead>
                        <tr style="background: #f1f5f9;">
                            <th style="border: 1px solid #000; padding: 6px; width: 40px; text-align: center;">No</th>
                            <th style="border: 1px solid #000; padding: 6px; text-align: left;">Nama Lengkap</th>
                            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 160px;">Jabatan</th>
                            <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 120px;">No HP</th>
                            <th style="border: 1px solid #000; padding: 6px; text-align: center; width: 100px;">Tanda Tangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daftarHadir as $i => $row)
                            <tr>
                                <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $i + 1 }}</td>
                                <td style="border: 1px solid #000; padding: 6px;">{{ $row['nama'] ?? '' }}</td>
                                <td style="border: 1px solid #000; padding: 6px;">{{ $row['jabatan'] ?? '' }}</td>
                                <td style="border: 1px solid #000; padding: 6px;">{{ $row['no_hp'] ?? '' }}</td>
                                <td style="border: 1px solid #000; padding: 6px; text-align: center; height: 30px;"></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="border: 1px solid #000; padding: 10px; text-align: center; color: #888;">(Presensi terlampir manual)</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- PEMISAH LAMPIRAN 2 -->
                <div class="page-break"></div>
                <div class="lampiran-divider" style="border-top: 2px dashed #999; margin: 50px 0 30px 0; padding-top: 15px; text-align: center; color: #666; font-size: 10pt;">
                    --- Lembar Lampiran 2: Dokumentasi Kegiatan ---
                </div>
                <div style="text-align: center; font-weight: bold; font-size: 13pt; margin-bottom: 20px;">Dokumentasi Kegiatan</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px;">
                    @for($i = 0; $i < 6; $i++)
                        <div style="height: 180px; border: 1px solid #000; display: flex; align-items: center; justify-content: center; background: #fafafa;">
                            @if(isset($fotoDokumentasi[$i]) && file_exists(public_path('storage/' . $fotoDokumentasi[$i])))
                                <img src="{{ asset('storage/' . $fotoDokumentasi[$i]) }}" style="max-height: 170px; max-width: 95%; object-fit: contain;">
                            @else
                                <span style="font-size: 9pt; color: #94a3b8;">[ Foto Dokumentasi {{ $i + 1 }} ]</span>
                            @endif
                        </div>
                    @endfor
                </div>

                <!-- PEMISAH LAMPIRAN 3 -->
                <div class="page-break"></div>
                <div class="lampiran-divider" style="border-top: 2px dashed #999; margin: 50px 0 30px 0; padding-top: 15px; text-align: center; color: #666; font-size: 10pt;">
                    --- Lembar Lampiran 3: Laporan Keuangan ---
                </div>
                <div style="text-align: center; font-weight: bold; font-size: 13pt; margin-bottom: 15px;">LAPORAN KEUANGAN</div>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10pt;">
                    <thead>
                        <tr style="background: #f1f5f9;">
                            <th style="border: 1px solid #000; padding: 6px; width: 90px; text-align: left;">Tanggal</th>
                            <th style="border: 1px solid #000; padding: 6px; text-align: left;">Jenis Kebutuhan</th>
                            <th style="border: 1px solid #000; padding: 6px; width: 105px; text-align: right;">Pemasukan (Rp)</th>
                            <th style="border: 1px solid #000; padding: 6px; width: 105px; text-align: right;">Pengeluaran (Rp)</th>
                            <th style="border: 1px solid #000; padding: 6px; width: 105px; text-align: right;">Sisa (Rp)</th>
                            <th style="border: 1px solid #000; padding: 6px; width: 120px; text-align: left;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($keuangan as $row)
                            <tr>
                                <td style="border: 1px solid #000; padding: 6px;">{{ $row['tanggal'] ?? '-' }}</td>
                                <td style="border: 1px solid #000; padding: 6px;">{{ $row['kebutuhan'] ?? ($row['uraian'] ?? '-') }}</td>
                                <td style="border: 1px solid #000; padding: 6px; text-align: right;">{{ number_format((int)($row['pemasukan'] ?? 0), 0, ',', '.') }}</td>
                                <td style="border: 1px solid #000; padding: 6px; text-align: right;">{{ number_format((int)($row['pengeluaran'] ?? 0), 0, ',', '.') }}</td>
                                <td style="border: 1px solid #000; padding: 6px; text-align: right;">{{ number_format((int)($row['sisa'] ?? 0), 0, ',', '.') }}</td>
                                <td style="border: 1px solid #000; padding: 6px;">{{ $row['keterangan'] ?? 'Terlampir' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="border: 1px solid #000; padding: 10px; text-align: center; color: #888;">Belum ada rincian transaksi keuangan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;">
                    @for($i = 0; $i < 4; $i++)
                        <div style="height: 140px; border: 1px solid #000; display: flex; align-items: center; justify-content: center; background: #fafafa;">
                            @if(isset($buktiStruk[$i]) && file_exists(public_path('storage/' . $buktiStruk[$i])))
                                <img src="{{ asset('storage/' . $buktiStruk[$i]) }}" style="max-height: 130px; max-width: 95%; object-fit: contain;">
                            @else
                                <span style="font-size: 8.5pt; color: #94a3b8;">&lt;&lt; FOTO STRUK / KUITANSI {{ $i + 1 }} &gt;&gt;</span>
                            @endif
                        </div>
                    @endfor
                </div>

                <div style="font-size: 8.5pt; line-height: 1.4; border-top: 1px solid #000; padding-top: 8px;">
                    <strong>CATATAN:</strong><br>
                    &bull; File surat/ proposal serta Laporan Kegiatan yang diupload sudah ditandatangan oleh ketua pelaksana, ketua ormawa, atau BEM/BPM dan cap basah.<br>
                    &bull; Nama file surat/proposal pengajuan: <em>pengajuan_nama kegiatan</em>.<br>
                    &bull; Nama file Laporan Kegiatan: <em>Laporan_nama kegiatan</em>.<br>
                    &bull; Tanda tangan Kepala BKKH dan Wakil Rektor akan dilakukan oleh biro kemahasiswaan.
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
