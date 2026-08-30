<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Verifikasi Pengajuan') }}
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
                                    <th class="py-2 px-4 border-b text-left">Ormawa</th>
                                    <th class="py-2 px-4 border-b text-left">Kegiatan</th>
                                    <th class="py-2 px-4 border-b text-left">Tanggal</th>
                                    <th class="py-2 px-4 border-b text-left">Dana</th>
                                    <th class="py-2 px-4 border-b text-center">Status</th>
                                    <th class="py-2 px-4 border-b text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pengajuans as $pengajuan)
                                <tr>
                                    <td class="py-2 px-4 border-b font-semibold">{{ $pengajuan->user->name }}</td>
                                    <td class="py-2 px-4 border-b">{{ $pengajuan->nama_kegiatan }}</td>
                                    <td class="py-2 px-4 border-b">{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d/m/Y') }}</td>
                                    <td class="py-2 px-4 border-b">Rp {{ number_format($pengajuan->dana_diajukan, 0, ',', '.') }}</td>
                                    <td class="py-2 px-4 border-b text-center">
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 border rounded-full text-xs font-semibold">
                                            {{ $pengajuan->state->label }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border-b text-center">
                                        <a href="{{ route('verifikasi.show', $pengajuan) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold border border-indigo-600 px-3 py-1 rounded">Verifikasi</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pengajuan</h3>
                                        <p class="mt-1 text-sm text-gray-500">Saat ini tidak ada pengajuan yang membutuhkan verifikasi Anda.</p>
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