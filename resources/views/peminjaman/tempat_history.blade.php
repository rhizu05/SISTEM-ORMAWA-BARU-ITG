<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Peminjaman Tempat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <p class="text-sm text-gray-600">Menampilkan riwayat peminjaman tempat milik Anda.</p>
                <a href="{{ route('peminjaman.tempat.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">
                    + Ajukan Peminjaman Tempat
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Kegiatan</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Ruangan</th>
                                    <th class="py-3 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Waktu</th>
                                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Status BKKH</th>
                                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Status Sarpras</th>
                                    <th class="py-3 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Status Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($peminjaman_tempat as $p)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4">
                                        <div class="font-semibold">{{ $p->nama_kegiatan }}</div>
                                        <div class="text-xs text-gray-500">{{ $p->deskripsi_kegiatan }}</div>
                                    </td>
                                    <td class="py-3 px-4">{{ $p->ruangan->nama_ruangan ?? '-' }}</td>
                                    <td class="py-3 px-4 text-sm">
                                        {{ \Carbon\Carbon::parse($p->tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($p->tgl_selesai)->format('d/m/Y') }}<br>
                                        <span class="text-gray-500">{{ \Carbon\Carbon::parse($p->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($p->jam_selesai)->format('H:i') }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $p->status_bkkh==='disetujui' ? 'bg-green-100 text-green-800' : ($p->status_bkkh==='ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($p->status_bkkh) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $p->status_sarpras==='disetujui' ? 'bg-green-100 text-green-800' : ($p->status_sarpras==='ditolak' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600') }}">
                                            {{ ucfirst($p->status_sarpras) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ str_contains(strtolower($p->status_akhir), 'tolak') ? 'bg-red-100 text-red-800' : (str_contains(strtolower($p->status_akhir), 'selesai') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $p->status_akhir }}
                                        </span>
                                        @if($p->catatan_penolakan)
                                            <div class="text-xs text-red-600 mt-1">{{ $p->catatan_penolakan }}</div>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="py-6 text-center text-gray-500">Belum ada riwayat peminjaman tempat.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
