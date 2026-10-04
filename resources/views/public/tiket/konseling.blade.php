<x-public-layout title="Konseling Mahasiswa" brand-label="Portal Layanan Mahasiswa" accent="emerald">
    <x-slot name="nav">
        <a href="{{ route('layanan.index') }}" class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            <span>Portal</span>
        </a>
        <a href="{{ route('layanan.cek-status') }}" class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span>Cek Tiket</span>
        </a>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
        <nav class="flex items-center gap-2 text-xs text-slate-600 mb-3">
            <a href="{{ route('layanan.index') }}" class="hover:text-emerald-700 transition">Portal Layanan</a>
            <span class="text-slate-400">/</span>
            <span class="text-slate-800 font-semibold">Konseling Personal Mahasiswa</span>
        </nav>
        <div class="flex flex-wrap items-center gap-3 mb-1">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Layanan Konseling Personal</h1>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                Kerahasiaan Terjamin
            </span>
        </div>
        <p class="text-sm text-slate-600 mt-1">Konsultasikan kendala akademik, psikologis, finansial, atau personal dengan konselor BKHM secara aman dan empatik.</p>

        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 mt-6 mb-8 flex gap-4 text-emerald-950 text-xs sm:text-sm">
            <div class="w-9 h-9 rounded-xl bg-emerald-700 text-white flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10.5" width="14" height="9.5" rx="2" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/></svg>
            </div>
            <div class="space-y-1">
                <strong class="font-bold text-emerald-950 text-sm block">Jaminan Kerahasiaan &amp; Privasi Eksklusif BKHM</strong>
                <p class="text-emerald-900 leading-relaxed">
                    Pengajuan konseling ini bersifat <strong>rahasia penuh antara Anda dan staf konselor BKHM</strong>. Badan Perwakilan Mahasiswa (BPM), BEM, maupun pengurus ormawa <strong>sama sekali tidak memiliki akses</strong> ke data atau tiket konseling ini. Jadwal pertemuan atau respons akan dikirimkan langsung ke email Anda secara tertutup.
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

        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8">
            <form action="{{ route('layanan.konseling.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf

                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-base font-bold text-slate-900">1. Data Mahasiswa</h2>
                    <p class="text-xs text-slate-600">Data Anda disimpan secara rahasia untuk keperluan komunikasi konselor.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="nim" class="block text-sm font-semibold text-slate-700 mb-1.5">NIM <span class="text-rose-600">*</span></label>
                        <input type="text" name="nim" id="nim" value="{{ old('nim') }}" required placeholder="Contoh: 2106001"
                            class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 py-3 px-3.5 placeholder-slate-500">
                    </div>
                    <div>
                        <label for="nama_mahasiswa" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-rose-600">*</span></label>
                        <input type="text" name="nama_mahasiswa" id="nama_mahasiswa" value="{{ old('nama_mahasiswa') }}" required placeholder="Nama lengkap Anda"
                            class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 py-3 px-3.5 placeholder-slate-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email Aktif <span class="text-rose-600">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email@itg.ac.id atau gmail"
                            class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 py-3 px-3.5 placeholder-slate-500">
                        <span class="text-[11px] text-slate-600 mt-1 block">Jadwal temu dan pesan konselor dikirim privat ke sini.</span>
                    </div>
                    <div>
                        <label for="no_hp" class="block text-sm font-semibold text-slate-700 mb-1.5">No. WhatsApp / HP <span class="text-rose-600">*</span></label>
                        <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}" required placeholder="08xxxxxxxxxx"
                            class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 py-3 px-3.5 placeholder-slate-500">
                    </div>
                </div>

                <div>
                    <label for="prodi" class="block text-sm font-semibold text-slate-700 mb-1.5">Program Studi <span class="text-rose-600">*</span></label>
                    <select name="prodi" id="prodi" required class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 py-3 px-3.5">
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
                    <p class="text-xs text-slate-600">Pilih topik dan preferensi metode pelaksanaan konseling.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="topik_konseling" class="block text-sm font-semibold text-slate-700 mb-1.5">Topik Masalah <span class="text-rose-600">*</span></label>
                        <select name="topik_konseling" id="topik_konseling" required class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 py-3 px-3.5">
                            <option value="">-- Pilih Topik Masalah --</option>
                            <option value="Kendala Akademik / IPK" {{ old('topik_konseling') == 'Kendala Akademik / IPK' ? 'selected' : '' }}>Kendala Akademik / IPK</option>
                            <option value="Stres Perkuliahan & Kesehatan Mental" {{ old('topik_konseling') == 'Stres Perkuliahan & Kesehatan Mental' ? 'selected' : '' }}>Stres Perkuliahan &amp; Kesehatan Mental</option>
                            <option value="Masalah Pribadi / Keluarga / Sosial" {{ old('topik_konseling') == 'Masalah Pribadi / Keluarga / Sosial' ? 'selected' : '' }}>Masalah Pribadi / Keluarga / Sosial</option>
                            <option value="Kendala Finansial / Pembayaran UKT" {{ old('topik_konseling') == 'Kendala Finansial / Pembayaran UKT' ? 'selected' : '' }}>Kendala Finansial / Pembayaran UKT</option>
                            <option value="Perencanaan Karir & Pasca-Kampus" {{ old('topik_konseling') == 'Perencanaan Karir & Pasca-Kampus' ? 'selected' : '' }}>Perencanaan Karir &amp; Pasca-Kampus</option>
                            <option value="Lainnya" {{ old('topik_konseling') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label for="metode_konseling" class="block text-sm font-semibold text-slate-700 mb-1.5">Metode Konseling yang Diinginkan <span class="text-rose-600">*</span></label>
                        <select name="metode_konseling" id="metode_konseling" required class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 py-3 px-3.5">
                            <option value="">-- Pilih Metode Konseling --</option>
                            <option value="Tatap Muka (Ruang Konseling BKHM)" {{ old('metode_konseling') == 'Tatap Muka (Ruang Konseling BKHM)' ? 'selected' : '' }}>Tatap Muka Langsung (Ruang BKHM)</option>
                            <option value="Daring / Online (Google Meet/Zoom)" {{ old('metode_konseling') == 'Daring / Online (Google Meet/Zoom)' ? 'selected' : '' }}>Daring / Online (Google Meet/Zoom)</option>
                            <option value="Fleksibel / Sesuai Kesepakatan" {{ old('metode_konseling') == 'Fleksibel / Sesuai Kesepakatan' ? 'selected' : '' }}>Fleksibel / Sesuai Kesepakatan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="deskripsi_masalah" class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi Cerita / Kendala yang Dihadapi <span class="text-rose-600">*</span></label>
                    <textarea name="deskripsi_masalah" id="deskripsi_masalah" rows="6" required placeholder="Ceritakan secara bebas apa yang sedang Anda rasakan atau hadapi. Konselor BKHM siap mendengarkan tanpa menghakimi..."
                        class="w-full text-sm rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 py-3 px-3.5 placeholder-slate-500">{{ old('deskripsi_masalah') }}</textarea>
                </div>

                <div>
                    <label for="lampiran" class="block text-sm font-semibold text-slate-700 mb-1.5">Dokumen Pendukung (Opsional)</label>
                    <input type="file" name="lampiran" id="lampiran" accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100 border border-slate-200 rounded-lg p-2">
                    <span class="text-[11px] text-slate-600 mt-1 block">Format: PDF, JPG, PNG (contoh: KHS, surat keterangan). Maks 5 MB.</span>
                </div>

                <div class="pt-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-slate-100">
                    <a href="{{ route('layanan.index') }}" class="inline-flex items-center justify-center min-h-[48px] px-5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400">
                        Batal &amp; Kembali
                    </a>
                    <button type="submit" :disabled="submitting" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 min-h-[48px] px-6 rounded-xl text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 disabled:opacity-70 transition shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 focus-visible:ring-offset-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="10.5" width="14" height="9.5" rx="2" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/></svg>
                        <span x-text="submitting ? 'Mengirim Permohonan...' : 'Ajukan Permohonan Konseling'">Ajukan Permohonan Konseling</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-public-layout>
