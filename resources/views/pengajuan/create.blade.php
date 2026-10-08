<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Pengajuan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(isset($blocking) && $blocking)
                @php
                    $isDanaCair = $blocking->state->name === 'funds_disbursed';
                    $isLpjReview = in_array($blocking->state->name, ['lpj_submitted', 'lpj_wr3_review']);
                @endphp
                <div class="{{ $isDanaCair ? 'bg-amber-50 border-l-4 border-amber-500' : ($isLpjReview ? 'bg-indigo-50 border-l-4 border-indigo-500' : 'bg-yellow-50 border-l-4 border-yellow-400') }} p-6 rounded-lg shadow mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-0.5">
                            @if($isDanaCair)
                                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @elseif($isLpjReview)
                                <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @else
                                <svg class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                            @endif
                        </div>
                        <div class="ml-4 flex-1">
                            <h3 class="text-base font-bold {{ $isDanaCair ? 'text-amber-900' : ($isLpjReview ? 'text-indigo-900' : 'text-yellow-800') }}">
                                @if($isDanaCair)
                                    Pengajuan Ditangguhkan: Menunggu Unggah Dokumen LPJ
                                @elseif($isLpjReview)
                                    Pengajuan Ditangguhkan: Dokumen LPJ Sedang Ditinjau
                                @else
                                    Pengajuan Ditangguhkan: Menunggu Penyelesaian Kegiatan Sebelumnya
                                @endif
                            </h3>
                            
                            <p class="text-sm {{ $isDanaCair ? 'text-amber-800' : ($isLpjReview ? 'text-indigo-800' : 'text-yellow-700') }} mt-1">
                                @if($isDanaCair)
                                    Dana kegiatan sebelumnya telah dicairkan oleh Bendahara. Sesuai ketentuan kepatuhan keuangan kemahasiswaan ITG, Anda <strong>wajib mengunggah Laporan Pertanggungjawaban (LPJ)</strong> kegiatan tersebut agar gembok pengajuan proposal baru dapat dibuka kembali.
                                @elseif($isLpjReview)
                                    Dokumen LPJ telah diunggah (status: <strong>{{ $blocking->state->label }}</strong>) dan saat ini sedang dalam proses evaluasi oleh tim verifikator (BKHM / WR3). Gembok pengajuan proposal baru akan otomatis terbuka setelah evaluasi LPJ disetujui penuh (Selesai/Completed).
                                @else
                                    Anda belum bisa mengajukan proposal baru karena masih ada proposal kegiatan yang sedang berjalan dalam tahapan verifikasi atau ditolak.
                                @endif
                            </p>

                            <div class="mt-3.5 text-sm bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div>
                                    <div class="text-gray-500 text-xs uppercase font-semibold">Kegiatan yang Sedang Berjalan:</div>
                                    <div class="font-bold text-gray-900 text-sm mt-0.5">{{ $blocking->nama_kegiatan }}</div>
                                    <div class="text-xs text-gray-600 mt-0.5">Status Saat Ini: <span class="font-bold {{ $isDanaCair ? 'text-amber-700' : 'text-indigo-700' }}">{{ $blocking->state->label ?? $blocking->state->name }}</span></div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('pengajuan.show', $blocking) }}" class="inline-flex items-center px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs transition">
                                        Lihat Pengajuan
                                    </a>
                                    @if($isDanaCair)
                                        <a href="{{ route('lpj.create', $blocking) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs shadow transition">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                            Unggah LPJ Sekarang
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 flex items-center gap-3">
                                <a href="{{ route('pengajuan.show', $blocking) }}" class="text-xs font-semibold {{ $isDanaCair ? 'text-amber-900 underline' : 'text-indigo-700 underline' }}">
                                    Lihat Detail & Riwayat Pengajuan
                                </a>
                                <span class="text-gray-300">•</span>
                                <a href="{{ route('pengajuan.index') }}" class="text-xs text-gray-600 hover:underline">
                                    Ke Riwayat Proposal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 @if(isset($blocking) && $blocking) opacity-50 pointer-events-none @endif">
                        <form method="POST" action="{{ route('pengajuan.store') }}" enctype="multipart/form-data" x-data="{ submitting: false }" @submit="submitting = true">
                            @csrf
                            
                            <div class="space-y-6">
                                <div class="mb-4">
                                    <x-input-label for="nama_kegiatan" :value="__('Nama Kegiatan')" />
                                    <x-text-input id="nama_kegiatan" class="block mt-1 w-full" type="text" name="nama_kegiatan" :value="old('nama_kegiatan')" required autofocus :disabled="isset($blocking) && $blocking" />
                                    <x-input-error :messages="$errors->get('nama_kegiatan')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="program_kerja_id" :value="__('Program Kerja Terkait (Opsional)')" />
                                    <select id="program_kerja_id" name="program_kerja_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" @if(isset($blocking) && $blocking) disabled @endif>
                                        <option value="">-- Tidak Terkait / Di Luar Program Kerja Tahunan --</option>
                                        @foreach($prokers ?? [] as $proker)
                                            <option value="{{ $proker->id }}" {{ old('program_kerja_id') == $proker->id ? 'selected' : '' }}>
                                                {{ $proker->nama_proker }} (Rencana: {{ \Carbon\Carbon::parse($proker->rencana_pelaksanaan)->format('d/m/Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(count($prokers ?? []) === 0)
                                        <p class="text-xs text-indigo-600 mt-1">💡 Belum memiliki program kerja tahunan? Daftarkan proker Anda di <a href="{{ route('proker.create') }}" class="underline font-semibold" target="_blank">Menu Program Kerja</a>.</p>
                                    @else
                                        <p class="text-xs text-gray-500 mt-1">Pilih proker tahunan ormawa Anda untuk memudahkan monitoring realisasi oleh BPM dan BKHM.</p>
                                    @endif
                                    <x-input-error :messages="$errors->get('program_kerja_id')" class="mt-2" />
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <x-input-label for="tanggal_mulai_kegiatan" :value="__('Tanggal Mulai Kegiatan (Opsional)')" />
                                        <x-text-input id="tanggal_mulai_kegiatan" class="block mt-1 w-full" type="date" name="tanggal_mulai_kegiatan" :value="old('tanggal_mulai_kegiatan')" :disabled="isset($blocking) && $blocking" onchange="document.getElementById('tanggal_selesai_kegiatan').min = this.value" />
                                        <x-input-error :messages="$errors->get('tanggal_mulai_kegiatan')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="tanggal_selesai_kegiatan" :value="__('Tanggal Selesai Kegiatan (Opsional)')" />
                                        <x-text-input id="tanggal_selesai_kegiatan" class="block mt-1 w-full" type="date" name="tanggal_selesai_kegiatan" :value="old('tanggal_selesai_kegiatan')" :min="old('tanggal_mulai_kegiatan')" :disabled="isset($blocking) && $blocking" />
                                        <x-input-error :messages="$errors->get('tanggal_selesai_kegiatan')" class="mt-2" />
                                    </div>
                                    <div class="col-span-1 md:col-span-2 -mt-2">
                                        <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded p-2">
                                            💡 <strong>Urgensi Pencairan:</strong> Mengisi tanggal pelaksanaan kegiatan akan menampilkan indikator hitung mundur (H-X) pada antrean verifikator sehingga proposal dapat diprioritaskan sebelum acara berlangsung.
                                        </p>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="dana_diajukan" :value="__('Dana Diajukan (Rp)')" />
                                    <x-text-input id="dana_diajukan" class="block mt-1 w-full [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" type="number" name="dana_diajukan" :value="old('dana_diajukan')" required min="0" :disabled="isset($blocking) && $blocking" onwheel="this.blur()" oninput="document.getElementById('dana_diajukan_preview').innerText = this.value ? 'Terbilang: Rp ' + new Intl.NumberFormat('id-ID').format(this.value) : ''" />
                                    <p id="dana_diajukan_preview" class="text-xs text-indigo-600 mt-1 font-semibold">
                                        {{ old('dana_diajukan') ? 'Terbilang: Rp ' . number_format(old('dana_diajukan'), 0, ',', '.') : '' }}
                                    </p>
                                    <x-input-error :messages="$errors->get('dana_diajukan')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="tanggal_pengajuan" :value="__('Tanggal Pengajuan')" />
                                    <x-text-input id="tanggal_pengajuan" class="block mt-1 w-full" type="date" name="tanggal_pengajuan" :value="old('tanggal_pengajuan', date('Y-m-d'))" required :disabled="isset($blocking) && $blocking" />
                                    <x-input-error :messages="$errors->get('tanggal_pengajuan')" class="mt-2" />
                                </div>

                                <div class="mb-6">
                                    <x-input-label for="file_proposal" :value="__('File Proposal (PDF, maks 5MB)')" />
                                    <input id="file_proposal" class="block mt-1 w-full border border-gray-300 rounded p-2" type="file" name="file_proposal" accept=".pdf" required @if(isset($blocking) && $blocking) disabled @endif />
                                    <x-input-error :messages="$errors->get('file_proposal')" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4" href="{{ route('pengajuan.index') }}">
                                    Batal
                                </a>
                                <x-primary-button :disabled="isset($blocking) && $blocking" x-bind:disabled="submitting">
                                    <span x-show="!submitting">{{ __('Simpan sebagai Draft') }}</span>
                                    <span x-show="submitting" class="inline-flex items-center gap-2" style="display: none;" x-cloak>
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>Mengunggah Dokumen...</span>
                                    </span>
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Financial Info -->
                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Sisa Saldo Anda</h3>
                        <p class="text-3xl font-bold text-green-600">Rp {{ number_format(Auth::user()->saldo, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-400 mt-2 italic">*Pastikan dana yang diajukan tidak melebihi saldo tersedia.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
