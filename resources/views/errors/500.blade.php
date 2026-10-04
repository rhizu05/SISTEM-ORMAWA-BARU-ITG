@extends('errors.layout')

@section('title', '500 Terjadi Kendala Server')
@section('code', 'Error 500')

@section('icon-bg', 'bg-rose-50 text-rose-600')

@section('icon')
<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>
@endsection

@section('headline', 'Terjadi Kendala pada Server')

@section('message')
Sistem sedang mengalami sedikit gangguan teknis tak terduga. Tim administrator telah diberitahu mengenai insiden ini. Silakan coba beberapa saat lagi atau hubungi layanan bantuan ITG.
@endsection

@section('actions')
@auth
    <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-xs transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Kembali ke Dashboard
    </a>
@else
    <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-xs transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        Halaman Utama
    </a>
@endauth
<a href="{{ route('bug.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl border border-slate-300 transition-colors focus:ring-2 focus:ring-slate-400">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
    Lapor Kendala Teknis
</a>
@endsection
