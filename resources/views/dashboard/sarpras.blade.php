<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 leading-tight">
            {{ __('Dashboard Sarana & Prasarana (Sarpras)') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Peminjaman Ruangan (Bulan Ini)</span>
                        <p class="text-3xl font-extrabold text-[#0B1528] mt-2">{{ $stats['peminjaman_ruangan'] }}</p>
                        <p class="text-xs text-slate-500 mt-1">Total permohonan penggunaan ruang kuliah dan aula</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('peminjaman.verifikasi.index') }}" class="inline-flex items-center min-h-[44px] text-xs font-bold text-blue-700 hover:text-blue-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 rounded">
                            Verifikasi Peminjaman Ruangan &rarr;
                        </a>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Peminjaman Barang (Bulan Ini)</span>
                        <p class="text-3xl font-extrabold text-[#0B1528] mt-2">{{ $stats['peminjaman_barang'] }}</p>
                        <p class="text-xs text-slate-500 mt-1">Total permohonan inventaris barang dan logistik ormawa</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('peminjaman.verifikasi.index') }}" class="inline-flex items-center min-h-[44px] text-xs font-bold text-blue-700 hover:text-blue-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 rounded">
                            Verifikasi Peminjaman Barang &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Antrean Persetujuan Masuk -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2.5 bg-amber-50 text-amber-700 rounded-xl border border-amber-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <div>
                                <h3 class="font-bold text-slate-900 text-base">Antrean Peminjaman Menunggu Verifikasi</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Permohonan fasilitas terbaru yang membutuhkan persetujuan Sarpras.</p>
                            </div>
                        </div>
                        <a href="{{ route('peminjaman.verifikasi.index') }}" class="inline-flex items-center min-h-[44px] px-2 text-xs font-bold text-blue-700 hover:text-blue-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 rounded">
                            Lihat Semua Antrean &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                        <!-- Antrean Ruangan -->
                        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/50">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                Ruangan / Tempat ({{ count($antrianTempat ?? []) }})
                            </h4>
                            @if(isset($antrianTempat) && count($antrianTempat) > 0)
                                <div class="space-y-2.5">
                                    @foreach($antrianTempat as $pt)
                                        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-slate-900 truncate">{{ $pt->ruangan->nama_ruangan ?? 'Ruangan' }}</p>
                                                <p class="text-xs text-slate-500 mt-0.5">{{ $pt->user->name ?? '-' }} &bull; {{ \Carbon\Carbon::parse($pt->tanggal_mulai)->format('d M Y') }}</p>
                                            </div>
                                            <a href="{{ route('peminjaman.verifikasi.index') }}" class="inline-flex items-center justify-center min-h-[44px] px-3.5 py-1.5 text-xs font-bold bg-[#0B1528] text-white hover:bg-[#1E3A8A] rounded-lg transition shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                                                Proses
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-500 italic py-6 text-center">Tidak ada antrean ruangan yang menunggu persetujuan.</p>
                            @endif
                        </div>

                        <!-- Antrean Barang -->
                        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/50">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-600"></span>
                                Barang &amp; Peralatan ({{ count($antrianBarang ?? []) }})
                            </h4>
                            @if(isset($antrianBarang) && count($antrianBarang) > 0)
                                <div class="space-y-2.5">
                                    @foreach($antrianBarang as $pb)
                                        <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-slate-900 truncate">{{ $pb->nama_barang ?? 'Barang Inventaris' }}</p>
                                                <p class="text-xs text-slate-500 mt-0.5">{{ $pb->user->name ?? '-' }} &bull; {{ \Carbon\Carbon::parse($pb->tanggal_mulai)->format('d M Y') }}</p>
                                            </div>
                                            <a href="{{ route('peminjaman.verifikasi.index') }}" class="inline-flex items-center justify-center min-h-[44px] px-3.5 py-1.5 text-xs font-bold bg-[#0B1528] text-white hover:bg-[#1E3A8A] rounded-lg transition shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                                                Proses
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-500 italic py-6 text-center">Tidak ada antrean barang yang menunggu persetujuan.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @hasrole('sarpras')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Master Ruangan Kampus</h3>
                        <p class="text-xs text-slate-600 mt-1">Kelola data gedung, ruangan, kapasitas, dan status ketersediaan.</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('sarpras.ruangan.index') }}" class="inline-flex items-center min-h-[44px] text-xs font-bold text-blue-700 hover:text-blue-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 rounded">
                            Kelola Ruangan &rarr;
                        </a>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Manajemen Inventaris Barang</h3>
                        <p class="text-xs text-slate-600 mt-1">Kelola stok dan daftar barang yang dapat dipinjam oleh Ormawa.</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('sarpras.barang.index') }}" class="inline-flex items-center min-h-[44px] text-xs font-bold text-blue-700 hover:text-blue-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 rounded">
                            Kelola Inventaris &rarr;
                        </a>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Jadwal Perkuliahan</h3>
                        <p class="text-xs text-slate-600 mt-1">Input jadwal kuliah untuk proteksi bentrok peminjaman ruangan.</p>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('sarpras.jadwal.index') }}" class="inline-flex items-center min-h-[44px] text-xs font-bold text-blue-700 hover:text-blue-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 rounded">
                            Kelola Jadwal &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @endhasrole

        </div>
    </div>
</x-app-layout>