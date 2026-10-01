{{-- Backdrop Mobile Drawer --}}
<div x-show="isMobile && sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false" 
     class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 lg:hidden" 
     x-cloak></div>

<nav :class="{
        'translate-x-0 w-64 fixed inset-y-0 left-0 z-50 shadow-2xl': isMobile && sidebarOpen,
        '-translate-x-full fixed inset-y-0 left-0 z-50': isMobile && !sidebarOpen,
        'relative z-30': !isMobile,
        'w-64': !isMobile && sidebarOpen,
        'w-20': !isMobile && !sidebarOpen
     }" 
     :data-collapsed="(!isMobile && !sidebarOpen) ? 'true' : 'false'" 
     aria-label="Navigasi utama" 
     class="bg-[#0B1528] text-white transition-all duration-300 flex flex-col h-full border-r border-[#1E2D4A] select-none">
    <style>
        /* Saat ringkas: pusatkan ikon agar sejajar dengan logo ITG di atas. */
        nav[data-collapsed="true"] a,
        nav[data-collapsed="true"] button { justify-content: center; }
        /* Scrollbar styling halus untuk sidebar */
        nav::-webkit-scrollbar { width: 4px; }
        nav::-webkit-scrollbar-thumb { background: #1E2D4A; border-radius: 4px; }
        .skin-scrollbar::-webkit-scrollbar { width: 4px; }
        .skin-scrollbar::-webkit-scrollbar-thumb { background: #1E2D4A; border-radius: 4px; }
    </style>

    {{-- Watermark Background Ornamen Logo Obor Monokrom Transparan (Opsi 4) --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden select-none" aria-hidden="true">
        <img src="{{ asset('images/logo-skin-torch-mono-white.png') }}" class="absolute -top-6 -left-6 w-24 opacity-[0.06] -rotate-12" alt="">
        <img src="{{ asset('images/logo-skin-torch-mono-white.png') }}" class="absolute top-1/4 -right-8 w-28 opacity-[0.05] rotate-12" alt="">
        <img src="{{ asset('images/logo-skin-torch-mono-white.png') }}" class="absolute top-1/2 -left-6 w-24 opacity-[0.06] rotate-6" alt="">
        <img src="{{ asset('images/logo-skin-torch-mono-white.png') }}" class="absolute top-3/4 -right-6 w-28 opacity-[0.05] -rotate-12" alt="">
        <img src="{{ asset('images/logo-skin-torch-mono-white.png') }}" class="absolute -bottom-8 -left-4 w-28 opacity-[0.07] rotate-6" alt="">
    </div>

    {{-- Header Brand Identitas Institusi --}}
    <div class="relative z-10 p-4 flex items-center justify-between border-b border-[#1E2D4A] shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-white p-1 flex items-center justify-center shadow-md shrink-0 border border-white/20">
                <img src="{{ asset('images/logo-skin.png') }}" class="w-full h-full object-contain" alt="Logo SKIN ITG">
            </div>
            <div x-show="sidebarOpen" class="min-w-0">
                <span class="block text-sm font-extrabold text-white tracking-wide truncate">SKIN ITG</span>
                <span class="block text-[11px] text-slate-400 font-medium truncate">Institut Teknologi Garut</span>
            </div>
        </a>
        <button x-show="isMobile" @click="sidebarOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition lg:hidden" aria-label="Tutup Menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Nav Items Body --}}
    <div class="relative z-10 p-2 space-y-1 flex-1 overflow-y-auto overflow-x-hidden skin-scrollbar">
        @hasrole('admin')
        {{-- ==================== ADMIN NAVIGATION ==================== --}}
        <div class="pb-1">
            <a href="{{ route('dashboard') }}" :title="!sidebarOpen ? 'Dashboard Admin' : null"
               class="flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-[#132342] text-white font-semibold border-l-[3.5px] border-amber-500 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}">
                <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('dashboard') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" /></svg>
                <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Dashboard Admin</span>
            </a>
        </div>

        <!-- Administrasi & Konfigurasi -->
        <div class="space-y-1 pb-1">
            <div x-data="{ open: true }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)"
                        class="w-full flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('admin.*', 'bkhm.saldo.*') ? 'bg-[#132342] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}"
                        :title="!sidebarOpen ? 'Administrasi Sistem' : null">
                    <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('admin.*', 'bkhm.saldo.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Administrasi Sistem</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-9 space-y-1 mt-1 border-l border-[#1E2D4A] ml-4">
                    <a href="{{ route('admin.users.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Manajemen Pengguna</a>
                    <a href="{{ route('admin.konfigurasi.edit') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('admin.konfigurasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Konfigurasi Sistem &amp; Kop</a>
                    <a href="{{ route('bkhm.saldo.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('bkhm.saldo.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Manajemen Saldo Kas</a>
                </div>
            </div>
        </div>

        <!-- Monitoring Proposal & Anggaran -->
        <div class="space-y-1 pb-1">
            <div x-data="{ open: true }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)"
                        class="w-full flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('pengajuan.*', 'verifikasi.*', 'lpj.*', 'archive.*', 'generator.*') ? 'bg-[#132342] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}"
                        :title="!sidebarOpen ? 'Proposal & LPJ' : null">
                    <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('pengajuan.*', 'verifikasi.*', 'lpj.*', 'archive.*', 'generator.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Proposal &amp; LPJ</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-9 space-y-1 mt-1 border-l border-[#1E2D4A] ml-4">
                    <a href="{{ route('verifikasi.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('verifikasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Antrean Verifikasi</a>
                    <a href="{{ route('pengajuan.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('pengajuan.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Daftar Pengajuan</a>
                    <a href="{{ route('lpj.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('lpj.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Monitoring LPJ</a>
                    <a href="{{ route('archive.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('archive.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Arsip Digital Dokumen</a>
                    <a href="{{ route('bkhm.export.excel') }}" class="block p-1.5 text-xs rounded-lg transition-colors text-slate-400 hover:text-white hover:bg-[#132342]">Ekspor Rekap Excel</a>
                    <a href="{{ route('bkhm.export.pdf') }}" class="block p-1.5 text-xs rounded-lg transition-colors text-slate-400 hover:text-white hover:bg-[#132342]">Ekspor Rekap PDF</a>
                </div>
            </div>
        </div>

        <!-- Fasilitas & Layanan Mahasiswa -->
        <div class="space-y-1 pb-1">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)"
                        class="w-full flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('peminjaman.*', 'sarpras.*', 'bpm.*', 'informasi.*', 'rapat.*') ? 'bg-[#132342] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}"
                        :title="!sidebarOpen ? 'Fasilitas & Layanan' : null">
                    <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('peminjaman.*', 'sarpras.*', 'bpm.*', 'informasi.*', 'rapat.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Fasilitas &amp; Layanan</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-9 space-y-1 mt-1 border-l border-[#1E2D4A] ml-4">
                    <a href="{{ route('peminjaman.verifikasi.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('peminjaman.verifikasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Verifikasi Peminjaman</a>
                    <a href="{{ route('sarpras.ruangan.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('sarpras.ruangan.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Master Ruangan</a>
                    <a href="{{ route('sarpras.barang.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('sarpras.barang.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Master Barang</a>
                    <a href="{{ route('sarpras.jadwal.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('sarpras.jadwal.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Jadwal Perkuliahan</a>
                    <a href="{{ route('bpm.aspirasi.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('bpm.aspirasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Aspirasi Mahasiswa</a>
                    <a href="{{ route('bpm.regulasi.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('bpm.regulasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Regulasi Organisasi</a>
                    <a href="{{ route('informasi.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('informasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Pusat Informasi Publik</a>
                    <a href="{{ route('rapat.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('rapat.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Jadwal Rapat</a>
                </div>
            </div>
        </div>
        @else
        {{-- ==================== NON-ADMIN ROLES ==================== --}}

        <!-- Notifikasi -->
        <div class="pb-1">
            <a href="{{ route('notifikasi.index') }}" @if(request()->routeIs('notifikasi.*')) aria-current="page" @endif :title="!sidebarOpen ? 'Notifikasi' : null"
               class="flex items-center justify-between p-2 rounded-xl transition-all {{ request()->routeIs('notifikasi.*') ? 'bg-[#132342] text-white font-semibold border-l-[3.5px] border-amber-500 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('notifikasi.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Notifikasi</span>
                </div>
                @if(($unreadNotifikasi ?? 0) > 0)
                    <span x-show="sidebarOpen" class="ml-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full" aria-label="{{ $unreadNotifikasi }} notifikasi belum dibaca">{{ $unreadNotifikasi }}</span>
                @endif
            </a>
        </div>

        <!-- Dashboard -->
        <div class="pb-1">
            <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif :title="!sidebarOpen ? 'Dashboard' : null"
               class="flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-[#132342] text-white font-semibold border-l-[3.5px] border-amber-500 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}">
                <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('dashboard') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Dashboard</span>
            </a>
        </div>

        <!-- Verifikasi Proposal (Pemeriksa: BEM, BPM, BKHM, WR3, Bendahara) -->
        @hasanyrole('bem|bpm|bkhm|wr3|bendahara')
        <div class="pb-1">
            <a href="{{ route('verifikasi.index') }}" @if(request()->routeIs('verifikasi.*')) aria-current="page" @endif :title="!sidebarOpen ? 'Verifikasi Proposal' : null"
               class="flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('verifikasi.*') ? 'bg-[#132342] text-white font-semibold border-l-[3.5px] border-amber-500 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}">
                <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('verifikasi.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span x-show="sidebarOpen" class="ml-3 text-sm truncate">
                    @if(auth()->user()->hasRole('bendahara'))
                        Pencairan Dana
                    @else
                        Verifikasi Proposal
                    @endif
                </span>
            </a>
        </div>
        @endhasanyrole

        <!-- BEM Special Group -->
        @hasrole('bem')
        <div class="space-y-1 pb-1">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)"
                        class="w-full flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('bem.*') ? 'bg-[#132342] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}"
                        :title="!sidebarOpen ? 'Kelola BEM' : null">
                    <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('bem.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v17M4 5h12l-2.5 3.5L16 12H4" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Kelola BEM</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-9 space-y-1 mt-1 border-l border-[#1E2D4A] ml-4">
                    <a href="{{ route('verifikasi.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('verifikasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Verifikasi Proposal</a>
                    <a href="{{ route('proker.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('proker.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Program Kerja</a>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- BPM Special Group -->
        @hasrole('bpm')
        <div class="space-y-1 pb-1">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)"
                        class="w-full flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('bpm.*') ? 'bg-[#132342] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}"
                        :title="!sidebarOpen ? 'Kelola BPM' : null">
                    <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('bpm.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16M6 8h12M6 8l-2.5 6h5L6 8zm12 0l-2.5 6h5L18 8zM9 20h6" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Kelola BPM</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-9 space-y-1 mt-1 border-l border-[#1E2D4A] ml-4">
                    <a href="{{ route('bpm.dashboard') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('bpm.dashboard') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Dashboard BPM</a>
                    <a href="{{ route('verifikasi.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('verifikasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Verifikasi Proposal</a>
                    <a href="{{ route('bpm.sp.create') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('bpm.sp.create') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Buat Surat Peringatan</a>
                    <a href="{{ route('bpm.sp.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('bpm.sp.index') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Riwayat Surat Peringatan</a>
                    <a href="{{ route('bpm.aspirasi.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors">Kelola Aspirasi</a>
                    <a href="{{ route('bpm.regulasi.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors">Kelola Regulasi</a>
                    <a href="{{ route('proker.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('proker.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Monitoring Proker Ormawa</a>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- WR3 Special Group -->
        @hasrole('wr3')
        <div class="space-y-1 pb-1">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)"
                        class="w-full flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('dashboard','wr3.*','verifikasi.*') ? 'bg-[#132342] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}"
                        :title="!sidebarOpen ? 'Kelola WR3' : null">
                    <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('dashboard','wr3.*','verifikasi.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2-2m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Kelola WR3</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-9 space-y-1 mt-1 border-l border-[#1E2D4A] ml-4">
                    <a href="{{ route('dashboard') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Dashboard WR3</a>
                    <a href="{{ route('wr3.sp.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('wr3.sp.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">
                        <div class="flex items-center justify-between">
                            <span>Validasi SP</span>
                            @php
                                $wr3PendingSpCount = \App\Models\SuratPeringatan::menungguValidasi()->count();
                            @endphp
                            @if($wr3PendingSpCount > 0)
                                <span class="bg-amber-400 text-gray-900 text-[10px] font-bold px-1.5 py-0.2 rounded-full">{{ $wr3PendingSpCount }}</span>
                            @endif
                        </div>
                    </a>
                    <a href="{{ route('verifikasi.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('verifikasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Verifikasi Proposal</a>
                    <a href="{{ route('lpj.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('lpj.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Monitoring LPJ</a>
                    <a href="{{ route('archive.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('archive.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Arsip Digital Dokumen</a>
                    <a href="{{ route('prestasi.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('prestasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Verifikasi Prestasi</a>
                    <a href="{{ route('bkhm.export.excel') }}" class="block p-1.5 text-xs rounded-lg transition-colors text-slate-400 hover:text-white hover:bg-[#132342]">Ekspor Keuangan Excel</a>
                    <a href="{{ route('bkhm.export.pdf') }}" class="block p-1.5 text-xs rounded-lg transition-colors text-slate-400 hover:text-white hover:bg-[#132342]">Ekspor Keuangan PDF</a>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- Bendahara Special Group -->
        @hasrole('bendahara')
        <div class="space-y-1 pb-1">
            <div x-data="{ open: true }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)"
                        class="w-full flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('bendahara.*','verifikasi.*') ? 'bg-[#132342] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}"
                        :title="!sidebarOpen ? 'Kelola Bendahara' : null">
                    <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('bendahara.*','verifikasi.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Kelola Bendahara</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-9 space-y-1 mt-1 border-l border-[#1E2D4A] ml-4">
                    <a href="{{ route('verifikasi.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('verifikasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Antrean Pencairan Dana</a>
                    <a href="{{ route('bendahara.export.excel') }}" class="block p-1.5 text-xs rounded-lg transition-colors text-slate-400 hover:text-white hover:bg-[#132342]">Unduh Rekap Excel</a>
                    <a href="{{ route('bendahara.export.pdf') }}" class="block p-1.5 text-xs rounded-lg transition-colors text-slate-400 hover:text-white hover:bg-[#132342]">Unduh Rekap PDF</a>
                    <a href="{{ route('bendahara.export') }}" class="block p-1.5 text-xs rounded-lg transition-colors text-slate-400 hover:text-white hover:bg-[#132342]">Unduh Rekap CSV</a>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- Sarpras Group -->
        @hasrole('sarpras')
        <div class="space-y-1 pb-1">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)"
                        class="w-full flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('sarpras.*','peminjaman.verifikasi.*') ? 'bg-[#132342] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}"
                        :title="!sidebarOpen ? 'Kelola Sarpras' : null">
                    <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('sarpras.*','peminjaman.verifikasi.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Kelola Sarpras</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-9 space-y-1 mt-1 border-l border-[#1E2D4A] ml-4">
                    <a href="{{ route('peminjaman.verifikasi.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('peminjaman.verifikasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Verifikasi Peminjaman</a>
                    <a href="{{ route('sarpras.barang.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('sarpras.barang.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Master Barang</a>
                    <a href="{{ route('sarpras.ruangan.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('sarpras.ruangan.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Master Ruangan</a>
                    <a href="{{ route('sarpras.jadwal.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('sarpras.jadwal.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Jadwal Perkuliahan</a>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- BKHM Special Group -->
        @hasrole('bkhm')
        <div class="space-y-1 pb-1">
            <div x-data="{ open: true }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)"
                        class="w-full flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('bkhm.*','admin.*','verifikasi.*','peminjaman.verifikasi.*') ? 'bg-[#132342] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}"
                        :title="!sidebarOpen ? 'Kelola BKHM' : null">
                    <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('bkhm.*','admin.*','verifikasi.*','peminjaman.verifikasi.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 21V6a1 1 0 011-1h9a1 1 0 011 1v15M2 21h20M15 21V11h4a1 1 0 011 1v9M8 9h3M8 13h3M8 17h3" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Kelola BKHM</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-4 space-y-1 mt-2">
                    <div class="text-[10px] font-bold text-amber-400 uppercase tracking-widest px-2">Verifikasi &amp; Anggaran</div>
                    <a href="{{ route('verifikasi.index') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('verifikasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Verifikasi Proposal</a>
                    <a href="{{ route('lpj.index') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('lpj.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Monitoring &amp; Arsip LPJ</a>
                    <a href="{{ route('archive.index') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('archive.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Arsip Digital Dokumen</a>
                    <a href="{{ route('peminjaman.verifikasi.index') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('peminjaman.verifikasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Verifikasi Peminjaman</a>
                    <a href="{{ route('bkhm.saldo.index') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('bkhm.saldo.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Manajemen Saldo</a>
                    <a href="{{ route('bkhm.arsip.index') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('bkhm.arsip.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Arsip Surat</a>
                    <a href="{{ route('bkhm.sp.create') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('bkhm.sp.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Buat Surat Peringatan</a>

                    <div class="text-[10px] font-bold text-amber-400 uppercase tracking-widest px-2 pt-2 border-t border-[#1E2D4A]">Administrasi &amp; Konfigurasi</div>
                    <a href="{{ route('admin.users.index') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Manajemen Pengguna</a>
                    <a href="{{ route('admin.konfigurasi.edit') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('admin.konfigurasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Konfigurasi Sistem &amp; Kop</a>
                    <a href="{{ route('admin.bug.index') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('admin.bug.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">
                        <div class="flex items-center justify-between">
                            <span>Evaluasi Bug IT</span>
                            @php
                                $itPendingBugCount = \App\Models\LaporanBug::whereIn('status', ['diteruskan_ke_it', 'sedang_diperbaiki'])->count();
                            @endphp
                            @if($itPendingBugCount > 0)
                                <span class="bg-blue-600 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full">{{ $itPendingBugCount }}</span>
                            @endif
                        </div>
                    </a>
                    
                    <div class="text-[10px] font-bold text-amber-400 uppercase tracking-widest px-2 pt-2 border-t border-[#1E2D4A]">Layanan &amp; Kehumasan Kampus</div>
                    <a href="{{ route('bkhm.kurasi.index') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('bkhm.kurasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">
                        <div class="flex items-center justify-between">
                            <span>Kurasi Berita Kampus</span>
                            @php
                                $bkhmPendingBeritaCount = \App\Models\Pengumuman::pendingKurasi()->count();
                            @endphp
                            @if($bkhmPendingBeritaCount > 0)
                                <span class="bg-amber-400 text-gray-900 text-[10px] font-bold px-1.5 py-0.2 rounded-full">{{ $bkhmPendingBeritaCount }}</span>
                            @endif
                        </div>
                    </a>
                    <a href="{{ route('bkhm.konseling.index') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('bkhm.konseling.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Tiket Konseling</a>
                    <a href="{{ route('bkhm.tiket-aspirasi.index') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('bkhm.tiket-aspirasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Eskalasi Aspirasi</a>
                    <a href="{{ route('bkhm.tiket-prestasi.index') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('bkhm.tiket-prestasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Verifikasi Prestasi</a>
                    <a href="{{ route('bkhm.bug.index') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('bkhm.bug.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">
                        <div class="flex items-center justify-between">
                            <span>Laporan Kendala Sistem</span>
                            @php
                                $bkhmPendingBugCount = \App\Models\LaporanBug::where('status', 'menunggu_bkhm')->count();
                            @endphp
                            @if($bkhmPendingBugCount > 0)
                                <span class="bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full">{{ $bkhmPendingBugCount }}</span>
                            @endif
                        </div>
                    </a>

                    <div class="text-[10px] font-bold text-amber-400 uppercase tracking-widest px-2 pt-2 border-t border-[#1E2D4A]">Ekspor Laporan</div>
                    <a href="{{ route('bkhm.export.excel') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors text-slate-400 hover:text-white hover:bg-[#132342]">Ekspor Keuangan Excel</a>
                    <a href="{{ route('bkhm.export.pdf') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors text-slate-400 hover:text-white hover:bg-[#132342]">Ekspor Keuangan PDF</a>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- Pengajuan Group (Ormawa, BEM, BPM) -->
        @hasanyrole('ormawa|bem|bpm')
        <div class="space-y-1 pb-1">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)"
                        class="w-full flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('pengajuan.*') ? 'bg-[#132342] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}"
                        :title="!sidebarOpen ? 'Pengajuan' : null">
                    <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('pengajuan.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Pengajuan</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-9 space-y-1 mt-1 border-l border-[#1E2D4A] ml-4">
                    <a href="{{ route('pengajuan.create') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('pengajuan.create') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Buat Pengajuan</a>
                    <a href="{{ route('pengajuan.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('pengajuan.index') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Riwayat Pengajuan</a>
                    <a href="{{ route('proker.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('proker.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Program Kerja</a>
                </div>
            </div>
        </div>

        <!-- Sarpras Group (Tempat & Barang) -->
        <div class="space-y-1 pb-1">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)"
                        class="w-full flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('peminjaman.*') ? 'bg-[#132342] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}"
                        :title="!sidebarOpen ? 'Sarpras' : null">
                    <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('peminjaman.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m0 4a4 4 0 014 4v8a4 4 0 01-4 4H5a4 4 0 01-4-4v-8a4 4 0 014-4h4z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Sarpras</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-4 space-y-2 mt-2">
                    <div class="space-y-1">
                        <div class="text-[10px] font-bold text-amber-400 uppercase tracking-widest px-2">Tempat &amp; Fasilitas</div>
                        <a href="{{ route('peminjaman.tempat.create') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('peminjaman.tempat.create') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Ajukan Peminjaman</a>
                        <a href="{{ route('peminjaman.tempat.index') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('peminjaman.tempat.index') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Riwayat Tempat</a>
                    </div>
                    <div class="space-y-1 pt-2 border-t border-[#1E2D4A]">
                        <div class="text-[10px] font-bold text-amber-400 uppercase tracking-widest px-2">Sarana &amp; Barang</div>
                        <a href="{{ route('peminjaman.barang.create') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('peminjaman.barang.create') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Ajukan Peminjaman</a>
                        <a href="{{ route('peminjaman.barang.index') }}" class="block ml-2 p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('peminjaman.barang.index') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Riwayat Barang</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Persuratan Digital Group -->
        <div class="space-y-1 pb-1">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)"
                        class="w-full flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('generator.*', 'archive.*') ? 'bg-[#132342] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}"
                        :title="!sidebarOpen ? 'Persuratan Digital' : null">
                    <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('generator.*', 'archive.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Persuratan Digital</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-9 space-y-1 mt-1 border-l border-[#1E2D4A] ml-4">
                    <a href="{{ route('generator.create') }}" class="block p-1.5 text-xs rounded-lg transition-colors text-slate-400 hover:text-white hover:bg-[#132342]">Buat Proposal</a>
                    <a href="{{ route('generator.letters.create') }}" class="block p-1.5 text-xs rounded-lg transition-colors text-slate-400 hover:text-white hover:bg-[#132342]">Buat Surat Lain</a>
                    <a href="{{ route('generator.lpj.create') }}" class="block p-1.5 text-xs rounded-lg transition-colors text-slate-400 hover:text-white hover:bg-[#132342]">Buat LPJ</a>
                    <a href="{{ route('archive.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors text-slate-400 hover:text-white hover:bg-[#132342]">Arsip Digital</a>
                </div>
            </div>
        </div>

        <!-- Laporan Group -->
        <div class="space-y-1 pb-1">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)"
                        class="w-full flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('lpj.*') ? 'bg-[#132342] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}"
                        :title="!sidebarOpen ? 'Laporan' : null">
                    <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('lpj.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 2v-6m-9-4h12a2 2 0 012 2v11a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Laporan</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-9 space-y-1 mt-1 border-l border-[#1E2D4A] ml-4">
                    <a href="{{ route('lpj.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors text-slate-400 hover:text-white hover:bg-[#132342]">Arsip LPJ</a>
                </div>
            </div>
        </div>

        <!-- Prestasi & Aspirasi Group -->
        <div class="space-y-1 pb-1">
            <a href="{{ route('prestasi.index') }}" :title="!sidebarOpen ? 'Pelaporan Prestasi' : null"
               class="flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('prestasi.*') ? 'bg-[#132342] text-white font-semibold border-l-[3.5px] border-amber-500 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}">
                <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('prestasi.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Pelaporan Prestasi</span>
            </a>
            <a href="{{ route('sp.saya.index') }}" :title="!sidebarOpen ? 'Surat Peringatan Saya' : null"
               class="flex items-center justify-between p-2 rounded-xl transition-all {{ request()->routeIs('sp.saya.*') ? 'bg-[#132342] text-white font-semibold border-l-[3.5px] border-amber-500 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 min-w-[20px] text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Surat Peringatan Saya</span>
                </div>
                @if(auth()->user()->suratPeringatans()->count() > 0)
                    <span x-show="sidebarOpen" class="ml-auto bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full" title="{{ auth()->user()->suratPeringatans()->count() }} Surat Peringatan">
                        {{ auth()->user()->suratPeringatans()->count() }}
                    </span>
                @endif
            </a>
        </div>
        @endhasanyrole

        <!-- Informasi & Jadwal Rapat (Tersedia untuk semua role internal) -->
        <div class="space-y-1 pb-1">
            <div x-data="{ open: false }" class="group">
                <button @click="!sidebarOpen ? (sidebarOpen = true, localStorage.setItem('skin.sidebarOpen','1'), open = true) : (open = !open)"
                        class="w-full flex items-center p-2 rounded-xl transition-all {{ request()->routeIs('informasi.*', 'rapat.*') ? 'bg-[#132342] text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-[#132342]/70' }}"
                        :title="!sidebarOpen ? 'Informasi & Agenda' : null">
                    <svg class="w-5 h-5 min-w-[20px] {{ request()->routeIs('informasi.*', 'rapat.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5h12v14H6a2 2 0 01-2-2V5zM16 8h3v9a2 2 0 01-2 2M7 8h6M7 11h6M7 14h4" /></svg>
                    <span x-show="sidebarOpen" class="ml-3 text-sm truncate">Informasi &amp; Agenda</span>
                    <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="ml-auto w-4 h-4 transition-transform text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" class="pl-9 space-y-1 mt-1 border-l border-[#1E2D4A] ml-4">
                    <a href="{{ route('informasi.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('informasi.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Pusat Info &amp; Berita</a>
                    <a href="{{ route('rapat.index') }}" class="block p-1.5 text-xs rounded-lg transition-colors {{ request()->routeIs('rapat.*') ? 'text-amber-400 font-bold bg-[#1E3A8A]/50' : 'text-slate-400 hover:text-white hover:bg-[#132342]' }}">Jadwal Rapat &amp; Koordinasi</a>
                </div>
            </div>
        </div>

        @endif

        {{-- ========================================================
             KARTU PENDAMPING SI UJANG ("BACA PANDUAN") - OPSI 4
             ======================================================== --}}
        <div x-show="sidebarOpen" class="relative z-10 pt-3 pb-2">
            <div class="bg-[#12203A] border border-[#263B66] rounded-2xl p-3.5 shadow-md flex flex-col gap-2.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-full bg-[#1E3A8A] border-2 border-amber-400 p-0.5 flex items-center justify-center shrink-0 shadow-sm">
                        <img src="{{ asset('images/maskot-itg-head.png') }}" class="w-full h-full object-contain rounded-full" alt="Si Ujang">
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-bold text-white">Si Ujang</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-amber-400 text-slate-900">BANTUAN</span>
                        </div>
                        <p class="text-[11px] text-slate-400 leading-tight truncate">Ada kendala alur proposal?</p>
                    </div>
                </div>
                <a href="{{ route('informasi.index') }}"
                   class="w-full py-2 px-3 rounded-xl bg-[#1E3A8A] hover:bg-blue-800 text-white font-bold text-xs flex items-center justify-center gap-2 transition shadow-sm">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span>Baca Panduan &rarr;</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ========================================================
         DOCK TOMBOL SI UJANG (MODE COLLAPSED / SLIDE - VARIASI B)
         ======================================================== --}}
    <div x-show="!sidebarOpen"
         x-data="{ openFlyout: false }"
         @mouseenter="openFlyout = true"
         @mouseleave="openFlyout = false"
         class="shrink-0 relative z-30 p-2 border-t border-[#1E2D4A]/70 flex flex-col items-center justify-center bg-[#0B1528]">
        
        {{-- Tombol Circular Si Ujang --}}
        <a href="{{ route('informasi.index') }}"
           class="group/ujang relative flex flex-col items-center justify-center p-1 rounded-2xl transition-all duration-200 hover:bg-[#132342] focus:outline-none focus:ring-2 focus:ring-amber-400"
           title="Si Ujang ITG - Baca Panduan & Regulasi Ormawa">
            
            <div class="relative w-11 h-11 rounded-full bg-[#1E3A8A] border-2 border-amber-400 p-0.5 flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover/ujang:border-amber-300 group-hover/ujang:shadow-amber-400/40 group-hover/ujang:scale-105 transition-all">
                <img src="{{ asset('images/maskot-itg-head.png') }}" class="w-full h-full object-contain rounded-full" alt="Si Ujang">
                {{-- Ping dot indicator --}}
                <span class="absolute -top-0.5 -right-0.5 flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-400 border-2 border-[#0B1528]"></span>
                </span>
            </div>
            
            <span class="text-[9px] font-bold text-amber-400 mt-1 tracking-wider uppercase group-hover/ujang:text-amber-300">Panduan</span>
        </a>

        {{-- Floating Popover Card (Flyout ke samping kanan saat di-hover) --}}
        <div x-show="openFlyout"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-2 scale-95"
             x-transition:enter-end="opacity-100 translate-x-0 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-x-0 scale-100"
             x-transition:leave-end="opacity-0 translate-x-2 scale-95"
             class="absolute left-full ml-3 bottom-0 w-72 bg-[#0E1A32] border border-[#2A4374] rounded-2xl p-4 shadow-2xl shadow-black/80 z-50 pointer-events-auto"
             style="display: none;">
            
            {{-- Panah Pointer Segitiga --}}
            <div class="absolute -left-2 bottom-6 w-4 h-4 bg-[#0E1A32] border-l border-b border-[#2A4374] rotate-45"></div>

            {{-- Header Popover --}}
            <div class="relative flex items-center gap-2.5 pb-2.5 border-b border-[#1E2D4A]">
                <div class="w-9 h-9 rounded-full bg-[#1E3A8A] border-2 border-amber-400 p-0.5 flex items-center justify-center shrink-0 shadow-sm">
                    <img src="{{ asset('images/maskot-itg-head.png') }}" class="w-full h-full object-contain rounded-full" alt="Si Ujang">
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-white truncate">Si Ujang ITG</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-amber-400 text-slate-900 shrink-0">BANTUAN</span>
                    </div>
                    <p class="text-[10px] text-slate-400 truncate">Asisten Panduan Ormawa</p>
                </div>
            </div>

            {{-- Body Popover --}}
            <div class="relative py-2.5">
                <p class="text-[11px] text-slate-300 leading-relaxed">
                    Ada kendala alur verifikasi proposal, pelaporan LPJ, atau peminjaman sarpras ITG?
                </p>
            </div>

            {{-- CTA Button & Quick Links --}}
            <div class="relative space-y-2">
                <a href="{{ route('informasi.index') }}"
                   class="w-full py-2 px-3 rounded-xl bg-[#1E3A8A] hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-2 transition shadow-sm border border-blue-500/40">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span>Baca Panduan Ormawa &rarr;</span>
                </a>

                <div class="flex items-center justify-between text-[10px] text-slate-400 pt-1">
                    <a href="{{ route('informasi.index') }}" class="hover:text-amber-400 transition">&bull; Regulasi SOP</a>
                    <a href="{{ route('informasi.index') }}" class="hover:text-amber-400 transition">&bull; Format LPJ</a>
                    <a href="{{ route('informasi.index') }}" class="hover:text-amber-400 transition">&bull; FAQ Kampus</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer Profil Pengguna --}}
    <div class="shrink-0 p-3 border-t border-[#1E2D4A] relative z-10 bg-[#0B1528]">
        <div class="flex items-center justify-between p-1.5 rounded-xl hover:bg-[#132342] transition">
            <a href="{{ route('profile.edit') }}" class="flex items-center min-w-0 gap-2.5">
                <div class="w-8 h-8 rounded-full bg-[#1E40AF] border border-blue-400/40 text-white font-bold text-xs flex items-center justify-center shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
                <div x-show="sidebarOpen" class="min-w-0">
                    <span class="block text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'User' }}</span>
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0"></span>
                        <span class="text-[10px] text-slate-400 capitalize truncate">{{ auth()->user()->role ?? 'Pengurus' }}</span>
                    </div>
                </div>
            </a>
            <a x-show="sidebarOpen" href="{{ route('profile.edit') }}" title="Pengaturan Profil" class="text-slate-400 hover:text-white p-1 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </a>
        </div>
    </div>
</nav>
