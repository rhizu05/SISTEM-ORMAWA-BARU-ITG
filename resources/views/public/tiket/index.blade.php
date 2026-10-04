<x-public-layout title="Portal Layanan Mahasiswa" brand-label="Portal Layanan Mahasiswa" accent="indigo">
    <x-slot name="nav">
        <a href="{{ route('informasi.index') }}"
           class="inline-flex items-center min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:text-blue-700 hover:bg-slate-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
            Informasi Kampus
        </a>
        <a href="{{ route('prestasi.showcase') }}"
           class="inline-flex items-center min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:text-blue-700 hover:bg-slate-100 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
            Prestasi
        </a>
        <a href="#lacak-tiket"
           class="inline-flex items-center gap-1.5 min-h-[44px] px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 shadow-2xs">
            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span>Lacak Tiket</span>
        </a>
    </x-slot>

    {{-- HERO SECTION: Deep Academic Navy dengan Sapaan Ramah Si Ujang --}}
    {{-- HERO SECTION: Deep Academic Navy dengan Sapaan Ramah Si Ujang --}}
    <section class="relative bg-[#0B1528] text-white pt-8 pb-12 sm:pt-12 sm:pb-16 px-4 sm:px-6 overflow-hidden">
        {{-- Watermark Ornamen Logo Obor Berwarna --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden select-none" aria-hidden="true">
            <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute -top-10 left-6 w-24 sm:w-28 opacity-[0.12] -rotate-12" alt="">
            <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute top-20 right-8 w-24 sm:w-32 opacity-[0.14] rotate-12" alt="">
            <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute -bottom-8 left-1/3 w-28 opacity-[0.10] -rotate-6" alt="">
        </div>

        <div class="relative z-10 max-w-6xl mx-auto">
            {{-- Sapaan Hangat Si Ujang sebagai Pendamping Ramah (Khusus Layar Mobile < lg) --}}
            <div class="block lg:hidden">
                <div class="inline-flex items-center gap-2.5 p-1.5 pr-4 rounded-full bg-[#12203A] border border-[#263B66] shadow-lg mb-4 max-w-full">
                    <div class="relative w-8 h-8 rounded-full bg-[#1E3A8A] border-2 border-amber-400 p-0.5 flex items-center justify-center shrink-0 shadow-sm">
                        <img src="{{ asset('images/maskot-itg-head.png') }}" class="w-full h-full object-contain rounded-full" alt="Si Ujang">
                        <span class="absolute -top-0.5 -right-0.5 flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-400 border border-[#0B1528]"></span>
                        </span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-amber-400 font-extrabold text-[11px] tracking-wider uppercase block leading-none">
                            Si Ujang &bull; Asisten Kampus
                        </span>
                        <span class="text-slate-200 text-xs font-semibold truncate block mt-0.5">
                            Sampurasun! Mau lapor atau butuh layanan apa hari ini?
                        </span>
                    </div>
                </div>
            </div>

            {{-- Grid Konten Hero: Split 2 Kolom di Desktop (lg) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                {{-- Kolom Kiri: Judul, Subtitle & Nilai Tambah --}}
                <div class="lg:col-span-7 text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-400/30 text-blue-300 text-[10px] sm:text-xs font-bold tracking-wide uppercase mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                        <span>Portal Resmi Biro Kemahasiswaan ITG</span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight mb-3">
                        Layanan Terpadu &amp; Kanal Aspirasi Mahasiswa
                    </h1>

                    <p class="text-slate-300 text-xs sm:text-base leading-relaxed mb-6 max-w-xl">
                        Sampaikan aspirasi kampus, jadwalkan konseling personal rahasia BKHM, atau ajukan prestasi juara dan dana delegasi lomba. Praktis, transparan, dan tanpa perlu login akun.
                    </p>

                    {{-- Fitur Kepercayaan / Nilai Tambah Cepat --}}
                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-300">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-[11px] font-semibold">
                            <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Tanpa Perlu Login</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-[11px] font-semibold">
                            <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Aspirasi Dapat Anonim</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 border border-white/10 text-[11px] font-semibold">
                            <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Kerahasiaan Terjamin</span>
                        </span>
                    </div>
                </div>

                {{-- Kolom Kanan: Panggung Maskot Si Ujang Besar (Khusus Desktop lg) --}}
                <div class="hidden lg:flex lg:col-span-5 flex-col items-center justify-center relative">
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
                             class="h-64 sm:h-72 lg:h-80 object-contain drop-shadow-2xl">
                    </div>

                    {{-- Label Tag Maskot --}}
                    <div class="mt-3 inline-flex items-center px-3.5 py-1 rounded-full bg-blue-950/80 border border-blue-400/30 text-blue-200 text-[11px] font-semibold tracking-wide shadow-md">
                        <span>Si Ujang &bull; Maskot Layanan Mahasiswa ITG</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION: 3 Kategori Layanan Utama (Prioritas Utama, Langsung Terlihat Mahasiswa) --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 -mt-6 sm:-mt-8 relative z-20">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            {{-- Kategori 1: Aspirasi Mahasiswa --}}
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 shadow-lg shadow-slate-200/50 hover:border-blue-400 hover:shadow-xl transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-3.5">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center shadow-xs shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5 6 9H3v6h3l5 4V5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.5 8.5a5 5 0 0 1 0 7"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.4 5.6a9 9 0 0 1 0 12.8"/>
                            </svg>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 whitespace-nowrap">
                            Dapat Anonim
                        </span>
                    </div>

                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5">
                        Kanal Aspirasi Mahasiswa
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4">
                        Sampaikan kritik, saran fasilitas, dan gagasan konstruktif untuk kemajuan kampus ITG. Diteruskan langsung ke BPM dan BKHM.
                    </p>

                    {{-- Poin Rinci (Tampil di Layar Lebih Lebar, Ramping di HP) --}}
                    <ul class="hidden sm:block text-xs text-slate-600 space-y-1.5 mb-5 pt-2 border-t border-slate-100">
                        <li class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Dapat dikirim secara anonim tanpa identitas</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Diteruskan resmi ke BPM &amp; BKHM</span>
                        </li>
                    </ul>
                </div>

                <a href="{{ route('layanan.aspirasi.create') }}"
                   class="inline-flex items-center justify-center gap-2 min-h-[44px] w-full px-4 rounded-xl font-bold text-xs sm:text-sm bg-[#1E3A8A] hover:bg-blue-800 text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 shadow-sm">
                    <span>Tulis Aspirasi Sekarang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
            </div>

            {{-- Kategori 2: Konseling Personal BKHM --}}
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 shadow-lg shadow-slate-200/50 hover:border-emerald-400 hover:shadow-xl transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-3.5">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shadow-xs shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <rect x="5" y="10.5" width="14" height="9.5" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/>
                            </svg>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 whitespace-nowrap">
                            Privasi Terenkripsi
                        </span>
                    </div>

                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5">
                        Konseling Personal BKHM
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4">
                        Konsultasikan kendala akademik, psikologis, atau pribadi secara aman dan rahasia bersama konselor profesional kampus.
                    </p>

                    {{-- Poin Rinci (Tampil di Layar Lebih Lebar, Ramping di HP) --}}
                    <ul class="hidden sm:block text-xs text-slate-600 space-y-1.5 mb-5 pt-2 border-t border-slate-100">
                        <li class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Data terenkripsi aman (Kerahasiaan terjaga)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Pilihan sesi tatap muka atau konsultasi daring</span>
                        </li>
                    </ul>
                </div>

                <a href="{{ route('layanan.konseling.create') }}"
                   class="inline-flex items-center justify-center gap-2 min-h-[44px] w-full px-4 rounded-xl font-bold text-xs sm:text-sm bg-[#059669] hover:bg-emerald-700 text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 shadow-sm">
                    <span>Jadwalkan Konseling</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
            </div>

            {{-- Kategori 3: Prestasi & Delegasi --}}
            <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 shadow-lg shadow-slate-200/50 hover:border-amber-400 hover:shadow-xl transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-3.5">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center shadow-xs shrink-0">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h10v5a5 5 0 0 1-10 0V4z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 5H4.5A2.5 2.5 0 0 0 7 10"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 5h2.5A2.5 2.5 0 0 1 17 10"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14v3M9 20h6"/>
                            </svg>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 whitespace-nowrap">
                            Rekognisi Dikti
                        </span>
                    </div>

                    <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5">
                        Prestasi &amp; Dana Lomba
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mb-4">
                        Laporkan raihan juara kompetisi untuk rekognisi Dikti atau ajukan permohonan bantuan dana delegasi lomba resmi.
                    </p>

                    {{-- Poin Rinci (Tampil di Layar Lebih Lebar, Ramping di HP) --}}
                    <ul class="hidden sm:block text-xs text-slate-600 space-y-1.5 mb-5 pt-2 border-t border-slate-100">
                        <li class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Pencatatan resmi di Pangkalan Data Dikti</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Dukungan permohonan delegasi resmi ormawa</span>
                        </li>
                    </ul>
                </div>

                <a href="{{ route('layanan.prestasi.create') }}"
                   class="inline-flex items-center justify-center gap-2 min-h-[44px] w-full px-4 rounded-xl font-bold text-xs sm:text-sm bg-[#D97706] hover:bg-amber-700 text-white transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 shadow-sm">
                    <span>Ajukan Prestasi Sekarang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- SECTION: Lacak Status Tiket (Wadah Khusus, Ergonomis, dan Tidak Membebani Pengunjung Baru) --}}
    <section id="lacak-tiket" class="max-w-4xl mx-auto px-4 sm:px-6 my-10 sm:my-14 scroll-mt-20">
        <div class="bg-white rounded-2xl p-5 sm:p-7 border border-slate-200/90 shadow-sm" x-data="{ submitting: false }">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 leading-tight">
                            Sudah Memiliki Kode Tiket Layanan?
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Pantau tindak lanjut aspirasi, jadwal konseling, atau status delegasi lomba Anda
                        </p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] sm:text-[11px] font-semibold w-fit">
                    Pelacakan Realtime
                </span>
            </div>

            <form action="{{ route('layanan.cek-status') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-2.5" @submit="submitting = true">
                <div class="sm:col-span-5">
                    <label for="kode" class="block text-xs font-semibold text-slate-700 mb-1">Kode Tiket</label>
                    <input id="kode" type="text" name="kode" required autocomplete="off" placeholder="SKIN-TKT-2026-XXXX"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-blue-600 text-xs sm:text-sm font-mono uppercase text-slate-900 placeholder-slate-400">
                </div>
                <div class="sm:col-span-5">
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email Mahasiswa</label>
                    <input id="email" type="email" name="email" required autocomplete="email" placeholder="email@mahasiswa.itg.ac.id"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-blue-600 text-xs sm:text-sm text-slate-900 placeholder-slate-400">
                </div>
                <div class="sm:col-span-2 flex items-end">
                    <button type="submit" :disabled="submitting"
                            class="inline-flex items-center justify-center gap-1.5 min-h-[42px] w-full px-4 rounded-xl bg-[#1E3A8A] hover:bg-blue-800 disabled:opacity-70 text-white font-bold text-xs sm:text-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 shadow-sm shrink-0">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span x-text="submitting ? 'Mencari...' : 'Cari'">Cari</span>
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- SECTION: Apa Itu SKIN ITG? (Pengenalan Ekosistem Kampus) --}}
    <section class="border-y border-slate-200 bg-slate-50/70 py-10 sm:py-14">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12 items-center">
                {{-- Kolom Penjelasan & Alur --}}
                <div class="lg:col-span-7">
                    <span class="inline-block text-xs font-extrabold uppercase tracking-widest text-[#1E40AF] mb-1.5">
                        EKOSISTEM KEMAHASISWAAN ITG
                    </span>
                    <h2 class="text-xl sm:text-3xl font-extrabold text-slate-900 leading-tight mb-3">
                        Satu platform terintegrasi untuk seluruh kegiatan ormawa dan layanan mahasiswa.
                    </h2>
                    <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-5">
                        SKIN (Sistem Kemahasiswaan Institut Teknologi Garut) menyatukan pelayanan publik mahasiswa umum dengan alur kerja verifikasi kegiatan organisasi mahasiswa. Menjamin koordinasi yang transparan, akuntabel, dan bebas hambatan birokrasi manual.
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-800 shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-[#1E40AF]"></span>
                            <span>ORMAWA: Pengaju Aspirasi &amp; Kegiatan</span>
                        </div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-800 shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-[#0284C7]"></span>
                            <span>BKHM: Pelayanan &amp; Verifikasi</span>
                        </div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-800 shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                            <span>WR3: Otoritas &amp; Pengesahan</span>
                        </div>
                    </div>
                </div>

                {{-- Kolom Badge Identitas Institusi --}}
                <div class="lg:col-span-5 flex justify-center">
                    <div class="w-full max-w-sm bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/90 shadow-sm flex flex-col items-center text-center">
                        <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-slate-50 border border-slate-200/80 p-2.5 flex items-center justify-center shadow-inner mb-3">
                            <img src="{{ asset('images/logo-skin.png') }}" class="w-full h-full object-contain" alt="Logo SKIN ITG">
                        </div>
                        <span class="text-[10px] font-extrabold tracking-wider text-[#1E40AF] uppercase mb-0.5">
                            SISTEM INFORMASI KEMAHASISWAAN
                        </span>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-900 mb-2">
                            Institut Teknologi Garut
                        </h3>
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full bg-blue-50 border border-blue-200 text-blue-800 text-[10px] font-bold tracking-wide">
                            T.A. 2026/2027
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CALLOUT BANNER: Avatar Kepala Si Ujang & Bantuan Cepat --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 my-10 sm:my-12">
        <div class="bg-blue-50/80 border border-blue-200/90 rounded-2xl p-4 sm:p-6 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-2xs">
            <div class="flex items-center gap-3.5 text-left w-full sm:w-auto">
                <div class="relative shrink-0">
                    <img src="{{ asset('images/maskot-itg-head.png') }}"
                         alt="Si Ujang"
                         class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-amber-100 p-1 border border-blue-200 object-contain shadow-2xs">
                </div>
                <div>
                    <h3 class="text-xs sm:text-base font-bold text-slate-900">
                        Butuh Bantuan Lebih Lanjut atau Lupa Kode Tiket?
                    </h3>
                    <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                        Tim Biro Kemahasiswaan (BKHM) siap melayani pertanyaan seputar perkuliahan dan ormawa pada jam kerja.
                    </p>
                </div>
            </div>
            <a href="https://wa.me/6285353791190?text=Halo%20Admin%20Layanan%20BKHM%20ITG%2C%20saya%20ingin%20bertanya%20seputar%20layanan%20kemahasiswaan"
               target="_blank"
               rel="noopener noreferrer"
               class="inline-flex items-center justify-center gap-2 min-h-[44px] w-full sm:w-auto px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-[#075E54] hover:bg-[#128C7E] transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 shadow-sm shrink-0">
                <svg class="w-4 h-4 shrink-0 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.196 8.196 0 01-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24m4.52 11.64c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.03-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.37-.44.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.78 2.71 4.3 3.8.6.26 1.07.42 1.44.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.1-.23-.17-.48-.29z"/>
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
            <details class="group bg-white border border-slate-200 rounded-xl px-4 sm:px-5 py-3.5 sm:py-4 transition-all">
                <summary class="flex items-center justify-between gap-4 cursor-pointer list-none font-semibold text-xs sm:text-sm text-slate-900 [&::-webkit-details-marker]:hidden min-h-[36px]">
                    <span>Berapa lama tiket saya diproses?</span>
                    <svg class="w-4 h-4 shrink-0 text-slate-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <p class="mt-3 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Tiket ditelaah terlebih dahulu oleh BPM, lalu diteruskan ke BKHM bila perlu tindak lanjut tingkat institusi. Waktu penyelesaian bervariasi bergantung pada jenis permohonan. Anda dapat memantau status secara realtime melalui kolom Lacak Tiket di atas.
                </p>
            </details>

            <details class="group bg-white border border-slate-200 rounded-xl px-4 sm:px-5 py-3.5 sm:py-4 transition-all">
                <summary class="flex items-center justify-between gap-4 cursor-pointer list-none font-semibold text-xs sm:text-sm text-slate-900 [&::-webkit-details-marker]:hidden min-h-[36px]">
                    <span>Bagaimana jika saya lupa Kode Tiket?</span>
                    <svg class="w-4 h-4 shrink-0 text-slate-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <p class="mt-3 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Kode Tiket dikirim secara otomatis ke alamat email yang Anda isi saat mengajukan tiket. Silakan periksa kotak masuk atau folder spam Anda. Jika tetap tidak ditemukan, Anda dapat <a href="https://wa.me/6285353791190?text=Halo%20Admin%20BKHM%20ITG%2C%20saya%20lupa%20kode%20tiket%20layanan%20saya" target="_blank" rel="noopener noreferrer" class="text-emerald-700 font-semibold hover:underline">menghubungi WhatsApp Layanan BKHM (+62 853-5379-1190)</a> dengan menyebutkan NIM dan tanggal pengajuan.
                </p>
            </details>

            <details class="group bg-white border border-slate-200 rounded-xl px-4 sm:px-5 py-3.5 sm:py-4 transition-all">
                <summary class="flex items-center justify-between gap-4 cursor-pointer list-none font-semibold text-xs sm:text-sm text-slate-900 [&::-webkit-details-marker]:hidden min-h-[36px]">
                    <span>Apakah identitas dan data saya aman?</span>
                    <svg class="w-4 h-4 shrink-0 text-slate-500 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </summary>
                <p class="mt-3 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Sistem melindungi privasi mahasiswa secara ketat. Aspirasi dapat dikirimkan secara anonim. Khusus layanan konseling, data Anda terenkripsi aman dan hanya dapat diakses oleh konselor resmi BKHM ITG (tidak dapat dilihat oleh pengurus ORMAWA, BEM, maupun BPM).
                </p>
            </details>
        </div>
    </section>
</x-public-layout>
