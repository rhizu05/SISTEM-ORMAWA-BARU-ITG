<x-public-layout title="Pusat Informasi & Regulasi" brand-label="Pusat Informasi & Regulasi" accent="indigo">
    <x-slot name="nav">
        <a href="{{ route('layanan.index') }}" class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            <span class="hidden sm:inline">Portal Layanan</span>
            <span class="sm:hidden">Portal</span>
        </a>
    </x-slot>

    <div x-data="{
        activeTab: 'pengumuman',
        showPengumumanModal: false,
        showRegulasiModal: false,
        searchQuery: '',
        matches(judul, isi, penerbit) {
            if (!this.searchQuery.trim()) return true;
            const q = this.searchQuery.toLowerCase();
            return (judul || '').toLowerCase().includes(q) ||
                   (isi || '').toLowerCase().includes(q) ||
                   (penerbit || '').toLowerCase().includes(q);
        }
    }">
        {{-- HERO SECTION BERKARAKTER ITG (Deep Academic Navy) --}}
        <section class="relative bg-[#0B1528] text-white pt-10 pb-16 sm:pt-14 sm:pb-20 overflow-hidden border-b border-slate-800">
            {{-- Watermark Ornamen Obor ITG --}}
            <div class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-1/4 w-[380px] sm:w-[500px] h-[380px] sm:h-[500px] pointer-events-none opacity-10 select-none">
                <img src="{{ asset('images/logo-skin-torch.png') }}" alt="" class="w-full h-full object-contain filter invert brightness-200">
            </div>

            {{-- Efek Pencahayaan Halus (Glow) --}}
            <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[700px] h-[320px] bg-gradient-to-b from-blue-600/20 via-amber-500/10 to-transparent blur-3xl pointer-events-none"></div>

            <div class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
                {{-- Breadcrumbs Bernuansa Emas Halus --}}
                <nav class="flex items-center gap-2 text-xs text-slate-400 mb-4 sm:mb-6" aria-label="Breadcrumb">
                    <a href="{{ route('layanan.index') }}" class="hover:text-amber-300 transition">Portal Layanan</a>
                    <span class="text-slate-600">/</span>
                    <span class="text-amber-400 font-semibold">Pusat Informasi &amp; Regulasi</span>
                </nav>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    {{-- Kolom Kiri: Judul, Subtitle & Value Props --}}
                    <div class="lg:col-span-7 text-left">
                        {{-- Mobile Pill Si Ujang Sapaan Hangat --}}
                        <div class="lg:hidden mb-4 inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-slate-900/90 border border-amber-400/30 shadow-lg max-w-full">
                            <div class="relative shrink-0">
                                <img src="{{ asset('images/maskot-itg-head.png') }}" alt="Si Ujang" class="w-7 h-7 rounded-full bg-amber-400/20 object-contain p-0.5">
                                <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-emerald-400"></span>
                            </div>
                            <div class="text-[11px] leading-tight text-slate-200 truncate font-medium">
                                <span class="text-amber-300 font-bold">SI UJANG &bull; WARTA KAMPUS:</span>
                                <span>Sampurasun! Temukan info terhangat ITG.</span>
                            </div>
                        </div>

                        {{-- Badge Header Institusi --}}
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/15 border border-amber-400/40 text-amber-300 text-[10px] sm:text-xs font-bold tracking-wide uppercase mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                            <span>Papan Pengumuman &amp; Arsip Dokumen Resmi ITG</span>
                        </div>

                        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight mb-3">
                            Pusat Informasi &amp; Regulasi
                        </h1>

                        <p class="text-slate-300 text-xs sm:text-base leading-relaxed mb-6 max-w-xl">
                            Pusat dokumentasi warta kegiatan ormawa, pengumuman resmi biro kemahasiswaan, dan pedoman regulasi kemahasiswaan Institut Teknologi Garut.
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
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                </svg>
                                <span>Warta Terpadu Ormawa</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-[11px] font-semibold">
                                <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span>Arsip Regulasi Sah</span>
                            </span>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Panggung Maskot Si Ujang Besar (Khusus Desktop lg) --}}
                    <div class="hidden lg:flex lg:col-span-5 flex-col items-center justify-center relative">
                        {{-- Speech Bubble Sapaan Informasi --}}
                        <div class="bg-white text-slate-900 text-xs sm:text-sm font-bold px-4 py-2.5 rounded-2xl shadow-2xl border border-slate-100 flex items-center gap-2 mb-3 relative">
                            <span>📢 Dapatkan info resmi dan agenda kampus terkini!</span>
                            <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-t-[8px] border-t-white"></div>
                        </div>

                        {{-- Karakter Maskot Si Ujang Full Body --}}
                        <div class="relative flex items-center justify-center">
                            <img src="{{ asset('images/maskot-itg-full.png') }}"
                                 alt="Maskot Si Ujang - Duta Informasi ITG"
                                 class="w-52 h-52 xl:w-60 xl:h-60 object-contain drop-shadow-[0_15px_25px_rgba(0,0,0,0.5)]">
                        </div>

                        <div class="mt-2 text-center">
                            <span class="inline-block px-3 py-1 rounded-full bg-slate-900/80 border border-slate-700/80 text-[11px] font-semibold text-amber-300">
                                Si Ujang &bull; Duta Informasi Kampus ITG
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- FLOATING CONTROL, TAB & FILTER BAR --}}
        <section class="max-w-6xl mx-auto px-4 sm:px-6 -mt-7 sm:-mt-9 relative z-20">
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-xl shadow-slate-900/5 space-y-4">
                {{-- Baris 1: Tab Utama Switcher & Tombol Aksi Administratif --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    {{-- Tabs Switcher: Berita/Pengumuman vs Regulasi/Pedoman --}}
                    <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 w-full sm:w-auto" role="tablist" aria-label="Tab Pusat Informasi">
                        <button type="button"
                                @click="activeTab = 'pengumuman'"
                                :class="activeTab === 'pengumuman' ? 'bg-[#1E3A8A] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                :aria-selected="activeTab === 'pengumuman'"
                                class="min-h-[44px] px-3.5 sm:px-5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            <span>Berita &amp; Pengumuman</span>
                        </button>
                        <button type="button"
                                @click="activeTab = 'regulasi'"
                                :class="activeTab === 'regulasi' ? 'bg-[#1E3A8A] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                :aria-selected="activeTab === 'regulasi'"
                                class="min-h-[44px] px-3.5 sm:px-5 rounded-xl text-xs sm:text-sm font-bold transition flex items-center justify-center gap-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            <span>Regulasi &amp; Pedoman</span>
                        </button>
                    </div>

                    {{-- Tombol Aksi Administratif Berdasarkan Role --}}
                    <div class="flex flex-wrap items-center gap-2">
                        @hasrole('bkhm')
                        <a href="{{ route('bkhm.kurasi.index') }}" x-show="activeTab === 'pengumuman'" class="inline-flex items-center gap-1.5 bg-blue-700 hover:bg-blue-800 text-white font-semibold min-h-[44px] px-3.5 rounded-xl text-xs sm:text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 shadow-xs">
                            <span>Kurasi Berita Kampus</span>
                            @if($antreanKurasiCount > 0)
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black bg-rose-600 text-white">{{ $antreanKurasiCount }}</span>
                            @endif
                        </a>
                        <button type="button" x-show="activeTab === 'pengumuman'" @click="showPengumumanModal = true" class="inline-flex items-center gap-1.5 bg-amber-700 hover:bg-amber-800 text-white font-semibold min-h-[44px] px-3.5 rounded-xl text-xs sm:text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Pengumuman Resmi</span>
                        </button>
                        @endhasrole

                        @hasrole('bem')
                        <button type="button" x-show="activeTab === 'pengumuman'" @click="showPengumumanModal = true" class="inline-flex items-center gap-1.5 bg-blue-700 hover:bg-blue-800 text-white font-semibold min-h-[44px] px-3.5 rounded-xl text-xs sm:text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Ajukan Berita / Agenda BEM</span>
                        </button>
                        @endhasrole

                        @hasrole('bpm')
                        <button type="button" x-show="activeTab === 'pengumuman'" @click="showPengumumanModal = true" class="inline-flex items-center gap-1.5 bg-blue-700 hover:bg-blue-800 text-white font-semibold min-h-[44px] px-3.5 rounded-xl text-xs sm:text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Ajukan Berita / Warta BPM</span>
                        </button>
                        <button type="button" x-show="activeTab === 'regulasi'" @click="showRegulasiModal = true" class="inline-flex items-center gap-2 bg-indigo-700 hover:bg-indigo-800 text-white font-semibold min-h-[44px] px-4 rounded-xl text-xs sm:text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Regulasi / UU</span>
                        </button>
                        @endhasrole

                        @hasrole('ormawa')
                        <button type="button" x-show="activeTab === 'pengumuman'" @click="showPengumumanModal = true" class="inline-flex items-center gap-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold min-h-[44px] px-3.5 rounded-xl text-xs sm:text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Ajukan Berita / Pamflet Acara</span>
                        </button>
                        @endhasrole
                    </div>
                </div>

                {{-- Baris 2: Filter Kategori & Kolom Pencarian --}}
                <div class="pt-3 border-t border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3" x-show="activeTab === 'pengumuman'">
                    {{-- Filter Tabs Kategori Pengumuman --}}
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none" role="tablist" aria-label="Filter Kategori Pengumuman">
                        <a href="{{ route('informasi.index') }}"
                           class="min-h-[44px] px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition shrink-0 inline-flex items-center justify-center {{ empty($kategoriFilter) || $kategoriFilter === 'semua' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            Semua
                        </a>
                        <a href="{{ route('informasi.index', ['kategori' => 'resmi_kampus']) }}"
                           class="min-h-[44px] px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition shrink-0 inline-flex items-center justify-center {{ $kategoriFilter === 'resmi_kampus' ? 'bg-amber-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            Resmi Kampus (BKHM)
                        </a>
                        <a href="{{ route('informasi.index', ['kategori' => 'kegiatan_kemahasiswaan']) }}"
                           class="min-h-[44px] px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition shrink-0 inline-flex items-center justify-center {{ $kategoriFilter === 'kegiatan_kemahasiswaan' ? 'bg-blue-700 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            Agenda &amp; Acara Ormawa
                        </a>
                    </div>

                    {{-- Kolom Input Pencarian Cepat --}}
                    <div class="relative w-full md:w-80 shrink-0">
                        <label for="search-informasi" class="sr-only">Cari Berita atau Pengumuman</label>
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input id="search-informasi"
                               type="text"
                               x-model="searchQuery"
                               placeholder="Cari berita, agenda acara..."
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
            </div>
        </section>

        {{-- MAIN CONTENT AREA --}}
        <main class="max-w-6xl mx-auto px-4 sm:px-6 py-10 sm:py-14">
            @if ($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-r-xl mb-6">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <svg class="w-5 h-5 text-rose-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <span>Terjadi kesalahan pada input:</span>
                    </div>
                    <ul class="list-disc pl-5 text-xs sm:text-sm space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- TAB 1: PENGUMUMAN & WARTA KAMPUS --}}
            <div x-show="activeTab === 'pengumuman'" class="space-y-8">
                @hasanyrole('ormawa|bem|bpm')
                @if($pengumumanSaya->isNotEmpty())
                    {{-- Status Pengajuan Berita Lembaga Saya --}}
                    <div class="bg-emerald-50/80 rounded-2xl p-5 border border-emerald-200 shadow-xs">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-emerald-700 text-white flex items-center justify-center shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5 6 9H3v6h3l5 4V5z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.5 8.5a5 5 0 0 1 0 7"/>
                                    </svg>
                                </span>
                                <div>
                                    <h4 class="font-bold text-sm text-emerald-950">Status Pengajuan Berita Saya (Kurasi BKHM)</h4>
                                    <p class="text-[11px] text-emerald-800">Pantau proses persetujuan artikel sebelum dipublikasikan ke publik</p>
                                </div>
                            </div>
                            <span class="text-xs text-emerald-800 font-bold bg-emerald-200/60 px-2.5 py-1 rounded-lg">{{ $pengumumanSaya->count() }} pengajuan</span>
                        </div>
                        <div class="space-y-2.5">
                            @foreach($pengumumanSaya as $ps)
                                <div class="bg-white border border-emerald-100 rounded-xl p-3.5 flex flex-wrap items-center justify-between gap-2 text-xs shadow-2xs">
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-slate-800">{{ $ps->judul }}</span>
                                        <span class="text-slate-500 block text-[11px]">{{ $ps->created_at->format('d/m/Y H:i') }} WIB</span>
                                        @if($ps->catatan_kurasi)
                                            <div class="text-rose-800 bg-rose-50 p-2 rounded-lg mt-1 text-[11px] border border-rose-200">
                                                <strong>Catatan Revisi BKHM:</strong> {{ $ps->catatan_kurasi }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="px-3 py-1 rounded-full font-bold text-[11px] {{ $ps->status === 'published' ? 'bg-emerald-100 text-emerald-800' : ($ps->status === 'pending_kurasi' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                        {{ $ps->status_label }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                @endhasanyrole

                {{-- Grid Kartu Pengumuman Berstandar Home --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
                    @forelse($pengumuman as $p)
                    <article x-show="matches('{{ addslashes($p->judul ?? '') }}', '{{ addslashes($p->isi ?? '') }}', '{{ addslashes($p->user->name ?? '') }}')"
                             x-transition
                             class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:border-amber-400 hover:shadow-xl transition-all duration-200 flex flex-col justify-between overflow-hidden group">
                        <div>
                            {{-- Foto Sampul atau Fallback Geometric Banner --}}
                            @if($p->gambar_sampul)
                                <a href="{{ route('informasi.show', $p) }}" class="block relative aspect-video w-full overflow-hidden bg-slate-900">
                                    <img src="{{ $p->gambar_url }}" alt="{{ $p->judul }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                                    <div class="absolute top-3 left-3">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] sm:text-[11px] font-bold bg-white/95 text-slate-900 border border-slate-200 shadow-sm">
                                            {{ $p->badge_label }}
                                        </span>
                                    </div>
                                </a>
                            @else
                                {{-- Fallback Banner Geometris Elegan Bertema ITG --}}
                                <div class="relative h-36 sm:h-40 w-full bg-gradient-to-br from-[#0B1528] via-[#132342] to-[#1E3A8A] p-4 flex flex-col justify-between overflow-hidden">
                                    <div class="absolute -right-4 -bottom-4 w-24 h-24 rounded-full bg-amber-400/10 blur-xl pointer-events-none"></div>
                                    <div class="flex items-center justify-between gap-2 relative z-10">
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] sm:text-[11px] font-bold uppercase tracking-wider bg-white/10 text-amber-300 border border-amber-300/30">
                                            {{ $p->badge_label }}
                                        </span>
                                        <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center text-amber-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="relative z-10 flex items-center gap-2 text-slate-300 text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Warta Resmi Kemahasiswaan ITG</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Isi Berita / Pengumuman --}}
                            <div class="p-5 sm:p-6">
                                <div class="flex items-center justify-between gap-2 mb-2.5">
                                    <div class="flex items-center gap-2">
                                        @if($p->tanggal_kegiatan)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-800 bg-blue-50 px-2.5 py-0.5 rounded-md border border-blue-100">
                                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $p->tanggal_kegiatan->format('d M Y') }}</span>
                                            </span>
                                        @else
                                            <span class="text-[11px] text-slate-500">
                                                {{ $p->created_at->format('d M Y') }}
                                            </span>
                                        @endif
                                    </div>

                                    @if(Auth::check() && (Auth::user()->hasAnyRole(['bem', 'bkhm', 'admin']) || Auth::id() === $p->user_id))
                                    <form action="{{ route('informasi.pengumuman.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex items-center min-h-[44px] px-2 text-rose-700 hover:text-rose-900 text-xs font-semibold hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 rounded">Hapus</button>
                                    </form>
                                    @endif
                                </div>

                                <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-2 leading-snug group-hover:text-amber-800 transition">
                                    <a href="{{ route('informasi.show', $p) }}">
                                        {{ $p->judul }}
                                    </a>
                                </h2>

                                <p class="text-slate-600 text-xs sm:text-sm whitespace-pre-line leading-relaxed line-clamp-3">
                                    {{ $p->isi }}
                                </p>
                            </div>
                        </div>

                        {{-- Footer Kartu Berita --}}
                        <div class="px-5 sm:px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                            <div class="flex items-center gap-2.5 truncate pr-2">
                                @if($p->user?->hasCustomAvatar())
                                    <img src="{{ $p->user->avatar_url }}"
                                         alt="{{ $p->user->name }}"
                                         class="w-7 h-7 rounded-full object-contain border border-slate-200 shrink-0 bg-white shadow-2xs p-0.5">
                                @else
                                    <div class="w-7 h-7 rounded-full bg-[#1E3A8A] text-amber-300 font-bold text-xs flex items-center justify-center shrink-0 border border-blue-400/30 shadow-2xs">
                                        {{ strtoupper(substr($p->user->name ?? 'K', 0, 1)) }}
                                    </div>
                                @endif
                                <span class="truncate font-medium text-slate-800">{{ $p->user->name ?? 'Kemahasiswaan' }}</span>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @if($p->file_lampiran)
                                <a href="{{ route('informasi.pengumuman.lampiran', $p) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center min-h-[44px] gap-1 px-2.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 font-medium rounded-lg text-xs transition-colors" title="Unduh lampiran">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    <span class="hidden sm:inline">Lampiran</span>
                                </a>
                                @endif
                                <a href="{{ route('informasi.show', $p) }}" class="inline-flex items-center min-h-[44px] gap-1 font-bold text-amber-800 hover:text-amber-900 transition">
                                    <span>Detail</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                    @empty
                    <div class="col-span-full text-center py-16 bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-10">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 bg-blue-50 rounded-2xl border border-blue-200/80 flex items-center justify-center mx-auto mb-4 text-[#1E3A8A] shadow-2xs">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                            </svg>
                        </div>
                        <h2 class="text-base sm:text-xl font-bold text-slate-900">Belum Ada Pengumuman Terbaru</h2>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-md mx-auto leading-relaxed">
                            Pengumuman resmi kemahasiswaan dan warta agenda ormawa akan ditampilkan pada laman ini.
                        </p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- TAB 2: REGULASI & PEDOMAN RESMI ITG --}}
            <div x-show="activeTab === 'regulasi'" style="display: none;" class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-sm">
                    <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-base sm:text-lg text-slate-900">Katalog Regulasi &amp; Pedoman Kemahasiswaan ITG</h3>
                            <p class="text-xs sm:text-sm text-slate-600 mt-0.5">Dokumentasi hukum organisasi mahasiswa, undang-undang DEMA, dan juklak/juknis kegiatan resmi.</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold self-start sm:self-auto">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>{{ $regulasi->count() }} Dokumen Terdaftar</span>
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <x-table>
                            <x-table.thead>
                                <x-table.tr>
                                    <x-table.th>Judul Dokumen</x-table.th>
                                    <x-table.th>Kategori</x-table.th>
                                    <x-table.th>Diterbitkan Oleh</x-table.th>
                                    <x-table.th align="center">Aksi</x-table.th>
                                </x-table.tr>
                            </x-table.thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @forelse($regulasi as $r)
                                <x-table.tr class="hover:bg-slate-50/80 transition-colors">
                                    <x-table.td>
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-red-50 text-red-700 flex items-center justify-center shrink-0 border border-red-200/60 mt-0.5">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 leading-snug">{{ $r->judul }}</p>
                                                @if($r->deskripsi)
                                                    <p class="text-xs text-slate-600 mt-1 line-clamp-2 leading-relaxed">{{ $r->deskripsi }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </x-table.td>
                                    <x-table.td>
                                        @php
                                            $badgeColor = match($r->kategori) {
                                                'Undang-Undang' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                                'Pedoman' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'Pengumuman' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                default => 'bg-slate-100 text-slate-800 border-slate-200',
                                            };
                                        @endphp
                                        <span class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                            {{ $r->kategori }}
                                        </span>
                                    </x-table.td>
                                    <x-table.td class="text-slate-600">
                                        <div class="flex items-center gap-2.5">
                                            @if($r->user?->hasCustomAvatar())
                                                <img src="{{ $r->user->avatar_url }}"
                                                     alt="{{ $r->user->name }}"
                                                     class="w-6 h-6 rounded-full object-contain border border-slate-200 shrink-0 bg-white p-0.5 shadow-2xs">
                                            @endif
                                            <div class="min-w-0">
                                                <span class="font-medium text-slate-800 block truncate">{{ $r->user->name }}</span>
                                                <span class="block text-xs text-slate-500 mt-0.5">{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y') }}</span>
                                            </div>
                                        </div>
                                    </x-table.td>
                                    <x-table.td align="center" class="whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('informasi.regulasi.unduh', $r) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center min-h-[44px] gap-1.5 px-3.5 bg-blue-50 hover:bg-blue-100 text-blue-800 font-semibold text-xs rounded-xl transition-colors border border-blue-200/60 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                <span>Unduh PDF</span>
                                            </a>

                                            @hasrole('bpm')
                                            @if($r->user_id === Auth::id())
                                            <form action="{{ route('informasi.regulasi.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('Hapus regulasi ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="inline-flex items-center min-h-[44px] px-2 text-rose-700 hover:text-rose-900 text-xs font-semibold hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 rounded">Hapus</button>
                                            </form>
                                            @endif
                                            @endhasrole
                                        </div>
                                    </x-table.td>
                                </x-table.tr>
                                @empty
                                <x-table.empty colspan="4" message="Belum ada dokumen regulasi yang diunggah." />
                                @endforelse
                            </tbody>
                        </x-table>
                    </div>
                </div>
            </div>
        </main>

        {{-- CALLOUT BANNER APRESIASI & PUBLIKASI ORMAWA --}}
        <section class="max-w-6xl mx-auto px-4 sm:px-6 my-10 sm:my-14">
            <div class="bg-gradient-to-r from-blue-500/10 via-amber-500/5 to-amber-500/10 border border-blue-200/90 rounded-2xl p-5 sm:p-7 flex flex-col sm:flex-row items-center justify-between gap-5 shadow-sm">
                <div class="flex items-center gap-4 text-left w-full sm:w-auto">
                    <div class="relative shrink-0">
                        <img src="{{ asset('images/maskot-itg-head.png') }}"
                             alt="Si Ujang"
                             class="w-12 h-12 sm:w-16 sm:h-16 rounded-2xl bg-amber-100 p-1 border border-amber-300 object-contain shadow-2xs">
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">
                            Ormawa Ingin Mengadakan Kegiatan atau Sosialisasi?
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-0.5 leading-relaxed">
                            Ajukan pamflet acara, seminar, atau warta organisasi Anda untuk ditinjau oleh Biro Kemahasiswaan (BKHM) agar tayang di papan pengumuman resmi kampus.
                        </p>
                    </div>
                </div>

                <div class="w-full sm:w-auto shrink-0 flex items-center gap-3">
                    <a href="{{ route('layanan.aspirasi.create') }}"
                       class="inline-flex items-center justify-center gap-2 min-h-[44px] w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-[#1E3A8A] hover:bg-blue-900 text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 shadow-sm">
                        <span>Layanan Mahasiswa</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        {{-- MODAL TAMBAH PENGUMUMAN --}}
        @hasanyrole('bem|bpm|bkhm|ormawa|admin')
        <div x-show="showPengumumanModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" @keydown.escape.window="showPengumumanModal = false">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 transition-opacity bg-slate-900/60" @click="showPengumumanModal = false"></div>
                <div class="relative bg-white w-full max-w-lg p-6 rounded-2xl border border-slate-200 shadow-2xl">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900" id="judul-modal-pengumuman">
                                @role('bkhm') Terbitkan Pengumuman Resmi Kampus
                                @elserole('bem') Ajukan Berita / Agenda BEM
                                @elserole('bpm') Ajukan Berita / Warta BPM
                                @elserole('ormawa') Ajukan Publikasi Berita / Acara Ormawa
                                @else Buat Pengumuman Baru
                                @endrole
                            </h3>
                            <p class="text-xs text-slate-600 mt-0.5">
                                @hasanyrole('ormawa|bem|bpm')
                                    Berita yang Anda ajukan akan dikurasi oleh Humas BKHM terlebih dahulu sebelum tayang ke publik.
                                @else
                                    Pengumuman akan langsung dipublikasikan dan dapat diakses sivitas kampus.
                                @endhasanyrole
                            </p>
                        </div>
                        <button type="button" @click="showPengumumanModal = false" aria-label="Tutup" class="inline-flex items-center justify-center w-11 h-11 -mr-2 text-slate-500 hover:text-slate-700 text-2xl font-bold rounded-lg hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">&times;</button>
                    </div>

                    @hasanyrole('ormawa|bem|bpm')
                    <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-2.5 text-xs text-amber-900">
                        <svg class="w-4 h-4 text-amber-700 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Draf publikasi Anda akan melalui proses kurasi oleh Humas BKHM ITG. Pastikan pamflet atau informasi jelas dan tidak melanggar etika kemahasiswaan.</span>
                    </div>
                    @endhasanyrole

                    <form id="form-tambah-pengumuman" action="{{ route('informasi.pengumuman.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="judul" value="Judul Pengumuman / Agenda" />
                                <x-text-input id="judul" name="judul" type="text" class="mt-1 block w-full" placeholder="Contoh: Open Recruitment Anggota Baru / Lomba Nasional" required />
                            </div>
                            <div>
                                <x-input-label for="tanggal_kegiatan" value="Tanggal Kegiatan (Opsional untuk Agenda Acara)" />
                                <x-text-input id="tanggal_kegiatan" name="tanggal_kegiatan" type="date" class="mt-1 block w-full" />
                            </div>
                            <div>
                                <x-input-label for="isi" value="Isi / Deskripsi Lengkap" />
                                <textarea id="isi" name="isi" rows="4" class="border-slate-300 focus:border-amber-600 focus:ring-amber-600 rounded-lg block mt-1 w-full text-sm" placeholder="Rincian isi pengumuman, deskripsi agenda, persyaratan lomba, narasi..." required></textarea>
                            </div>
                            <div>
                                <x-input-label for="gambar_sampul" value="Gambar Sampul / Poster Pamflet (Opsional, JPG/PNG/WebP maks 5MB)" />
                                <input id="gambar_sampul" name="gambar_sampul" type="file" accept="image/jpeg,image/png,image/webp" class="mt-1 block w-full border border-slate-300 rounded-lg p-2 text-sm text-slate-600 bg-slate-50" />
                                <p class="text-[11px] text-slate-500 mt-1">Akan ditampilkan sebagai cover poster pada kartu dan halaman detail.</p>
                            </div>
                            <div>
                                <x-input-label for="file_lampiran" value="Dokumen Panduan / Lampiran PDF (Opsional, maks 5MB)" />
                                <input id="file_lampiran" name="file_lampiran" type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full border border-slate-300 rounded-lg p-2 text-sm text-slate-600 bg-slate-50" />
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3 pt-3 border-t border-slate-200">
                            <button type="button" @click="showPengumumanModal = false" class="inline-flex items-center min-h-[44px] px-4 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 font-medium text-sm">Batal</button>
                            <x-primary-button id="btn-submit-pengumuman">
                                @hasanyrole('ormawa|bem|bpm') Ajukan ke BKHM @else Terbitkan Sekarang @endhasanyrole
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endhasanyrole

        {{-- MODAL TAMBAH REGULASI --}}
        @hasrole('bpm')
        <div x-show="showRegulasiModal" id="modal-regulasi" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" @keydown.escape.window="showRegulasiModal = false">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 transition-opacity bg-slate-900/60" @click="showRegulasiModal = false"></div>
                <div class="relative bg-white w-full max-w-lg p-6 rounded-2xl border border-slate-200 shadow-2xl">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-200">
                        <h3 class="text-lg font-bold text-slate-900">Tambah Regulasi / Pedoman Baru</h3>
                        <button type="button" @click="showRegulasiModal = false" aria-label="Tutup" class="inline-flex items-center justify-center w-11 h-11 -mr-2 text-slate-500 hover:text-slate-700 text-2xl font-bold rounded-lg hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">&times;</button>
                    </div>
                    <form id="form-tambah-regulasi" action="{{ route('informasi.regulasi.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="judul_reg" value="Judul Dokumen" />
                                <x-text-input id="judul_reg" name="judul" type="text" class="mt-1 block w-full" placeholder="Contoh: UU DEMA No. 2 Tahun 2026" required />
                            </div>
                            <div>
                                <x-input-label for="kategori_reg" value="Kategori Regulasi" />
                                <select name="kategori" id="kategori_reg" class="border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg block mt-1 w-full text-sm py-3 px-3.5" required>
                                    <option value="Undang-Undang">Undang-Undang</option>
                                    <option value="Pedoman">Pedoman</option>
                                    <option value="Pengumuman">Pengumuman</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="deskripsi_reg" value="Deskripsi Singkat" />
                                <textarea id="deskripsi_reg" name="deskripsi" rows="3" class="border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg block mt-1 w-full text-sm" placeholder="Penjelasan singkat isi regulasi..."></textarea>
                            </div>
                            <div>
                                <x-input-label for="file_dokumen" value="Dokumen PDF (Maks. 10MB)" />
                                <input id="file_dokumen" name="file_path" type="file" accept=".pdf" class="mt-1 block w-full border border-slate-300 rounded-lg p-2 text-sm text-slate-600 bg-slate-50" required />
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3 pt-3 border-t border-slate-200">
                            <button type="button" @click="showRegulasiModal = false" class="inline-flex items-center min-h-[44px] px-4 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 font-medium text-sm">Batal</button>
                            <x-primary-button id="btn-submit-regulasi">Unggah Dokumen</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endhasrole
    </div>
</x-public-layout>
