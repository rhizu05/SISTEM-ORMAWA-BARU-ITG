<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    {{ __('Monitoring Laporan Pertanggungjawaban (LPJ)') }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Daftar kegiatan yang telah dicairkan dan membutuhkan pelaporan pertanggungjawaban dana kegiatan.
                </p>
            </div>
            @if(isset($pengajuans))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-xs font-bold tracking-wide self-start sm:self-auto shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    {{ $pengajuans->total() }} Kegiatan
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @php $isVerifikator = Auth::user()->hasAnyRole(['admin', 'bkhm', 'wr3', 'bpm']); @endphp

            {{-- Main Table Card --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] overflow-hidden">
                <div class="overflow-x-auto skin-scrollbar">
                    <table class="min-w-full divide-y divide-slate-100 text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/90 border-b border-slate-200/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider select-none">
                                @if($isVerifikator)
                                    <th scope="col" class="py-3.5 px-5">Ormawa Pengusul</th>
                                @endif
                                <th scope="col" class="py-3.5 px-5">Nama Kegiatan</th>
                                <th scope="col" class="py-3.5 px-5">Tanggal Cair</th>
                                <th scope="col" class="py-3.5 px-5">Dana Terealisasi</th>
                                <th scope="col" class="py-3.5 px-5 text-center">Status LPJ</th>
                                <th scope="col" class="py-3.5 px-5 text-center">Dokumen Berkas</th>
                                <th scope="col" class="py-3.5 px-5 text-center">Aksi Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white text-sm">
                            @forelse ($pengajuans as $pengajuan)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    {{-- Kolom Ormawa (Khusus Verifikator) --}}
                                    @if($isVerifikator)
                                        <td class="py-4 px-5 align-middle">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#1E40AF] border border-blue-200/60 flex items-center justify-center font-bold text-xs shrink-0 select-none">
                                                    {{ strtoupper(substr($pengajuan->user->name ?? 'O', 0, 2)) }}
                                                </div>
                                                <div class="font-bold text-slate-800 text-sm truncate max-w-[160px]" title="{{ $pengajuan->user->name }}">
                                                    {{ $pengajuan->user->name ?? '-' }}
                                                </div>
                                            </div>
                                        </td>
                                    @endif

                                    {{-- Kolom Nama Kegiatan --}}
                                    <td class="py-4 px-5 align-middle">
                                        <div class="font-bold text-slate-900 hover:text-blue-700 transition">
                                            {{ $pengajuan->nama_kegiatan }}
                                        </div>
                                        @if($pengajuan->programKerja)
                                            <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                                                <span>📋</span>
                                                <span class="truncate max-w-xs">{{ $pengajuan->programKerja->nama_proker }}</span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Kolom Tanggal Cair --}}
                                    <td class="py-4 px-5 align-middle text-xs whitespace-nowrap">
                                        @if($pengajuan->dana && $pengajuan->dana->tanggal_cair)
                                            <div class="font-bold text-slate-800">
                                                {{ \Carbon\Carbon::parse($pengajuan->dana->tanggal_cair)->isoFormat('D MMMM Y') }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">Sudah Dicairkan</div>
                                        @else
                                            <span class="text-slate-400 italic">-</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Dana Cair --}}
                                    <td class="py-4 px-5 align-middle whitespace-nowrap">
                                        <div class="font-extrabold text-emerald-600 text-sm tracking-tight font-mono">
                                            Rp {{ number_format($pengajuan->dana->nominal_cair ?? $pengajuan->dana_diajukan, 0, ',', '.') }}
                                        </div>
                                    </td>

                                    {{-- Kolom Status LPJ --}}
                                    <td class="py-4 px-5 align-middle text-center whitespace-nowrap">
                                        @php
                                            $chipClass = 'bg-slate-50 text-slate-700 border-slate-200';
                                            $dotClass = 'bg-slate-400';
                                            $statusText = $pengajuan->state->label;

                                            if ($pengajuan->state->name === 'funds_disbursed') {
                                                if ($pengajuan->file_lpj) {
                                                    $chipClass = 'bg-amber-50 text-amber-800 border-amber-200/90';
                                                    $dotClass = 'bg-amber-500 animate-pulse';
                                                    $statusText = 'Perlu Revisi LPJ';
                                                } else {
                                                    $chipClass = 'bg-rose-50 text-rose-700 border-rose-200/90';
                                                    $dotClass = 'bg-rose-500 animate-pulse';
                                                    $statusText = 'Belum Upload';
                                                }
                                            } elseif ($pengajuan->state->name === 'completed') {
                                                $chipClass = 'bg-emerald-50 text-emerald-700 border-emerald-200/90';
                                                $dotClass = 'bg-emerald-500';
                                            } elseif ($pengajuan->state->name === 'lpj_submitted') {
                                                $chipClass = 'bg-blue-50 text-blue-700 border-blue-200/90';
                                                $dotClass = 'bg-blue-600 animate-pulse';
                                            } else {
                                                $chipClass = 'bg-amber-50 text-amber-800 border-amber-200/90';
                                                $dotClass = 'bg-amber-500';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border shadow-2xs {{ $chipClass }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                            {{ $statusText }}
                                        </span>
                                    </td>

                                    {{-- Kolom Berkas LPJ --}}
                                    <td class="py-4 px-5 align-middle text-center whitespace-nowrap">
                                        @if($pengajuan->file_lpj)
                                            <a href="{{ route('dokumen.lpj', $pengajuan) }}" target="_blank" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 border border-blue-200/80 text-[#1E40AF] rounded-xl text-xs font-bold shadow-2xs transition active:scale-95">
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span>Lihat LPJ</span>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-400 italic">
                                                <span>Belum ada</span>
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Kolom Aksi --}}
                                    <td class="py-4 px-5 align-middle text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            @if($pengajuan->state->name === 'funds_disbursed' && $pengajuan->user_id === Auth::id())
                                                <a href="{{ route('lpj.create', $pengajuan) }}" 
                                                   class="inline-flex items-center gap-1 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition active:scale-95">
                                                    <svg class="w-3.5 h-3.5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                    <span>{{ $pengajuan->file_lpj ? 'Revisi LPJ' : 'Upload LPJ' }}</span>
                                                </a>
                                            @elseif($isVerifikator)
                                                <a href="{{ route('verifikasi.show', $pengajuan) }}" 
                                                   class="inline-flex items-center gap-1 px-3.5 py-1.5 bg-[#0B1528] hover:bg-[#1E3A8A] text-white text-xs font-bold rounded-xl shadow-xs transition active:scale-95">
                                                    @if(($pengajuan->state->name === 'lpj_submitted' && Auth::user()->hasRole('bkhm')) || ($pengajuan->state->name === 'lpj_wr3_review' && Auth::user()->hasRole('wr3')))
                                                        <span>Verifikasi LPJ</span>
                                                    @else
                                                        <span>Detail</span>
                                                    @endif
                                                    <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </a>
                                                
                                                @if($pengajuan->state->name === 'funds_disbursed' && Auth::user()->hasAnyRole(['bpm', 'admin']))
                                                    <a href="{{ route('bpm.sp.create') }}?target_id={{ $pengajuan->user_id }}&perihal={{ urlencode('Peringatan Keterlambatan LPJ: ' . $pengajuan->nama_kegiatan) }}&alasan={{ urlencode('Keterlambatan pengumpulan LPJ kegiatan ' . $pengajuan->nama_kegiatan) }}" 
                                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 text-xs font-bold rounded-xl shadow-2xs transition active:scale-95" 
                                                       title="Terbitkan Surat Peringatan">
                                                        <span>Terbitkan SP</span>
                                                    </a>
                                                @endif
                                            @else
                                                <a href="{{ route('pengajuan.show', $pengajuan) }}" 
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-slate-200/90 hover:border-blue-400 hover:text-blue-700 text-slate-700 text-xs font-bold rounded-xl shadow-2xs transition active:scale-95">
                                                    <span>Detail</span>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isVerifikator ? 7 : 6 }}" class="py-16 px-4 text-center">
                                        <div class="w-14 h-14 mx-auto mb-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-center text-slate-400 shadow-2xs">
                                            <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-bold text-slate-800">Tidak Ada Data Kegiatan LPJ</h3>
                                        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                                            Belum ada kegiatan yang telah dicairkan dan membutuhkan pelaporan LPJ saat ini.
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