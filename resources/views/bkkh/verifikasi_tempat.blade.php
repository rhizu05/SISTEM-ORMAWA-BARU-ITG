<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Verifikasi Peminjaman Tempat (BKKH) - Tahap 1</h2></x-slot>
    <div class="py-6"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold mb-3">Daftar Pengajuan Ruangan</h3>
            <div class="overflow-x-auto">
            <table class="min-w-full text-sm border">
                <thead class="bg-gray-50"><tr><th class="p-2 border">No</th><th class="p-2 border">Ormawa</th><th class="p-2 border">Kegiatan</th><th class="p-2 border">Ruangan</th><th class="p-2 border">Waktu Pelaksanaan</th><th class="p-2 border">Status BKKH</th><th class="p-2 border">Status Sarpras</th><th class="p-2 border">Aksi</th></tr></thead>
                <tbody>
                @forelse($antrian as $i=>$p)
                <tr><td class="p-2 border">{{ $i+1 }}</td><td class="p-2 border">{{ $p->user->name }}</td><td class="p-2 border">{{ $p->nama_kegiatan }}</td><td class="p-2 border">{{ $p->ruangan->nama_ruangan ?? '-' }}</td><td class="p-2 border">{{ $p->tgl_mulai }} {{ $p->jam_mulai }} s/d {{ $p->tgl_selesai }} {{ $p->jam_selesai }}</td><td class="p-2 border">{{ $p->status_bkkh }}</td><td class="p-2 border">{{ $p->status_sarpras }}</td><td class="p-2 border flex gap-1">
                    <form method="POST" action="{{ route('peminjaman.tempat.proses',$p) }}">@csrf<input type="hidden" name="aksi" value="setuju"><button class="bg-green-600 text-white px-2 py-1 text-xs rounded">Setuju</button></form>
                    <form method="POST" action="{{ route('peminjaman.tempat.proses',$p) }}">@csrf<input type="hidden" name="aksi" value="tolak"><input type="hidden" name="catatan" value="Ditolak BKKH"><button class="bg-red-600 text-white px-2 py-1 text-xs rounded">Tolak</button></form>
                </td></tr>
                @empty<tr><td colspan="8" class="p-4 text-center text-gray-500">Belum ada pengajuan peminjaman tempat.</td></tr>@endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div></div>
</x-app-layout>
