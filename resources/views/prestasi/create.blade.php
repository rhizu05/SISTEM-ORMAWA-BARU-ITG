<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Laporkan Prestasi / Kompetisi') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc pl-5 text-sm">
                            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('prestasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="nama_kegiatan" :value="__('Nama Kegiatan / Lomba')" />
                        <x-text-input id="nama_kegiatan" name="nama_kegiatan" type="text" class="mt-1 block w-full" :value="old('nama_kegiatan')" required />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="penyelenggara" :value="__('Penyelenggara')" />
                            <x-text-input id="penyelenggara" name="penyelenggara" type="text" class="mt-1 block w-full" :value="old('penyelenggara')" />
                        </div>
                        <div>
                            <x-input-label for="tingkat" :value="__('Tingkat')" />
                            <select name="tingkat" id="tingkat" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                @foreach(\App\Models\Prestasi::TINGKAT as $t)
                                    <option value="{{ $t }}" {{ old('tingkat') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="juara" :value="__('Juara / Capaian')" />
                            <x-text-input id="juara" name="juara" type="text" class="mt-1 block w-full" :value="old('juara')" placeholder="Cth: Juara 1" />
                        </div>
                        <div>
                            <x-input-label for="tanggal" :value="__('Tanggal')" />
                            <x-text-input id="tanggal" name="tanggal" type="date" class="mt-1 block w-full" :value="old('tanggal', date('Y-m-d'))" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="afiliasi" :value="__('Afiliasi')" />
                            <select name="afiliasi" id="afiliasi" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                @foreach(\App\Models\Prestasi::AFILIASI as $a)
                                    <option value="{{ $a }}" {{ old('afiliasi', 'individu') === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="unit_terkait" :value="__('Unit Terkait (opsional)')" />
                            <x-text-input id="unit_terkait" name="unit_terkait" type="text" class="mt-1 block w-full" :value="old('unit_terkait')" placeholder="Cth: Prodi Informatika" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="deskripsi" :value="__('Deskripsi (opsional)')" />
                        <textarea id="deskripsi" name="deskripsi" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div>
                        <x-input-label for="file_bukti" :value="__('Bukti (PDF/JPG/PNG, maks 5MB)')" />
                        <input id="file_bukti" name="file_bukti" type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full border border-gray-300 rounded p-2" required />
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('prestasi.index') }}" class="underline text-sm text-gray-600 self-center">Batal</a>
                        <x-primary-button>Kirim Laporan</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
