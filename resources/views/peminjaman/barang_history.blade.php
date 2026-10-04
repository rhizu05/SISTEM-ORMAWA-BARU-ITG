<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Peminjaman Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Riwayat Peminjaman Barang</h3>
                    <p class="text-sm text-slate-500">Menampilkan seluruh riwayat permohonan peminjaman sarana & barang milik Anda</p>
                </div>
                <a href="{{ route('peminjaman.barang.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    + Ajukan Peminjaman Barang
                </a>
            </div>

            {{-- Mobile Card Feed View --}}
            <div class="block md:hidden space-y-3">
                @forelse ($peminjaman_barang as $p)
                    @php
                        $bkhmClass = match($p->status_bkhm) {
                            'disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-amber-50 text-amber-700 border-amber-200'
                        };
                        $sarprasClass = match($p->status_sarpras) {
                            'disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-slate-100 text-slate-600 border-slate-200'
                        };
                        $st = strtolower($p->status_akhir);
                        $akhirClass = match(true) {
                            str_contains($st, 'tolak') => 'bg-rose-50 text-rose-700 border-rose-200',
                            str_contains($st, 'selesai') || str_contains($st, 'disetujui') || str_contains($st, 'digunakan') || str_contains($st, 'kembali') => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            default => 'bg-amber-50 text-amber-700 border-amber-200'
                        };
                    @endphp
                    <div class="bg-white border border-slate-200/90 rounded-xl p-4 shadow-2xs space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-bold text-slate-900 text-sm leading-snug">
                                    {{ $p->nama_kegiatan }}
                                </h4>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border shrink-0 {{ $akhirClass }}">
                                {{ $p->status_akhir }}
                            </span>
                        </div>

                        {{-- Daftar Barang --}}
                        <div class="bg-slate-50 p-2.5 rounded-lg">
                            <span class="text-[10px] text-slate-400 block uppercase tracking-wider font-semibold mb-1">Barang Dipinjam</span>
                            <ul class="list-disc pl-4 space-y-0.5 text-xs text-slate-600">
                                @foreach($p->kebutuhan_barang as $brg)
                                    <li><span class="font-medium text-slate-800">{{ $brg['nama_barang'] }}</span> ({{ $brg['qty'] }})</li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-lg space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Waktu Penggunaan:</span>
                                <span class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($p->tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($p->tgl_selesai)->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-2 text-xs pt-1 border-t border-slate-100 flex-wrap">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[10px] text-slate-400">Status:</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $bkhmClass }}">
                                    BKHM: {{ ucfirst($p->status_bkhm) }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $sarprasClass }}">
                                    Sarpras: {{ ucfirst($p->status_sarpras) }}
                                </span>
                            </div>

                            @if(($p->status_bkhm === 'disetujui' && $p->status_sarpras === 'disetujui') || str_contains(strtolower($p->status_akhir), 'selesai') || str_contains(strtolower($p->status_akhir), 'disetujui') || str_contains(strtolower($p->status_akhir), 'digunakan') || str_contains(strtolower($p->status_akhir), 'kembali'))
                                <a href="{{ route('peminjaman.barang.cetak', $p) }}" target="_blank" class="min-h-[44px] inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>Cetak Bukti</span>
                                </a>
                            @endif
                        </div>

                        @if($p->catatan_penolakan)
                            <div class="text-xs text-rose-600 bg-rose-50 p-2 rounded-lg font-medium">Catatan: {{ $p->catatan_penolakan }}</div>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center bg-white border border-dashed border-slate-200 rounded-2xl">
                        <p class="text-sm font-semibold text-slate-700">Belum ada riwayat peminjaman barang.</p>
                        <a href="{{ route('peminjaman.barang.create') }}" class="mt-3 inline-flex items-center gap-1.5 min-h-[44px] px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                            + Ajukan Peminjaman Barang
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden md:block">
                <x-table>
                    <x-table.thead>
                        <tr>
                            <x-table.th>Kegiatan</x-table.th>
                            <x-table.th>Barang Dipinjam</x-table.th>
                            <x-table.th>Waktu Penggunaan</x-table.th>
                            <x-table.th align="center">Status BKHM</x-table.th>
                            <x-table.th align="center">Status Sarpras</x-table.th>
                            <x-table.th align="center">Status Akhir</x-table.th>
                            <x-table.th align="center">Aksi</x-table.th>
                        </tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($peminjaman_barang as $p)
                        <x-table.tr>
                            <x-table.td>
                                <span class="font-semibold text-slate-900">{{ $p->nama_kegiatan }}</span>
                            </x-table.td>
                            <x-table.td>
                                <ul class="list-disc pl-4 space-y-0.5 text-xs text-slate-600">
                                    @foreach($p->kebutuhan_barang as $brg)
                                        <li><span class="font-medium text-slate-800">{{ $brg['nama_barang'] }}</span> ({{ $brg['qty'] }})</li>
                                    @endforeach
                                </ul>
                            </x-table.td>
                            <x-table.td>
                                <span class="text-xs font-medium text-slate-700">
                                    {{ \Carbon\Carbon::parse($p->tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($p->tgl_selesai)->format('d/m/Y') }}
                                </span>
                            </x-table.td>
                            <x-table.td align="center">
                                @php
                                    $bkhmClass = match($p->status_bkhm) {
                                        'disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $bkhmClass }}">
                                    {{ ucfirst($p->status_bkhm) }}
                                </span>
                            </x-table.td>
                            <x-table.td align="center">
                                @php
                                    $sarprasClass = match($p->status_sarpras) {
                                        'disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-slate-100 text-slate-600 border-slate-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $sarprasClass }}">
                                    {{ ucfirst($p->status_sarpras) }}
                                </span>
                            </x-table.td>
                            <x-table.td align="center">
                                @php
                                    $st = strtolower($p->status_akhir);
                                    $akhirClass = match(true) {
                                        str_contains($st, 'tolak') => 'bg-rose-50 text-rose-700 border-rose-200',
                                        str_contains($st, 'selesai') || str_contains($st, 'disetujui') || str_contains($st, 'digunakan') || str_contains($st, 'kembali') => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $akhirClass }}">
                                    {{ $p->status_akhir }}
                                </span>
                                @if($p->catatan_penolakan)
                                    <div class="text-xs text-rose-600 font-medium mt-1">{{ $p->catatan_penolakan }}</div>
                                @endif
                            </x-table.td>
                            <x-table.td align="center">
                                @if(($p->status_bkhm === 'disetujui' && $p->status_sarpras === 'disetujui') || str_contains(strtolower($p->status_akhir), 'selesai') || str_contains(strtolower($p->status_akhir), 'disetujui') || str_contains(strtolower($p->status_akhir), 'digunakan') || str_contains(strtolower($p->status_akhir), 'kembali'))
                                    <a href="{{ route('peminjaman.barang.cetak', $p) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        Cetak Bukti
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 italic">Menunggu Izin</span>
                                @endif
                            </x-table.td>
                        </x-table.tr>
                        @empty
                        <x-table.empty colspan="7" message="Belum ada riwayat peminjaman barang." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>
        </div>
    </div>
</x-app-layout>
