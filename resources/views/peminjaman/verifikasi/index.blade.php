<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verifikasi Peminjaman Tempat & Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            

            <!-- Antrian Verifikasi Ruangan -->
            @hasanyrole('bkhm|sarpras')
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-slate-900">Antrian Peminjaman Ruangan</h3>
                    <p class="text-xs text-slate-500">Permohonan peminjaman gedung & ruangan yang membutuhkan verifikasi</p>
                </div>
                
                <x-table>
                    <x-table.thead>
                        <tr>
                            <x-table.th>Ormawa</x-table.th>
                            <x-table.th>Kegiatan</x-table.th>
                            <x-table.th>Ruangan</x-table.th>
                            <x-table.th>Waktu Penggunaan</x-table.th>
                            <x-table.th align="center">Aksi</x-table.th>
                        </tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($antrian_tempat as $p)
                        <x-table.tr>
                            <x-table.td>
                                <span class="font-semibold text-slate-900">{{ $p->user->name }}</span>
                            </x-table.td>
                            <x-table.td>
                                <div class="font-medium text-slate-900">{{ $p->nama_kegiatan }}</div>
                                @if($p->file_persetujuan_prodi)
                                    <div class="mt-1">
                                        <a href="{{ route('dokumen.peminjaman-tempat.prodi', $p) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            Surat Prodi (Lampiran)
                                        </a>
                                    </div>
                                @endif
                            </x-table.td>
                            <x-table.td>
                                <span class="text-sm font-medium text-slate-700">{{ $p->ruangan->nama_ruangan }}</span>
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
                                <form action="{{ route('peminjaman.tempat.proses', $p) }}" method="POST" class="flex items-center justify-center gap-2" onsubmit="
                                    if(event.submitter.value === 'tolak') {
                                        let alasan = prompt('Alasan penolakan:');
                                        if(!alasan) return false;
                                        this.catatan.value = alasan;
                                    } else {
                                        return confirm('Setujui peminjaman ini?');
                                    }
                                ">
                                    @csrf
                                    <input type="hidden" name="catatan" value="">
                                    <button type="submit" name="aksi" value="setuju" class="px-3 py-1.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm transition-colors min-h-[32px]">Setujui</button>
                                    <button type="submit" name="aksi" value="tolak" class="px-3 py-1.5 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-lg shadow-sm transition-colors min-h-[32px]">Tolak</button>
                                </form>
                            </x-table.td>
                        </x-table.tr>
                        @empty
                        <x-table.empty colspan="5" message="Tidak ada antrian peminjaman ruangan." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>
            @endhasanyrole

            <!-- Antrian Verifikasi Barang -->
            @hasanyrole('bkhm|sarpras')
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-slate-900">Antrian Peminjaman Barang</h3>
                    <p class="text-xs text-slate-500">Permohonan perlengkapan logistik yang membutuhkan verifikasi sarpras</p>
                </div>
                
                <x-table>
                    <x-table.thead>
                        <tr>
                            <x-table.th>Ormawa</x-table.th>
                            <x-table.th>Kegiatan</x-table.th>
                            <x-table.th>Daftar Barang</x-table.th>
                            <x-table.th>Waktu Penggunaan</x-table.th>
                            <x-table.th align="center">Aksi</x-table.th>
                        </tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($antrian_barang as $p)
                        <x-table.tr>
                            <x-table.td>
                                <span class="font-semibold text-slate-900">{{ $p->user->name }}</span>
                            </x-table.td>
                            <x-table.td>
                                <div class="font-medium text-slate-900">{{ $p->nama_kegiatan }}</div>
                                @if($p->file_persetujuan_prodi)
                                    <div class="mt-1">
                                        <a href="{{ route('dokumen.peminjaman-barang.prodi', $p) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            Surat Prodi (Lampiran)
                                        </a>
                                    </div>
                                @endif
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
                                <form action="{{ route('peminjaman.barang.proses', $p) }}" method="POST" class="flex items-center justify-center gap-2" onsubmit="
                                    if(event.submitter.value === 'tolak') {
                                        let alasan = prompt('Alasan penolakan:');
                                        if(!alasan) return false;
                                        this.catatan.value = alasan;
                                    } else {
                                        return confirm('Setujui peminjaman ini?');
                                    }
                                ">
                                    @csrf
                                    <input type="hidden" name="catatan" value="">
                                    <button type="submit" name="aksi" value="setuju" class="px-3 py-1.5 text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm transition-colors min-h-[32px]">Setujui</button>
                                    <button type="submit" name="aksi" value="tolak" class="px-3 py-1.5 text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white rounded-lg shadow-sm transition-colors min-h-[32px]">Tolak</button>
                                </form>
                            </x-table.td>
                        </x-table.tr>
                        @empty
                        <x-table.empty colspan="5" message="Tidak ada antrian peminjaman barang." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>
            @endhasanyrole

            <!-- BASE-06: Barang Sedang Dipinjam - Validasi Pengembalian -->
            @hasanyrole('bkhm|sarpras|admin')
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-slate-900">Barang Sedang Dipinjam (Validasi Pengembalian)</h3>
                    <p class="text-xs text-slate-500">Daftar peminjaman barang aktif yang menunggu konfirmasi pengembalian unit fisik</p>
                </div>

                <x-table>
                    <x-table.thead>
                        <tr>
                            <x-table.th>Ormawa</x-table.th>
                            <x-table.th>Kegiatan</x-table.th>
                            <x-table.th>Daftar Barang</x-table.th>
                            <x-table.th>Waktu Penggunaan</x-table.th>
                            <x-table.th align="center">Aksi</x-table.th>
                        </tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($barangDipinjam as $p)
                        <x-table.tr>
                            <x-table.td>
                                <span class="font-semibold text-slate-900">{{ $p->user->name }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="font-medium text-slate-900">{{ $p->nama_kegiatan }}</span>
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
                                <form action="{{ route('peminjaman.barang.kembali', $p) }}" method="POST" onsubmit="return confirm('Validasi barang sudah dikembalikan? Stok akan dikembalikan.')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold bg-amber-600 hover:bg-amber-700 text-white rounded-lg shadow-sm transition-colors min-h-[32px]">Validasi Pengembalian</button>
                                </form>
                            </x-table.td>
                        </x-table.tr>
                        @empty
                        <x-table.empty colspan="5" message="Tidak ada barang yang sedang dipinjam." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>
            @endhasanyrole

        </div>
    </div>
</x-app-layout>