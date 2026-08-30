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
                    <div class="mb-12 p-6 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <div class="flex items-center mb-6">
                            <svg class="w-6 h-6 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.538 3.538M9 11l3 3m-3 3l3-3m-3 3l-3-3m7 8a4 4 0 100-8 4 4 0 018 0z" /></svg>
                            <h3 class="text-lg font-bold text-gray-800">Penandatangan (Pilih dari Profil)</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="p-4 bg-white rounded shadow-sm border">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanda Tangan 1 (Ketua Pelaksana)</label>
                                <select name="ttd_1_role" class="w-full border-gray-300 rounded-sm text-sm">
                                    <option value="ketua">Ketua</option>
                                    <option value="sekretaris">Sekretaris</option>
                                    <option value="bendahara">Bendahara</option>
                                </select>
                            </div>
                            <div class="p-4 bg-white rounded shadow-sm border">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanda Tangan 2 (Sekretaris)</label>
                                <select name="ttd_2_role" class="w-full border-gray-300 rounded-sm text-sm">
                                    <option value="sekretaris" selected>Sekretaris</option>
                                    <option value="ketua">Ketua</option>
                                    <option value="bendahara">Bendahara</option>
                                </select>
                            </div>
                            <div class="p-4 bg-white rounded shadow-sm border">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanda Tangan 3 (Mengetahui)</label>
                                <select name="ttd_3_role" class="w-full border-gray-300 rounded-sm text-sm">
                                    <option value="ketua">Saran: Ketua</option>
                                    <option value="sekretaris">Sekretaris</option>
                                    <option value="bendahara">Bendahara</option>
                                </select>
                            </div>
                        </div>
                        <p class="mt-4 text-xs text-gray-500 italic">Data TTD diambil dari menu Profil. Pastikan sudah mengunggah TTD transparan di sana.</p>
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
