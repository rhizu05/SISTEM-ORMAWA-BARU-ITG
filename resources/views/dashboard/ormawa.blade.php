<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Ormawa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex flex-col items-center">
                    <h3 class="text-sm font-medium text-gray-500 uppercase">Sisa Saldo Dana</h3>
                    <p class="text-3xl font-bold text-green-600 mt-2">Rp {{ number_format($stats['saldo'], 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex flex-col items-center">
                    <h3 class="text-sm font-medium text-gray-500 uppercase">Total Pengajuan</h3>
                    <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $stats['total_pengajuan'] }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex flex-col items-center">
                    <h3 class="text-sm font-medium text-gray-500 uppercase">Dalam Proses</h3>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['sedang_proses'] }}</p>
                </div>
            </div>

            <div class="md:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Pengumuman Terbaru</h3>
                    <p class="text-gray-500 italic">Belum ada pengumuman.</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>