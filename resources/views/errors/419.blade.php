@extends('errors.layout')

@section('title', '419 Sesi Kedaluwarsa')
@section('code', 'Status 419')

@section('icon-bg', 'bg-orange-50 text-orange-600')

@section('icon')
<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
</svg>
@endsection

@section('headline', 'Sesi Halaman Berakhir')

@section('message')
Halaman ini sudah tidak aktif cukup lama sehingga token keamanan formulir telah kedaluwarsa. Silakan muat ulang halaman untuk melanjutkan pengisian atau masuk kembali.
@endsection

@section('actions')
<button onclick="window.location.reload()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-xs transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
    Muat Ulang Halaman
</button>
<a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl border border-slate-300 transition-colors focus:ring-2 focus:ring-slate-400">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
    Halaman Masuk (Login)
</a>
@endsection
