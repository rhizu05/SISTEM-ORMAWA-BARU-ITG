<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Sistem Kemahasiswaan ITG') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen flex flex-col justify-center items-center">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Sistem Kemahasiswaan ITG</h1>
            <p class="text-gray-600">Sistem Kemahasiswaan Mahasiswa Institut Teknologi Garut</p>
        </div>

        <div class="space-y-4">
            <a href="{{ route('login') }}" class="block w-64 text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded shadow">
                Login Sistem
            </a>
            
            <a href="{{ route('aspirasi.create') }}" class="block w-64 text-center bg-white border border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-bold py-3 px-4 rounded shadow">
                Sampaikan Aspirasi
            </a>
        </div>
    </div>
</body>
</html>