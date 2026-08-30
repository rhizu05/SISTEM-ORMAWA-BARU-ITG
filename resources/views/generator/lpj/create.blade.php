<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat LPJ Otomatis') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                
                <form action="{{ route('generator.lpj.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="proposal_id" value="{{ $proposal->id ?? '' }}">

                    <!-- BATANG TUBUH LPJ -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6 pb-2 border-b-2 border-indigo-500">
                            <span class="bg-indigo-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3">1</span>
                            <h3 class="text-xl font-bold text-gray-800">Batang Tubuh LPJ</h3>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-input-label for="nama_kegiatan" :value="__('Nama Kegiatan')" />
                                <x-text-input id="nama_kegiatan" name="nama_kegiatan" type="text" class="mt-1 block w-full" required placeholder="Contoh: Malam Keakraban HIMATIF 2024" value="{{ $proposal->nama_kegiatan ?? '' }}" />
                            </div>
                            <div>
                                <x-input-label for="pendahuluan" :value="__('Pendahuluan / Latar Belakang')" />
                                <textarea id="pendahuluan" name="pendahuluan" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Jelaskan secara singkat mengenai terlaksananya kegiatan ini..."></textarea>
                            </div>
                            <div>
                                <x-input-label for="waktu_tempat" :value="__('Waktu & Tempat Pelaksanaan')" />
                                <textarea id="waktu_tempat" name="waktu_tempat" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Contoh: Sabtu, 15 Mei 2024 di Villa Garut. Dihadiri oleh 100 peserta."></textarea>
                            </div>
                            <div>
                                <x-input-label for="hasil_kegiatan" :value="__('Hasil Kegiatan')" />
                                <textarea id="hasil_kegiatan" name="hasil_kegiatan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Apa saja capaian dari kegiatan ini?"></textarea>
                            </div>
                            <div>
                                <x-input-label for="hambatan" :value="__('Hambatan & Kendala')" />
                                <textarea id="hambatan" name="hambatan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Sebutkan kendala teknis atau non-teknis yang dihadapi..."></textarea>
                            </div>
                            <div>
                                <x-input-label for="saran" :value="__('Saran & Rekomendasi')" />
                                <textarea id="saran" name="saran" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Saran untuk panitia di masa mendatang..."></textarea>
                            </div>
                            <div>
                                <x-input-label for="penutup" :value="__('Penutup')" />
                                <textarea id="penutup" name="penutup" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Demikian laporan pertanggungjawaban ini kami buat sebagai bahan evaluasi."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- LAPORAN REALISASI DANA -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6 pb-2 border-b-2 border-indigo-500">
                            <span class="bg-indigo-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3">2</span>
                            <h3 class="text-xl font-bold text-gray-800">Laporan Realisasi Dana</h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border">
                                <thead class="bg-gray-50">
                                    <tr class="text-xs font-medium text-gray-500 uppercase">
                                        <th class="px-4 py-3 text-left">Uraian</th>
                                        <th class="px-4 py-3 text-right w-40">Estimasi (Rp)</th>
                                        <th class="px-4 py-3 text-right w-40">Realisasi (Rp)</th>
                                        <th class="px-4 py-3 text-left">Keterangan</th>
                                        <th class="px-4 py-3 text-center w-16">#</th>
                                    </tr>
                                </thead>
                                <tbody id="realisasi-body">
                                    <tr class="border-b">
                                        <td class="p-2"><input type="text" name="realisasi_items[0][uraian]" class="w-full border-gray-300 rounded-sm" placeholder="Contoh: Konsumsi"></td>
                                        <td class="p-2"><input type="number" name="realisasi_items[0][estimasi]" class="w-full text-right border-gray-300 rounded-sm"></td>
                                        <td class="p-2"><input type="number" name="realisasi_items[0][realisasi]" class="w-full text-right border-gray-300 rounded-sm"></td>
                                        <td class="p-2"><input type="text" name="realisasi_items[0][keterangan]" class="w-full border-gray-300 rounded-sm"></td>
                                        <td class="p-2 text-center">
                                            <button type="button" onclick="this.closest('tr').remove()" class="text-red-500">&times;</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" onclick="addRow()" class="mt-4 text-sm text-indigo-600 font-semibold hover:text-indigo-800">+ Tambah Baris Realisasi</button>
                        </div>
                    </div>

                    <!-- LAMPIRAN BUKTI & DOKUMENTASI -->
                    <div class="mb-12">
                        <div class="flex items-center mb-6 pb-2 border-b-2 border-indigo-500">
                            <span class="bg-indigo-500 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold mr-3">3</span>
                            <h3 class="text-xl font-bold text-gray-800">Lampiran Bukti & Dokumentasi</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                <x-input-label for="bukti_pembayaran" :value="__('Upload Kwitansi / Bukti Pembayaran')" />
                                <input id="bukti_pembayaran" name="bukti_pembayaran[]" type="file" multiple accept="image/*,.pdf" class="mt-2 block w-full text-sm" />
                                <p class="mt-1 text-xs text-gray-500">Bisa pilih banyak file sekaligus (Gambar/PDF).</p>
                            </div>
                            <div class="p-4 bg-gray-500 rounded-lg border border-dashed border-gray-300">
                                <x-input-label for="foto_dokumentasi" :value="__('Upload Foto Dokumentasi Kegiatan')" />
                                <input id="foto_dokumentasi" name="foto_dokumentasi[]" type="file" multiple accept="image/*" class="mt-2 block w-full text-sm" />
                                <p class="mt-1 text-xs text-gray-500">Bisa pilih banyak foto kegiatan sekaligus.</p>
                            </div>
                        </div>
                    </div>

                    <!-- TANDA TANGAN -->
                    <div class="mb-12 p-6 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <div class="flex items-center mb-6">
                            <svg class="w-6 h-6 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.538 3.538M9 11l3 3m-3 3l3-3m-3 3l-3-3m7 8a4 4 0 100-8 4 4 0 018 0z" /></svg>
                            <h3 class="text-lg font-bold text-gray-800">Tanda Tangan</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="p-4 bg-white rounded shadow-sm border">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanda Tangan 1 (Ketua)</label>
                                <select name="ttd_1" class="w-full border-gray-300 rounded-sm text-sm">
                                    <option value="ketua">Ketua</option>
                                    <option value="sekretaris">Sekretaris</option>
                                    <option value="bendahara">Bendahara</option>
                                </select>
                            </div>
                            <div class="p-4 bg-white rounded shadow-sm border">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanda Tangan 2 (Sekretaris)</label>
                                <select name="ttd_2" class="w-full border-gray-300 rounded-sm text-sm">
                                    <option value="sekretaris" selected>Sekretaris</option>
                                    <option value="ketua">Ketua</option>
                                    <option value="bendahara">Bendahara</option>
                                </select>
                            </div>
                            <div class="p-4 bg-white rounded shadow-sm border">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanda Tangan 3 (Bendahara)</label>
                                <select name="ttd_3" class="w-full border-gray-300 rounded-sm text-sm">
                                    <option value="bendahara">Bendahara</option>
                                    <option value="ketua">Ketua</option>
                                    <option value="sekretaris">Sekretaris</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 mt-8">
                        <x-secondary-button type="submit" name="action" value="draft" class="px-6 py-2">
                            Simpan Draft
                        </x-secondary-button>
                        <x-primary-button type="submit" name="action" value="print" class="px-8 py-3 text-lg">
                            Simpan & Cetak LPJ
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let rowIdx = 1;
        function addRow() {
            const tbody = document.getElementById('realisasi-body');
            const row = `
                <tr class="border-b">
                    <td class="p-2"><input type="text" name="realisasi_items[${rowIdx}][uraian]" class="w-full border-gray-300 rounded-sm"></td>
                    <td class="p-2"><input type="number" name="realisasi_items[${rowIdx}][estimasi]" class="w-full text-right border-gray-300 rounded-sm"></td>
                    <td class="p-2"><input type="number" name="realisasi_items[${rowIdx}][realisasi]" class="w-full text-right border-gray-300 rounded-sm"></td>
                    <td class="p-2"><input type="text" name="realisasi_items[${rowIdx}][keterangan]" class="w-full border-gray-300 rounded-sm"></td>
                    <td class="p-2 text-center">
                        <button type="button" onclick="this.closest('tr').remove()" class="text-red-500">&times;</button>
                    </td>
                </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
            rowIdx++;
        }
    </script>
</x-app-layout>
