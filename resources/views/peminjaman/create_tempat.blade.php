<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajukan Peminjaman Ruangan') }}
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

                    <form method="POST" action="{{ route('peminjaman.tempat.store') }}">
                        @csrf

                        <!-- Ruangan -->
                        <div class="mb-4">
                            <x-input-label for="ruangan_id" :value="__('Pilih Ruangan')" />
                            <select id="ruangan_id" name="ruangan_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($ruangans as $ruang)
                                    <option value="{{ $ruang->id }}" {{ old('ruangan_id') == $ruang->id ? 'selected' : '' }}>
                                        {{ $ruang->nama_ruangan }} (Kapasitas: {{ $ruang->kapasitas }} orang)
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('ruangan_id')" class="mt-2" />
                        </div>

                        <!-- Nama Kegiatan -->
                        <div class="mb-4">
                            <x-input-label for="nama_kegiatan" :value="__('Nama Kegiatan')" />
                            <x-text-input id="nama_kegiatan" class="block mt-1 w-full" type="text" name="nama_kegiatan" :value="old('nama_kegiatan')" required />
                            <x-input-error :messages="$errors->get('nama_kegiatan')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <!-- Tanggal Mulai -->
                            <div>
                                <x-input-label for="tgl_mulai" :value="__('Tanggal Mulai')" />
                                <x-text-input id="tgl_mulai" class="block mt-1 w-full" type="date" name="tgl_mulai" :value="old('tgl_mulai', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('tgl_mulai')" class="mt-2" />
                            </div>
                            
                            <!-- Tanggal Selesai -->
                            <div>
                                <x-input-label for="tgl_selesai" :value="__('Tanggal Selesai')" />
                                <x-text-input id="tgl_selesai" class="block mt-1 w-full" type="date" name="tgl_selesai" :value="old('tgl_selesai', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('tgl_selesai')" class="mt-2" />
                            </div>

                            <!-- Jam Mulai -->
                            <div>
                                <x-input-label for="jam_mulai" :value="__('Jam Mulai')" />
                                <x-text-input id="jam_mulai" class="block mt-1 w-full" type="time" name="jam_mulai" :value="old('jam_mulai')" required />
                                <x-input-error :messages="$errors->get('jam_mulai')" class="mt-2" />
                            </div>
                            
                            <!-- Jam Selesai -->
                            <div>
                                <x-input-label for="jam_selesai" :value="__('Jam Selesai')" />
                                <x-text-input id="jam_selesai" class="block mt-1 w-full" type="time" name="jam_selesai" :value="old('jam_selesai')" required />
                                <x-input-error :messages="$errors->get('jam_selesai')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-6">
                            <x-input-label for="deskripsi_kegiatan" :value="__('Deskripsi Kegiatan Singkat')" />
                            <textarea id="deskripsi_kegiatan" name="deskripsi_kegiatan" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('deskripsi_kegiatan') }}</textarea>
                            <x-input-error :messages="$errors->get('deskripsi_kegiatan')" class="mt-2" />
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