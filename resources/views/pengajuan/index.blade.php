<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Pengajuan Dana') }}
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

                    <div class="flex justify-between items-center mb-6">
                        <a href="{{ route('pengajuan.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            + Buat Pengajuan Baru
                        </a>

                        <!-- Filter Status -->
                        <form method="GET" action="{{ route('pengajuan.index') }}" class="flex items-center gap-2">
                            <label for="status" class="text-sm text-gray-600">Filter Status:</label>
                            <select name="status" id="status" class="border-gray-300 rounded text-sm py-1" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->name }}" {{ request('status') === $state->name ? 'selected' : '' }}>
                                        {{ $state->label }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Nama Kegiatan</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Tanggal</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Dana Diajukan</th>
                                    <th class="py-2 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                                    <th class="py-2 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($pengajuans as $pengajuan)
                                <tr>
                                    <td class="py-3 px-4">{{ $pengajuan->nama_kegiatan }}</td>
                                    <td class="py-3 px-4">{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4">Rp {{ number_format($pengajuan->dana_diajukan, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @php
                                            $badgeClass = 'bg-gray-100 text-gray-800';
                                            if ($pengajuan->state->name === 'rejected') $badgeClass = 'bg-red-100 text-red-800 border border-red-300';
                                            elseif ($pengajuan->state->name === 'completed') $badgeClass = 'bg-green-100 text-green-800';
                                            elseif ($pengajuan->state->name === 'draft') $badgeClass = 'bg-yellow-100 text-yellow-800';
                                            else $badgeClass = 'bg-blue-100 text-blue-800';
                                        @endphp
                                        <span class="px-2 py-1 {{ $badgeClass }} rounded-full text-xs font-semibold">
                                            {{ $pengajuan->state->label }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center space-x-2">
                                        <a href="{{ route('pengajuan.show', $pengajuan) }}" class="text-blue-600 hover:text-blue-900 text-sm font-semibold">Detail</a>
                                        
                                        @if(in_array($pengajuan->state->name, ['draft', 'rejected']))
                                            <a href="{{ route('pengajuan.edit', $pengajuan) }}" class="text-yellow-600 hover:text-yellow-900 text-sm font-semibold">Edit</a>
                                        @endif

                                        @if($pengajuan->state->name === 'draft')
                                            <form action="{{ route('pengajuan.ajukan', $pengajuan) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 text-sm font-semibold ml-1" onclick="return confirm('Yakin ingin mengajukan ke BEM?')">Ajukan</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">
                                        @if(request('status'))
                                            Tidak ada pengajuan dengan status tersebut.
                                        @else
                                            Belum ada pengajuan.
                                        @endif
                                    </td>
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