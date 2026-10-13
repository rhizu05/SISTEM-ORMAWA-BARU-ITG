<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Generator Surat Otomatis') }}</h2></x-slot>
    <div class="py-12" x-data="{ type: '{{ old('type','undangan') }}' }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Informasi Konfigurasi KOP Profil -->
            <div class="mb-6 p-4 rounded-2xl bg-indigo-50/70 border border-indigo-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-slate-700 leading-relaxed">
                        <span class="font-bold text-indigo-950">KOP Surat Otomatis:</span> Surat ini akan otomatis mencantumkan KOP resmi organisasi Anda (Nama Ormawa, Logo, Alamat Kampus, Website, dan Email). Format KOP dapat disesuaikan kapan saja di <strong class="text-indigo-900">Halaman Profil</strong>.
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" target="_blank" class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-indigo-300 hover:bg-indigo-50 text-indigo-700 font-semibold rounded-lg shadow-sm transition">
                    <span>Atur KOP di Profil</span> &rarr;
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('generator.letters.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <h3 class="font-bold text-indigo-600 border-b-2 border-indigo-500 pb-2 mb-4">Informasi Dasar Surat</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <x-input-label for="type" :value="__('Jenis Surat')" />
                            <select id="type" name="type" x-model="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="undangan">Surat Undangan</option>
                                <option value="tugas">Surat Tugas / Mandat</option>
                                <option value="permohonan">Surat Permohonan (Alat / Tempat)</option>
                                <option value="keterangan_aktif">Surat Keterangan Aktif</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="nomor_surat" :value="__('Nomor Surat (Opsional)')" />
                            <x-text-input id="nomor_surat" name="nomor_surat" type="text" class="mt-1 block w-full" placeholder="Contoh: 001/HIMATIF/ITG/V/2026" :value="old('nomor_surat')" />
                        </div>
                        <div>
                            <x-input-label for="lampiran" :value="__('Lampiran (Opsional)')" />
                            <x-text-input id="lampiran" name="lampiran" type="text" class="mt-1 block w-full" placeholder="Contoh: 1 (satu) Berkas atau -" :value="old('lampiran')" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="perihal" :value="__('Perihal')" />
                            <x-text-input id="perihal" name="perihal" type="text" class="mt-1 block w-full" required placeholder="Contoh: Undangan Pemateri Seminar Nasional" :value="old('perihal')" />
                            <x-input-error :messages="$errors->get('perihal')" class="mt-2" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="tujuan" :value="__('Tujuan Surat (Yth.)')" />
                            <x-text-input id="tujuan" name="tujuan" type="text" class="mt-1 block w-full" required placeholder="Contoh: Bapak Ir. Budi Santoso, M.T. di Tempat" :value="old('tujuan')" />
                            <x-input-error :messages="$errors->get('tujuan')" class="mt-2" />
                        </div>
                        <div class="sm:col-span-2 p-3 bg-indigo-50/50 rounded-lg border border-dashed border-indigo-200">
                            <x-input-label for="file_lampiran_pdf" :value="__('Unggah Lampiran Berkas PDF Tambahan (Opsional - Otomatis di-merge)')" class="text-indigo-900 font-semibold text-xs" />
                            <input id="file_lampiran_pdf" name="file_lampiran_pdf" type="file" accept="application/pdf" class="mt-1 block w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700" />
                            <p class="mt-1 text-[11px] text-slate-500">Jika diunggah, lembar lampiran PDF ini akan digabungkan otomatis di akhir dokumen surat resmi saat diunduh.</p>
                        </div>
                    </div>

                    <!-- Detail Undangan -->
                    <div x-show="type==='undangan'" x-cloak class="mt-6">
                        <h3 class="font-bold text-indigo-600 border-b-2 border-indigo-500 pb-2 mb-4">Detail Undangan</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <x-input-label for="kalimat_pembuka_undangan" :value="__('Kalimat Pembuka')" />
                                <textarea id="kalimat_pembuka_undangan" name="kalimat_pembuka" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Sehubungan dengan akan dilaksanakannya kegiatan...">{{ old('kalimat_pembuka', 'Dengan hormat, sehubungan dengan akan dilaksanakannya kegiatan kami, bersama ini kami bermaksud mengundang Bapak/Ibu untuk dapat hadir pada kegiatan yang diselenggarakan:') }}</textarea>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2"><x-input-label for="nama_acara" :value="__('Nama Acara')" /><x-text-input id="nama_acara" name="nama_acara" type="text" class="mt-1 block w-full" placeholder="Seminar Nasional Teknologi Informasi" :value="old('nama_acara')" /></div>
                                <div><x-input-label for="hari_tanggal" :value="__('Hari / Tanggal')" /><x-text-input id="hari_tanggal" name="hari_tanggal" type="text" class="mt-1 block w-full" placeholder="Senin, 20 Mei 2026" :value="old('hari_tanggal')" /></div>
                                <div><x-input-label for="waktu" :value="__('Waktu')" /><x-text-input id="waktu" name="waktu" type="text" class="mt-1 block w-full" placeholder="08.00 WIB s.d Selesai" :value="old('waktu')" /></div>
                                <div class="sm:col-span-2"><x-input-label for="tempat" :value="__('Tempat')" /><x-text-input id="tempat" name="tempat" type="text" class="mt-1 block w-full" placeholder="Aula Utama Institut Teknologi Garut" :value="old('tempat')" /></div>
                            </div>
                            <div>
                                <x-input-label for="kalimat_penutup_undangan" :value="__('Kalimat Penutup')" />
                                <textarea id="kalimat_penutup_undangan" name="kalimat_penutup" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('kalimat_penutup', 'Demikian surat undangan ini kami sampaikan. Atas perhatian, kehadiran, dan kerja sama Bapak/Ibu, kami ucapkan terima kasih.') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Tugas -->
                    <div x-show="type==='tugas'" x-cloak class="mt-6"
                         x-data="{
                             tambahanPetugas: [
                                 @php
                                     $oldTambahanNama = old('petugas_tambahan_nama', []);
                                     $oldTambahanNim  = old('petugas_tambahan_nim', []);
                                 @endphp
                                 @if(is_array($oldTambahanNama) && count($oldTambahanNama) > 0)
                                     @foreach($oldTambahanNama as $idx => $n)
                                         { nama: '{{ addslashes($n) }}', nim: '{{ addslashes($oldTambahanNim[$idx] ?? '') }}' },
                                     @endforeach
                                 @endif
                             ],
                             tambahPetugas() {
                                 this.tambahanPetugas.push({ nama: '', nim: '' });
                             },
                             hapusPetugas(index) {
                                 this.tambahanPetugas.splice(index, 1);
                             }
                         }">
                        <h3 class="font-bold text-indigo-600 border-b-2 border-indigo-500 pb-2 mb-4">Detail Surat Tugas</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <x-input-label for="kalimat_pembuka_tugas" :value="__('Kalimat Pembuka')" />
                                <textarea id="kalimat_pembuka_tugas" name="kalimat_pembuka_tugas" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('kalimat_pembuka_tugas', 'Yang bertanda tangan di bawah ini Pengurus Organisasi Kemahasiswaan Institut Teknologi Garut, dengan ini memberikan tugas / mandat kepada:') }}</textarea>
                            </div>

                            <!-- Daftar Petugas / Penerima Mandat -->
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-200 pb-2">
                                    <div>
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Daftar Penerima Tugas / Mandat</h4>
                                        <p class="text-[11px] text-slate-500">Dapat menambahkan lebih dari 1 orang jika tugas/mandat diberikan secara kolektif (delegasi/tim).</p>
                                    </div>
                                    <button type="button" @click="tambahPetugas()" class="self-start sm:self-auto inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>+ Tambah Petugas</span>
                                    </button>
                                </div>

                                <!-- Petugas Utama (Item #1) -->
                                <div class="p-3 bg-white border border-slate-200 rounded-lg shadow-2xs">
                                    <div class="text-[11px] font-bold text-indigo-900 mb-2">Penerima Mandat #1 (Utama)</div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <x-input-label for="nama_petugas" :value="__('Nama Petugas')" />
                                            <x-text-input id="nama_petugas" name="nama_petugas" type="text" class="mt-1 block w-full text-sm" placeholder="Contoh: Muhammad Rasyid" :value="old('nama_petugas')" />
                                        </div>
                                        <div>
                                            <x-input-label for="nim_tugas" :value="__('NIM')" />
                                            <x-text-input id="nim_tugas" name="nim" type="text" class="mt-1 block w-full text-sm font-mono" placeholder="Contoh: 2206001" x-bind:disabled="type !== 'tugas'" :value="old('nim')" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Petugas Tambahan Dinamis (Item #2, #3, dst.) -->
                                <template x-for="(petugas, idx) in tambahanPetugas" :key="idx">
                                    <div class="p-3 bg-white border border-indigo-100 rounded-lg shadow-2xs">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="text-[11px] font-bold text-slate-800" x-text="'Penerima Mandat #' + (idx + 2)"></div>
                                            <button type="button" @click="hapusPetugas(idx)" class="inline-flex items-center gap-1 text-[11px] text-rose-600 hover:text-rose-800 font-semibold transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Nama Petugas</label>
                                                <input type="text" name="petugas_tambahan_nama[]" x-model="petugas.nama" placeholder="Contoh: Ahmad Fauzan" x-bind:disabled="type !== 'tugas'" class="mt-1 block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">NIM</label>
                                                <input type="text" name="petugas_tambahan_nim[]" x-model="petugas.nim" placeholder="Contoh: 2206002" x-bind:disabled="type !== 'tugas'" class="mt-1 block w-full text-sm font-mono border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <x-input-label for="uraian_tugas" :value="__('Uraian Tugas')" />
                                    <textarea id="uraian_tugas" name="uraian_tugas" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Menjadi delegasi resmi dalam kegiatan Musyawarah Nasional...">{{ old('uraian_tugas') }}</textarea>
                                </div>
                                <div>
                                    <x-input-label for="tanggal_pelaksanaan" :value="__('Tanggal Pelaksanaan')" />
                                    <x-text-input id="tanggal_pelaksanaan" name="tanggal_pelaksanaan" type="text" class="mt-1 block w-full" placeholder="20 - 25 Mei 2026" :value="old('tanggal_pelaksanaan')" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="kalimat_penutup_tugas" :value="__('Kalimat Penutup')" />
                                <textarea id="kalimat_penutup_tugas" name="kalimat_penutup_tugas" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('kalimat_penutup_tugas', 'Untuk melaksanakan tugas tersebut dengan penuh tanggung jawab. Surat tugas ini dibuat untuk dapat dipergunakan sebagaimana mestinya.') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Permohonan -->
                    <div x-show="type==='permohonan'" x-cloak class="mt-6">
                        <h3 class="font-bold text-indigo-600 border-b-2 border-indigo-500 pb-2 mb-4">Detail Permohonan</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <x-input-label for="kalimat_pembuka_permohonan" :value="__('Kalimat Pembuka')" />
                                <textarea id="kalimat_pembuka_permohonan" name="kalimat_pembuka_permohonan" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('kalimat_pembuka_permohonan', 'Dengan hormat, sehubungan dengan agenda kegiatan yang akan kami selenggarakan, bersama ini kami mengajukan permohonan peminjaman:') }}</textarea>
                            </div>
                            <div><x-input-label for="nama_alat_tempat" :value="__('Nama Alat / Ruangan yang Dipinjam')" /><x-text-input id="nama_alat_tempat" name="nama_alat_tempat" type="text" class="mt-1 block w-full" placeholder="Aula Gedung D / Sound System Portable" :value="old('nama_alat_tempat')" /></div>
                            <div><x-input-label for="waktu_penggunaan" :value="__('Waktu Penggunaan')" /><x-text-input id="waktu_penggunaan" name="waktu_penggunaan" type="text" class="mt-1 block w-full" placeholder="Rabu, 22 Mei 2026 Jam 08.00 - 15.00 WIB" :value="old('waktu_penggunaan')" /></div>
                            <div><x-input-label for="alasan_tujuan" :value="__('Alasan / Tujuan Peminjaman')" /><textarea id="alasan_tujuan" name="alasan_tujuan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Keperluan pelaksanaan workshop pembekalan pengurus...">{{ old('alasan_tujuan') }}</textarea></div>
                            <div>
                                <x-input-label for="kalimat_penutup_permohonan" :value="__('Kalimat Penutup')" />
                                <textarea id="kalimat_penutup_permohonan" name="kalimat_penutup_permohonan" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('kalimat_penutup_permohonan', 'Demikian permohonan ini kami ajukan. Atas perhatian, izin, dan bantuan Bapak/Ibu, kami ucapkan terima kasih.') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Keterangan Aktif -->
                    <div x-show="type==='keterangan_aktif'" x-cloak class="mt-6"
                         x-data="{
                             tambahanMahasiswa: [
                                 @php
                                     $oldTambahanMhsNama    = old('mahasiswa_tambahan_nama', []);
                                     $oldTambahanMhsNim     = old('mahasiswa_tambahan_nim', []);
                                     $oldTambahanMhsJabatan = old('mahasiswa_tambahan_jabatan', []);
                                 @endphp
                                 @if(is_array($oldTambahanMhsNama) && count($oldTambahanMhsNama) > 0)
                                     @foreach($oldTambahanMhsNama as $idx => $n)
                                         {
                                             nama: '{{ addslashes($n) }}',
                                             nim: '{{ addslashes($oldTambahanMhsNim[$idx] ?? '') }}',
                                             jabatan: '{{ addslashes($oldTambahanMhsJabatan[$idx] ?? '') }}'
                                         },
                                     @endforeach
                                 @endif
                             ],
                             tambahMahasiswa() {
                                 this.tambahanMahasiswa.push({ nama: '', nim: '', jabatan: '' });
                             },
                             hapusMahasiswa(index) {
                                 this.tambahanMahasiswa.splice(index, 1);
                             }
                         }">
                        <h3 class="font-bold text-indigo-600 border-b-2 border-indigo-500 pb-2 mb-4">Detail Surat Keterangan</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <x-input-label for="kalimat_pembuka_ket" :value="__('Kalimat Pembuka')" />
                                <textarea id="kalimat_pembuka_ket" name="kalimat_pembuka_ket" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('kalimat_pembuka_ket', 'Yang bertanda tangan di bawah ini Pengurus Organisasi Kemahasiswaan Institut Teknologi Garut, dengan ini menerangkan bahwa:') }}</textarea>
                            </div>

                            <!-- Daftar Mahasiswa yang Diterangkan -->
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-200 pb-2">
                                    <div>
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Daftar Mahasiswa yang Diterangkan</h4>
                                        <p class="text-[11px] text-slate-500">Dapat menambahkan lebih dari 1 mahasiswa jika surat keterangan ditujukan secara kolektif.</p>
                                    </div>
                                    <button type="button" @click="tambahMahasiswa()" class="self-start sm:self-auto inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-xs transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>+ Tambah Mahasiswa</span>
                                    </button>
                                </div>

                                <!-- Mahasiswa Utama (#1) -->
                                <div class="p-3 bg-white border border-slate-200 rounded-lg shadow-2xs">
                                    <div class="text-[11px] font-bold text-indigo-900 mb-2">Mahasiswa #1 (Utama)</div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <x-input-label for="nama_mahasiswa" :value="__('Nama Mahasiswa')" />
                                            <x-text-input id="nama_mahasiswa" name="nama_mahasiswa" type="text" class="mt-1 block w-full text-sm" placeholder="Contoh: Deni Firmansyah" :value="old('nama_mahasiswa')" />
                                        </div>
                                        <div>
                                            <x-input-label for="nim_ket" :value="__('NIM')" />
                                            <x-text-input id="nim_ket" name="nim" type="text" class="mt-1 block w-full text-sm font-mono" placeholder="Contoh: 2206001" x-bind:disabled="type !== 'keterangan_aktif'" :value="old('nim')" />
                                        </div>
                                        <div class="sm:col-span-2">
                                            <x-input-label for="jabatan" :value="__('Jabatan di Organisasi')" />
                                            <x-text-input id="jabatan" name="jabatan" type="text" class="mt-1 block w-full text-sm" placeholder="Contoh: Anggota Bidang Minat dan Bakat" :value="old('jabatan')" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Mahasiswa Tambahan Dinamis (#2, #3, dst.) -->
                                <template x-for="(mhs, idx) in tambahanMahasiswa" :key="idx">
                                    <div class="p-3 bg-white border border-indigo-100 rounded-lg shadow-2xs">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="text-[11px] font-bold text-slate-800" x-text="'Mahasiswa #' + (idx + 2)"></div>
                                            <button type="button" @click="hapusMahasiswa(idx)" class="inline-flex items-center gap-1 text-[11px] text-rose-600 hover:text-rose-800 font-semibold transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Nama Mahasiswa</label>
                                                <input type="text" name="mahasiswa_tambahan_nama[]" x-model="mhs.nama" placeholder="Contoh: Siti Aisyah" x-bind:disabled="type !== 'keterangan_aktif'" class="mt-1 block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">NIM</label>
                                                <input type="text" name="mahasiswa_tambahan_nim[]" x-model="mhs.nim" placeholder="Contoh: 2206002" x-bind:disabled="type !== 'keterangan_aktif'" class="mt-1 block w-full text-sm font-mono border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Jabatan di Organisasi</label>
                                                <input type="text" name="mahasiswa_tambahan_jabatan[]" x-model="mhs.jabatan" placeholder="Contoh: Anggota Divisi Humas" x-bind:disabled="type !== 'keterangan_aktif'" class="mt-1 block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <x-input-label for="keperluan" :value="__('Tujuan / Keperluan Surat')" />
                                    <textarea id="keperluan" name="keperluan" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Persyaratan Beasiswa Prestasi Kemendikbud...">{{ old('keperluan') }}</textarea>
                                </div>
                            </div>
                            <div>
                                <x-input-label for="kalimat_penutup_ket" :value="__('Kalimat Penutup')" />
                                <textarea id="kalimat_penutup_ket" name="kalimat_penutup_ket" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('kalimat_penutup_ket', 'Adalah benar mahasiswa/anggota aktif pada organisasi kami. Surat keterangan ini kami buat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Penandatangan Dinamis --}}
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200"
                         x-data="{
                            signers: [{
                                jenis: 'internal',
                                role: 'ketua',
                                nama: '{{ addslashes(Auth::user()->nama_ketua ?? Auth::user()->name) }}',
                                jabatan: 'Ketua',
                                nim: '{{ addslashes(Auth::user()->nim_ketua ?? '') }}'
                            }],
                            namaMap: {
                                ketua: '{{ addslashes(Auth::user()->nama_ketua ?? Auth::user()->name) }}',
                                sekretaris: '{{ addslashes(Auth::user()->nama_sekretaris ?? Auth::user()->name) }}',
                                bendahara: '{{ addslashes(Auth::user()->nama_bendahara ?? Auth::user()->name) }}'
                            },
                            nimMap: {
                                ketua: '{{ addslashes(Auth::user()->nim_ketua ?? '') }}',
                                sekretaris: '{{ addslashes(Auth::user()->nim_sekretaris ?? '') }}',
                                bendahara: '{{ addslashes(Auth::user()->nim_bendahara ?? '') }}'
                            },
                            roleLabels: { ketua: 'Ketua', sekretaris: 'Sekretaris', bendahara: 'Bendahara' },
                            updateNama(idx) {
                                let s = this.signers[idx];
                                if (s.jenis !== 'internal') return;
                                s.nama = this.namaMap[s.role] || '';
                                s.nim = this.nimMap[s.role] || '';
                                s.jabatan = this.roleLabels[s.role] || 'Penandatangan';
                            },
                            onJenisChange(idx) {
                                let s = this.signers[idx];
                                if (s.jenis === 'internal') {
                                    s.nama = this.namaMap[s.role] || '';
                                    s.nim = this.nimMap[s.role] || '';
                                    s.jabatan = this.roleLabels[s.role] || 'Penandatangan';
                                } else {
                                    s.nama = '';
                                    s.nim = '';
                                    s.jabatan = '';
                                }
                            },
                            addSigner() {
                                this.signers.push({
                                    jenis: 'internal',
                                    role: 'sekretaris',
                                    nama: this.namaMap['sekretaris'] || '',
                                    jabatan: 'Sekretaris',
                                    nim: this.nimMap['sekretaris'] || ''
                                });
                            },
                            removeSigner(idx) {
                                if (this.signers.length > 1) this.signers.splice(idx, 1);
                            }
                         }">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="font-bold text-indigo-600">Penandatangan Surat Resmi</h3>
                                <p class="text-xs text-slate-500">Penandatangan internal (HIMA/UKM/BEM/BPM) menyertakan Nama &amp; NIM dan diverifikasi QR Code.</p>
                            </div>
                            <button type="button" @click="addSigner()"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg px-3 py-1.5 hover:bg-indigo-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                + Tambah Penandatangan
                            </button>
                        </div>

                        <template x-for="(signer, idx) in signers" :key="idx">
                            <div class="flex gap-3 items-start mb-3 p-3.5 bg-white rounded-lg border border-gray-200 shadow-sm">
                                <div class="flex-none pt-1">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold" x-text="idx + 1"></span>
                                </div>
                                <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jenis Penandatangan</label>
                                        <select :name="'penandatangan_jenis[' + idx + ']'"
                                                x-model="signer.jenis"
                                                @change="onJenisChange(idx)"
                                                class="block w-full border-gray-300 rounded-md shadow-sm text-xs">
                                            <option value="internal">Internal (Pengurus Ormawa)</option>
                                            <option value="eksternal">Pihak Luar (Manual)</option>
                                        </select>
                                    </div>
                                    <div x-show="signer.jenis === 'internal'">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jabatan Internal</label>
                                        <select :name="'penandatangan_role[' + idx + ']'"
                                                x-model="signer.role"
                                                @change="updateNama(idx)"
                                                class="block w-full border-gray-300 rounded-md shadow-sm text-xs">
                                            <option value="ketua">Ketua Umum</option>
                                            <option value="sekretaris">Sekretaris</option>
                                            <option value="bendahara">Bendahara</option>
                                        </select>
                                    </div>
                                    <div x-show="signer.jenis === 'eksternal'">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jabatan / Keterangan</label>
                                        <input type="text"
                                               :name="'penandatangan_jabatan[' + idx + ']'"
                                               x-model="signer.jabatan"
                                               placeholder="Contoh: Pembina UKM / Dosen Pembimbing"
                                               class="block w-full border-gray-300 rounded-md shadow-sm text-xs" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap</label>
                                        <input type="text"
                                               :name="'penandatangan_nama[' + idx + ']'"
                                               x-model="signer.nama"
                                               :placeholder="signer.jenis === 'internal' ? 'Nama pengurus' : 'Nama pihak luar...'"
                                               class="block w-full border-gray-300 rounded-md shadow-sm text-xs" />
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">
                                            <span x-text="signer.jenis === 'internal' ? 'Nomor Induk Mahasiswa (NIM) Penandatangan' : 'NIM / NIDN (Opsional)'"></span>
                                        </label>
                                        <input type="text"
                                               :name="'penandatangan_nim[' + idx + ']'"
                                               x-model="signer.nim"
                                               :placeholder="signer.jenis === 'internal' ? 'Contoh: 2206001 (Wajib diisi untuk pengurus HIMA/UKM/BEM/BPM)' : 'Nomor identitas pihak luar jika ada...'"
                                               class="block w-full border-gray-300 rounded-md shadow-sm text-xs font-mono" />
                                    </div>
                                </div>
                                <div class="flex-none mt-6">
                                    <button type="button" @click="removeSigner(idx)"
                                            x-show="signers.length > 1"
                                            class="p-1.5 text-slate-400 hover:text-rose-500 rounded transition-colors"
                                            title="Hapus penandatangan ini">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="flex justify-end mt-6"><x-primary-button>Generate Surat Resmi</x-primary-button></div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
