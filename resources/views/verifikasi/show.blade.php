<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Proses Verifikasi: ') }} {{ $pengajuan->nama_kegiatan }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="md:col-span-2 space-y-6">
                
                @if ($errors->any())
                    <div role="alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        @php $urgensi = $pengajuan->statusUrgensi(); @endphp
                        @if($urgensi && $urgensi['is_urgent'])
                            <div class="mb-5 p-4 rounded-lg border-l-4 {{ $urgensi['days'] <= 0 ? 'bg-red-50 border-red-500 text-red-800' : 'bg-rose-50 border-rose-500 text-rose-800' }}">
                                <div class="flex items-start gap-3">
                                    <span class="text-2xl">⚠️</span>
                                    <div>
                                        <h4 class="font-bold text-sm">Prioritas Pelaksanaan: {{ $urgensi['label'] }}</h4>
                                        <p class="text-xs mt-1">Kegiatan dijadwalkan pada <strong>{{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai_kegiatan)->format('d F Y') }}</strong>. Harap prioritaskan verifikasi & pencairan dana agar panitia tidak perlu menalangi dana operasional kegiatan secara pribadi.</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Informasi Pengajuan</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                            <div>
                                <p class="text-sm text-gray-500">Ormawa / Pengaju</p>
                                <p class="font-semibold text-gray-900">{{ $pengajuan->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status Saat Ini</p>
                                <p class="font-semibold text-indigo-600">{{ $pengajuan->state->label }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Program Kerja Terkait</p>
                                @if($pengajuan->programKerja)
                                    <p class="font-semibold text-indigo-700 flex items-center gap-1.5 mt-0.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            📋 {{ $pengajuan->programKerja->nama_proker }}
                                        </span>
                                    </p>
                                @else
                                    <p class="text-gray-400 italic text-sm mt-0.5">Non-Program Kerja (Insidental)</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Jadwal Pelaksanaan Kegiatan</p>
                                @if($pengajuan->tanggal_mulai_kegiatan)
                                    <div class="mt-0.5 flex flex-wrap items-center gap-1.5">
                                        <span class="font-semibold text-gray-800 text-sm">
                                            {{ \Carbon\Carbon::parse($pengajuan->tanggal_mulai_kegiatan)->format('d/m/Y') }}
                                            @if($pengajuan->tanggal_selesai_kegiatan && $pengajuan->tanggal_selesai_kegiatan != $pengajuan->tanggal_mulai_kegiatan)
                                                s/d {{ \Carbon\Carbon::parse($pengajuan->tanggal_selesai_kegiatan)->format('d/m/Y') }}
                                            @endif
                                        </span>
                                        @if($urgensi)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] {{ $urgensi['badge_class'] }}">
                                                ⏱️ {{ $urgensi['label'] }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-gray-400 italic text-sm mt-0.5">- Belum Ditentukan -</p>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Dana Diajukan</p>
                                <p class="font-bold text-xl text-emerald-600">Rp {{ number_format($pengajuan->dana_diajukan, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Kode Unik</p>
                                <p class="font-semibold font-mono">{{ $pengajuan->unique_code }}</p>
                            </div>
                            @if($pengajuan->dana && $pengajuan->dana->bukti_transfer)
                            <div>
                                <p class="text-sm text-gray-500">Bukti Transfer Dana</p>
                                <a href="{{ route('dokumen.bukti-transfer', $pengajuan->dana) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 px-2.5 py-1 rounded-md transition mt-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    Lihat Bukti Transfer
                                </a>
                            </div>
                            @endif
                            @if($pengajuan->nomor_surat)
                            <div class="sm:col-span-2">
                                <p class="text-sm text-gray-500">Nomor Surat Resmi</p>
                                <p class="font-semibold">{{ $pengajuan->nomor_surat }}</p>
                            </div>
                            @endif
                        </div>

                        @php
                            $sigProposal = $pengajuan->tandaTanganProposal();
                            $sigLpj = $pengajuan->tandaTanganLpj();
                        @endphp

                        @if($sigProposal->isNotEmpty())
                        <div class="mb-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                    <span class="text-emerald-600 font-bold">🔒</span> Pengesahan Proposal Kegiatan ({{ $sigProposal->count() }})
                                </h4>
                                <span class="text-[10px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-bold">
                                    Terverifikasi Kriptografis
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($sigProposal as $sig)
                                <div class="p-3 bg-white rounded-lg border border-slate-200 shadow-sm flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="font-bold text-indigo-700 uppercase tracking-wide">{{ $sig->role_badge_label }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $sig->signed_at ? $sig->signed_at->format('d/m/Y H:i') : '-' }} WIB</span>
                                        </div>
                                        <div class="font-semibold text-sm text-slate-900 leading-snug">{{ $sig->nama_penandatangan }}</div>
                                        <div class="text-[11px] text-slate-500 leading-tight mt-0.5">{{ $sig->jabatan_penandatangan }}</div>
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between">
                                        <span class="font-mono text-[9px] text-slate-400">ID: {{ substr($sig->token_verifikasi, 0, 14) }}...</span>
                                        <a href="{{ $sig->verification_url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 hover:text-emerald-800 hover:underline">
                                            <span>Cek Keaslian</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($sigLpj->isNotEmpty())
                        <div class="mb-6 p-4 rounded-xl bg-purple-50/70 border border-purple-200">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-purple-900 flex items-center gap-1.5">
                                    <span class="text-purple-600 font-bold">📑</span> Legalisir TTD Digital LPJ Kegiatan ({{ $sigLpj->count() }}/3)
                                </h4>
                                <span class="text-[10px] bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full font-bold">
                                    {{ $sigLpj->count() === 3 ? 'Legalisir Penuh (Ormawa, BKHM, WR3)' : 'Proses Legalisir' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                @foreach($sigLpj as $sig)
                                <div class="p-3 bg-white rounded-lg border border-purple-200 shadow-sm flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="font-bold text-purple-700 uppercase tracking-wide text-[11px]">{{ $sig->role_badge_label }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $sig->signed_at ? $sig->signed_at->format('d/m/Y H:i') : '-' }} WIB</span>
                                        </div>
                                        <div class="font-semibold text-sm text-slate-900 leading-snug">{{ $sig->nama_penandatangan }}</div>
                                        <div class="text-[11px] text-slate-500 leading-tight mt-0.5">{{ $sig->jabatan_penandatangan }}</div>
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between">
                                        <span class="font-mono text-[9px] text-slate-400">ID: {{ substr($sig->token_verifikasi, 0, 14) }}...</span>
                                        <a href="{{ $sig->verification_url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-semibold text-purple-700 hover:text-purple-900 hover:underline">
                                            <span>Cek Keaslian</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @php
                            $proposalFileExists = !empty($pengajuan->file_proposal) && (
                                \Illuminate\Support\Facades\Storage::disk('local')->exists($pengajuan->file_proposal) ||
                                \Illuminate\Support\Facades\Storage::disk('public')->exists($pengajuan->file_proposal)
                            );
                            $lpjFileExists = !empty($pengajuan->file_lpj) && (
                                \Illuminate\Support\Facades\Storage::disk('local')->exists($pengajuan->file_lpj) ||
                                \Illuminate\Support\Facades\Storage::disk('public')->exists($pengajuan->file_lpj)
                            );
                            $hasLpj = $lpjFileExists;
                            $isLpjStage = in_array($pengajuan->state->name, ['lpj_submitted', 'lpj_wr3_review', 'completed']);
                            $defaultTab = ($hasLpj && $isLpjStage) ? 'lpj' : 'proposal';
                        @endphp

                        @if($hasLpj)
                        <div x-data="{ docTab: '{{ $defaultTab }}' }" class="mb-4">
                            <!-- Banner Info LPJ -->
                            @if($isLpjStage)
                            <div class="mb-4 p-4 rounded-lg bg-emerald-50 border border-emerald-200">
                                <div class="flex items-start gap-3">
                                    <span class="text-2xl">📑</span>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between flex-wrap gap-2">
                                            <h4 class="font-bold text-sm text-emerald-950">Berkas Laporan Pertanggungjawaban (LPJ) Tersedia</h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                Diunggah: {{ $pengajuan->tanggal_upload_lpj ? \Carbon\Carbon::parse($pengajuan->tanggal_upload_lpj)->format('d F Y H:i') : '-' }} WIB
                                            </span>
                                        </div>
                                        <p class="text-xs text-emerald-800 mt-1">
                                            Ormawa telah mengunggah dokumen LPJ untuk diverifikasi. Silakan periksa kesesuaian laporan pertanggungjawaban kegiatan dan rincian penggunaan anggaran berikut dengan membandingkannya terhadap proposal awal.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Tab Navigation -->
                            <div class="border-b border-gray-200 mb-3 flex items-center justify-between flex-wrap gap-2">
                                <div class="flex space-x-2">
                                    <button type="button" @click="docTab = 'lpj'" 
                                        :class="docTab === 'lpj' ? 'border-indigo-600 text-indigo-700 bg-indigo-50/50 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                                        class="py-2.5 px-4 border-b-2 text-sm flex items-center gap-2 rounded-t-lg transition-colors">
                                        <span>📑 Dokumen LPJ (Laporan Pertanggungjawaban)</span>
                                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">Siap Diperiksa</span>
                                    </button>
                                    <button type="button" @click="docTab = 'proposal'" 
                                        :class="docTab === 'proposal' ? 'border-indigo-600 text-indigo-700 bg-indigo-50/50 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                                        class="py-2.5 px-4 border-b-2 text-sm flex items-center gap-2 rounded-t-lg transition-colors">
                                        <span>📋 Dokumen Proposal Awal</span>
                                    </button>
                                </div>
                            </div>

                            <!-- LPJ Document Pane -->
                            <div x-show="docTab === 'lpj'" x-transition class="space-y-2">
                                <div class="flex items-center justify-between bg-gray-50 p-2.5 rounded border border-gray-200">
                                    <span class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Pratinjau Berkas LPJ Mahasiswa
                                    </span>
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <a href="{{ route('dokumen.lpj', ['pengajuan' => $pengajuan, 'mode' => 'pengesahan']) }}" target="_blank" class="inline-flex items-center px-2 py-1 text-xs font-semibold bg-purple-50 border border-purple-200 rounded text-purple-700 hover:bg-purple-100 shadow-2xs transition" title="Lihat hanya Lembar Pengesahan resmi ITG (1 Halaman)">
                                            <svg class="w-3.5 h-3.5 mr-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Lembar Pengesahan (PDF)
                                        </a>
                                        <a href="{{ route('dokumen.lpj', $pengajuan) }}" target="_blank" class="inline-flex items-center px-2 py-1 text-xs font-semibold bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50 shadow-2xs transition" title="Buka Dokumen Lengkap Terlegalisir di Tab Baru">
                                            <svg class="w-3.5 h-3.5 mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            Buka Tab Baru
                                        </a>
                                        <a href="{{ route('dokumen.lpj', ['pengajuan' => $pengajuan, 'download' => 1]) }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold bg-indigo-600 rounded text-white hover:bg-indigo-700 shadow-2xs transition" title="Unduh Berkas Lengkap Terlegalisir">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                            Unduh LPJ
                                        </a>
                                    </div>
                                </div>
                                @if($lpjFileExists)
                                    <iframe src="{{ route('dokumen.lpj', $pengajuan) }}" class="w-full h-[520px] border rounded-lg bg-gray-50" frameborder="0"></iframe>
                                @else
                                    <div class="p-8 rounded-xl bg-slate-50 border border-slate-200 text-center flex flex-col items-center justify-center my-2">
                                        <svg class="w-10 h-10 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p class="text-sm font-semibold text-slate-700">Berkas LPJ Belum Tersedia</p>
                                        <p class="text-xs text-slate-500 mt-1 max-w-sm">Ormawa belum mengunggah berkas LPJ atau dokumen fisik belum tersimpan di server.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Proposal Document Pane -->
                            <div x-show="docTab === 'proposal'" x-transition class="space-y-2">
                                <div class="flex items-center justify-between bg-gray-50 p-2.5 rounded border border-gray-200">
                                    <span class="text-xs font-semibold text-gray-700">Dokumen Proposal Awal</span>
                                    @if($proposalFileExists)
                                    <a href="{{ route('dokumen.proposal', $pengajuan) }}" target="_blank" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold bg-white border border-gray-300 rounded text-gray-700 hover:bg-gray-50 shadow-sm transition">
                                        <svg class="w-3.5 h-3.5 mr-1 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Buka di tab baru
                                    </a>
                                    @endif
                                </div>
                                @if($proposalFileExists)
                                    <iframe src="{{ route('dokumen.proposal', $pengajuan) }}" class="w-full h-[520px] border rounded-lg bg-gray-50" frameborder="0"></iframe>
                                @else
                                    <div class="p-8 rounded-xl bg-slate-50 border border-slate-200 text-center flex flex-col items-center justify-center my-2">
                                        <svg class="w-10 h-10 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p class="text-sm font-semibold text-slate-700">Berkas Proposal Belum Tersedia</p>
                                        <p class="text-xs text-slate-500 mt-1 max-w-sm">Pengajuan ini belum memiliki lampiran berkas proposal atau berkas fisik tidak ditemukan di penyimpanan server.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @else
                        <!-- Tampilan Default Jika Belum Ada LPJ (Hanya Proposal) -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-semibold text-gray-700">Dokumen Proposal</p>
                                @if($proposalFileExists)
                                <a href="{{ route('dokumen.proposal', $pengajuan) }}" target="_blank" class="text-xs text-indigo-600 hover:underline flex items-center gap-1 font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Buka di tab baru &rarr;
                                </a>
                                @endif
                            </div>
                            @if($proposalFileExists)
                                <iframe src="{{ route('dokumen.proposal', $pengajuan) }}" class="w-full h-96 border rounded-lg" frameborder="0"></iframe>
                            @else
                                <div class="p-8 rounded-xl bg-slate-50 border border-slate-200 text-center flex flex-col items-center justify-center">
                                    <svg class="w-10 h-10 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-sm font-semibold text-slate-700">Berkas Dokumen Belum Tersedia</p>
                                    <p class="text-xs text-slate-500 mt-1 max-w-sm">Pengajuan ini belum memiliki lampiran berkas proposal fisik di server.</p>
                                </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                {{-- BR-11: evaluasi termin sebelum pencairan termin berikutnya --}}
                @php $roleName = Auth::user()->roles->first()->name; @endphp
                @if(in_array($roleName, ['wr3','bkhm','admin']) && $pengajuan->state->name === 'funds_disbursed' && !$pengajuan->evaluasi_termin_ok)
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-amber-200">
                    <div class="p-6 text-slate-900">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <h3 class="text-base font-bold text-slate-900">Evaluasi Termin (BR-11)</h3>
                        </div>
                        <p class="text-xs text-slate-600 mb-4">Tandai apabila evaluasi pelaksanaan kegiatan dan LPJ termin sebelumnya telah memenuhi syarat untuk pencairan termin berikutnya.</p>
                        <form action="{{ route('verifikasi.evaluasi-termin', $pengajuan) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-sm transition min-h-[40px] focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500">
                                Tandai Evaluasi Termin Selesai
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                @if($availableTransitions->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-indigo-200">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4">Aksi Verifikasi</h3>
                        
                        @if(Auth::user()->roles->first()->name === 'bendahara' && $pengajuan->state->name === 'to_treasurer')
                            <!-- Form Khusus Pencairan Dana -->
                            <form action="{{ route('bendahara.proses', $pengajuan) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-4">
                                    <x-input-label for="nominal_cair" :value="__('Nominal Dicairkan (Rp)')" />
                                    <x-text-input id="nominal_cair" name="nominal_cair" type="number" 
                                        class="mt-1 block w-full [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" 
                                        :value="old('nominal_cair', $pengajuan->dana_diajukan)" required min="0" onwheel="this.blur()"
                                        oninput="document.getElementById('nominal_cair_preview').innerText = this.value ? 'Terbilang: Rp ' + new Intl.NumberFormat('id-ID').format(this.value) : ''" />
                                    <p id="nominal_cair_preview" class="text-xs text-emerald-600 mt-1 font-semibold">
                                        Terbilang: Rp {{ number_format(old('nominal_cair', $pengajuan->dana_diajukan), 0, ',', '.') }}
                                    </p>
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="tanggal_cair" :value="__('Tanggal Pencairan')" />
                                    <x-text-input id="tanggal_cair" name="tanggal_cair" type="date" class="mt-1 block w-full" :value="old('tanggal_cair', date('Y-m-d'))" required />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="catatan" :value="__('Catatan Tambahan')" />
                                    <textarea id="catatan" name="catatan" rows="2" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" placeholder="Cth: Dicairkan melalui transfer Bank Mandiri / Tunai..."></textarea>
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="bukti_transfer" :value="__('Bukti Transfer / Slip Bank (Opsional, PDF/JPG/PNG maks 3MB)')" />
                                    <input id="bukti_transfer" name="bukti_transfer" type="file" accept=".pdf,.jpg,.jpeg,.png"
                                        class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none p-1.5" />
                                    <p class="text-xs text-gray-500 mt-1">Unggah salinan struk ATM, bukti transfer mobile banking, atau kwitansi resmi pencairan.</p>
                                </div>

                                <div class="flex border-t pt-4">
                                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold uppercase tracking-wider shadow-sm transition min-h-[40px] focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500" onclick="return confirm('Anda yakin ingin mencairkan dana ini?')">
                                        Konfirmasi Pencairan Dana
                                    </button>
                                </div>
                            </form>
                        @else
                            <!-- Form Verifikasi Standar -->
                            <form action="{{ route('verifikasi.process', $pengajuan) }}" method="POST">
                                @csrf
                                
                                @if(Auth::user()->roles->first()->name === 'bkhm' && !$pengajuan->nomor_surat && $pengajuan->state->name === 'bpm_approved')
                                    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
                                        <x-input-label for="nomor_surat" :value="__('Nomor Surat Resmi (Wajib diisi sebelum disetujui BKHM)')" />
                                        <x-text-input id="nomor_surat" name="nomor_surat" type="text" class="mt-1 block w-full" placeholder="Contoh: 001/BEM/ITG/2026" />
                                    </div>
                                @endif

                                <div class="mb-4">
                                    <x-input-label for="catatan" :value="__('Catatan (Wajib diisi jika menolak atau merevisi pengajuan)')" />
                                    <textarea id="catatan" name="catatan" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" placeholder="Tuliskan catatan untuk ormawa atau pemeriksa selanjutnya...">{{ old('catatan') }}</textarea>
                                </div>

                                <div class="flex gap-4 border-t pt-4">
                                    @foreach($availableTransitions as $transition)
                                        @php
                                             $btnClass = 'bg-slate-800 hover:bg-slate-700 text-white'; // Default
                                            $label = strtolower($transition->action_label);
                                            if (str_contains($label, 'setuju') || str_contains($label, 'cair') || str_contains($label, 'ajukan')) {
                                                $btnClass = 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm';
                                            } elseif (str_contains($label, 'tolak')) {
                                                $btnClass = 'bg-rose-600 hover:bg-rose-700 text-white shadow-sm';
                                            } elseif (str_contains($label, 'revisi')) {
                                                $btnClass = 'bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold shadow-sm';
                                            } else {
                                                $btnClass = 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm';
                                            }
                                        @endphp
                                        <button type="submit" name="transition_id" value="{{ $transition->id }}" 
                                                class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-xl font-semibold text-xs uppercase tracking-wider focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 transition ease-in-out duration-150 min-h-[40px] {{ $btnClass }}"
                                                onclick="return confirm('Anda yakin ingin melakukan aksi: {{ $transition->action_label }}?')">
                                            {{ $transition->action_label }}
                                        </button>
                                    @endforeach
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Kolom Kanan: Riwayat Status & Diskusi Follow-up -->
            <div class="space-y-6">
                <!-- History Timeline -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-fit">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Riwayat Status</h3>
                        
                        <div class="relative border-l border-gray-200 ml-3 space-y-4">
                            @foreach($pengajuan->histori as $history)
                            <div class="mb-4 ml-4">
                                <div class="absolute w-3 h-3 bg-indigo-500 rounded-full -left-1.5 border border-white mt-1.5"></div>
                                <time class="mb-1 text-xs font-normal text-gray-400">{{ $history->created_at->format('d/m/Y H:i') }}</time>
                                <h4 class="text-sm font-semibold text-gray-900">{{ $history->state->label }}</h4>
                                <p class="text-xs text-gray-500 mb-1">Oleh: {{ $history->user->name }}</p>
                                @if($history->catatan)
                                <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded border mt-1">{{ $history->catatan }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- FR-011: Follow-up / Komunikasi Pengajuan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-fit border border-indigo-100">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="text-lg font-bold">Diskusi &amp; Follow-up</h3>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 font-semibold">
                                {{ $pengajuan->komunikasi ? $pengajuan->komunikasi->count() : 0 }} Pesan
                            </span>
                        </div>

                        <div class="space-y-3 mb-4 max-h-72 overflow-y-auto">
                            @if($pengajuan->komunikasi && $pengajuan->komunikasi->count() > 0)
                                @foreach($pengajuan->komunikasi as $k)
                                    <div class="p-3 rounded-lg {{ $k->user_id === Auth::id() ? 'bg-indigo-50 ml-6 border border-indigo-100' : 'bg-gray-50 mr-6 border border-gray-200' }}">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-xs font-bold {{ $k->user_id === Auth::id() ? 'text-indigo-700' : 'text-gray-700' }}">
                                                {{ $k->user->name ?? 'Pengguna' }}
                                                <span class="text-[10px] font-normal text-gray-500 bg-white px-1.5 py-0.5 rounded border border-gray-200 ml-1">
                                                    {{ strtoupper($k->user->roles->first()?->name ?? 'User') }}
                                                </span>
                                            </span>
                                            <span class="text-[10px] text-gray-400">{{ $k->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-800">{{ $k->pesan }}</p>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-6 text-gray-400">
                                    <svg class="w-8 h-8 mx-auto mb-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    <p class="text-xs italic">Belum ada diskusi atau follow-up pada pengajuan ini.</p>
                                </div>
                            @endif
                        </div>

                        <form action="{{ route('pengajuan.komunikasi.store', $pengajuan) }}" method="POST">
                            @csrf
                            <textarea name="pesan" rows="2" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tulis pesan balasan atau follow-up untuk ormawa..." required></textarea>
                            <div class="flex justify-end mt-2">
                                <x-primary-button class="text-xs bg-indigo-600 hover:bg-indigo-700">Kirim Balasan</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>