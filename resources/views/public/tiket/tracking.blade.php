<x-public-layout title="Cek Status Tiket" brand-label="Portal Layanan Mahasiswa" accent="indigo">
    <x-slot name="nav">
        <a href="{{ route('layanan.index') }}" class="inline-flex items-center gap-1.5 min-h-[44px] px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            <span>Portal Layanan</span>
        </a>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
        <nav class="flex items-center gap-2 text-xs text-slate-600 mb-3">
            <a href="{{ route('layanan.index') }}" class="hover:text-indigo-700 transition">Portal Layanan</a>
            <span class="text-slate-400">/</span>
            <span class="text-slate-800 font-semibold">Pelacakan Status Tiket</span>
        </nav>

        <div class="text-center sm:text-left mb-8">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Pelacakan Status Tiket Layanan</h1>
            <p class="text-sm text-slate-600 mt-2">Masukkan Kode Tiket yang Anda peroleh dan email yang digunakan saat pengajuan.</p>
        </div>

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-950 rounded-2xl p-5 mb-6 text-sm flex flex-col sm:flex-row items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1 space-y-1">
                    <strong class="font-bold text-base block text-emerald-950">Permohonan Berhasil Dikirim</strong>
                    <p class="text-xs sm:text-sm text-emerald-800 leading-relaxed">{{ session('success') }}</p>
                    <div class="pt-2 text-xs font-semibold text-emerald-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Tips: Simpan atau salin kode tiket di bawah untuk memantau perkembangan tindak lanjut.</span>
                    </div>
                </div>
            </div>
        @endif

        @php
            $displayError = $errorMessage ?? session('errorMessage');
        @endphp

        @if (!empty($displayError))
            <div class="bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl p-4 mb-6 text-sm flex gap-3">
                <svg class="w-5 h-5 flex-shrink-0 text-rose-600 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>{{ $displayError }}</div>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 mb-8">
            <form action="{{ route('layanan.tracking.verify') }}" method="POST" class="space-y-4" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="kode" class="block text-sm font-semibold text-slate-700 mb-1.5">Kode Tiket <span class="text-rose-600">*</span></label>
                        <input type="text" name="kode" id="kode" value="{{ old('kode', request('kode')) }}" required placeholder="Contoh: SKIN-TKT-2026-X8K9M2"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-3.5 font-mono uppercase placeholder-slate-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email Pengaju <span class="text-rose-600">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', request('email')) }}" required placeholder="email@contoh.com"
                            class="w-full text-sm rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-3.5 placeholder-slate-500">
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:justify-end pt-2">
                    <button type="submit" :disabled="submitting" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 min-h-[48px] px-6 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span x-text="submitting ? 'Memverifikasi...' : 'Lacak Status Tiket'">Lacak Status Tiket</span>
                    </button>
                </div>
            </form>
        </div>

        @if ($tiket)
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden mb-8" x-data="{ copied: false }">
                <div class="p-5 sm:p-6 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <span class="text-xs text-slate-600 font-mono block">Kode Tiket Resmi</span>
                        <div class="flex items-center gap-3 mt-1 flex-wrap">
                            <div class="text-xl sm:text-2xl font-black font-mono text-slate-900 tracking-tight select-all">{{ $tiket->kode_tiket }}</div>
                            <button type="button" @click="navigator.clipboard.writeText('{{ $tiket->kode_tiket }}'); copied = true; setTimeout(() => copied = false, 2500)"
                                class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-1.5 rounded-xl text-xs font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                                :class="copied ? 'bg-emerald-600 text-white' : 'bg-white text-indigo-700 hover:bg-indigo-50 border border-indigo-200 shadow-sm'">
                                <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <svg x-show="copied" style="display: none;" class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="copied ? 'Kode Tersalin!' : 'Salin Kode Tiket'">Salin Kode Tiket</span>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        @if ($tiket->kategori === 'aspirasi')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-800 border border-indigo-200">
                                Aspirasi
                            </span>
                        @elseif ($tiket->kategori === 'konseling')
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                Konseling (Rahasia)
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                {{ $tiket->sub_kategori === 'lapor_prestasi' ? 'Prestasi Mahasiswa' : 'Bantuan Lomba' }}
                            </span>
                        @endif

                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $tiket->status_color }}">
                            {{ $tiket->status_label }}
                        </span>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pb-6 border-b border-slate-100 text-xs">
                        <div>
                            <span class="text-slate-600 block mb-1">NIM</span>
                            <span class="font-bold text-slate-800">{{ $tiket->nim }}</span>
                        </div>
                        <div>
                            <span class="text-slate-600 block mb-1">Nama Mahasiswa</span>
                            <span class="font-bold text-slate-800">{{ $tiket->nama_mahasiswa }}</span>
                        </div>
                        <div>
                            <span class="text-slate-600 block mb-1">Program Studi</span>
                            <span class="font-bold text-slate-800">{{ $tiket->prodi ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-600 block mb-1">Tanggal Diajukan</span>
                            <span class="font-bold text-slate-800">{{ $tiket->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                    </div>

                    @if ($tiket->kategori === 'aspirasi')
                        <div class="space-y-4">
                            <div>
                                <h2 class="text-xs font-semibold text-slate-600">Judul Aspirasi</h2>
                                <p class="text-base font-bold text-slate-900 mt-1">{{ $tiket->judul }}</p>
                            </div>
                            <div>
                                <h2 class="text-xs font-semibold text-slate-600">Isi / Uraian Masukan</h2>
                                <div class="text-sm text-slate-700 bg-slate-50 p-4 rounded-xl mt-1 leading-relaxed whitespace-pre-line border border-slate-100">{{ $tiket->isi }}</div>
                            </div>
                        </div>
                    @elseif ($tiket->kategori === 'konseling')
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <h2 class="text-xs font-semibold text-slate-600">Topik Permasalahan</h2>
                                    <p class="text-sm font-bold text-slate-800 mt-1">{{ $tiket->topik_konseling }}</p>
                                </div>
                                <div>
                                    <h2 class="text-xs font-semibold text-slate-600">Metode Diinginkan</h2>
                                    <p class="text-sm font-bold text-slate-800 mt-1">{{ $tiket->metode_konseling }}</p>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-xs font-semibold text-slate-600">Deskripsi Kendala</h2>
                                <div class="text-sm text-slate-700 bg-slate-50 p-4 rounded-xl mt-1 leading-relaxed whitespace-pre-line border border-slate-100">{{ $tiket->deskripsi_masalah }}</div>
                            </div>
                        </div>
                    @elseif ($tiket->kategori === 'prestasi')
                        <div class="space-y-4">
                            <div>
                                <h2 class="text-xs font-semibold text-slate-600">Nama Kompetisi / Kegiatan</h2>
                                <p class="text-base font-bold text-slate-900 mt-1">{{ $tiket->nama_kegiatan }}</p>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                                <div>
                                    <span class="text-slate-600 block mb-0.5">Penyelenggara</span>
                                    <span class="font-semibold text-slate-800">{{ $tiket->penyelenggara }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-600 block mb-0.5">Tingkat</span>
                                    <span class="font-semibold text-slate-800">{{ $tiket->tingkat }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-600 block mb-0.5">Capaian</span>
                                    <span class="font-semibold text-slate-800">{{ $tiket->capaian ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-600 block mb-0.5">Bantuan Dana</span>
                                    <span class="font-semibold text-slate-800">{{ $tiket->estimasi_biaya ? 'Rp ' . number_format($tiket->estimasi_biaya, 0, ',', '.') : '-' }}</span>
                                </div>
                            </div>

                            @if ($tiket->tampil_ke_publik)
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-900 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 4h10v5a5 5 0 0 1-10 0V4z"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 5H4.5A2.5 2.5 0 0 0 7 10"/><path stroke-linecap="round" stroke-linejoin="round" d="M17 5h2.5A2.5 2.5 0 0 1 17 10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14v3M9 20h6"/></svg>
                                    <span>Prestasi ini telah disetujui BKHM dan resmi ditampilkan di <strong>Showcase Prestasi Kampus</strong>.</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($tiket->lampiran || $tiket->lampiran_bukti)
                        <div class="pt-4 border-t border-slate-100">
                            <span class="text-xs font-semibold text-slate-600 block mb-2">Berkas Lampiran Pengaju</span>
                            <a href="{{ route('layanan.lampiran', ['tiket' => $tiket->id, 'email' => $tiket->email]) }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center min-h-[44px] gap-2 px-3.5 rounded-xl text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Unduh Dokumen Pendukung
                            </a>
                        </div>
                    @endif

                    @if ($tiket->tanggapan_resmi || $tiket->jadwal_temu)
                        <div class="pt-6 border-t border-slate-100 space-y-4">
                            <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-5 text-indigo-950">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-700 text-white flex items-center justify-center font-bold text-[10px]">
                                            ITG
                                        </div>
                                        <strong class="font-bold text-sm text-indigo-950">Tanggapan Resmi Pengelola (BKHM / BPM)</strong>
                                    </div>
                                    @if ($tiket->updated_at)
                                        <span class="text-[11px] text-indigo-800">{{ $tiket->updated_at->translatedFormat('d M Y, H:i') }}</span>
                                    @endif
                                </div>

                                @if ($tiket->jadwal_temu)
                                    <div class="bg-white border border-indigo-200 rounded-xl p-4 my-3 text-xs text-indigo-950 space-y-2">
                                        <strong class="font-semibold block text-indigo-950 text-sm">Jadwal Temu Konseling</strong>
                                        <div class="text-sm font-bold text-indigo-800 mt-0.5">{{ $tiket->jadwal_temu->translatedFormat('l, d F Y') }} &bull; Pukul {{ $tiket->jadwal_temu->format('H:i') }} WIB</div>
                                        @if ($tiket->lokasi_atau_link)
                                            <div class="text-xs text-slate-700"><span class="font-semibold">Tempat / Tautan:</span> {{ $tiket->lokasi_atau_link }}</div>
                                        @endif
                                        <p class="text-slate-700">Silakan hadir di Ruang BKHM ITG atau membuka tautan sesuai jadwal di atas.</p>

                                        @if ($tiket->kategori === 'konseling')
                                            <div class="pt-3 mt-2 border-t border-indigo-100">
                                                @if ($tiket->konfirmasi_mahasiswa)
                                                    <div class="p-3 rounded-lg text-xs {{ $tiket->konfirmasi_mahasiswa === 'bersedia_hadir' ? 'bg-emerald-50 border border-emerald-200 text-emerald-900' : ($tiket->konfirmasi_mahasiswa === 'minta_reschedule' ? 'bg-amber-50 border border-amber-200 text-amber-900' : 'bg-rose-50 border border-rose-200 text-rose-900') }}">
                                                        <div class="font-bold">
                                                            @if($tiket->konfirmasi_mahasiswa === 'bersedia_hadir')
                                                                <span>Anda telah mengonfirmasi bersedia hadir pada sesi ini.</span>
                                                            @elseif($tiket->konfirmasi_mahasiswa === 'minta_reschedule')
                                                                <span>Anda telah mengajukan permohonan reschedule (ganti jadwal).</span>
                                                            @else
                                                                <span>Anda telah membatalkan sesi konseling ini.</span>
                                                            @endif
                                                        </div>
                                                        @if($tiket->catatan_konfirmasi_mahasiswa)
                                                            <div class="mt-1 text-slate-700 italic">{{ $tiket->catatan_konfirmasi_mahasiswa }}</div>
                                                        @endif
                                                        <div class="text-[10px] text-slate-600 mt-1">Dikonfirmasi pada: {{ $tiket->konfirmasi_at ? $tiket->konfirmasi_at->translatedFormat('d M Y') . ', pukul ' . $tiket->konfirmasi_at->format('H:i') : '-' }} WIB</div>
                                                    </div>
                                                @else
                                                    <form action="{{ route('layanan.konseling.konfirmasi', $tiket) }}" method="POST" class="p-3.5 bg-slate-50 border border-indigo-200 rounded-xl space-y-2.5" x-data="{ submitting: false }" @submit="submitting = true">
                                                        @csrf
                                                        <input type="hidden" name="email" value="{{ $tiket->email }}">
                                                        <span class="block font-bold text-xs text-indigo-950">Konfirmasi Kesediaan Anda:</span>
                                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                                            <label class="flex items-center gap-2.5 p-3 min-h-[48px] bg-white rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer text-xs text-slate-700 transition">
                                                                <input type="radio" name="konfirmasi" value="bersedia_hadir" required class="text-emerald-700 focus:ring-emerald-600">
                                                                <span class="font-semibold text-slate-900">Bersedia Hadir</span>
                                                            </label>
                                                            <label class="flex items-center gap-2.5 p-3 min-h-[48px] bg-white rounded-xl border border-slate-200 hover:border-amber-500 cursor-pointer text-xs text-slate-700 transition">
                                                                <input type="radio" name="konfirmasi" value="minta_reschedule" class="text-amber-700 focus:ring-amber-600">
                                                                <span class="font-semibold text-slate-900">Minta Reschedule</span>
                                                            </label>
                                                            <label class="flex items-center gap-2.5 p-3 min-h-[48px] bg-white rounded-xl border border-slate-200 hover:border-rose-500 cursor-pointer text-xs text-slate-700 transition">
                                                                <input type="radio" name="konfirmasi" value="dibatalkan_mahasiswa" class="text-rose-700 focus:ring-rose-600">
                                                                <span class="font-semibold text-slate-900">Batalkan Sesi</span>
                                                            </label>
                                                        </div>
                                                        <div>
                                                            <label for="catatan_konfirmasi" class="sr-only">Catatan tambahan</label>
                                                            <input type="text" name="catatan" id="catatan_konfirmasi" placeholder="Catatan tambahan (misal opsi hari atau jam luang Anda jika reschedule)" class="w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3 placeholder-slate-500">
                                                        </div>
                                                        <div class="pt-1">
                                                            <button type="submit" :disabled="submitting" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 min-h-[48px] px-5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 text-white font-bold text-xs sm:text-sm rounded-xl transition shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                                <span x-text="submitting ? 'Memproses...' : 'Kirim Konfirmasi ke BKHM'">Kirim Konfirmasi ke BKHM</span>
                                                            </button>
                                                        </div>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if ($tiket->tanggapan_resmi)
                                    <div class="text-xs sm:text-sm text-indigo-950 leading-relaxed whitespace-pre-line bg-white p-3.5 rounded-xl border border-indigo-100">
                                        {{ $tiket->tanggapan_resmi }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="pt-4 border-t border-slate-100">
                            <div class="text-xs text-slate-700 bg-slate-50 p-4 rounded-xl border border-slate-200 flex items-center gap-3">
                                <svg class="w-5 h-5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Tiket Anda saat ini dalam antrean verifikasi petugas. Tanggapan resmi atau jadwal temu akan diperbarui di sini dan otomatis diteruskan ke email Anda.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-public-layout>
