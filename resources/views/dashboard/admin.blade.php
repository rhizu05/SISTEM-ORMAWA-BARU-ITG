<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin / BKKH') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase">Total Pengguna Sistem</h3>
                        <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $stats['total_users'] }}</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="bg-indigo-100 text-indigo-800 px-4 py-2 rounded font-semibold hover:bg-indigo-200">Kelola Users</a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase">Pengaturan Sistem</h3>
                        <p class="text-sm text-gray-600 mt-2">Kelola logo, kop surat, dan nama aplikasi</p>
                    </div>
                    <a href="{{ route('admin.konfigurasi.edit') }}" class="bg-gray-100 text-gray-800 px-4 py-2 rounded font-semibold hover:bg-gray-200">Atur Sistem</a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>