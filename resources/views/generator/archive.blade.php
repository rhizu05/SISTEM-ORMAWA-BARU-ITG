<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Arsip Digital Persuratan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="mb-6 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Kumpulan Dokumen Digital</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('generator.create') }}" class="bg-indigo-600 text-white px-3 py-2 rounded text-sm">Buat Proposal</a>
                        <a href="{{ route('generator.letters.create') }}" class="bg-green-600 text-white px-3 py-2 rounded text-sm">Buat Surat</a>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8">
                    <!-- Proposal Section -->
                    <div>
                        <h4 class="font-bold text-gray-700 mb-3 border-b pb-2">Proposal & RAB</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kegiatan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($proposals as $p)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $p->nama_kegiatan }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $p->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('generator.lpj.create', $p->id) }}" class="text-green-600 hover:underline text-sm font-medium">Buat LPJ</a>
                                        <a href="{{ route('generator.print', $p) }}" target="_blank" class="text-indigo-600 hover:underline text-sm font-medium">Cetak PDF</a>
                                    </div>
                                </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Tidak ada proposal.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Letters Section -->
                    <div>
                        <h4 class="font-bold text-gray-700 mb-3 border-b pb-2">Surat-Surat Digital</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Perihal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($letters as $l)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $l->perihal }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs rounded bg-gray-100">{{ str_replace('_', ' ', strtoupper($l->type)) }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $l->created_at->format('d M Y') }}</td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('generator.letters.show', $l) }}" target="_blank" class="text-indigo-600 hover:underline">Lihat/Cetak</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">Tidak ada surat.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
