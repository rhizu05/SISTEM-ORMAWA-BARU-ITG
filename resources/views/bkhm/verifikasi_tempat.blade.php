<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    Verifikasi Tempat (BKHM) - Tahap 1
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Peninjauan izin penggunaan gedung dan ruangan kegiatan ormawa.</p>
            </div>
            <a href="{{ route('peminjaman.verifikasi.index') }}" class="inline-flex items-center px-3.5 py-2 border border-slate-300 text-slate-700 hover:bg-slate-100 rounded-xl text-xs font-semibold shadow-sm transition min-h-[40px]">
                &larr; Verifikasi Terpadu
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-base">Daftar Pengajuan Ruangan</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                        {{ count($antrian) }} Pengajuan
                    </span>
                </div>

                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            <x-table.th align="center" class="w-12">No</x-table.th>
                            <x-table.th>Ormawa</x-table.th>
                            <x-table.th>Kegiatan</x-table.th>
                            <x-table.th>Ruangan</x-table.th>
                            <x-table.th>Waktu Pelaksanaan</x-table.th>
                            <x-table.th align="center">Status BKHM</x-table.th>
                            <x-table.th align="center">Status Sarpras</x-table.th>
                            <x-table.th align="center">Aksi</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($antrian as $i => $p)
                            <x-table.tr>
                                <x-table.td align="center" class="text-xs font-medium text-slate-500">{{ $i + 1 }}</x-table.td>
                                <x-table.td class="font-bold text-slate-900">{{ $p->user->name }}</x-table.td>
                                <x-table.td class="font-semibold text-slate-800">{{ $p->nama_kegiatan }}</x-table.td>
                                <x-table.td>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700">
                                        {{ $p->ruangan->nama_ruangan ?? '-' }}
                                    </span>
                                </x-table.td>
                                <x-table.td class="text-xs text-slate-600 whitespace-nowrap">
                                    {{ $p->tgl_mulai }} {{ $p->jam_mulai }} s/d {{ $p->tgl_selesai }} {{ $p->jam_selesai }}
                                </x-table.td>
                                <x-table.td align="center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $p->status_bkhm === 'disetujui' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($p->status_bkhm === 'ditolak' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-800 border border-amber-200') }}">
                                        {{ ucfirst($p->status_bkhm ?? 'Menunggu') }}
                                    </span>
                                </x-table.td>
                                <x-table.td align="center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $p->status_sarpras === 'disetujui' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($p->status_sarpras === 'ditolak' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                        {{ ucfirst($p->status_sarpras ?? 'Menunggu') }}
                                    </span>
                                </x-table.td>
                                <x-table.td align="center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <form method="POST" action="{{ route('peminjaman.tempat.proses', $p) }}">
                                            @csrf
                                            <input type="hidden" name="aksi" value="setuju">
                                            <button class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition min-h-[32px]">
                                                Setuju
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('peminjaman.tempat.proses', $p) }}">
                                            @csrf
                                            <input type="hidden" name="aksi" value="tolak">
                                            <input type="hidden" name="catatan" value="Ditolak BKHM">
                                            <button class="inline-flex items-center px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold shadow-sm transition min-h-[32px]">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                </x-table.td>
                            </x-table.tr>
                        @empty
                            <x-table.empty colspan="8" message="Belum ada pengajuan peminjaman tempat." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>
        </div>
    </div>
</x-app-layout>
