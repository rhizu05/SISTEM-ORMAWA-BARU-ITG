<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verifikasi Peminjaman Tempat & Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Antrian Verifikasi Ruangan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Antrian Peminjaman Ruangan</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Ormawa</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Kegiatan</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Ruangan</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Waktu</th>
                                    <th class="py-2 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($antrian_tempat as $p)
                                <tr>
                                    <td class="py-3 px-4 font-semibold">{{ $p->user->name }}</td>
                                    <td class="py-3 px-4">{{ $p->nama_kegiatan }}</td>
                                    <td class="py-3 px-4 text-sm">{{ $p->ruangan->nama_ruangan }}</td>
                                    <td class="py-3 px-4 text-sm">
                                        {{ \Carbon\Carbon::parse($p->tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($p->tgl_selesai)->format('d/m/Y') }}<br>
                                        <span class="text-gray-500">{{ \Carbon\Carbon::parse($p->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($p->jam_selesai)->format('H:i') }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <form action="{{ route('peminjaman.tempat.proses', $p) }}" method="POST" class="flex items-center gap-2" onsubmit="
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
                                            <button type="submit" name="aksi" value="setuju" class="text-xs bg-green-600 hover:bg-green-700 text-white py-1 px-3 rounded">Setuju</button>
                                            <button type="submit" name="aksi" value="tolak" class="text-xs bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded">Tolak</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="py-4 text-center text-gray-500">Tidak ada antrian peminjaman ruangan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Antrian Verifikasi Barang -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Antrian Peminjaman Barang</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Ormawa</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Kegiatan</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Daftar Barang</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                                    <th class="py-2 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($antrian_barang as $p)
                                <tr>
                                    <td class="py-3 px-4 font-semibold">{{ $p->user->name }}</td>
                                    <td class="py-3 px-4">{{ $p->nama_kegiatan }}</td>
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
                                        <form action="{{ route('peminjaman.barang.proses', $p) }}" method="POST" class="flex items-center gap-2" onsubmit="
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
                                            <button type="submit" name="aksi" value="setuju" class="text-xs bg-green-600 hover:bg-green-700 text-white py-1 px-3 rounded">Setuju</button>
                                            <button type="submit" name="aksi" value="tolak" class="text-xs bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded">Tolak</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="py-4 text-center text-gray-500">Tidak ada antrian peminjaman barang.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>