<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-slate-900 leading-tight">
                {{ __('Tambah Program Kerja') }}
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Daftarkan rencana kegiatan kepengurusan organisasi mahasiswa untuk periode aktif berjalan.
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('proker.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <x-input-label for="nama_proker" :value="__('Nama Program Kerja')" />
                            <x-text-input id="nama_proker" name="nama_proker" type="text" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="rencana_pelaksanaan" :value="__('Rencana Pelaksanaan')" />
                            <x-text-input id="rencana_pelaksanaan" name="rencana_pelaksanaan" type="date" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="deskripsi" :value="__('Deskripsi Singkat')" />
                            <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end mt-6">
                        <x-primary-button>Simpan Program Kerja</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
