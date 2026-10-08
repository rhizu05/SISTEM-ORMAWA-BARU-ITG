<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Inventaris Barang') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showAddModal: false, showEditModal: false, editBarang: { id: null, nama_barang: '', stok_tersedia: 0, status_aktif: 1, boleh_dibawa_keluar: 1 } }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Daftar Barang Inventaris</h3>
                    <p class="text-sm text-slate-500">Kelola stok ketersediaan aset logistik & peralatan yang dapat dipinjam</p>
                </div>
                <button @click="showAddModal = true" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    + Tambah Barang
                </button>
            </div>

            <x-table>
                <x-table.thead>
                    <tr>
                        <x-table.th>Nama Barang</x-table.th>
                        <x-table.th align="center">Stok Tersedia</x-table.th>
                        <x-table.th align="center">Status</x-table.th>
                        <x-table.th align="center">Boleh Dibawa Keluar</x-table.th>
                        <x-table.th align="center">Aksi</x-table.th>
                    </tr>
                </x-table.thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($barangs as $barang)
                    <x-table.tr>
                        <x-table.td>
                            <span class="font-semibold text-slate-900">{{ $barang->nama_barang }}</span>
                        </x-table.td>
                        <x-table.td align="center">
                            <span class="inline-flex items-center justify-center font-mono font-bold text-sm px-2.5 py-0.5 rounded-md {{ $barang->stok_tersedia > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                {{ $barang->stok_tersedia }}
                            </span>
                        </x-table.td>
                        <x-table.td align="center">
                            @if($barang->status_aktif)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">Nonaktif</span>
                            @endif
                        </x-table.td>
                        <x-table.td align="center">
                            @if($barang->boleh_dibawa_keluar)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">Boleh</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">Tidak Boleh (Di Tempat)</span>
                            @endif
                        </x-table.td>
                        <x-table.td align="center">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="showEditModal = true; editBarang = {{ json_encode($barang) }}" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">Edit</button>
                                
                                <form action="{{ route('sarpras.barang.destroy', $barang) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors" onclick="return confirm('Yakin ingin menghapus barang ini?')">Hapus</button>
                                </form>
                            </div>
                        </x-table.td>
                    </x-table.tr>
                    @empty
                    <x-table.empty colspan="5" message="Belum ada barang inventaris." />
                    @endforelse
                </tbody>
            </x-table>
            
            <div class="mt-4">
                {{ $barangs->links() }}
            </div>
        </div>

        <!-- Add Modal -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showAddModal = false"></div>
                <div class="relative bg-white w-full max-w-md p-6 rounded-lg shadow-xl">
                    <h3 class="text-lg font-bold mb-4">Tambah Barang Inventaris</h3>
                    <form action="{{ route('sarpras.barang.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="nama_barang" value="Nama Barang" />
                                <x-text-input id="nama_barang" name="nama_barang" type="text" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <x-input-label for="stok_tersedia" value="Stok Awal" />
                                <x-text-input id="stok_tersedia" name="stok_tersedia" type="number" class="mt-1 block w-full" value="0" required min="0" />
                            </div>
                            <div class="flex items-center mt-4">
                                <input id="status_aktif" name="status_aktif" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" checked value="1">
                                <label for="status_aktif" class="ml-2 text-sm text-gray-600">Aktif (Dapat dipinjam)</label>
                            </div>
                            <div class="flex items-center mt-2">
                                <input id="boleh_dibawa_keluar" name="boleh_dibawa_keluar" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" checked value="1">
                                <label for="boleh_dibawa_keluar" class="ml-2 text-sm text-gray-600">Boleh dibawa keluar kampus</label>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showAddModal = false" class="px-4 py-2 border rounded text-gray-600">Batal</button>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="showEditModal = false"></div>
                <div class="relative bg-white w-full max-w-md p-6 rounded-lg shadow-xl">
                    <h3 class="text-lg font-bold mb-4">Edit Barang Inventaris</h3>
                    <form :action="'/sarpras/barang/' + editBarang?.id" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="edit_nama_barang" value="Nama Barang" />
                                <x-text-input id="edit_nama_barang" name="nama_barang" type="text" class="mt-1 block w-full" x-model="editBarang.nama_barang" required />
                            </div>
                            <div>
                                <x-input-label for="edit_stok_tersedia" value="Stok" />
                                <x-text-input id="edit_stok_tersedia" name="stok_tersedia" type="number" class="mt-1 block w-full" x-model="editBarang.stok_tersedia" required min="0" />
                            </div>
                            <div class="flex items-center mt-4">
                                <input id="edit_status_aktif" name="status_aktif" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" :checked="editBarang?.status_aktif" value="1">
                                <label for="edit_status_aktif" class="ml-2 text-sm text-gray-600">Aktif (Dapat dipinjam)</label>
                            </div>
                            <div class="flex items-center mt-2">
                                <input id="edit_boleh_dibawa_keluar" name="boleh_dibawa_keluar" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" :checked="editBarang?.boleh_dibawa_keluar" value="1">
                                <label for="edit_boleh_dibawa_keluar" class="ml-2 text-sm text-gray-600">Boleh dibawa keluar kampus</label>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-slate-300 rounded-md text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-md font-semibold text-xs text-white uppercase tracking-wider transition-colors shadow-sm">
                                Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>