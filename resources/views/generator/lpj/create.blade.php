<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Laporan Pertanggungjawaban (LPJ) Kegiatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                
                <form action="{{ route('generator.lpj.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="proposal_id" value="{{ $proposal->id ?? '' }}">

                    <!-- BAGIAN 1: IDENTITAS & COVER LAPORAN -->
                    <div class="mb-10">
                        <div class="flex items-center mb-6 pb-2 border-b-2 border-indigo-500">
                            <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3 text-sm">1</span>
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Identitas &amp; Waktu Kegiatan</h3>
                                <p class="text-xs text-gray-500">Informasi utama kegiatan sesuai format standar ITG</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <x-input-label for="nama_kegiatan" :value="__('A. Nama Kegiatan')" class="font-bold text-gray-700" />
                                <x-text-input id="nama_kegiatan" name="nama_kegiatan" type="text" class="mt-1 block w-full" required placeholder="Contoh: SEMINAR NASIONAL CYBER SECURITY 2026" value="{{ $proposal->nama_kegiatan ?? '' }}" />
                            </div>

                            <div>
                                <x-input-label for="tahun_akademik" :value="__('Tahun Akademik')" class="font-bold text-gray-700" />
                                <x-text-input id="tahun_akademik" name="tahun_akademik" type="text" class="mt-1 block w-full" required value="2024-2025" placeholder="Contoh: 2024-2025" />
                            </div>

                            <div>
                                <x-input-label for="waktu_hari_tanggal" :value="__('B. Hari & Tanggal Pelaksanaan')" class="font-bold text-gray-700" />
                                <x-text-input id="waktu_hari_tanggal" name="waktu_hari_tanggal" type="text" class="mt-1 block w-full" required placeholder="Contoh: Sabtu, 18 Mei 2025" />
                            </div>

                            <div>
                                <x-input-label for="waktu_jam" :value="__('Waktu / Jam')" class="font-bold text-gray-700" />
                                <x-text-input id="waktu_jam" name="waktu_jam" type="text" class="mt-1 block w-full" required placeholder="Contoh: 08.00 – 16.00 WIB" />
                            </div>

                            <div>
                                <x-input-label for="tempat" :value="__('Tempat Pelaksanaan')" class="font-bold text-gray-700" />
                                <x-text-input id="tempat" name="tempat" type="text" class="mt-1 block w-full" required placeholder="Contoh: Aula Gedung D Institut Teknologi Garut, Jl. Mayor Syamsu No. 1" />
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN 2: BATANG TUBUH LAPORAN (BAB C - K) -->
                    <div class="mb-10">
                        <div class="flex items-center mb-6 pb-2 border-b-2 border-indigo-500">
                            <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3 text-sm">2</span>
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Batang Tubuh Laporan (Bab C - K)</h3>
                                <p class="text-xs text-gray-500">Poin narasi evaluasi, sasaran, metode, dan keberhasilan kegiatan</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <x-input-label for="tujuan_kegiatan" :value="__('C. Tujuan Kegiatan')" class="font-bold text-gray-700" />
                                <textarea id="tujuan_kegiatan" name="tujuan_kegiatan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" required placeholder="Kegiatan ini bertujuan untuk meningkatkan pemahaman mahasiswa mengenai...">Kegiatan ini bertujuan untuk memberikan wawasan komprehensif, pemahaman teknis, dan sertifikasi kompetensi dasar kepada mahasiswa di lingkungan Institut Teknologi Garut.</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="sasaran_kegiatan" :value="__('D. Sasaran Kegiatan')" class="font-bold text-gray-700" />
                                    <textarea id="sasaran_kegiatan" name="sasaran_kegiatan" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" required placeholder="Sasaran dalam kegiatan ini adalah seluruh mahasiswa tingkat 1-4...">Sasaran dalam kegiatan ini adalah seluruh mahasiswa aktif Institut Teknologi Garut serta perwakilan delegasi ormawa.</textarea>
                                </div>
                                <div>
                                    <x-input-label for="ruang_lingkup" :value="__('Ruang Lingkup Pembahasan')" class="font-bold text-gray-700" />
                                    <textarea id="ruang_lingkup" name="ruang_lingkup" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" required placeholder="Adapun ruang lingkup kegiatan ini mencakup pembahasan mengenai:&#10;▪ Pengantar keamanan sistem&#10;▪ Praktik hands-on penetration testing&#10;▪ Evaluasi implementasi sistem">Adapun ruang lingkup kegiatan ini mencakup pembahasan mengenai:
