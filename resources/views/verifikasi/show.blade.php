<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Proses Verifikasi: ') }} {{ $pengajuan->nama_kegiatan }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Detail Card -->
            <div class="md:col-span-2 space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Informasi Pengajuan</h3>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <p class="text-sm text-gray-500">Ormawa / Pengaju</p>
                                <p class="font-semibold">{{ $pengajuan->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status Saat Ini</p>
                                <p class="font-semibold text-indigo-600">{{ $pengajuan->state->label }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Dana Diajukan</p>
                                <p class="font-semibold text-xl text-green-600">Rp {{ number_format($pengajuan->dana_diajukan, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Kode Unik</p>
                                <p class="font-semibold font-mono">{{ $pengajuan->unique_code }}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Dokumen Proposal</p>
                            <iframe src="{{ Storage::url($pengajuan->file_proposal) }}" class="w-full h-96 border rounded" frameborder="0"></iframe>
                            <div class="mt-2 text-right">
                                <a href="{{ Storage::url($pengajuan->file_proposal) }}" target="_blank" class="text-sm text-indigo-600 hover:underline">Buka di tab baru &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                @if($availableTransitions->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-indigo-200">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4">Aksi Verifikasi</h3>
                        
                        <form action="{{ route('verifikasi.process', $pengajuan) }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <x-input-label for="catatan" :value="__('Catatan (Opsional, wajib jika revisi/ditolak)')" />
                                <textarea id="catatan" name="catatan" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" placeholder="Tuliskan catatan untuk ormawa atau pemeriksa selanjutnya..."></textarea>
                            </div>

                            <div class="flex gap-4 border-t pt-4">
                                @foreach($availableTransitions as $transition)
                                    @php
                                        $btnClass = 'bg-gray-800 hover:bg-gray-700'; // Default
                                        $label = strtolower($transition->action_label);
                                        if (str_contains($label, 'setuju') || str_contains($label, 'cair')) {
                                            $btnClass = 'bg-green-600 hover:bg-green-700';
                                        } elseif (str_contains($label, 'tolak')) {
                                            $btnClass = 'bg-red-600 hover:bg-red-700';
                                        } elseif (str_contains($label, 'revisi')) {
                                            $btnClass = 'bg-yellow-500 hover:bg-yellow-600 text-black';
                                        }
                                    @endphp
                                    <button type="submit" name="transition_id" value="{{ $transition->id }}" 
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 {{ $btnClass }}"
                                            onclick="return confirm('Anda yakin ingin melakukan aksi: {{ $transition->action_label }}?')">
                                        {{ $transition->action_label }}
                                    </button>
                                @endforeach
                            </div>
                        </form>
                    </div>
                </div>
                @endif
            </div>

            <!-- History Timeline -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-fit">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Riwayat Status</h3>
                    
                    <div class="relative border-l border-gray-200 ml-3 space-y-4">
                        @foreach($pengajuan->histori as $history)
                        <div class="mb-4 ml-4">
                            <div class="absolute w-3 h-3 bg-indigo-500 rounded-full -left-1.5 border border-white mt-1.5"></div>
                            <time class="mb-1 text-xs font-normal text-gray-400">{{ $history->created_at->format('d/m/Y H:i') }}</time>
                            <h4 class="text-sm font-semibold text-gray-900">{{ $history->state->label }}</h4>
                            <p class="text-xs text-gray-500 mb-1">Oleh: {{ $history->user->name }}</p>
                            @if($history->catatan)
                            <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded border mt-1">{{ $history->catatan }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>