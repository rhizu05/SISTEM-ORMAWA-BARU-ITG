<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SKIN ITG — Sistem Informasi Kemahasiswaan') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-50 text-slate-900 font-sans selection:bg-indigo-500 selection:text-white flex flex-col min-h-screen">
    {{-- Navbar Publik --}}
    <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-700 to-indigo-500 flex items-center justify-center shadow-md text-white font-bold text-xl tracking-wider">
                    ITG
                </div>
                <div>
                    <span class="font-extrabold text-lg tracking-tight text-slate-900 block leading-tight">SKIN ITG</span>
                    <span class="text-[11px] font-medium text-slate-500 block leading-none">Sistem Informasi Kemahasiswaan</span>
                </div>
            </div>

            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                <a href="{{ route('layanan.index') }}" class="text-blue-600 font-bold hover:text-blue-700 transition-colors">Portal Layanan Mahasiswa</a>
                <a href="{{ route('layanan.cek-status') }}" class="hover:text-indigo-600 transition-colors">Lacak Tiket</a>
                <a href="{{ route('prestasi.showcase') }}" class="hover:text-indigo-600 transition-colors">Showcase Prestasi</a>
                <a href="{{ route('informasi.index') }}" class="hover:text-indigo-600 transition-colors">Pengumuman & Regulasi</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-sm transition-all focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                        <span>Buka Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-sm transition-all focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1">
                        <span>Login Pengurus</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <main class="flex-grow">
        <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28 bg-gradient-to-b from-indigo-50/70 via-white to-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100/80 text-indigo-700 text-xs font-semibold uppercase tracking-wider mb-6 border border-indigo-200/60">
                        <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                        Portal Resmi Kemahasiswaan Institut Teknologi Garut
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-tight mb-6">
                        Satu Portal Terpadu <br class="hidden sm:inline" />
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-600">Aktivitas Kemahasiswaan</span>
                    </h1>
                    <p class="text-base sm:text-lg text-slate-600 mb-8 leading-relaxed">
                        Layanan aspirasi, konseling personal BKHM secara rahasia, pelaporan prestasi, pengajuan dana delegasi lomba, hingga tata kelola ormawa dan peminjaman fasilitas kampus terintegrasi.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('layanan.index') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm sm:text-base shadow-md hover:shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <span>Portal Layanan Mahasiswa (Tanpa Login)</span>
                        </a>
                        <a href="{{ route('layanan.cek-status') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-white border border-slate-300 hover:border-slate-400 hover:bg-slate-50 text-slate-700 font-bold text-sm sm:text-base shadow-sm transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>Lacak Status Tiket</span>
                        </a>
                        <a href="{{ route('prestasi.showcase') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold text-sm sm:text-base transition-all flex items-center justify-center gap-2">
                            <span>🏆 Showcase Prestasi</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Layanan Utama Grid --}}
        <section id="layanan" class="py-16 bg-white border-y border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">Layanan Kemahasiswaan</h2>
                    <p class="text-sm sm:text-base text-slate-600 mt-2">Akses fasilitas dan administrasi kemahasiswaan dengan alur yang cepat, transparan, dan terstandar.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {{-- Card 1: Portal Layanan Mahasiswa --}}
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6 hover:shadow-lg transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Portal Layanan & Konseling</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Mahasiswa dapat menyampaikan aspirasi, mengajukan konseling personal rahasia ke BKHM, serta mendaftarkan prestasi & bantuan dana delegasi lomba tanpa perlu login akun.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-200 flex items-center justify-between">
                            <a href="{{ route('layanan.index') }}" class="text-blue-600 font-semibold text-sm hover:text-blue-700 flex items-center gap-1">
                                <span>Buka Portal Layanan</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                            <a href="{{ route('layanan.cek-status') }}" class="text-xs text-slate-500 hover:text-blue-600">
                                Lacak Tiket &rarr;
                            </a>
                        </div>
                    </div>

                    {{-- Card 2: Pengumuman & Regulasi --}}
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6 hover:shadow-lg transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Pusat Informasi & Regulasi</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Akses terbuka untuk seluruh sivitas akademika: berita kegiatan dari BEM, pedoman ormawa, dan undang-undang kemahasiswaan dari BPM tanpa perlu login.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-200">
                            <a href="{{ route('informasi.index') }}" class="text-blue-600 font-semibold text-sm hover:text-blue-700 flex items-center gap-1">
                                <span>Lihat Informasi Publik</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Card 3: Administrasi & Workflow --}}
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-6 hover:shadow-lg transition-all flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-5">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Pengajuan & Verifikasi Ormawa</h3>
                            <p class="text-sm text-slate-600 leading-relaxed">
                                Pengajuan anggaran berbasis saldo ormawa, generator proposal & LPJ otomatis berkop surat resmi ITG, serta tracking persetujuan berjenjang hingga Bendahara.
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-200">
                            <a href="{{ route('login') }}" class="text-emerald-600 font-semibold text-sm hover:text-emerald-700 flex items-center gap-1">
                                <span>Masuk ke Akun Ormawa</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Footer Resmi Kampus --}}
    <footer class="bg-slate-900 text-slate-400 py-10 text-sm border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <span class="text-white font-bold text-base block mb-2">Institut Teknologi Garut (ITG)</span>
                    <p class="text-xs leading-relaxed text-slate-400">
                        Jalan Mayor Syamsu No. 1 Jayaraga, Garut 44151<br />
                        Jawa Barat, Indonesia
                    </p>
                </div>
                <div>
                    <span class="text-white font-bold text-base block mb-2">Biro Kemahasiswaan (BKHM)</span>
                    <p class="text-xs leading-relaxed text-slate-400">
                        Layanan administrasi, ormawa, prestasi, serta fasilitas sarana & prasarana kegiatan kemahasiswaan ITG.
                    </p>
                </div>
                <div>
                    <span class="text-white font-bold text-base block mb-2">Kontak & Bantuan</span>
                    <p class="text-xs leading-relaxed text-slate-400">
                        Website: <a href="https://itg.ac.id" target="_blank" class="hover:underline text-indigo-400">www.itg.ac.id</a><br />
                        Email: info@itg.ac.id | kemahasiswaan@itg.ac.id
                    </p>
                </div>
            </div>
            <div class="pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Institut Teknologi Garut. Seluruh hak cipta dilindungi.</p>
                <p class="mt-2 sm:mt-0">Sistem Informasi Kemahasiswaan (SKIN)</p>
            </div>
        </div>
    </footer>
</body>
</html>