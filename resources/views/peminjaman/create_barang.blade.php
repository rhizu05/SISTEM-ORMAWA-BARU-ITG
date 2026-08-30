<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajukan Peminjaman Barang Inventaris') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 max-w-2xl mx-auto">
                    
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('peminjaman.barang.store') }}">
                        @csrf

                        <!-- Nama Kegiatan -->
                        <div class="mb-4">
                            <x-input-label for="nama_kegiatan" :value="__('Nama Kegiatan')" />
                            <x-text-input id="nama_kegiatan" class="block mt-1 w-full" type="text" name="nama_kegiatan" :value="old('nama_kegiatan')" required />
                            <x-input-error :messages="$errors->get('nama_kegiatan')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <!-- Tanggal Mulai -->
                            <div>
                                <x-input-label for="tgl_mulai" :value="__('Tanggal Mulai Pinjam')" />
                                <x-text-input id="tgl_mulai" class="block mt-1 w-full" type="date" name="tgl_mulai" :value="old('tgl_mulai', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('tgl_mulai')" class="mt-2" />
                            </div>
                            
                            <!-- Tanggal Selesai -->
                            <div>
                                <x-input-label for="tgl_selesai" :value="__('Tanggal Kembali')" />
                                <x-text-input id="tgl_selesai" class="block mt-1 w-full" type="date" name="tgl_selesai" :value="old('tgl_selesai', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('tgl_selesai')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Pilih Barang -->
                        <div class="mb-6">
                            <h3 class="text-lg font-bold mb-2">Pilih Barang:</h3>
                            <div class="space-y-3 bg-gray-50 p-4 border rounded">
                                @forelse($barangs as $i => $barang)
                                    <div class="flex items-center justify-between">
                                        <label class="flex items-center w-2/3">
                                            <input type="hidden" name="barang_id[{{$i}}]" value="{{ $barang->id }}">
                                            <span class="ml-2">{{ $barang->nama_barang }} <small class="text-gray-500">(Stok: {{ $barang->stok_tersedia }})</small></span>
                                        </label>
                                        <div class="w-1/3 text-right">
                                            <input type="number" name="qty[{{$i}}]" min="0" max="{{ $barang->stok_tersedia }}" value="0" class="border-gray-300 rounded w-20 px-2 py-1 text-right">
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-red-500 text-sm">Tidak ada barang yang tersedia untuk dipinjam saat ini.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 mr-4" href="{{ route('peminjaman.index') }}">Batal</a>
                            <x-primary-button>Ajukan Peminjaman</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>