<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <a href="{{ route('informasi.index') }}" class="hover:text-indigo-600 transition flex items-center gap-1 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Pusat Informasi</span>
                </a>
                <span>/</span>
                <span class="text-slate-800 font-semibold truncate max-w-xs sm:max-w-sm">{{ $pengumuman->judul }}</span>
            </div>
            <a href="{{ route('informasi.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-2xs transition">
                &larr; Kembali ke Pusat Informasi
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ imageModal: false, copied: false }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Status Banner jika masih draf/pending bagi pengurus --}}
            @if($pengumuman->status !== 'published')
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <strong class="font-bold block">Mode Pratinjau Draf (Belum Terbit)</strong>
                        <span>Status berita ini adalah <strong>{{ $pengumuman->status_label }}</strong>. Informasi ini belum dapat diakses oleh publik sebelum disetujui kurator BEM.</span>
                    </div>
                </div>
            @endif

            {{-- Artikel Utama --}}
            <article class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                {{-- Header Artikel --}}
                <div class="p-6 sm:p-8 space-y-4 border-b border-slate-100">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $pengumuman->badge_color }}">
                            {{ $pengumuman->badge_label }}
                        </span>
                        @if($pengumuman->tanggal_kegiatan)
                            <span class="inline-flex items-center gap-1 text-xs text-indigo-700 bg-indigo-50 border border-indigo-100 px-3 py-1 rounded-full font-medium">
                                📅 Pelaksanaan: {{ $pengumuman->tanggal_kegiatan->format('d F Y') }}
                            </span>
                        @endif
                        <span class="text-xs text-slate-400">
                            Terbit: {{ $pengumuman->created_at->format('d M Y, H:i') }} WIB
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        {{ $pengumuman->judul }}
                    </h1>

                    <div class="flex items-center justify-between pt-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-600 to-violet-500 text-white font-bold flex items-center justify-center text-sm shadow-sm">
                                {{ strtoupper(substr($pengumuman->user->name ?? 'K', 0, 2)) }}
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 block">Diterbitkan oleh</span>
                                <span class="text-sm font-bold text-slate-800">{{ $pengumuman->user->name ?? 'Biro Kemahasiswaan' }}</span>
                            </div>
                        </div>

                        @if(Auth::check() && (Auth::user()->hasAnyRole(['bem', 'bkhm', 'admin']) || Auth::id() === $pengumuman->user_id))
                            <form action="{{ route('informasi.pengumuman.destroy', $pengumuman) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition">
                                    Hapus Berita
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Gambar Poster / Sampul Hero --}}
                @if($pengumuman->gambar_sampul)
                    <div class="bg-slate-900/5 p-4 sm:p-6 flex justify-center border-b border-slate-100">
                        <div class="relative group cursor-pointer max-w-2xl w-full" @click="imageModal = true">
                            <img src="{{ $pengumuman->gambar_url }}" alt="{{ $pengumuman->judul }}" class="rounded-xl w-full max-h-[500px] object-contain mx-auto shadow-md border border-slate-200/60 transition group-hover:opacity-95">
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition bg-slate-900/30 rounded-xl">
                                <span class="px-4 py-2 rounded-xl bg-white/90 text-slate-800 text-xs font-bold shadow-lg backdrop-blur-sm flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                                    Klik untuk memperbesar gambar
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Narasi Isi Berita --}}
                <div class="p-6 sm:p-8">
                    <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed sm:text-base text-sm whitespace-pre-line space-y-4">
                        {{ $pengumuman->isi }}
                    </div>

                    {{-- Lampiran Dokumen PDF jika ada --}}
                    @if($pengumuman->file_lampiran)
                        <div class="mt-8 p-4 rounded-xl bg-indigo-50/70 border border-indigo-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">Dokumen Lampiran / Panduan</h4>
                                    <p class="text-xs text-slate-500">Berkas pendukung resmi terkait informasi ini.</p>
                                </div>
                            </div>
                            <a href="{{ route('informasi.pengumuman.lampiran', $pengumuman) }}" target="_blank" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition flex items-center justify-center gap-1.5 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Unduh Lampiran</span>
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Action / Share Bar --}}
                <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                        <span>Bagikan berita ini:</span>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($pengumuman->judul . ' - Baca selengkapnya di: ' . url()->current()) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 font-bold transition flex items-center gap-1">
                            <span>WhatsApp</span>
                        </a>
                        <button type="button" @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2500)" class="px-3 py-1.5 rounded-lg bg-white text-slate-700 hover:bg-slate-100 border border-slate-300 font-bold transition flex items-center gap-1">
                            <span x-text="copied ? '✓ Tersalin!' : 'Salin Tautan'"></span>
                        </button>
                    </div>

                    <a href="{{ route('informasi.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                        &larr; Lihat Semua Pengumuman
                    </a>
                </div>
            </article>

            {{-- Berita Lainnya --}}
            @if($beritaTerkait->count() > 0)
                <div class="space-y-4 pt-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-900">Berita & Informasi Lainnya</h3>
                        <a href="{{ route('informasi.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">Lihat Semua &rarr;</a>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($beritaTerkait as $b)
                            <a href="{{ route('informasi.show', $b) }}" class="p-4 rounded-xl bg-white border border-slate-200/80 hover:border-indigo-300 hover:shadow-sm transition flex gap-3 group">
                                @if($b->gambar_sampul)
                                    <div class="w-20 h-20 rounded-lg overflow-hidden bg-slate-100 shrink-0">
                                        <img src="{{ $b->gambar_url }}" alt="{{ $b->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    </div>
                                @endif
                                <div class="space-y-1 overflow-hidden">
                                    <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded {{ $b->badge_color }}">
                                        {{ $b->badge_label }}
                                    </span>
                                    <h4 class="text-xs font-bold text-slate-800 line-clamp-2 leading-snug group-hover:text-indigo-600 transition">
                                        {{ $b->judul }}
                                    </h4>
                                    <span class="text-[10px] text-slate-400 block">
                                        {{ $b->created_at->format('d M Y') }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- Modal Lightbox Gambar Penuh --}}
        @if($pengumuman->gambar_sampul)
            <div x-show="imageModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" style="display: none;" @click="imageModal = false" @keydown.escape.window="imageModal = false">
                <div class="relative max-w-4xl max-h-[90vh] p-2 bg-white rounded-2xl shadow-2xl overflow-hidden" @click.stop>
                    <div class="flex items-center justify-between p-3 border-b border-slate-100">
                        <span class="text-xs font-bold text-slate-700 truncate max-w-md">{{ $pengumuman->judul }}</span>
                        <button @click="imageModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold px-2">&times;</button>
                    </div>
                    <div class="p-2 overflow-auto max-h-[80vh] flex justify-center">
                        <img src="{{ $pengumuman->gambar_url }}" alt="{{ $pengumuman->judul }}" class="max-h-[75vh] w-auto object-contain rounded-lg">
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>