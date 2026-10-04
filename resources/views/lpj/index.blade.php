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
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] p-6 space-y-4">
                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            @if($isVerifikator)
                                <x-table.th>Ormawa Pengusul</x-table.th>
                            @endif
                            <x-table.th>Nama Kegiatan</x-table.th>
                            <x-table.th>Tanggal Cair</x-table.th>
                            <x-table.th>Dana Terealisasi</x-table.th>
                            <x-table.th align="center">Status LPJ</x-table.th>
                            <x-table.th align="center">Dokumen Berkas</x-table.th>
                            <x-table.th align="center">Aksi Tindakan</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-sm">
                        @forelse ($pengajuans as $pengajuan)
                            <x-table.tr>
                                {{-- Kolom Ormawa (Khusus Verifikator) --}}
                                @if($isVerifikator)
                                    <x-table.td>
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#1E40AF] border border-blue-200/60 flex items-center justify-center font-bold text-xs shrink-0 select-none">
                                                {{ strtoupper(substr($pengajuan->user->name ?? 'O', 0, 2)) }}
                                            </div>
                                            <div class="font-bold text-slate-800 text-sm truncate max-w-[160px]" title="{{ $pengajuan->user->name }}">
                                                {{ $pengajuan->user->name ?? '-' }}
                                            </div>
                                        </div>
                                    </x-table.td>
                                @endif

                                {{-- Kolom Nama Kegiatan --}}
                                <x-table.td>
                                    <div class="font-bold text-slate-900 hover:text-blue-700 transition">
                                        {{ $pengajuan->nama_kegiatan }}
                                    </div>
                                    @if($pengajuan->programKerja)
                                        <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                                            <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <span class="truncate max-w-xs">{{ $pengajuan->programKerja->nama_proker }}</span>
                                        </div>
                                    @endif
                                </x-table.td>

                                {{-- Kolom Tanggal Cair --}}
                                <x-table.td class="text-xs whitespace-nowrap">
                                    @if($pengajuan->dana && $pengajuan->dana->tanggal_cair)
                                        <div class="font-bold text-slate-800">
                                            {{ \Carbon\Carbon::parse($pengajuan->dana->tanggal_cair)->isoFormat('D MMMM Y') }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">Sudah Dicairkan</div>
                                    @else
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </x-table.td>

                                {{-- Kolom Dana Cair --}}
                                <x-table.td class="whitespace-nowrap">
                                    <div class="font-extrabold text-emerald-600 text-sm tracking-tight font-mono">
                                        Rp {{ number_format($pengajuan->dana->nominal_cair ?? $pengajuan->dana_diajukan, 0, ',', '.') }}
                                    </div>
                                </x-table.td>

                                {{-- Kolom Status LPJ --}}
                                <x-table.td align="center" class="whitespace-nowrap">
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
                                            $statusText = 'Selesai & Dilegalisir';
                                        } elseif ($pengajuan->state->name === 'lpj_submitted') {
                                            $chipClass = 'bg-blue-50 text-blue-700 border-blue-200/90';
                                            $dotClass = 'bg-blue-600 animate-pulse';
                                            $statusText = 'Review BKHM';
                                        } elseif ($pengajuan->state->name === 'lpj_wr3_review') {
                                            $chipClass = 'bg-purple-50 text-purple-700 border-purple-200/90';
                                            $dotClass = 'bg-purple-600 animate-pulse';
                                            $statusText = 'Verifikasi WR3';
                                        } else {
                                            $chipClass = 'bg-amber-50 text-amber-800 border-amber-200/90';
                                            $dotClass = 'bg-amber-500';
                                        }

                                        $hasTtdOrmawa = (bool) $pengajuan->tandaTanganLpjOrmawa();
                                        $hasTtdBkhm = (bool) $pengajuan->tandaTanganLpjBkhm();
                                        $hasTtdWr3 = (bool) $pengajuan->tandaTanganLpjWr3();
                                    @endphp
                                    <div class="flex flex-col items-center gap-1.5">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border shadow-2xs {{ $chipClass }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                            {{ $statusText }}
                                        </span>

                                        @if($pengajuan->file_lpj || in_array($pengajuan->state->name, ['lpj_submitted', 'lpj_wr3_review', 'completed']))
                                        <div class="inline-flex items-center gap-1 text-[10px] text-slate-500 font-medium bg-slate-100/80 px-2 py-0.5 rounded-md border border-slate-200/60" title="Status Legalisir TTD Digital">
                                            <span class="font-semibold text-slate-600">TTD:</span>
                                            <span class="{{ $hasTtdOrmawa ? 'text-emerald-700 font-bold' : 'text-slate-400' }}" title="Ormawa (Pelapor)">Ormawa {!! $hasTtdOrmawa ? '&#10003;' : '&#8943;' !!}</span>
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="{{ $hasTtdBkhm ? 'text-emerald-700 font-bold' : 'text-slate-400' }}" title="BKHM (Konfirmasi)">BKHM {!! $hasTtdBkhm ? '&#10003;' : '&#8943;' !!}</span>
                                            <span class="text-slate-300">&bull;</span>
                                            <span class="{{ $hasTtdWr3 ? 'text-emerald-700 font-bold' : 'text-slate-400' }}" title="WR3 (Pengesahan Akhir)">WR3 {!! $hasTtdWr3 ? '&#10003;' : '&#8943;' !!}</span>
                                        </div>
                                        @endif
                                    </div>
                                </x-table.td>

                                {{-- Kolom Berkas LPJ --}}
                                <x-table.td align="center" class="whitespace-nowrap">
                                    @if($pengajuan->file_lpj)
                                        <div class="flex flex-col items-center gap-1">
                                            <a href="{{ route('dokumen.lpj', $pengajuan) }}" target="_blank" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 border border-blue-200/80 text-[#1E40AF] rounded-xl text-xs font-bold shadow-2xs transition active:scale-95" title="Buka Dokumen Lengkap Terlegalisir TTD Digital">
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span>Lihat LPJ</span>
                                            </a>
                                            <a href="{{ route('dokumen.lpj', ['pengajuan' => $pengajuan, 'mode' => 'pengesahan']) }}" target="_blank" 
                                               class="text-[10px] text-purple-700 hover:text-purple-900 hover:underline font-semibold flex items-center gap-0.5" title="Lihat Lembar Pengesahan Saja">
                                                <span>Lembar Pengesahan</span>
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs text-slate-400 italic">
                                            <span>Belum ada</span>
                                        </span>
                                    @endif
                                </x-table.td>

                                {{-- Kolom Aksi --}}
                                <x-table.td align="center" class="whitespace-nowrap">
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
                                </x-table.td>
                            </x-table.tr>
                        @empty
                            <x-table.empty :colspan="$isVerifikator ? 7 : 6" message="Belum ada kegiatan yang telah dicairkan dan membutuhkan pelaporan LPJ saat ini." />
                        @endforelse
                    </tbody>
                </x-table>

                {{-- Pagination Footer --}}
                @if($pengajuans->hasPages())
                    <div class="pt-2">
                        {{ $pengajuans->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>