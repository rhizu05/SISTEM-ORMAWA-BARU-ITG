<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-900 leading-tight">
                    {{ __('Pusat Notifikasi') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Pemberitahuan aktivitas, status proposal, dan jadwal.</p>
            </div>
            
            <form action="{{ route('notifikasi.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 text-sm font-semibold rounded-xl hover:bg-blue-100 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Tandai semua dibaca
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12 bg-slate-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-[0_4px_16px_rgba(0,0,0,0.04)] border border-slate-200 sm:rounded-2xl divide-y divide-slate-100">
                @forelse($notifikasis as $n)
                    @php
                        $pesan = strtolower($n->pesan);
                        if (str_contains($pesan, 'setuju') || str_contains($pesan, 'berhasil')) {
                            $iconColor = 'text-emerald-600';
                            $bgColor = 'bg-emerald-50';
                            $svg = '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                        } elseif (str_contains($pesan, 'tolak') || str_contains($pesan, 'batal')) {
                            $iconColor = 'text-rose-600';
                            $bgColor = 'bg-rose-50';
                            $svg = '<path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                        } elseif (str_contains($pesan, 'peringatan') || str_contains($pesan, 'telat') || str_contains($pesan, 'belum')) {
                            $iconColor = 'text-amber-600';
                            $bgColor = 'bg-amber-50';
                            $svg = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                        } else {
                            $iconColor = 'text-blue-600';
                            $bgColor = 'bg-blue-50';
                            $svg = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />';
                        }
                        $isUnread = $n->status_baca === 'belum';
                    @endphp

                    <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-start justify-between gap-4 transition hover:bg-slate-50/80 {{ $isUnread ? 'bg-blue-50/20' : '' }}">
                        <div class="flex items-start gap-4 flex-1">
                            
                            {{-- Icon Wrap --}}
                            <div class="relative shrink-0">
                                <div class="w-12 h-12 rounded-full {{ $bgColor }} {{ $iconColor }} flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        {!! $svg !!}
                                    </svg>
                                </div>
                                {{-- Unread Dot Indicator --}}
                                @if($isUnread)
                                    <span class="absolute top-0 right-0 block w-3 h-3 rounded-full bg-rose-500 ring-2 ring-white"></span>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="pt-1">
                                <p class="text-sm {{ $isUnread ? 'text-slate-900 font-bold' : 'text-slate-700' }}">
                                    {{ $n->pesan }}
                                </p>
                                <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $n->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        {{-- Action Buttons & Status --}}
                        <div class="pl-16 sm:pl-0 shrink-0 flex items-center justify-end sm:min-w-[120px]">
                            @if($isUnread)
                                <form action="{{ route('notifikasi.read', $n) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-blue-600 transition shadow-sm whitespace-nowrap focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        Tandai dibaca
                                    </button>
                                </form>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Telah dibaca
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mb-4">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Semua Bersih!</h3>
                        <p class="text-sm text-slate-500 mt-1 max-w-sm">Belum ada notifikasi baru untuk Anda saat ini.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-6">
                {{ $notifikasis->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
