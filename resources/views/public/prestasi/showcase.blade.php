<x-public-layout title="Showcase Prestasi Mahasiswa" brand-label="Showcase Prestasi" accent="amber">
    <x-slot name="nav">
        <a href="{{ route('layanan.index') }}"
           class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:text-amber-800 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 focus-visible:ring-offset-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
            </svg>
            <span class="hidden sm:inline">Portal Layanan</span>
            <span class="sm:hidden">Portal</span>
        </a>
        <a href="{{ route('layanan.prestasi.create') }}"
           class="inline-flex items-center gap-1.5 min-h-[44px] px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-amber-700 hover:bg-amber-800 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 focus-visible:ring-offset-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span class="hidden sm:inline">Lapor Prestasi Anda</span>
            <span class="sm:hidden">Lapor</span>
        </a>
    </x-slot>

    {{-- HERO SECTION: Deep Academic Navy Senada dengan Halaman Home --}}
    <section class="relative bg-[#0B1528] text-white pt-8 pb-14 sm:pt-12 sm:pb-20 px-4 sm:px-6 overflow-hidden">
        {{-- Watermark Ornamen Logo Obor Berwarna --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden select-none" aria-hidden="true">
            <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute -top-10 left-6 w-24 sm:w-28 opacity-[0.12] -rotate-12" alt="">
            <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute top-20 right-8 w-24 sm:w-32 opacity-[0.14] rotate-12" alt="">
            <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute -bottom-8 left-1/3 w-28 opacity-[0.10] -rotate-6" alt="">
        </div>

        <div class="relative z-10 max-w-6xl mx-auto">
            {{-- Breadcrumb Navigasi --}}
            <nav class="flex items-center gap-2 text-xs text-slate-300 mb-4 sm:mb-6">
                <a href="{{ route('layanan.index') }}" class="hover:text-amber-300 transition">Portal Layanan</a>
                <span class="text-slate-500">/</span>
                <span class="text-amber-400 font-semibold">Galeri Prestasi Mahasiswa</span>
            </nav>

            {{-- Sapaan Maskot Si Ujang untuk Mobile (< lg) --}}
            <div class="block lg:hidden mb-4">
                <div class="inline-flex items-center gap-2.5 p-1.5 pr-4 rounded-full bg-[#12203A] border border-[#263B66] shadow-lg max-w-full">
                    <div class="relative w-8 h-8 rounded-full bg-[#1E3A8A] border-2 border-amber-400 p-0.5 flex items-center justify-center shrink-0 shadow-sm">
                        <img src="{{ asset('images/maskot-itg-head.png') }}" class="w-full h-full object-contain rounded-full" alt="Si Ujang">
                        <span class="absolute -top-0.5 -right-0.5 flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-400 border border-[#0B1528]"></span>
                        </span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-amber-400 font-extrabold text-[10px] sm:text-[11px] tracking-wider uppercase block leading-none">
                            Si Ujang &bull; Galeri Prestasi
                        </span>
                        <span class="text-slate-200 text-xs font-semibold truncate block mt-0.5">
                            Sampurasun! Lihat kebanggaan karya dan prestasi mahasiswa ITG!
                        </span>
                    </div>
                </div>
            </div>

            {{-- Grid Konten Hero (2 Kolom di Desktop lg) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                {{-- Kolom Kiri: Judul, Subtitle & Value Props --}}
                <div class="lg:col-span-7 text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/15 border border-amber-400/40 text-amber-300 text-[10px] sm:text-xs font-bold tracking-wide uppercase mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                        <span>Etalase Capaian Mahasiswa Institut Teknologi Garut</span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight mb-3">
                        Jejak Juara &amp; Prestasi Membanggakan ITG
                    </h1>

                    <p class="text-slate-300 text-xs sm:text-base leading-relaxed mb-6 max-w-xl">
                        Dokumentasi resmi apresiasi atas dedikasi dan capaian kompetisi mahasiswa Institut Teknologi Garut di kancah regional, nasional, dan internasional.
                    </p>

                    {{-- Badges Nilai Tambah Senada Home --}}
                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-300">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-[11px] font-semibold">
                            <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Terverifikasi BKHM</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-[11px] font-semibold">
                            <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                            </svg>
                            <span>Cakupan Nasional &amp; Internasional</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-[11px] font-semibold">
                            <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Pangkalan Data Resmi</span>
                        </span>
                    </div>
                </div>

                {{-- Kolom Kanan: Panggung Maskot Si Ujang Besar (Khusus Desktop lg) --}}
                <div class="hidden lg:flex lg:col-span-5 flex-col items-center justify-center relative">
                    {{-- Speech Bubble Sapaan Juara --}}
                    <div class="bg-white text-slate-900 text-xs sm:text-sm font-bold px-4 py-2.5 rounded-2xl shadow-2xl border border-slate-100 flex items-center gap-2 mb-3 relative">
                        <span>🏆 Bangga berprestasi untuk almamater ITG!</span>
                        <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-t-[8px] border-t-white"></div>
                    </div>

                    {{-- Karakter Maskot Si Ujang Full Body --}}
                    <div class="relative flex items-center justify-center">
                        <img src="{{ asset('images/maskot-itg-full.png') }}"
                             alt="Maskot Si Ujang - Prestasi Mahasiswa ITG"
                             class="h-64 sm:h-72 lg:h-80 object-contain drop-shadow-2xl">
                    </div>

                    {{-- Label Tag Maskot --}}
                    <div class="mt-3 inline-flex items-center px-3.5 py-1 rounded-full bg-blue-950/80 border border-amber-400/30 text-amber-300 text-[11px] font-semibold tracking-wide shadow-md">
                        <span>Si Ujang &bull; Duta Semangat Prestasi ITG</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- KONTEN UTAMA: Client-Side Filter & Grid Showcase (Alpine.js State) --}}
    <div x-data="{
        searchQuery: '',
        selectedTingkat: 'semua',
        matches(tingkat, nama, mahasiswa, prodi) {
            const matchesTingkat = (this.selectedTingkat === 'semua') || (tingkat.toLowerCase() === this.selectedTingkat.toLowerCase());
            if (!matchesTingkat) return false;
            if (!this.searchQuery.trim()) return true;
            const q = this.searchQuery.toLowerCase();
            return nama.toLowerCase().includes(q) || mahasiswa.toLowerCase().includes(q) || prodi.toLowerCase().includes(q);
        }
    }">
        {{-- FLOATING FILTER & SEARCH BAR (Menumpuk Batas Hero Senada Home) --}}
        <section class="max-w-6xl mx-auto px-4 sm:px-6 -mt-7 sm:-mt-9 relative z-20">
            <div class="bg-white rounded-2xl p-3.5 sm:p-4 border border-slate-200/90 shadow-xl shadow-slate-900/5 flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4">
                {{-- Filter Tabs Tingkat --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none" role="tablist" aria-label="Filter Tingkat Prestasi">
                    <button type="button"
                            @click="selectedTingkat = 'semua'"
                            :class="selectedTingkat === 'semua' ? 'bg-[#1E3A8A] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="min-h-[44px] px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                        Semua Tingkat
                    </button>
                    <button type="button"
                            @click="selectedTingkat = 'nasional'"
                            :class="selectedTingkat === 'nasional' ? 'bg-amber-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="min-h-[44px] px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600">
                        Nasional
                    </button>
                    <button type="button"
                            @click="selectedTingkat = 'internasional'"
                            :class="selectedTingkat === 'internasional' ? 'bg-indigo-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="min-h-[44px] px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-600">
                        Internasional
                    </button>
                    <button type="button"
                            @click="selectedTingkat = 'provinsi'"
                            :class="selectedTingkat === 'provinsi' ? 'bg-blue-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="min-h-[44px] px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                        Provinsi / Wilayah
                    </button>
                </div>

                {{-- Kolom Input Pencarian Cepat --}}
                <div class="relative w-full md:w-80 shrink-0">
                    <label for="search-showcase" class="sr-only">Cari Prestasi atau Mahasiswa</label>
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input id="search-showcase"
                           type="text"
                           x-model="searchQuery"
                           placeholder="Cari kompetisi, nama, prodi..."
                           class="w-full pl-10 pr-9 py-2.5 min-h-[44px] rounded-xl border border-slate-300 focus:border-amber-600 focus:ring-amber-600 text-xs sm:text-sm text-slate-900 placeholder-slate-400">
                    <button type="button"
                            x-show="searchQuery.length > 0"
                            @click="searchQuery = ''"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                            title="Bersihkan pencarian">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        {{-- MAIN SHOWCASE GRID --}}
        <main class="max-w-6xl mx-auto px-4 sm:px-6 py-10 sm:py-14">
            @if ($prestasis->isEmpty())
                {{-- State Belum Ada Prestasi di Database --}}
                <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-10">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-amber-50 rounded-2xl border border-amber-200/80 flex items-center justify-center mx-auto mb-4 text-amber-700 shadow-2xs">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h10v5a5 5 0 0 1-10 0V4z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 5H4.5A2.5 2.5 0 0 0 7 10"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 5h2.5A2.5 2.5 0 0 1 17 10"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v3M9 20h6"/>
                        </svg>
                    </div>
                    <h2 class="text-base sm:text-xl font-bold text-slate-900">Belum Ada Prestasi yang Dipublikasikan</h2>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-md mx-auto leading-relaxed">
                        Pernah menjuarai perlombaan atau kompetisi ilmiah? Daftarkan capaian Anda sekarang melalui portal layanan terpadu ITG.
                    </p>
                    <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a href="{{ route('layanan.prestasi.create') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center min-h-[44px] px-6 rounded-xl text-xs sm:text-sm font-bold text-white bg-amber-700 hover:bg-amber-800 transition shadow-sm">
                            Lapor Prestasi Mahasiswa
                        </a>
                        <a href="{{ route('layanan.index') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center min-h-[44px] px-6 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition">
                            Kembali ke Portal
                        </a>
                    </div>
                </div>
            @else
                {{-- Grid Kartu Prestasi Terpadu --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
                    @foreach ($prestasis as $item)
                        @php
                            $tingkatLower = strtolower($item->tingkat ?? '');
                            $isNasional = str_contains($tingkatLower, 'nasional') && !str_contains($tingkatLower, 'internasional');
                            $isInternasional = str_contains($tingkatLower, 'internasional');
                        @endphp
                        <article x-show="matches('{{ addslashes($item->tingkat ?? '') }}', '{{ addslashes($item->nama_kegiatan ?? '') }}', '{{ addslashes($item->nama_mahasiswa ?? '') }}', '{{ addslashes($item->prodi ?? '') }}')"
                                 x-transition
                                 class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:border-amber-400 hover:shadow-xl transition-all duration-200 flex flex-col justify-between overflow-hidden group">
                            <div>
                                {{-- Media Foto / Banner Visual --}}
                                @if ($item->foto_penyerahan && \Illuminate\Support\Facades\Storage::disk('public')->exists($item->foto_penyerahan))
                                    <div class="relative h-48 sm:h-52 w-full bg-slate-900 overflow-hidden">
                                        <img src="{{ asset('storage/' . $item->foto_penyerahan) }}"
                                             alt="Foto penyerahan prestasi {{ $item->nama_kegiatan }}"
                                             loading="lazy"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>

                                        {{-- Badges Mengambang di Atas Foto --}}
                                        <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between gap-2">
                                            <span class="px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] font-bold {{ $isInternasional ? 'bg-indigo-600 text-white' : ($isNasional ? 'bg-amber-600 text-white' : 'bg-slate-800 text-white') }} shadow-sm">
                                                {{ $item->tingkat }}
                                            </span>
                                            @if ($item->capaian)
                                                <span class="px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] font-black bg-amber-400 text-slate-950 shadow-sm">
                                                    {{ $item->capaian }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    {{-- Banner Geometris Elegan (Fallback saat tidak ada foto) --}}
                                    <div class="relative h-36 sm:h-40 w-full bg-gradient-to-br from-[#0B1528] via-[#132342] to-[#1E3A8A] p-4 flex flex-col justify-between overflow-hidden">
                                        <div class="absolute -right-4 -bottom-4 w-24 h-24 rounded-full bg-amber-400/10 blur-xl pointer-events-none"></div>
                                        <div class="flex items-center justify-between gap-2 relative z-10">
                                            <span class="px-2.5 py-0.5 rounded-md text-[10px] sm:text-[11px] font-bold uppercase tracking-wider {{ $isInternasional ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-400/40' : ($isNasional ? 'bg-amber-500/20 text-amber-300 border border-amber-400/40' : 'bg-white/10 text-slate-300 border border-white/20') }}">
                                                {{ $item->tingkat }}
                                            </span>
                                            <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center text-amber-400">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 6.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V6.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.617a1 1 0 01.894-1.788l1.599.799L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>

                                        @if ($item->capaian)
                                            <div class="relative z-10">
                                                <span class="inline-block px-3 py-1 rounded-lg text-xs font-black bg-amber-400 text-slate-950 shadow-md">
                                                    🏆 {{ $item->capaian }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Body Informasi Lomba & Mahasiswa --}}
                                <div class="p-5 sm:p-6">
                                    {{-- Judul Kompetisi --}}
                                    <h2 class="text-base sm:text-lg font-bold text-slate-900 leading-snug line-clamp-2 group-hover:text-amber-800 transition">
                                        {{ $item->nama_kegiatan }}
                                    </h2>

                                    {{-- Penyelenggara --}}
                                    <div class="text-xs text-slate-600 mt-2.5 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        @if ($item->url_penyelenggara)
                                            <a href="{{ $item->url_penyelenggara }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               class="truncate text-amber-800 hover:text-amber-900 hover:underline flex items-center gap-1 font-semibold"
                                               title="Kunjungi website resmi penyelenggara">
                                                <span class="truncate">{{ $item->penyelenggara }}</span>
                                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                            </a>
                                        @else
                                            <span class="truncate font-medium text-slate-700">{{ $item->penyelenggara }}</span>
                                        @endif
                                    </div>

                                    {{-- Profil Mahasiswa Penerima --}}
                                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-3">
                                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#1E3A8A] text-amber-300 font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs border border-blue-400/30">
                                            {{ strtoupper(substr($item->nama_mahasiswa, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-xs sm:text-sm font-bold text-slate-900 truncate">{{ $item->nama_mahasiswa }}</div>
                                            <div class="text-[11px] text-slate-500 truncate flex items-center gap-1.5 mt-0.5">
                                                <span>{{ $item->nim }}</span>
                                                <span>&bull;</span>
                                                <span class="font-medium text-slate-700">{{ $item->prodi ?? 'ITG' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Kartu (Rentang Waktu Kegiatan) --}}
                            <div class="bg-slate-50 px-5 sm:px-6 py-3 border-t border-slate-100 flex items-center text-[11px] text-slate-600">
                                <span class="inline-flex items-center gap-1.5 font-medium">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $item->rentang_tanggal }}</span>
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                <div class="mt-10">
                    {{ $prestasis->links() }}
                </div>
            @endif
        </main>
    </div>

    {{-- CALLOUT BANNER APRESIASI: Ajakan Lapor Prestasi Senada Halaman Home --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 my-10 sm:my-14">
        <div class="bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-blue-500/10 border border-amber-200/90 rounded-2xl p-5 sm:p-7 flex flex-col sm:flex-row items-center justify-between gap-5 shadow-sm">
            <div class="flex items-center gap-4 text-left w-full sm:w-auto">
                <div class="relative shrink-0">
                    <img src="{{ asset('images/maskot-itg-head.png') }}"
                         alt="Si Ujang"
                         class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-amber-100 p-1 border border-amber-300 object-contain shadow-2xs">
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900">
                        Punya Prestasi atau Menjuarai Lomba Mewakili ITG?
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-0.5 leading-relaxed">
                        Catatkan prestasimu di pangkalan data resmi kampus untuk mendapatkan apresiasi dan pengakuan institusi dari Biro Kemahasiswaan (BKHM).
                    </p>
                </div>
            </div>

            <div class="w-full sm:w-auto shrink-0 flex items-center gap-3">
                <a href="{{ route('layanan.prestasi.create') }}"
                   class="inline-flex items-center justify-center gap-2 min-h-[44px] w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-amber-700 hover:bg-amber-800 text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 shadow-sm">
                    <span>Lapor Prestasi Anda</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
</x-public-layout>
