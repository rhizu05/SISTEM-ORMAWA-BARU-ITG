<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Manajemen Saldo</h2></x-slot>
    <div class="py-6"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Q-BKHM-02: Periode Anggaran --}}
        <div class="bg-white p-4 rounded shadow mb-6">
            <h3 class="font-bold mb-1">Periode Anggaran</h3>
            <p class="text-xs text-gray-500 mb-3">Periode berjalan (dari rapat pimpinan) — dipakai saat mencatat perubahan saldo.</p>

            @if($periodeAktif)
                <p class="text-sm mb-3">Periode aktif: <span class="font-semibold text-indigo-700">{{ $periodeAktif->nama }}</span> ({{ $periodeAktif->tanggal_mulai->format('d/m/Y') }} – {{ $periodeAktif->tanggal_selesai->format('d/m/Y') }})</p>
            @else
                <p class="text-sm mb-3 text-red-600">Belum ada periode anggaran aktif.</p>
            @endif

            <form action="{{ route('bkhm.periode.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end mb-4">
                @csrf
                <div>
                    <x-input-label for="nama_periode" value="Nama Periode" />
                    <x-text-input id="nama_periode" name="nama" type="text" class="mt-1 block w-full" placeholder="Cth: Anggaran 2026" required />
                </div>
                <div>
                    <x-input-label for="tanggal_mulai" value="Mulai" />
                    <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label for="tanggal_selesai" value="Selesai" />
                    <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="mt-1 block w-full" required />
                </div>
                <div class="flex items-center gap-3">
                    <label class="inline-flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="aktif" value="1" class="rounded border-gray-300 text-indigo-600"> Jadikan aktif
                    </label>
                    <x-primary-button>Simpan</x-primary-button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50"><tr><th class="p-2 border text-left">Nama</th><th class="p-2 border text-left">Rentang</th><th class="p-2 border text-center">Status</th><th class="p-2 border text-center">Aksi</th></tr></thead>
                    <tbody>
                    @forelse($periodes as $p)
                        <tr>
                            <td class="p-2 border">{{ $p->nama }}</td>
                            <td class="p-2 border">{{ $p->tanggal_mulai->format('d/m/Y') }} – {{ $p->tanggal_selesai->format('d/m/Y') }}</td>
                            <td class="p-2 border text-center">
                                @if($p->aktif)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Aktif</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">Nonaktif</span>
                                @endif
                            </td>
                            <td class="p-2 border text-center">
                                @unless($p->aktif)
                                <form action="{{ route('bkhm.periode.aktifkan', $p) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-indigo-600 text-xs hover:underline">Aktifkan</button>
                                </form>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-3 border text-center text-gray-500">Belum ada periode anggaran.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

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
                    <thead class="bg-gray-50"><tr><th class="p-2 border text-left">Waktu</th><th class="p-2 border text-left">Pengguna</th><th class="p-2 border text-left">Aktor</th><th class="p-2 border text-left">Periode</th><th class="p-2 border text-left">Saldo</th><th class="p-2 border text-left">Alasan</th></tr></thead>
                    <tbody>
                    @forelse($saldoHistori as $history)
                        <tr><td class="p-2 border">{{ $history->created_at->format('d/m/Y H:i') }}</td><td class="p-2 border">{{ $history->user->name }}</td><td class="p-2 border">{{ $history->actor->name }}</td><td class="p-2 border">{{ $history->periode->nama ?? '-' }}</td><td class="p-2 border">Rp {{ number_format($history->nominal_sebelum, 0, ',', '.') }} → Rp {{ number_format($history->nominal_sesudah, 0, ',', '.') }}</td><td class="p-2 border">{{ $history->catatan }}</td></tr>
                    @empty
                        <tr><td colspan="6" class="p-4 border text-center text-gray-500">Belum ada riwayat perubahan saldo.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div></div>
</x-app-layout>
