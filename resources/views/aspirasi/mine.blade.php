<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Aspirasi Saya') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <p class="text-sm text-gray-600">Lacak status tindak lanjut aspirasi yang Anda kirim.</p>
                    <a href="{{ route('aspirasi.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded">+ Kirim Aspirasi</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-2 border text-left">Judul</th>
                                <th class="p-2 border text-left">Kategori</th>
                                <th class="p-2 border text-left">Tanggal</th>
                                <th class="p-2 border text-center">Status</th>
                                <th class="p-2 border text-left">Tanggapan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($aspirasis as $a)
                            <tr class="border-b">
                                <td class="p-2 border">{{ $a->judul }}</td>
                                <td class="p-2 border">{{ $a->kategori }}</td>
                                <td class="p-2 border">{{ $a->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-2 border text-center">
                                    @php
                                        $statusClass = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'direkap' => 'bg-purple-100 text-purple-800',
                                            'diproses' => 'bg-blue-100 text-blue-800',
                                            'ditindaklanjuti' => 'bg-indigo-100 text-indigo-800',
                                            'selesai' => 'bg-green-100 text-green-800',
                                            'ditolak' => 'bg-red-100 text-red-800',
                                            'tidak_terbukti' => 'bg-gray-200 text-gray-800',
                                        ][$a->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs {{ $statusClass }}">
                                        {{ \Illuminate\Support\Str::of($a->status)->replace('_', ' ')->title() }}
                                    </span>
                                </td>
                                <td class="p-2 border">{{ $a->catatan_bpm ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="p-4 text-center text-gray-500">Belum ada aspirasi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $aspirasis->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
