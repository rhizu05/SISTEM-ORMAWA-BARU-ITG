<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Prestasi & Kompetisi') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Prestasi & Kompetisi Mahasiswa</h3>
                    <p class="text-sm text-slate-500">Laporkan perolehan kejuaraan, sertifikat kompetisi, dan rekognisi ormawa maupun individu</p>
                </div>
                <a href="{{ route('prestasi.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    + Laporkan Prestasi
                </a>
            </div>

            <x-table>
                <x-table.thead>
                    <tr>
                        <x-table.th>Kegiatan & Penyelenggara</x-table.th>
                        <x-table.th>Pelapor</x-table.th>
                        <x-table.th>Tingkat</x-table.th>
                        <x-table.th>Capaian Juara</x-table.th>
                        <x-table.th>Afiliasi</x-table.th>
                        <x-table.th align="center">Status</x-table.th>
                        <x-table.th align="center">Aksi</x-table.th>
                    </tr>
                </x-table.thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($prestasis as $p)
                    <x-table.tr>
                        <x-table.td>
                            <div class="font-semibold text-slate-900">{{ $p->nama_kegiatan }}</div>
                            <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-1">
                                <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>{{ $p->rentang_tanggal }}</span>
                                @if($p->penyelenggara)
                                    <span>&bull;</span>
                                    @if($p->url_penyelenggara)
                                        <a href="{{ $p->url_penyelenggara }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline">
                                            {{ $p->penyelenggara }} &nearr;
                                        </a>
                                    @else
                                        <span>{{ $p->penyelenggara }}</span>
                                    @endif
                                @endif
                            </div>
                        </x-table.td>
                        <x-table.td>
                            <span class="text-sm font-medium text-slate-700">{{ $p->user->name ?? '-' }}</span>
                        </x-table.td>
                        <x-table.td>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200/60">{{ $p->tingkat }}</span>
                        </x-table.td>
                        <x-table.td>
                            <span class="font-semibold text-slate-900">{{ $p->juara ?? '-' }}</span>
                        </x-table.td>
                        <x-table.td>
                            <span class="text-xs text-slate-600">{{ ucfirst($p->afiliasi) }}{{ $p->unit_terkait ? ' ('.$p->unit_terkait.')' : '' }}</span>
                        </x-table.td>
                        <x-table.td align="center">
                            @php
                                $statusClass = match($p->status) {
                                    'terverifikasi' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-amber-50 text-amber-700 border-amber-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusClass }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </x-table.td>
                        <x-table.td align="center">
                            <div class="flex items-center justify-center gap-2">
                                @if($p->file_bukti)
                                    <a href="{{ route('prestasi.bukti', $p) }}" class="inline-flex items-center px-2 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">Sertifikat</a>
                                @endif
                                @if($p->foto_penyerahan)
                                    <a href="{{ asset('storage/' . $p->foto_penyerahan) }}" target="_blank" class="inline-flex items-center px-2 py-1 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors">Foto</a>
                                @endif
                                @hasanyrole('bkhm|wr3|admin')
                                @if($p->status === 'pending')
                                <form action="{{ route('prestasi.verify', $p) }}" method="POST" class="inline-flex gap-1.5 items-center ml-1">
                                    @csrf @method('PATCH')
                                    <input type="text" name="catatan_bkhm" placeholder="Catatan..." class="border-slate-300 rounded-lg text-xs py-1 px-2 w-28 focus:border-indigo-500 focus:ring-indigo-500">
                                    <button name="status" value="terverifikasi" class="inline-flex items-center px-2 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors">Setujui</button>
                                    <button name="status" value="ditolak" class="inline-flex items-center px-2 py-1 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">Tolak</button>
                                </form>
                                @endif
                                @endhasanyrole
                            </div>
                        </x-table.td>
                    </x-table.tr>
                    @empty
                    <x-table.empty colspan="7" message="Belum ada prestasi dilaporkan." />
                    @endforelse
                </tbody>
            </x-table>
            <div class="mt-4">{{ $prestasis->links() }}</div>
        </div>
    </div>
</x-app-layout>
