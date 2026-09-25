<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Verifikasi Pengajuan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Ormawa</th>
                                    <th class="py-2 px-4 border-b text-left">Kegiatan</th>
                                    <th class="py-2 px-4 border-b text-left">Tanggal</th>
                                    <th class="py-2 px-4 border-b text-left">Dana</th>
                                    <th class="py-2 px-4 border-b text-center">Status</th>
                                    <th class="py-2 px-4 border-b text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pengajuans as $pengajuan)
                                @php
                                    $urgensi = $pengajuan->statusUrgensi();
                                    $isDiingatkan = $pengajuan->terakhir_diingatkan_at && $pengajuan->terakhir_diingatkan_at->diffInHours(now()) < 48;
                                    $rowClass = '';
                                    if ($urgensi && $urgensi['is_urgent']) {
                                        $rowClass = 'bg-rose-50/70 border-l-4 border-l-rose-500';
                                    } elseif ($isDiingatkan) {
                                        $rowClass = 'bg-amber-50/50 border-l-4 border-l-amber-500';
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50/80 transition-colors {{ $rowClass }}">
                                    <td class="py-2.5 px-4 border-b font-semibold text-gray-800">{{ $pengajuan->user->name }}</td>
                                    <td class="py-2.5 px-4 border-b">
                                        <div class="font-medium text-gray-900">{{ $pengajuan->nama_kegiatan }}</div>
                                        <div class="flex items-center flex-wrap gap-1.5 mt-1">
                                            @if($urgensi)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] {{ $urgensi['badge_class'] }}">
                                                    ⏱️ {{ $urgensi['label'] }}
                                                </span>
                                            @endif
                                            @if($pengajuan->programKerja)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                    📋 {{ $pengajuan->programKerja->nama_proker }}
                                                </span>
                                            @endif
                                            @if($isDiingatkan)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                                    🔔 Diingatkan ({{ $pengajuan->terakhir_diingatkan_at->diffForHumans() }})
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-2.5 px-4 border-b text-xs">
                                        @if($pengajuan->tanggal_mulai_kegiatan)
                                            <div class="font-semibold text-gray-900">
                                                {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai_kegiatan)->format('d/m/Y') }}
                                            </div>
                                            <div class="text-[10px] text-gray-400">Diajukan: {{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d/m/Y') }}</div>
                                        @else
                                            <div class="text-gray-700">{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d/m/Y') }}</div>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 border-b font-semibold text-gray-900">Rp {{ number_format($pengajuan->dana_diajukan, 0, ',', '.') }}</td>
                                    <td class="py-2.5 px-4 border-b text-center">
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 border rounded-full text-xs font-semibold">
                                            {{ $pengajuan->state->label }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-4 border-b text-center">
                                        <a href="{{ route('verifikasi.show', $pengajuan) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold border border-indigo-600 px-3 py-1 rounded inline-block hover:bg-indigo-50">Verifikasi</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pengajuan</h3>
                                        <p class="mt-1 text-sm text-gray-500">Saat ini tidak ada pengajuan yang membutuhkan verifikasi Anda.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $pengajuans->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>