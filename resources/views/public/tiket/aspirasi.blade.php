<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sampaikan Aspirasi Mahasiswa - ITG</title>
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
            <div class="flex items-center gap-3">
                <a href="{{ route('layanan.cek-status') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 transition">
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
                <a href="{{ route('layanan.index') }}" class="hover:text-blue-600 transition">Portal Layanan</a>
                <span>/</span>
                <span class="text-slate-800 font-medium">Aspirasi Mahasiswa</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Formulir Aspirasi & Masukan Mahasiswa</h1>
            <p class="text-sm text-slate-600 mt-1">Sampaikan aspirasi, keluhan fasilitas, atau masukan akademik secara konstruktif langsung ke Badan Perwakilan Mahasiswa (BPM).</p>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50/80 border border-blue-200 rounded-2xl p-4 sm:p-5 mb-8 flex gap-3.5 text-blue-900 text-xs sm:text-sm">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="leading-relaxed">
                <strong class="font-semibold block text-blue-950 mb-0.5">Pelacakan Menggunakan Kode Tiket</strong>
                Anda tidak perlu login. Setelah form dikirim, sistem akan menerbitkan <strong>Kode Tiket</strong> unik yang juga dikirim ke email Anda untuk memantau proses tindak lanjut dari BPM / BKHM.
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
            <form action="{{ route('layanan.aspirasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-base font-bold text-slate-900">1. Data Pengaju</h2>
                    <p class="text-xs text-slate-500">Identitas mahasiswa untuk keperluan verifikasi dan notifikasi tanggapan.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="nim" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">NIM <span class="text-rose-500">*</span></label>
                        <input type="text" name="nim" id="nim" value="{{ old('nim') }}" required placeholder="Contoh: 2106001"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-3.5">
                    </div>
                    <div>
                        <label for="nama_mahasiswa" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_mahasiswa" id="nama_mahasiswa" value="{{ old('nama_mahasiswa') }}" required placeholder="Nama lengkap Anda"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-3.5">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Email Aktif <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email@itg.ac.id atau gmail"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-3.5">
                        <span class="text-[11px] text-slate-500 mt-1 block">Kode tiket & info tindak lanjut akan dikirim ke alamat ini.</span>
                    </div>
                    <div>
                        <label for="no_hp" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">No. WhatsApp / HP</label>
                        <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-3.5">
                    </div>
                </div>

                <div>
                    <label for="prodi" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Program Studi</label>
                    <select name="prodi" id="prodi" class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-3.5">
                        <option value="">-- Pilih Program Studi --</option>
                        <option value="Teknik Informatika" {{ old('prodi') == 'Teknik Informatika' ? 'selected' : '' }}>Teknik Informatika</option>
                        <option value="Sistem Informasi" {{ old('prodi') == 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
                        <option value="Teknik Sipil" {{ old('prodi') == 'Teknik Sipil' ? 'selected' : '' }}>Teknik Sipil</option>
                        <option value="Teknik Industri" {{ old('prodi') == 'Teknik Industri' ? 'selected' : '' }}>Teknik Industri</option>
                        <option value="Arsitektur" {{ old('prodi') == 'Arsitektur' ? 'selected' : '' }}>Arsitektur</option>
                    </select>
                </div>

                <div class="border-b border-slate-100 pt-4 pb-4">
                    <h2 class="text-base font-bold text-slate-900">2. Rincian Aspirasi</h2>
                    <p class="text-xs text-slate-500">Uraikan pokok masalah atau masukan Anda dengan jelas dan sopan.</p>
                </div>

                <div>
                    <label for="judul" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Judul Aspirasi <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul') }}" required placeholder="Ringkasan inti aspirasi"
                        class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-3.5">
                </div>

                <div>
                    <label for="isi" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Lengkap Aspirasi <span class="text-rose-500">*</span></label>
                    <textarea name="isi" id="isi" rows="6" required placeholder="Jelaskan aspirasi, usulan, atau kronologi kendala yang Anda alami..."
                        class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-3.5">{{ old('isi') }}</textarea>
                </div>

                <div>
                    <label for="lampiran" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Lampiran Dokumen / Foto Pendukung (Opsional)</label>
                    <input type="file" name="lampiran" id="lampiran" accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-xl p-2">
                    <span class="text-[11px] text-slate-500 mt-1 block">Format: PDF, JPG, PNG. Maksimal 5 MB.</span>
                </div>

                <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                    <a href="{{ route('layanan.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Kirim Aspirasi
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
