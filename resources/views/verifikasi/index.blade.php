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
            
            {{-- Filter Tabs Antrean --}}
            @php
                $currentStatus = request('status');
            @endphp
            <div class="flex items-center gap-2 overflow-x-auto pb-1 select-none">
                <a href="{{ route('verifikasi.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ !$currentStatus ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                    <span>Semua Antrean</span>
                </a>
                <a href="{{ route('verifikasi.index', ['status' => 'proposal']) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $currentStatus === 'proposal' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                    <span>Verifikasi Proposal</span>
                </a>
                <a href="{{ route('verifikasi.index', ['status' => 'siap_bendahara']) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $currentStatus === 'siap_bendahara' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                    <span>Siap Bendahara (Pencairan)</span>
                </a>
                <a href="{{ route('verifikasi.index', ['status' => 'lpj']) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $currentStatus === 'lpj' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80' }}">
                    <span>Verifikasi LPJ</span>
                </a>
            </div>

            @if($currentStatus === 'siap_bendahara')
                <div class="p-4 bg-purple-50 border border-purple-200/80 rounded-2xl flex items-center justify-between gap-3 text-xs text-purple-900">
                    <div class="flex items-center gap-2">
                        <span>Menampilkan proposal yang telah disahkan WR3 dan siap diajukan pencairan dananya ke Bendahara.</span>
                    </div>
                    <a href="{{ route('verifikasi.index') }}" class="font-bold text-purple-700 hover:text-purple-900 underline shrink-0">Reset Filter</a>
                </div>
            @endif

            {{-- Main Table Card --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] p-6 space-y-4">
                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            <x-table.th>Ormawa Pengusul</x-table.th>
                            <x-table.th>Nama Kegiatan &amp; Proker</x-table.th>
                            <x-table.th>Jadwal Acara</x-table.th>
                            <x-table.th>Dana Diajukan</x-table.th>
                            <x-table.th align="center">Status Alur</x-table.th>
                            <x-table.th align="center">Aksi</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
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
                            <x-table.tr class="{{ $rowClass }}">
                                {{-- Kolom Ormawa --}}
                                <x-table.td>
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
                                </x-table.td>

                                {{-- Kolom Kegiatan & Proker --}}
                                <x-table.td>
                                    <div class="font-semibold text-slate-900 group-hover:text-blue-700 transition">
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
                                                <span>{{ Str::limit($pengajuan->programKerja->nama_proker, 28) }}</span>
                                            </span>
                                        @endif
                                        @if($isDiingatkan)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">
                                                <svg class="w-3 h-3 text-amber-800 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                                <span>Diingatkan ({{ $pengajuan->terakhir_diingatkan_at->diffForHumans() }})</span>
                                            </span>
                                        @endif
                                    </div>
                                </x-table.td>

                                {{-- Kolom Jadwal --}}
                                <x-table.td class="text-xs whitespace-nowrap">
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
                                </x-table.td>

                                {{-- Kolom Dana --}}
                                <x-table.td class="whitespace-nowrap">
                                    <div class="font-extrabold text-slate-900 text-sm tracking-tight font-mono">
                                        Rp {{ number_format($pengajuan->dana_diajukan, 0, ',', '.') }}
                                    </div>
                                </x-table.td>

                                {{-- Kolom Status --}}
                                <x-table.td align="center" class="whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200/90 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        {{ $pengajuan->state->label }}
                                    </span>
                                </x-table.td>

                                {{-- Kolom Aksi --}}
                                <x-table.td align="center" class="whitespace-nowrap">
                                    <a href="{{ route('verifikasi.show', $pengajuan) }}" 
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#0B1528] hover:bg-[#1E3A8A] text-white text-xs font-bold rounded-xl shadow-xs hover:shadow-md transition active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 min-h-[32px]">
                                        <span>Verifikasi</span>
                                        <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </x-table.td>
                            </x-table.tr>
                        @empty
                            <x-table.empty colspan="6" message="Saat ini tidak ada proposal atau LPJ yang menunggu tindakan verifikasi Anda." />
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