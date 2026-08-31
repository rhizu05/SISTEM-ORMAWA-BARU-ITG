<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Pusat Regulasi & Pengumuman BPM</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold">Daftar Regulasi Terbit</h3>
                <a href="{{ route('bpm.regulasi.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">Terbitkan Regulasi Baru</a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-3 text-left border">Judul & Kategori</th>
                            <th class="p-3 text-left border">Deskripsi Singkat</th>
                            <th class="p-3 text-left border">Tanggal Terbit</th>
                            <th class="p-3 text-center border">File</th>
                            <th class="p-3 text-center border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($regulasis as $r)
                        <tr class="border-b">
                            <td class="p-3 border">
                                <div class="font-bold">{{ $r->judul }}</div>
                                <div class="text-xs text-gray-500">{{ $r->kategori }}</div>
                            </td>
                            <td class="p-3 border">{{ Str::limit($r->deskripsi, 80) }}</td>
                            <td class="p-3 border">{{ $r->tanggal_terbit }}</td>
                            <td class="p-3 border text-center">
                                <a href="{{ asset('storage/'.$r->file_path) }}" target="_blank" class="text-indigo-600 hover:underline">PDF</a>
                            </td>
                            <td class="p-3 border text-center flex justify-center gap-2">
                                <form action="{{ route('bpm.regulasi.destroy', $r) }}" method="POST" onsubmit="return confirm('Hapus regulasi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="p-8 text-center text-gray-500 italic">Belum ada regulasi yang diterbitkan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
