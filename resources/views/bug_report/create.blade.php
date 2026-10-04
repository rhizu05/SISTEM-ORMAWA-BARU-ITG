<x-public-layout title="Laporkan Kendala Sistem" brand-label="Biro Kemahasiswaan &amp; Hubungan Masyarakat" accent="indigo">
    <x-slot name="nav">
        @auth
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                <span>Dashboard</span>
            </a>
        @else
            <a href="{{ route('layanan.index') }}" class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                <span>Portal Layanan</span>
            </a>
        @endauth
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-6 sm:py-10">
        <nav class="flex items-center gap-2 text-xs text-slate-600 mb-4">
            <a href="{{ route('layanan.index') }}" class="hover:text-indigo-700 transition">Portal Layanan</a>
            <span class="text-slate-400">/</span>
            <span class="text-slate-800 font-semibold">Lapor Kendala Sistem</span>
        </nav>
        <div class="mb-8 text-center sm:text-left">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200 mb-3">
                <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Helpdesk Kendala Sistem
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Formulir Laporan Kendala &amp; Bug Sistem</h1>
            <p class="text-sm text-slate-600 mt-2 leading-relaxed">
                Temukan masalah teknis, kejanggalan tampilan, atau fitur yang macet? Laporan Anda akan ditampung oleh <strong>Biro Kemahasiswaan (BKHM)</strong> dan dievaluasi bersama <strong>Tim IT ITG</strong>.
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl p-4 mb-6 text-sm">
                <strong class="font-bold block mb-1">Periksa kembali isian formulir:</strong>
                <ul class="list-disc pl-5 space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8">
            <form action="{{ route('bug.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf

                <!-- Identitas Pelapor -->
                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">1. Identitas Pelapor</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Informasi kontak untuk keperluan verifikasi dan notifikasi tindak lanjut.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_pelapor" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-600">*</span></label>
                        <input type="text" name="nama_pelapor" id="nama_pelapor" value="{{ old('nama_pelapor', $user->name ?? '') }}" required placeholder="Nama Anda"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3 placeholder-slate-400">
                    </div>
                    <div>
                        <label for="email_pelapor" class="block text-xs font-semibold text-slate-700 mb-1">Email Aktif <span class="text-rose-600">*</span></label>
                        <input type="email" name="email_pelapor" id="email_pelapor" value="{{ old('email_pelapor', $user->email ?? '') }}" required placeholder="email@itg.ac.id"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3 placeholder-slate-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="no_hp_pelapor" class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp / HP <span class="text-rose-600">*</span></label>
                        <input type="text" name="no_hp_pelapor" id="no_hp_pelapor" value="{{ old('no_hp_pelapor', $user->no_hp ?? '') }}" required placeholder="08xxxxxxxxxx"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3 placeholder-slate-400">
                    </div>
                    <div>
                        <label for="role_pelapor" class="block text-xs font-semibold text-slate-700 mb-1">Peran / Role di Sistem <span class="text-rose-600">*</span></label>
                        @php
                            $userRole = $user ? $user->getRoleNames()->first() : 'mahasiswa';
                        @endphp
                        <select name="role_pelapor" id="role_pelapor" required class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3">
                            <option value="mahasiswa" {{ old('role_pelapor', $userRole) === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="ormawa" {{ old('role_pelapor', $userRole) === 'ormawa' ? 'selected' : '' }}>Pengurus Ormawa / UKM / HIMA</option>
                            <option value="bem" {{ old('role_pelapor', $userRole) === 'bem' ? 'selected' : '' }}>Badan Eksekutif Mahasiswa (BEM)</option>
                            <option value="bpm" {{ old('role_pelapor', $userRole) === 'bpm' ? 'selected' : '' }}>Badan Perwakilan Mahasiswa (BPM)</option>
                            <option value="sarpras" {{ old('role_pelapor', $userRole) === 'sarpras' ? 'selected' : '' }}>Staf Sarpras</option>
                            <option value="bendahara" {{ old('role_pelapor', $userRole) === 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                            <option value="bkhm" {{ old('role_pelapor', $userRole) === 'bkhm' ? 'selected' : '' }}>Staf BKHM</option>
                            <option value="wr3" {{ old('role_pelapor', $userRole) === 'wr3' ? 'selected' : '' }}>Wakil Rektor III / Pimpinan</option>
                            <option value="admin" {{ old('role_pelapor', $userRole) === 'admin' ? 'selected' : '' }}>Administrator / IT</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="prodi_pelapor" class="block text-xs font-semibold text-slate-700 mb-1">Program Studi / Unit Kerja (Opsional)</label>
                    <select name="prodi_pelapor" id="prodi_pelapor" class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3">
                        <option value="">-- Pilih Program Studi (Jika Mahasiswa/Dosen) --</option>
                        <option value="Teknik Informatika" {{ old('prodi_pelapor') === 'Teknik Informatika' ? 'selected' : '' }}>Teknik Informatika</option>
                        <option value="Sistem Informasi" {{ old('prodi_pelapor') === 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
                        <option value="Teknik Sipil" {{ old('prodi_pelapor') === 'Teknik Sipil' ? 'selected' : '' }}>Teknik Sipil</option>
                        <option value="Teknik Industri" {{ old('prodi_pelapor') === 'Teknik Industri' ? 'selected' : '' }}>Teknik Industri</option>
                        <option value="Arsitektur" {{ old('prodi_pelapor') === 'Arsitektur' ? 'selected' : '' }}>Arsitektur</option>
                    </select>
                </div>

                <!-- Rincian Kendala Teknis -->
                <div class="border-b border-slate-100 pt-3 pb-4">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">2. Rincian Kendala / Masalah</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Jelaskan lokasi dan gejala error yang dialami.</p>
                </div>

                <div>
                    <label for="halaman_url" class="block text-xs font-semibold text-slate-700 mb-1">Halaman / URL Tempat Terjadi Error</label>
                    <input type="url" name="halaman_url" id="halaman_url" value="{{ old('halaman_url', $currentUrl) }}" placeholder="http://127.0.0.1:8000/..."
                        class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3 font-mono text-xs placeholder-slate-400">
                    <span class="text-[11px] text-slate-500 mt-1 block">Tautan otomatis terdeteksi dari halaman aktif saat Anda menekan tombol lapor.</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="kategori" class="block text-xs font-semibold text-slate-700 mb-1">Kategori Kendala <span class="text-rose-600">*</span></label>
                        <select name="kategori" id="kategori" required class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3">
                            <option value="error_sistem" {{ old('kategori') === 'error_sistem' ? 'selected' : '' }}>Error Sistem / Pesan 500</option>
                            <option value="tampilan_uiux" {{ old('kategori') === 'tampilan_uiux' ? 'selected' : '' }}>Tampilan / UI Ganjil</option>
                            <option value="fitur_gagal" {{ old('kategori') === 'fitur_gagal' ? 'selected' : '' }}>Fitur Macet / Gagal Simpan</option>
                            <option value="usulan" {{ old('kategori') === 'usulan' ? 'selected' : '' }}>Usulan Pengembangan Fitur</option>
                        </select>
                    </div>
                    <div>
                        <label for="tingkat_urgensi" class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Urgensi <span class="text-rose-600">*</span></label>
                        <select name="tingkat_urgensi" id="tingkat_urgensi" required class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3">
                            <option value="rendah" {{ old('tingkat_urgensi') === 'rendah' ? 'selected' : '' }}>Rendah (Kosmetik / typo kecil)</option>
                            <option value="sedang" {{ old('tingkat_urgensi', 'sedang') === 'sedang' ? 'selected' : '' }}>Sedang (Kendala minor, masih bisa lanjut)</option>
                            <option value="tinggi" {{ old('tingkat_urgensi') === 'tinggi' ? 'selected' : '' }}>Tinggi (Fungsi terhambat / menu error)</option>
                            <option value="kritis" {{ old('tingkat_urgensi') === 'kritis' ? 'selected' : '' }}>Kritis (Memblokir kegiatan / tidak bisa simpan)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="judul" class="block text-xs font-semibold text-slate-700 mb-1">Ringkasan Kendala (Judul Singkat) <span class="text-rose-600">*</span></label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul') }}" required placeholder="Contoh: Tombol simpan LPJ tidak merespons di Safari"
                        class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3 placeholder-slate-400">
                </div>

                <div>
                    <label for="deskripsi" class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Lengkap / Kronologi Kejadian <span class="text-rose-600">*</span></label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" required placeholder="Jelaskan langkah apa yang Anda lakukan sebelum error muncul, atau tempelkan pesan kesalahan yang tertera di layar..."
                        class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3 placeholder-slate-400 leading-relaxed">{{ old('deskripsi') }}</textarea>
                </div>

                <div>
                    <label for="tangkapan_layar" class="block text-xs font-semibold text-slate-700 mb-1">Tangkapan Layar Bukti Error (Screenshot - Opsional)</label>
                    <input type="file" name="tangkapan_layar" id="tangkapan_layar" accept="image/png,image/jpeg,image/jpg"
                        class="w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                    <span class="text-[11px] text-slate-500 mt-1 block">Format: JPG, PNG. Maksimal 5 MB.</span>
                </div>

                <div class="pt-6 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('layanan.index') }}" class="inline-flex items-center justify-center min-h-[48px] px-5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400">
                        Batal &amp; Kembali
                    </a>
                    <button type="submit" :disabled="submitting" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 min-h-[48px] px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 transition shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span x-text="submitting ? 'Mengirim...' : 'Kirim Laporan Kendala'">Kirim Laporan Kendala</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-public-layout>
