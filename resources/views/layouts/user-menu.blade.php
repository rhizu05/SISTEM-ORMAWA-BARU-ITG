@auth
@php
    $avatarUser = Auth::user();
    $avatarInitial = strtoupper(substr($avatarUser->name ?? $avatarUser->username ?? 'U', 0, 1));
    $rawRole = $avatarUser->getRoleNames()->first();
    $roleMap = [
        'admin' => 'Administrator Sistem',
        'bkhm' => 'Verifikator Utama BKHM',
        'sarpras' => 'Verifikator Sarpras',
        'wr3' => 'Wakil Rektor III',
        'bem' => 'Pengurus BEM ITG',
        'bpm' => 'Pengurus BPM ITG',
        'ormawa' => 'Pengurus Ormawa',
        'bendahara' => 'Bendahara Kampus',
    ];
    $userRoleTitle = $roleMap[$rawRole] ?? (ucwords(str_replace('_', ' ', $rawRole ?? 'Pengguna Sistem')));
    $unreadNotifCount = \App\Models\Notifikasi::where('user_id', $avatarUser->id)->where('status_baca', 'belum')->count();
@endphp
<div class="flex items-center gap-2 sm:gap-2.5">
    
    <!-- Quick Link: Portal Publik -->
    <a href="{{ url('/') }}" target="_blank" 
       title="Buka portal layanan mahasiswa publik di tab baru"
       class="hidden md:inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 hover:text-blue-700 border border-slate-200/90 rounded-xl transition shadow-xs shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
        </svg>
        <span>Portal Publik</span>
    </a>

    <!-- Notification Bell -->
    <a href="{{ route('notifikasi.index') }}" 
       title="Pusat Notifikasi Sistem ({{ $unreadNotifCount }} belum dibaca)"
       class="relative p-2 sm:p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/90 text-slate-600 hover:text-slate-900 transition shadow-xs shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if ($unreadNotifCount > 0)
            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[9px] font-extrabold flex items-center justify-center ring-2 ring-white shadow-xs">
                {{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}
            </span>
        @endif
    </a>

    <!-- Tombol Pintas Universal: Lapor Bug / Kendala Sistem -->
    <a href="{{ route('bug.create', ['url' => url()->current()]) }}" 
       title="Laporkan kendala, bug, atau error sistem ke BKHM" 
       class="inline-flex items-center gap-1.5 p-2 sm:px-3 sm:py-2 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/90 rounded-xl transition shadow-xs shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500">
        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <span class="hidden sm:inline">Lapor Bug</span>
    </a>

    <!-- Divider Pemisah -->
    <div class="h-7 w-px bg-slate-200 hidden sm:block mx-1 shrink-0"></div>

    <!-- User Profile Dropdown Chip -->
    <div class="relative">
        <x-dropdown align="right" width="w-64">
            <x-slot name="trigger">
                <button class="flex items-center gap-1.5 sm:gap-2 p-1 pl-1 sm:pl-1.5 pr-1.5 sm:pr-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/90 text-left transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 shadow-xs group" aria-label="Menu pengguna">
                    @if ($avatarUser->foto_profil)
                        <img class="h-8 w-8 rounded-lg object-cover border border-slate-200 shrink-0" src="{{ asset('storage/'.$avatarUser->foto_profil) }}" alt="Foto profil {{ $avatarUser->name ?? 'User' }}">
                    @else
                        <span class="h-8 w-8 rounded-lg bg-[#1E40AF] text-white text-xs font-extrabold flex items-center justify-center shadow-xs shrink-0">{{ $avatarInitial }}</span>
                    @endif
                    <div class="hidden lg:block min-w-0 pr-1 text-left">
                        <span class="block text-xs font-extrabold text-slate-900 leading-tight truncate max-w-[130px]">{{ $avatarUser->name ?? $avatarUser->username ?? 'User' }}</span>
                        <span class="block text-[10px] font-semibold text-slate-500 leading-none mt-0.5 truncate max-w-[130px]">{{ $userRoleTitle }}</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition ml-auto shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                </button>
            </x-slot>
            <x-slot name="content">
                {{-- Header info di dalam popover dropdown --}}
                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/60 rounded-t-md">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ $avatarUser->name ?? 'Pengguna' }}</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ $avatarUser->email }}</p>
                    <div class="mt-1.5 inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-bold">
                        {{ $userRoleTitle }}
                    </div>
                </div>

                <div class="py-1">
                    <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Pengaturan Profil</span>
                    </x-dropdown-link>
                    <x-dropdown-link :href="route('notifikasi.index')" class="flex items-center gap-2 text-xs">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span>Pusat Notifikasi</span>
                        @if ($unreadNotifCount > 0)
                            <span class="ml-auto px-1.5 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[10px] font-bold">{{ $unreadNotifCount }}</span>
                        @endif
                    </x-dropdown-link>
                    <x-dropdown-link :href="route('bug.create', ['url' => url()->current()])" class="flex items-center gap-2 text-xs text-rose-600 hover:text-rose-700">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Laporkan Kendala / Bug</span>
                    </x-dropdown-link>
                </div>

                <div class="border-t border-slate-100 py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center gap-2 text-xs text-rose-600 hover:text-rose-700 hover:bg-rose-50">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Keluar dari Sistem</span>
                        </x-dropdown-link>
                    </form>
                </div>
            </x-slot>
        </x-dropdown>
    </div>
</div>
@endauth
@guest
<div class="flex items-center space-x-2">
    <a href="{{ route('login') }}" class="text-sm font-bold text-blue-700 hover:text-blue-900 px-3.5 py-2 rounded-xl bg-blue-50 border border-blue-200">Masuk Pengurus</a>
</div>
@endguest
