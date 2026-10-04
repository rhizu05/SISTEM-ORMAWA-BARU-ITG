<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 leading-tight">
            {{ __('Dashboard Admin Sistem') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengguna Sistem</h3>
                    <p class="text-3xl font-extrabold text-[#0B1528] mt-2">{{ $stats['total_users'] }}</p>
                    <p class="text-xs text-slate-500 mt-1">Akun ormawa, pimpinan, dan verifikator aktif</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2 bg-[#0B1528] hover:bg-[#1E3A8A] text-white rounded-xl text-xs font-bold shadow-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                    Kelola Pengguna
                </a>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengaturan Institusi</h3>
                    <p class="text-xs text-slate-600 mt-2">Kelola logo, kop surat resmi, dan identitas kampus</p>
                </div>
                <a href="{{ route('admin.konfigurasi.edit') }}" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-600">
                    Atur Sistem
                </a>
            </div>

        </div>
    </div>
</x-app-layout>