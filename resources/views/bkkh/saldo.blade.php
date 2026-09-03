<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Manajemen Saldo</h2></x-slot>
    <div class="py-6"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold mb-3">Daftar Rincian Saldo Pengguna</h3>
            <p class="text-xs text-gray-500 mb-3">Data Saldo Ormawa, BEM, dan BPM</p>
            <div class="overflow-x-auto">
            <table class="min-w-full text-sm border">
                <thead class="bg-gray-50"><tr><th class="p-2 border">No</th><th class="p-2 border">Nama Ormawa</th><th class="p-2 border">Saldo Awal</th><th class="p-2 border">Total Terpakai & Diproses</th><th class="p-2 border">Sisa Saldo</th><th class="p-2 border">Rincian Kegiatan</th><th class="p-2 border">Aksi</th></tr></thead>
                <tbody>
                @foreach($users as $i=>$u)
                <tr>
                    <td class="p-2 border">{{ $i+1 }}</td>
                    <td class="p-2 border">{{ $u->name }}<br><span class="text-xs bg-gray-100 px-1 rounded">{{ strtoupper($u->roles->first()->name ?? '') }}</span></td>
                    <td class="p-2 border">Rp {{ number_format($u->saldo_awal,0,',','.') }}</td>
                    <td class="p-2 border">Rp {{ number_format($u->total_terpakai,0,',','.') }}</td>
                    <td class="p-2 border font-bold">Rp {{ number_format($u->saldo,0,',','.') }}</td>
                    <td class="p-2 border text-xs">{{ $u->rincian ?: 'Belum ada pengajuan' }}</td>
                    <td class="p-2 border"><a href="{{ route('admin.users.index') }}" class="text-indigo-600 text-xs">Kelola</a></td>
                </tr>
                @endforeach
                </tbody>
            </table>
            </div>
        </div>

        <div class="mt-6 bg-white p-4 rounded shadow">
            <h3 class="font-bold mb-3">Riwayat Perubahan Saldo</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50"><tr><th class="p-2 border text-left">Waktu</th><th class="p-2 border text-left">Pengguna</th><th class="p-2 border text-left">Aktor</th><th class="p-2 border text-left">Saldo</th><th class="p-2 border text-left">Alasan</th></tr></thead>
                    <tbody>
                    @forelse($saldoHistori as $history)
                        <tr><td class="p-2 border">{{ $history->created_at->format('d/m/Y H:i') }}</td><td class="p-2 border">{{ $history->user->name }}</td><td class="p-2 border">{{ $history->actor->name }}</td><td class="p-2 border">Rp {{ number_format($history->nominal_sebelum, 0, ',', '.') }} → Rp {{ number_format($history->nominal_sesudah, 0, ',', '.') }}</td><td class="p-2 border">{{ $history->catatan }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="p-4 border text-center text-gray-500">Belum ada riwayat perubahan saldo.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div></div>
</x-app-layout>
