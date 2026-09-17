<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased">
        {{-- UI-019: skip link untuk pengguna keyboard/screen reader --}}
        <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-2 focus:left-2 focus:bg-white focus:text-indigo-700 focus:px-4 focus:py-2 focus:rounded focus:shadow">
            Lewati ke konten utama
        </a>
        <div class="min-h-screen bg-gray-100" x-data="{ sidebarOpen: false }">
            <div class="flex h-screen overflow-hidden">
                
                <!-- Sidebar -->
                @include('layouts.sidebar')

                <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
                    <!-- Top Header/Navbar -->
                    <header class="flex items-center justify-between px-6 py-4 bg-white border-b sticky top-0 z-50">
                        <div class="flex items-center">
                            <button @click="sidebarOpen = !sidebarOpen" aria-label="Buka atau tutup menu navigasi" class="text-gray-500 focus:outline-none focus:text-gray-700 p-2 rounded-md hover:bg-gray-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <div class="ml-4 font-semibold text-xl text-gray-800">
                                {{ $header ?? '' }}
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            @include('layouts.user-menu')
                        </div>
                    </header>

                    <!-- Main Content -->
                    <main id="main-content" class="p-6">
                        {{-- SEC-03: flash dirender SEKALI di sini. Jangan tambahkan rendering
                             flash per-view — halaman yang lupa merendernya membuat pesan
                             hilang tanpa jejak (bug F1–F4, BACKLOG-004). --}}
                        <x-flash />

                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
