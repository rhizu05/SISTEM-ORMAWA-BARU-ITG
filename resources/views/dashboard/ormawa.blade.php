<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Ormawa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(isset($menungguLpj) && $menungguLpj->isNotEmpty())
            <div class="mb-6 p-4 rounded-lg bg-amber-50 border-l-4 border-amber-500 shadow-sm">
                <div class="flex items-start justify-between flex-col sm:flex-row gap-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 text-amber-600 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-bold text-amber-900">Perhatian: Anda memiliki {{ $menungguLpj->count() }} kegiatan yang menunggu unggah LPJ</h4>
                            <p class="text-xs text-amber-800 mt-1">
                                Dana kegiatan telah dicairkan oleh Bendahara. Segera unggah Laporan Pertanggungjawaban (LPJ) setelah kegiatan selesai agar akses pengajuan proposal baru dapat dibuka kembali.
                            </p>
                            <div class="mt-2 space-y-1">
                                @foreach($menungguLpj as $item)
                                <div class="text-xs font-semibold text-gray-800 flex items-center gap-2">
                                    <span>• {{ $item->nama_kegiatan }} (Rp {{ number_format($item->dana_diajukan, 0, ',', '.') }})</span>
                                    <a href="{{ route('lpj.create', $item) }}" class="text-indigo-700 underline font-bold hover:text-indigo-900">Unggah LPJ Sekarang &rarr;</a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(isset($spAktif) && $spAktif->isNotEmpty())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border-l-4 border-red-600 shadow-sm">
                <div class="flex items-start justify-between flex-col sm:flex-row gap-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 text-red-600 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-bold text-red-950 flex items-center gap-2">
                                <span>⚠️ Perhatian: Organisasi Anda Menerima {{ $spAktif->count() }} Surat Peringatan (SP) Resmi</span>
                            </h4>
                            <p class="text-xs text-red-800 mt-1">
                                Pimpinan institusi kampus telah menerbitkan surat peringatan resmi terkait kedisiplinan organisasi Anda. Harap perhatikan sanksi dan lakukan tindak lanjut segera.
                            </p>
                            <div class="mt-3 space-y-2">
                                @foreach($spAktif->take(3) as $sp)
                                <div class="bg-white/80 rounded-lg p-2.5 border border-red-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-full font-bold text-[10px]
                                            @if($sp->tingkat === 'SP-3') bg-red-600 text-white
                                            @elseif($sp->tingkat === 'SP-2') bg-orange-500 text-white
                                            @else bg-amber-400 text-gray-900
                                            @endif">
                                            {{ $sp->tingkat }}
                                        </span>
                                        <span class="font-bold text-gray-900">{{ $sp->nomor_surat }}</span>
                                        <span class="text-gray-600 hidden md:inline">— {{ Str::limit($sp->perihal, 50) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-500 text-[11px]">{{ $sp->tanggal_surat ? $sp->tanggal_surat->format('d/m/Y') : '' }}</span>
                                        <a href="{{ route('sp.saya.show', $sp) }}" class="inline-flex items-center gap-1 text-red-700 font-bold hover:text-red-900 underline text-xs">
                                            <span>Lihat Dokumen</span> &rarr;
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="mt-2.5">
                                <a href="{{ route('sp.saya.index') }}" class="text-xs font-semibold text-red-700 hover:text-red-900 underline">
                                    Buka Halaman Surat Peringatan Saya &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Top Stats Cards (Opsi A: Modern Academic Executive) -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
                <!-- Sisa Saldo Tersedia -->
                <div class="col-span-2 sm:col-span-1 group bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-emerald-300 transition-all duration-200 p-4 flex flex-col justify-between min-h-[136px]">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <span>💰</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px]">
                            Kas Ormawa
                        </span>
                    </div>
                    <div class="my-auto py-1">
                        <div class="text-2xl font-extrabold font-mono text-emerald-600 tracking-tight leading-none">
                            Rp {{ number_format($stats['saldo'], 0, ',', '.') }}
                        </div>
                        <div class="text-xs font-bold text-slate-900 mt-1 line-clamp-1">Sisa Saldo Tersedia</div>
                    </div>
                    <a href="{{ route('pengajuan.index') }}" class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-blue-600 group-hover:text-blue-800 transition-colors">
                        <span>Cek Mutasi</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                </div>

                <!-- Total Dana Diberikan -->
                <div class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-blue-300 transition-all duration-200 p-4 flex flex-col justify-between min-h-[136px]">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <span>🏛️</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px]">
                            Pagu Anggaran
                        </span>
                    </div>
                    <div class="my-auto py-1">
                        <div class="text-2xl font-extrabold font-mono text-[#1E40AF] tracking-tight leading-none">
                            Rp {{ number_format($stats['total_dana'], 0, ',', '.') }}
                        </div>
                        <div class="text-xs font-bold text-slate-900 mt-1 line-clamp-1">Total Dana Diberikan</div>
                    </div>
                    <a href="{{ route('pengajuan.index') }}" class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-blue-600 group-hover:text-blue-800 transition-colors">
                        <span>Rincian Pagu</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                </div>

                <!-- Dana Terpakai & Diproses -->
                <div class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-amber-300 transition-all duration-200 p-4 flex flex-col justify-between min-h-[136px]">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <span>⏳</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px]">
                            Realisasi
                        </span>
                    </div>
                    <div class="my-auto py-1">
                        <div class="text-2xl font-extrabold font-mono text-amber-600 tracking-tight leading-none">
                            Rp {{ number_format($stats['dana_diproses'], 0, ',', '.') }}
                        </div>
                        <div class="text-xs font-bold text-slate-900 mt-1 line-clamp-1">Dana Terpakai & Diproses</div>
                    </div>
                    <a href="{{ route('pengajuan.index') }}" class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-blue-600 group-hover:text-blue-800 transition-colors">
                        <span>Tracking Dana</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                </div>

                <!-- Total Proposal Diajukan -->
                <div class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-indigo-300 transition-all duration-200 p-4 flex flex-col justify-between min-h-[136px]">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <span>📑</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[10px]">
                            Total Usulan
                        </span>
                    </div>
                    <div class="my-auto py-1">
                        <div class="text-2xl font-extrabold font-mono text-indigo-600 tracking-tight leading-none">
                            {{ $stats['total_pengajuan'] }}
                        </div>
                        <div class="text-xs font-bold text-slate-900 mt-1 line-clamp-1">Total Proposal Diajukan</div>
                    </div>
                    <a href="{{ route('pengajuan.index') }}" class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-blue-600 group-hover:text-blue-800 transition-colors">
                        <span>Lihat Semua</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                </div>

                <!-- Proposal Dalam Proses -->
                <div class="group bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_16px_rgba(0,0,0,0.03)] hover:shadow-md hover:border-purple-300 transition-all duration-200 p-4 flex flex-col justify-between min-h-[136px]">
                    <div class="flex items-center justify-between gap-1.5 mb-2">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-sm shadow-2xs group-hover:scale-105 transition-transform">
                            <span>⚡</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full @if($stats['sedang_proses'] > 0) bg-purple-100 text-purple-700 @else bg-slate-100 text-slate-500 @endif font-bold text-[10px]">
                            Review Aktif
                        </span>
                    </div>
                    <div class="my-auto py-1">
                        <div class="text-2xl font-extrabold font-mono text-purple-600 tracking-tight leading-none">
                            {{ $stats['sedang_proses'] }}
                        </div>
                        <div class="text-xs font-bold text-slate-900 mt-1 line-clamp-1">Proposal Dalam Proses</div>
                    </div>
                    <a href="{{ route('pengajuan.index') }}" class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-blue-600 group-hover:text-blue-800 transition-colors">
                        <span>Pantau Progres</span>
                        <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                </div>
            </div>

            @php
                $totalPagu = ($stats['saldo'] ?? 0) + ($stats['dana_diproses'] ?? 0) + ($stats['total_dana'] ?? 0);
                $pctSaldo = $totalPagu > 0 ? round((($stats['saldo'] ?? 0) / $totalPagu) * 100) : 100;
                $pctDiproses = $totalPagu > 0 ? round((($stats['dana_diproses'] ?? 0) / $totalPagu) * 100) : 0;
                $pctTerpakai = $totalPagu > 0 ? round((($stats['total_dana'] ?? 0) / $totalPagu) * 100) : 0;
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Visualisasi Dana (Executive Split Layout) -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-6 flex flex-col justify-between overflow-hidden">
                    <div>
                        <!-- Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-100">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Visualisasi Alokasi & Realisasi Anggaran</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Monitoring serapan pagu kas ormawa aktif Tahun Akademik 2026/2027</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 shrink-0">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Anggaran Sehat {{ $pctSaldo }}%
                            </span>
                        </div>

                        <!-- Body: Donut + Progress Breakdown -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center py-6">
                            <!-- Donut Column -->
                            <div class="md:col-span-5 flex flex-col items-center justify-center">
                                <div class="relative w-40 h-40 flex items-center justify-center">
                                    <canvas id="financeChart" class="w-full h-full"></canvas>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none text-center">
                                        <span class="text-2xl font-extrabold font-mono text-emerald-600 leading-none">{{ $pctSaldo }}%</span>
                                        <span class="text-[11px] font-semibold text-slate-500 mt-1">Saldo Utuh</span>
                                    </div>
                                </div>
                                <!-- Legend below donut -->
                                <div class="flex flex-wrap items-center justify-center gap-3 mt-4 text-[11px] text-slate-600 font-medium">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Sisa Saldo
                                    </span>
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Diproses
                                    </span>
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Terpakai
                                    </span>
                                </div>
                            </div>

                            <!-- Progress Breakdown Column -->
                            <div class="md:col-span-7 space-y-4">
                                <!-- Bar 1: Sisa Saldo -->
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-semibold text-slate-700">Sisa Saldo Tersedia</span>
                                        <div class="font-mono font-bold text-slate-900">
                                            Rp {{ number_format($stats['saldo'], 0, ',', '.') }}
                                            <span class="text-emerald-600 font-bold ml-1">({{ $pctSaldo }}%)</span>
                                        </div>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $pctSaldo }}%"></div>
                                    </div>
                                </div>

                                <!-- Bar 2: Dana Diproses -->
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-semibold text-slate-700">Dana Sedang Diproses (Antrean)</span>
                                        <div class="font-mono font-bold text-slate-900">
                                            Rp {{ number_format($stats['dana_diproses'], 0, ',', '.') }}
                                            <span class="text-amber-600 font-bold ml-1">({{ $pctDiproses }}%)</span>
                                        </div>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-amber-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $pctDiproses }}%"></div>
                                    </div>
                                </div>

                                <!-- Bar 3: Dana Terpakai -->
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-semibold text-slate-700">Dana Terealisasi & Selesai</span>
                                        <div class="font-mono font-bold text-slate-900">
                                            Rp {{ number_format($stats['total_dana'], 0, ',', '.') }}
                                            <span class="text-rose-600 font-bold ml-1">({{ $pctTerpakai }}%)</span>
                                        </div>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-rose-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $pctTerpakai }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Tip & CTA -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2 text-slate-500 min-w-0">
                            <span class="shrink-0 text-sm">💡</span>
                            <span class="leading-normal">Ajukan proposal minimal H-14 kegiatan agar verifikasi tepat waktu.</span>
                        </div>
                        <a href="{{ route('pengajuan.create') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-[#0B1528] hover:bg-[#1E3A8A] text-white rounded-xl font-bold shadow-xs hover:shadow transition-all shrink-0 whitespace-nowrap">
                            <span>+ Buat Pengajuan Baru</span>
                        </a>
                    </div>
                </div>

                <!-- Informasi Akun (Executive Ormawa Identity) -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_4px_20px_rgba(0,0,0,0.02)] overflow-hidden flex flex-col justify-between">
                    <div>
                        <!-- Header Banner -->
                        <div class="bg-[#0B1528] text-white px-5 py-3.5 flex items-center justify-between">
                            <span class="text-[11px] font-bold text-blue-300 uppercase tracking-wider">Portal Resmi Ormawa</span>
                            <span class="text-[11px] font-medium text-slate-300">T.A. 2026/2027</span>
                        </div>

                        <!-- Profile Info Body -->
                        <div class="p-5">
                            <div class="flex items-center space-x-3.5 mb-4">
                                <div class="relative">
                                    <img src="{{ Auth::user()->foto_profil ? asset('storage/'.Auth::user()->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=EFF6FF&color=1E40AF&bold=true' }}" 
                                         class="w-14 h-14 rounded-2xl object-cover ring-2 ring-slate-100 shadow-xs">
                                    <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white" title="Akun Terverifikasi"></span>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-slate-900 text-sm truncate">{{ Auth::user()->name }}</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ '@' . Auth::user()->username }} • ID: ORM-{{ str_pad(Auth::user()->id, 3, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>

                            <!-- Badges -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    ● {{ Auth::user()->status_akun ?? 'Aktif' }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                    🏛️ Ormawa Kampus
                                </span>
                            </div>

                            <!-- Structural Metadata -->
                            <div class="bg-slate-50/80 border border-slate-100 rounded-xl p-3 space-y-2 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Pembina Institusi:</span>
                                    <span class="font-bold text-slate-800">BKHM ITG</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Pengawas Legislatif:</span>
                                    <span class="font-bold text-slate-800">BPM ITG</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Status Legalitas:</span>
                                    <span class="font-semibold text-emerald-700">SK Rektor Terdaftar</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Link -->
                    <div class="px-5 py-3.5 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('profile.edit') }}" class="text-xs font-bold text-blue-700 hover:text-blue-900 transition flex items-center gap-1 group">
                            <span>Pengaturan Profil & SK Organisasi</span>
                            <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

                <!-- Calendar / Facility Usage -->
                <div class="lg:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 border-b pb-3">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Kalender Pemakaian Fasilitas</h3>
                        </div>
                        <div class="flex flex-wrap items-center gap-4 text-xs mt-2 sm:mt-0">
                            <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                                <span class="w-3 h-3 rounded-full bg-indigo-500 shadow-sm ring-2 ring-indigo-100"></span> 
                                Fasilitas
                            </span>
                            <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                                <span class="w-3 h-3 rounded-full bg-amber-500 shadow-sm ring-2 ring-amber-100"></span> 
                                Rapat
                            </span>
                        </div>
                    </div>
                    <x-calendar-style />
                    <div class="skin-calendar-wrapper">
                        <div id="calendar"></div>
                    </div>
                </div>

                <!-- Agenda & Facilities List (Simplified) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Agenda Rapat Mendatang</h3>
                    <div class="space-y-4">
                        @forelse($meetings ?? [] as $meeting)
                            <div class="flex items-start space-x-3 p-2 hover:bg-gray-50 rounded transition">
                                <div class="bg-indigo-100 text-indigo-600 p-2 rounded text-center min-w-[50px]">
                                    <div class="text-xs font-bold uppercase">{{ \Carbon\Carbon::parse($meeting->tanggal_rapat)->format('M') }}</div>
                                    <div class="text-lg font-bold">{{ \Carbon\Carbon::parse($meeting->tanggal_rapat)->format('d') }}</div>
                                </div>
                                <div>
                                    <p class="text-sm font-bold">{{ $meeting->judul_rapat }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($meeting->tanggal_rapat)->format('d M Y') }} {{ $meeting->jam_rapat ?? '' }} WIB</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm">Tidak ada jadwal rapat.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Pemakaian Fasilitas Terdekat</h3>
                    <div class="space-y-4">
                        @forelse($facilities ?? [] as $fac)
                            <div class="flex items-center justify-between p-2 border-b last:border-0">
                                <div>
                                    <p class="text-sm font-bold">{{ $fac->ruangan->nama_ruangan ?? $fac->nama_kegiatan }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($fac->tgl_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($fac->tgl_selesai)->format('d M Y') }}</p>
                                </div>
                                <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded">{{ $fac->status_akhir ?? 'Aktif' }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm">Tidak ada jadwal pemakaian.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Pengumuman Terbaru</h3>
                    <div class="space-y-4">
                        @forelse($announcements ?? [] as $ann)
                            <div class="p-3 bg-indigo-50 rounded-lg border-l-4 border-indigo-500">
                                <p class="text-sm font-bold">{{ $ann->judul }}</p>
                                <p class="text-xs text-gray-600 line-clamp-2">{{ $ann->isi }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm">Belum ada pengumuman.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Finance Chart (Modern Academic Donut)
            const ctx = document.getElementById('financeChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sisa Saldo', 'Dana Diproses', 'Dana Terpakai'],
                    datasets: [{
                        data: [
                            {{ $stats['saldo'] }}, 
                            {{ $stats['dana_diproses'] }}, 
                            {{ $stats['total_dana'] }}
                        ],
                        backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                        hoverBackgroundColor: ['#059669', '#D97706', '#DC2626'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const val = context.raw || 0;
                                    return ' ' + context.label + ': Rp ' + Number(val).toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });

            // FullCalendar Integration
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'id',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [
                    @foreach($facilities as $fac)
                        {
                            title: '{{ $fac->ruangan->nama_ruangan ?? $fac->nama_kegiatan }}',
                            start: '{{ $fac->tgl_mulai }}',
                            end: '{{ \Carbon\Carbon::parse($fac->tgl_selesai)->addDay()->format('Y-m-d') }}',
                            color: '#4f46e5'
                        },
                    @endforeach
                    @foreach($meetings as $m)
                        {
                            title: 'Rapat: {{ $m->judul_rapat }}',
                            start: '{{ $m->tanggal_rapat }}',
                            color: '#f59e0b'
                        },
                    @endforeach
                ],
                eventClick: function(info) {
                    alert('Peminjaman: ' + info.event.title);
                }
            });
            calendar.render();
        });
    </script>
</x-app-layout>
