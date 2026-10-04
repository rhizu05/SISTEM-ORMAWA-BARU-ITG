<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                Konseling Mahasiswa (Rahasia BKHM)
            </h2>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-800 border border-teal-200">
                Akses Terbatas BKHM
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">Daftar Pengajuan Konseling Personal</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Seluruh tiket bersifat rahasia. Berikan jadwal temu konseling atau respons tertutup langsung ke email mahasiswa.</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-teal-50 text-teal-700 border border-teal-200">
                        {{ $tikets->total() }} Tiket Tercatat
                    </span>
                </div>

                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            <x-table.th>Kode Tiket</x-table.th>
                            <x-table.th>Mahasiswa</x-table.th>
                            <x-table.th>Topik &amp; Metode</x-table.th>
                            <x-table.th>Status</x-table.th>
                            <x-table.th>Jadwal Temu</x-table.th>
                            <x-table.th>Tanggal Masuk</x-table.th>
                            <x-table.th align="center">Aksi</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($tikets as $tiket)
                            <x-table.tr>
                                <x-table.td class="font-mono font-bold text-xs text-teal-700 whitespace-nowrap">
                                    {{ $tiket->kode_tiket }}
                                </x-table.td>
                                <x-table.td>
                                    <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ $tiket->nama_mahasiswa }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $tiket->nim }} &bull; {{ $tiket->prodi ?? '-' }}</div>
                                </x-table.td>
                                <x-table.td>
                                    <div class="font-semibold text-xs text-slate-800">{{ $tiket->topik_konseling }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $tiket->metode_konseling }}</div>
                                </x-table.td>
                                <x-table.td>
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $tiket->status_color }}">
                                        {{ $tiket->status_label }}
                                    </span>
                                </x-table.td>
                                <x-table.td class="text-xs whitespace-nowrap">
                                    @if ($tiket->jadwal_temu)
                                        <span class="font-semibold text-slate-800">{{ $tiket->jadwal_temu->format('d/m/Y H:i') }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Belum dijadwalkan</span>
                                    @endif
                                </x-table.td>
                                <x-table.td class="text-xs text-slate-500 whitespace-nowrap">
                                    {{ $tiket->created_at->format('d/m/Y H:i') }}
                                </x-table.td>
                                <x-table.td align="center" class="whitespace-nowrap">
                                    <a href="{{ route('bkhm.konseling.show', $tiket) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-teal-700 bg-teal-50 border border-teal-200 hover:bg-teal-100 transition min-h-[32px]">
                                        <span>Buka Tiket</span>
                                        <span>&rarr;</span>
                                    </a>
                                </x-table.td>
                            </x-table.tr>
                        @empty
                            <x-table.empty colspan="7" message="Belum ada permohonan konseling masuk." />
                        @endforelse
                    </tbody>
                </x-table>

                @if($tikets->hasPages())
                    <div class="pt-2">
                        {{ $tikets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
