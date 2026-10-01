<x-public-layout title="Portal Layanan Mahasiswa" brand-label="Portal Layanan Mahasiswa" accent="indigo">
    <x-slot name="nav">
        <a href="{{ route('informasi.index') }}"
           class="inline-flex items-center min-h-[44px] px-2.5 sm:px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:text-blue-700 hover:bg-slate-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
            Informasi Kampus
        </a>
        <a href="{{ route('prestasi.showcase') }}"
           class="inline-flex items-center min-h-[44px] px-2.5 sm:px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:text-blue-700 hover:bg-slate-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
            Prestasi
        </a>
        <a href="{{ route('layanan.cek-status') }}"
           class="inline-flex items-center gap-1.5 min-h-[44px] px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span class="hidden sm:inline">Lacak Tiket</span>
            <span class="sm:hidden">Lacak</span>
        </a>
    </x-slot>

    {{-- HERO SECTION: Deep Academic Navy dengan Watermark Logo Obor Monokrom & Maskot Si Ujang --}}
    <section class="relative bg-[#0B1528] text-white py-12 lg:py-16 px-4 sm:px-6 overflow-hidden">
        {{-- Watermark Ornamen Logo Obor Berwarna (Sesuai Versi Warna Asli) --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden select-none" aria-hidden="true">
            <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute -top-10 left-6 w-28 opacity-[0.14] -rotate-12" style="opacity: 0.14;" alt="">
            <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute top-24 left-44 w-24 opacity-10 rotate-6" style="opacity: 0.10;" alt="">
            <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute -top-12 left-1/3 w-32 opacity-[0.14] rotate-3" style="opacity: 0.14;" alt="">
            <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute top-36 left-1/2 w-28 opacity-[0.12] -rotate-6" style="opacity: 0.12;" alt="">
            <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute -top-8 right-28 w-36 opacity-[0.14] rotate-12" style="opacity: 0.14;" alt="">
            <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute top-44 right-8 w-32 opacity-20 -rotate-6" style="opacity: 0.20;" alt="">
            <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute -bottom-10 left-12 w-36 opacity-[0.12] -rotate-6" style="opacity: 0.12;" alt="">
            <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute -bottom-12 right-1/4 w-32 opacity-[0.14] rotate-12" style="opacity: 0.14;" alt="">
        </div>

        <div class="relative z-10 max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
            {{-- Kolom Kiri: Judul, Deskripsi & Formulir Lacak Tiket --}}
            <div class="lg:col-span-7 text-left">
                {{-- Badge Institusi --}}
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-500/10 border border-amber-400/30 text-amber-400 text-xs font-bold tracking-wide uppercase mb-4 shadow-sm">
                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2c1.1 0 2 .9 2 2 0 .74-.4 1.38-1 1.72v1.44c1.93.42 3.5 1.76 4.3 3.55l1.62-.93c.27-.16.61-.13.85.08.24.21.31.55.17.84l-1.29 2.58c1.33 1.49 2.15 3.42 2.15 5.56 0 4.54-3.66 8.16-8.2 8.16S4.4 22.84 4.4 18.3c0-2.14.82-4.07 2.15-5.56L5.26 10.16c-.14-.29-.07-.63.17-.84.24-.21.58-.24.85-.08l1.62.93c.8-1.79 2.37-3.13 4.3-3.55V5.72C11.4 5.38 11 4.74 11 4c0-1.1.9-2 2-2z"/>
                    </svg>
                    <span>PORTAL RESMI BIRO KEMAHASISWAAN (BKHM)</span>
                </div>

                {{-- Judul Utama --}}
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight mb-4">
                    Layanan Terpadu &amp; Kanal Aspirasi Mahasiswa ITG
                </h1>

                {{-- Subtitle --}}
                <p class="text-slate-300 text-sm sm:text-base leading-relaxed mb-8 max-w-xl">
                    Sampaikan aspirasi kampus, jadwalkan konseling personal BKHM rahasia, atau daftarkan prestasi juara dan permohonan dana delegasi lomba. Mudah, cepat, dan tanpa perlu login akun.
                </p>

                {{-- Formulir Melayang Lacak Tiket --}}
                <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-2xl text-slate-900 border border-slate-100" x-data="{ submitting: false }">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                            </svg>
                        </div>
                        <h2 class="text-xs sm:text-sm font-bold text-slate-900">
                            Sudah punya Kode Tiket? Lacak Perkembangannya di Sini
                        </h2>
                    </div>

                    <form action="{{ route('layanan.cek-status') }}" method="GET" class="flex flex-col sm:flex-row gap-2.5 items-stretch" @submit="submitting = true">
                        <div class="flex-1 min-w-0">
                            <label for="kode" class="sr-only">Kode Tiket</label>
                            <input id="kode" type="text" name="kode" required autocomplete="off" placeholder="SKIN-TKT-2026-XXXX"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-blue-600 text-xs sm:text-sm font-mono uppercase text-slate-900 placeholder-slate-400">
                        </div>
                        <div class="flex-1 min-w-0">
                            <label for="email" class="sr-only">Email</label>
                            <input id="email" type="email" name="email" required autocomplete="email" placeholder="email@mahasiswa.itg.ac.id"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-blue-600 text-xs sm:text-sm text-slate-900 placeholder-slate-400">
                        </div>
                        <button type="submit" :disabled="submitting"
                                class="inline-flex items-center justify-center gap-2 min-h-[42px] px-5 rounded-xl bg-[#1E3A8A] hover:bg-blue-800 disabled:opacity-70 text-white font-bold text-xs sm:text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 shadow-md shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span x-text="submitting ? 'Memproses...' : 'Cari Tiket'">Cari Tiket</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Kolom Kanan: Panggung Maskot Si Ujang --}}
            <div class="lg:col-span-5 flex flex-col items-center justify-center relative">
                {{-- Balon Sapaan (Speech Bubble) --}}
                <div class="bg-white text-slate-900 text-xs sm:text-sm font-bold px-4 py-2.5 rounded-2xl shadow-2xl border border-slate-100 flex items-center gap-2 mb-3 relative">
                    <span>👋 Sampurasun! Ada yang bisa dibantu?</span>
                    {{-- Segitiga Balon Bawah --}}
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-t-[8px] border-t-white"></div>
                </div>

                {{-- Karakter Maskot Si Ujang Full Body --}}
                <div class="relative flex items-center justify-center">
                    <img src="{{ asset('images/maskot-itg-full.png') }}"
                         alt="Maskot Si Ujang - Layanan Mahasiswa Institut Teknologi Garut"
                         class="h-64 sm:h-72 lg:h-84 object-contain drop-shadow-2xl">
                </div>

                {{-- Label Tag Maskot --}}
                <div class="mt-3 inline-flex items-center px-3.5 py-1 rounded-full bg-blue-950/80 border border-blue-400/30 text-blue-200 text-[11px] font-semibold tracking-wide shadow-md">
                    <span>Si Ujang · Maskot Layanan Mahasiswa</span>
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION: Apa Itu SKIN ITG? (Pengenalan Ekosistem & Showcase Card Logo SKIN ala AISnet) --}}
    <section class="border-y border-slate-200 bg-slate-50/70 py-12 lg:py-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                {{-- Kolom Kiri: Penjelasan & Alur --}}
                <div class="lg:col-span-7">
                    <span class="inline-block text-xs font-extrabold uppercase tracking-widest text-[#1E40AF] mb-2">
                        APA ITU SKIN ITG?
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight mb-4">
                        Satu pusat ekosistem untuk ritme kemahasiswaan yang terus bergerak.
                    </h2>
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed mb-6">
                        SKIN (Sistem Kemahasiswaan Institut Teknologi Garut) adalah platform digital terpadu yang memadukan layanan mahasiswa umum dengan alur kerja resmi organisasi mahasiswa. Menjembatani koordinasi transparan antara Organisasi Mahasiswa (ORMAWA), Biro Kemahasiswaan &amp; Hubungan Masyarakat (BKHM), serta Wakil Rektor III (WR3).
                    </p>

                    <div class="flex flex-wrap gap-2.5">
                        <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-[#1E40AF]"></span>
                            <span>ORMAWA · Pengaju Aspirasi &amp; Kegiatan</span>
                        </div>
                        <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-[#0284C7]"></span>
                            <span>BKHM · Pelayanan &amp; Verifikasi</span>
                        </div>
                        <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                            <span>WR3 · Otoritas &amp; Pengesahan</span>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Card Showcase Logo SKIN ala AISnet --}}
                <div class="lg:col-span-5 flex justify-center">
                    <div class="w-full max-w-sm bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-xl shadow-slate-200/60 flex flex-col items-center text-center">
                        <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-2xl bg-slate-50 border border-slate-200/80 p-3 flex items-center justify-center shadow-inner mb-4">
                            <img src="{{ asset('images/logo-skin.png') }}" class="w-full h-full object-contain" alt="Logo SKIN ITG">
                        </div>
                        <span class="text-[11px] font-extrabold tracking-wider text-[#1E40AF] uppercase mb-1">
                            SISTEM INFORMASI KEMAHASISWAAN
                        </span>
                        <h3 class="text-base sm:text-lg font-extrabold text-slate-900 mb-3">
                            Institut Teknologi Garut
                        </h3>
                        <span class="inline-flex items-center px-3.5 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-800 text-[10px] font-bold tracking-wide">
                            ALUR: BKKH · ORMAWA · WR3
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION: Kategori Layanan Publik --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-14">
        <div class="text-center mb-10">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Pilih Kategori Layanan Publik</h2>
            <p class="text-slate-600 mt-2 text-sm max-w-xl mx-auto">
                Tidak memerlukan akun. Cukup isi formulir singkat untuk menerima Kode Tiket pelacakan unik.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Kategori 1: Aspirasi --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:border-blue-300 hover:shadow-xl transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-5">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5 6 9H3v6h3l5 4V5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.5 8.5a5 5 0 0 1 0 7"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.4 5.6a9 9 0 0 1 0 12.8"/>
                            </svg>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100 whitespace-nowrap">
                            Dapat Anonim
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Kanal Aspirasi Mahasiswa</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Sampaikan kritik, saran fasilitas, dan gagasan untuk kemajuan kampus ITG.
                    </p>
                    <ul class="text-xs text-slate-600 space-y-2 mb-6">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Dapat dikirim secara anonim</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Diteruskan langsung ke BPM &amp; BKHM</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Pantau status tindak lanjut via tiket</span>
                        </li>
                    </ul>
                </div>
                <a href="{{ route('layanan.aspirasi.create') }}"
                   class="inline-flex items-center justify-center gap-2 min-h-[44px] px-5 rounded-xl font-bold text-sm bg-[#1E3A8A] hover:bg-blue-800 text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 shadow-md">
                    <span>Buat Tiket Aspirasi</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
            </div>

            {{-- Kategori 2: Konseling --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:border-emerald-300 hover:shadow-xl transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-5">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="5" y="10.5" width="14" height="9.5" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/>
                            </svg>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 whitespace-nowrap">
                            Tatap Muka / Daring
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Konseling Personal BKHM</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Konsultasikan kendala akademik, psikologis, atau pribadi secara rahasia bersama konselor.
                    </p>
                    <ul class="text-xs text-slate-600 space-y-2 mb-6">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Data terenkripsi AES-256 (Rahasia)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Pilih sesi tatap muka atau daring</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Konfirmasi kehadiran terjadwal</span>
                        </li>
                    </ul>
                </div>
                <a href="{{ route('layanan.konseling.create') }}"
                   class="inline-flex items-center justify-center gap-2 min-h-[44px] px-5 rounded-xl font-bold text-sm bg-[#059669] hover:bg-emerald-700 text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 shadow-md">
                    <span>Jadwalkan Konseling</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
            </div>

            {{-- Kategori 3: Prestasi & Delegasi --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:border-amber-300 hover:shadow-xl transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-5">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h10v5a5 5 0 0 1-10 0V4z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 5H4.5A2.5 2.5 0 0 0 7 10"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 5h2.5A2.5 2.5 0 0 1 17 10"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v3M9 20h6"/>
                            </svg>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-100 whitespace-nowrap">
                            Rekognisi Dikti
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Prestasi &amp; Dana Lomba</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Laporkan raihan juara kompetisi atau ajukan permohonan bantuan dana delegasi lomba.
                    </p>
                    <ul class="text-xs text-slate-600 space-y-2 mb-6">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Dicatat pada Pangkalan Data Dikti</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Showcase galeri prestasi publik</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Permohonan delegasi resmi ormawa</span>
                        </li>
                    </ul>
                </div>
                <a href="{{ route('layanan.prestasi.create') }}"
                   class="inline-flex items-center justify-center gap-2 min-h-[44px] px-5 rounded-xl font-bold text-sm bg-[#D97706] hover:bg-amber-700 text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 shadow-md">
                    <span>Ajukan Sekarang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- CALLOUT BANNER: Avatar Kepala Si Ujang & Bantuan Cepat --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 mb-12">
        <div class="bg-blue-50/80 border border-blue-200/90 rounded-2xl p-5 sm:p-6 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-4 text-left">
                <div class="relative shrink-0">
                    <img src="{{ asset('images/maskot-itg-head.png') }}"
                         alt="Si Ujang"
                         class="w-14 h-14 rounded-2xl bg-amber-100 p-1 border border-blue-200 object-contain shadow-sm">
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900">
                        Butuh Bantuan Lebih Lanjut atau Lupa Kode Tiket?
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                        Tim Biro Kemahasiswaan (BKHM) siap melayani pertanyaan seputar perkuliahan dan ormawa pada jam kerja.
                    </p>
                </div>
            </div>
            <a href="mailto:kemahasiswaan@itg.ac.id"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-[#1E3A8A] hover:bg-blue-800 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 shadow-sm shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
                <span>Hubungi Layanan BKHM</span>
            </a>
        </div>
    </section>

    {{-- SECTION: Pertanyaan yang Sering Diajukan (FAQ) --}}
    <section class="max-w-4xl mx-auto px-4 sm:px-6 pb-16">
        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 text-center">Pertanyaan yang Sering Diajukan</h2>
        <p class="text-xs sm:text-sm text-slate-500 text-center mt-1 mb-6">Informasi umum seputar alur dan keamanan layanan kemahasiswaan</p>
        
        <div class="space-y-3">
            <details class="group bg-white border border-slate-200 rounded-xl px-5 py-4 transition-all">
                <summary class="flex items-center justify-between gap-4 cursor-pointer list-none font-semibold text-sm text-slate-900 [&::-webkit-details-marker]:hidden">
                    <span>Berapa lama tiket saya diproses?</span>
                    <svg class="w-4 h-4 shrink-0 text-slate-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <p class="mt-3 text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Tiket ditelaah lebih dulu oleh BPM, lalu diteruskan ke BKHM bila perlu tindak lanjut tingkat institusi. Waktu penyelesaian berbeda untuk tiap tiket, jadi pantau status dan tanggapan resmi kapan saja melalui halaman Lacak Tiket.
                </p>
            </details>

            <details class="group bg-white border border-slate-200 rounded-xl px-5 py-4 transition-all">
                <summary class="flex items-center justify-between gap-4 cursor-pointer list-none font-semibold text-sm text-slate-900 [&::-webkit-details-marker]:hidden">
                    <span>Bagaimana jika saya lupa Kode Tiket?</span>
                    <svg class="w-4 h-4 shrink-0 text-slate-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <p class="mt-3 text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Kode Tiket dikirim ke alamat email yang Anda isi saat mengajukan. Periksa kotak masuk atau folder spam untuk email berisi kode tersebut. Jika tetap tidak ditemukan, ajukan tiket baru atau hubungi BKHM dengan menyebutkan NIM dan tanggal pengajuan.
                </p>
            </details>

            <details class="group bg-white border border-slate-200 rounded-xl px-5 py-4 transition-all">
                <summary class="flex items-center justify-between gap-4 cursor-pointer list-none font-semibold text-sm text-slate-900 [&::-webkit-details-marker]:hidden">
                    <span>Apakah identitas dan data saya aman?</span>
                    <svg class="w-4 h-4 shrink-0 text-slate-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <p class="mt-3 text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Identitas aspirasi hanya diketahui BPM dan BKHM, dan dapat dikirim sebagai anonim. Data konseling bersifat rahasia antara Anda dan konselor BKHM, BPM, BEM, maupun ormawa tidak memiliki akses. Berkas lampiran disimpan di penyimpanan privat dan tidak dapat diakses publik.
                </p>
            </details>
        </div>
    </section>
</x-public-layout>