▪ Penerapan keamanan jaringan dan sistem komputer
▪ Simulasi pertahanan siber dan mitigasi ancaman
▪ Diskusi interaktif dan evaluasi implementasi sistem</textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="metode_kegiatan" :value="__('E. Metode Pendekatan Kegiatan')" class="font-bold text-gray-700" />
                                    <textarea id="metode_kegiatan" name="metode_kegiatan" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" required placeholder="Pendekatan yang dilakukan...">Kegiatan dilakukan berdasarkan pendekatan partisipatif, dimana para peserta dilibatkan secara aktif dalam workshop dan diskusi studi kasus langsung. Narasumber memfasilitasi jalannya praktik dan tanya jawab.</textarea>
                                </div>
                                <div>
                                    <x-input-label for="tahapan_kegiatan" :value="__('Tahapan Kegiatan')" class="font-bold text-gray-700" />
                                    <textarea id="tahapan_kegiatan" name="tahapan_kegiatan" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" required placeholder="Tahapan kegiatannya adalah:&#10;▪ Tahap 1: Registrasi dan pembukaan&#10;▪ Tahap 2: Pemaparan materi&#10;▪ Tahap 3: Praktik dan evaluasi">Tahapan kegiatannya adalah sebagai berikut:
▪ Tahap 1: Registrasi peserta, pembukaan, dan pre-test materi
▪ Tahap 2: Pemaparan modul materi oleh narasumber utama
▪ Tahap 3: Praktik terbimbing (hands-on) dan diskusi interaktif
▪ Tahap 4: Post-test, evaluasi capaian, dan penutupan</textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="peserta_deskripsi" :value="__('F. Peserta')" class="font-bold text-gray-700" />
                                    <textarea id="peserta_deskripsi" name="peserta_deskripsi" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" required placeholder="Peserta yang hadir dalam kegiatan ini...">Peserta yang hadir dalam kegiatan ini berjumlah 120 orang mahasiswa dari berbagai program studi di ITG, dengan daftar kehadiran terlampir pada dokumen ini.</textarea>
                                </div>
                                <div>
                                    <x-input-label for="indikator_keberhasilan" :value="__('H. Indikator Keberhasilan')" class="font-bold text-gray-700" />
                                    <textarea id="indikator_keberhasilan" name="indikator_keberhasilan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" required placeholder="Indikator keberhasilan kegiatannya adalah...">Indikator keberhasilan kegiatan adalah tercapainya tingkat kepuasan peserta minimal 85% dan peningkatan pemahaman teknis peserta melalui hasil post-test.</textarea>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="jadwal_kegiatan" :value="__('G. Jadwal Kegiatan (Rundown)')" class="font-bold text-gray-700" />
                                <textarea id="jadwal_kegiatan" name="jadwal_kegiatan" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm font-mono text-xs" required placeholder="08.00 - 08.30 Registrasi Peserta&#10;08.30 - 09.00 Pembukaan & Sambutan&#10;09.00 - 12.00 Sesi Materi 1&#10;12.00 - 13.00 Ishoma&#10;13.00 - 15.30 Workshop Praktik&#10;15.30 - 16.00 Evaluasi & Penutupan">08.00 - 08.30  Registrasi Peserta & Pengisian Presensi
