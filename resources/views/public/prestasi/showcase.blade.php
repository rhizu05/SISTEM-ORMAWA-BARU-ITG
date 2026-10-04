<x-public-layout title="Showcase Prestasi Mahasiswa" brand-label="Showcase Prestasi" accent="amber">
    <x-slot name="nav">
        <a href="{{ route('layanan.index') }}" class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 focus-visible:ring-offset-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            <span class="hidden sm:inline">Portal Layanan</span>
            <span class="sm:hidden">Portal</span>
        </a>
        <a href="{{ route('layanan.prestasi.create') }}" class="inline-flex items-center gap-1.5 min-h-[44px] px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-amber-700 hover:bg-amber-800 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 focus-visible:ring-offset-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <span class="hidden sm:inline">Lapor Prestasi Anda</span>
            <span class="sm:hidden">Lapor Prestasi</span>
        </a>
    </x-slot>

    <section class="bg-indigo-900 text-white py-10 sm:py-14 px-4 sm:px-6">
        <div class="max-w-4xl mx-auto">
            <nav class="flex items-center gap-2 text-xs text-indigo-200 mb-6">
                <a href="{{ route('layanan.index') }}" class="hover:text-white transition">Portal Layanan</a>
                <span class="text-indigo-300">/</span>
                <span class="text-white font-semibold">Showcase Prestasi</span>
            </nav>

            <div class="text-center">
                <div class="w-14 h-14 rounded-2xl bg-amber-400/15 text-amber-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h10v5a5 5 0 0 1-10 0V4z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 5H4.5A2.5 2.5 0 0 0 7 10"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 5h2.5A2.5 2.5 0 0 1 17 10"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v3M9 20h6"/>
                    </svg>
                </div>
                <h1 class="text-2xl sm:text-5xl font-extrabold tracking-tight">Prestasi Membanggakan Mahasiswa ITG</h1>
                <p class="text-sm sm:text-lg text-indigo-100 max-w-2xl mx-auto mt-4 leading-relaxed">
                    Apresiasi atas dedikasi dan capaian kompetisi mahasiswa Institut Teknologi Garut di kancah regional, nasional, dan internasional.
                </p>

                @if (($totalNasional + $totalInternasional) > 0)
                    <div class="mt-8 inline-flex flex-wrap items-center justify-center gap-8 sm:gap-12">
                        <div>
                            <div class="text-3xl sm:text-4xl font-black text-amber-400">{{ $totalNasional }}</div>
                            <div class="text-xs sm:text-sm text-indigo-100 mt-1">Prestasi Tingkat Nasional</div>
                        </div>
                        <div>
                            <div class="text-3xl sm:text-4xl font-black text-amber-400">{{ $totalInternasional }}</div>
                            <div class="text-xs sm:text-sm text-indigo-100 mt-1">Prestasi Tingkat Internasional</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
        @if ($prestasis->isEmpty())
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-200 p-8">
                <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4 text-amber-700">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h10v5a5 5 0 0 1-10 0V4z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 5H4.5A2.5 2.5 0 0 0 7 10"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 5h2.5A2.5 2.5 0 0 1 17 10"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v3M9 20h6"/>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800">Belum Ada Prestasi yang Dipublikasikan</h2>
                <p class="text-sm text-slate-600 mt-1 max-w-md mx-auto">Pernah menjuarai perlombaan atau kompetisi ilmiah? Daftarkan capaian Anda sekarang melalui portal layanan.</p>
                <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('layanan.prestasi.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center min-h-[48px] px-6 rounded-xl text-sm font-bold text-white bg-amber-700 hover:bg-amber-800 transition shadow-sm">
                        Lapor Prestasi Mahasiswa
                    </a>
                    <a href="{{ route('layanan.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center min-h-[48px] px-6 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition">
                        Kembali ke Portal
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($prestasis as $item)
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col justify-between">
                        <div>
                            @if ($item->foto_penyerahan && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->foto_penyerahan))
                                <div class="relative h-48 w-full bg-slate-100 overflow-hidden group">
                                    <img src="{{ asset('storage/' . $item->foto_penyerahan) }}" alt="Foto penyerahan prestasi {{ $item->nama_kegiatan }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent"></div>
                                    <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between gap-2">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-700 text-white">
                                            {{ $item->tingkat }}
                                        </span>
                                        @if ($item->capaian)
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-indigo-600 text-white">
                                                {{ $item->capaian }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="p-6">
                                @if (!$item->foto_penyerahan || !\Illuminate\Support\Facades\Storage::disk('public')->exists($item->foto_penyerahan))
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                            {{ $item->tingkat }}
                                        </span>
                                        @if ($item->capaian)
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-indigo-50 text-indigo-800 border border-indigo-200">
                                                {{ $item->capaian }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <h2 class="text-base font-bold text-slate-900 leading-snug line-clamp-2">
                                    {{ $item->nama_kegiatan }}
                                </h2>

                                <div class="text-xs text-slate-600 mt-2 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    @if ($item->url_penyelenggara)
                                        <a href="{{ $item->url_penyelenggara }}" target="_blank" rel="noopener noreferrer" class="truncate text-amber-800 hover:text-amber-900 hover:underline flex items-center gap-1 font-medium" title="Kunjungi website resmi penyelenggara">
                                            <span class="truncate">{{ $item->penyelenggara }}</span>
                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        <span class="truncate">{{ $item->penyelenggara }}</span>
                                    @endif
                                </div>

                                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-amber-700 text-white font-bold text-xs flex items-center justify-center flex-shrink-0">
                                        {{ strtoupper(substr($item->nama_mahasiswa, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-bold text-slate-900 truncate">{{ $item->nama_mahasiswa }}</div>
                                        <div class="text-[11px] text-slate-600 truncate">{{ $item->nim }} &bull; {{ $item->prodi ?? 'ITG' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-600">
                            <span>{{ $item->rentang_tanggal }}</span>
                            <span class="font-mono text-slate-500 text-[10px]">{{ $item->kode_tiket }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $prestasis->links() }}
            </div>
        @endif
    </main>
</x-public-layout>
