<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit / Revisi Pengajuan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 max-w-2xl mx-auto">
                    
                    @if($pengajuan->state->name === 'rejected')
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
                            <h3 class="font-bold text-red-700">Catatan Revisi/Penolakan:</h3>
                            <p class="text-red-600 mt-1">
                                @php
                                    $rejectHistory = $pengajuan->histori()->whereHas('state', function($q) {
                                        $q->where('name', 'rejected');
                                    })->latest()->first();
                                @endphp
                                {{ $rejectHistory ? $rejectHistory->catatan : 'Silakan perbaiki proposal Anda dan upload ulang.' }}
                            </p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pengajuan.update', $pengajuan) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Nama Kegiatan -->
                        <div class="mb-4">
                            <x-input-label for="nama_kegiatan" :value="__('Nama Kegiatan')" />
                            <x-text-input id="nama_kegiatan" class="block mt-1 w-full" type="text" name="nama_kegiatan" :value="old('nama_kegiatan', $pengajuan->nama_kegiatan)" required />
                            <x-input-error :messages="$errors->get('nama_kegiatan')" class="mt-2" />
                        </div>

                        <!-- Dana Diajukan -->
                        <div class="mb-4">
                            <x-input-label for="dana_diajukan" :value="__('Dana Diajukan (Rp)')" />
                            <x-text-input id="dana_diajukan" class="block mt-1 w-full" type="number" name="dana_diajukan" :value="old('dana_diajukan', $pengajuan->dana_diajukan)" required min="0" />
                            <x-input-error :messages="$errors->get('dana_diajukan')" class="mt-2" />
                        </div>

                        <!-- Tanggal Pengajuan -->
                        <div class="mb-4">
                            <x-input-label for="tanggal_pengajuan" :value="__('Tanggal Kegiatan')" />
                            <x-text-input id="tanggal_pengajuan" class="block mt-1 w-full" type="date" name="tanggal_pengajuan" :value="old('tanggal_pengajuan', \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('tanggal_pengajuan')" class="mt-2" />
                        </div>

                        <!-- File Proposal -->
                        <div class="mb-6">
                            <x-input-label for="file_proposal" :value="__('File Proposal Baru (PDF, maks 5MB)')" />
                            <p class="text-sm text-gray-500 mb-1">Kosongkan jika tidak ingin mengganti file proposal yang sudah ada.</p>
                            <input id="file_proposal" class="block mt-1 w-full border border-gray-300 rounded p-2" type="file" name="file_proposal" accept=".pdf" />
                            <x-input-error :messages="$errors->get('file_proposal')" class="mt-2" />
                            
                            <div class="mt-2 text-sm">
                                File saat ini: <a href="{{ Storage::url($pengajuan->file_proposal) }}" target="_blank" class="text-indigo-600 hover:underline">Lihat PDF</a>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4" href="{{ route('pengajuan.show', $pengajuan) }}">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>