08.30 - 09.00  Pembukaan dan Sambutan Ketua Pelaksana serta Ketua Ormawa
09.00 - 12.00  Pemaparan Materi Utama dan Sesi Tanya Jawab
12.00 - 13.00  Istirahat, Sholat, dan Makan (Ishoma)
13.00 - 15.30  Workshop Praktik Terbimbing dan Simulasi Kasus
15.30 - 16.00  Rekomendasi Peningkatan, Evaluasi, dan Penutupan</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="hambatan" :value="__('I. Hambatan-Hambatan')" class="font-bold text-gray-700" />
                                    <textarea id="hambatan" name="hambatan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" required placeholder="Adapun hambatan kegiatan yang terjadi adalah:&#10;- Keterbatasan daya tampung laboratorium&#10;- Gangguan teknis jaringan sementara">- Kapasitas jaringan laboratorium sempat mengalami lonjakan trafik saat sesi pengunduhan materi.
- Waktu sesi tanya jawab terasa singkat karena tingginya antusiasme peserta.</textarea>
                                </div>
                                <div>
                                    <x-input-label for="upaya_mengatasi" :value="__('Upaya Mengatasi Hambatan')" class="font-bold text-gray-700" />
                                    <textarea id="upaya_mengatasi" name="upaya_mengatasi" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" required placeholder="Upaya mengatasi:&#10;- Penyediaan mirror server lokal&#10;- Membuat sesi forum diskusi daring lanjutan">- Panitia langsung menyediakan mirror lokal untuk distribusi aset dan modul materi secara offline.
