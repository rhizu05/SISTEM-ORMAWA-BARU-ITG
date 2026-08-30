<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Aspirasi Mahasiswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Tanggal</th>
                                    <th class="py-2 px-4 border-b text-left">Pengirim</th>
                                    <th class="py-2 px-4 border-b text-left">Aspirasi</th>
                                    <th class="py-2 px-4 border-b text-center">Status</th>
                                    <th class="py-2 px-4 border-b text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($aspirasis as $aspirasi)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $aspirasi->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-2 px-4 border-b">{{ $aspirasi->nama_pengirim }}</td>
                                    <td class="py-2 px-4 border-b">{{ Str::limit($aspirasi->isi_aspirasi, 50) }}</td>
                                    <td class="py-2 px-4 border-b text-center">
                                        @if($aspirasi->status == 'menunggu')
                                            <span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs">Menunggu</span>
                                        @elseif($aspirasi->status == 'ditindaklanjuti')
                                            <span class="px-2 py-1 bg-blue-200 text-blue-800 rounded-full text-xs">Proses</span>
                                        @else
                                            <span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs">Selesai</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-4 border-b text-center">
                                        <a href="{{ route('aspirasi.show', $aspirasi) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Lihat</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-500">Belum ada aspirasi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $aspirasis->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>