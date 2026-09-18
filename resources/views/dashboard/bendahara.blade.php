<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Dashboard Bendahara</h2></x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-4 rounded shadow">Selamat Datang kembali, Bendahara!</div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-6 rounded shadow text-center border-l-4 border-indigo-500">
                    <div class="text-3xl font-bold">{{ $stats['siap_cair'] }}</div>
                    <div class="text-sm text-gray-600">Proposal Siap Cair</div>
                </div>
                <div class="bg-white p-6 rounded shadow text-center border-l-4 border-green-500">
                    <div class="text-3xl font-bold">Rp {{ number_format($stats['total_dicairkan'], 0, ',', '.') }}</div>
                    <div class="text-sm text-gray-600">Total Dana Dicairkan</div>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <div class="flex flex-wrap justify-between items-center gap-2 mb-2">
                    <h3 class="font-bold">Daftar Proposal Siap Dicairkan</h3>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('bendahara.export.excel') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-3 py-2 rounded flex items-center gap-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Unduh Excel (.xlsx)
                        </a>
                        <a href="{{ route('bendahara.export.pdf') }}" class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold px-3 py-2 rounded flex items-center gap-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Unduh PDF Rekap
                        </a>
                        <a href="{{ route('bendahara.export') }}" class="bg-gray-600 hover:bg-gray-700 text-white text-xs font-semibold px-3 py-2 rounded">
                            CSV
                        </a>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mb-4">Berikut adalah daftar proposal final yang telah diajukan oleh BKHM dan siap untuk proses transfer dana.</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-2 border">No</th>
                                <th class="p-2 border">Nama Kegiatan</th>
                                <th class="p-2 border">Ormawa</th>
                                <th class="p-2 border">Tanggal Diajukan</th>
                                <th class="p-2 border">Dana Disetujui</th>
                                <th class="p-2 border">Termin</th>
                                <th class="p-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siapCairQueue as $i => $p)
                            <tr class="border-b">
                                <td class="p-2 border text-center">{{ $i + 1 }}</td>
                                <td class="p-2 border">{{ $p->nama_kegiatan }}</td>
                                <td class="p-2 border">{{ $p->user->name }}</td>
                                <td class="p-2 border">{{ $p->tanggal_pengajuan ?? $p->created_at->format('d M Y') }}</td>
                                <td class="p-2 border">Rp {{ number_format($p->dana_diajukan, 0, ',', '.') }}</td>
                                <td class="p-2 border text-center">Termin ke-{{ $p->terminBerikutnya() }}</td>
                                <td class="p-2 border text-center">
                                    <a href="{{ route('verifikasi.show', $p) }}" class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 inline-block">Proses Cair</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="p-4 text-center text-gray-500 italic">Tidak ada proposal yang siap dicairkan saat ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
