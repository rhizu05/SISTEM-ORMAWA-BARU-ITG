<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Bendahara') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-yellow-300">
                <div class="p-6 text-gray-900 text-center">
                    <h3 class="text-sm font-medium text-gray-500 uppercase">Pengajuan Siap Cair</h3>
                    <p class="text-4xl font-bold text-yellow-600 mt-2">{{ $stats['siap_cair'] }}</p>
                    <a href="{{ route('verifikasi.index') }}" class="mt-4 inline-block text-sm text-yellow-600 hover:underline">Proses Sekarang &rarr;</a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    <h3 class="text-sm font-medium text-gray-500 uppercase">Total Dana Dicairkan (Bulan Ini)</h3>
                    <p class="text-4xl font-bold text-green-600 mt-2">Rp {{ number_format($stats['total_dicairkan'], 0, ',', '.') }}</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>