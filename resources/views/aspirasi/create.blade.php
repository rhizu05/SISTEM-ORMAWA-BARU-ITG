<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sampaikan Aspirasi - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-100">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div>
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-2xl font-bold mb-4 text-center">Sampaikan Aspirasi</h2>
            
            @if (session('success'))
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 border border-green-400 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('aspirasi.store') }}">
                @csrf

                <!-- Nama -->
                <div>
                    <x-input-label for="nama_pengirim" :value="__('Nama Lengkap (opsional/samaran boleh)')" />
                    <x-text-input id="nama_pengirim" class="block mt-1 w-full" type="text" name="nama_pengirim" :value="old('nama_pengirim', 'Anonim')" required autofocus />
                    <x-input-error :messages="$errors->get('nama_pengirim')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <x-input-label for="email_pengirim" :value="__('Email (opsional)')" />
                    <x-text-input id="email_pengirim" class="block mt-1 w-full" type="email" name="email_pengirim" :value="old('email_pengirim')" />
                    <x-input-error :messages="$errors->get('email_pengirim')" class="mt-2" />
                </div>

                <!-- Isi Aspirasi -->
                <div class="mt-4">
                    <x-input-label for="isi_aspirasi" :value="__('Isi Aspirasi')" />
                    <textarea id="isi_aspirasi" name="isi_aspirasi" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="5" required>{{ old('isi_aspirasi') }}</textarea>
                    <x-input-error :messages="$errors->get('isi_aspirasi')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                        {{ __('Kembali ke Login') }}
                    </a>
                    
                    <x-primary-button class="ms-4">
                        {{ __('Kirim Aspirasi') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>