<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' - ' : '' }}SKIN - Sistem Ormawa Institut Teknologi Garut</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo-skin-torch.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/logo-skin-torch.png') }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#0B1528">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-50">
            <div>
                <a href="/" class="flex flex-col items-center">
                    <div class="w-24 h-24 rounded-2xl bg-white p-2 border border-slate-200 flex items-center justify-center mb-2">
                        <x-application-logo class="w-full h-full object-contain" />
                    </div>
                    <span class="text-xl font-bold text-slate-800 tracking-tight">Institut Teknologi Garut</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white border border-slate-200 overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
