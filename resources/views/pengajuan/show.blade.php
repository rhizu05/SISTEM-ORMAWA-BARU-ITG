<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pengajuan: ') }} {{ $pengajuan->nama_kegiatan }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Detail Card -->
            <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold">Informasi Pengajuan</h3>
                        @if(in_array($pengajuan->state->name, ['draft', 'rejected']))
                            <a href="{{ route('pengajuan.edit', $pengajuan) }}" class="text-sm bg-yellow-100 text-yellow-800 hover:bg-yellow-200 px-3 py-1 rounded font-semibold border border-yellow-300">
                                Edit / Revisi
                            </a>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Kode Unik</p>
                            <p class="font-semibold font-mono">{{ $pengajuan->unique_code }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status Saat Ini</p>
                            <p class="font-semibold text-indigo-600">{{ $pengajuan->state->label }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Kegiatan</p>
                            <p class="font-semibold">{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Dana Diajukan</p>
                            <p class="font-semibold">Rp {{ number_format($pengajuan->dana_diajukan, 0, ',', '.') }}</p>
                        </div>
                        @if($pengajuan->nomor_surat)
                        <div class="col-span-2">
                            <p class="text-sm text-gray-500">Nomor Surat Resmi</p>
                            <p class="font-semibold">{{ $pengajuan->nomor_surat }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-500 mb-1">File Proposal</p>
                        <a href="{{ asset('storage/' . $pengajuan->file_proposal) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Lihat Dokumen PDF
                        </a>
                    </div>

                    @if($pengajuan->state->name === 'draft')
                    <div class="mt-8 pt-4 border-t flex justify-end">
                        <form action="{{ route('pengajuan.ajukan', $pengajuan) }}" method="POST">
                            @csrf
                            <x-primary-button onclick="return confirm('Kirim pengajuan ke BEM sekarang? Pastikan data sudah benar.')">
                                Ajukan ke BEM
                            </x-primary-button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            <!-- History Timeline -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-fit">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Riwayat & Status PIC</h3>
                    
                    @if($pengajuan->state && $pengajuan->state->pic_role)
                    <div class="mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-lg text-xs">
                        <span class="font-bold text-indigo-800 uppercase block mb-1">Status PIC Saat Ini:</span>
                        <p class="text-indigo-900 font-semibold">{{ $pengajuan->state->pic_role }}</p>
                        <p class="text-indigo-700 mt-0.5">Kontak / Pihak terkait: <span class="font-medium">{{ $pengajuan->state->pic_contact }}</span></p>
                    </div>
                    @endif

                    <div class="relative border-l border-gray-200 ml-3 space-y-4">
                        @foreach($pengajuan->histori as $history)
                        <div class="mb-4 ml-4">
                            <div class="absolute w-3 h-3 bg-indigo-500 rounded-full -left-1.5 border border-white mt-1.5"></div>
                            <time class="mb-1 text-xs font-normal text-gray-400">{{ $history->created_at->format('d/m/Y H:i') }}</time>
                            <h4 class="text-sm font-semibold text-gray-900">{{ $history->state->label ?? 'Status' }}</h4>
                            @if($history->state && $history->state->pic_role)
                            <p class="text-[11px] text-indigo-600 font-medium">PIC: {{ $history->state->pic_role }} ({{ $history->state->pic_contact }})</p>
                            @endif
                            <p class="text-xs text-gray-500 mb-1">Oleh: {{ $history->user->name ?? 'Sistem' }}</p>
                            @if($history->catatan)
                            <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded border mt-1">{{ $history->catatan }}</p>
                            @endif
                            @if($history->catatan_kendala)
                            <p class="text-sm text-red-700 bg-red-50 p-2 rounded border border-red-200 mt-1"><strong>Kendala / Catatan Revisi:</strong> {{ $history->catatan_kendala }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>