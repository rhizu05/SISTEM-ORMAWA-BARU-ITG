<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Status Tiket Layanan - ITG</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">
    <!-- Navbar -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between">
            <a href="{{ route('layanan.index') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo_itg.png') }}" alt="Logo ITG" class="h-11 w-auto object-contain">
                <div>
                    <span class="text-xs font-semibold tracking-wider text-blue-700 uppercase block">Institut Teknologi Garut</span>
                    <span class="text-base font-extrabold text-slate-900 leading-tight">Portal Layanan Mahasiswa</span>
                </div>
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('layanan.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
                    &larr; Beranda Portal
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-10">
        <!-- Breadcrumb & Title -->
        <div class="text-center mb-8">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pelacakan Status Tiket Layanan</h1>
            <p class="text-sm text-slate-600 mt-2">Masukkan Kode Tiket yang Anda peroleh dan Alamat Email yang digunakan saat pengajuan.</p>
        </div>

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 mb-6 text-sm flex gap-3">
                <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <div>
                    <strong class="font-bold block mb-0.5">Berhasil Terkirim!</strong>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (!empty($errorMessage))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl p-4 mb-6 text-sm flex gap-3">
                <svg class="w-5 h-5 flex-shrink-0 text-rose-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>{{ $errorMessage }}</div>
            </div>
        @endif

        <!-- Search Box -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8">
            <form action="{{ route('layanan.tracking') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="kode" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kode Tiket <span class="text-rose-500">*</span></label>
                        <input type="text" name="kode" id="kode" value="{{ request('kode') }}" required placeholder="Contoh: SKIN-TKT-2026-ABCD"
                            class="w-full text-sm font-mono uppercase rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-3.5">
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email Pengaju <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ request('email') }}" required placeholder="email@domain.com"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-3.5">
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        Lacak Tiket
                    </button>
                </div>
            </form>
        </div>

        @if ($tiket)
            <!-- Ticket Detail Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-8">
                <!-- Card Header -->
                <div class="p-6 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <span class="text-xs text-slate-500 font-mono block">Kode Tiket Resmi</span>
                        <div class="text-xl sm:text-2xl font-black font-mono text-slate-900 tracking-tight">{{ $tiket->kode_tiket }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Badge Kategori -->
                        @if ($tiket->kategori === 'aspirasi')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                Aspirasi
                            </span>
                        @elseif ($tiket->kategori === 'konseling')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-800 border border-teal-200">
                                Konseling (Rahasia)
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                {{ $tiket->sub_kategori === 'lapor_prestasi' ? 'Prestasi Mahasiswa' : 'Bantuan Lomba' }}
                            </span>
                        @endif

                        <!-- Badge Status -->
                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $tiket->status_color }}">
                            {{ $tiket->status_label }}
                        </span>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pb-6 border-b border-slate-100 text-xs">
                        <div>
                            <span class="text-slate-400 block mb-1">NIM</span>
                            <span class="font-bold text-slate-800">{{ $tiket->nim }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Nama Mahasiswa</span>
                            <span class="font-bold text-slate-800">{{ $tiket->nama_mahasiswa }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Program Studi</span>
                            <span class="font-bold text-slate-800">{{ $tiket->prodi ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Tanggal Diajukan</span>
                            <span class="font-bold text-slate-800">{{ $tiket->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    <!-- Detail Spesifik per Kategori -->
                    @if ($tiket->kategori === 'aspirasi')
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Judul Aspirasi</h3>
                                <p class="text-base font-bold text-slate-900 mt-1">{{ $tiket->judul }}</p>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Isi / Uraian Masukan</h3>
                                <div class="text-sm text-slate-700 bg-slate-50 p-4 rounded-xl mt-1 leading-relaxed whitespace-pre-line border border-slate-100">{{ $tiket->isi }}</div>
                            </div>
                        </div>
                    @elseif ($tiket->kategori === 'konseling')
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Topik Permasalahan</h3>
                                    <p class="text-sm font-bold text-slate-800 mt-1">{{ $tiket->topik_konseling }}</p>
                                </div>
                                <div>
                                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Metode Diinginkan</h3>
                                    <p class="text-sm font-bold text-slate-800 mt-1">{{ $tiket->metode_konseling }}</p>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Deskripsi Kendala</h3>
                                <div class="text-sm text-slate-700 bg-slate-50 p-4 rounded-xl mt-1 leading-relaxed whitespace-pre-line border border-slate-100">{{ $tiket->deskripsi_masalah }}</div>
                            </div>
                        </div>
                    @elseif ($tiket->kategori === 'prestasi')
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400">Nama Kompetisi / Kegiatan</h3>
                                <p class="text-base font-bold text-slate-900 mt-1">{{ $tiket->nama_kegiatan }}</p>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                                <div>
                                    <span class="text-slate-400 block mb-0.5">Penyelenggara</span>
                                    <span class="font-semibold text-slate-800">{{ $tiket->penyelenggara }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block mb-0.5">Tingkat</span>
                                    <span class="font-semibold text-slate-800">{{ $tiket->tingkat }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block mb-0.5">Capaian</span>
                                    <span class="font-semibold text-slate-800">{{ $tiket->capaian ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block mb-0.5">Bantuan Dana</span>
                                    <span class="font-semibold text-slate-800">{{ $tiket->estimasi_biaya ? 'Rp ' . number_format($tiket->estimasi_biaya, 0, ',', '.') : '-' }}</span>
                                </div>
                            </div>

                            @if ($tiket->tampil_ke_publik)
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800 flex items-center gap-2">
                                    <span>🏆</span>
                                    <span>Prestasi ini telah disetujui BKHM dan resmi ditampilkan di <strong>Showcase Prestasi Kampus</strong>.</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Lampiran File Pengaju -->
                    @if ($tiket->lampiran || $tiket->lampiran_bukti)
                        <div class="pt-4 border-t border-slate-100">
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block mb-2">Berkas Lampiran Pengaju</span>
                            <a href="{{ route('layanan.lampiran', ['tiket' => $tiket->id, 'email' => $tiket->email]) }}" target="_blank"
                                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Unduh Dokumen Pendukung
                            </a>
                        </div>
                    @endif

                    <!-- Tanggapan Resmi Petugas / Tim Verifikator -->
                    @if ($tiket->tanggapan_resmi || $tiket->jadwal_temu)
                        <div class="pt-6 border-t border-slate-100 space-y-4">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-5 text-blue-950">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                                            ITG
                                        </div>
                                        <strong class="font-bold text-sm text-blue-900">Tanggapan Resmi Pengelola (BKHM / BPM)</strong>
                                    </div>
                                    @if ($tiket->updated_at)
                                        <span class="text-[11px] text-blue-600">{{ $tiket->updated_at->translatedFormat('d M Y, H:i') }}</span>
                                    @endif
                                </div>

                                @if ($tiket->jadwal_temu)
                                    <div class="bg-white/90 border border-blue-200/80 rounded-xl p-4 my-3 text-xs text-blue-900 space-y-2">
                                        <strong class="font-semibold block text-blue-950 text-sm">📅 Jadwal Temu Konseling:</strong>
                                        <div class="text-sm font-bold text-blue-700 mt-0.5">{{ $tiket->jadwal_temu->translatedFormat('l, d F Y - Jam H:i') }} WIB</div>
                                        @if ($tiket->lokasi_atau_link)
                                            <div class="text-xs text-slate-700"><span class="font-semibold">Tempat / Tautan:</span> {{ $tiket->lokasi_atau_link }}</div>
                                        @endif
                                        <p class="text-slate-600">Silakan hadir di Ruang BKHM ITG atau membuka tautan sesuai jadwal di atas.</p>

                                        @if ($tiket->kategori === 'konseling')
                                            <!-- Status / Form Konfirmasi Kehadiran Mahasiswa -->
                                            <div class="pt-3 mt-2 border-t border-blue-100">
                                                @if ($tiket->konfirmasi_mahasiswa)
                                                    <div class="p-3 rounded-lg text-xs {{ $tiket->konfirmasi_mahasiswa === 'bersedia_hadir' ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : ($tiket->konfirmasi_mahasiswa === 'minta_reschedule' ? 'bg-amber-50 border border-amber-200 text-amber-800' : 'bg-rose-50 border border-rose-200 text-rose-800') }}">
                                                        <div class="font-bold flex items-center gap-1.5">
                                                            @if($tiket->konfirmasi_mahasiswa === 'bersedia_hadir')
                                                                <span>✅ Anda telah mengonfirmasi Bersedia Hadir pada sesi ini.</span>
                                                            @elseif($tiket->konfirmasi_mahasiswa === 'minta_reschedule')
                                                                <span>⏳ Anda telah mengajukan Permohonan Reschedule (Ganti Jadwal).</span>
                                                            @else
                                                                <span>❌ Anda telah membatalkan sesi konseling ini.</span>
                                                            @endif
                                                        </div>
                                                        @if($tiket->catatan_konfirmasi_mahasiswa)
                                                            <div class="mt-1 text-slate-600 italic">"{{ $tiket->catatan_konfirmasi_mahasiswa }}"</div>
                                                        @endif
                                                        <div class="text-[10px] text-slate-400 mt-1">Dikonfirmasi pada: {{ $tiket->konfirmasi_at ? $tiket->konfirmasi_at->translatedFormat('d M Y H:i') : '-' }} WIB</div>
                                                    </div>
                                                @else
                                                    <form action="{{ route('layanan.konseling.konfirmasi', $tiket) }}" method="POST" class="p-3.5 bg-slate-50 border border-indigo-200 rounded-xl space-y-2.5">
                                                        @csrf
                                                        <input type="hidden" name="email" value="{{ $tiket->email }}">
                                                        <span class="block font-bold text-xs text-indigo-950">Konfirmasi Kesediaan Anda:</span>
                                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                                            <label class="flex items-center gap-2 p-2 bg-white rounded-lg border border-slate-200 hover:border-emerald-400 cursor-pointer text-xs text-slate-700">
                                                                <input type="radio" name="konfirmasi" value="bersedia_hadir" required class="text-emerald-600 focus:ring-emerald-500">
                                                                <span class="font-medium">Bersedia Hadir</span>
                                                            </label>
                                                            <label class="flex items-center gap-2 p-2 bg-white rounded-lg border border-slate-200 hover:border-amber-400 cursor-pointer text-xs text-slate-700">
                                                                <input type="radio" name="konfirmasi" value="minta_reschedule" class="text-amber-600 focus:ring-amber-500">
                                                                <span class="font-medium">Minta Reschedule</span>
                                                            </label>
                                                            <label class="flex items-center gap-2 p-2 bg-white rounded-lg border border-slate-200 hover:border-rose-400 cursor-pointer text-xs text-slate-700">
                                                                <input type="radio" name="konfirmasi" value="dibatalkan_mahasiswa" class="text-rose-600 focus:ring-rose-500">
                                                                <span class="font-medium">Batalkan Sesi</span>
                                                            </label>
                                                        </div>
                                                        <div>
                                                            <input type="text" name="catatan" placeholder="Catatan tambahan (misal opsi hari/jam luang Anda jika reschedule)..." class="w-full text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-1.5 px-2.5">
                                                        </div>
                                                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-lg shadow-sm transition inline-flex items-center gap-1.5">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                            <span>Kirim Konfirmasi ke BKHM</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if ($tiket->tanggapan_resmi)
                                    <div class="text-xs sm:text-sm text-blue-900 leading-relaxed whitespace-pre-line bg-white/70 p-3.5 rounded-xl border border-blue-100">
                                        {{ $tiket->tanggapan_resmi }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="pt-4 border-t border-slate-100">
                            <div class="text-xs text-slate-500 bg-slate-50 p-4 rounded-xl border border-slate-200/60 flex items-center gap-3">
                                <svg class="w-5 h-5 text-slate-400 flex-shrink-0 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>Tiket Anda saat ini dalam antrean verifikasi petugas. Tanggapan resmi atau jadwal temu akan diperbarui di sini dan otomatis diteruskan ke email Anda.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </main>

    <footer class="bg-white border-t border-slate-200 py-8 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Institut Teknologi Garut. Bagian Kemahasiswaan & Hubungan Alumni.
    </footer>
</body>
</html>
