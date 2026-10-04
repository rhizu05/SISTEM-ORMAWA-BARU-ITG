<x-public-layout title="Aspirasi Mahasiswa" brand-label="Portal Layanan Mahasiswa" accent="indigo">
    <x-slot name="nav">
        <a href="{{ route('layanan.index') }}" class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            <span>Portal</span>
        </a>
        <a href="{{ route('layanan.cek-status') }}" class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span>Cek Tiket</span>
        </a>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
        <nav class="flex items-center gap-2 text-xs text-slate-600 mb-3">
            <a href="{{ route('layanan.index') }}" class="hover:text-indigo-700 transition">Portal Layanan</a>
            <span class="text-slate-400">/</span>
            <span class="text-slate-800 font-semibold">Aspirasi Mahasiswa</span>
        </nav>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Formulir Aspirasi &amp; Masukan Mahasiswa</h1>
        <p class="text-sm text-slate-600 mt-1">Sampaikan aspirasi, keluhan fasilitas, atau masukan akademik secara konstruktif langsung ke Badan Perwakilan Mahasiswa (BPM).</p>

        <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-4 sm:p-5 mt-6 mb-8 flex gap-3.5 text-indigo-900 text-xs sm:text-sm">
            <svg class="w-5 h-5 flex-shrink-0 text-indigo-700 mt-0.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <div class="leading-relaxed">
                <strong class="font-semibold block text-indigo-950 mb-0.5">Alur Penanganan &amp; Jaminan Kerahasiaan Identitas</strong>
                Aspirasi Anda akan <strong>ditampung dan ditelaah terlebih dahulu oleh BPM</strong> (Badan Perwakilan Mahasiswa), kemudian dapat dilanjutkan ke <strong>BKHM</strong> untuk tindak lanjut tingkat institusi. <strong>Data identitas Anda dijamin rahasia</strong> dan hanya dapat diketahui oleh pihak BPM dan BKHM. Anda dapat memantau perkembangannya melalui <strong>Kode Tiket</strong> yang diterbitkan setelah pengiriman.
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
            <form action="{{ route('layanan.aspirasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf

                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-base font-bold text-slate-900">1. Data Pengaju</h2>
                    <p class="text-xs text-slate-600">Identitas mahasiswa untuk keperluan verifikasi dan notifikasi tanggapan.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="nim" class="block text-sm font-semibold text-slate-700 mb-1.5">NIM <span class="text-rose-600">*</span></label>
                        <input type="text" name="nim" id="nim" value="{{ old('nim') }}" required placeholder="Contoh: 2106001"
                            class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-3.5 placeholder-slate-500">
                    </div>
                    <div>
                        <label for="nama_mahasiswa" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-rose-600">*</span></label>
                        <input type="text" name="nama_mahasiswa" id="nama_mahasiswa" value="{{ old('nama_mahasiswa') }}" required placeholder="Nama lengkap Anda"
                            class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-3.5 placeholder-slate-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email Aktif <span class="text-rose-600">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email@itg.ac.id atau gmail"
                            class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-3.5 placeholder-slate-500">
                        <span class="text-[11px] text-slate-600 mt-1 block">Kode tiket dan info tindak lanjut akan dikirim ke alamat ini.</span>
                    </div>
                    <div>
                        <label for="no_hp" class="block text-sm font-semibold text-slate-700 mb-1.5">No. WhatsApp / HP <span class="text-rose-600">*</span></label>
                        <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}" required placeholder="08xxxxxxxxxx"
                            class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-3.5 placeholder-slate-500">
                    </div>
                </div>

                <div>
                    <label for="prodi" class="block text-sm font-semibold text-slate-700 mb-1.5">Program Studi <span class="text-rose-600">*</span></label>
                    <select name="prodi" id="prodi" required class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-3.5">
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
                    <p class="text-xs text-slate-600">Uraikan pokok masalah atau masukan Anda dengan jelas dan sopan.</p>
                </div>

                <div>
                    <label for="judul" class="block text-sm font-semibold text-slate-700 mb-1.5">Judul Aspirasi <span class="text-rose-600">*</span></label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul') }}" required placeholder="Ringkasan inti aspirasi"
                        class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-3.5 placeholder-slate-500">
                </div>

                <div>
                    <label for="isi" class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi Lengkap Aspirasi <span class="text-rose-600">*</span></label>
                    <textarea name="isi" id="isi" rows="6" required placeholder="Jelaskan aspirasi, usulan, atau kronologi kendala yang Anda alami..."
                        class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-3.5 placeholder-slate-500">{{ old('isi') }}</textarea>
                </div>

                <div>
                    <label for="lampiran" class="block text-sm font-semibold text-slate-700 mb-1.5">Lampiran Dokumen / Foto Pendukung (Opsional)</label>
                    <input type="file" name="lampiran" id="lampiran" accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-200 rounded-lg p-2">
                    <span class="text-[11px] text-slate-600 mt-1 block">Format: PDF, JPG, PNG. Maksimal 5 MB.</span>
                </div>

                <div class="pt-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-slate-100">
                    <a href="{{ route('layanan.index') }}" class="inline-flex items-center justify-center min-h-[48px] px-5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400">
                        Batal &amp; Kembali
                    </a>
                    <button type="submit" :disabled="submitting" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 min-h-[48px] px-6 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 transition shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <span x-text="submitting ? 'Mengirim Data...' : 'Kirim Aspirasi Sekarang'">Kirim Aspirasi Sekarang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-public-layout>
