<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Showcase Prestasi Mahasiswa - Institut Teknologi Garut</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans">
    <!-- Navbar -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('layanan.index') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo_itg.png') }}" alt="Logo ITG" class="h-12 w-auto object-contain">
                <div>
                    <span class="text-xs font-semibold tracking-wider text-amber-600 uppercase block">Institut Teknologi Garut</span>
                    <span class="text-lg font-black tracking-tight text-slate-900 leading-tight">Showcase Prestasi</span>
                </div>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('layanan.prestasi.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 shadow-md shadow-amber-500/20 transition">
                    + Lapor Prestasi Anda
                </a>
                <a href="{{ route('layanan.index') }}" class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
                    Portal Layanan
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Header -->
    <section class="bg-gradient-to-b from-amber-600 via-amber-700 to-slate-900 text-white py-16 px-4 sm:px-6 lg:px-8 text-center">
        <div class="max-w-4xl mx-auto space-y-4">
            <span class="inline-block px-3.5 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm border border-white/30 text-amber-100">
                Hall of Fame & Galeri Kejuaraan
            </span>
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">
                Prestasi Membanggakan Mahasiswa ITG
            </h1>
            <p class="text-base sm:text-lg text-amber-100/90 max-w-2xl mx-auto font-light leading-relaxed">
                Apresiasi atas dedikasi dan capaian kompetisi mahasiswa Institut Teknologi Garut di kancah regional, nasional, dan internasional.
            </p>
        </div>
    </section>

    <!-- Main Grid -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if ($prestasis->isEmpty())
            <div class="text-center py-16 bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4 text-amber-600 text-2xl">
                    🏆
                </div>
                <h3 class="text-lg font-bold text-slate-800">Belum Ada Prestasi yang Dipublikasikan</h3>
                <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">Pernah menjuarai perlombaan atau kompetisi ilmiah? Daftarkan capaian Anda sekarang melalui portal layanan.</p>
                <div class="mt-6">
                    <a href="{{ route('layanan.prestasi.create') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-amber-600 hover:bg-amber-700 transition inline-block">
                        Lapor Prestasi Mahasiswa
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($prestasis as $item)
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between overflow-hidden">
                        <div class="p-6">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    {{ $item->tingkat }}
                                </span>
                                @if ($item->capaian)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-blue-50 text-blue-800 border border-blue-200">
                                        {{ $item->capaian }}
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-base font-bold text-slate-900 leading-snug line-clamp-2">
                                {{ $item->nama_kegiatan }}
                            </h3>

                            <div class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="truncate">{{ $item->penyelenggara }}</span>
                            </div>

                            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-amber-500 to-amber-600 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 shadow-sm">
                                    {{ strtoupper(substr($item->nama_mahasiswa, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-slate-900 truncate">{{ $item->nama_mahasiswa }}</div>
                                    <div class="text-[11px] text-slate-500 truncate">{{ $item->nim }} &bull; {{ $item->prodi ?? 'ITG' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50/80 px-6 py-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>📅 {{ $item->tanggal_kegiatan ? \Carbon\Carbon::parse($item->tanggal_kegiatan)->translatedFormat('d M Y') : $item->created_at->translatedFormat('d M Y') }}</span>
                            <span class="font-mono text-slate-400 text-[10px]">{{ $item->kode_tiket }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $prestasis->links() }}
            </div>
        @endif
    </main>

    <footer class="bg-white border-t border-slate-200 py-8 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Institut Teknologi Garut. Bagian Kemahasiswaan & Hubungan Alumni.
    </footer>
</body>
</html>
