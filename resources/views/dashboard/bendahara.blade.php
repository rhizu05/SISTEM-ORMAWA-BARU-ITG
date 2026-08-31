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
                <h3 class="font-bold mb-2">Daftar Proposal Siap Dicairkan</h3>
                <p class="text-sm text-gray-500 mb-4">Berikut adalah daftar proposal final yang telah diajukan oleh BKKH dan siap untuk proses transfer dana.</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-2 border">No</th>
                                <th class="p-2 border">Nama Kegiatan</th>
                                <th class="p-2 border">Ormawa</th>
                                <th class="p-2 border">Tanggal Diajukan</th>
                                <th class="p-2 border">Dana Disetujui</th>
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
                                <td class="p-2 border text-center">
                                    <form action="{{ route('bendahara.proses') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="pengajuan_id" value="{{ $p->id }}">
                                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">Proses Cair</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="p-4 text-center text-gray-500 italic">Tidak ada proposal yang siap dicairkan saat ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
