<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Prestasi & Kompetisi') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-600">Laporkan prestasi/kompetisi Anda (termasuk kegiatan individual/non-afiliasi).</p>
                <a href="{{ route('prestasi.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded">+ Laporkan Prestasi</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="p-2 border text-left">Kegiatan</th>
                                    <th scope="col" class="p-2 border text-left">Pelapor</th>
                                    <th scope="col" class="p-2 border text-left">Tingkat</th>
                                    <th scope="col" class="p-2 border text-left">Juara</th>
                                    <th scope="col" class="p-2 border text-left">Afiliasi</th>
                                    <th scope="col" class="p-2 border text-center">Status</th>
                                    <th scope="col" class="p-2 border text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prestasis as $p)
                                <tr class="border-b">
                                    <td class="p-2 border">{{ $p->nama_kegiatan }}</td>
                                    <td class="p-2 border">{{ $p->user->name ?? '-' }}</td>
                                    <td class="p-2 border">{{ $p->tingkat }}</td>
                                    <td class="p-2 border">{{ $p->juara ?? '-' }}</td>
                                    <td class="p-2 border">{{ ucfirst($p->afiliasi) }}{{ $p->unit_terkait ? ' ('.$p->unit_terkait.')' : '' }}</td>
                                    <td class="p-2 border text-center">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $p->status==='terverifikasi' ? 'bg-green-100 text-green-800' : ($p->status==='ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($p->status) }}
                                        </span>
                                    </td>
                                    <td class="p-2 border text-center space-x-2">
                                        @if($p->file_bukti)
                                            <a href="{{ route('prestasi.bukti', $p) }}" class="text-indigo-600 hover:underline text-xs">Bukti</a>
                                        @endif
                                        @hasanyrole('bkhm|wr3|admin')
                                        @if($p->status === 'pending')
                                        <form action="{{ route('prestasi.verify', $p) }}" method="POST" class="inline-flex gap-1 items-center">
                                            @csrf @method('PATCH')
                                            <input type="text" name="catatan_bkhm" placeholder="Catatan" class="border-gray-300 rounded text-xs w-28">
                                            <button name="status" value="terverifikasi" class="text-green-600 hover:underline text-xs">Verifikasi</button>
                                            <button name="status" value="ditolak" class="text-red-600 hover:underline text-xs">Tolak</button>
                                        </form>
                                        @endif
                                        @endhasanyrole
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="p-4 text-center text-gray-500">Belum ada prestasi dilaporkan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $prestasis->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
