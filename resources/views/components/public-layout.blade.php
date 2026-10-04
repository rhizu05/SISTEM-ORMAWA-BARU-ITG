@props([
    'title' => 'Portal Layanan Mahasiswa',
    'brandLabel' => 'Portal Layanan Mahasiswa',
    'accent' => 'indigo',
])

@php
    $accentText = [
        'indigo' => 'text-indigo-700',
        'emerald' => 'text-emerald-700',
        'amber' => 'text-amber-700',
    ][$accent] ?? 'text-indigo-700';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - Institut Teknologi Garut</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-skin-torch.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-skin-torch.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0B1528">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans min-h-screen flex flex-col"
      x-data="{ mobileMenuOpen: false }"
      @keydown.escape.window="mobileMenuOpen = false"
      x-init="$watch('mobileMenuOpen', value => document.body.classList.toggle('overflow-hidden', value))">
    <a href="#konten-utama" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-2 focus:left-2 focus:bg-white focus:text-indigo-700 focus:px-4 focus:py-2 focus:rounded focus:shadow">
        Lewati ke konten utama
    </a>

    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/90 transition-all">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-2.5 sm:py-3 flex items-center justify-between gap-x-4">
            <a href="{{ route('layanan.index') }}" class="flex items-center gap-2 sm:gap-3 min-w-0 min-h-[44px]">
                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <img src="{{ asset('images/logo_itg.png') }}" alt="Logo Institut Teknologi Garut" class="h-8 sm:h-11 w-auto object-contain">
                    <div class="hidden sm:block h-6 sm:h-7 w-[1px] bg-slate-200"></div>
                    <img src="{{ asset('images/logo-skin-torch.png') }}" alt="Logo Obor Kemahasiswaan ITG" class="hidden sm:block h-7 sm:h-10 w-auto object-contain">
                </div>
                <span class="min-w-0">
                    <span class="hidden sm:block text-[11px] font-bold tracking-wider text-blue-900 uppercase">Institut Teknologi Garut</span>
                    <span class="hidden sm:block text-xs sm:text-base font-extrabold text-slate-900 leading-tight truncate">{{ $brandLabel }}</span>
                    <span class="block sm:hidden text-xs font-extrabold text-slate-900 leading-tight">Layanan Mahasiswa</span>
                </span>
            </a>

            <div class="flex items-center gap-1.5 sm:gap-2">
                {{-- Navigasi Desktop (> lg) --}}
                <div class="hidden lg:flex items-center gap-1.5 sm:gap-2">
                    {{ $nav ?? '' }}
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 min-h-[44px] px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold text-white bg-[#1E3A8A] hover:bg-blue-800 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-700 focus-visible:ring-offset-2 ml-1 shadow-sm">
                            <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    @endauth
                </div>

                {{-- Sisi Kanan Mobile (< lg): Tepat 2 Kontrol Bersih & Lega --}}
                <div class="flex lg:hidden items-center gap-1.5">
                    {{-- 1. Tombol Lacak Tiket --}}
                    <a href="{{ request()->routeIs('layanan.index') ? '#lacak-tiket' : route('layanan.cek-status') }}"
                       class="inline-flex items-center gap-1 min-h-[44px] px-2.5 sm:px-3 py-1.5 rounded-xl text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 shadow-2xs"
                       aria-label="Lacak Status Tiket Layanan">
                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Lacak</span>
                    </a>

                    {{-- 2. Tombol Hamburger Icon-Only --}}
                    <button type="button" @click="mobileMenuOpen = true"
                            class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] rounded-xl text-slate-700 hover:text-indigo-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                            aria-label="Buka Menu Layanan Mahasiswa"
                            :aria-expanded="mobileMenuOpen.toString()">
                        <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    {{-- Slide-over Drawer Menu Navigasi Mobile (< lg) --}}
    <div x-show="mobileMenuOpen"
         style="display: none;"
         class="fixed inset-0 z-50 overflow-hidden lg:hidden"
         role="dialog"
         aria-modal="true"
         aria-label="Menu Layanan Mahasiswa ITG">

        {{-- Backdrop Gelap --}}
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
             @click="mobileMenuOpen = false"
             aria-hidden="true"></div>

        {{-- Panel Samping Slide-over --}}
        <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
            <div x-show="mobileMenuOpen"
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="w-screen max-w-xs sm:max-w-sm bg-white shadow-2xl flex flex-col justify-between overflow-y-auto">

                {{-- Header Drawer --}}
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                    <div class="flex items-center gap-2.5">
                        <img src="{{ asset('images/logo_itg.png') }}" alt="Logo ITG" class="h-7 w-auto object-contain">
                        <div>
                            <span class="block text-xs font-black text-slate-900 leading-tight">Institut Teknologi Garut</span>
                            <span class="block text-[11px] text-slate-600 font-medium leading-tight">Layanan Mahasiswa</span>
                        </div>
                    </div>
                    <button type="button" @click="mobileMenuOpen = false"
                            class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-200/60 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                            aria-label="Tutup Menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body Drawer: Daftar Menu Terstruktur --}}
                <div class="p-4 sm:p-5 space-y-5 flex-1">
                    {{-- Navigasi Beranda Utama --}}
                    <div>
                        <a href="{{ route('layanan.index') }}" @click="mobileMenuOpen = false"
                           class="flex items-center gap-3 p-2.5 sm:p-3 rounded-xl hover:bg-slate-100 text-slate-800 transition group border border-slate-200/90 bg-slate-50/70 min-h-[44px]">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 group-hover:bg-[#1E3A8A] group-hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-bold block text-slate-900 group-hover:text-blue-900">Beranda Portal Layanan</span>
                                <span class="text-[10px] sm:text-[11px] text-slate-500 block truncate">Kembali ke halaman muka portal</span>
                            </div>
                        </a>
                    </div>

                    {{-- Grup 1: Informasi & Prestasi Kampus --}}
                    <div>
                        <span class="text-[10px] font-bold tracking-wider text-slate-600 uppercase block mb-1.5 px-1">
                            Informasi &amp; Prestasi
                        </span>
                        <div class="space-y-1">
                            <a href="{{ route('informasi.index') }}" @click="mobileMenuOpen = false"
                               class="flex items-center gap-3 p-2.5 sm:p-3 rounded-xl hover:bg-indigo-50/70 text-slate-800 hover:text-indigo-900 transition group border border-transparent hover:border-indigo-100 min-h-[44px]">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold block text-slate-900 group-hover:text-indigo-900">Informasi &amp; Berita Kampus</span>
                                    <span class="text-[10px] sm:text-[11px] text-slate-600 block truncate">Pengumuman &amp; agenda ormawa</span>
                                </div>
                            </a>

                            <a href="{{ route('prestasi.showcase') }}" @click="mobileMenuOpen = false"
                               class="flex items-center gap-3 p-2.5 sm:p-3 rounded-xl hover:bg-amber-50/70 text-slate-800 hover:text-amber-900 transition group border border-transparent hover:border-amber-100 min-h-[44px]">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 group-hover:bg-amber-600 group-hover:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 4h10v5a5 5 0 0 1-10 0V4z"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 5H4.5A2.5 2.5 0 0 0 7 10"/><path stroke-linecap="round" stroke-linejoin="round" d="M17 5h2.5A2.5 2.5 0 0 1 17 10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14v3M9 20h6"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold block text-slate-900 group-hover:text-amber-900">Showcase Galeri Prestasi</span>
                                    <span class="text-[10px] sm:text-[11px] text-slate-600 block truncate">Dokumentasi juara mahasiswa ITG</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    {{-- Grup 2: Layanan Mandiri Mahasiswa --}}
                    <div>
                        <span class="text-[10px] font-bold tracking-wider text-slate-600 uppercase block mb-1.5 px-1">
                            Layanan Mahasiswa
                        </span>
                        <div class="space-y-1">
                            <a href="{{ route('layanan.aspirasi.create') }}" @click="mobileMenuOpen = false"
                               class="flex items-center gap-3 p-2.5 sm:p-3 rounded-xl hover:bg-blue-50/70 text-slate-800 hover:text-blue-900 transition group border border-transparent hover:border-blue-100 min-h-[44px]">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 group-hover:bg-[#1E3A8A] group-hover:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5 6 9H3v6h3l5 4V5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15.5 8.5a5 5 0 0 1 0 7"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold block text-slate-900 group-hover:text-blue-900">Kanal Aspirasi Mahasiswa</span>
                                    <span class="text-[10px] sm:text-[11px] text-slate-600 block truncate">Sampaikan usulan ke BPM &amp; BKHM</span>
                                </div>
                            </a>

                            <a href="{{ route('layanan.konseling.create') }}" @click="mobileMenuOpen = false"
                               class="flex items-center gap-3 p-2.5 sm:p-3 rounded-xl hover:bg-emerald-50/70 text-slate-800 hover:text-emerald-900 transition group border border-transparent hover:border-emerald-100 min-h-[44px]">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 group-hover:bg-emerald-700 group-hover:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="10.5" width="14" height="9.5" rx="2" stroke-linecap="round" stroke-linejoin="round"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 10.5V7a4 4 0 0 1 8 0v3.5"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold block text-slate-900 group-hover:text-emerald-900">Konseling Personal BKHM</span>
                                    <span class="text-[10px] sm:text-[11px] text-slate-600 block truncate">Konsultasi rahasia bersama konselor</span>
                                </div>
                            </a>

                            <a href="{{ route('layanan.prestasi.create') }}" @click="mobileMenuOpen = false"
                               class="flex items-center gap-3 p-2.5 sm:p-3 rounded-xl hover:bg-amber-50/70 text-slate-800 hover:text-amber-900 transition group border border-transparent hover:border-amber-100 min-h-[44px]">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 group-hover:bg-amber-600 group-hover:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold block text-slate-900 group-hover:text-amber-900">Lapor Prestasi &amp; Dana Lomba</span>
                                    <span class="text-[10px] sm:text-[11px] text-slate-600 block truncate">Pencatatan Dikti atau delegasi lomba</span>
                                </div>
                            </a>

                            <a href="{{ route('layanan.cek-status') }}" @click="mobileMenuOpen = false"
                               class="flex items-center gap-3 p-2.5 sm:p-3 rounded-xl hover:bg-indigo-50/70 text-slate-800 hover:text-indigo-900 transition group border border-transparent hover:border-indigo-100 min-h-[44px]">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold block text-slate-900 group-hover:text-indigo-900">Lacak Status Tiket</span>
                                    <span class="text-[10px] sm:text-[11px] text-slate-600 block truncate">Pantau balasan &amp; jadwal konseling</span>
                                </div>
                            </a>
                        </div>
                    </div>

                    {{-- Grup 3: Validasi & Utilitas --}}
                    <div>
                        <span class="text-[10px] font-bold tracking-wider text-slate-600 uppercase block mb-1.5 px-1">
                            Validasi &amp; Bantuan
                        </span>
                        <div class="space-y-1">
                            <a href="https://wa.me/6285353791190?text=Halo%20Admin%20Layanan%20BKHM%20ITG%2C%20saya%20ingin%20bertanya%20seputar%20layanan%20kemahasiswaan"
                               target="_blank"
                               rel="noopener noreferrer"
                               @click="mobileMenuOpen = false"
                               class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-emerald-50 text-slate-700 hover:text-emerald-950 transition group min-h-[44px]">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 group-hover:bg-[#075E54] group-hover:text-white transition">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 012.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.196 8.196 0 01-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24m4.52 11.64c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.03-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.37-.44.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.35-.77-1.85-.2-.49-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.78 2.71 4.3 3.8.6.26 1.07.42 1.44.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.1-.23-.17-.48-.29z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold block text-slate-900 group-hover:text-emerald-950">WhatsApp Layanan BKHM</span>
                                    <span class="text-[10px] sm:text-[11px] text-slate-600 block truncate">+62 853-5379-1190</span>
                                </div>
                            </a>
                            <a href="{{ route('dokumen.verifikasi.index') }}" @click="mobileMenuOpen = false"
                               class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition group min-h-[44px]">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span class="text-xs font-semibold">Verifikasi Dokumen Resmi</span>
                            </a>
                            <a href="{{ route('bug.create', ['url' => url()->current()]) }}" @click="mobileMenuOpen = false"
                               class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-100 text-slate-700 transition group min-h-[44px]">
                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span class="text-xs font-semibold">Lapor Kendala Web</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Footer Drawer: Tanpa Akses Login (Sesuai Permintaan Pengguna) --}}
                <div class="p-4 border-t border-slate-100 bg-slate-50/80 text-[11px] text-slate-600 text-center">
                    <span>Biro Kemahasiswaan &amp; Hubungan Masyarakat &bull; ITG</span>
                </div>
            </div>
        </div>
    </div>

    <main id="konten-utama" class="flex-grow">
        {{ $slot }}
    </main>

    <footer class="bg-white border-t border-slate-200 mt-auto">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 grid grid-cols-1 sm:grid-cols-3 gap-6 text-xs leading-relaxed text-slate-600">
            <div>
                <span class="text-slate-900 font-bold text-sm block mb-1">Institut Teknologi Garut</span>
                <p>Jalan Mayor Syamsu No. 1 Jayaraga, Garut 44151, Jawa Barat, Indonesia</p>
            </div>
            <div>
                <span class="text-slate-900 font-bold text-sm block mb-1">Biro Kemahasiswaan (BKHM)</span>
                <p>Layanan aspirasi, konseling personal, prestasi, serta fasilitas kegiatan kemahasiswaan ITG.</p>
            </div>
            <div>
                <span class="text-slate-900 font-bold text-sm block mb-1">Kontak</span>
                <p>
                    Website: <a href="https://itg.ac.id" target="_blank" rel="noopener noreferrer" class="text-indigo-700 hover:underline">www.itg.ac.id</a><br>
                    WhatsApp: <a href="https://wa.me/6285353791190" target="_blank" rel="noopener noreferrer" class="text-emerald-700 hover:underline font-semibold">+62 853-5379-1190</a><br>
                    Email: kemahasiswaan@itg.ac.id
                </p>
            </div>
        </div>
        <div class="border-t border-slate-100">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-600">
                <p>&copy; {{ date('Y') }} Institut Teknologi Garut. Biro Kemahasiswaan &amp; Hubungan Masyarakat (BKHM).</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('bug.create', ['url' => url()->current()]) }}" class="inline-flex items-center gap-1.5 min-h-[44px] font-semibold text-rose-700 hover:text-rose-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500 rounded">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Lapor Kendala Web
                    </a>
                    <a href="{{ route('dokumen.verifikasi.index') }}" class="inline-flex items-center gap-1.5 min-h-[44px] font-semibold text-indigo-700 hover:text-indigo-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 rounded">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Verifikasi Keaslian Dokumen
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
