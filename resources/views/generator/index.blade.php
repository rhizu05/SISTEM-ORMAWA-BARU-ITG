<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Generator Dokumen Proposal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4">
                <a href="{{ route('generator.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    + Buat Proposal Otomatis
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Nama Kegiatan</th>
                                <th class="py-2 px-4 border-b text-left text-xs font-semibold text-gray-600 uppercase">Dibuat Pada</th>
                                <th class="py-2 px-4 border-b text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($proposals as $p)
                            <tr>
                                <td class="py-3 px-4 font-semibold">{{ $p->nama_kegiatan }}</td>
                                <td class="py-3 px-4 text-sm">{{ \Carbon\Carbon::parse($p->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="py-3 px-4 text-center space-x-3">
                                    <a href="{{ route('generator.show', $p) }}" class="text-blue-600 hover:underline text-sm">Detail</a>
                                    <a href="{{ route('generator.print', $p) }}" target="_blank" class="text-indigo-600 hover:underline text-sm">Cetak (PDF/Print)</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="py-6 text-center text-gray-500">Belum ada dokumen proposal yang di-generate.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>