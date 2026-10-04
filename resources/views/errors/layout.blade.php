<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Terjadi Kendala') - SKIN ITG</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-skin-torch.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 font-sans text-slate-800 antialiased flex flex-col justify-between">
    <!-- Top Bar -->
    <header class="w-full bg-white border-b border-slate-200 py-3.5 px-4 sm:px-8">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg">
                <img src="{{ asset('images/logo_itg.png') }}" alt="Logo ITG" class="h-9 w-auto object-contain">
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-slate-900 group-hover:text-blue-700 transition-colors">SKIN ITG</span>
                    <span class="text-xs text-slate-500">Sistem Informasi Kemahasiswaan</span>
                </div>
            </a>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-3 py-1.5 rounded-lg transition-colors focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 px-3 py-1.5 rounded-lg transition-colors focus:ring-2 focus:ring-blue-500">
                        Masuk Sistem
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Card -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-auto">
        <div class="w-full max-w-lg bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-10 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl @yield('icon-bg', 'bg-blue-50 text-blue-600') mb-6 ring-8 ring-slate-50">
                @yield('icon')
            </div>

            <div class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">
                @yield('code', 'Error')
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-3">
                @yield('headline')
            </h1>

            <p class="text-sm text-slate-600 leading-relaxed max-w-md mx-auto mb-8">
                @yield('message')
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                @yield('actions')
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full py-4 text-center text-xs text-slate-400 border-t border-slate-200 bg-white">
        &copy; {{ date('Y') }} Institut Teknologi Garut &bull; Sistem Informasi Kemahasiswaan
    </footer>
</body>
</html>
