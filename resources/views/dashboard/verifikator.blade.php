<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 leading-tight">
            {{ __('Dashboard Verifikator') }} ({{ strtoupper(Auth::user()->roles->first()?->name) }})
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Antrean Verifikasi Anda</h3>
                    <p class="text-3xl font-extrabold text-[#0B1528] mt-2">{{ $stats['antrian_verifikasi'] }}</p>
                    <p class="text-xs text-slate-500 mt-1">Proposal masuk yang memerlukan telaah dan tindak lanjut</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('verifikasi.index') }}" class="inline-flex items-center min-h-[44px] text-xs font-bold text-blue-700 hover:text-blue-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 rounded">
                        Lihat Antrean &rarr;
                    </a>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Telah Disetujui (Bulan Ini)</h3>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $stats['total_disetujui'] }}</p>
                    <p class="text-xs text-slate-500 mt-1">Proposal yang telah berhasil Anda rekomendasikan / setujui</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>