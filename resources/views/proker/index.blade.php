<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    {{ __('Daftar Program Kerja Tahunan') }}
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Rencana program kerja organisasi mahasiswa dan monitoring pengawasan oleh Badan Perwakilan Mahasiswa (BPM).
                </p>
            </div>
            @if(isset($prokers))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-xs font-bold tracking-wide self-start sm:self-auto shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    {{ $prokers->total() }} Program Kerja
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8 bg-slate-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Toolbar: Tombol Tambah Proker --}}
            @hasanyrole('ormawa|bem')
                <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-[0_4px_20px_rgba(0,0,0,0.02)] flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">Pengajuan &amp; Realisasi Proker</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftarkan rencana kegiatan kepengurusan ormawa Anda untuk periode aktif saat ini.</p>
                    </div>
                    <a href="{{ route('proker.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#0B1528] hover:bg-[#1E3A8A] text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs hover:shadow-md transition active:scale-95 shrink-0">
                        <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Proker Baru</span>
                    </a>
                </div>
            @endhasanyrole

            {{-- Main Table Card --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] overflow-hidden">
                <div class="overflow-x-auto skin-scrollbar">
                    <table class="min-w-full divide-y divide-slate-100 text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/90 border-b border-slate-200/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider select-none">
                                <th scope="col" class="py-3.5 px-5">Ormawa Pengusul</th>
                                <th scope="col" class="py-3.5 px-5">Nama Program Kerja</th>
                                <th scope="col" class="py-3.5 px-5">Target Pelaksanaan</th>
                                <th scope="col" class="py-3.5 px-5 text-center">Status Proker</th>
                                <th scope="col" class="py-3.5 px-5">Catatan Evaluasi BPM</th>
                                @hasanyrole('bpm|admin')
                                    <th scope="col" class="py-3.5 px-5 text-center">Tindakan BPM</th>
                                @endhasanyrole
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white text-sm">
                            @forelse ($prokers as $proker)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    {{-- Kolom Ormawa --}}
                                    <td class="py-4 px-5 align-middle">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#1E40AF] border border-blue-200/60 flex items-center justify-center font-bold text-xs shrink-0 select-none">
                                                {{ strtoupper(substr($proker->user->name ?? 'O', 0, 2)) }}
                                            </div>
                                            <div class="font-bold text-slate-800 text-sm truncate max-w-[160px]" title="{{ $proker->user->name }}">
                                                {{ $proker->user->name }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom Nama Proker --}}
                                    <td class="py-4 px-5 align-middle">
                                        <div class="font-bold text-slate-900 hover:text-blue-700 transition">
                                            {{ $proker->nama_proker }}
                                        </div>
                                        <div class="text-xs text-slate-500 mt-0.5 line-clamp-1">
                                            {{ $proker->deskripsi }}
                                        </div>
                                        <div class="mt-1.5">
                                            @if(($proker->pengajuans_count ?? 0) > 0)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    📑 {{ $proker->pengajuans_count }} Proposal Terhubung
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] text-slate-400 bg-slate-50 border border-slate-200/70">
                                                    Belum ada proposal
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Kolom Target Pelaksanaan --}}
                                    <td class="py-4 px-5 align-middle text-xs whitespace-nowrap">
                                        <div class="font-bold text-slate-800">
                                            {{ \Carbon\Carbon::parse($proker->rencana_pelaksanaan)->isoFormat('D MMMM Y') }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">Rencana Kegiatan</div>
                                    </td>

                                    {{-- Kolom Status Proker --}}
                                    <td class="py-4 px-5 align-middle text-center whitespace-nowrap">
                                        @php
                                            $chipClass = 'bg-slate-50 text-slate-700 border-slate-200';
                                            $dotClass = 'bg-slate-400';
                                            $labelStatus = 'Rencana';

                                            if ($proker->status == 'rencana') {
                                                $chipClass = 'bg-slate-50 text-slate-700 border-slate-200';
                                                $dotClass = 'bg-slate-400';
                                                $labelStatus = 'Rencana';
                                            } elseif ($proker->status == 'proses') {
                                                $chipClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                                $dotClass = 'bg-blue-600 animate-pulse';
                                                $labelStatus = 'Dalam Proses';
                                            } elseif ($proker->status == 'terlaksana') {
                                                $chipClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                                $dotClass = 'bg-emerald-500';
                                                $labelStatus = 'Terlaksana';
                                            } else {
                                                $chipClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                                $dotClass = 'bg-rose-500';
                                                $labelStatus = 'Kendala';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border shadow-2xs {{ $chipClass }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                            {{ $labelStatus }}
                                        </span>
                                    </td>

                                    {{-- Kolom Catatan Evaluasi BPM --}}
                                    <td class="py-4 px-5 align-middle text-xs text-slate-600 max-w-xs">
                                        @if($proker->catatan_bpm)
                                            <span class="bg-amber-50 text-amber-900 border border-amber-200/80 px-2.5 py-1 rounded-lg inline-block">
                                                {{ $proker->catatan_bpm }}
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic">Belum ada catatan</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Aksi BPM --}}
                                    @hasanyrole('bpm|admin')
                                        <td class="py-4 px-5 align-middle text-center whitespace-nowrap">
                                            <form action="{{ route('proker.update', $proker) }}" method="POST" class="inline-flex items-center gap-1.5">
                                                @csrf
                                                @method('PUT')
                                                <select name="status" class="text-xs bg-slate-50 border-slate-200 rounded-xl py-1.5 px-2.5 focus:ring-blue-100 focus:border-blue-500 shadow-2xs">
                                                    <option value="rencana" {{ $proker->status == 'rencana' ? 'selected' : '' }}>Rencana</option>
                                                    <option value="proses" {{ $proker->status == 'proses' ? 'selected' : '' }}>Proses</option>
                                                    <option value="terlaksana" {{ $proker->status == 'terlaksana' ? 'selected' : '' }}>Terlaksana</option>
                                                    <option value="kendala" {{ $proker->status == 'kendala' ? 'selected' : '' }}>Kendala</option>
                                                </select>
                                                <input type="text" name="catatan_bpm" placeholder="Catatan BPM..." value="{{ $proker->catatan_bpm }}" 
                                                       class="text-xs bg-slate-50 border-slate-200 rounded-xl w-32 py-1.5 px-2.5 focus:bg-white focus:ring-blue-100 focus:border-blue-500 shadow-2xs">
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-[#0B1528] hover:bg-[#1E3A8A] text-white text-xs font-bold rounded-xl shadow-xs transition active:scale-95">
                                                    Simpan
                                                </button>
                                            </form>
                                        </td>
                                    @endhasanyrole
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->hasAnyRole(['bpm', 'admin']) ? 6 : 5 }}" class="py-16 px-4 text-center">
                                        <div class="w-14 h-14 mx-auto mb-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-center text-slate-400 shadow-2xs">
                                            <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <h3 class="text-base font-bold text-slate-800">Belum Ada Program Kerja Terdaftar</h3>
                                        <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                                            Daftar program kerja ormawa untuk tahun kepengurusan aktif ini belum diunggah.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                @if($prokers->hasPages())
                    <div class="px-6 py-4 bg-slate-50/60 border-t border-slate-100">
                        {{ $prokers->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
