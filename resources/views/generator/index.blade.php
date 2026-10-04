<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 leading-tight">
            {{ __('Generator Dokumen Proposal') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Arsip Generator Dokumen</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar naskah proposal otomatis yang dibuat melalui sistem generator.</p>
                </div>
                <a href="{{ route('generator.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow-sm transition min-h-[44px] focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Proposal Otomatis
                </a>
            </div>

            <x-table>
                <x-table.thead>
                    <tr>
                        <x-table.th>Nama Kegiatan</x-table.th>
                        <x-table.th>Dibuat Pada</x-table.th>
                        <x-table.th align="center">Aksi Dokumen</x-table.th>
                    </tr>
                </x-table.thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($proposals as $p)
                    <x-table.tr>
                        <x-table.td>
                            <div class="font-bold text-slate-900">{{ $p->nama_kegiatan }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">Penandatangan: {{ count($p->penandatangan ?? []) }} Pihak</div>
                        </x-table.td>
                        <x-table.td>
                            <div class="text-xs font-semibold text-slate-800">{{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('d F Y') }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($p->created_at)->format('H:i') }} WIB</div>
                        </x-table.td>
                        <x-table.td align="center">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('generator.show', $p) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition min-h-[36px]">
                                    Detail
                                </a>
                                <a href="{{ route('generator.pdf', $p) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-emerald-200 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 transition min-h-[36px]">
                                    Unduh PDF
                                </a>
                                <a href="{{ route('generator.print', $p) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-blue-50 text-xs font-bold text-blue-700 hover:bg-blue-100 transition min-h-[36px]">
                                    Cetak
                                </a>
                            </div>
                        </x-table.td>
                    </x-table.tr>
                    @empty
                    <x-table.empty :colspan="3" message="Belum ada dokumen proposal yang di-generate." />
                    @endforelse
                </tbody>
            </x-table>

        </div>
    </div>
</x-app-layout>