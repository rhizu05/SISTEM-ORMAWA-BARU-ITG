<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Peminjaman Tempat & Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            

            <div class="flex flex-wrap gap-3 mb-6">
                <a href="{{ route('peminjaman.tempat.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    + Ajukan Peminjaman Ruangan
                </a>
                <a href="{{ route('peminjaman.barang.create') }}" class="inline-flex items-center px-4 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                    + Ajukan Peminjaman Barang
                </a>
            </div>

            <!-- Tab: Peminjaman Tempat -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-slate-900">Riwayat Peminjaman Ruangan</h3>
                    <p class="text-xs text-slate-500">Status terkini permohonan peminjaman gedung & ruangan</p>
                </div>
                
                <x-table>
                    <x-table.thead>
                        <tr>
                            <x-table.th>Kegiatan</x-table.th>
                            <x-table.th>Ruangan</x-table.th>
                            <x-table.th>Waktu Penggunaan</x-table.th>
                            <x-table.th align="center">Status</x-table.th>
                        </tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($peminjaman_tempat as $p)
                        <x-table.tr>
                            <x-table.td>
                                <span class="font-semibold text-slate-900">{{ $p->nama_kegiatan }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="text-sm font-medium text-slate-700">{{ $p->ruangan->nama_ruangan ?? '-' }}</span>
                            </x-table.td>
                            <x-table.td>
                                <div class="text-xs font-medium text-slate-700">
                                    {{ \Carbon\Carbon::parse($p->tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($p->tgl_selesai)->format('d/m/Y') }}
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ \Carbon\Carbon::parse($p->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($p->jam_selesai)->format('H:i') }} WIB
                                </div>
                            </x-table.td>
                            <x-table.td align="center">
                                @php
                                    $st = strtolower($p->status_akhir);
                                    $badgeClass = match(true) {
                                        str_contains($st, 'tolak') => 'bg-rose-50 text-rose-700 border-rose-200',
                                        str_contains($st, 'selesai') || str_contains($st, 'disetujui') => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $badgeClass }}">
                                    {{ $p->status_akhir }}
                                </span>
                            </x-table.td>
                        </x-table.tr>
                        @empty
                        <x-table.empty colspan="4" message="Belum ada riwayat peminjaman ruangan." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            <!-- Tab: Peminjaman Barang -->
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-slate-900">Riwayat Peminjaman Barang</h3>
                    <p class="text-xs text-slate-500">Status terkini permohonan logistik dan perlengkapan sarpras</p>
                </div>
                
                <x-table>
                    <x-table.thead>
                        <tr>
                            <x-table.th>Kegiatan</x-table.th>
                            <x-table.th>Barang Dipinjam</x-table.th>
                            <x-table.th>Waktu Penggunaan</x-table.th>
                            <x-table.th align="center">Status</x-table.th>
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
                                    $st = strtolower($p->status_akhir);
                                    $badgeClass = match(true) {
                                        str_contains($st, 'tolak') => 'bg-rose-50 text-rose-700 border-rose-200',
                                        str_contains($st, 'selesai') || str_contains($st, 'disetujui') || str_contains($st, 'digunakan') || str_contains($st, 'kembali') => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $badgeClass }}">
                                    {{ $p->status_akhir }}
                                </span>
                            </x-table.td>
                        </x-table.tr>
                        @empty
                        <x-table.empty colspan="4" message="Belum ada riwayat peminjaman barang." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>

        </div>
    </div>
</x-app-layout>