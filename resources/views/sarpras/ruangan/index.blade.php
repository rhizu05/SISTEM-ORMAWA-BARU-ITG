<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Master Ruangan') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showAddModal: false, showEditModal: false, editRuangan: { id: null, nama_ruangan: '', kapasitas: 0, status_aktif: 1 } }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Daftar Ruangan Kampus</h3>
                    <p class="text-sm text-slate-500">Kelola master ruangan, aula, dan laboratorium yang dapat dipinjam oleh Ormawa</p>
                </div>
                <button @click="showAddModal = true" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    + Tambah Ruangan
                </button>
            </div>

            <x-table>
                <x-table.thead>
                    <tr>
                        <x-table.th>Nama Ruangan</x-table.th>
                        <x-table.th align="center">Kapasitas (Orang)</x-table.th>
                        <x-table.th align="center">Status</x-table.th>
                        <x-table.th align="center">Aksi</x-table.th>
                    </tr>
                </x-table.thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ruangans as $ruangan)
                    <x-table.tr>
                        <x-table.td>
                            <span class="font-semibold text-slate-900">{{ $ruangan->nama_ruangan }}</span>
                        </x-table.td>
                        <x-table.td align="center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $ruangan->kapasitas }} Mahasiswa
                            </span>
                        </x-table.td>
                        <x-table.td align="center">
                            @if($ruangan->status_aktif)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Tersedia</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200">Nonaktif / Renovasi</span>
                            @endif
                        </x-table.td>
                        <x-table.td align="center">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="showEditModal = true; editRuangan = {{ json_encode($ruangan) }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">Edit</button>
                                
                                <form action="{{ route('sarpras.ruangan.destroy', $ruangan) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors" onclick="return confirm('Yakin ingin menghapus ruangan ini?')">Hapus</button>
                                </form>
                            </div>
                        </x-table.td>
                    </x-table.tr>
                    @empty
                    <x-table.empty colspan="4" message="Belum ada data ruangan terdaftar." />
                    @endforelse
                </tbody>
            </x-table>
            
            <div class="mt-4">
                {{ $ruangans->links() }}
            </div>
        </div>

        <!-- Add Ruangan Modal -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="showAddModal = false"></div>
                <div class="bg-white rounded-lg shadow-xl z-10 max-w-md w-full p-6">
                    <h3 class="text-lg font-bold mb-4">Tambah Ruangan Baru</h3>
                    <form action="{{ route('sarpras.ruangan.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Ruangan</label>
                            <input type="text" name="nama_ruangan" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Contoh: Aula Gedung Rektorat atau Lab Komputer 3">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Kapasitas (Orang)</label>
                            <input type="number" name="kapasitas" min="1" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="100">
                        </div>
                        <div class="mb-4 flex items-center">
                            <input type="checkbox" name="status_aktif" id="status_aktif_add" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <label for="status_aktif_add" class="ml-2 block text-sm text-gray-900">Ruangan Aktif / Tersedia untuk Dipinjam</label>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" @click="showAddModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Batal</button>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Ruangan Modal -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50" @click="showEditModal = false"></div>
                <div class="bg-white rounded-lg shadow-xl z-10 max-w-md w-full p-6">
                    <h3 class="text-lg font-bold mb-4">Edit Data Ruangan</h3>
                    <form :action="'/sarpras/ruangan/' + editRuangan?.id" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Ruangan</label>
                            <input type="text" name="nama_ruangan" :value="editRuangan?.nama_ruangan" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Kapasitas (Orang)</label>
                            <input type="number" name="kapasitas" :value="editRuangan?.kapasitas" min="1" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4 flex items-center">
                            <input type="checkbox" name="status_aktif" id="status_aktif_edit" value="1" :checked="editRuangan?.status_aktif" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <label for="status_aktif_edit" class="ml-2 block text-sm text-gray-900">Ruangan Aktif / Tersedia untuk Dipinjam</label>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-slate-300 rounded-md text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md shadow-sm transition-colors text-xs uppercase tracking-wider">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
