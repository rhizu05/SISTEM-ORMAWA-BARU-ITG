<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('informasi.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                    &larr; Pusat Informasi
                </a>
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    Kurasi Berita & Pengumuman HIMA/UKM
                </h2>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                Panel Kurator BEM
            </span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-base">Antrean Pengajuan Publikasi Berita</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Periksa keabsahan pamflet dan isi berita kegiatan dari Himpunan dan UKM sebelum diterbitkan ke halaman publik mahasiswa.</p>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($pengumumans as $p)
                        <div class="p-6 space-y-4 hover:bg-slate-50/50 transition">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        {{ $p->user->name ?? 'Ormawa' }}
                                    </span>
                                    <span class="text-xs text-slate-400">
                                        Diajukan: {{ $p->created_at->translatedFormat('d M Y, H:i') }}
                                    </span>
                                    @if ($p->tanggal_kegiatan)
                                        <span class="text-xs text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                            📅 Pelaksanaan: {{ $p->tanggal_kegiatan->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    Menunggu Review BEM
                                </span>
                            </div>

                            @if ($p->gambar_sampul)
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <a href="{{ $p->gambar_url }}" target="_blank" class="shrink-0 w-full sm:w-44 h-44 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 block group relative shadow-2xs">
                                        <img src="{{ $p->gambar_url }}" alt="{{ $p->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                        <span class="absolute inset-0 bg-slate-900/30 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold backdrop-blur-2xs">
                                            Buka Poster &nearr;
                                        </span>
                                    </a>
                                    <div class="flex-1 space-y-2 w-full">
                                        <h4 class="font-bold text-slate-900 text-lg leading-snug">{{ $p->judul }}</h4>
                                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                                            {{ $p->isi }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-2">
                                    <h4 class="font-bold text-slate-900 text-lg leading-snug">{{ $p->judul }}</h4>
                                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                                        {{ $p->isi }}
                                    </div>
                                </div>
                            @endif

                            <div class="flex flex-wrap items-center justify-between gap-4 pt-2 border-t border-slate-100">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('informasi.show', $p) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Pratinjau Tampilan Berita
                                    </a>

                                    @if ($p->file_lampiran)
                                        <a href="{{ route('informasi.pengumuman.lampiran', $p) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            Dokumen PDF
                                        </a>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="openRejectModal('{{ $p->id }}', '{{ addslashes($p->judul) }}')"
                                        class="px-4 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 border border-rose-200 hover:bg-rose-100 transition">
                                        Tolak Berita
                                    </button>
                                    <form action="{{ route('bem.kurasi.approve', $p) }}" method="POST">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Setujui dan publikasikan berita ini ke mahasiswa?')"
                                            class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm flex items-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Setujui & Publikasikan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center text-slate-400 text-sm">
                            Tidak ada pengajuan berita dari HIMA/UKM yang sedang menunggu kurasi.
                        </div>
                    @endforelse
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $pengumumans->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tolak Kurasi -->
    <div id="modal-reject" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" onclick="closeRejectModal()"></div>
            <div class="bg-white rounded-2xl shadow-xl z-10 max-w-lg w-full p-6 relative border border-slate-200">
                <h3 class="text-base font-bold text-slate-900 mb-1">Tolak Pengajuan Berita</h3>
                <p class="text-xs text-slate-500 mb-4" id="reject-judul-label">Judul: -</p>
                <form id="form-reject" method="POST" action="">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="catatan_kurasi" :value="__('Alasan Penolakan / Catatan untuk Ormawa *')" />
                            <textarea name="catatan_kurasi" id="catatan_kurasi" rows="4" required class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm text-sm focus:border-rose-500 focus:ring-rose-500" placeholder="Jelaskan alasan penolakan atau revisi yang perlu dilakukan oleh HIMA/UKM..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end mt-6 gap-2">
                        <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-slate-100 rounded-xl transition">Batal</button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition">Kirim Penolakan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRejectModal(id, judul) {
            document.getElementById('modal-reject').style.display = 'block';
            document.getElementById('form-reject').action = '/bem/kurasi-pengumuman/' + id + '/reject';
            document.getElementById('reject-judul-label').textContent = 'Berita: ' + judul;
        }
        function closeRejectModal() {
            document.getElementById('modal-reject').style.display = 'none';
        }
    </script>
</x-app-layout>