- Dibuat grup koordinasi dan forum daring khusus untuk diskusi tindak lanjut pasca kegiatan.</textarea>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="penutup" :value="__('K. Penutup')" class="font-bold text-gray-700" />
                                <textarea id="penutup" name="penutup" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" required placeholder="Demikian laporan kegiatan ini dibuat...">Demikian laporan kegiatan ini dibuat dengan maksud untuk memberikan gambaran pertanggungjawaban pelaksanaan kegiatan di masa yang akan datang demi kelangsungan dan kemajuan Institut Teknologi Garut.</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN 3: LAMPIRAN LAPORAN KEUANGAN (6 KOLOM RESMI) -->
                    <div class="mb-10" x-data="keuanganHandler()">
                        <div class="flex items-center justify-between mb-6 pb-2 border-b-2 border-indigo-500">
                            <div class="flex items-center">
                                <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3 text-sm">3</span>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Lampiran Laporan Keuangan (6 Kolom Resmi)</h3>
                                    <p class="text-xs text-gray-500">Pencatatan tanggal, jenis kebutuhan, pemasukan, pengeluaran, dan saldo sisa</p>
                                </div>
                            </div>
                            <button type="button" @click="addRow()" class="inline-flex items-center text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 px-3 py-1.5 rounded hover:bg-indigo-100 font-semibold">
                                + Tambah Baris Transaksi
                            </button>
                        </div>

                        <div class="overflow-x-auto border rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                <thead class="bg-gray-100 font-bold text-gray-700 uppercase">
                                    <tr>
                                        <th class="p-2 text-left w-32">Tanggal</th>
                                        <th class="p-2 text-left">Jenis Kebutuhan</th>
                                        <th class="p-2 text-right w-36">Pemasukan (Rp)</th>
                                        <th class="p-2 text-right w-36">Pengeluaran (Rp)</th>
                                        <th class="p-2 text-right w-36">Sisa (Rp)</th>
                                        <th class="p-2 text-left w-44">Keterangan</th>
                                        <th class="p-2 text-center w-12">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, idx) in items" :key="idx">
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="p-1.5">
                                                <input type="text" :name="'keuangan_items[' + idx + '][tanggal]'" x-model="row.tanggal" placeholder="14 Mei 2025" class="w-full text-xs border-gray-300 rounded p-1">
                                            </td>
                                            <td class="p-1.5">
                                                <input type="text" :name="'keuangan_items[' + idx + '][kebutuhan]'" x-model="row.kebutuhan" placeholder="Anggaran Kemahasiswaan / Konsumsi" class="w-full text-xs border-gray-300 rounded p-1">
                                            </td>
                                            <td class="p-1.5">
                                                <input type="number" :name="'keuangan_items[' + idx + '][pemasukan]'" x-model.number="row.pemasukan" @input="recalc()" class="w-full text-xs text-right border-gray-300 rounded p-1">
                                            </td>
                                            <td class="p-1.5">
                                                <input type="number" :name="'keuangan_items[' + idx + '][pengeluaran]'" x-model.number="row.pengeluaran" @input="recalc()" class="w-full text-xs text-right border-gray-300 rounded p-1">
                                            </td>
                                            <td class="p-1.5">
                                                <input type="number" :name="'keuangan_items[' + idx + '][sisa]'" :value="row.sisa" readonly class="w-full text-xs text-right bg-gray-100 border-gray-200 rounded p-1 font-semibold text-gray-700">
                                            </td>
                                            <td class="p-1.5">
                                                <input type="text" :name="'keuangan_items[' + idx + '][keterangan]'" x-model="row.keterangan" placeholder="Kuitansi / Struk Terlampir" class="w-full text-xs border-gray-300 rounded p-1">
                                            </td>
                                            <td class="p-1.5 text-center">
                                                <button type="button" @click="removeRow(idx)" class="text-red-500 font-bold hover:text-red-700">&times;</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="bg-gray-50 font-bold text-xs text-gray-800">
                                    <tr>
                                        <td colspan="2" class="p-2 text-right">TOTAL KESELURUHAN:</td>
                                        <td class="p-2 text-right text-indigo-700" x-text="formatRupiah(totalPemasukan)"></td>
                                        <td class="p-2 text-right text-red-700" x-text="formatRupiah(totalPengeluaran)"></td>
                                        <td class="p-2 text-right text-emerald-700 font-extrabold" x-text="formatRupiah(totalSisa)"></td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- BAGIAN 4: LAMPIRAN DAFTAR HADIR PESERTA -->
                    <div class="mb-10" x-data="hadirHandler()">
                        <div class="flex items-center justify-between mb-6 pb-2 border-b-2 border-indigo-500">
                            <div class="flex items-center">
                                <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3 text-sm">4</span>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Lampiran Daftar Hadir Peserta</h3>
                                    <p class="text-xs text-gray-500">Tabel absensi peserta untuk dicetak pada lembar lampiran terpisah</p>
                                </div>
                            </div>
                            <button type="button" @click="addRow()" class="inline-flex items-center text-xs bg-indigo-50 text-indigo-700 border border-indigo-200 px-3 py-1.5 rounded hover:bg-indigo-100 font-semibold">
                                + Tambah Peserta
                            </button>
                        </div>

                        <div class="overflow-x-auto border rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                <thead class="bg-gray-100 font-bold text-gray-700 uppercase">
                                    <tr>
                                        <th class="p-2 text-center w-12">No</th>
                                        <th class="p-2 text-left">Nama Lengkap</th>
                                        <th class="p-2 text-left">Jabatan / Instansi</th>
                                        <th class="p-2 text-left w-36">No HP</th>
                                        <th class="p-2 text-center w-12">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(peserta, idx) in items" :key="idx">
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="p-2 text-center font-bold text-gray-500" x-text="idx + 1"></td>
                                            <td class="p-1.5">
                                                <input type="text" :name="'daftar_hadir_items[' + idx + '][nama]'" x-model="peserta.nama" placeholder="Nama Peserta..." class="w-full text-xs border-gray-300 rounded p-1">
                                            </td>
                                            <td class="p-1.5">
                                                <input type="text" :name="'daftar_hadir_items[' + idx + '][jabatan]'" x-model="peserta.jabatan" placeholder="Mahasiswa / Dosen / Tamu" class="w-full text-xs border-gray-300 rounded p-1">
                                            </td>
                                            <td class="p-1.5">
                                                <input type="text" :name="'daftar_hadir_items[' + idx + '][no_hp]'" x-model="peserta.no_hp" placeholder="0812xxxx" class="w-full text-xs border-gray-300 rounded p-1">
                                            </td>
                                            <td class="p-1.5 text-center">
                                                <button type="button" @click="removeRow(idx)" class="text-red-500 font-bold hover:text-red-700">&times;</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- BAGIAN 5: UPLOAD DOKUMENTASI KEGIATAN & STRUK KEUANGAN -->
                    <div class="mb-10">
                        <div class="flex items-center mb-6 pb-2 border-b-2 border-indigo-500">
                            <span class="bg-indigo-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3 text-sm">5</span>
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Lampiran Foto Dokumentasi &amp; Bukti Struk</h3>
                                <p class="text-xs text-gray-500">Otomatis ditata ke dalam lembar khusus foto berbingkai pada dokumen PDF</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-5 bg-indigo-50/50 rounded-lg border border-dashed border-indigo-300">
                                <x-input-label for="foto_dokumentasi" :value="__('Upload Foto Dokumentasi Kegiatan (Maks 6 Foto)')" class="font-bold text-indigo-900" />
                                <input id="foto_dokumentasi" name="foto_dokumentasi[]" type="file" multiple accept="image/*" class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700" />
                                <p class="mt-2 text-xs text-gray-500">Format gambar JPEG, PNG. Akan ditata rapi dalam kisi foto dokumentasi.</p>
                            </div>

                            <div class="p-5 bg-emerald-50/50 rounded-lg border border-dashed border-emerald-300">
                                <x-input-label for="bukti_pembayaran" :value="__('Upload Bukti Struk / Kuitansi / Nota (Maks 4 Foto)')" class="font-bold text-emerald-900" />
                                <input id="bukti_pembayaran" name="bukti_pembayaran[]" type="file" multiple accept="image/*,.pdf" class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700" />
                                <p class="mt-2 text-xs text-gray-500">Format gambar atau PDF kuitansi. Akan disandingkan di lembar Laporan Keuangan.</p>
                            </div>
                        </div>
                    </div>

                    <!-- BAGIAN 6: TANDA TANGAN RESMI 4 PIHAK STANDAR ITG -->
                    <div class="mb-10 p-6 bg-gray-50 rounded-lg border border-gray-300"
                         x-data="{
                            signers: [
                                { jenis: 'internal', role: 'ormawa', nama: '{{ addslashes(Auth::user()->nama_ketua ?? Auth::user()->name) }}', jabatan: 'Ketua Ormawa', nim: '{{ addslashes(Auth::user()->nim_ketua ?? '') }}' },
                                { jenis: 'internal', role: 'pelaksana', nama: '{{ addslashes(Auth::user()->nama_sekretaris ?? Auth::user()->name) }}', jabatan: 'Ketua Pelaksana', nim: '{{ addslashes(Auth::user()->nim_sekretaris ?? '') }}' },
                                { jenis: 'eksternal', role: 'wr3', nama: 'Dr. Ayu Latifah, S.T., M.T.', jabatan: 'Wakil Rektor III', nim: '0421099301' },
                                { jenis: 'eksternal', role: 'bkhm', nama: 'Encep Jianul Hayat, S.T., M.T.', jabatan: 'Kepala BKKH', nim: '0401019004' }
                            ]
                         }">
                        <div class="flex items-center mb-4">
                            <svg class="w-6 h-6 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20h16"/></svg>
                            <h3 class="text-lg font-bold text-gray-800">Susunan Penandatangan Dokumen (Standar Template ITG)</h3>
                        </div>
                        <p class="text-xs text-gray-600 mb-6">Secara default disesuaikan dengan template resmi ITG: Ketua Ormawa &amp; Ketua Pelaksana (isi NIM), serta Mengetahui Wakil Rektor III dan Kepala BKKH (NIDN).</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="(signer, idx) in signers" :key="idx">
                                <div class="p-3 bg-white rounded border border-gray-200 shadow-sm">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-indigo-700" x-text="(idx < 2 ? 'Pengaju: ' : 'Mengetahui: ') + signer.jabatan"></span>
                                        <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-600" x-text="signer.jenis === 'internal' ? 'Digital QR' : 'Pejabat ITG'"></span>
                                    </div>
                                    <input type="hidden" :name="'penandatangan_jenis[' + idx + ']'" :value="signer.jenis">
                                    <input type="hidden" :name="'penandatangan_role[' + idx + ']'" :value="signer.role">
                                    <div class="space-y-2">
                                        <div>
                                            <label class="block text-xs text-gray-500">Jabatan Penandatangan</label>
                                            <input type="text" :name="'penandatangan_jabatan[' + idx + ']'" x-model="signer.jabatan" class="w-full text-xs border-gray-300 rounded p-1.5 font-medium">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500">Nama Lengkap &amp; Gelar</label>
                                            <input type="text" :name="'penandatangan_nama[' + idx + ']'" x-model="signer.nama" class="w-full text-xs border-gray-300 rounded p-1.5 font-semibold">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-500" x-text="idx < 2 ? 'Nomor Induk Mahasiswa (NIM)' : 'Nomor Induk Dosen (NIDN)'"></label>
                                            <input type="text" :name="'penandatangan_nim[' + idx + ']'" x-model="signer.nim" :placeholder="idx < 2 ? 'Contoh: 2106001' : 'Contoh: 0421099301'" class="w-full text-xs border-gray-300 rounded p-1.5 font-mono">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="flex justify-end gap-4 mt-8 pt-4 border-t">
                        <x-secondary-button type="submit" name="action" value="draft" class="px-6 py-2.5">
                            Simpan Draft
                        </x-secondary-button>
                        <x-primary-button type="submit" name="action" value="print" class="px-8 py-3 text-base">
                            Simpan &amp; Lihat Preview LPJ
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function keuanganHandler() {
            return {
                items: [
                    { tanggal: '14 Mei 2025', kebutuhan: 'Anggaran Kemahasiswaan ITG', pemasukan: 1000000, pengeluaran: 0, sisa: 1000000, keterangan: 'Kuitansi Terlampir' },
                    { tanggal: '18 Mei 2025', kebutuhan: 'Cetak Banner & Sertifikat', pemasukan: 0, pengeluaran: 150000, sisa: 850000, keterangan: 'Struk Terlampir' }
                ],
                totalPemasukan: 1000000,
                totalPengeluaran: 150000,
                totalSisa: 850000,
                addRow() {
                    let lastSisa = this.items.length > 0 ? this.items[this.items.length - 1].sisa : 0;
                    this.items.push({
                        tanggal: '',
                        kebutuhan: '',
                        pemasukan: 0,
                        pengeluaran: 0,
                        sisa: lastSisa,
                        keterangan: 'Struk Terlampir'
                    });
                    this.recalc();
                },
                removeRow(idx) {
                    if (this.items.length > 1) {
                        this.items.splice(idx, 1);
                        this.recalc();
                    }
                },
                recalc() {
                    let runningSisa = 0;
                    let sumIn = 0;
                    let sumOut = 0;

                    this.items.forEach(row => {
                        let pIn = Number(row.pemasukan) || 0;
                        let pOut = Number(row.pengeluaran) || 0;
                        runningSisa += (pIn - pOut);
                        row.sisa = runningSisa;
                        sumIn += pIn;
                        sumOut += pOut;
                    });

                    this.totalPemasukan = sumIn;
                    this.totalPengeluaran = sumOut;
                    this.totalSisa = runningSisa;
                },
                formatRupiah(num) {
                    return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
                }
            };
        }

        function hadirHandler() {
            return {
                items: [
                    { nama: 'Ahmad Fauzi, S.Kom.', jabatan: 'Dosen Pembimbing ITG', no_hp: '081234567890' },
                    { nama: 'Muhammad Rasyid', jabatan: 'Ketua HIMA Informatika', no_hp: '085712345678' }
                ],
                addRow() {
                    this.items.push({ nama: '', jabatan: '', no_hp: '' });
                },
                removeRow(idx) {
                    if (this.items.length > 1) {
                        this.items.splice(idx, 1);
                    }
                }
            };
        }
    </script>
</x-app-layout>
