<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Konseling Mahasiswa (Rahasia) - ITG</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">
    <!-- Navbar -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between">
            <a href="{{ route('layanan.index') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo_itg.png') }}" alt="Logo ITG" class="h-11 w-auto object-contain">
                <div>
                    <span class="text-xs font-semibold tracking-wider text-teal-700 uppercase block">Institut Teknologi Garut</span>
                    <span class="text-base font-extrabold text-slate-900 leading-tight">Portal Layanan Mahasiswa</span>
                </div>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('layanan.cek-status') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-teal-700 bg-teal-50 hover:bg-teal-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Cek Status
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-10">
        <!-- Breadcrumb & Title -->
        <div class="mb-6">
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                <a href="{{ route('layanan.index') }}" class="hover:text-teal-600 transition">Portal Layanan</a>
                <span>/</span>
                <span class="text-slate-800 font-medium">Konseling Mahasiswa</span>
            </div>
            <div class="flex items-center gap-3 mb-1">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Layanan Konseling Personal</h1>
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-teal-100 text-teal-800 border border-teal-200">
                    Kerahasiaan Terjamin
                </span>
            </div>
            <p class="text-sm text-slate-600 mt-1">Konsultasikan kendala akademik, psikologis, finansial, atau personal dengan konselor BKHM secara aman dan empatik.</p>
        </div>

        <!-- Privacy Assurance Alert -->
        <div class="bg-gradient-to-r from-teal-50 to-emerald-50 border border-teal-200 rounded-2xl p-5 mb-8 flex gap-4 text-teal-950 text-xs sm:text-sm shadow-sm">
            <div class="w-9 h-9 rounded-xl bg-teal-600 text-white flex items-center justify-center flex-shrink-0 shadow">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <div class="space-y-1">
                <strong class="font-bold text-teal-900 text-sm block">Jaminan Kerahasiaan & Privasi 100%</strong>
                <p class="text-teal-800 leading-relaxed">
                    Pengajuan konseling ini <strong>hanya dapat dibaca oleh staf konseling BKHM</strong>. Mahasiswa lain atau pengurus ormawa sama sekali tidak memiliki akses ke tiket ini. Jadwal pertemuan atau respons akan dikirimkan ke email Anda secara tertutup.
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl p-4 mb-6 text-sm">
                <strong class="font-semibold block mb-1">Terdapat kesalahan input:</strong>
                <ul class="list-disc pl-5 space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <form action="{{ route('layanan.konseling.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-base font-bold text-slate-900">1. Data Mahasiswa</h2>
                    <p class="text-xs text-slate-500">Data Anda disimpan secara rahasia untuk keperluan komunikasi konselor.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="nim" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">NIM <span class="text-rose-500">*</span></label>
                        <input type="text" name="nim" id="nim" value="{{ old('nim') }}" required placeholder="Contoh: 2106001"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm py-2.5 px-3.5">
                    </div>
                    <div>
                        <label for="nama_mahasiswa" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_mahasiswa" id="nama_mahasiswa" value="{{ old('nama_mahasiswa') }}" required placeholder="Nama lengkap Anda"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm py-2.5 px-3.5">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Email Aktif <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email@itg.ac.id atau gmail"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm py-2.5 px-3.5">
                        <span class="text-[11px] text-slate-500 mt-1 block">Jadwal temu & pesan konselor dikirim privat ke sini.</span>
                    </div>
                    <div>
                        <label for="no_hp" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">No. WhatsApp / HP</label>
                        <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm py-2.5 px-3.5">
                    </div>
                </div>

                <div>
                    <label for="prodi" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Program Studi</label>
                    <select name="prodi" id="prodi" class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm py-2.5 px-3.5">
                        <option value="">-- Pilih Program Studi --</option>
                        <option value="Teknik Informatika" {{ old('prodi') == 'Teknik Informatika' ? 'selected' : '' }}>Teknik Informatika</option>
                        <option value="Sistem Informasi" {{ old('prodi') == 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
                        <option value="Teknik Sipil" {{ old('prodi') == 'Teknik Sipil' ? 'selected' : '' }}>Teknik Sipil</option>
                        <option value="Teknik Industri" {{ old('prodi') == 'Teknik Industri' ? 'selected' : '' }}>Teknik Industri</option>
                        <option value="Arsitektur" {{ old('prodi') == 'Arsitektur' ? 'selected' : '' }}>Arsitektur</option>
                    </select>
                </div>

                <div class="border-b border-slate-100 pt-4 pb-4">
                    <h2 class="text-base font-bold text-slate-900">2. Rencana Konseling</h2>
                    <p class="text-xs text-slate-500">Pilih topik dan preferensi metode pelaksanaan konseling.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="topik_konseling" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Topik Masalah <span class="text-rose-500">*</span></label>
                        <select name="topik_konseling" id="topik_konseling" required class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm py-2.5 px-3.5">
                            <option value="">-- Pilih Topik Masalah --</option>
                            <option value="Kendala Akademik / IPK" {{ old('topik_konseling') == 'Kendala Akademik / IPK' ? 'selected' : '' }}>Kendala Akademik / IPK</option>
                            <option value="Stres Perkuliahan & Kesehatan Mental" {{ old('topik_konseling') == 'Stres Perkuliahan & Kesehatan Mental' ? 'selected' : '' }}>Stres Perkuliahan & Kesehatan Mental</option>
                            <option value="Masalah Pribadi / Keluarga / Sosial" {{ old('topik_konseling') == 'Masalah Pribadi / Keluarga / Sosial' ? 'selected' : '' }}>Masalah Pribadi / Keluarga / Sosial</option>
                            <option value="Kendala Finansial / Pembayaran UKT" {{ old('topik_konseling') == 'Kendala Finansial / Pembayaran UKT' ? 'selected' : '' }}>Kendala Finansial / Pembayaran UKT</option>
                            <option value="Perencanaan Karir & Pasca-Kampus" {{ old('topik_konseling') == 'Perencanaan Karir & Pasca-Kampus' ? 'selected' : '' }}>Perencanaan Karir & Pasca-Kampus</option>
                            <option value="Lainnya" {{ old('topik_konseling') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label for="metode_konseling" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Metode Konseling yang Diinginkan <span class="text-rose-500">*</span></label>
                        <select name="metode_konseling" id="metode_konseling" required class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm py-2.5 px-3.5">
                            <option value="">-- Pilih Metode Konseling --</option>
                            <option value="Tatap Muka (Ruang Konseling BKHM)" {{ old('metode_konseling') == 'Tatap Muka (Ruang Konseling BKHM)' ? 'selected' : '' }}>Tatap Muka Langsung (Ruang BKHM)</option>
                            <option value="Daring / Online (Google Meet/Zoom)" {{ old('metode_konseling') == 'Daring / Online (Google Meet/Zoom)' ? 'selected' : '' }}>Daring / Online (Google Meet/Zoom)</option>
                            <option value="Fleksibel / Sesuai Kesepakatan" {{ old('metode_konseling') == 'Fleksibel / Sesuai Kesepakatan' ? 'selected' : '' }}>Fleksibel / Sesuai Kesepakatan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="deskripsi_masalah" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Cerita / Kendala yang Dihadapi <span class="text-rose-500">*</span></label>
                    <textarea name="deskripsi_masalah" id="deskripsi_masalah" rows="6" required placeholder="Ceritakan secara bebas apa yang sedang Anda rasakan atau hadapi. Konselor BKHM siap mendengarkan tanpa menghakimi..."
                        class="w-full text-sm rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 shadow-sm py-2.5 px-3.5">{{ old('deskripsi_masalah') }}</textarea>
                </div>

                <div>
                    <label for="lampiran" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Dokumen Pendukung (Opsional)</label>
                    <input type="file" name="lampiran" id="lampiran" accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 border border-slate-200 rounded-xl p-2">
                    <span class="text-[11px] text-slate-500 mt-1 block">Format: PDF, JPG, PNG (contoh: KHS, surat keterangan, dsb). Maks 5 MB.</span>
                </div>

                <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                    <a href="{{ route('layanan.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-teal-600 hover:bg-teal-700 shadow-md shadow-teal-500/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Kirim Permohonan Konseling
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-8 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Institut Teknologi Garut. Bagian Kemahasiswaan & Hubungan Alumni.
    </footer>
</body>
</html>
