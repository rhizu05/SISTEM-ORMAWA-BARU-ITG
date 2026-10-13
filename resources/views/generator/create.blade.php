<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pembuatan Proposal Otomatis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                
                <!-- Breadcrumbs -->
                <div class="flex items-center text-sm text-gray-500 mb-8">
                    <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-800 font-medium">Buat Proposal</span>
                </div>

                <!-- Informasi Konfigurasi KOP Profil -->
                <div class="mb-6 p-4 rounded-2xl bg-indigo-50/70 border border-indigo-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-slate-700 leading-relaxed">
                            <span class="font-bold text-indigo-950">KOP Proposal Otomatis:</span> Dokumen ini akan otomatis mencantumkan KOP resmi organisasi Anda (Nama Ormawa, Logo, Alamat Kampus, Website, dan Email). Konfigurasi KOP dapat disesuaikan kapan saja di <strong class="text-indigo-900">Halaman Profil</strong>.
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" target="_blank" class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-indigo-300 hover:bg-indigo-50 text-indigo-700 font-semibold rounded-lg shadow-sm transition">
                        <span>Atur KOP di Profil</span> &rarr;
                    </a>
                </div>

                <form action="{{ route('generator.store') }}" method="POST">
                    @csrf

                    <!-- Section I: Pendahuluan & Narasi -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6 pb-2 border-b-2 border-indigo-500">
                            <span class="bg-indigo-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3">I</span>
                            <h3 class="text-xl font-bold text-gray-800">Pendahuluan & Narasi</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="nama_kegiatan" :value="__('Nama Kegiatan')" />
                                <x-text-input id="nama_kegiatan" name="nama_kegiatan" type="text" class="mt-1 block w-full" required placeholder="Contoh: Lomba Coding Nasional 2024" />
                            </div>
                            <div>
                                <x-input-label for="latar_belakang" :value="__('Latar Belakang')" />
                                <textarea id="latar_belakang" name="latar_belakang" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Jelaskan alasan kegiatan ini diadakan..."></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="tujuan" :value="__('Tujuan Kegiatan')" />
                                    <textarea id="tujuan" name="tujuan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Apa yang ingin dicapai?"></textarea>
                                </div>
                                <div>
                                    <x-input-label for="sasaran" :value="__('Sasaran / Peserta')" />
                                    <textarea id="sasaran" name="sasaran" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: Mahasiswa Se-Indonesia"></textarea>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="indikator" :value="__('Indikator Keberhasilan')" />
                                    <textarea id="indikator" name="indikator" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Cth: Jumlah peserta, tingkat kepuasan"></textarea>
                                </div>
                                <div>
                                    <x-input-label for="luaran" :value="__('Luaran (Output)')" />
                                    <textarea id="luaran" name="luaran" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Cth: Laporan, produk, publikasi"></textarea>
                                </div>
                                <div>
                                    <x-input-label for="dampak" :value="__('Dampak (Outcome)')" />
                                    <textarea id="dampak" name="dampak" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Cth: Peningkatan kompetensi mahasiswa"></textarea>
                                </div>
                            </div>
                            <div>
                                <x-input-label for="penutup" :value="__('Penutup')" />
                                <textarea id="penutup" name="penutup" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Kalimat penutup proposal..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section II: RAB -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6 pb-2 border-b-2 border-indigo-500">
                            <span class="bg-indigo-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3">II</span>
                            <h3 class="text-xl font-bold text-gray-800">Rencana Anggaran Biaya (RAB)</h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rincian Kebutuhan</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Vol</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-32">Satuan</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase w-40">Harga Satuan</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">#</th>
                                    </tr>
                                </thead>
                                <tbody id="rab-body">
                                    <tr class="border-b">
                                        <td class="p-2"><input type="text" name="rab_rincian[]" class="w-full border-gray-300 rounded-sm" placeholder="Misal: Konsumsi Peserta"></td>
                                        <td class="p-2"><input type="number" name="rab_vol[]" class="w-full text-center border-gray-300 rounded-sm"></td>
                                        <td class="p-2"><input type="text" name="rab_sat[]" class="w-full text-center border-gray-300 rounded-sm" placeholder="Box/Org"></td>
                                        <td class="p-2"><input type="number" name="rab_harga[]" class="w-full text-right border-gray-300 rounded-sm"></td>
                                        <td class="p-2 text-center">
                                            <button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700">&times;</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" onclick="addRow('rab-body')" class="mt-4 text-sm text-indigo-600 font-semibold hover:text-indigo-800">+ Tambah Baris RAB</button>
                        </div>
                    </div>

                    <!-- Section III: Susunan Panitia -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6 pb-2 border-indigo-500 border-b-2">
                            <span class="bg-indigo-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3">III</span>
                            <h3 class="text-xl font-bold text-gray-800">Susunan Panitia</h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jabatan</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Mahasiswa</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">#</th>
                                    </tr>
                                </thead>
                                <tbody id="pan-body">
                                    <tr class="border-b">
                                        <td class="p-2"><input type="text" name="pan_jabatan[]" class="w-full border-gray-300 rounded-sm" placeholder="Ketua Pelaksana"></td>
                                        <td class="p-2"><input type="text" name="pan_nama[]" class="w-full border-gray-300 rounded-sm" placeholder="Nama Lengkap"></td>
                                        <td class="p-2 text-center">
                                            <button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700">&times;</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" onclick="addRow('pan-body')" class="mt-4 text-sm text-indigo-600 font-semibold hover:text-indigo-800">+ Tambah Panitia</button>
                        </div>
                    </div>

                    <!-- Section IV: Penandatangan -->
                    <div class="mb-12 p-6 bg-slate-50 rounded-xl border border-slate-200"
                         x-data="{
                            signers: [{
                                jenis: 'internal',
                                role: 'ketua',
                                nama: '{{ addslashes(Auth::user()->nama_ketua ?? Auth::user()->name) }}',
                                nim: '{{ addslashes(Auth::user()->nim_ketua ?? '') }}',
                                jabatan: 'Ketua'
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
                            roleLabels: {
                                ketua: 'Ketua',
                                sekretaris: 'Sekretaris',
                                bendahara: 'Bendahara',
                                ketua_pelaksana: 'Ketua Pelaksana',
                                sekretaris_pelaksana: 'Sekretaris Pelaksana',
                                bendahara_pelaksana: 'Bendahara Pelaksana',
                                custom: ''
                            },
                            isProfileRole(role) {
                                return ['ketua', 'sekretaris', 'bendahara'].includes(role);
                            },
                            updateRole(idx) {
                                let s = this.signers[idx];
                                if (s.jenis !== 'internal') return;
                                if (this.isProfileRole(s.role)) {
                                    s.nama = this.namaMap[s.role] || '';
                                    s.nim = this.nimMap[s.role] || '';
                                    s.jabatan = this.roleLabels[s.role] || 'Penandatangan';
                                } else if (s.role === 'custom') {
                                    s.nama = '';
                                    s.nim = '';
                                    s.jabatan = '';
                                } else {
                                    s.nama = '';
                                    s.nim = '';
                                    s.jabatan = this.roleLabels[s.role] || '';
                                }
                            },
                            onJenisChange(idx) {
                                let s = this.signers[idx];
                                if (s.jenis === 'internal') {
                                    s.role = 'ketua';
                                    s.nama = this.namaMap['ketua'] || '';
                                    s.nim = this.nimMap['ketua'] || '';
                                    s.jabatan = 'Ketua';
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
                                    nim: this.nimMap['sekretaris'] || '',
                                    jabatan: 'Sekretaris'
                                });
                            },
                            removeSigner(idx) {
                                if (this.signers.length > 1) this.signers.splice(idx, 1);
                            }
                         }">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-slate-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20h16"/></svg>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800">Penandatangan Proposal</h3>
                                    <p class="text-xs text-slate-500">Pilih jabatan pengurus ormawa, panitia pelaksana, atau masukkan jabatan kustom.</p>
                                </div>
                            </div>
                            <button type="button" @click="addSigner()"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg px-3 py-1.5 hover:bg-indigo-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                + Tambah Penandatangan
                            </button>
                        </div>

                        <template x-for="(signer, idx) in signers" :key="idx">
                            <div class="mb-3 p-4 bg-white rounded-xl border border-slate-200 shadow-2xs space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold" x-text="idx + 1"></span>
                                        <span class="text-xs font-bold text-slate-700" x-text="'Penandatangan ke-' + (idx + 1)"></span>
                                        <template x-if="signer.jenis === 'internal' && isProfileRole(signer.role)">
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                                ✓ Otomatis terisi dari profil
                                            </span>
                                        </template>
                                    </div>
                                    <button type="button" @click="removeSigner(idx)"
                                            x-show="signers.length > 1"
                                            class="text-xs text-rose-500 hover:text-rose-700 font-semibold p-1"
                                            title="Hapus penandatangan ini">
                                        Hapus
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jenis Pihak</label>
                                        <select :name="'penandatangan_jenis[' + idx + ']'"
                                                x-model="signer.jenis"
                                                @change="onJenisChange(idx)"
                                                class="block w-full border-slate-300 rounded-lg shadow-2xs text-xs">
                                            <option value="internal">Internal (Ormawa ITG)</option>
                                            <option value="eksternal">Pihak Luar (Manual)</option>
                                        </select>
                                    </div>

                                    <div x-show="signer.jenis === 'internal'">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Pilihan Jabatan</label>
                                        <select :name="'penandatangan_role[' + idx + ']'"
                                                x-model="signer.role"
                                                @change="updateRole(idx)"
                                                class="block w-full border-slate-300 rounded-lg shadow-2xs text-xs">
                                            <optgroup label="Pengurus Inti (Otomatis dari Profil)">
                                                <option value="ketua">Ketua Ormawa</option>
                                                <option value="sekretaris">Sekretaris Ormawa</option>
                                                <option value="bendahara">Bendahara Ormawa</option>
                                            </optgroup>
                                            <optgroup label="Kepanitiaan Kegiatan">
                                                <option value="ketua_pelaksana">Ketua Pelaksana</option>
                                                <option value="sekretaris_pelaksana">Sekretaris Pelaksana</option>
                                                <option value="bendahara_pelaksana">Bendahara Pelaksana</option>
                                            </optgroup>
                                            <optgroup label="Lainnya">
                                                <option value="custom">Jabatan Lainnya (Input Manual)...</option>
                                            </optgroup>
                                        </select>
                                    </div>

                                    <!-- Kolom Jabatan Kustom / Eksternal -->
                                    <div x-show="signer.jenis === 'eksternal' || signer.role === 'custom'">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Jabatan</label>
                                        <input type="text"
                                               :name="'penandatangan_jabatan[' + idx + ']'"
                                               x-model="signer.jabatan"
                                               placeholder="Contoh: Koordinator Sie Acara / Pembina UKM"
                                               class="block w-full border-slate-300 rounded-lg shadow-2xs text-xs" />
                                    </div>
                                    <!-- Hidden fallback input jabatan untuk preset -->
                                    <template x-if="signer.jenis === 'internal' && signer.role !== 'custom'">
                                        <input type="hidden" :name="'penandatangan_jabatan[' + idx + ']'" :value="signer.jabatan">
                                    </template>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Lengkap</label>
                                        <input type="text"
                                               :name="'penandatangan_nama[' + idx + ']'"
                                               x-model="signer.nama"
                                               :readonly="signer.jenis === 'internal' && isProfileRole(signer.role)"
                                               :class="signer.jenis === 'internal' && isProfileRole(signer.role) ? 'bg-slate-100 cursor-not-allowed text-slate-700' : 'bg-white text-slate-900'"
                                               :placeholder="signer.jenis === 'internal' && isProfileRole(signer.role) ? 'Nama terisi dari profil' : 'Masukkan nama lengkap...'"
                                               class="block w-full border-slate-300 rounded-lg shadow-2xs text-xs font-medium" />
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600 mb-1" x-text="signer.jenis === 'internal' ? 'Nomor Induk Mahasiswa (NIM)' : 'NIM / NIDN / Identitas'"></label>
                                        <input type="text"
                                               :name="'penandatangan_nim[' + idx + ']'"
                                               x-model="signer.nim"
                                               :readonly="signer.jenis === 'internal' && isProfileRole(signer.role)"
                                               :class="signer.jenis === 'internal' && isProfileRole(signer.role) ? 'bg-slate-100 cursor-not-allowed text-slate-700' : 'bg-white text-slate-900'"
                                               :placeholder="signer.jenis === 'internal' && isProfileRole(signer.role) ? 'NIM terisi dari profil' : 'Contoh: 2106001'"
                                               class="block w-full border-slate-300 rounded-lg shadow-2xs text-xs font-mono" />
                                    </div>
                                </div>
                            </div>
                        </template>

                        <p class="mt-4 text-xs text-slate-500 italic">Penandatangan internal mendapat tanda tangan digital kriptografis yang dapat diverifikasi melalui QR code pada lembar pengesahan proposal.</p>
                    </div>

                    <div class="flex justify-end items-center gap-3 mt-8">
                        <a href="{{ route('generator.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 bg-white hover:bg-gray-50">Batal</a>
                        <button type="submit" name="action" value="draft" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md font-semibold text-sm text-gray-700 bg-white hover:bg-gray-50">Simpan Draft</button>
                        <button type="submit" name="action" value="print" class="inline-flex items-center px-8 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700">Simpan & Cetak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function addRow(tableId) {
            const tbody = document.getElementById(tableId);
            const firstRow = tbody.querySelector('tr');
            const newRow = firstRow.cloneNode(true);
            newRow.querySelectorAll('input').forEach(input => input.value = '');
            tbody.appendChild(newRow);
        }
    </script>
</x-app-layout>
