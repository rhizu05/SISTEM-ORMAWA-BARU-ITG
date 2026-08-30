<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload LPJ: ') }} {{ $pengajuan->nama_kegiatan }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 max-w-2xl mx-auto">
                    
                    <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Pastikan laporan pertanggungjawaban (LPJ) beserta bukti transaksi (kwitansi) dan dokumentasi kegiatan telah digabungkan menjadi <strong>satu file PDF</strong> sebelum diunggah.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('lpj.store', $pengajuan) }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Informasi Readonly -->
                        <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded">
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Dana Dicairkan</p>
                                <p class="font-bold text-green-600">Rp {{ number_format($pengajuan->dana->nominal_cair ?? $pengajuan->dana_diajukan, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Tanggal Cair</p>
                                <p class="font-bold">{{ \Carbon\Carbon::parse($pengajuan->dana->tanggal_cair ?? now())->format('d F Y') }}</p>
                            </div>
                        </div>

                        <!-- File LPJ -->
                        <div class="mb-6">
                            <x-input-label for="file_lpj" :value="__('File Dokumen LPJ Lengkap (PDF, Maks 10MB)')" />
                            <input id="file_lpj" class="block mt-1 w-full border border-gray-300 rounded p-2" type="file" name="file_lpj" accept=".pdf" required />
                            <x-input-error :messages="$errors->get('file_lpj')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4" href="{{ route('lpj.index') }}">
                                Batal
                            </a>
                            <x-primary-button>
                                {{ __('Upload & Ajukan Verifikasi LPJ') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>