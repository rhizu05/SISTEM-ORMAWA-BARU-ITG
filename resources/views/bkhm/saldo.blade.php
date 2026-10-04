<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Manajemen Saldo Anggaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Q-BKHM-02: Periode Anggaran --}}
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-slate-900">Periode Anggaran</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Periode berjalan (dari rapat pimpinan): dipakai saat mencatat perubahan saldo</p>
                </div>

                @if($periodeAktif)
                    <div class="mb-4 p-3 bg-indigo-50/75 border border-indigo-200/80 rounded-xl text-xs text-indigo-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                        <span>Periode aktif saat ini: <strong class="font-bold">{{ $periodeAktif->nama }}</strong> ({{ $periodeAktif->tanggal_mulai->format('d/m/Y') }} &ndash; {{ $periodeAktif->tanggal_selesai->format('d/m/Y') }})</span>
                    </div>
                @else
                    <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                        <span>Belum ada periode anggaran aktif. Silakan buat atau aktifkan salah satu periode di bawah.</span>
                    </div>
                @endif

                <form action="{{ route('bkhm.periode.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end mb-6 p-4 bg-slate-50/60 rounded-xl border border-slate-200/60">
                    @csrf
                    <div>
                        <x-input-label for="nama_periode" value="Nama Periode" />
                        <x-text-input id="nama_periode" name="nama" type="text" class="mt-1 block w-full rounded-xl" placeholder="Cth: Anggaran 2026" required />
                    </div>
                    <div>
                        <x-input-label for="tanggal_mulai" value="Mulai" />
                        <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" class="mt-1 block w-full rounded-xl" required />
                    </div>
                    <div>
                        <x-input-label for="tanggal_selesai" value="Selesai" />
                        <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="mt-1 block w-full rounded-xl" required />
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="inline-flex items-center text-xs font-medium text-slate-700">
                            <input type="checkbox" name="aktif" value="1" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2">Jadikan aktif</span>
                        </label>
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Simpan
                        </button>
                    </div>
                </form>

                <x-table>
                    <x-table.thead>
                        <tr>
                            <x-table.th>Nama Periode</x-table.th>
                            <x-table.th>Rentang Waktu</x-table.th>
                            <x-table.th align="center">Status</x-table.th>
                            <x-table.th align="center">Aksi</x-table.th>
                        </tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($periodes as $p)
                        <x-table.tr>
                            <x-table.td>
                                <span class="font-semibold text-slate-900">{{ $p->nama }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="text-xs text-slate-600">{{ $p->tanggal_mulai->format('d/m/Y') }} &ndash; {{ $p->tanggal_selesai->format('d/m/Y') }}</span>
                            </x-table.td>
                            <x-table.td align="center">
                                @if($p->aktif)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">Nonaktif</span>
                                @endif
                            </x-table.td>
                            <x-table.td align="center">
                                @unless($p->aktif)
                                <form action="{{ route('bkhm.periode.aktifkan', $p) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">Aktifkan</button>
                                </form>
                                @else
                                    <span class="text-xs text-slate-400">&mdash;</span>
                                @endunless
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.empty colspan="4" message="Belum ada periode anggaran." />
                    @endforelse
                    </tbody>
                </x-table>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Daftar Rincian Saldo Pengguna</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Monitoring pagu anggaran kas Ormawa, BEM, dan BPM</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('bkhm.export.excel') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export Excel
                        </a>
                        <a href="{{ route('bkhm.export.pdf') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Export PDF
                        </a>
                    </div>
                </div>

                <x-table>
                    <x-table.thead>
                        <tr>
                            <x-table.th align="center">No</x-table.th>
                            <x-table.th>Nama Ormawa / Lembaga</x-table.th>
                            <x-table.th align="right">Saldo Awal</x-table.th>
                            <x-table.th align="right">Total Terpakai &amp; Diproses</x-table.th>
                            <x-table.th align="right">Sisa Saldo</x-table.th>
                            <x-table.th>Rincian Kegiatan</x-table.th>
                            <x-table.th align="center">Aksi</x-table.th>
                        </tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100">
                    @foreach($users as $i=>$u)
                    <x-table.tr>
                        <x-table.td align="center">
                            <span class="text-xs text-slate-500 font-medium">{{ $i+1 }}</span>
                        </x-table.td>
                        <x-table.td>
                            <div class="font-semibold text-slate-900">{{ $u->name }}</div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-slate-100 text-slate-600 border border-slate-200 mt-0.5">
                                {{ $u->roles->first()->name ?? '' }}
                            </span>
                        </x-table.td>
                        <x-table.td align="right">
                            <span class="font-mono text-xs text-slate-600">Rp {{ number_format($u->saldo_awal,0,',','.') }}</span>
                        </x-table.td>
                        <x-table.td align="right">
                            <span class="font-mono text-xs font-semibold text-amber-600">Rp {{ number_format($u->total_terpakai,0,',','.') }}</span>
                        </x-table.td>
                        <x-table.td align="right">
                            <span class="font-mono text-xs font-extrabold text-emerald-600">Rp {{ number_format($u->saldo,0,',','.') }}</span>
                        </x-table.td>
                        <x-table.td>
                            <span class="text-xs text-slate-600">{{ $u->rincian ?: 'Belum ada pengajuan' }}</span>
                        </x-table.td>
                        <x-table.td align="center">
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                Kelola
                            </a>
                        </x-table.td>
                    </x-table.tr>
                    @endforeach
                    </tbody>
                </x-table>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <div class="mb-4">
                    <h3 class="font-bold text-slate-900 text-base">Riwayat Perubahan Saldo</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Catatan log audit penyesuaian saldo pagu ormawa</p>
                </div>
                
                <x-table>
                    <x-table.thead>
                        <tr>
                            <x-table.th>Waktu</x-table.th>
                            <x-table.th>Target Akun</x-table.th>
                            <x-table.th>Aktor Eksekusi</x-table.th>
                            <x-table.th>Periode</x-table.th>
                            <x-table.th>Perubahan Nominal</x-table.th>
                            <x-table.th>Alasan / Catatan</x-table.th>
                        </tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($saldoHistori as $history)
                        <x-table.tr>
                            <x-table.td>
                                <span class="text-xs text-slate-600 font-medium">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="font-semibold text-slate-900">{{ $history->user->name }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">{{ $history->actor->name }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="text-xs text-slate-600">{{ $history->periode->nama ?? '-' }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="font-mono text-xs text-slate-500">Rp {{ number_format($history->nominal_sebelum, 0, ',', '.') }}</span>
                                <span class="mx-1 text-slate-400">&rarr;</span>
                                <span class="font-mono font-bold text-xs text-emerald-600">Rp {{ number_format($history->nominal_sesudah, 0, ',', '.') }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="text-xs text-slate-600">{{ $history->catatan }}</span>
                            </x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.empty colspan="6" message="Belum ada riwayat perubahan saldo." />
                    @endforelse
                    </tbody>
                </x-table>
            </div>
        </div>
    </div>
</x-app-layout>
