<x-public-layout title="Prestasi & Delegasi" brand-label="Portal Layanan Mahasiswa" accent="amber">
    <x-slot name="nav">
        <a href="{{ route('layanan.index') }}" class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 focus-visible:ring-offset-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            <span>Portal</span>
        </a>
        <a href="{{ route('prestasi.showcase') }}" class="inline-flex items-center min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 focus-visible:ring-offset-2">
            <span>Showcase</span>
        </a>
        <a href="{{ route('layanan.cek-status') }}" class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 focus-visible:ring-offset-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span class="hidden sm:inline">Cek Tiket</span>
        </a>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
        <nav class="flex items-center gap-2 text-xs text-slate-600 mb-3">
            <a href="{{ route('layanan.index') }}" class="hover:text-amber-700 transition">Portal Layanan</a>
            <span class="text-slate-400">/</span>
            <span class="text-slate-800 font-semibold">Prestasi &amp; Delegasi Lomba</span>
        </nav>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pelaporan Prestasi &amp; Bantuan Dana Lomba</h1>
        <p class="text-sm text-slate-600 mt-1">Daftarkan capaian prestasi lomba yang telah Anda raih atau ajukan permohonan bantuan dana delegasi kompetisi ke BKHM ITG.</p>

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl p-4 mt-6 mb-6 text-sm">
                <strong class="font-semibold block mb-1">Terdapat kesalahan input:</strong>
                <ul class="list-disc pl-5 space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border p-6 sm:p-8 mt-6 transition-all duration-200"
            :class="jenis === 'lapor_prestasi' ? 'border-amber-200 shadow-sm shadow-amber-500/5 ring-1 ring-amber-400/20' : 'border-indigo-200 shadow-sm shadow-indigo-500/5 ring-1 ring-indigo-400/20'">
            <form action="{{ route('layanan.prestasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6"
                x-data="{ 
                    jenis: '{{ old('sub_kategori', 'lapor_prestasi') }}', 
                    submitting: false,
                    inputFocus() {
                        return this.jenis === 'lapor_prestasi' 
                            ? 'focus:border-amber-600 focus:ring-amber-600' 
                            : 'focus:border-indigo-600 focus:ring-indigo-600';
                    }
                }" 
                @submit="submitting = true">
                @csrf

                <div>
                    <span class="block text-sm font-semibold text-slate-700 mb-2">Pilih Jenis Pengajuan <span class="text-rose-600">*</span></span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition border-slate-200"
                            :class="jenis === 'lapor_prestasi' ? 'border-amber-500 bg-amber-50/70 shadow-sm ring-2 ring-amber-500/20' : 'hover:border-amber-300 hover:bg-amber-50/30'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="sub_kategori" value="lapor_prestasi" id="opt_lapor"
                                    x-model="jenis"
                                    class="w-4 h-4 text-amber-700 border-slate-300 focus:ring-amber-600">
                                <span class="font-bold text-slate-900 text-sm">Lapor Capaian Prestasi</span>
                            </div>
                            <p class="text-xs text-slate-600 mt-2 pl-7">Untuk perlombaan yang <strong>sudah selesai</strong>. Sertifikat Anda diverifikasi dan berkesempatan tampil di Showcase Kampus.</p>
                        </label>

                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition border-slate-200"
                            :class="jenis === 'pengajuan_dana_delegasi' ? 'border-indigo-600 bg-indigo-50/70 shadow-sm ring-2 ring-indigo-500/20' : 'hover:border-indigo-300 hover:bg-indigo-50/30'">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="sub_kategori" value="pengajuan_dana_delegasi" id="opt_delegasi"
                                    x-model="jenis"
                                    class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                <span class="font-bold text-slate-900 text-sm">Pengajuan Dana Delegasi</span>
                            </div>
                            <p class="text-xs text-slate-600 mt-2 pl-7">Untuk perlombaan yang <strong>akan diikuti</strong> dan memerlukan bantuan dana pendaftaran atau transportasi kampus.</p>
                        </label>
                    </div>
                </div>

                <div class="border-b border-slate-100 pt-4 pb-4">
                    <h2 class="text-base font-bold text-slate-900">1. Data Mahasiswa</h2>
                    <p class="text-xs text-slate-600" x-text="jenis === 'lapor_prestasi' ? 'Identitas mahasiswa pengaju laporan prestasi.' : 'Identitas ketua delegasi / perwakilan tim permohonan dana bantuan.'">
                        Identitas pengaju tiket.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="nim" class="block text-sm font-semibold text-slate-700 mb-1.5">NIM <span class="text-rose-600">*</span></label>
                        <input type="text" name="nim" id="nim" value="{{ old('nim') }}" required placeholder="Contoh: 2106001"
                            class="w-full text-sm rounded-lg border-slate-300 py-3 px-3.5 placeholder-slate-500 transition-colors"
                            :class="inputFocus()">
                    </div>
                    <div>
                        <label for="nama_mahasiswa" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-rose-600">*</span></label>
                        <input type="text" name="nama_mahasiswa" id="nama_mahasiswa" value="{{ old('nama_mahasiswa') }}" required placeholder="Nama lengkap Anda"
                            class="w-full text-sm rounded-lg border-slate-300 py-3 px-3.5 placeholder-slate-500 transition-colors"
                            :class="inputFocus()">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email Aktif <span class="text-rose-600">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email@itg.ac.id atau gmail"
                            class="w-full text-sm rounded-lg border-slate-300 py-3 px-3.5 placeholder-slate-500 transition-colors"
                            :class="inputFocus()">
                        <span class="text-[11px] text-slate-600 mt-1 block">Kode tiket dan info verifikasi dikirim ke sini.</span>
                    </div>
                    <div>
                        <label for="no_hp" class="block text-sm font-semibold text-slate-700 mb-1.5">No. WhatsApp / HP <span class="text-rose-600">*</span></label>
                        <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}" required placeholder="08xxxxxxxxxx"
                            class="w-full text-sm rounded-lg border-slate-300 py-3 px-3.5 placeholder-slate-500 transition-colors"
                            :class="inputFocus()">
                    </div>
                </div>

                <div>
                    <label for="prodi" class="block text-sm font-semibold text-slate-700 mb-1.5">Program Studi <span class="text-rose-600">*</span></label>
                    <select name="prodi" id="prodi" required
                        class="w-full text-sm rounded-lg border-slate-300 py-3 px-3.5 transition-colors"
                        :class="inputFocus()">
                        <option value="">-- Pilih Program Studi --</option>
                        <option value="Teknik Informatika" {{ old('prodi') == 'Teknik Informatika' ? 'selected' : '' }}>Teknik Informatika</option>
                        <option value="Sistem Informasi" {{ old('prodi') == 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
                        <option value="Teknik Sipil" {{ old('prodi') == 'Teknik Sipil' ? 'selected' : '' }}>Teknik Sipil</option>
                        <option value="Teknik Industri" {{ old('prodi') == 'Teknik Industri' ? 'selected' : '' }}>Teknik Industri</option>
                        <option value="Arsitektur" {{ old('prodi') == 'Arsitektur' ? 'selected' : '' }}>Arsitektur</option>
                    </select>
                </div>

                <div class="border-b border-slate-100 pt-4 pb-4">
                    <h2 class="text-base font-bold text-slate-900" x-text="jenis === 'lapor_prestasi' ? '2. Rincian Capaian Prestasi' : '2. Rincian Delegasi & Bantuan Dana'">
                        {{ old('sub_kategori') === 'pengajuan_dana_delegasi' ? '2. Rincian Delegasi & Bantuan Dana' : '2. Rincian Capaian Prestasi' }}
                    </h2>
                    <p class="text-xs text-slate-600" x-text="jenis === 'lapor_prestasi' ? 'Informasi kompetisi yang telah Anda selesaikan dan bukti perolehan juara.' : 'Informasi kompetisi yang akan diikuti dan kebutuhan estimasi biaya bantuan.'">
                        {{ old('sub_kategori') === 'pengajuan_dana_delegasi' ? 'Informasi kompetisi yang akan diikuti dan kebutuhan estimasi biaya bantuan.' : 'Informasi kompetisi yang telah Anda selesaikan dan bukti perolehan juara.' }}
                    </p>
                </div>

                <div>
                    <label for="nama_kegiatan" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        <span x-text="jenis === 'lapor_prestasi' ? 'Nama Kompetisi / Kegiatan' : 'Nama Kompetisi yang Akan Diikuti'">Nama Kompetisi / Kegiatan</span>
                        <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="nama_kegiatan" id="nama_kegiatan" value="{{ old('nama_kegiatan') }}" required placeholder="Contoh: Gemastik XVII 2026 Divisi UX Design"
                        class="w-full text-sm rounded-lg border-slate-300 py-3 px-3.5 placeholder-slate-500 transition-colors"
                        :class="inputFocus()">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="penyelenggara" class="block text-sm font-semibold text-slate-700 mb-1.5">Penyelenggara <span class="text-rose-600">*</span></label>
                        <input type="text" name="penyelenggara" id="penyelenggara" value="{{ old('penyelenggara') }}" required placeholder="Contoh: Kemendikbudristek / ITB"
                            class="w-full text-sm rounded-lg border-slate-300 py-3 px-3.5 placeholder-slate-500 transition-colors"
                            :class="inputFocus()">
                    </div>
                    <div>
                        <label for="tingkat" class="block text-sm font-semibold text-slate-700 mb-1.5">Tingkat Kompetisi <span class="text-rose-600">*</span></label>
                        <select name="tingkat" id="tingkat" required
                            class="w-full text-sm rounded-lg border-slate-300 py-3 px-3.5 transition-colors"
                            :class="inputFocus()">
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="Perguruan Tinggi / Lokal" {{ old('tingkat') == 'Perguruan Tinggi / Lokal' ? 'selected' : '' }}>Internal Kampus / Lokal</option>
                            <option value="Wilayah / Provinsi" {{ old('tingkat') == 'Wilayah / Provinsi' ? 'selected' : '' }}>Wilayah / LLDIKTI / Provinsi</option>
                            <option value="Nasional" {{ old('tingkat') == 'Nasional' ? 'selected' : '' }}>Nasional</option>
                            <option value="Internasional" {{ old('tingkat') == 'Internasional' ? 'selected' : '' }}>Internasional</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="url_penyelenggara" class="block text-sm font-semibold text-slate-700 mb-1.5">Tautan / Website Resmi Penyelenggara</label>
                    <input type="url" name="url_penyelenggara" id="url_penyelenggara" value="{{ old('url_penyelenggara') }}" placeholder="https://link website lomba"
                        class="w-full text-sm rounded-lg border-slate-300 py-3 px-3.5 placeholder-slate-500 transition-colors"
                        :class="inputFocus()">
                    <span class="text-[11px] text-slate-600 mt-1 block">Tautan website atau publikasi lomba resmi untuk verifikasi panitia BKHM.</span>
                </div>

                <!-- Input Khusus Lapor Prestasi: Capaian / Predikat Juara (Outline Orange/Amber) -->
                <div x-show="jenis === 'lapor_prestasi'" id="wrapper_capaian">
                    <label for="capaian" class="block text-sm font-semibold text-slate-700 mb-1.5">Prestasi yang Diraih <span class="text-rose-600">*</span></label>
                    <input type="text" name="capaian" id="capaian" value="{{ old('capaian') }}"
                        :required="jenis === 'lapor_prestasi'"
                        placeholder="Contoh: Juara 1 / Medali Emas / Juara Harapan 1"
                        class="w-full text-sm rounded-lg border-slate-300 focus:border-amber-600 focus:ring-amber-600 py-3 px-3.5 placeholder-slate-500 transition-colors">
                    <span class="text-[11px] text-slate-600 mt-1 block">Tuliskan capaian juara atau medali yang berhasil Anda menangkan.</span>
                </div>

                <!-- Input Khusus Dana Delegasi: Estimasi Biaya Bantuan (Outline Ungu/Indigo) -->
                <div x-show="jenis === 'pengajuan_dana_delegasi'" id="wrapper_estimasi" style="display: none;">
                    <label for="estimasi_biaya" class="block text-sm font-semibold text-slate-700 mb-1.5">Estimasi Biaya yang Diajukan (Rp) <span class="text-rose-600">*</span></label>
                    <input type="number" name="estimasi_biaya" id="estimasi_biaya" value="{{ old('estimasi_biaya') }}" min="1"
                        :required="jenis === 'pengajuan_dana_delegasi'"
                        placeholder="Contoh: 1500000"
                        class="w-full text-sm rounded-lg border-slate-300 focus:border-indigo-600 focus:ring-indigo-600 py-3 px-3.5 placeholder-slate-500 transition-colors">
                    <span class="text-[11px] text-slate-600 mt-1 block">Nominal perkiraan bantuan dana yang diajukan ke kampus (registrasi lomba, tiket/transportasi, akomodasi).</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            <span x-text="jenis === 'lapor_prestasi' ? 'Tanggal Perolehan / Pelaksanaan' : 'Tanggal Mulai Perlombaan'">Tanggal Pelaksanaan</span>
                            <span class="text-rose-600">*</span>
                        </label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai', old('tanggal_kegiatan')) }}" required
                            class="w-full text-sm rounded-lg border-slate-300 py-3 px-3.5 transition-colors"
                            :class="inputFocus()">
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            <span x-text="jenis === 'lapor_prestasi' ? 'Tanggal Selesai (Bila Berhari-hari)' : 'Tanggal Selesai Perlombaan'">Tanggal Selesai Perlombaan</span>
                        </label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                            class="w-full text-sm rounded-lg border-slate-300 py-3 px-3.5 transition-colors"
                            :class="inputFocus()">
                        <span class="text-[11px] text-slate-600 mt-1 block">Bila kegiatan hanya 1 hari, tanggal selesai dapat dikosongkan.</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5" :class="jenis === 'lapor_prestasi' ? 'sm:grid-cols-2' : 'sm:grid-cols-1'">
                    <div>
                        <label for="lampiran_bukti" id="label_lampiran" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            <span x-text="jenis === 'lapor_prestasi' ? 'Scan Sertifikat / Piagam Prestasi' : 'Proposal Delegasi & Undangan Resmi Lomba'">Dokumen Lampiran</span>
                            <span class="text-rose-600">*</span>
                        </label>
                        <input type="file" name="lampiran_bukti" id="lampiran_bukti" required accept=".pdf,.jpg,.jpeg,.png"
                            class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold border border-slate-200 rounded-lg p-2 transition-colors"
                            :class="jenis === 'lapor_prestasi' ? 'file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100 focus:border-amber-600 focus:ring-amber-600' : 'file:bg-indigo-50 file:text-indigo-800 hover:file:bg-indigo-100 focus:border-indigo-600 focus:ring-indigo-600'">
                        <span id="hint_lampiran" class="text-[11px] text-slate-600 mt-1 block"
                            x-text="jenis === 'lapor_prestasi' ? 'Format: PDF, JPG, PNG. Sertakan scan piagam atau sertifikat resmi (maks 5 MB).' : 'Format: PDF, JPG, PNG. Sertakan proposal estimasi biaya serta surat undangan/LoA/panduan lomba resmi (maks 5 MB).'">
                            Format: PDF, JPG, PNG (maks 5 MB).
                        </span>
                    </div>
                    <div id="wrapper_foto_penyerahan" x-show="jenis === 'lapor_prestasi'">
                        <label for="foto_penyerahan" class="block text-sm font-semibold text-slate-700 mb-1.5">Foto Penyerahan Medali / Piala / Sertifikat</label>
                        <input type="file" name="foto_penyerahan" id="foto_penyerahan" accept=".jpg,.jpeg,.png"
                            class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100 border border-slate-200 rounded-lg p-2 transition-colors focus:border-amber-600 focus:ring-amber-600">
                        <span class="text-[11px] text-slate-600 mt-1 block">Format: JPG, PNG (maks 5 MB). Foto ini akan ditampilkan di Showcase Prestasi ITG bila diverifikasi.</span>
                    </div>
                </div>

                <div class="pt-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-slate-100">
                    <a href="{{ route('layanan.index') }}" class="inline-flex items-center justify-center min-h-[48px] px-5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400">
                        Batal &amp; Kembali
                    </a>
                    <button type="submit" id="btn_submit" :disabled="submitting"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 min-h-[48px] px-6 rounded-xl text-sm font-bold text-white transition shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-70"
                        :class="jenis === 'lapor_prestasi' ? 'bg-amber-700 hover:bg-amber-800 focus-visible:ring-amber-600' : 'bg-indigo-600 hover:bg-indigo-700 focus-visible:ring-indigo-500'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="submitting ? 'Mengirim Data...' : (jenis === 'lapor_prestasi' ? 'Kirim Laporan Prestasi' : 'Kirim Pengajuan Dana Delegasi')">Kirim Pengajuan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-public-layout>
