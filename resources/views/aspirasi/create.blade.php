<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Kirim Aspirasi & Suara Mahasiswa</h2></x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-6 text-center">
                    <h3 class="text-lg font-bold">Sampaikan Suaramu</h3>
                    <p class="text-sm text-gray-500">Aspirasi Anda akan dikelola oleh BPM untuk kemajuan organisasi mahasiswa.</p>
                </div>
                <form action="{{ route('aspirasi.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-6">
                        <div class="flex items-center gap-2 p-3 bg-gray-50 rounded border">
                            <input type="checkbox" name="anonim" id="anonim" class="rounded border-gray-300">
                            <label for="anonim" class="text-sm font-medium text-gray-700">Kirim sebagai Anonim (Nama tidak akan ditampilkan)</label>
                        </div>
                        <div>
                            <x-input-label for="judul" :value="__('Judul Aspirasi')" />
                            <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" required placeholder="Contoh: Keluhan Fasilitas Aula" />
                        </div>
                        <div>
                            <x-input-label for="kategori" :value="__('Kategori')" />
                            <select name="kategori" id="kategori" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="Fasilitas">Fasilitas</option>
                                <option value="Pelayanan">Pelayanan</option>
                                <option value="Kebijakan">Kebijakan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="isi" :value="__('Isi Aspirasi / Keluhan')" />
                            <textarea id="isi" name="isi" rows="6" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Tuliskan aspirasi Anda secara detail..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end mt-6">
                        <x-primary-button>Kirim Aspirasi</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
