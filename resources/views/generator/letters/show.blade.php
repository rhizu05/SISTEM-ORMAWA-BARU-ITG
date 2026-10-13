<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Preview Surat') }}</h2></x-slot>

    <style>
        @media print {
            nav, header, aside, #main-sidebar, .no-print, [role="navigation"], button, a, #modal-panduan {
                display: none !important;
            }

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

            .letter-sheet {
                width: 100% !important;
                max-width: 100% !important;
                min-height: auto !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            @page {
                size: A4 portrait;
                margin: 15mm 20mm 20mm 20mm;
            }
        }
    </style>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if($letter->type === 'lpj')
                <div class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-xl text-center">
                    <p class="text-sm text-purple-800 font-semibold mb-2">Dokumen ini adalah Laporan Pertanggungjawaban (LPJ) resmi.</p>
                    <a href="{{ route('generator.lpj.show', $letter) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-bold transition">
                        Buka Tampilan Resmi LPJ &rarr;
                    </a>
                </div>
            @endif

            @php
                $m = $letter->metadata ?? [];
                $lampiranPath = $letter->file_lampiran_pdf ?? ($m['file_lampiran_pdf'] ?? null);
            @endphp

            @if($lampiranPath)
                <div class="mb-4 p-4 bg-indigo-50 border border-indigo-200 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 no-print">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-600 text-white rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <div class="font-bold text-indigo-950 text-sm">Lampiran Berkas PDF Terlampir</div>
                            <div class="text-xs text-indigo-700">Berkas ini otomatis digabungkan di akhir dokumen resmi saat Anda menekan tombol <strong>Unduh PDF</strong>.</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ asset('storage/' . $lampiranPath) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-indigo-300 hover:bg-indigo-50 text-indigo-800 text-xs font-semibold rounded-lg shadow-sm transition">
                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Buka Tab Baru</span>
                        </a>
                        <a href="{{ asset('storage/' . $lampiranPath) }}" download class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh Berkas</span>
                        </a>
                    </div>
                </div>
            @endif

            <div class="flex justify-between mb-4 no-print">
                <a href="{{ route('generator.letters.create') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Kembali</a>
                <div class="flex gap-2">
                    <a href="{{ route('generator.letters.pdf', $letter) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Unduh PDF</a>
                    <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Cetak</button>
                </div>
            </div>

            <div class="letter-sheet bg-white p-10 shadow-lg mx-auto" style="width: 210mm; min-height: 297mm; font-family: 'Times New Roman';">
                @if($letter->user && $letter->user->hasAnyRole(['ormawa', 'bem', 'bpm']))
                    @include('generator.partials.kop-ormawa', ['user' => $letter->user])
                @else
                    @include('generator.partials.kop', ['konfig' => $konfig])
                @endif

                <div style="text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 25px; font-size: 13pt;">
                    @if($letter->type==='undangan') SURAT UNDANGAN
                    @elseif($letter->type==='tugas') SURAT TUGAS / MANDAT
                    @elseif($letter->type==='permohonan') SURAT PERMOHONAN
                    @elseif($letter->type==='keterangan_aktif') SURAT KETERANGAN AKTIF
                    @else SURAT @endif
                </div>

                <table style="width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 11pt; line-height: 1.5;">
                    <tr>
                        <td style="width: 75px; vertical-align: top;">Nomor</td>
                        <td style="width: 15px; vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ $letter->nomor_surat ?? '___/___/___' }}</td>
                        <td style="text-align: right; vertical-align: top;">Garut, {{ \Carbon\Carbon::parse($letter->created_at)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Lampiran</td>
                        <td style="vertical-align: top;">:</td>
                        <td colspan="2" style="vertical-align: top;">{{ $letter->lampiran ?? ($m['lampiran'] ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Perihal</td>
                        <td style="vertical-align: top;">:</td>
                        <td colspan="2" style="vertical-align: top; font-weight: bold;">{{ $letter->perihal }}</td>
                    </tr>
                </table>

                <div style="margin-bottom: 20px; line-height: 1.4;">
                    Kepada Yth.<br>
                    <strong>{{ $m['tujuan'] ?? '..........................' }}</strong><br>
                    di Tempat
                </div>

                @if($letter->type==='undangan')
                    <div style="text-align: justify; line-height: 1.6;">{{ $m['kalimat_pembuka'] ?? 'Dengan hormat, sehubungan dengan akan dilaksanakannya kegiatan kami, bersama ini kami bermaksud mengundang Bapak/Ibu untuk dapat hadir pada kegiatan yang diselenggarakan:' }}</div>
                    <table style="margin-top:14px; margin-bottom:14px; margin-left: 18px; line-height:1.8;">
                        <tr><td style="width: 140px;">Nama Acara</td><td>: {{ $m['nama_acara'] ?? '-' }}</td></tr>
                        <tr><td>Hari / Tanggal</td><td>: {{ $m['hari_tanggal'] ?? '-' }}</td></tr>
                        <tr><td>Waktu</td><td>: {{ $m['waktu'] ?? '-' }}</td></tr>
                        <tr><td>Tempat</td><td>: {{ $m['tempat'] ?? '-' }}</td></tr>
                    </table>
                    <div style="text-align: justify; line-height: 1.6;">{{ $m['kalimat_penutup'] ?? 'Demikian surat undangan ini kami sampaikan. Atas perhatian, kehadiran, dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.' }}</div>
                @elseif($letter->type==='tugas')
                    <div style="text-align: justify; line-height: 1.6;">{{ $m['kalimat_pembuka'] ?? 'Yang bertanda tangan di bawah ini Pengurus Organisasi Kemahasiswaan Institut Teknologi Garut, dengan ini memberikan tugas / mandat kepada:' }}</div>
                    @php
                        $petugasList = $m['petugas_list'] ?? [];
                        if (empty($petugasList) && (!empty($m['nama_petugas']) || !empty($m['nim']))) {
                            $petugasList = [['nama' => $m['nama_petugas'] ?? '-', 'nim' => $m['nim'] ?? '-']];
                        }
                    @endphp

                    @if(count($petugasList) > 1)
                        <table style="width: 100%; border-collapse: collapse; margin-top: 14px; margin-bottom: 14px; font-size: 11pt; border: 1px solid #cbd5e1;">
                            <thead>
                                <tr style="background-color: #f8fafc; border-bottom: 1px solid #94a3b8; text-align: left;">
                                    <th style="width: 45px; padding: 6px 10px; border: 1px solid #cbd5e1; text-align: center;">No</th>
                                    <th style="padding: 6px 10px; border: 1px solid #cbd5e1;">Nama Penerima Tugas / Mandat</th>
                                    <th style="width: 180px; padding: 6px 10px; border: 1px solid #cbd5e1;">NIM</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($petugasList as $idx => $p)
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1; text-align: center;">{{ $idx + 1 }}.</td>
                                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold;">{{ $p['nama'] }}</td>
                                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-family: monospace;">{{ $p['nim'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table style="margin-bottom: 14px; margin-left: 18px; line-height: 1.8;">
                            <tr><td style="width: 160px; vertical-align: top;">Uraian Tugas</td><td>: {{ $m['uraian_tugas'] ?? '-' }}</td></tr>
                            <tr><td style="vertical-align: top;">Tanggal Pelaksanaan</td><td>: {{ $m['tanggal_pelaksanaan'] ?? '-' }}</td></tr>
                        </table>
                    @else
                        <table style="margin-top:14px; margin-bottom:14px; margin-left: 18px; line-height:1.8;">
                            <tr><td style="width: 160px;">Nama</td><td>: {{ $petugasList[0]['nama'] ?? ($m['nama_petugas'] ?? '-') }}</td></tr>
                            <tr><td>NIM</td><td>: {{ $petugasList[0]['nim'] ?? ($m['nim'] ?? '-') }}</td></tr>
                            <tr><td>Uraian Tugas</td><td>: {{ $m['uraian_tugas'] ?? '-' }}</td></tr>
                            <tr><td>Tanggal Pelaksanaan</td><td>: {{ $m['tanggal_pelaksanaan'] ?? '-' }}</td></tr>
                        </table>
                    @endif
                    <div style="text-align: justify; line-height: 1.6;">{{ $m['kalimat_penutup'] ?? 'Untuk melaksanakan tugas tersebut dengan penuh tanggung jawab. Surat tugas ini dibuat untuk dapat dipergunakan sebagaimana mestinya.' }}</div>
                @elseif($letter->type==='permohonan')
                    <div style="text-align: justify; line-height: 1.6;">{{ $m['kalimat_pembuka'] ?? 'Dengan hormat, sehubungan dengan agenda kegiatan yang akan kami selenggarakan, bersama ini kami mengajukan permohonan peminjaman:' }}</div>
                    <table style="margin-top:14px; margin-bottom:14px; margin-left: 18px; line-height:1.8;">
                        <tr><td style="width: 170px;">Nama Alat / Ruangan</td><td>: {{ $m['nama_alat_tempat'] ?? '-' }}</td></tr>
                        <tr><td>Waktu Penggunaan</td><td>: {{ $m['waktu_penggunaan'] ?? '-' }}</td></tr>
                        <tr><td>Alasan / Tujuan</td><td>: {{ $m['alasan_tujuan'] ?? '-' }}</td></tr>
                    </table>
                    <div style="text-align: justify; line-height: 1.6;">{{ $m['kalimat_penutup'] ?? 'Demikian permohonan ini kami ajukan. Atas perhatian, izin, dan bantuan Bapak/Ibu, kami ucapkan terima kasih.' }}</div>
                @elseif($letter->type==='keterangan_aktif')
                    <div style="text-align: justify; line-height: 1.6;">{{ $m['kalimat_pembuka'] ?? 'Yang bertanda tangan di bawah ini Pengurus Organisasi Kemahasiswaan Institut Teknologi Garut, dengan ini menerangkan bahwa:' }}</div>
                    @php
                        $mahasiswaList = $m['mahasiswa_list'] ?? [];
                        if (empty($mahasiswaList) && (!empty($m['nama_mahasiswa']) || !empty($m['nim']))) {
                            $mahasiswaList = [[
                                'nama'    => $m['nama_mahasiswa'] ?? '-',
                                'nim'     => $m['nim'] ?? '-',
                                'jabatan' => $m['jabatan'] ?? '-',
                            ]];
                        }
                    @endphp

                    @if(count($mahasiswaList) > 1)
                        <table style="width: 100%; border-collapse: collapse; margin-top: 14px; margin-bottom: 14px; font-size: 11pt; border: 1px solid #cbd5e1;">
                            <thead>
                                <tr style="background-color: #f8fafc; border-bottom: 1px solid #94a3b8; text-align: left;">
                                    <th style="width: 45px; padding: 6px 10px; border: 1px solid #cbd5e1; text-align: center;">No</th>
                                    <th style="padding: 6px 10px; border: 1px solid #cbd5e1;">Nama Mahasiswa</th>
                                    <th style="width: 150px; padding: 6px 10px; border: 1px solid #cbd5e1;">NIM</th>
                                    <th style="padding: 6px 10px; border: 1px solid #cbd5e1;">Jabatan di Organisasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mahasiswaList as $idx => $mhs)
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1; text-align: center;">{{ $idx + 1 }}.</td>
                                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-weight: bold;">{{ $mhs['nama'] }}</td>
                                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1; font-family: monospace;">{{ $mhs['nim'] }}</td>
                                        <td style="padding: 6px 10px; border: 1px solid #cbd5e1;">{{ $mhs['jabatan'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table style="margin-bottom: 14px; margin-left: 18px; line-height: 1.8;">
                            <tr><td style="width: 170px; vertical-align: top;">Keperluan</td><td>: {{ $m['keperluan'] ?? '-' }}</td></tr>
                        </table>
                    @else
                        <table style="margin-top:14px; margin-bottom:14px; margin-left: 18px; line-height:1.8;">
                            <tr><td style="width: 170px;">Nama</td><td>: {{ $mahasiswaList[0]['nama'] ?? ($m['nama_mahasiswa'] ?? '-') }}</td></tr>
                            <tr><td>NIM</td><td>: {{ $mahasiswaList[0]['nim'] ?? ($m['nim'] ?? '-') }}</td></tr>
                            <tr><td>Jabatan di Organisasi</td><td>: {{ $mahasiswaList[0]['jabatan'] ?? ($m['jabatan'] ?? '-') }}</td></tr>
                            <tr><td>Keperluan</td><td>: {{ $m['keperluan'] ?? '-' }}</td></tr>
                        </table>
                    @endif
                    <div style="text-align: justify; line-height: 1.6;">{{ $m['kalimat_penutup'] ?? 'Adalah benar mahasiswa/anggota aktif pada organisasi kami. Surat keterangan ini kami buat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.' }}</div>
                @else
                    @if(!empty($m['kalimat_pembuka']))
                        <div style="text-align: justify; line-height: 1.6; margin-bottom: 12px;">{{ $m['kalimat_pembuka'] }}</div>
                    @endif
                    <div style="text-align: justify; line-height: 1.6; white-space: pre-wrap;">{{ $letter->content }}</div>
                    @if(!empty($m['kalimat_penutup']))
                        <div style="text-align: justify; line-height: 1.6; margin-top: 12px;">{{ $m['kalimat_penutup'] }}</div>
                    @endif
                @endif

                @include('generator.partials.penandatangan', [
                    'penandatanganList' => $letter->penandatangan_list,
                    'signatures' => $letter->signatures_by_index,
                    'keteranganPeriode' => $letter->user->periode_kepengurusan ?? null,
                    'namaOrganisasi' => $letter->user->name ?? null,
                ])
            </div>

            @if($lampiranPath)
                <div class="mt-8 no-print">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <span class="px-2.5 py-1 bg-indigo-100 text-indigo-700 font-bold text-xs rounded-md uppercase tracking-wider">Lampiran Surat</span>
                                <h4 class="font-bold text-slate-800 text-sm">Dokumen Berkas Lampiran PDF</h4>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ asset('storage/' . $lampiranPath) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg transition shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    <span>Buka Ukuran Penuh</span>
                                </a>
                                <a href="{{ asset('storage/' . $lampiranPath) }}" download class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span>Unduh Lampiran</span>
                                </a>
                            </div>
                        </div>
                        <div class="p-4 bg-slate-100">
                            <iframe src="{{ asset('storage/' . $lampiranPath) }}#view=FitH" class="w-full h-[650px] rounded-xl border border-slate-300 shadow-inner bg-white" title="Preview Dokumen Lampiran PDF"></iframe>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
