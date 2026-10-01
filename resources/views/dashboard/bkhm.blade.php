<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Dashboard BKHM</h2></x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-4 rounded shadow">Selamat Datang kembali, {{ Auth::user()->name }}!</div>

            <!-- Pusat Kendali Administrasi Tertinggi BKHM -->
            <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-indigo-700 rounded-lg shadow text-white p-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/40 text-indigo-100 border border-indigo-400/30 mb-2">
                            <span>🛡️ Otoritas Sistem Tertinggi</span>
                        </div>
                        <h3 class="text-lg font-bold text-white">Pusat Kendali Administrasi & Kemahasiswaan ITG</h3>
                        <p class="text-xs text-indigo-200 mt-1">BKHM memegang wewenang penuh atas manajemen akun pengguna, alokasi saldo ormawa, dan konfigurasi resmi institusi.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-3 py-2 bg-white text-indigo-900 rounded-lg text-xs font-bold shadow-sm hover:bg-indigo-50 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            Manajemen Pengguna
                        </a>
                        <a href="{{ route('admin.konfigurasi.edit') }}" class="inline-flex items-center px-3 py-2 bg-indigo-800 text-white border border-indigo-500 rounded-lg text-xs font-bold shadow-sm hover:bg-indigo-600 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Konfigurasi Sistem
                        </a>
                        <a href="{{ route('bkhm.saldo.index') }}" class="inline-flex items-center px-3 py-2 bg-indigo-800 text-white border border-indigo-500 rounded-lg text-xs font-bold shadow-sm hover:bg-indigo-600 transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Kelola Saldo
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-3">Agenda Rapat & Koordinasi</h3>
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50"><tr><th class="p-2 border">Waktu</th><th class="p-2 border">Agenda</th><th class="p-2 border">Lokasi</th><th class="p-2 border">Penyelenggara</th><th class="p-2 border">Link</th></tr></thead>
                    <tbody>
                    @forelse($rapats as $r)
                    <tr><td class="p-2 border">{{ $r->tanggal_rapat }} {{ $r->jam_rapat }}</td><td class="p-2 border">{{ $r->judul_rapat }}</td><td class="p-2 border">{{ $r->lokasi }}</td><td class="p-2 border">{{ $r->penyelenggara->name ?? '-' }}</td><td class="p-2 border">@if($r->link_meeting)<a href="{{ $r->link_meeting }}" target="_blank" class="text-indigo-600 underline">Link</a>@else - @endif</td></tr>
                    @empty <tr><td colspan="5" class="p-4 text-center text-gray-500">Belum ada agenda rapat terdaftar.</td></tr> @endforelse
                    </tbody>
                </table>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
                <!-- Verifikasi Proposal -->
                <a href="{{ route('verifikasi.index') }}" class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-blue-300 transition-all duration-200 p-3.5 flex flex-col justify-between min-h-[136px]">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <span>📑</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px]">
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
                <a href="{{ route('lpj.index') }}" class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-emerald-300 transition-all duration-200 p-3.5 flex flex-col justify-between min-h-[136px]">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <span>📋</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px]">
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
                <a href="{{ route('bkhm.kurasi.index') }}" class="group bg-white rounded-2xl border @if(($counts['kurasi_berita'] ?? 0) > 0) border-amber-300 shadow-[0_4px_16px_rgba(245,158,11,0.10)] @else border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] @endif hover:shadow-md hover:border-amber-400 transition-all duration-200 p-3.5 flex flex-col justify-between min-h-[136px]">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <span>📰</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full @if(($counts['kurasi_berita'] ?? 0) > 0) bg-amber-100 text-amber-800 font-bold @else bg-slate-100 text-slate-500 @endif text-[10px]">
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
                <div class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md transition-all duration-200 p-3.5 flex flex-col justify-between min-h-[136px] cursor-default">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-sm shadow-2xs">
                            <span>💰</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px]">
                            Pencairan
                        </span>
                    </div>
                    <div class="my-auto py-1">
                        <div class="text-2xl font-extrabold font-mono text-purple-600 tracking-tight leading-none">{{ $counts['siap_bendahara'] }}</div>
                        <div class="text-xs font-bold text-slate-900 mt-1 line-clamp-1">Siap Bendahara</div>
                    </div>
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-purple-600">
                        <span>Antrean Kasir</span>
                        <span>&rarr;</span>
                    </div>
                </div>

                <!-- Verifikasi Tempat -->
                <a href="{{ route('peminjaman.verifikasi.index') }}" class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-emerald-300 transition-all duration-200 p-3.5 flex flex-col justify-between min-h-[136px]">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50/60 border border-emerald-100 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <span>🏢</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px]">
                            Sarpras
                        </span>
                    </div>
                    <div class="my-auto py-1">
                        <div class="text-2xl font-extrabold font-mono text-emerald-700 tracking-tight leading-none">{{ $counts['verifikasi_tempat'] }}</div>
                        <div class="text-xs font-bold text-slate-900 mt-1 line-clamp-1">Verifikasi Tempat</div>
                    </div>
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-blue-600 group-hover:text-blue-800 transition-colors">
                        <span>Kelola Slot</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </div>
                </a>

                <!-- Verifikasi Barang -->
                <a href="{{ route('peminjaman.verifikasi.index') }}" class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-slate-300 transition-all duration-200 p-3.5 flex flex-col justify-between min-h-[136px]">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <span>📦</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px]">
                            Inventaris
                        </span>
                    </div>
                    <div class="my-auto py-1">
                        <div class="text-2xl font-extrabold font-mono text-slate-700 tracking-tight leading-none">{{ $counts['verifikasi_barang'] }}</div>
                        <div class="text-xs font-bold text-slate-900 mt-1 line-clamp-1">Verifikasi Barang</div>
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
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                Antrean Verifikasi Proposal (BKHM)
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Proposal kegiatan ormawa menunggu persetujuan institusi</p>
                        </div>
                        <a href="{{ route('verifikasi.index') }}" class="text-xs font-bold text-blue-700 hover:text-blue-900 transition">Semua &rarr;</a>
                    </div>
                    <div class="overflow-x-auto skin-scrollbar">
                        <table class="min-w-full divide-y divide-slate-100 text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider select-none">
                                    <th scope="col" class="py-2.5 px-4">Nama Kegiatan</th>
                                    <th scope="col" class="py-2.5 px-4">Ormawa</th>
                                    <th scope="col" class="py-2.5 px-4">Dana</th>
                                    <th scope="col" class="py-2.5 px-4 text-center">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse($proposalQueue as $p)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-4 font-bold text-slate-900">{{ $p->nama_kegiatan }}</td>
                                        <td class="py-3 px-4 text-slate-600">{{ $p->user->name }}</td>
                                        <td class="py-3 px-4 font-mono font-extrabold text-slate-900">Rp {{ number_format($p->dana_diajukan,0,',','.') }}</td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <a href="{{ route('verifikasi.show',$p) }}" 
                                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#0B1528] hover:bg-[#1E3A8A] text-white rounded-lg text-xs font-bold transition">
                                                <span>Telaah</span>
                                                <svg class="w-3 h-3 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-slate-400 text-xs italic">
                                            Tidak ada proposal menunggu verifikasi saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabel Antrean LPJ -->
                <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Antrean Verifikasi LPJ Mahasiswa
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Laporan pertanggungjawaban dana kegiatan</p>
                        </div>
                        <a href="{{ route('lpj.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 transition">Semua LPJ &rarr;</a>
                    </div>
                    <div class="overflow-x-auto skin-scrollbar">
                        <table class="min-w-full divide-y divide-slate-100 text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider select-none">
                                    <th scope="col" class="py-2.5 px-4">Nama Kegiatan</th>
                                    <th scope="col" class="py-2.5 px-4">Ormawa</th>
                                    <th scope="col" class="py-2.5 px-4 text-center">Berkas</th>
                                    <th scope="col" class="py-2.5 px-4 text-center">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse($lpjQueue as $p)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-4 font-bold text-slate-900">{{ $p->nama_kegiatan }}</td>
                                        <td class="py-3 px-4 text-slate-600">{{ $p->user->name }}</td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            @if($p->file_lpj)
                                                <a href="{{ route('dokumen.lpj', $p) }}" target="_blank" 
                                                   class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200/80 rounded-md font-bold text-[11px]">
                                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    <span>PDF</span>
                                                </a>
                                            @else
                                                <span class="text-slate-400 italic">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <a href="{{ route('verifikasi.show', $p) }}" 
                                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">
                                                <span>Verifikasi</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-slate-400 text-xs italic">
                                            Tidak ada berkas LPJ baru menunggu verifikasi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tabel Antrean Verifikasi Tempat -->
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">Antrean Peminjaman Tempat (BKHM)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Permohonan izin pemakaian gedung &amp; ruangan kegiatan</p>
                    </div>
                    <a href="{{ route('peminjaman.verifikasi.index') }}" class="text-xs font-bold text-blue-700 hover:text-blue-900 transition">Kelola &rarr;</a>
                </div>
                <div class="overflow-x-auto skin-scrollbar">
                    <table class="min-w-full divide-y divide-slate-100 text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider select-none">
                                <th scope="col" class="py-3 px-5">Ormawa Pengusul</th>
                                <th scope="col" class="py-3 px-5">Nama Kegiatan</th>
                                <th scope="col" class="py-3 px-5">Ruangan / Gedung</th>
                                <th scope="col" class="py-3 px-5">Waktu Penggunaan</th>
                                <th scope="col" class="py-3 px-5 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($tempatQueue as $p)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-5 font-bold text-slate-800">{{ $p->user->name }}</td>
                                    <td class="py-3 px-5 font-semibold text-slate-900">{{ $p->nama_kegiatan }}</td>
                                    <td class="py-3 px-5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-slate-100 font-medium text-slate-700">
                                            🏢 {{ $p->ruangan->nama_ruangan ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-5 text-slate-600 whitespace-nowrap">{{ $p->tgl_mulai }} {{ $p->jam_mulai }}</td>
                                    <td class="py-3 px-5 text-center whitespace-nowrap">
                                        <a href="{{ route('peminjaman.verifikasi.index') }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1 bg-[#0B1528] hover:bg-[#1E3A8A] text-white rounded-lg text-xs font-bold transition">
                                            Proses
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 text-xs italic">
                                        Tidak ada antrean verifikasi tempat saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Antrean Verifikasi Barang -->
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">Antrean Peminjaman Barang &amp; Alat (BKHM)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Permohonan peminjaman sarana inventaris ormawa</p>
                    </div>
                    <a href="{{ route('peminjaman.verifikasi.index') }}" class="text-xs font-bold text-blue-700 hover:text-blue-900 transition">Kelola &rarr;</a>
                </div>
                <div class="overflow-x-auto skin-scrollbar">
                    <table class="min-w-full divide-y divide-slate-100 text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider select-none">
                                <th scope="col" class="py-3 px-5">Ormawa Pengusul</th>
                                <th scope="col" class="py-3 px-5">Nama Kegiatan</th>
                                <th scope="col" class="py-3 px-5">Daftar Barang</th>
                                <th scope="col" class="py-3 px-5">Mulai Tanggal</th>
                                <th scope="col" class="py-3 px-5 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($barangQueue as $p)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-5 font-bold text-slate-800">{{ $p->user->name }}</td>
                                    <td class="py-3 px-5 font-semibold text-slate-900">{{ $p->nama_kegiatan }}</td>
                                    <td class="py-3 px-5 text-slate-700">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-amber-50 text-amber-900 font-medium">
                                            📦 {{ collect($p->kebutuhan_barang)->pluck('nama_barang')->implode(', ') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-5 text-slate-600 whitespace-nowrap">{{ $p->tgl_mulai }}</td>
                                    <td class="py-3 px-5 text-center whitespace-nowrap">
                                        <a href="{{ route('peminjaman.verifikasi.index') }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1 bg-[#0B1528] hover:bg-[#1E3A8A] text-white rounded-lg text-xs font-bold transition">
                                            Proses
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 text-xs italic">
                                        Tidak ada antrean verifikasi barang saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Riwayat Perubahan Saldo -->
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Audit Riwayat Perubahan Saldo Kas</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Log pencatatan mutasi penambahan, pengurangan, dan penyesuaian saldo ormawa</p>
                </div>
                <div class="overflow-x-auto skin-scrollbar">
                    <table class="min-w-full divide-y divide-slate-100 text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider select-none">
                                <th scope="col" class="py-3 px-5">Waktu Pencatatan</th>
                                <th scope="col" class="py-3 px-5">Target Akun</th>
                                <th scope="col" class="py-3 px-5">Aktor Eksekusi</th>
                                <th scope="col" class="py-3 px-5">Perubahan Nominal</th>
                                <th scope="col" class="py-3 px-5">Catatan / Alasan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($saldoHistori as $history)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-5 text-slate-600 whitespace-nowrap">{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-3 px-5 font-bold text-slate-800">{{ $history->user->name }}</td>
                                    <td class="py-3 px-5 text-slate-700">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 font-semibold text-[11px]">
                                            👤 {{ $history->actor->name }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-5 font-mono whitespace-nowrap">
                                        <span class="text-slate-400">Rp {{ number_format($history->nominal_sebelum, 0, ',', '.') }}</span>
                                        <span class="mx-1 text-slate-300">&rarr;</span>
                                        <span class="font-extrabold text-emerald-600">Rp {{ number_format($history->nominal_sesudah, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="py-3 px-5 text-slate-600">{{ $history->catatan }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 text-xs italic">
                                        Belum ada riwayat perubahan saldo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 border-b pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900">Jadwal Terpadu Fasilitas & Barang</h3>
                        <p class="text-xs text-slate-500 mt-1">Klik pada agenda untuk melihat detail kegiatan</p>
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
