<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">{{ __('Laporkan Prestasi / Kompetisi') }}</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Catat prestasi dan capaian kompetisi mahasiswa untuk pendataan kemahasiswaan ITG.</p>
        </div>
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

                    <div>
                        <x-input-label for="url_penyelenggara" :value="__('Tautan / Website Resmi Penyelenggara (opsional)')" />
                        <x-text-input id="url_penyelenggara" name="url_penyelenggara" type="url" class="mt-1 block w-full" :value="old('url_penyelenggara')" placeholder="https://..." />
                        <span class="text-xs text-gray-500 mt-1 block">Tautan rujukan untuk verifikasi pelaporan Dikti / Simkatmawa.</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="juara" :value="__('Juara / Capaian')" />
                            <x-text-input id="juara" name="juara" type="text" class="mt-1 block w-full" :value="old('juara')" placeholder="Cth: Juara 1" />
                        </div>
                        <div>
                            <x-input-label for="unit_terkait" :value="__('Unit Terkait (opsional)')" />
                            <x-text-input id="unit_terkait" name="unit_terkait" type="text" class="mt-1 block w-full" :value="old('unit_terkait')" placeholder="Cth: Prodi Informatika" />
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
                            <x-input-label for="tanggal_mulai" :value="__('Tanggal Mulai Pelaksanaan')" />
                            <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" class="mt-1 block w-full" :value="old('tanggal_mulai', old('tanggal', date('Y-m-d')))" required />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="tanggal_selesai" :value="__('Tanggal Selesai Pelaksanaan (opsional)')" />
                        <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="mt-1 block w-full" :value="old('tanggal_selesai')" />
                        <span class="text-xs text-gray-500 mt-1 block">Kosongkan bila kompetisi hanya berlangsung satu hari.</span>
                    </div>

                    <div>
                        <x-input-label for="deskripsi" :value="__('Deskripsi (opsional)')" />
                        <textarea id="deskripsi" name="deskripsi" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="file_bukti" :value="__('Scan Sertifikat / Piagam (Wajib)')" />
                            <input id="file_bukti" name="file_bukti" type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full border border-gray-300 rounded p-2 text-sm" required />
                            <span class="text-xs text-gray-500 mt-0.5 block">Format: PDF, JPG, PNG (maks 5MB).</span>
                        </div>
                        <div>
                            <x-input-label for="foto_penyerahan" :value="__('Foto Penyerahan Medali/Piala (Opsional)')" />
                            <input id="foto_penyerahan" name="foto_penyerahan" type="file" accept=".jpg,.jpeg,.png" class="mt-1 block w-full border border-gray-300 rounded p-2 text-sm" />
                            <span class="text-xs text-gray-500 mt-0.5 block">Format: JPG, PNG (maks 5MB) untuk galeri publik.</span>
                        </div>
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
