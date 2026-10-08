<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Buat Surat Peringatan (SP) Resmi ITG') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Format standar persuratan Institut Teknologi Garut untuk Ormawa & Mahasiswa</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900 border px-3 py-1.5 rounded-lg bg-white shadow-sm">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        tipe: '{{ old('tipe_sasaran', 'ormawa') }}',
        mahasiswas: {{ json_encode(old('target_mahasiswas', [['nim' => '', 'nama' => '', 'prodi' => '', 'kontak' => '']])) }},
        addMahasiswa() { this.mahasiswas.push({ nim: '', nama: '', prodi: '', kontak: '' }); },
        removeMahasiswa(i) { if (this.mahasiswas.length > 1) this.mahasiswas.splice(i, 1); }
    }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-gray-200">
                <div class="border-b pb-4 mb-6">
                    <h3 class="font-bold text-gray-900 text-lg">Formulir Penerbitan Dokumen SP</h3>
                    <p class="text-xs text-gray-500 mt-1">Dokumen yang diterbitkan akan menghasilkan surat resmi ber-kop ITG dengan stempel dan QR Code verifikasi digital.</p>
                </div>

                <form method="POST" action="{{ route('bkhm.sp.store') }}" class="space-y-6">
                    @csrf

                    @if($errors->any())
                        <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-xs">
                            <ul class="list-disc pl-4 space-y-0.5">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- 1. Pilihan Tipe Sasaran -->
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">Tipe Sasaran Penerima SP <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-4">
                            <label :class="tipe === 'ormawa' ? 'border-indigo-600 bg-indigo-50/50 text-indigo-900 ring-2 ring-indigo-500' : 'border-gray-200 bg-gray-50 text-gray-700'" class="border rounded-xl p-4 cursor-pointer flex items-center gap-3 transition">
                                <input type="radio" name="tipe_sasaran" value="ormawa" x-model="tipe" class="text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <div class="font-bold text-sm">Organisasi Mahasiswa (ORMAWA)</div>
                                    <div class="text-[11px] text-gray-500">HIMA, BEM, BPM, atau UKM di lingkungan ITG</div>
                                </div>
                            </label>
                            <label :class="tipe === 'mahasiswa' ? 'border-indigo-600 bg-indigo-50/50 text-indigo-900 ring-2 ring-indigo-500' : 'border-gray-200 bg-gray-50 text-gray-700'" class="border rounded-xl p-4 cursor-pointer flex items-center gap-3 transition">
                                <input type="radio" name="tipe_sasaran" value="mahasiswa" x-model="tipe" class="text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <div class="font-bold text-sm">Mahasiswa Perorangan (Individu)</div>
                                    <div class="text-[11px] text-gray-500">Pelanggaran tata tertib / indisipliner mahasiswa</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 2. Detail Sasaran -->
                    <!-- Bagian A: Ormawa -->
                    <div x-show="tipe === 'ormawa'" class="space-y-3 bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Pilih Organisasi Target <span class="text-red-500">*</span></label>
                        <select name="target_user_id" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Pilih Organisasi Mahasiswa --</option>
                            @foreach($ormawas as $o)
                                <option value="{{ $o->id }}" {{ old('target_user_id') == $o->id ? 'selected' : '' }}>
                                    {{ $o->name }} ({{ $o->username }})
                                </option>
                            @endforeach
                        </select>
                        @error('target_user_id')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
                    </div>

                    <!-- Bagian B: Mahasiswa Perorangan (bisa lebih dari satu) -->
                    <div x-show="tipe === 'mahasiswa'" class="space-y-4 bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-xs font-bold uppercase tracking-wider text-gray-700">Daftar Mahasiswa Penerima</div>
                            <button type="button" @click="addMahasiswa()" class="inline-flex items-center gap-1 min-h-[44px] px-3 text-xs font-semibold text-indigo-700 border border-indigo-300 rounded-lg hover:bg-indigo-50 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                                + Tambah Mahasiswa
                            </button>
                        </div>

                        <template x-for="(m, idx) in mahasiswas" :key="idx">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-3 bg-white rounded-lg border border-gray-200">
                                <div class="sm:col-span-2 flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-gray-500" x-text="'Mahasiswa #' + (idx + 1)"></span>
                                    <button type="button" x-show="mahasiswas.length > 1" @click="removeMahasiswa(idx)" class="inline-flex items-center min-h-[44px] px-2 text-xs font-semibold text-rose-600 hover:text-rose-800">
                                        Hapus
                                    </button>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Induk Mahasiswa (NIM) <span class="text-red-500">*</span></label>
                                    <input type="text" :name="'target_mahasiswas[' + idx + '][nim]'" x-model="m.nim" placeholder="Contoh: 2306085" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap Mahasiswa <span class="text-red-500">*</span></label>
                                    <input type="text" :name="'target_mahasiswas[' + idx + '][nama]'" x-model="m.nama" placeholder="Contoh: Andi Muhamad Ramdani" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Program Studi <span class="text-red-500">*</span></label>
                                    <select :name="'target_mahasiswas[' + idx + '][prodi]'" x-model="m.prodi" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- Pilih Program Studi --</option>
                                        @foreach($prodis as $pr)
                                            <option value="{{ $pr }}">{{ $pr }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kontak Mahasiswa (Email / No. HP)</label>
                                    <input type="text" :name="'target_mahasiswas[' + idx + '][kontak]'" x-model="m.kontak" placeholder="Contoh: mahasiswa@itg.ac.id / 08123456789" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                        </template>

                        <p class="text-[10px] text-gray-500">Setiap mahasiswa akan tercantum sebagai satu baris pada tabel identitas di surat.</p>
                    </div>

                    <!-- 3. Rincian Surat -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nomor Surat Resmi <span class="text-red-500">*</span></label>
                            <input type="text" name="nomor_surat" placeholder="Contoh: 501/ITG/E.8/B/VI/2026" value="{{ old('nomor_surat', '501/ITG/BKHM/SP/' . date('m/Y')) }}" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            @error('nomor_surat')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Tingkat SP <span class="text-red-500">*</span></label>
                            <select name="tingkat" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 font-bold" required>
                                <option value="SP-1" {{ old('tingkat') === 'SP-1' ? 'selected' : '' }}>SP-1 (Peringatan Pertama)</option>
                                <option value="SP-2" {{ old('tingkat') === 'SP-2' ? 'selected' : '' }}>SP-2 (Peringatan Kedua)</option>
                                <option value="SP-3" {{ old('tingkat') === 'SP-3' ? 'selected' : '' }}>SP-3 (Peringatan Terakhir)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Perihal Surat <span class="text-red-500">*</span></label>
                            <input type="text" name="perihal" value="{{ old('perihal', 'Surat Peringatan Pelanggaran Tata Tertib & Ketentuan Kampus') }}" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            @error('perihal')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Alasan Singkat / Dasar Peringatan <span class="text-red-500">*</span></label>
                            <input type="text" name="alasan_singkat" placeholder="Contoh: Keterlambatan LPJ / Indisipliner Kegiatan" value="{{ old('alasan_singkat') }}" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            @error('alasan_singkat')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Uraian / Deskripsi Pelanggaran <span class="text-red-500">*</span></label>
                        <textarea name="deskripsi" rows="3" placeholder="Jelaskan secara rinci kronologi, pasal/aturan yang dilanggar, serta temuan pengawasan lapangan..." class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" required>{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-red-700 mb-1">Sanksi yang Ditetapkan <span class="text-red-500">*</span></label>
                        <textarea name="sanksi" rows="2" placeholder="Contoh: Penangguhan pencairan dana proposal berikutnya / Pembatasan izin kegiatan selama 1 semester..." class="w-full border-red-300 bg-red-50/20 rounded-lg text-sm focus:ring-red-500 focus:border-red-500 text-red-950 font-medium" required>{{ old('sanksi') }}</textarea>
                        @error('sanksi')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
                    </div>

                    <!-- Tanggal Surat & Informasi Penandatangan Resmi WR3 -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Tanggal Surat <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat', date('Y-m-d')) }}" class="w-full border-gray-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        <div class="p-3 bg-indigo-50/70 border border-indigo-200 rounded-xl">
                            <span class="text-[10px] font-bold text-indigo-900 uppercase tracking-wider block">Otoritas Penandatangan Resmi:</span>
                            <div class="text-xs font-bold text-gray-900 mt-0.5">{{ $wr3Info['nama'] ?? 'Pejabat Wakil Rektor III' }}</div>
                            <div class="text-[11px] text-gray-600">NIDN: {{ $wr3Info['nidn'] ?? '-' }} &bull; {{ $wr3Info['jabatan'] ?? 'Wakil Rektor III' }}</div>
                        </div>
                    </div>

                    <div class="p-3.5 bg-indigo-50 border border-indigo-200 rounded-xl flex items-start gap-2.5 text-xs text-indigo-900">
                        <svg class="w-4 h-4 text-indigo-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Sesuai tata kelola kemahasiswaan ITG, draf Surat Peringatan ini akan diajukan terlebih dahulu ke <strong>Wakil Rektor III</strong> untuk ditinjau, divalidasi, dan ditandatangani secara digital sebelum resmi diterbitkan kepada mahasiswa/ormawa target.</span>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-semibold transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold shadow-md transition inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Simpan &amp; Ajukan ke WR3</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
