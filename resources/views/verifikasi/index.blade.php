<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    {{ __('Daftar Verifikasi Pengajuan') }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Antrean berkas proposal &amp; LPJ ormawa yang membutuhkan peninjauan dan persetujuan Anda.
                </p>
            </div>
            @if(isset($pengajuans) && $pengajuans->total() > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-xs font-bold tracking-wide self-start sm:self-auto shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    {{ $pengajuans->total() }} Menunggu Tindakan
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Main Table Card --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] overflow-hidden">
                <div class="overflow-x-auto skin-scrollbar">
                    <table class="min-w-full divide-y divide-slate-100 text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/90 border-b border-slate-200/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider select-none">
                                <th scope="col" class="py-3.5 px-5">Ormawa Pengusul</th>
                                <th scope="col" class="py-3.5 px-5">Nama Kegiatan &amp; Proker</th>
                                <th scope="col" class="py-3.5 px-5">Jadwal Acara</th>
                                <th scope="col" class="py-3.5 px-5">Dana Diajukan</th>
                                <th scope="col" class="py-3.5 px-5 text-center">Status Alur</th>
                                <th scope="col" class="py-3.5 px-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white text-sm">
                            @forelse ($pengajuans as $pengajuan)
                                @php
                                    $urgensi = $pengajuan->statusUrgensi();
                                    $isDiingatkan = $pengajuan->terakhir_diingatkan_at && $pengajuan->terakhir_diingatkan_at->diffInHours(now()) < 48;
                                    $rowClass = 'hover:bg-slate-50/80 transition-colors';
                                    if ($urgensi && $urgensi['is_urgent']) {
                                        $rowClass = 'bg-rose-50/40 hover:bg-rose-50/70 border-l-4 border-l-rose-500 transition-colors';
                                    } elseif ($isDiingatkan) {
                                        $rowClass = 'bg-amber-50/40 hover:bg-amber-50/70 border-l-4 border-l-amber-500 transition-colors';
                                    }
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    {{-- Kolom Ormawa --}}
                                    <td class="py-4 px-5 align-middle">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#1E40AF] border border-blue-200/60 flex items-center justify-center font-bold text-xs shrink-0 select-none">
                                                {{ strtoupper(substr($pengajuan->user->name ?? 'O', 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-800 text-sm truncate max-w-[180px]" title="{{ $pengajuan->user->name }}">
                                                    {{ $pengajuan->user->name }}
                                                </div>
                                                <div class="text-[11px] text-slate-400 font-mono">
                                                    ID: #{{ $pengajuan->id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom Kegiatan & Proker --}}
                                    <td class="py-4 px-5 align-middle">
                                        <div class="font-semibold text-slate-900 group-hover:text-blue-700 transition">
                                            {{ $pengajuan->nama_kegiatan }}
                                        </div>
                                        <div class="flex items-center flex-wrap gap-1.5 mt-1.5">
                                            @if($urgensi)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold tracking-tight shadow-2xs {{ $urgensi['badge_class'] }}">
                                                    ⏱️ {{ $urgensi['label'] }}
                                                </span>
                                            @endif
                                            @if($pengajuan->programKerja)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/70">
                                                    📋 {{ Str::limit($pengajuan->programKerja->nama_proker, 28) }}
                                                </span>
                                            @endif
                                            @if($isDiingatkan)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                                    🔔 Diingatkan ({{ $pengajuan->terakhir_diingatkan_at->diffForHumans() }})
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Kolom Jadwal --}}
                                    <td class="py-4 px-5 align-middle text-xs whitespace-nowrap">
                                        @if($pengajuan->tanggal_mulai_kegiatan)
                                            <div class="font-bold text-slate-800">
                                                {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai_kegiatan)->isoFormat('D MMMM Y') }}
                                            </div>
                                            <div class="text-[11px] text-slate-400 mt-0.5">
                                                Diajukan: {{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d/m/Y') }}
                                            </div>
                                        @else
                                            <div class="font-medium text-slate-700">
                                                {{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->isoFormat('D MMMM Y') }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">Tgl Pengajuan</div>
                                        @endif
                                    </td>

                                    {{-- Kolom Dana --}}
                                    <td class="py-4 px-5 align-middle whitespace-nowrap">
                                        <div class="font-extrabold text-slate-900 text-sm tracking-tight font-mono">
                                            Rp {{ number_format($pengajuan->dana_diajukan, 0, ',', '.') }}
                                        </div>
                                    </td>

                                    {{-- Kolom Status --}}
                                    <td class="py-4 px-5 align-middle text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200/90 shadow-2xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            {{ $pengajuan->state->label }}
                                        </span>
                                    </td>

                                    {{-- Kolom Aksi --}}
                                    <td class="py-4 px-5 align-middle text-center whitespace-nowrap">
                                        <a href="{{ route('verifikasi.show', $pengajuan) }}" 
                                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#0B1528] hover:bg-[#1E3A8A] text-white text-xs font-bold rounded-xl shadow-xs hover:shadow-md transition active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                                            <span>Verifikasi</span>
                                            <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-16 px-4 text-center">
                                        <div class="w-14 h-14 mx-auto mb-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-center text-slate-400 shadow-2xs">
                                            <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-bold text-slate-800">Semua Antrean Bersih!</h3>
                                        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                                            Saat ini tidak ada proposal atau LPJ yang menunggu tindakan verifikasi Anda.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                @if($pengajuans->hasPages())
                    <div class="px-6 py-4 bg-slate-50/60 border-t border-slate-100">
                        {{ $pengajuans->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>