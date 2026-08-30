<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Sarana & Prasarana (Sarpras)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                <div class="p-6 text-gray-900 text-center">
                    <h3 class="text-sm font-medium text-gray-500 uppercase">Peminjaman Ruangan (Bulan Ini)</h3>
                    <p class="text-4xl font-bold text-indigo-600 mt-2">{{ $stats['peminjaman_ruangan'] }}</p>
                    <a href="{{ route('peminjaman.verifikasi.index') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:underline">Verifikasi Peminjaman Ruangan &rarr;</a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                <div class="p-6 text-gray-900 text-center">
                    <h3 class="text-sm font-medium text-gray-500 uppercase">Peminjaman Barang (Bulan Ini)</h3>
                    <p class="text-4xl font-bold text-blue-600 mt-2">{{ $stats['peminjaman_barang'] }}</p>
                    <a href="{{ route('peminjaman.verifikasi.index') }}" class="mt-4 inline-block text-sm text-blue-600 hover:underline">Verifikasi Peminjaman Barang &rarr;</a>
                </div>
            </div>

            @hasrole('sarpras_barang')
            <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                <div class="p-6 text-gray-900 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold">Manajemen Inventaris Barang</h3>
                        <p class="text-sm text-gray-600 mt-1">Kelola stok dan daftar barang yang dapat dipinjam oleh Ormawa.</p>
                    </div>
                    <a href="{{ route('sarpras.barang.index') }}" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded font-semibold">Kelola Inventaris</a>
                </div>
            </div>
            @endhasrole

        </div>
    </div>
</x-app-layout>