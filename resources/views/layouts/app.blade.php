<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' - ' : '' }}SKIN - Sistem Ormawa Institut Teknologi Garut</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-900 bg-slate-50">
        {{-- UI-019: skip link untuk pengguna keyboard/screen reader --}}
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-2 focus:left-2 focus:bg-white focus:text-blue-700 focus:px-4 focus:py-2 focus:rounded-xl focus:shadow-md">
            Lewati ke konten utama
        </a>
        <div class="min-h-screen bg-slate-50" x-data="{
            isMobile: window.innerWidth < 1024,
            sidebarOpen: window.innerWidth >= 1024 
                ? (localStorage.getItem('skin.sidebarOpen') !== null ? localStorage.getItem('skin.sidebarOpen') === '1' : true)
                : false,
            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
                if (!this.isMobile) {
                    localStorage.setItem('skin.sidebarOpen', this.sidebarOpen ? '1' : '0');
                }
            },
            init() {
                window.addEventListener('resize', () => {
                    const wasMobile = this.isMobile;
                    this.isMobile = window.innerWidth < 1024;
                    if (wasMobile !== this.isMobile) {
                        this.sidebarOpen = !this.isMobile && (localStorage.getItem('skin.sidebarOpen') !== null ? localStorage.getItem('skin.sidebarOpen') === '1' : true);
                    }
                });
            }
        }">
            <div class="flex h-screen overflow-hidden">
                
                <!-- Sidebar (Opsi 4) -->
                @include('layouts.sidebar')

                <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
                    
                    <!-- Top Header / Top Bar (Opsi 1: Clean Academic Light) -->
                    <header class="min-h-[72px] flex items-center justify-between px-4 sm:px-6 lg:px-8 py-3 bg-white border-b border-slate-200/90 sticky top-0 z-40 transition-all select-none">
                        
                        {{-- Kiri: Tombol Toggle Sidebar, Breadcrumb & Header Title --}}
                        <div class="flex items-center min-w-0 mr-4">
                            <button @click="toggleSidebar()" 
                                    aria-label="Buka atau tutup menu navigasi" 
                                    :aria-expanded="sidebarOpen" 
                                    title="Perluas / perkecil menu samping" 
                                    class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 hover:text-slate-900 border border-slate-200/90 flex items-center justify-center transition shadow-xs shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <div class="ml-3 sm:ml-4 min-w-0">
                                @php
                                    $rawHeader = isset($header) ? (string)$header : '';
                                    
                                    // 1. Ekstrak judul bersih dari tag heading <h1>..<h4> jika ada, bukan seluruh isi div/link/paragraf
                                    $extractedTitle = '';
                                    if (preg_match('/<h[1-4][^>]*>(.*?)<\/h[1-4]>/is', $rawHeader, $matches)) {
                                        $extractedTitle = trim(strip_tags($matches[1]));
                                    } elseif (!empty($rawHeader)) {
                                        // Ambil potongan teks pertama sebelum tag paragraf/div/link
                                        $parts = preg_split('/<(?:p|div|ul|ol|table|a|button)\b/i', $rawHeader);
                                        $extractedTitle = trim(strip_tags($parts[0] ?? $rawHeader));
                                    }

                                    // Decode HTML entities (seperti &amp; -> &, &larr; -> ←)
                                    $extractedTitle = html_entity_decode($extractedTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                    
                                    if (empty($extractedTitle)) {
                                        $extractedTitle = 'Dashboard';
                                    }

                                    $rawRole = Auth::user() ? Auth::user()->getRoleNames()->first() : null;
                                    $roleShort = match ($rawRole) {
                                        'admin' => 'Admin',
                                        'bkhm' => 'BKHM',
                                        'sarpras' => 'Sarpras',
                                        'wr3' => 'WR3',
                                        'bem' => 'BEM',
                                        'bpm' => 'BPM',
                                        'ormawa' => 'Ormawa',
                                        'bendahara' => 'Bendahara',
                                        default => 'Portal',
                                    };

                                    $panelTitle = match ($rawRole) {
                                        'admin' => 'Panel Administrator Sistem',
                                        'bkhm' => 'Panel Verifikator BKHM',
                                        'sarpras' => 'Panel Manajemen Sarpras',
                                        'wr3' => 'Panel Wakil Rektor III',
                                        'bem' => 'Panel Pengurus BEM ITG',
                                        'bpm' => 'Panel Pengurus BPM ITG',
                                        'ormawa' => 'Portal Pengurus Ormawa',
                                        'bendahara' => 'Panel Bendahara Kampus',
                                        default => 'Dashboard Utama',
                                    };

                                    $isDashboardRoute = request()->routeIs('dashboard');
                                    $displayTitle = $isDashboardRoute ? $panelTitle : $extractedTitle;
                                    
                                    // Breadcrumb title: ringkas (maks 26 karakter) agar tidak mentok
                                    $displayBreadcrumb = $isDashboardRoute 
                                        ? 'Dashboard Utama' 
                                        : \Illuminate\Support\Str::limit($extractedTitle, 26, '…');

                                    // Cek apakah header memiliki action button atau deskripsi tambahan dari sub-view
                                    $hasActionLink = (bool) preg_match('/<a\b[^>]*>/i', $rawHeader);
                                    $hasParagraph = (bool) preg_match('/<p\b[^>]*>/i', $rawHeader);
                                    $isRichHeader = $hasActionLink || $hasParagraph;
                                @endphp

                                {{-- Breadcrumb Navigation 3 Level (Anti Mentok & Truncated) --}}
                                <nav aria-label="Breadcrumb" class="hidden sm:flex items-center gap-1.5 text-[11px] font-medium text-slate-400 mb-1 leading-none select-none max-w-full">
                                    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-blue-700 transition shrink-0">Sistem Ormawa</a>
                                    <svg class="w-3 h-3 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    <span class="text-slate-400 shrink-0">{{ $breadcrumbGroup ?? $roleShort }}</span>
                                    <svg class="w-3 h-3 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    <span class="text-[#1E40AF] font-bold truncate max-w-[200px]" title="{{ $extractedTitle }}">{{ $breadcrumbTitle ?? $displayBreadcrumb }}</span>
                                </nav>

                                {{-- Page Title & Academic Year Badge (Flex-nowrap agar satu baris) --}}
                                <div class="flex items-center gap-2.5 flex-nowrap min-w-0">
                                    <h1 class="text-sm sm:text-base lg:text-lg font-extrabold text-slate-900 tracking-tight leading-tight truncate max-w-[160px] sm:max-w-xs xl:max-w-md" title="{{ $displayTitle }}">
                                        {{ $displayTitle }}
                                    </h1>
                                    <span class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-[10px] sm:text-[11px] font-bold tracking-wide shadow-xs shrink-0 select-none whitespace-nowrap">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        T.A. 2026/2027 Ganjil
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Tengah: Quick Search Bar Global dengan Pintasan Keyboard (Ctrl + K) --}}
                        <div class="hidden xl:flex items-center mx-4 flex-1 max-w-sm 2xl:max-w-md" 
                             x-data="{ searchModalOpen: false }" 
                             @keydown.window.prevent.ctrl.k="searchModalOpen = true; $nextTick(() => $refs.searchInput?.focus())">
                            <div class="relative w-full group">
                                <div class="w-full flex items-center gap-2.5 px-3.5 py-2 rounded-2xl bg-slate-50/80 border border-slate-200/90 hover:border-slate-300 focus-within:border-blue-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-blue-100 transition shadow-xs cursor-pointer"
                                     @click="searchModalOpen = true">
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <input type="text" 
                                           x-ref="searchInput"
                                           placeholder="Cari proposal, nomor surat, tiket..." 
                                           class="w-full bg-transparent text-xs text-slate-700 placeholder-slate-400 border-none p-0 focus:outline-none focus:ring-0 cursor-pointer"
                                           @focus="searchModalOpen = true"
                                           aria-label="Pencarian Cepat">
                                    <kbd class="hidden sm:inline-flex items-center px-1.5 py-0.5 rounded-md bg-white border border-slate-200 text-[10px] font-bold text-slate-400 shadow-xs select-none whitespace-nowrap shrink-0">
                                        Ctrl + K
                                    </kbd>
                                </div>
                                
                                {{-- Quick Search Dropdown / Command Palette --}}
                                <div x-show="searchModalOpen" 
                                     x-cloak
                                     @click.away="searchModalOpen = false"
                                     @keydown.escape.window="searchModalOpen = false"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                                     class="absolute left-0 right-0 top-full mt-2 bg-white rounded-2xl border border-slate-200 shadow-2xl p-3 z-50">
                                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider px-2 mb-2">Pintasan Navigasi &amp; Pencarian</p>
                                    <div class="space-y-1 text-xs">
                                        <a href="{{ route('pengajuan.index') }}" class="flex items-center justify-between p-2 rounded-xl hover:bg-blue-50/70 text-slate-700 hover:text-blue-700 font-medium transition">
                                            <span class="flex items-center gap-2">📄 Daftar Semua Proposal Ormawa</span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-semibold">Modul Pengajuan</span>
                                        </a>
                                        <a href="{{ route('generator.letters.create') }}" class="flex items-center justify-between p-2 rounded-xl hover:bg-blue-50/70 text-slate-700 hover:text-blue-700 font-medium transition">
                                            <span class="flex items-center gap-2">✉️ Buat Surat Tugas &amp; Pengantar</span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 font-semibold">Modul Surat</span>
                                        </a>
                                        <a href="{{ route('layanan.tracking') }}" target="_blank" class="flex items-center justify-between p-2 rounded-xl hover:bg-blue-50/70 text-slate-700 hover:text-blue-700 font-medium transition">
                                            <span class="flex items-center gap-2">🎫 Lacak Tiket Layanan Mahasiswa</span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-blue-100 text-blue-700 font-bold">Portal Publik ↗</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kanan: Action Buttons & Profil Pengguna --}}
                        <div class="flex items-center space-x-2.5 sm:space-x-3 shrink-0">
                            @include('layouts.user-menu')
                        </div>
                    </header>

                    <!-- Main Content -->
                    <main id="main-content" class="p-4 sm:p-6 lg:p-8">
                        {{-- SEC-03: flash dirender SEKALI di sini. Jangan tambahkan rendering
                             flash per-view, halaman yang lupa merendernya membuat pesan
                             hilang tanpa jejak (bug F1-F4, BACKLOG-004). --}}
                        <x-flash />

                        {{-- Banner Aksi Halaman jika sub-view memiliki tombol aksi atau deskripsi kaya --}}
                        @if ($isRichHeader)
                            <div class="mb-6 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs">
                                {!! $header !!}
                            </div>
                        @endif

                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
