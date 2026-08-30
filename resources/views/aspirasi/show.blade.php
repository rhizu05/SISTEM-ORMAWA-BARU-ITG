<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Aspirasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-4">
                        <a href="{{ route('aspirasi.index') }}" class="text-indigo-600 hover:text-indigo-900">&larr; Kembali ke Daftar</a>
                    </div>

                    <div class="bg-gray-50 p-4 rounded border mb-6">
                        <p><strong>Pengirim:</strong> {{ $aspirasi->nama_pengirim }} ({{ $aspirasi->email_pengirim ?? '-' }})</p>
                        <p><strong>Tanggal:</strong> {{ $aspirasi->created_at->format('d F Y H:i') }}</p>
                        <p><strong>Status Saat Ini:</strong> 
                            @if($aspirasi->status == 'menunggu')
                                <span class="text-yellow-600 font-semibold">Menunggu</span>
                            @elseif($aspirasi->status == 'ditindaklanjuti')
                                <span class="text-blue-600 font-semibold">Sedang Ditindaklanjuti</span>
                            @else
                                <span class="text-green-600 font-semibold">Selesai</span>
                            @endif
                        </p>
                        
                        <div class="mt-4 border-t pt-4">
                            <h3 class="font-bold mb-2">Isi Aspirasi:</h3>
                            <p class="whitespace-pre-wrap">{{ $aspirasi->isi_aspirasi }}</p>
                        </div>
                    </div>

                    <form action="{{ route('aspirasi.update', $aspirasi) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Update Status')" />
                            <select id="status" name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="menunggu" {{ $aspirasi->status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="ditindaklanjuti" {{ $aspirasi->status == 'ditindaklanjuti' ? 'selected' : '' }}>Ditindaklanjuti</option>
                                <option value="selesai" {{ $aspirasi->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="tanggapan" :value="__('Tanggapan (Opsional)')" />
                            <textarea id="tanggapan" name="tanggapan" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="4">{{ old('tanggapan', $aspirasi->tanggapan) }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <x-primary-button>Simpan Perubahan</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>