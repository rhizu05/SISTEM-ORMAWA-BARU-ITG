<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                Konseling Mahasiswa (Rahasia BKHM)
            </h2>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-800 border border-teal-200">
                🔒 Akses Terbatas Staf BKHM
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

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">Daftar Pengajuan Konseling Personal</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Seluruh tiket bersifat rahasia. Berikan jadwal temu konseling atau respons tertutup langsung ke email mahasiswa.</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="py-3.5 px-4">Kode Tiket</th>
                                <th class="py-3.5 px-4">Mahasiswa</th>
                                <th class="py-3.5 px-4">Topik & Metode</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4">Jadwal Temu</th>
                                <th class="py-3.5 px-4">Tanggal Masuk</th>
                                <th class="py-3.5 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($tikets as $tiket)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-4 font-mono font-bold text-xs text-teal-700">
                                        {{ $tiket->kode_tiket }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900 text-xs sm:text-sm">{{ $tiket->nama_mahasiswa }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $tiket->nim }} &bull; {{ $tiket->prodi ?? '-' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-xs text-slate-800">{{ $tiket->topik_konseling }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $tiket->metode_konseling }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $tiket->status_color }}">
                                            {{ $tiket->status_label }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs">
                                        @if ($tiket->jadwal_temu)
                                            <span class="font-semibold text-slate-800">{{ $tiket->jadwal_temu->format('d/m/Y H:i') }}</span>
                                        @else
                                            <span class="text-slate-400 italic">Belum dijadwalkan</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-slate-500">
                                        {{ $tiket->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <a href="{{ route('bkhm.konseling.show', $tiket) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-teal-700 bg-teal-50 border border-teal-200 hover:bg-teal-100 transition">
                                            Buka Tiket &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-10 text-center text-slate-400 text-sm">
                                        Belum ada permohonan konseling masuk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $tikets->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
