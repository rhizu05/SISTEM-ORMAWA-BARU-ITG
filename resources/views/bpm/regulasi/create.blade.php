<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Terbitkan Regulasi/Pengumuman</h2></x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('bpm.regulasi.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-input-label for="judul" :value="__('Judul')" />
                            <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" required placeholder="Contoh: UU Ormawa No 1 2024" />
                        </div>
                        <div>
                            <x-input-label for="kategori" :value="__('Kategori')" />
                            <select id="kategori" name="kategori" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="Undang-Undang">Undang-Undang</option>
                                <option value="Pengumuman">Pengumuman</option>
                                <option value="Pedoman">Pedoman</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="deskripsi" :value="__('Isi / Keterangan Singkat')" />
                            <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Jelaskan isi singkat regulasi..."></textarea>
                        </div>
                        <div>
                            <x-input-label for="file" :value="__('File Dokumen (PDF/Gambar)')" />
                            <input id="file" name="file" type="file" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required accept=".pdf,.jpg,.jpeg,.png" />
                        </div>
                        <div>
                            <x-input-label for="tanggal_terbit" :value="__('Tanggal Terbit')" />
                            <x-text-input id="tanggal_terbit" name="tanggal_terbit" type="date" class="mt-1 block w-full" required value="{{ date('Y-m-d') }}" />
                        </div>
                    </div>
                    <div class="flex justify-end mt-6">
                        <a href="{{ route('bpm.regulasi.index') }}" class="mr-4 text-sm text-gray-600 underline">Batal</a>
                        <x-primary-button>Terbitkan Sekarang</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
