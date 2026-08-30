<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Sistem') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.konfigurasi.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Kolom Kiri: Info Umum -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-bold border-b pb-2">Informasi Umum</h3>
                                
                                <div>
                                    <x-input-label for="nama_aplikasi" value="Nama Aplikasi / Sistem" />
                                    <x-text-input id="nama_aplikasi" name="nama_aplikasi" type="text" class="mt-1 block w-full" value="{{ $konfigurasis['nama_aplikasi'] ?? '' }}" required />
                                </div>

                                <div>
                                    <x-input-label for="logo_sistem" value="Logo Sistem (Opsional, PNG/JPG max 2MB)" />
                                    @if(isset($konfigurasis['logo_sistem']) && $konfigurasis['logo_sistem'])
                                        <div class="my-2">
                                            <img src="{{ Storage::url($konfigurasis['logo_sistem']) }}" alt="Logo" class="h-16 object-contain">
                                        </div>
                                    @endif
                                    <input id="logo_sistem" name="logo_sistem" type="file" class="mt-1 block w-full border border-gray-300 rounded p-1" accept="image/*" />
                                </div>
                            </div>

                            <!-- Kolom Kanan: Kop Surat -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-bold border-b pb-2">Kop Surat Resmi</h3>
                                
                                <div>
                                    <x-input-label for="kop_baris1" value="Kop Surat - Baris 1 (Instansi Induk)" />
                                    <x-text-input id="kop_baris1" name="kop_baris1" type="text" class="mt-1 block w-full" value="{{ $konfigurasis['kop_baris1'] ?? '' }}" />
                                </div>
                                
                                <div>
                                    <x-input-label for="kop_baris2" value="Kop Surat - Baris 2 (Nama Institusi)" />
                                    <x-text-input id="kop_baris2" name="kop_baris2" type="text" class="mt-1 block w-full font-bold" value="{{ $konfigurasis['kop_baris2'] ?? '' }}" />
                                </div>

                                <div>
                                    <x-input-label for="kop_baris3" value="Kop Surat - Baris 3 (Alamat)" />
                                    <x-text-input id="kop_baris3" name="kop_baris3" type="text" class="mt-1 block w-full text-sm" value="{{ $konfigurasis['kop_baris3'] ?? '' }}" />
                                </div>

                                <div>
                                    <x-input-label for="kop_baris4" value="Kop Surat - Baris 4 (Kontak/Web)" />
                                    <x-text-input id="kop_baris4" name="kop_baris4" type="text" class="mt-1 block w-full text-sm" value="{{ $konfigurasis['kop_baris4'] ?? '' }}" />
                                </div>

                                <div>
                                    <x-input-label for="kop_logo" value="Logo Kop Surat (Opsional, PNG/JPG max 2MB)" />
                                    @if(isset($konfigurasis['kop_logo']) && $konfigurasis['kop_logo'])
                                        <div class="my-2">
                                            <img src="{{ Storage::url($konfigurasis['kop_logo']) }}" alt="Logo Kop" class="h-16 object-contain bg-gray-100 p-1">
                                        </div>
                                    @endif
                                    <input id="kop_logo" name="kop_logo" type="file" class="mt-1 block w-full border border-gray-300 rounded p-1" accept="image/*" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 border-t pt-4">
                            <x-primary-button>
                                {{ __('Simpan Pengaturan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>