<div :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-indigo-900 text-white transition-all duration-300 flex flex-col h-full overflow-y-auto">
    <div class="p-4 flex items-center justify-center border-b border-indigo-800">
        <x-application-logo class="block h-10 w-auto fill-current text-white" />
        <span x-show="sidebarOpen" class="ml-3 font-bold text-lg whitespace-nowrap">Sistem Keuangan</span>
    </div>

    <nav class="flex-1 px-2 py-4 space-y-2">
        <!-- Main Section -->
        <div class="pb-4">
            <a href="{{ route('dashboard') }}" class="flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-800' : '' }}">
                <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Dashboard</span>
            </a>
        </div>

        <!-- Pengajuan Group -->
        @hasanyrole('ormawa|bem|bpm')
        <div class="space-y-1 pb-4">
            <div x-data="{ open: false }" class="group">
                <button @click="open = !open" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('pengajuan.*') ? 'bg-indigo-800' : '' }}">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Pengajuan</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('pengajuan.create') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Buat Pengajuan</a>
                    <a href="{{ route('pengajuan.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Riwayat Pengajuan</a>
                </div>
            </div>
        </div>
        @endhasanyrole

        <!-- Sarpras Opsi A: Riwayat terpisah per jenis - milik sendiri -->
        @hasanyrole('ormawa|bem|bpm')
        <div class="space-y-1 pb-4">
            <div x-data="{ open: true }" class="group">
                <button @click="open = !open" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('peminjaman.*') ? 'bg-indigo-800' : '' }}">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m0 4a4 4 0 014 4v8a4 4 0 01-4 4H5a4 4 0 01-4-4v-8a4 4 0 014-4h4z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Sarpras</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-4 space-y-2 mt-2">
                    <div class="space-y-1">
                        <div class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest px-2">Tempat & Fasilitas</div>
                        <a href="{{ route('peminjaman.tempat.create') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('peminjaman.tempat.create') ? 'bg-indigo-700' : '' }}">Ajukan Peminjaman</a>
                        <a href="{{ route('peminjaman.tempat.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('peminjaman.tempat.index') ? 'bg-indigo-700' : '' }}">Riwayat Tempat</a>
                    </div>
                    <div class="space-y-1 pt-2 border-t border-indigo-800">
                        <div class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest px-2">Sarana & Barang</div>
                        <a href="{{ route('peminjaman.barang.create') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('peminjaman.barang.create') ? 'bg-indigo-700' : '' }}">Ajukan Peminjaman</a>
                        <a href="{{ route('peminjaman.barang.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 transition-colors {{ request()->routeIs('peminjaman.barang.index') ? 'bg-indigo-700' : '' }}">Riwayat Barang</a>
                    </div>
                </div>
            </div>
        </div>
        @endhasanyrole

        @hasrole('bkh')
        <div class="space-y-1 pb-4">
            <div x-data="{ open: true }" class="group">
                <button @click="open = !open" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('bkkh.*','admin.*') ? 'bg-indigo-800' : '' }}">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Kelola BKKH</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-4 space-y-1 mt-2">
                    <a href="{{ route('bkkh.saldo.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('bkkh.saldo.*') ? 'bg-indigo-700' : '' }}">Manajemen Saldo</a>
                    <a href="{{ route('admin.users.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('admin.users.*') ? 'bg-indigo-700' : '' }}">Manajemen User</a>
                    <a href="{{ route('admin.konfigurasi.edit') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('admin.konfigurasi.*') ? 'bg-indigo-700' : '' }}">Manajemen Sistem</a>
                    <a href="{{ route('bkkh.arsip.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('bkkh.arsip.*') ? 'bg-indigo-700' : '' }}">Arsip Surat</a>
                    <a href="{{ route('bkkh.sp.create') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('bkkh.sp.*') ? 'bg-indigo-700' : '' }}">Buat Surat Peringatan</a>
                    <a href="{{ route('bkkh.verifikasi-tempat.index') }}" class="block ml-2 p-2 text-xs rounded hover:bg-indigo-700 {{ request()->routeIs('bkkh.verifikasi-tempat.*') ? 'bg-indigo-700' : '' }}">Verifikasi Tempat</a>
                </div>
            </div>
        </div>
        @endhasrole

        @hasanyrole('ormawa|bem|bpm')
        <!-- Persuratan Digital Group - hidden for bkh -->
        <div class="space-y-1 pb-4">
            <div x-data="{ open: false }" class="group">
                <button @click="open = !open" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('generator.*', 'archive.*') ? 'bg-indigo-800' : '' }}">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Persuratan Digital</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('generator.create') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Buat Proposal</a>
                    <a href="{{ route('generator.letters.create') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Buat Surat Lain</a>
                    <a href="{{ route('generator.lpj.create') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Buat LPJ</a>
                    <a href="{{ route('archive.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Arsip Digital</a>
                </div>
            </div>
        </div>

        <!-- Laporan Group - hidden for bkh -->
        <div class="space-y-1 pb-4">
            <div x-data="{ open: false }" class="group">
                <button @click="open = !open" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('lpj.*') ? 'bg-indigo-800' : '' }}">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 2v-6m-9-4h12a2 2 0 012 2v11a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Laporan</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('lpj.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Arsip LPJ</a>
                </div>
            </div>
        </div>

        <!-- Info Group - hidden for bkh -->
        <div class="space-y-1 pb-4">
            <div x-data="{ open: false }" class="group">
                <button @click="open = !open" class="w-full flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors {{ request()->routeIs('informasi.*', 'rapat.*') ? 'bg-indigo-800' : '' }}">
                    <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h-1.06C14.43 10 14.82 9.7 15.34 9.5C15.87 9.29 16.45 9.29 17 9.5M16 12a4 4 0 01-8 0c0 1.11-.3 2.15-1 3m5-10V4a2 2 0 00-2-2h-1a2 2 0 00-2 2v1m5 10V12a2 2 0 012-2h2a2 2 0 012 2v1" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Informasi</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7,7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-10 space-y-1 mt-1">
                    <a href="{{ route('informasi.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Pusat Info & Berita</a>
                    <a href="{{ route('rapat.index') }}" class="block p-2 text-xs rounded hover:bg-indigo-700 transition-colors">Jadwal Rapat & Koordinasi</a>
                </div>
            </div>
        </div>
        @endhasanyrole
    </nav>

    <div class="mt-auto p-4 border-t border-indigo-800">
        <a href="{{ route('profile.edit') }}" class="flex items-center p-2 rounded-lg hover:bg-indigo-800 transition-colors">
            <svg class="w-6 h-6 min-w-[24px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            <span x-show="sidebarOpen" class="ml-3 text-sm font-medium truncate">Profil Pengguna</span>
        </a>
    </div>
</div>
