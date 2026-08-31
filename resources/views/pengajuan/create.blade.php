<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Pengajuan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(isset($blocking) && $blocking)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded shadow mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0"><svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg></div>
                        <div class="ml-3">
                            <h3 class="text-sm font-bold text-yellow-800">Pengajuan Ditangguhkan</h3>
                            <p class="text-sm text-yellow-700 mt-1">Anda belum bisa mengajukan proposal baru karena masih ada proposal/LPJ yang belum selesai diproses atau ditolak.</p>
                            <div class="mt-3 text-sm bg-white p-3 rounded border">
                                <div>Nama Kegiatan: <span class="font-semibold">{{ $blocking->nama_kegiatan }}</span></div>
                                <div>Status Saat Ini: <span class="font-semibold">{{ $blocking->state->label ?? $blocking->state->name }}</span></div>
                            </div>
                            <div class="mt-4"><a href="{{ route('pengajuan.show', $blocking) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded text-sm">Lihat Pengajuan</a> <a href="{{ route('pengajuan.index') }}" class="ml-2 text-sm text-yellow-800 underline">Ke Riwayat</a></div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 @if(isset($blocking) && $blocking) opacity-50 pointer-events-none @endif">
                        <form method="POST" action="{{ route('pengajuan.store') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="space-y-6">
                                <div class="mb-4">
                                    <x-input-label for="nama_kegiatan" :value="__('Nama Kegiatan')" />
                                    <x-text-input id="nama_kegiatan" class="block mt-1 w-full" type="text" name="nama_kegiatan" :value="old('nama_kegiatan')" required autofocus :disabled="isset($blocking) && $blocking" />
                                    <x-input-error :messages="$errors->get('nama_kegiatan')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="dana_diajukan" :value="__('Dana Diajukan (Rp)')" />
                                    <x-text-input id="dana_diajukan" class="block mt-1 w-full" type="number" name="dana_diajukan" :value="old('dana_diajukan')" required min="0" :disabled="isset($blocking) && $blocking" />
                                    <x-input-error :messages="$errors->get('dana_diajukan')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="tanggal_pengajuan" :value="__('Tanggal Pengajuan')" />
                                    <x-text-input id="tanggal_pengajuan" class="block mt-1 w-full" type="date" name="tanggal_pengajuan" :value="old('tanggal_pengajuan', date('Y-m-d'))" required :disabled="isset($blocking) && $blocking" />
                                    <x-input-error :messages="$errors->get('tanggal_pengajuan')" class="mt-2" />
                                </div>

                                <div class="mb-6">
                                    <x-input-label for="file_proposal" :value="__('File Proposal (PDF, maks 5MB)')" />
                                    <input id="file_proposal" class="block mt-1 w-full border border-gray-300 rounded p-2" type="file" name="file_proposal" accept=".pdf" required @if(isset($blocking) && $blocking) disabled @endif />
                                    <x-input-error :messages="$errors->get('file_proposal')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4" href="{{ route('pengajuan.index') }}">
                                    Batal
                                </a>
                                <x-primary-button :disabled="isset($blocking) && $blocking">
                                    {{ __('Simpan sebagai Draft') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Financial Info -->
                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Sisa Saldo Anda</h3>
                        <p class="text-3xl font-bold text-green-600">Rp {{ number_format(Auth::user()->saldo, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-2 italic">*Pastikan dana yang diajukan tidak melebihi saldo tersedia.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
