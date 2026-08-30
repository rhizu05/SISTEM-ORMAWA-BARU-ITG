<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Pengajuan Dana (Ormawa)') }}
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
                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <a href="{{ route('pengajuan.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            + Buat Pengajuan Baru
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Nama Kegiatan</th>
                                    <th class="py-2 px-4 border-b text-left">Tanggal</th>
                                    <th class="py-2 px-4 border-b text-left">Dana Diajukan</th>
                                    <th class="py-2 px-4 border-b text-center">Status</th>
                                    <th class="py-2 px-4 border-b text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pengajuans as $pengajuan)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $pengajuan->nama_kegiatan }}</td>
                                    <td class="py-2 px-4 border-b">{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d/m/Y') }}</td>
                                    <td class="py-2 px-4 border-b">Rp {{ number_format($pengajuan->dana_diajukan, 0, ',', '.') }}</td>
                                    <td class="py-2 px-4 border-b text-center">
                                        <span class="px-2 py-1 bg-gray-100 border rounded-full text-xs font-semibold">
                                            {{ $pengajuan->state->label }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border-b text-center space-x-2">
                                        <a href="{{ route('pengajuan.show', $pengajuan) }}" class="text-blue-600 hover:text-blue-900 text-sm">Detail</a>
                                        
                                        @if($pengajuan->state->name === 'draft')
                                            <form action="{{ route('pengajuan.ajukan', $pengajuan) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 text-sm" onclick="return confirm('Yakin ingin mengajukan ke BEM?')">Ajukan</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-500">Belum ada pengajuan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $pengajuans->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>