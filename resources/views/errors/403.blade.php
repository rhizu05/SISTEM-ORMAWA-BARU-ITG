@extends('errors.layout')

@section('title', '403 Akses Ditolak')
@section('code', 'Error 403')

@section('icon-bg', 'bg-amber-50 text-amber-600')

@section('icon')
<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
</svg>
@endsection

@section('headline', 'Akses Tidak Diizinkan')

@section('message')
{{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses atau wewenang untuk membuka halaman ini. Silakan periksa kembali akun yang Anda gunakan atau kembali ke dashboard.' }}
@endsection

@section('actions')
@auth
    <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-xs transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Kembali ke Dashboard
    </a>
@else
    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-xs transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        Masuk Akun
    </a>
@endauth
<button onclick="history.back()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl border border-slate-300 transition-colors focus:ring-2 focus:ring-slate-400">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Kembali Sebelumnya
</button>
@endsection
