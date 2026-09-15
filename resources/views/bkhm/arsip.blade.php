<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Arsip Surat Balasan</h2></x-slot>
    <div class="py-6"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold">Arsip & Penerbitan Nomor Surat</h3>
            <p class="text-xs text-gray-500 mb-3">Seluruh Surat Balasan yang Telah Diterbitkan</p>
            <form method="GET" class="flex gap-2 mb-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="border rounded px-2 py-1 text-sm">
                <button class="bg-indigo-600 text-white px-3 py-1 rounded text-sm">Search</button>
            </form>
            <div class="overflow-x-auto">
            <table class="min-w-full text-sm border">
                <thead class="bg-gray-50"><tr><th class="p-2 border">No</th><th class="p-2 border">Nama Kegiatan</th><th class="p-2 border">Ormawa</th><th class="p-2 border">Nomor Surat</th><th class="p-2 border">Status Terakhir</th><th class="p-2 border">Aksi</th></tr></thead>
                <tbody>
                @forelse($arsip as $i=>$a)
                <tr><td class="p-2 border">{{ $arsip->firstItem()+$i }}</td><td class="p-2 border">{{ $a->nama_kegiatan }}</td><td class="p-2 border">{{ $a->user->name }}</td><td class="p-2 border">{{ $a->nomor_surat }}</td><td class="p-2 border">{{ $a->state->name ?? $a->status_akhir ?? '-' }}</td><td class="p-2 border"><a href="{{ route('verifikasi.show',$a) }}" class="text-indigo-600 text-xs">Lihat</a></td></tr>
                @empty<tr><td colspan="6" class="p-4 text-center text-gray-500">Tidak ada arsip.</td></tr>@endforelse
                </tbody>
            </table>
            </div>
            <div class="mt-3">{{ $arsip->links() }}</div>
        </div>
    </div></div>
</x-app-layout>
