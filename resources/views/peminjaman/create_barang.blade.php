<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajukan Peminjaman Barang Inventaris') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200/90">
                <div class="p-4 sm:p-6 lg:p-8 text-gray-900 max-w-2xl mx-auto">
                    

                    <form method="POST" action="{{ route('peminjaman.barang.store') }}" enctype="multipart/form-data" x-data="{ submitting: false }" @submit="submitting = true">
                        @csrf

                        <!-- Nama Kegiatan -->
                        <div class="mb-4">
                            <x-input-label for="nama_kegiatan" :value="__('Nama Kegiatan')" />
                            <x-text-input id="nama_kegiatan" class="block mt-1 w-full" type="text" name="nama_kegiatan" :value="old('nama_kegiatan')" required />
                            <x-input-error :messages="$errors->get('nama_kegiatan')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
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
                                            <span class="ml-2">{{ $barang->nama_barang }} <small class="text-gray-500">(Stok: {{ $barang->stok_tersedia }})</small>
                                                @unless($barang->boleh_dibawa_keluar)
                                                    <small class="text-red-600 font-semibold">(tidak boleh dibawa keluar kampus)</small>
                                                @endunless
                                            </span>
                                        </label>
                                        <div class="w-1/3 text-right">
                                            <input type="number" name="qty[{{$i}}]" min="0" max="{{ $barang->stok_tersedia }}" value="0" class="border-gray-300 rounded w-20 px-2 py-1 text-right" @unless($barang->boleh_dibawa_keluar) disabled @endunless>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-red-500 text-sm">Tidak ada barang yang tersedia untuk dipinjam saat ini.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Alur Persetujuan Terpadu BKHM & Sarpras -->
                        <div class="mb-6 p-4 bg-indigo-50 border border-indigo-200 rounded-xl text-xs text-indigo-900 leading-relaxed flex items-start gap-3">
                            <svg class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="font-bold text-indigo-950 mb-0.5">Alur Konfirmasi Peminjaman Terpadu:</p>
                                <p class="text-indigo-800">Pengajuan peminjaman barang inventaris ini akan diverifikasi oleh <strong>Biro Kemahasiswaan (BKHM)</strong> terlebih dahulu. Setelah disetujui BKHM, pengajuan diteruskan otomatis ke <strong>Bagian Sarana &amp; Prasarana (Sarpras)</strong> untuk penyiapan stok dan serah terima barang.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4 gap-3">
                            <a class="min-h-[44px] inline-flex items-center justify-center underline text-sm text-gray-600 hover:text-gray-900 px-3" href="{{ route('peminjaman.index') }}">Batal</a>
                            <x-primary-button class="min-h-[44px]" x-bind:disabled="submitting">
                                <span x-show="!submitting">Ajukan Peminjaman</span>
                                <span x-show="submitting" class="inline-flex items-center gap-2" style="display: none;" x-cloak>
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Memproses...</span>
                                </span>
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>