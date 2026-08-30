<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Pertanggungjawaban (LPJ)') }}
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

                    <div class="mb-4 text-gray-600 text-sm">
                        <p>Daftar kegiatan yang telah dicairkan dan membutuhkan Laporan Pertanggungjawaban (LPJ).</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Nama Kegiatan</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Tanggal Cair</th>
                                    <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Dana Cair</th>
                                    <th class="py-2 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Status LPJ</th>
                                    <th class="py-2 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($pengajuans as $pengajuan)
                                <tr>
                                    <td class="py-3 px-4">{{ $pengajuan->nama_kegiatan }}</td>
                                    <td class="py-3 px-4">{{ $pengajuan->dana ? \Carbon\Carbon::parse($pengajuan->dana->tanggal_cair)->format('d/m/Y') : '-' }}</td>
                                    <td class="py-3 px-4 text-green-600 font-semibold">Rp {{ number_format($pengajuan->dana->nominal_cair ?? $pengajuan->dana_diajukan, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @php
                                            $badgeClass = 'bg-gray-100 text-gray-800';
                                            if ($pengajuan->state->name === 'funds_disbursed') $badgeClass = 'bg-red-100 text-red-800 border border-red-300';
                                            elseif ($pengajuan->state->name === 'completed') $badgeClass = 'bg-green-100 text-green-800 border border-green-300';
                                            else $badgeClass = 'bg-yellow-100 text-yellow-800';
                                        @endphp
                                        <span class="px-2 py-1 {{ $badgeClass }} rounded-full text-xs font-semibold">
                                            @if($pengajuan->state->name === 'funds_disbursed')
                                                Belum Upload
                                            @else
                                                {{ $pengajuan->state->label }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-center space-x-2">
                                        @if($pengajuan->state->name === 'funds_disbursed')
                                            <a href="{{ route('lpj.create', $pengajuan) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold border border-indigo-600 px-3 py-1 rounded">Upload LPJ</a>
                                        @else
                                            <a href="{{ route('pengajuan.show', $pengajuan) }}" class="text-blue-600 hover:text-blue-900 text-sm font-semibold">Detail</a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">
                                        Belum ada data kegiatan untuk LPJ.
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