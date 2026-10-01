<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    {{ __('Daftar Pengajuan Anggaran & Proposal') }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Kelola dan pantau seluruh riwayat proposal kegiatan ormawa beserta status alur verifikasinya.
                </p>
            </div>
            @if(isset($pengajuans))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-xs font-bold tracking-wide self-start sm:self-auto shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    Total {{ $pengajuans->total() }} Proposal
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Toolbar: Tombol Tambah & Form Filter --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-[0_4px_20px_rgba(0,0,0,0.02)] flex flex-col md:flex-row md:items-center justify-between gap-4">
                <a href="{{ route('pengajuan.create') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#0B1528] hover:bg-[#1E3A8A] text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs hover:shadow-md transition active:scale-95 shrink-0">
                    <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Buat Pengajuan Baru</span>
                </a>

                {{-- Filter & Pencarian --}}
                <form method="GET" action="{{ route('pengajuan.index') }}" class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
                    <div class="relative flex-1 sm:w-60">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama kegiatan..." 
                               class="w-full bg-slate-50/80 border border-slate-200/90 rounded-xl text-xs sm:text-sm py-2 pl-3 pr-8 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition shadow-2xs" />
                        @if(request('q'))
                            <a href="{{ route('pengajuan.index', ['status' => request('status')]) }}" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-slate-600 text-xs">✕</a>
                        @endif
                    </div>

                    <div class="flex items-center gap-1.5">
                        <select name="status" id="status" class="bg-slate-50/80 border border-slate-200/90 rounded-xl text-xs sm:text-sm py-2 px-3 focus:bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition shadow-2xs" onchange="this.form.submit()">
                            <option value="">Semua Status Alur</option>
                            @foreach($states as $state)
                                <option value="{{ $state->name }}" {{ request('status') === $state->name ? 'selected' : '' }}>
                                    {{ $state->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl border border-slate-200/80 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Cari</span>
                    </button>

                    @if(request('q') || request('status'))
                        <a href="{{ route('pengajuan.index') }}" class="px-3 py-2 text-xs font-bold text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition border border-rose-200">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Main Table Card --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] overflow-hidden">
                <div class="overflow-x-auto skin-scrollbar">
                    <table class="min-w-full divide-y divide-slate-100 text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/90 border-b border-slate-200/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider select-none">
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
                                    $rowClass = 'hover:bg-slate-50/80 transition-colors';
                                    if ($urgensi && $urgensi['is_urgent']) {
                                        $rowClass = 'bg-rose-50/40 hover:bg-rose-50/70 border-l-4 border-l-rose-500 transition-colors';
                                    }
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    {{-- Kolom Kegiatan & Proker --}}
                                    <td class="py-4 px-5 align-middle">
                                        <div class="font-bold text-slate-900 hover:text-blue-700 transition">
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
                                                    📋 {{ Str::limit($pengajuan->programKerja->nama_proker, 30) }}
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
                                        @php
                                            $chipClass = 'bg-slate-50 text-slate-700 border-slate-200';
                                            $dotClass = 'bg-slate-400';
                                            if ($pengajuan->state->name === 'rejected') {
                                                $chipClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                                $dotClass = 'bg-rose-500';
                                            } elseif ($pengajuan->state->name === 'completed') {
                                                $chipClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                                $dotClass = 'bg-emerald-500';
                                            } elseif ($pengajuan->state->name === 'draft') {
                                                $chipClass = 'bg-slate-100 text-slate-600 border-slate-200';
                                                $dotClass = 'bg-slate-400';
                                            } else {
                                                $chipClass = 'bg-amber-50 text-amber-800 border-amber-200';
                                                $dotClass = 'bg-amber-500 animate-pulse';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border shadow-2xs {{ $chipClass }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                            {{ $pengajuan->state->label }}
                                        </span>
                                    </td>

                                    {{-- Kolom Aksi --}}
                                    <td class="py-4 px-5 align-middle text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('pengajuan.show', $pengajuan) }}" 
                                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-slate-200/90 hover:border-blue-400 hover:text-blue-700 text-slate-700 text-xs font-bold rounded-xl shadow-2xs transition active:scale-95">
                                                <span>Detail</span>
                                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                            
                                            @if(in_array($pengajuan->state->name, ['draft', 'rejected']))
                                                <a href="{{ route('pengajuan.edit', $pengajuan) }}" 
                                                   class="inline-flex items-center px-2.5 py-1.5 bg-amber-50 border border-amber-200 text-amber-800 hover:bg-amber-100 text-xs font-bold rounded-xl shadow-2xs transition active:scale-95">
                                                    Edit
                                                </a>
                                            @endif

                                            @if($pengajuan->state->name === 'draft')
                                                @php
                                                    $submitTarget = auth()->user()->hasRole('bpm') ? 'BKHM' : (auth()->user()->hasRole('bem') ? 'BPM' : 'BEM');
                                                @endphp
                                                <form action="{{ route('pengajuan.ajukan', $pengajuan) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition active:scale-95" 
                                                            onclick="return confirm('Yakin ingin mengajukan proposal ini ke {{ $submitTarget }}?')">
                                                        <span>Ajukan</span>
                                                        <svg class="w-3 h-3 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-16 px-4 text-center">
                                        <div class="w-14 h-14 mx-auto mb-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-center text-slate-400 shadow-2xs">
                                            <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-bold text-slate-800">
                                            @if(request('q') || request('status'))
                                                Tidak Ada Pengajuan yang Cocok
                                            @else
                                                Belum Ada Proposal Diajukan
                                            @endif
                                        </h3>
                                        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                                            @if(request('q') || request('status'))
                                                Silakan sesuaikan kata kunci pencarian atau ubah filter status alur.
                                            @else
                                                Mulai buat pengajuan anggaran dan proposal kegiatan ormawa baru Anda dengan tombol di atas.
                                            @endif
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