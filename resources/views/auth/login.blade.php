<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Pengurus &amp; Ormawa - SKIN Institut Teknologi Garut</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased font-sans min-h-screen">
    <div class="min-h-screen w-full flex flex-col lg:flex-row">
        
        {{-- ========================================================
             KOLOM KIRI: STAGE BRANDING, WATERMARK & MASKOT SI UJANG
             ======================================================== --}}
        <div class="lg:w-1/2 relative bg-[#0B1528] text-white p-8 sm:p-12 lg:p-16 flex flex-col justify-between overflow-hidden min-h-[520px] lg:min-h-screen">
            
            {{-- Watermark Ornamen Logo Obor Berwarna --}}
            <div class="absolute inset-0 pointer-events-none overflow-hidden select-none" aria-hidden="true">
                <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute -top-10 left-6 w-32 opacity-[0.14] -rotate-12" style="opacity: 0.14;" alt="">
                <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute top-28 right-8 w-28 opacity-[0.12] rotate-12" style="opacity: 0.12;" alt="">
                <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute top-1/2 left-4 w-36 opacity-[0.14] rotate-6" style="opacity: 0.14;" alt="">
                <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute -bottom-10 right-12 w-40 opacity-[0.14] -rotate-12" style="opacity: 0.14;" alt="">
                <img src="{{ asset('images/logo-skin-torch.png') }}" class="absolute -bottom-8 left-16 w-32 opacity-[0.12] rotate-6" style="opacity: 0.12;" alt="">
            </div>

            {{-- Header Brand Identitas Institusi --}}
            <div class="relative z-10 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white p-1 flex items-center justify-center shadow-md shrink-0">
                    <img src="{{ asset('images/logo-skin.png') }}" alt="Logo SKIN ITG" class="w-full h-full object-contain">
                </div>
                <img src="{{ asset('images/logo-skin-torch.png') }}" alt="Logo Obor Kemahasiswaan" class="h-9 w-auto object-contain shrink-0">
                <div class="border-l border-slate-700/80 pl-3">
                    <span class="block text-[11px] font-bold text-amber-400 tracking-wider uppercase">Institut Teknologi Garut</span>
                    <span class="block text-sm font-extrabold text-white leading-tight">SKIN ITG — Sistem Ormawa</span>
                </div>
            </div>

            {{-- Center: Panggung Kolaborasi Showcase Card Logo SKIN & Maskot Si Ujang --}}
            <div class="relative z-10 flex flex-row items-center justify-center gap-4 sm:gap-6 my-auto py-6 text-center">
                {{-- Card Showcase Logo SKIN ala AISnet --}}
                <div class="w-48 sm:w-56 bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/90 shadow-2xl text-center flex flex-col items-center shrink-0">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-slate-50 border border-slate-200/80 p-2.5 flex items-center justify-center shadow-inner mb-2.5">
                        <img src="{{ asset('images/logo-skin.png') }}" class="w-full h-full object-contain" alt="Logo SKIN ITG">
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold tracking-wider text-[#1E40AF] uppercase mb-0.5">
                        SISTEM KEMAHASISWAAN
                    </span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 mb-2">
                        Institut Teknologi Garut
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-blue-50 border border-blue-200 text-blue-800 text-[9px] font-bold tracking-wide">
                        BKKH · ORMAWA · WR3
                    </span>
                </div>

                {{-- Karakter Maskot Si Ujang & Balon Sapaan --}}
                <div class="flex flex-col items-center">
                    {{-- Balon Percakapan Lokal Sunda --}}
                    <div class="bg-white text-slate-900 text-xs font-bold px-3.5 py-1.5 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-1.5 mb-2 relative animate-pulse-slow">
                        <span>👋 Sampurasun!</span>
                        <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-0 h-0 border-l-[5px] border-l-transparent border-r-[5px] border-r-transparent border-t-[6px] border-t-white"></div>
                    </div>

                    {{-- Karakter Maskot Si Ujang --}}
                    <div class="relative flex items-center justify-center">
                        <img src="{{ asset('images/maskot-itg-full.png') }}"
                             alt="Maskot Si Ujang ITG"
                             class="h-48 sm:h-56 lg:h-64 object-contain drop-shadow-2xl">
                    </div>

                    {{-- Label Tag Maskot --}}
                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full bg-blue-950/80 border border-blue-400/30 text-amber-400 text-[10px] font-semibold tracking-wide shadow-md">
                        <span>Si Ujang · Maskot ITG</span>
                    </div>
                </div>
            </div>

            {{-- Bottom: 3 Poin Fitur Ekosistem & Badge Keamanan --}}
            <div class="relative z-10 space-y-3">
                <ul class="space-y-2 text-xs text-slate-300">
                    <li class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 border border-amber-400/40">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span>Pengajuan &amp; Verifikasi Proposal Digital Terpadu</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 border border-amber-400/40">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span>Peminjaman Sarana Prasarana &amp; Digitalisasi Surat</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 border border-amber-400/40">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span>Monitoring Kas Ormawa &amp; Unggah Arsip LPJ Resmi</span>
                    </li>
                </ul>

                <div class="pt-2">
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-900/80 border border-slate-700/60 text-slate-400 text-[11px]">
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                        <span>Portal Resmi Biro Kemahasiswaan ITG · Enkripsi SSL/TLS 256-bit</span>
                    </span>
                </div>
            </div>
        </div>

        {{-- ========================================================
             KOLOM KANAN: FORMULIR OTENTIKASI & CALLOUT UX
             ======================================================== --}}
        <div class="lg:w-1/2 bg-white p-8 sm:p-12 lg:p-16 flex flex-col justify-center items-center relative overflow-y-auto">
            
            <div class="w-full max-w-md my-auto">
                {{-- Header Formulir --}}
                <div class="mb-6">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-800 text-[11px] font-bold tracking-wide uppercase mb-3">
                        <svg class="w-3.5 h-3.5 text-blue-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                        <span>PORTAL OTENTIKASI PENGURUS &amp; ORMAWA</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Masuk ke Akun Anda
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
                        Silakan masukkan email akun resmi dan kata sandi yang terdaftar untuk mengelola sistem ormawa.
                    </p>
                </div>

                {{-- Session Status Flash --}}
                <x-auth-session-status class="mb-4" :status="session('status')" />

                {{-- Form Login dengan Alpine.js untuk show/hide password dan submit state --}}
                <form method="POST" action="{{ route('login') }}"
                      x-data="{ submitting: false, showPassword: false }"
                      @submit="submitting = true"
                      class="space-y-4">
                    @csrf

                    {{-- Input Email --}}
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">
                            Email Akun Pengurus / Ormawa
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </div>
                            <input id="email"
                                   type="text"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus
                                   autocomplete="username"
                                   placeholder="bkhm@test.com atau bem@itg.ac.id"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-blue-600 text-sm text-slate-900 placeholder-slate-400 transition">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                    </div>

                    {{-- Input Password dengan Toggle Visibilitas --}}
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">
                            Kata Sandi
                        </label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                            <input id="password"
                                   :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="••••••••••••"
                                   class="w-full pl-10 pr-11 py-3 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-blue-600 text-sm text-slate-900 placeholder-slate-400 transition">
                            
                            {{-- Toggle Tombol Mata --}}
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                                    title="Tampilkan / sembunyikan kata sandi">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                    </div>

                    {{-- Remember Me & Forgot Password --}}
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me"
                                   type="checkbox"
                                   name="remember"
                                   class="w-4 h-4 rounded border-slate-300 text-blue-700 focus:ring-blue-600">
                            <span class="ms-2 text-xs text-slate-700 font-medium">Ingat saya di perangkat ini</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                               class="text-xs font-semibold text-blue-700 hover:text-blue-900 hover:underline">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>

                    {{-- Tombol Masuk --}}
                    <div class="pt-2">
                        <button type="submit"
                                :disabled="submitting"
                                class="w-full min-h-[46px] py-3 px-5 rounded-xl bg-[#1E3A8A] hover:bg-blue-800 disabled:opacity-70 text-white font-bold text-sm flex items-center justify-center gap-2 shadow-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            <span x-text="submitting ? 'Memproses Masuk...' : 'Masuk ke Sistem SKIN'">Masuk ke Sistem SKIN</span>
                            <span x-show="!submitting">&rarr;</span>
                        </button>
                    </div>
                </form>

                {{-- Garis Pembatas ATAU --}}
                <div class="relative my-6 flex items-center justify-center">
                    <div class="border-t border-slate-200 w-full"></div>
                    <span class="bg-white px-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest absolute">
                        ATAU
                    </span>
                </div>

                {{-- Callout Card Bantuan Khusus Mahasiswa Bebas Login --}}
                <div class="p-4 bg-blue-50/80 border border-blue-200 rounded-2xl flex items-center gap-3.5 text-left shadow-sm">
                    <img src="{{ asset('images/maskot-itg-head.png') }}"
                         alt="Si Ujang"
                         class="w-11 h-11 rounded-full bg-amber-100 p-0.5 border border-blue-200 object-contain shrink-0 shadow-sm">
                    <div class="text-xs">
                        <p class="text-slate-600">Mahasiswa umum tidak perlu login untuk mengajukan tiket.</p>
                        <a href="{{ route('layanan.index') }}" class="font-bold text-blue-700 hover:text-blue-900 hover:underline inline-flex items-center gap-1 mt-0.5">
                            <span>Buka Portal Layanan Mahasiswa (Tanpa Login)</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                {{-- Footer Hak Cipta --}}
                <div class="mt-8 text-center text-xs text-slate-400">
                    &copy; {{ date('Y') }} Institut Teknologi Garut · Biro Kemahasiswaan &amp; Hubungan Masyarakat
                </div>
            </div>

        </div>
    </div>
</body>
</html>
