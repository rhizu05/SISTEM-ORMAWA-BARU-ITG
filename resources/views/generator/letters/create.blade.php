<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Surat Digital') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('generator.letters.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-input-label for="type" :value="__('Jenis Surat')" />
                            <select id="type" name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="undangan">Surat Undangan</option>
                                <option value="tugas">Surat Tugas / Mandat</option>
                                <option value="permohonan">Surat Permohonan (Alat/Tempat)</option>
                                <option value="keterangan_aktif">Surat Keterangan Aktif</option>
                            </select>
                        </div>

                        <div>
                            <x-input-label for="perihal" :value="__('Perihal')" />
                            <x-text-input id="perihal" name="perihal" type="text" class="mt-1 block w-full" required placeholder="Contoh: Undangan Rapat Koordinasi" />
                        </div>

                        <div id="dynamic-fields" class="grid grid-cols-2 gap-4">
                            <!-- Fields will be handled by JS based on type -->
                            <div>
                                <x-input-label for="tujuan" :value="__('Tujuan / Penerima')" />
                                <x-text-input id="tujuan" name="tujuan" type="text" class="mt-1 block w-full" placeholder="Nama/Instansi Tujuan" />
                            </div>
                            <div>
                                <x-input-label for="tanggal_acara" :value="__('Tanggal/Waktu')" />
                                <x-text-input id="tanggal_acara" name="tanggal_acara" type="text" class="mt-1 block w-full" placeholder="Contoh: 30 Agustus 2026, 10:00 WIB" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="content" :value="__('Isi Surat')" />
                            <textarea id="content" name="content" rows="10" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Tuliskan detail isi surat di sini..."></textarea>
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>Generate Surat</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
