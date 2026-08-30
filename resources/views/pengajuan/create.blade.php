<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Pengajuan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 max-w-2xl mx-auto">
                    
                    <form method="POST" action="{{ route('pengajuan.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Nama Kegiatan -->
                        <div class="mb-4">
                            <x-input-label for="nama_kegiatan" :value="__('Nama Kegiatan')" />
                            <x-text-input id="nama_kegiatan" class="block mt-1 w-full" type="text" name="nama_kegiatan" :value="old('nama_kegiatan')" required autofocus />
                            <x-input-error :messages="$errors->get('nama_kegiatan')" class="mt-2" />
                        </div>

                        <!-- Dana Diajukan -->
                        <div class="mb-4">
                            <x-input-label for="dana_diajukan" :value="__('Dana Diajukan (Rp)')" />
                            <x-text-input id="dana_diajukan" class="block mt-1 w-full" type="number" name="dana_diajukan" :value="old('dana_diajukan')" required min="0" />
                            <x-input-error :messages="$errors->get('dana_diajukan')" class="mt-2" />
                        </div>

                        <!-- Tanggal Pengajuan -->
                        <div class="mb-4">
                            <x-input-label for="tanggal_pengajuan" :value="__('Tanggal Pengajuan')" />
                            <x-text-input id="tanggal_pengajuan" class="block mt-1 w-full" type="date" name="tanggal_pengajuan" :value="old('tanggal_pengajuan', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('tanggal_pengajuan')" class="mt-2" />
                        </div>

                        <!-- File Proposal -->
                        <div class="mb-6">
                            <x-input-label for="file_proposal" :value="__('File Proposal (PDF, maks 5MB)')" />
                            <input id="file_proposal" class="block mt-1 w-full border border-gray-300 rounded p-2" type="file" name="file_proposal" accept=".pdf" required />
                            <x-input-error :messages="$errors->get('file_proposal')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4" href="{{ route('pengajuan.index') }}">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Simpan sebagai Draft') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>