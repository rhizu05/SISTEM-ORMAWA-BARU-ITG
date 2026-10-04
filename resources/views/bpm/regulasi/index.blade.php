<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Pusat Regulasi & Pengumuman BPM') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-2">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Daftar Regulasi Terbit</h3>
                    <p class="text-sm text-slate-500">Kumpulan produk hukum, ketetapan, dan pedoman resmi organisasi mahasiswa</p>
                </div>
                <a href="{{ route('bpm.regulasi.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    + Terbitkan Regulasi Baru
                </a>
            </div>

            <x-table>
                <x-table.thead>
                    <tr>
                        <x-table.th>Judul &amp; Kategori</x-table.th>
                        <x-table.th>Deskripsi Singkat</x-table.th>
                        <x-table.th>Tanggal Terbit</x-table.th>
                        <x-table.th align="center">Dokumen</x-table.th>
                        <x-table.th align="center">Aksi</x-table.th>
                    </tr>
                </x-table.thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($regulasis as $r)
                    <x-table.tr>
                        <x-table.td>
                            <div class="font-semibold text-slate-900">{{ $r->judul }}</div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 mt-1">
                                {{ $r->kategori }}
                            </span>
                        </x-table.td>
                        <x-table.td>
                            <span class="text-xs text-slate-600 line-clamp-2">{{ Str::limit($r->deskripsi, 80) }}</span>
                        </x-table.td>
                        <x-table.td>
                            <span class="text-xs text-slate-700 font-medium">{{ $r->tanggal_terbit }}</span>
                        </x-table.td>
                        <x-table.td align="center">
                            <a href="{{ route('informasi.regulasi.unduh', $r) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                PDF
                            </a>
                        </x-table.td>
                        <x-table.td align="center">
                            <form action="{{ route('bpm.regulasi.destroy', $r) }}" method="POST" onsubmit="return confirm('Hapus regulasi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </x-table.td>
                    </x-table.tr>
                    @empty
                    <x-table.empty colspan="5" message="Belum ada regulasi yang diterbitkan." />
                    @endforelse
                </tbody>
            </x-table>

        </div>
    </div>
</x-app-layout>
