<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Peminjaman Tempat & Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex gap-4 mb-4">
                <a href="{{ route('peminjaman.tempat.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    + Ajukan Peminjaman Ruangan
                </a>
                <a href="{{ route('peminjaman.barang.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + Ajukan Peminjaman Barang
                </a>
            </div>

            <!-- Tab: Peminjaman Tempat -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Riwayat Peminjaman Ruangan</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Kegiatan</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Ruangan</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Waktu</th>
                                    <th class="py-2 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($peminjaman_tempat as $p)
                                <tr>
                                    <td class="py-3 px-4 font-semibold">{{ $p->nama_kegiatan }}</td>
                                    <td class="py-3 px-4">{{ $p->ruangan->nama_ruangan }}</td>
                                    <td class="py-3 px-4 text-sm">
                                        {{ \Carbon\Carbon::parse($p->tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($p->tgl_selesai)->format('d/m/Y') }}<br>
                                        <span class="text-gray-500">{{ \Carbon\Carbon::parse($p->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($p->jam_selesai)->format('H:i') }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ str_contains(strtolower($p->status_akhir), 'tolak') ? 'bg-red-100 text-red-800' : (str_contains(strtolower($p->status_akhir), 'selesai') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $p->status_akhir }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="py-4 text-center text-gray-500">Belum ada riwayat peminjaman ruangan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab: Peminjaman Barang -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Riwayat Peminjaman Barang</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Kegiatan</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Barang Dipinjam</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Waktu</th>
                                    <th class="py-2 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($peminjaman_barang as $p)
                                <tr>
                                    <td class="py-3 px-4 font-semibold">{{ $p->nama_kegiatan }}</td>
                                    <td class="py-3 px-4 text-sm">
                                        <ul class="list-disc pl-4">
                                            @foreach($p->kebutuhan_barang as $brg)
                                                <li>{{ $brg['nama_barang'] }} ({{ $brg['qty'] }})</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="py-3 px-4 text-sm">
                                        {{ \Carbon\Carbon::parse($p->tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($p->tgl_selesai)->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ str_contains(strtolower($p->status_akhir), 'tolak') ? 'bg-red-100 text-red-800' : (str_contains(strtolower($p->status_akhir), 'selesai') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $p->status_akhir }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="py-4 text-center text-gray-500">Belum ada riwayat peminjaman barang.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>