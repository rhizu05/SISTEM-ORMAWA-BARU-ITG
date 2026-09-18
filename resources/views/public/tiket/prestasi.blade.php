<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prestasi & Delegasi Kompetisi Mahasiswa - ITG</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">
    <!-- Navbar -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between">
            <a href="{{ route('layanan.index') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo_itg.png') }}" alt="Logo ITG" class="h-11 w-auto object-contain">
                <div>
                    <span class="text-xs font-semibold tracking-wider text-amber-600 uppercase block">Institut Teknologi Garut</span>
                    <span class="text-base font-extrabold text-slate-900 leading-tight">Portal Layanan Mahasiswa</span>
                </div>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('prestasi.showcase') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 transition">
                    🏆 Showcase Prestasi
                </a>
                <a href="{{ route('layanan.cek-status') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
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
                <a href="{{ route('layanan.index') }}" class="hover:text-amber-600 transition">Portal Layanan</a>
                <span>/</span>
                <span class="text-slate-800 font-medium">Prestasi & Delegasi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pelaporan Prestasi & Bantuan Dana Lomba</h1>
            <p class="text-sm text-slate-600 mt-1">Daftarkan capaian prestasi lomba yang telah Anda raih atau ajukan permohonan bantuan dana delegasi kompetisi ke BKHM ITG.</p>
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
            <form action="{{ route('layanan.prestasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Pilihan Kategori Layanan Prestasi -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Pilih Jenis Pengajuan <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition hover:bg-amber-50/50 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50/40 border-slate-200">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="sub_kategori" value="lapor_prestasi" id="opt_lapor" {{ old('sub_kategori', 'lapor_prestasi') === 'lapor_prestasi' ? 'checked' : '' }}
                                    class="w-4 h-4 text-amber-600 border-slate-300 focus:ring-amber-500" onchange="toggleFormMode()">
                                <div class="font-bold text-slate-900 text-sm">Lapor Capaian Prestasi</div>
                            </div>
                            <p class="text-xs text-slate-500 mt-2 pl-7">Untuk perlombaan yang <strong>sudah selesai</strong>. Sertifikat Anda diverifikasi dan berkesempatan tampil di Showcase Kampus.</p>
                        </label>

                        <label class="relative flex flex-col p-4 rounded-xl border-2 cursor-pointer transition hover:bg-blue-50/50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/40 border-slate-200">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="sub_kategori" value="pengajuan_dana_delegasi" id="opt_delegasi" {{ old('sub_kategori') === 'pengajuan_dana_delegasi' ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500" onchange="toggleFormMode()">
                                <div class="font-bold text-slate-900 text-sm">Pengajuan Dana Delegasi</div>
                            </div>
                            <p class="text-xs text-slate-500 mt-2 pl-7">Untuk perlombaan yang <strong>akan diikuti</strong> dan memerlukan bantuan dana pendaftaran/transportasi kampus.</p>
                        </label>
                    </div>
                </div>

                <div class="border-b border-slate-100 pt-4 pb-4">
                    <h2 class="text-base font-bold text-slate-900">1. Data Mahasiswa</h2>
                    <p class="text-xs text-slate-500">Identitas pengaju tiket.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="nim" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">NIM <span class="text-rose-500">*</span></label>
                        <input type="text" name="nim" id="nim" value="{{ old('nim') }}" required placeholder="Contoh: 2106001"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2.5 px-3.5">
                    </div>
                    <div>
                        <label for="nama_mahasiswa" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_mahasiswa" id="nama_mahasiswa" value="{{ old('nama_mahasiswa') }}" required placeholder="Nama lengkap Anda"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2.5 px-3.5">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Email Aktif <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email@itg.ac.id atau gmail"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2.5 px-3.5">
                        <span class="text-[11px] text-slate-500 mt-1 block">Kode tiket & info verifikasi dikirim ke sini.</span>
                    </div>
                    <div>
                        <label for="no_hp" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">No. WhatsApp / HP</label>
                        <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2.5 px-3.5">
                    </div>
                </div>

                <div>
                    <label for="prodi" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Program Studi</label>
                    <select name="prodi" id="prodi" class="w-full text-sm rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2.5 px-3.5">
                        <option value="">-- Pilih Program Studi --</option>
                        <option value="Teknik Informatika" {{ old('prodi') == 'Teknik Informatika' ? 'selected' : '' }}>Teknik Informatika</option>
                        <option value="Sistem Informasi" {{ old('prodi') == 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
                        <option value="Teknik Sipil" {{ old('prodi') == 'Teknik Sipil' ? 'selected' : '' }}>Teknik Sipil</option>
                        <option value="Teknik Industri" {{ old('prodi') == 'Teknik Industri' ? 'selected' : '' }}>Teknik Industri</option>
                        <option value="Arsitektur" {{ old('prodi') == 'Arsitektur' ? 'selected' : '' }}>Arsitektur</option>
                    </select>
                </div>

                <div class="border-b border-slate-100 pt-4 pb-4">
                    <h2 class="text-base font-bold text-slate-900">2. Rincian Kegiatan Perlombaan</h2>
                    <p class="text-xs text-slate-500">Informasi kompetisi dan dokumen verifikasi.</p>
                </div>

                <div>
                    <label for="nama_kegiatan" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Nama Kompetisi / Kegiatan <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_kegiatan" id="nama_kegiatan" value="{{ old('nama_kegiatan') }}" required placeholder="Contoh: Gemastik XVII 2026 Divisi UX Design"
                        class="w-full text-sm rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2.5 px-3.5">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="penyelenggara" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Penyelenggara <span class="text-rose-500">*</span></label>
                        <input type="text" name="penyelenggara" id="penyelenggara" value="{{ old('penyelenggara') }}" required placeholder="Contoh: Kemendikbudristek / ITB"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2.5 px-3.5">
                    </div>
                    <div>
                        <label for="tingkat" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tingkat Kompetisi <span class="text-rose-500">*</span></label>
                        <select name="tingkat" id="tingkat" required class="w-full text-sm rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2.5 px-3.5">
                            <option value="">-- Pilih Tingkat --</option>
                            <option value="Perguruan Tinggi / Lokal" {{ old('tingkat') == 'Perguruan Tinggi / Lokal' ? 'selected' : '' }}>Internal Kampus / Lokal</option>
                            <option value="Wilayah / Provinsi" {{ old('tingkat') == 'Wilayah / Provinsi' ? 'selected' : '' }}>Wilayah / LLDIKTI / Provinsi</option>
                            <option value="Nasional" {{ old('tingkat') == 'Nasional' ? 'selected' : '' }}>Nasional</option>
                            <option value="Internasional" {{ old('tingkat') == 'Internasional' ? 'selected' : '' }}>Internasional</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div id="wrapper_capaian">
                        <label for="capaian" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Prestasi yang Diraih</label>
                        <input type="text" name="capaian" id="capaian" value="{{ old('capaian') }}" placeholder="Contoh: Juara 1 / Medali Emas / Finalis"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2.5 px-3.5">
                    </div>
                    <div id="wrapper_estimasi">
                        <label for="estimasi_biaya" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Estimasi Biaya yang Diajukan (Rp)</label>
                        <input type="number" name="estimasi_biaya" id="estimasi_biaya" value="{{ old('estimasi_biaya') }}" min="0" placeholder="Contoh: 1500000"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm py-2.5 px-3.5">
                    </div>
                    <div>
                        <label for="tanggal_kegiatan" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Pelaksanaan</label>
                        <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan" value="{{ old('tanggal_kegiatan') }}"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2.5 px-3.5">
                    </div>
                </div>

                <div>
                    <label for="lampiran_bukti" id="label_lampiran" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Sertifikat Prestasi / Proposal Kompetisi <span class="text-rose-500">*</span></label>
                    <input type="file" name="lampiran_bukti" id="lampiran_bukti" required accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100 border border-slate-200 rounded-xl p-2">
                    <span id="hint_lampiran" class="text-[11px] text-slate-500 mt-1 block">Format: PDF, JPG, PNG. Sertakan scan piagam/sertifikat resmi atau proposal lomba. Maks 5 MB.</span>
                </div>

                <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                    <a href="{{ route('layanan.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">
                        Batal
                    </a>
                    <button type="submit" id="btn_submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 shadow-md shadow-amber-500/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 py-8 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Institut Teknologi Garut. Bagian Kemahasiswaan & Hubungan Alumni.
    </footer>

    <script>
        function toggleFormMode() {
            const isLapor = document.getElementById('opt_lapor').checked;
            const capaianInput = document.getElementById('capaian');
            const estimasiInput = document.getElementById('estimasi_biaya');
            const labelLampiran = document.getElementById('label_lampiran');
            const hintLampiran = document.getElementById('hint_lampiran');
            const btnSubmit = document.getElementById('btn_submit');

            if (isLapor) {
                capaianInput.placeholder = "Contoh: Juara 1 / Medali Emas / Finalis";
                labelLampiran.innerHTML = 'Scan Sertifikat / Piagam Prestasi <span class="text-rose-500">*</span>';
                hintLampiran.textContent = "Format: PDF, JPG, PNG. Sertakan scan sertifikat atau dokumentasi penyerahan piala resmi. Maks 5 MB.";
                btnSubmit.className = "px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 shadow-md shadow-amber-500/20 transition flex items-center gap-2";
            } else {
                labelLampiran.innerHTML = 'Proposal Delegasi / Brosur Lomba <span class="text-rose-500">*</span>';
                hintLampiran.textContent = "Format: PDF. Sertakan proposal rincian kebutuhan biaya dan surat undangan/panduan lomba resmi. Maks 5 MB.";
                btnSubmit.className = "px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition flex items-center gap-2";
            }
        }
        document.addEventListener('DOMContentLoaded', toggleFormMode);
    </script>
</body>
</html>
