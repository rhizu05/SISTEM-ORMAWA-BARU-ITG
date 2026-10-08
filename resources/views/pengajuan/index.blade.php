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

    <div class="py-8 bg-slate-50/50 min-h-screen" x-data="{ showCancelModal: false, cancelAction: '', cancelTitle: '' }">
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

            {{-- Main Table & Mobile Card Feed Card --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] p-4 sm:p-6 space-y-4">
                {{-- Tampilan Mobile: Card Feed (block md:hidden) --}}
                <div class="block md:hidden space-y-3">
                    @forelse ($pengajuans as $pengajuan)
                        @php
                            $urgensi = $pengajuan->statusUrgensi();
                            $chipClass = 'bg-slate-50 text-slate-700 border-slate-200';
                            $dotClass = 'bg-slate-400';
                            if ($pengajuan->state->name === 'rejected' || $pengajuan->state->name === 'cancelled') {
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
                                $dotClass = 'bg-amber-500';
                            }
                            $cardBorder = ($urgensi && $urgensi['is_urgent']) ? 'border-l-4 border-l-rose-500 bg-rose-50/20' : 'bg-white';
                        @endphp
                        <div class="border border-slate-200/90 rounded-xl p-4 shadow-2xs {{ $cardBorder }} space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('pengajuan.show', $pengajuan) }}" class="font-bold text-slate-900 text-sm hover:text-blue-700 transition leading-snug line-clamp-2">
                                        {{ $pengajuan->nama_kegiatan }}
                                    </a>
                                    @if($pengajuan->programKerja)
                                        <div class="text-[11px] text-slate-500 font-medium mt-1 truncate">
                                            Proker: {{ $pengajuan->programKerja->nama_proker }}
                                        </div>
                                    @endif
                                </div>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border shrink-0 {{ $chipClass }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                    {{ $pengajuan->state->label }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs text-slate-600 pt-1 border-t border-slate-100">
                                <div>
                                    <span class="text-[10px] text-slate-400 block uppercase tracking-wider font-semibold">Jadwal Acara</span>
                                    <span class="font-medium text-slate-700">
                                        {{ $pengajuan->tanggal_mulai_kegiatan ? \Carbon\Carbon::parse($pengajuan->tanggal_mulai_kegiatan)->isoFormat('D MMM Y') : \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->isoFormat('D MMM Y') }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 block uppercase tracking-wider font-semibold">Dana Diajukan</span>
                                    <span class="font-extrabold text-slate-900 font-mono text-sm">
                                        Rp {{ number_format($pengajuan->dana_diajukan, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            @if($urgensi)
                                <div class="pt-0.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $urgensi['badge_class'] }}">
                                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>{{ $urgensi['label'] }}</span>
                                    </span>
                                </div>
                            @endif

                            {{-- Action Buttons Row (min-h-[44px] touch target) --}}
                            <div class="flex items-center gap-2 pt-2 border-t border-slate-100 flex-wrap">
                                <a href="{{ route('pengajuan.show', $pengajuan) }}" 
                                   class="flex-1 min-h-[44px] inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-200/90 text-slate-700 text-xs font-bold rounded-xl transition active:scale-95 shadow-2xs">
                                    <span>Detail</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>

                                @if(in_array($pengajuan->state->name, ['draft', 'rejected']) && $pengajuan->user_id === Auth::id())
                                    <a href="{{ route('pengajuan.edit', $pengajuan) }}" 
                                       class="min-h-[44px] px-3 py-2 inline-flex items-center justify-center bg-amber-50 border border-amber-200 text-amber-800 hover:bg-amber-100 text-xs font-bold rounded-xl transition active:scale-95 shadow-2xs">
                                        Edit
                                    </a>
                                @endif

                                @if($pengajuan->state->name === 'draft' && $pengajuan->user_id === Auth::id())
                                    @php
                                        $submitTarget = auth()->user()->hasRole('bpm') ? 'BKHM' : (auth()->user()->hasRole('bem') ? 'BPM' : 'BEM');
                                    @endphp
                                    <form action="{{ route('pengajuan.ajukan', $pengajuan) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="min-h-[44px] px-3.5 py-2 inline-flex items-center justify-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition active:scale-95" 
                                                onclick="return confirm('Yakin ingin mengajukan proposal ini ke {{ $submitTarget }}?')">
                                            <span>Ajukan</span>
                                            <svg class="w-3.5 h-3.5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>

                                    <form action="{{ route('pengajuan.destroy', $pengajuan) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus draft proposal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="min-h-[44px] px-3 py-2 inline-flex items-center justify-center bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 text-xs font-bold rounded-xl transition active:scale-95 shadow-2xs">
                                            Hapus
                                        </button>
                                    </form>
                                @endif

                                @php
                                    $canCancelInQueue = in_array($pengajuan->state->name, ['submitted', 'bem_approved', 'bpm_approved', 'bkhm_approved', 'wr3_approved', 'to_treasurer']) && $pengajuan->user_id === Auth::id();
                                @endphp
                                @if($canCancelInQueue)
                                    <button type="button" 
                                            @click="showCancelModal = true; cancelAction = '{{ route('pengajuan.batalkan', $pengajuan) }}'; cancelTitle = '{{ addslashes($pengajuan->nama_kegiatan) }}'"
                                            class="min-h-[44px] px-3 py-2 inline-flex items-center justify-center gap-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition active:scale-95 shadow-2xs">
                                        <span>Batalkan</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center bg-slate-50 border border-dashed border-slate-200 rounded-2xl">
                            <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-sm font-semibold text-slate-700">{{ request('q') || request('status') ? 'Tidak ada pengajuan yang sesuai dengan kriteria filter.' : 'Belum ada proposal diajukan.' }}</p>
                            @if(!request('q') && !request('status'))
                                <a href="{{ route('pengajuan.create') }}" class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                                    + Ajukan Proposal Baru
                                </a>
                            @endif
                        </div>
                    @endforelse
                </div>

                {{-- Tampilan Desktop: Tabel Lengkap (hidden md:block) --}}
                <div class="hidden md:block">
                    <x-table>
                        <x-table.thead>
                            <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th scope="col" class="py-3.5 px-5 text-left">Nama Kegiatan &amp; Proker</th>
                                <th scope="col" class="py-3.5 px-5 text-left">Jadwal Acara</th>
                                <th scope="col" class="py-3.5 px-5 text-left">Dana Diajukan</th>
                                <th scope="col" class="py-3.5 px-5 text-center">Status Alur</th>
                                <th scope="col" class="py-3.5 px-5 text-center">Aksi</th>
                            </tr>
                        </x-table.thead>
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
                                    <td class="py-4 px-5 align-middle text-sm text-slate-800">
                                        <div class="font-bold text-slate-900 hover:text-blue-700 transition">
                                            {{ $pengajuan->nama_kegiatan }}
                                        </div>
                                        <div class="flex items-center flex-wrap gap-1.5 mt-1.5">
                                            @if($urgensi)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold tracking-tight shadow-2xs {{ $urgensi['badge_class'] }}">
                                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>{{ $urgensi['label'] }}</span>
                                                </span>
                                            @endif
                                            @if($pengajuan->programKerja)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/70">
                                                    <svg class="w-3 h-3 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                    <span>{{ Str::limit($pengajuan->programKerja->nama_proker, 30) }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Kolom Jadwal --}}
                                    <td class="py-4 px-5 align-middle text-xs whitespace-nowrap text-slate-800">
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
                                    <td class="py-4 px-5 align-middle whitespace-nowrap text-slate-800">
                                        <div class="font-extrabold text-slate-900 text-sm tracking-tight font-mono">
                                            Rp {{ number_format($pengajuan->dana_diajukan, 0, ',', '.') }}
                                        </div>
                                    </td>

                                    {{-- Kolom Status --}}
                                    <td class="py-4 px-5 align-middle text-center whitespace-nowrap text-sm text-slate-800">
                                        @php
                                            $chipClass = 'bg-slate-50 text-slate-700 border-slate-200';
                                            $dotClass = 'bg-slate-400';
                                            if ($pengajuan->state->name === 'rejected') {
                                                $chipClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                                $dotClass = 'bg-rose-500';
                                            } elseif ($pengajuan->state->name === 'cancelled') {
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
                                                $dotClass = 'bg-amber-500';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border shadow-2xs {{ $chipClass }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                            {{ $pengajuan->state->label }}
                                        </span>
                                    </td>

                                    {{-- Kolom Aksi --}}
                                    <td class="py-4 px-5 align-middle text-center whitespace-nowrap text-sm text-slate-800">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('pengajuan.show', $pengajuan) }}" 
                                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-slate-200/90 hover:border-blue-400 hover:text-blue-700 text-slate-700 text-xs font-bold rounded-xl shadow-2xs transition active:scale-95">
                                                <span>Detail</span>
                                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                            
                                            @if(in_array($pengajuan->state->name, ['draft', 'rejected']) && $pengajuan->user_id === Auth::id())
                                                <a href="{{ route('pengajuan.edit', $pengajuan) }}" 
                                                   class="inline-flex items-center px-2.5 py-1.5 bg-amber-50 border border-amber-200 text-amber-800 hover:bg-amber-100 text-xs font-bold rounded-xl shadow-2xs transition active:scale-95">
                                                    Edit
                                                </a>
                                            @endif

                                            @if($pengajuan->state->name === 'draft' && $pengajuan->user_id === Auth::id())
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

                                                <form action="{{ route('pengajuan.destroy', $pengajuan) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus draft proposal ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 text-xs font-bold rounded-xl shadow-2xs transition active:scale-95">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif

                                            @php
                                                $canCancelInQueue = in_array($pengajuan->state->name, ['submitted', 'bem_approved', 'bpm_approved', 'bkhm_approved', 'wr3_approved', 'to_treasurer']) && $pengajuan->user_id === Auth::id();
                                            @endphp
                                            @if($canCancelInQueue)
                                                <button type="button" 
                                                        @click="showCancelModal = true; cancelAction = '{{ route('pengajuan.batalkan', $pengajuan) }}'; cancelTitle = '{{ addslashes($pengajuan->nama_kegiatan) }}'"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition active:scale-95 shadow-2xs">
                                                    <span>Batalkan</span>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <x-table.empty colspan="5" :message="request('q') || request('status') ? 'Tidak ada pengajuan yang sesuai dengan kriteria filter.' : 'Belum ada proposal diajukan.'" />
                            @endforelse
                        </tbody>
                    </x-table>
                </div>

                {{-- Pagination Footer --}}
                @if($pengajuans->hasPages())
                    <div class="pt-2">
                        {{ $pengajuans->links() }}
                    </div>
                @endif
            </div>

            <!-- Modal Konfirmasi Pembatalan -->
            <div x-show="showCancelModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-slate-900/60" @click="showCancelModal = false"></div>
                    <div class="relative inline-block w-full max-w-lg p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-slate-200">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                                <span class="text-rose-600">⚠️</span> Konfirmasi Pembatalan Pengajuan
                            </h3>
                            <button type="button" @click="showCancelModal = false" class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
                        </div>
                        <form :action="cancelAction" method="POST" class="mt-4 space-y-4">
                            @csrf
                            <p class="text-xs text-slate-600 leading-relaxed">
                                Apakah Anda yakin ingin membatalkan pengajuan <strong x-text="cancelTitle"></strong>? Proposal akan ditarik dari antrean verifikator dan dicatat pembatalan resmi.
                            </p>
                            <div>
                                <label for="alasan_batal" class="block text-xs font-bold text-slate-700 mb-1">
                                    Alasan Pembatalan <span class="text-rose-500">*</span>
                                </label>
                                <textarea id="alasan_batal" name="alasan" rows="3" required minlength="5" maxlength="500" placeholder="Contoh: Kegiatan dibatalkan panitia / Salah mengunggah berkas rancangan anggaran..." class="w-full text-xs rounded-xl border border-slate-200 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 p-2.5"></textarea>
                                <p class="text-[11px] text-slate-400 mt-1">Alasan pembatalan akan disimpan di catatan riwayat alur pengajuan.</p>
                            </div>
                            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                                <button type="button" @click="showCancelModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                                    Kembali
                                </button>
                                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                    Ya, Batalkan Proposal Ini
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>