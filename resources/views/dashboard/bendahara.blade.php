<x-app-layout>
    <x-slot name="header"><h2 class="font-bold text-xl text-slate-900 leading-tight">Dashboard Bendahara</h2></x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert Messages -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Banner Selamat Datang Pimpinan / Bendahara (Slate-Indigo) -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 rounded-2xl shadow-sm border border-slate-800">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-400/30">
                                Unit Keuangan &amp; Perbendaharaan Kampus
                            </span>
                            <span class="text-xs text-slate-400">{{ now()->translatedFormat('l, d F Y') }}</span>
                        </div>
                        <h3 class="text-xl font-bold tracking-tight text-white">Selamat Datang, Bendahara Institut Teknologi Garut</h3>
                        <p class="text-xs text-slate-300 max-w-2xl leading-relaxed">
                            Kelola validasi transfer dana ormawa, terbitkan slip pencairan termin resmi, serta pastikan pemotongan saldo kas ormawa terekam secara atomik dan akuntabel.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('verifikasi.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow-sm transition min-h-[44px] focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span>Antrean Verifikasi Proposal</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Kartu Metrik Ringkasan Finansial -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Proposal Siap Cair</p>
                        <h4 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $stats['siap_cair'] }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Proposal tervalidasi BKHM/WR3 menunggu transfer</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg flex-shrink-0 border border-amber-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Dana Dicairkan</p>
                        <h4 class="text-2xl font-extrabold text-emerald-700 mt-1 font-mono">Rp {{ number_format($stats['total_dicairkan'], 0, ',', '.') }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Akumulasi realisasi pencairan kas ormawa</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg flex-shrink-0 border border-emerald-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Tabel Daftar Proposal Siap Dicairkan -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Daftar Proposal Siap Dicairkan</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Berikut adalah daftar proposal final yang telah berstatus resmi siap transfer dana.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('bendahara.export.excel') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold px-3.5 py-2 rounded-xl inline-flex items-center gap-1.5 shadow-sm transition min-h-[44px] focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Unduh Excel (.xlsx)</span>
                        </a>
                        <a href="{{ route('bendahara.export.pdf') }}" class="bg-rose-700 hover:bg-rose-800 text-white text-xs font-semibold px-3.5 py-2 rounded-xl inline-flex items-center gap-1.5 shadow-sm transition min-h-[44px] focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            <span>Unduh PDF Rekap</span>
                        </a>
                        <a href="{{ route('bendahara.export') }}" class="bg-slate-700 hover:bg-slate-800 text-white text-xs font-semibold px-3.5 py-2 rounded-xl inline-flex items-center gap-1.5 transition min-h-[44px] focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Ekspor Rekap Pencairan (CSV)</span>
                        </a>
                    </div>
                </div>

                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            <x-table.th align="center" class="w-12">No</x-table.th>
                            <x-table.th>Nama Kegiatan</x-table.th>
                            <x-table.th>Ormawa</x-table.th>
                            <x-table.th>Tanggal Pengajuan</x-table.th>
                            <x-table.th align="right">Dana Disetujui</x-table.th>
                            <x-table.th align="center">Termin</x-table.th>
                            <x-table.th align="center">Aksi</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($siapCairQueue as $i => $p)
                        <x-table.tr>
                            <x-table.td align="center" class="text-xs font-medium text-slate-500">{{ $i + 1 }}</x-table.td>
                            <x-table.td class="font-semibold text-slate-900">{{ $p->nama_kegiatan }}</x-table.td>
                            <x-table.td class="text-slate-700">{{ $p->user->name }}</x-table.td>
                            <x-table.td class="text-slate-600 text-xs whitespace-nowrap">{{ $p->tanggal_pengajuan ?? $p->created_at->format('d/m/Y') }}</x-table.td>
                            <x-table.td align="right" class="font-mono font-bold text-slate-900 whitespace-nowrap">Rp {{ number_format($p->dana_diajukan, 0, ',', '.') }}</x-table.td>
                            <x-table.td align="center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    Termin ke-{{ $p->terminBerikutnya() }}
                                </span>
                            </x-table.td>
                            <x-table.td align="center">
                                <a href="{{ route('verifikasi.show', $p) }}" class="inline-flex items-center justify-center px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition min-h-[36px] focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                                    Proses Pencairan
                                </a>
                            </x-table.td>
                        </x-table.tr>
                        @empty
                        <x-table.empty colspan="7" message="Tidak ada proposal yang siap dicairkan saat ini." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            <!-- Tabel Riwayat Pencairan Yang Telah Selesai -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Riwayat Transaksi Pencairan Terkini</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Histori transfer pendanaan kegiatan ormawa yang telah berhasil dibayarkan.</p>
                    </div>
                </div>

                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            <x-table.th align="center" class="w-12">No</x-table.th>
                            <x-table.th align="center">Tanggal Cair</x-table.th>
                            <x-table.th>Kegiatan Terbayar</x-table.th>
                            <x-table.th>Lembaga Pengusul</x-table.th>
                            <x-table.th align="center">Periode Pencairan</x-table.th>
                            <x-table.th align="right">Nominal Dicairkan</x-table.th>
                            <x-table.th>Catatan / Bukti</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($riwayatPencairan ?? [] as $i => $dana)
                        <x-table.tr>
                            <x-table.td align="center" class="text-xs font-medium text-slate-500">{{ $i + 1 }}</x-table.td>
                            <x-table.td align="center" class="text-slate-600 text-xs whitespace-nowrap">{{ \Carbon\Carbon::parse($dana->tanggal_cair)->format('d/m/Y') }}</x-table.td>
                            <x-table.td class="font-semibold text-slate-900">{{ $dana->pengajuan->nama_kegiatan ?? '-' }}</x-table.td>
                            <x-table.td class="text-slate-700">{{ $dana->pengajuan->user->name ?? '-' }}</x-table.td>
                            <x-table.td align="center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-indigo-50 text-indigo-700 font-semibold border border-indigo-200">
                                    Termin {{ $dana->termin_ke }}
                                </span>
                            </x-table.td>
                            <x-table.td align="right" class="font-mono font-extrabold text-emerald-700 whitespace-nowrap">Rp {{ number_format($dana->nominal_cair, 0, ',', '.') }}</x-table.td>
                            <x-table.td class="text-slate-600">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="truncate max-w-xs">{{ $dana->catatan ?? '-' }}</span>
                                    @if($dana->bukti_transfer)
                                        <a href="{{ route('dokumen.bukti-transfer', $dana) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-semibold text-[11px] underline flex-shrink-0">
                                            Lihat Bukti &nearr;
                                        </a>
                                    @endif
                                </div>
                            </x-table.td>
                        </x-table.tr>
                        @empty
                        <x-table.empty colspan="7" message="Belum ada riwayat transaksi pencairan dana tercatat." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>

        </div>
    </div>
</x-app-layout>
