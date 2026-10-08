<x-app-layout>
    <x-slot name="header"><h2 class="font-bold text-xl text-slate-900 leading-tight">Dashboard BKHM</h2></x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-sm font-semibold text-slate-800">
                Selamat Datang kembali, {{ Auth::user()->name }}!
            </div>

            <!-- Pusat Kendali Administrasi Tertinggi BKHM -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-2xl shadow-sm text-white p-6 border border-slate-800">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/30 text-indigo-200 border border-indigo-400/30 mb-2">
                            <svg class="w-3.5 h-3.5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Otoritas Sistem Institusi</span>
                        </div>
                        <h3 class="text-lg font-bold text-white">Pusat Kendali Administrasi &amp; Kemahasiswaan ITG</h3>
                        <p class="text-xs text-indigo-200 mt-1 max-w-2xl leading-relaxed">BKHM memegang wewenang penuh atas manajemen akun pengguna, alokasi saldo kas ormawa, dan konfigurasi resmi institusi.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-3.5 py-2.5 min-h-[40px] bg-white text-indigo-950 rounded-xl text-xs font-bold shadow-sm hover:bg-indigo-50 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            Manajemen Pengguna
                        </a>
                        <a href="{{ route('admin.konfigurasi.edit') }}" class="inline-flex items-center px-3.5 py-2.5 min-h-[40px] bg-indigo-900/60 text-white border border-indigo-500/50 rounded-xl text-xs font-bold shadow-sm hover:bg-indigo-800 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Konfigurasi Sistem
                        </a>
                        <a href="{{ route('bkhm.saldo.index') }}" class="inline-flex items-center px-3.5 py-2.5 min-h-[40px] bg-indigo-900/60 text-white border border-indigo-500/50 rounded-xl text-xs font-bold shadow-sm hover:bg-indigo-800 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Kelola Saldo
                        </a>
                    </div>
                </div>
            </div>

            <!-- Widget Agenda Rapat & Koordinasi -->
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Agenda Rapat &amp; Koordinasi</h3>
                    <span class="text-xs text-slate-500">{{ count($rapats) }} Agenda Tercatat</span>
                </div>

                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            <x-table.th>Waktu</x-table.th>
                            <x-table.th>Agenda</x-table.th>
                            <x-table.th>Lokasi</x-table.th>
                            <x-table.th>Penyelenggara</x-table.th>
                            <x-table.th align="center">Link</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-sm">
                        @forelse($rapats as $r)
                            <x-table.tr>
                                <x-table.td class="text-xs text-slate-700 whitespace-nowrap">{{ $r->tanggal_rapat }} {{ $r->jam_rapat }}</x-table.td>
                                <x-table.td class="font-bold text-slate-900">{{ $r->judul_rapat }}</x-table.td>
                                <x-table.td class="text-xs text-slate-600">{{ $r->lokasi }}</x-table.td>
                                <x-table.td class="text-xs font-semibold text-slate-700">{{ $r->penyelenggara->name ?? '-' }}</x-table.td>
                                <x-table.td align="center">
                                    @if($r->link_meeting)
                                        <a href="{{ $r->link_meeting }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline font-semibold text-xs">Link</a>
                                    @else
                                        <span class="text-slate-400 text-xs italic">-</span>
                                    @endif
                                </x-table.td>
                            </x-table.tr>
                        @empty 
                            <x-table.empty colspan="5" message="Belum ada agenda rapat terdaftar." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            <!-- Grid Kartu Metrik Antrean BKHM -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
                <!-- Verifikasi Proposal -->
                <a href="{{ route('verifikasi.index') }}" class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-blue-300 transition-all duration-200 p-3.5 flex flex-col justify-between min-h-[136px] focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px]">
                            Tahap 1
                        </span>
                    </div>
                    <div class="my-auto py-1">
                        <div class="text-2xl font-extrabold font-mono text-[#1E40AF] tracking-tight leading-none">{{ $counts['verifikasi_proposal'] }}</div>
                        <div class="text-xs font-bold text-slate-900 mt-1 line-clamp-1">Verifikasi Proposal</div>
                    </div>
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-blue-600 group-hover:text-blue-800 transition-colors">
                        <span>Antrean</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </div>
                </a>

                <!-- Verifikasi LPJ -->
                <a href="{{ route('lpj.index') }}" class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-emerald-300 transition-all duration-200 p-3.5 flex flex-col justify-between min-h-[136px] focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px]">
                            Akuntabilitas
                        </span>
                    </div>
                    <div class="my-auto py-1">
                        <div class="text-2xl font-extrabold font-mono text-emerald-600 tracking-tight leading-none">{{ $counts['verifikasi_lpj'] }}</div>
                        <div class="text-xs font-bold text-slate-900 mt-1 line-clamp-1">Verifikasi LPJ</div>
                    </div>
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-blue-600 group-hover:text-blue-800 transition-colors">
                        <span>Buka Arsip</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </div>
                </a>

                <!-- Kurasi Berita -->
                <a href="{{ route('bkhm.kurasi.index') }}" class="group bg-white rounded-2xl border @if(($counts['kurasi_berita'] ?? 0) > 0) border-amber-300 shadow-[0_4px_16px_rgba(245,158,11,0.10)] @else border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] @endif hover:shadow-md hover:border-amber-400 transition-all duration-200 p-3.5 flex flex-col justify-between min-h-[136px] focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        </div>
                        <span class="px-2 py-0.5 rounded-full @if(($counts['kurasi_berita'] ?? 0) > 0) bg-amber-100 text-amber-800 font-bold @else bg-slate-100 text-slate-600 @endif text-[10px]">
                            Perlu Review
                        </span>
                    </div>
                    <div class="my-auto py-1">
                        <div class="text-2xl font-extrabold font-mono text-amber-600 tracking-tight leading-none">{{ $counts['kurasi_berita'] ?? 0 }}</div>
                        <div class="text-xs font-bold text-slate-900 mt-1 line-clamp-1">Kurasi Berita</div>
                    </div>
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-blue-600 group-hover:text-blue-800 transition-colors">
                        <span>Kurasi</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </div>
                </a>

                <!-- Siap Bendahara -->
                <a href="{{ route('verifikasi.index', ['status' => 'siap_bendahara']) }}" class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-purple-300 transition-all duration-200 p-3.5 flex flex-col justify-between min-h-[136px] focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px]">
                            Pencairan
                        </span>
                    </div>
                    <div class="my-auto py-1">
                        <div class="text-2xl font-extrabold font-mono text-purple-600 tracking-tight leading-none">{{ $counts['siap_bendahara'] }}</div>
                        <div class="text-xs font-bold text-slate-900 mt-1 line-clamp-1">Siap Bendahara</div>
                    </div>
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-purple-600 group-hover:text-purple-800 transition-colors">
                        <span>Antrean Kasir</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </div>
                </a>

                <!-- Verifikasi Tempat -->
                <a href="{{ route('peminjaman.verifikasi.index') }}" class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-emerald-300 transition-all duration-200 p-3.5 flex flex-col justify-between min-h-[136px] focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50/60 border border-emerald-100 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px]">
                            Sarpras
                        </span>
                    </div>
                    <div class="my-auto py-1">
                        <div class="text-2xl font-extrabold font-mono text-emerald-700 tracking-tight leading-none">{{ $counts['verifikasi_tempat'] }}</div>
                        <div class="text-xs font-bold text-slate-900 mt-1 line-clamp-1">Antrean Verifikasi Tempat</div>
                    </div>
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-blue-600 group-hover:text-blue-800 transition-colors">
                        <span>Kelola Slot</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </div>
                </a>

                <!-- Verifikasi Barang -->
                <a href="{{ route('peminjaman.verifikasi.index') }}" class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-slate-300 transition-all duration-200 p-3.5 flex flex-col justify-between min-h-[136px] focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-500">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px]">
                            Inventaris
                        </span>
                    </div>
                    <div class="my-auto py-1">
                        <div class="text-2xl font-extrabold font-mono text-slate-700 tracking-tight leading-none">{{ $counts['verifikasi_barang'] }}</div>
                        <div class="text-xs font-bold text-slate-900 mt-1 line-clamp-1">Antrean Verifikasi Barang</div>
                    </div>
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-blue-600 group-hover:text-blue-800 transition-colors">
                        <span>Kelola Alat</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </div>
                </a>
            </div>

            <!-- Tabel Verifikasi Proposal & Verifikasi LPJ -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Tabel Proposal -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-6 space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                Tabel Verifikasi Proposal (BKHM)
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Proposal kegiatan ormawa menunggu persetujuan institusi</p>
                        </div>
                        <a href="{{ route('verifikasi.index') }}" class="text-xs font-bold text-blue-700 hover:text-blue-900 transition">Semua &rarr;</a>
                    </div>
                    
                    <x-table>
                        <x-table.thead>
                            <x-table.tr>
                                <x-table.th>Nama Kegiatan</x-table.th>
                                <x-table.th>Ormawa</x-table.th>
                                <x-table.th>Dana</x-table.th>
                                <x-table.th align="center">Tindakan</x-table.th>
                            </x-table.tr>
                        </x-table.thead>
                        <tbody class="divide-y divide-slate-100 bg-white text-xs">
                            @forelse($proposalQueue as $p)
                                <x-table.tr>
                                    <x-table.td class="font-bold text-slate-900">{{ $p->nama_kegiatan }}</x-table.td>
                                    <x-table.td class="text-slate-600">{{ $p->user->name }}</x-table.td>
                                    <x-table.td class="font-mono font-extrabold text-slate-900 whitespace-nowrap">Rp {{ number_format($p->dana_diajukan,0,',','.') }}</x-table.td>
                                    <x-table.td align="center" class="whitespace-nowrap">
                                        <a href="{{ route('verifikasi.show',$p) }}" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#0B1528] hover:bg-[#1E3A8A] text-white rounded-lg text-xs font-bold transition min-h-[30px]">
                                            <span>Telaah</span>
                                            <svg class="w-3 h-3 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </x-table.td>
                                </x-table.tr>
                            @empty
                                <x-table.empty colspan="4" message="Tidak ada proposal menunggu verifikasi saat ini." />
                            @endforelse
                        </tbody>
                    </x-table>
                </div>

                <!-- Tabel Antrean LPJ -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-6 space-y-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Antrean Verifikasi LPJ Mahasiswa
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Laporan pertanggungjawaban dana kegiatan</p>
                        </div>
                        <a href="{{ route('lpj.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 transition">Semua LPJ &rarr;</a>
                    </div>
                    
                    <x-table>
                        <x-table.thead>
                            <x-table.tr>
                                <x-table.th>Nama Kegiatan</x-table.th>
                                <x-table.th>Ormawa</x-table.th>
                                <x-table.th align="center">Berkas</x-table.th>
                                <x-table.th align="center">Tindakan</x-table.th>
                            </x-table.tr>
                        </x-table.thead>
                        <tbody class="divide-y divide-slate-100 bg-white text-xs">
                            @forelse($lpjQueue as $p)
                                <x-table.tr>
                                    <x-table.td class="font-bold text-slate-900">{{ $p->nama_kegiatan }}</x-table.td>
                                    <x-table.td class="text-slate-600">{{ $p->user->name }}</x-table.td>
                                    <x-table.td align="center" class="whitespace-nowrap">
                                        @if($p->file_lpj)
                                            <a href="{{ route('dokumen.lpj', $p) }}" target="_blank" 
                                               class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200/80 rounded-md font-bold text-[11px]">
                                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span>PDF</span>
                                            </a>
                                        @else
                                            <span class="text-slate-400 italic">-</span>
                                        @endif
                                    </x-table.td>
                                    <x-table.td align="center" class="whitespace-nowrap">
                                        <a href="{{ route('verifikasi.show', $p) }}" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition min-h-[30px]">
                                            <span>Verifikasi</span>
                                        </a>
                                    </x-table.td>
                                </x-table.tr>
                            @empty
                                <x-table.empty colspan="4" message="Tidak ada berkas LPJ baru menunggu verifikasi." />
                            @endforelse
                        </tbody>
                    </x-table>
                </div>
            </div>

            <!-- Tabel Antrean Verifikasi Tempat -->
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-6 space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">Antrean Peminjaman Tempat (BKHM)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Permohonan izin pemakaian gedung &amp; ruangan kegiatan</p>
                    </div>
                    <a href="{{ route('peminjaman.verifikasi.index') }}" class="text-xs font-bold text-blue-700 hover:text-blue-900 transition">Kelola &rarr;</a>
                </div>

                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            <x-table.th>Ormawa Pengusul</x-table.th>
                            <x-table.th>Nama Kegiatan</x-table.th>
                            <x-table.th>Ruangan / Gedung</x-table.th>
                            <x-table.th>Waktu Penggunaan</x-table.th>
                            <x-table.th align="center">Tindakan</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-xs">
                        @forelse($tempatQueue as $p)
                            <x-table.tr>
                                <x-table.td class="font-bold text-slate-800">{{ $p->user->name }}</x-table.td>
                                <x-table.td class="font-semibold text-slate-900">{{ $p->nama_kegiatan }}</x-table.td>
                                <x-table.td>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 font-medium text-slate-700">
                                        <svg class="w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        <span>{{ $p->ruangan->nama_ruangan ?? '-' }}</span>
                                    </span>
                                </x-table.td>
                                <x-table.td class="text-slate-600 whitespace-nowrap">{{ $p->tgl_mulai }} {{ $p->jam_mulai }}</x-table.td>
                                <x-table.td align="center" class="whitespace-nowrap">
                                    <a href="{{ route('peminjaman.verifikasi.index') }}" 
                                       class="inline-flex items-center justify-center min-h-[36px] px-3.5 py-1.5 bg-[#0B1528] hover:bg-[#1E3A8A] text-white rounded-xl text-xs font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                                        Proses
                                    </a>
                                </x-table.td>
                            </x-table.tr>
                        @empty
                            <x-table.empty colspan="5" message="Tidak ada antrean verifikasi tempat saat ini." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            <!-- Tabel Antrean Verifikasi Barang -->
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-6 space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">Antrean Peminjaman Barang &amp; Alat (BKHM)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Permohonan peminjaman sarana inventaris ormawa</p>
                    </div>
                    <a href="{{ route('peminjaman.verifikasi.index') }}" class="inline-flex items-center min-h-[36px] px-2 text-xs font-bold text-blue-700 hover:text-blue-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 rounded">Kelola &rarr;</a>
                </div>

                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            <x-table.th>Ormawa Pengusul</x-table.th>
                            <x-table.th>Nama Kegiatan</x-table.th>
                            <x-table.th>Daftar Barang</x-table.th>
                            <x-table.th>Mulai Tanggal</x-table.th>
                            <x-table.th align="center">Tindakan</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-xs">
                        @forelse($barangQueue as $p)
                            <x-table.tr>
                                <x-table.td class="font-bold text-slate-800">{{ $p->user->name }}</x-table.td>
                                <x-table.td class="font-semibold text-slate-900">{{ $p->nama_kegiatan }}</x-table.td>
                                <x-table.td class="text-slate-700">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-900 font-medium">
                                        <svg class="w-3.5 h-3.5 text-amber-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        <span>{{ collect($p->kebutuhan_barang)->pluck('nama_barang')->implode(', ') }}</span>
                                    </span>
                                </x-table.td>
                                <x-table.td class="text-slate-600 whitespace-nowrap">{{ $p->tgl_mulai }}</x-table.td>
                                <x-table.td align="center" class="whitespace-nowrap">
                                    <a href="{{ route('peminjaman.verifikasi.index') }}" 
                                       class="inline-flex items-center justify-center min-h-[36px] px-3.5 py-1.5 bg-[#0B1528] hover:bg-[#1E3A8A] text-white rounded-xl text-xs font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                                        Proses
                                    </a>
                                </x-table.td>
                            </x-table.tr>
                        @empty
                            <x-table.empty colspan="5" message="Tidak ada antrean verifikasi barang saat ini." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            <!-- Tabel Riwayat Perubahan Saldo -->
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-6 space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Audit Riwayat Perubahan Saldo Kas</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Log pencatatan mutasi penambahan, pengurangan, dan penyesuaian saldo ormawa</p>
                </div>

                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            <x-table.th>Waktu Pencatatan</x-table.th>
                            <x-table.th>Target Akun</x-table.th>
                            <x-table.th>Aktor Eksekusi</x-table.th>
                            <x-table.th>Perubahan Nominal</x-table.th>
                            <x-table.th>Catatan / Alasan</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-xs">
                        @forelse($saldoHistori as $history)
                            <x-table.tr>
                                <x-table.td class="text-slate-600 whitespace-nowrap">{{ $history->created_at->format('d/m/Y H:i') }}</x-table.td>
                                <x-table.td class="font-bold text-slate-800">{{ $history->user->name }}</x-table.td>
                                <x-table.td class="text-slate-700">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-slate-100 font-semibold text-[11px] text-slate-700">
                                        <svg class="w-3 h-3 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <span>{{ $history->actor->name }}</span>
                                    </span>
                                </x-table.td>
                                <x-table.td class="font-mono whitespace-nowrap">
                                    <span class="text-slate-500">Rp {{ number_format($history->nominal_sebelum, 0, ',', '.') }}</span>
                                    <span class="mx-1 text-slate-400">&rarr;</span>
                                    <span class="font-extrabold text-emerald-600">Rp {{ number_format($history->nominal_sesudah, 0, ',', '.') }}</span>
                                </x-table.td>
                                <x-table.td class="text-slate-600">{{ $history->catatan }}</x-table.td>
                            </x-table.tr>
                        @empty
                            <x-table.empty colspan="5" message="Belum ada riwayat perubahan saldo." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Jadwal Terpadu Fasilitas &amp; Barang</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pemantauan agenda dan ketersediaan sarana prasarana kampus</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 text-xs mt-2 sm:mt-0">
                        <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                            <span class="w-3 h-3 rounded-full bg-indigo-500 shadow-sm ring-2 ring-indigo-100"></span> 
                            Fasilitas
                        </span>
                        <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                            <span class="w-3 h-3 rounded-full bg-amber-500 shadow-sm ring-2 ring-amber-100"></span> 
                            Barang
                        </span>
                    </div>
                </div>
                <x-calendar-style />
                <div class="skin-calendar-wrapper">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
    <script defer src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
        const el=document.getElementById('calendar');
        if(!el) return;
        const cal=new FullCalendar.Calendar(el,{locale:'id',initialView:'dayGridMonth',headerToolbar:{left:'prev,next today',center:'title',right:'dayGridMonth,timeGridWeek'},events:[
            @foreach($calendarTempat as $c){title:'Tempat: {{ $c->ruangan->nama_ruangan ?? $c->nama_kegiatan }}',start:'{{ $c->tgl_mulai }}',end:'{{ \Carbon\Carbon::parse($c->tgl_selesai)->addDay()->format('Y-m-d') }}',color:'#4f46e5'},
            @endforeach
            @foreach($calendarBarang as $c){title:'Barang: {{ $c->nama_kegiatan }}',start:'{{ $c->tgl_mulai }}',end:'{{ \Carbon\Carbon::parse($c->tgl_selesai)->addDay()->format('Y-m-d') }}',color:'#f59e0b'},
            @endforeach
        ]});cal.render();
    });
    </script>
</x-app-layout>
