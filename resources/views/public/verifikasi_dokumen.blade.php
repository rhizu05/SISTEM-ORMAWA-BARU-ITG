<x-public-layout title="Verifikasi Keaslian Dokumen" brand-label="Verifikasi Dokumen" accent="indigo">
    <x-slot name="nav">
        <span class="hidden sm:inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
            Sistem Validasi Resmi ITG
        </span>
        <a href="{{ route('layanan.index') }}" class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            <span>Portal Layanan</span>
        </a>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
        <nav class="flex items-center gap-2 text-xs text-slate-600 mb-4">
            <a href="{{ route('layanan.index') }}" class="hover:text-indigo-700 transition">Portal Layanan</a>
            <span class="text-slate-400">/</span>
            <span class="text-slate-800 font-semibold">Verifikasi Dokumen Resmi</span>
        </nav>

        @if ($mode === 'result')
            @if ($isValid)
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    <div class="bg-emerald-700 p-6 sm:p-8 text-white text-center">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-white/30">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="inline-block px-3.5 py-1 rounded-full text-xs font-extrabold bg-white/20 border border-white/30 uppercase tracking-widest text-emerald-50 mb-2">
                            Keabsahan Terverifikasi
                        </span>
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight">
                            DOKUMEN RESMI ASLI &amp; SAH
                        </h1>
                        <p class="text-xs sm:text-sm text-emerald-50 max-w-lg mx-auto mt-1 leading-relaxed">
                            Dokumen elektronik ini telah ditandatangani secara sah sesuai ketentuan hukum dan tercatat di Pangkalan Data Resmi Institut Teknologi Garut.
                        </p>
                    </div>

                    <div class="p-6 sm:p-8 space-y-6">
                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5">
                            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-3 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Pejabat Penandatangan Resmi
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl bg-indigo-700 text-white font-extrabold text-base flex items-center justify-center flex-shrink-0">
                                    {{ strtoupper(substr($data['pejabat_nama'], 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h2 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">
                                        {{ $data['pejabat_nama'] }}
                                    </h2>
                                    @if ($data['pejabat_nidn'] && $data['pejabat_nidn'] !== '-')
                                        @php
                                            $lbl = $data['identitas_label'] ?? 'NIDN';
                                        @endphp
                                        <p class="text-xs text-slate-600 font-mono mt-0.5">{{ $lbl }}. {{ $data['pejabat_nidn'] }}</p>
                                    @endif
                                    <p class="text-xs text-indigo-800 font-semibold mt-1">{{ $data['pejabat_jabatan'] }}</p>
                                    <div class="mt-3 pt-3 border-t border-slate-200 flex flex-wrap items-center gap-y-1 gap-x-4 text-xs text-slate-600">
                                        <span>Ditandatangani pada: <strong class="text-slate-800">{{ $data['formatted_date'] }}</strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-3">Rincian Dokumen Elektronik</h2>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                                    <dt class="text-slate-600 font-medium">Jenis Dokumen</dt>
                                    <dd class="text-slate-900 font-bold text-sm mt-0.5">{{ $data['snapshot']['document_type'] ?? 'Naskah Dinas Resmi' }}</dd>
                                </div>
                                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                                    <dt class="text-slate-600 font-medium">Nomor Surat Resmi</dt>
                                    <dd class="text-slate-900 font-mono font-bold text-sm mt-0.5">{{ $data['nomor_surat'] ?? '-' }}</dd>
                                </div>

                                @if(isset($data['snapshot']['perihal']))
                                <div class="sm:col-span-2 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                                    <dt class="text-slate-600 font-medium">Perihal Dokumen</dt>
                                    <dd class="text-slate-900 font-bold mt-0.5">{{ $data['snapshot']['perihal'] }}</dd>
                                </div>
                                @endif

                                @if(isset($data['snapshot']['penerima']) || isset($data['snapshot']['pemohon']) || isset($data['snapshot']['ormawa']))
                                <div class="sm:col-span-2 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                                    <dt class="text-slate-600 font-medium">Pihak Dituju / Pemohon</dt>
                                    <dd class="text-slate-900 font-bold mt-0.5">
                                        {{ $data['snapshot']['penerima'] ?? ($data['snapshot']['pemohon'] ?? ($data['snapshot']['ormawa'] ?? '-')) }}
                                        @if(isset($data['snapshot']['identitas']))
                                            <span class="text-slate-600 font-normal">({{ $data['snapshot']['identitas'] }})</span>
                                        @endif
                                    </dd>
                                </div>
                                @endif

                                @if(isset($data['snapshot']['nama_kegiatan']))
                                <div class="sm:col-span-2 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                                    <dt class="text-slate-600 font-medium">Nama Kegiatan</dt>
                                    <dd class="text-slate-900 font-bold mt-0.5">{{ $data['snapshot']['nama_kegiatan'] }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <div class="border-t border-slate-200 pt-5" x-data="{ tokenCopied: false }">
                            <div class="flex items-center justify-between mb-2">
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-600">Integritas Kriptografis (SHA-256 Digest)</h2>
                                <button type="button" @click="navigator.clipboard.writeText('{{ $data['token'] }}'); tokenCopied = true; setTimeout(() => tokenCopied = false, 2500)"
                                    class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 rounded px-1.5 py-0.5">
                                    <span x-text="tokenCopied ? '✓ Token Tersalin' : 'Salin Token'">Salin Token</span>
                                </button>
                            </div>
                            <div class="bg-slate-900 text-slate-200 p-4 rounded-xl text-[11px] font-mono space-y-1 overflow-x-auto">
                                <div class="text-slate-400 text-[10px]">ID VALIDASI DIGITAL:</div>
                                <div class="text-amber-400 font-bold select-all">{{ $data['token'] }}</div>
                                <div class="text-slate-400 text-[10px] pt-1">FINGERPRINT CRYPTOGRAPHIC HASH (HMAC-SHA256):</div>
                                <div class="break-all text-emerald-400 select-all">{{ $data['hash'] }}</div>
                            </div>
                            <p class="text-[11px] text-slate-600 mt-2">
                                Sidik jari digital ini mengunci seluruh konten dokumen secara matematis. Jika ada perubahan satu karakter pada naskah surat, maka hash tidak akan cocok dan dokumen dinyatakan tidak sah.
                            </p>
                        </div>

                        <div class="pt-2 text-center">
                            <a href="{{ route('dokumen.verifikasi.index') }}" class="inline-flex items-center min-h-[44px] px-4 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                Periksa Dokumen Lain
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-2xl border border-rose-200 overflow-hidden">
                    <div class="bg-rose-700 p-6 sm:p-8 text-white text-center">
                        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-white/30">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span class="inline-block px-3.5 py-1 rounded-full text-xs font-extrabold bg-white/20 border border-white/30 uppercase tracking-widest text-rose-50 mb-2">
                            Peringatan Keabsahan
                        </span>
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight">
                            DOKUMEN TIDAK TERDAFTAR ATAU TIDAK VALID!
                        </h1>
                        <p class="text-xs sm:text-sm text-rose-50 max-w-lg mx-auto mt-1 leading-relaxed">
                            Dokumen dengan token validasi ini tidak tercatat dalam pangkalan data resmi Institut Teknologi Garut.
                        </p>
                    </div>

                    <div class="p-6 sm:p-8 space-y-6 text-center">
                        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 text-left text-xs text-rose-900 space-y-2">
                            <strong class="font-bold block text-sm">Kemungkinan Penyebab:</strong>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>QR Code atau kode validasi tidak diterbitkan oleh sistem resmi Institut Teknologi Garut.</li>
                                <li>Dokumen merupakan naskah palsu atau hasil rekayasa tanpa persetujuan pejabat kampus.</li>
                                <li>Dokumen resmi telah dicabut atau dibatalkan keabsahannya oleh pihak pimpinan kampus.</li>
                            </ul>
                        </div>

                        <div class="text-xs font-mono text-slate-600">
                            Token yang dicari: <span class="bg-slate-100 px-2.5 py-1 rounded-lg text-slate-800 font-bold">{{ $token }}</span>
                        </div>

                        <div>
                            <a href="{{ route('dokumen.verifikasi.index') }}" class="inline-flex items-center min-h-[44px] gap-2 px-5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition">
                                Cari Ulang Kode Dokumen
                            </a>
                        </div>
                    </div>
                </div>
            @endif

        @else
            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8">
                <div class="text-center max-w-md mx-auto mb-6">
                    <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-3 text-indigo-700">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        Cek Keaslian Dokumen ITG
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1">
                        Masukkan ID Validasi yang tercantum di bawah QR Code pada naskah surat atau dokumen resmi Anda.
                    </p>
                </div>

                @if(session('error'))
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-3 mb-4 text-xs font-medium">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('dokumen.verifikasi.search') }}" method="POST" class="max-w-md mx-auto space-y-4" x-data="{ submitting: false }" @submit="submitting = true">
                    @csrf
                    <div>
                        <label for="token" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            ID Validasi Dokumen
                        </label>
                        <input type="text" name="token" id="token" placeholder="Contoh: SKIN-SIG-2026-X9K2P4" required autofocus
                            class="w-full text-sm font-mono rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 uppercase placeholder-slate-500">
                    </div>
                    <button type="submit" :disabled="submitting" class="w-full inline-flex items-center justify-center gap-2 min-h-[48px] rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-70 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2">
                        <span x-text="submitting ? 'Memproses...' : 'Periksa Keaslian Dokumen'">Periksa Keaslian Dokumen</span>
                    </button>
                </form>
            </div>
        @endif

    </div>
</x-public-layout>
