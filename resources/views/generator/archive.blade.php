<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Arsip Digital Persuratan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6 md:p-8">
                
                <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-100">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Kumpulan Dokumen Digital</h3>
                        <p class="text-sm text-slate-500 mt-0.5">Kelola dan unduh arsip proposal, RAB, dan surat resmi organisasi Anda</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('generator.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            + Buat Proposal
                        </a>
                        <a href="{{ route('generator.letters.create') }}" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            + Buat Surat
                        </a>
                    </div>
                </div>

                @if(Auth::user()->file_sk)
                <!-- Legalitas SK Banner -->
                <div class="mb-8 p-6 bg-gradient-to-r from-indigo-900 to-indigo-800 rounded-2xl text-white shadow-sm border border-indigo-700 relative overflow-hidden">
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-indigo-100 border border-white/20">
                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Dokumen Legalitas Resmi Aktif</span>
                            </div>
                            <h3 class="text-xl font-bold tracking-tight">Surat Keputusan (SK) Pengesahan Kepengurusan</h3>
                            <p class="text-xs text-indigo-200">
                                Nomor SK: <span class="font-mono font-bold text-white">{{ Auth::user()->nomor_sk ?? '-' }}</span> &bull; 
                                Tanggal Terbit: <span class="text-white">{{ Auth::user()->tanggal_sk ? Auth::user()->tanggal_sk->format('d F Y') : '-' }}</span>
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('dokumen.sk-ormawa', Auth::user()) }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white text-indigo-900 hover:bg-indigo-50 font-bold rounded-xl text-xs transition shadow-sm">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Buka PDF SK</span>
                            </a>
                            <a href="{{ route('dokumen.sk-ormawa', ['user' => Auth::user(), 'download' => 1]) }}" class="inline-flex items-center p-2.5 bg-indigo-700/60 hover:bg-indigo-700 text-white border border-white/20 rounded-xl transition" title="Unduh SK">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 gap-8">
                    <!-- Proposal Section -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-bold text-slate-800 text-base">Proposal & RAB</h4>
                                <p class="text-xs text-slate-500">Daftar arsip dokumen proposal kegiatan yang telah dibuat</p>
                            </div>
                        </div>

                        <x-table>
                            <x-table.thead>
                                <tr>
                                    <x-table.th>Nama Kegiatan</x-table.th>
                                    <x-table.th>Tanggal Dibuat</x-table.th>
                                    <x-table.th align="right">Aksi</x-table.th>
                                </tr>
                            </x-table.thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($proposals as $p)
                                    <x-table.tr>
                                        <x-table.td>
                                            <div class="font-semibold text-slate-900">{{ $p->nama_kegiatan }}</div>
                                        </x-table.td>
                                        <x-table.td>
                                            <span class="text-xs text-slate-600 font-medium">{{ $p->created_at->format('d M Y') }}</span>
                                        </x-table.td>
                                        <x-table.td align="right">
                                            <div class="flex items-center justify-end gap-2">
                                                @php
                                                    $existingLpj = $letters->first(function($item) use ($p) {
                                                        return $item->type === 'lpj' && (($item->metadata['proposal_id'] ?? null) == $p->id || $item->proposal_otomatis_id == $p->id);
                                                    });
                                                @endphp
                                                @if($existingLpj)
                                                    <a href="{{ route('generator.lpj.show', $existingLpj) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-purple-50 text-purple-700 hover:bg-purple-100 rounded-lg text-xs font-semibold transition-colors">
                                                        Lihat LPJ
                                                    </a>
                                                @else
                                                    <a href="{{ route('generator.lpj.create', $p->id) }}" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-semibold transition-colors">
                                                        Buat LPJ
                                                    </a>
                                                @endif
                                                <a href="{{ route('generator.print', $p) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg text-xs font-semibold transition-colors">
                                                    Cetak PDF
                                                </a>
                                            </div>
                                        </x-table.td>
                                    </x-table.tr>
                                @empty
                                    <x-table.empty colspan="3" message="Belum ada arsip proposal." />
                                @endforelse
                            </tbody>
                        </x-table>
                    </div>

                    <!-- Letters Section -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-bold text-slate-800 text-base">Surat-Surat Digital</h4>
                                <p class="text-xs text-slate-500">Daftar arsip surat resmi organisasi yang diterbitkan</p>
                            </div>
                        </div>

                        <x-table>
                            <x-table.thead>
                                <tr>
                                    <x-table.th>Perihal</x-table.th>
                                    <x-table.th>Jenis Surat</x-table.th>
                                    <x-table.th>Tanggal Dibuat</x-table.th>
                                    <x-table.th align="right">Aksi</x-table.th>
                                </tr>
                            </x-table.thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($letters as $l)
                                    <x-table.tr>
                                        <x-table.td>
                                            <div class="font-semibold text-slate-900">{{ $l->perihal }}</div>
                                            @if($l->nomor_surat)
                                                <div class="text-[11px] font-mono text-slate-500">{{ $l->nomor_surat }}</div>
                                            @endif
                                            @if($l->user && (Auth::user()->hasAnyRole(['admin', 'bkhm', 'wr3', 'bem', 'bpm']) && $l->user_id !== Auth::id()))
                                                <div class="text-[11px] text-indigo-600 font-medium mt-0.5">Ormawa: {{ $l->user->name }}</div>
                                            @endif
                                        </x-table.td>
                                        <x-table.td>
                                            @if($l->type === 'sk_kepengurusan')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    SK KEPENGURUSAN
                                                </span>
                                            @elseif($l->type === 'lpj')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                    LAPORAN (LPJ)
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200/60">
                                                    {{ str_replace('_', ' ', strtoupper($l->type)) }}
                                                </span>
                                            @endif
                                        </x-table.td>
                                        <x-table.td>
                                            <span class="text-xs text-slate-600 font-medium">{{ $l->created_at->format('d M Y') }}</span>
                                        </x-table.td>
                                        <x-table.td align="right">
                                            @if($l->type === 'sk_kepengurusan')
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <a href="{{ route('dokumen.sk-ormawa', $l->user_id) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-semibold transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <span>Buka SK</span>
                                                    </a>
                                                    <a href="{{ route('dokumen.sk-ormawa', ['user' => $l->user_id, 'download' => 1]) }}" class="inline-flex items-center p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" title="Unduh File SK">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    </a>
                                                </div>
                                            @elseif($l->type === 'lpj')
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <a href="{{ route('generator.lpj.show', $l) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-50 text-purple-700 hover:bg-purple-100 rounded-lg text-xs font-semibold transition-colors">
                                                        <span>Buka LPJ</span>
                                                    </a>
                                                    <a href="{{ route('generator.lpj.pdf', $l) }}" class="inline-flex items-center p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" title="Unduh PDF LPJ">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    </a>
                                                </div>
                                            @else
                                                <a href="{{ route('generator.letters.show', $l) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg text-xs font-semibold transition-colors">
                                                    Lihat / Cetak
                                                </a>
                                            @endif
                                        </x-table.td>
                                    </x-table.tr>
                                @empty
                                    <x-table.empty colspan="4" message="Belum ada arsip surat." />
                                @endforelse
                            </tbody>
                        </x-table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
