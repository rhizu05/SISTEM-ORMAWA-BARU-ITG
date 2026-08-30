<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Inventaris Barang') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showAddModal: false, showEditModal: false, editBarang: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold">Daftar Barang Inventaris</h3>
                        <button @click="showAddModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            + Tambah Barang
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Barang</th>
                                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Stok Tersedia</th>
                                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($barangs as $barang)
                                <tr>
                                    <td class="py-3 px-4 font-semibold">{{ $barang->nama_barang }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="text-lg {{ $barang->stok_tersedia > 0 ? 'text-green-600' : 'text-red-600 font-bold' }}">
                                            {{ $barang->stok_tersedia }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($barang->status_aktif)
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Aktif</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center space-x-2">
                                        <button @click="showEditModal = true; editBarang = {{ json_encode($barang) }}" class="text-blue-600 hover:text-blue-900 text-sm">Edit</button>
                                        
                                        <form action="{{ route('sarpras.barang.destroy', $barang) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('Yakin ingin menghapus barang ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-gray-500">Belum ada barang inventaris.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $barangs->links() }}
                    </div>
                </div>
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
                                <input id="status_aktif" name="status_aktif" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" checked>
                                <label for="status_aktif" class="ml-2 text-sm text-gray-600">Aktif (Dapat dipinjam)</label>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showAddModal = false" class="px-4 py-2 border rounded text-gray-600">Batal</button>
                            <x-primary-button>Simpan</x-primary-button>
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
                                <input id="edit_status_aktif" name="status_aktif" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" :checked="editBarang?.status_aktif">
                                <label for="edit_status_aktif" class="ml-2 text-sm text-gray-600">Aktif (Dapat dipinjam)</label>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 border rounded text-gray-600">Batal</button>
                            <x-primary-button>Update</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>