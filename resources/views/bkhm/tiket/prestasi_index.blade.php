<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800 leading-tight">
                Verifikasi Prestasi & Delegasi Kompetisi
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('prestasi.showcase') }}" target="_blank" class="px-3 py-1.5 rounded-xl text-xs font-bold text-amber-800 bg-amber-100 hover:bg-amber-200 transition">
                    🏆 Buka Showcase Publik &rarr;
                </a>
            </div>
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
                    <h3 class="font-bold text-slate-800 text-base">Daftar Pengajuan Prestasi & Permohonan Bantuan Lomba</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Verifikasi keaslian sertifikat untuk dipublikasikan ke Showcase atau tinjau proposal bantuan dana delegasi lomba.</p>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse ($tikets as $tiket)
                        <div class="p-6 space-y-4 hover:bg-slate-50/50 transition border-l-4 {{ $tiket->sub_kategori === 'lapor_prestasi' ? 'border-l-amber-500' : 'border-l-indigo-600' }}">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="font-mono font-bold text-xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                                        {{ $tiket->kode_tiket }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $tiket->sub_kategori === 'lapor_prestasi' ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-indigo-100 text-indigo-800 border-indigo-300' }}">
                                        {{ $tiket->sub_kategori === 'lapor_prestasi' ? '🏆 Lapor Prestasi' : '💸 Pengajuan Delegasi' }}
                                    </span>
                                    @if ($tiket->tampil_ke_publik)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            ✓ Tampil di Showcase
                                        </span>
                                    @endif
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $tiket->status_color }}">
                                    {{ $tiket->status_label }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2 space-y-2">
                                    <h4 class="font-bold text-slate-900 text-base leading-snug">{{ $tiket->nama_kegiatan }}</h4>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Penyelenggara</span>
                                            @if ($tiket->url_penyelenggara)
                                                <a href="{{ $tiket->url_penyelenggara }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-indigo-600 hover:underline inline-flex items-center gap-1" title="Buka website penyelenggara">
                                                    <span class="truncate max-w-[130px]">{{ $tiket->penyelenggara }}</span>
                                                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                            @else
                                                <span class="font-semibold text-slate-800">{{ $tiket->penyelenggara }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Tingkat</span>
                                            <span class="font-semibold text-slate-800">{{ $tiket->tingkat }}</span>
                                        </div>
                                        @if ($tiket->sub_kategori === 'lapor_prestasi')
                                            <div>
                                                <span class="text-slate-400 block mb-0.5">Capaian Juara</span>
                                                <span class="font-bold text-amber-700">{{ $tiket->capaian ?? '-' }}</span>
                                            </div>
                                        @else
                                            <div>
                                                <span class="text-slate-400 block mb-0.5">Bantuan Dana</span>
                                                <span class="font-bold text-indigo-700">{{ $tiket->estimasi_biaya ? 'Rp ' . number_format($tiket->estimasi_biaya, 0, ',', '.') : '-' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-slate-500 pt-1">
                                        Pengaju: <span class="font-semibold text-slate-700">{{ $tiket->nama_mahasiswa }}</span> ({{ $tiket->nim }} &bull; {{ $tiket->prodi ?? 'ITG' }})
                                        &bull; Email: <a href="mailto:{{ $tiket->email }}" class="text-indigo-600 hover:underline">{{ $tiket->email }}</a>
                                        &bull; HP: {{ $tiket->no_hp ?? '-' }}
                                    </div>
                                </div>

                                <div class="flex flex-col justify-between items-start md:items-end gap-2 border-t md:border-t-0 md:border-l border-slate-100 pt-3 md:pt-0 md:pl-4">
                                    <div class="text-xs text-slate-500">
                                        📅 <span class="font-medium text-slate-700">{{ $tiket->rentang_tanggal }}</span>
                                    </div>
                                    <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                                        @if ($tiket->lampiran_bukti)
                                            <a href="{{ route('layanan.lampiran', $tiket) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition border {{ $tiket->sub_kategori === 'lapor_prestasi' ? 'text-amber-800 bg-amber-50 border-amber-200 hover:bg-amber-100' : 'text-indigo-700 bg-indigo-50 border-indigo-200 hover:bg-indigo-100' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                {{ $tiket->sub_kategori === 'lapor_prestasi' ? 'Scan Sertifikat' : 'Proposal / Undangan' }}
                                            </a>
                                        @endif
                                        @if ($tiket->foto_penyerahan)
                                            <a href="{{ asset('storage/' . $tiket->foto_penyerahan) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                Foto Penyerahan
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Form Verifikasi BKHM -->
                            <form action="{{ route('bkhm.tiket-prestasi.update', $tiket) }}" method="POST" class="pt-4 border-t border-slate-100 bg-slate-50/70 p-4 rounded-xl space-y-3">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Keputusan Status <span class="text-rose-500">*</span></label>
                                        <select name="status" class="w-full text-xs rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2 px-3">
                                            <option value="diverifikasi_bkhm" {{ $tiket->status === 'diverifikasi_bkhm' ? 'selected' : '' }}>Diverifikasi BKHM</option>
                                            <option value="disetujui" {{ $tiket->status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                            <option value="ditolak" {{ $tiket->status === 'ditolak' ? 'selected' : '' }}>Ditolak / Tidak Memenuhi Syarat</option>
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-[11px] font-semibold text-slate-600 uppercase mb-1">Catatan / Keterangan untuk Mahasiswa</label>
                                        <input type="text" name="catatan_bkhm" value="{{ old('catatan_bkhm', $tiket->catatan_bkhm) }}" placeholder="Keterangan persetujuan atau alasan penolakan..."
                                            class="w-full text-xs rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm py-2 px-3">
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                                    @if ($tiket->sub_kategori === 'lapor_prestasi')
                                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                                            <input type="checkbox" name="tampil_ke_publik" value="1" {{ old('tampil_ke_publik', $tiket->tampil_ke_publik) ? 'checked' : '' }}
                                                class="w-4 h-4 rounded text-amber-600 border-slate-300 focus:ring-amber-500">
                                            <span>Tampilkan ke Halaman Publik (Showcase Prestasi Kampus)</span>
                                        </label>
                                    @else
                                        <div class="text-[11px] text-slate-400 italic">Pengajuan bantuan delegasi dikelola internal BKHM.</div>
                                    @endif

                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 transition shadow-sm flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Simpan Keputusan & Notifikasi Mahasiswa
                                    </button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="p-10 text-center text-slate-400 text-sm">
                            Belum ada pengajuan prestasi atau bantuan lomba masuk.
                        </div>
                    @endforelse
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $tikets->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
