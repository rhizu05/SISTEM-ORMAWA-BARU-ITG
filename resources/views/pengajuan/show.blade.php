<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pengajuan: ') }} {{ $pengajuan->nama_kegiatan }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Detail Card -->
            <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold">Informasi Pengajuan</h3>
                        <div class="flex items-center gap-2">
                            @if(in_array($pengajuan->state->name, ['draft', 'rejected']) && $pengajuan->user_id === Auth::id())
                                <a href="{{ route('pengajuan.edit', $pengajuan) }}" class="text-sm bg-yellow-100 text-yellow-800 hover:bg-yellow-200 px-3 py-1 rounded font-semibold border border-yellow-300">
                                    Edit / Revisi
                                </a>
                            @endif
                            @if($pengajuan->state->name === 'draft' && $pengajuan->user_id === Auth::id())
                                <form action="{{ route('pengajuan.destroy', $pengajuan) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus draft proposal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm bg-rose-50 text-rose-700 hover:bg-rose-100 px-3 py-1 rounded font-semibold border border-rose-300">
                                        Hapus Draft
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Kode Unik</p>
                            <p class="font-semibold font-mono">{{ $pengajuan->unique_code }}</p>
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
                                    @php $urgensi = $pengajuan->statusUrgensi(); @endphp
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
                            <p class="text-sm text-gray-500">Tanggal Pengajuan Proposal</p>
                            <p class="font-semibold">{{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Dana Diajukan</p>
                            <p class="font-semibold text-emerald-700">Rp {{ number_format($pengajuan->dana_diajukan, 0, ',', '.') }}</p>
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

                    <div class="mb-4">
                        <p class="text-sm text-gray-500 mb-1">File Proposal</p>
                        @php
                            $proposalFileExists = !empty($pengajuan->file_proposal) && (
                                \Illuminate\Support\Facades\Storage::disk('local')->exists($pengajuan->file_proposal) ||
                                \Illuminate\Support\Facades\Storage::disk('public')->exists($pengajuan->file_proposal)
                            );
                        @endphp
                        @if($proposalFileExists)
                            <a href="{{ route('dokumen.proposal', $pengajuan) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-lg font-semibold text-xs text-indigo-700 hover:bg-indigo-100 transition shadow-2xs">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Lihat Dokumen PDF Proposal
                            </a>
                        @else
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-slate-500 bg-slate-100 border border-slate-200">
                                Berkas fisik proposal belum diunggah
                            </span>
                        @endif
                    </div>

                    @php
                        $sigProposal = $pengajuan->tandaTanganProposal();
                        $sigLpj = $pengajuan->tandaTanganLpj();
                    @endphp

                    @if($sigProposal->isNotEmpty())
                    <div class="mt-6 mb-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
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
                    <div class="mt-4 mb-6 p-4 rounded-xl bg-purple-50/70 border border-purple-200">
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

                    @if($pengajuan->state->name === 'cancelled')
                    <div class="mt-6 mb-4 p-4 bg-rose-50 border border-rose-200 rounded-xl">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                🚫 Proposal Dibatalkan
                            </span>
                        </div>
                        <h4 class="font-bold text-rose-950 text-sm mt-1.5">Pengajuan Ini Telah Dibatalkan oleh Pengaju</h4>
                        <p class="text-xs text-rose-800 mt-1">
                            Pengajuan proposal ini telah dibatalkan resmi sehingga tidak diproses lebih lanjut dan tidak memotong alokasi anggaran Anda. Anda dapat mengajukan proposal baru kapan saja.
                        </p>
                    </div>
                    @endif

                    @if($pengajuan->state->name === 'draft')
                    @php
                        $submitTarget = auth()->user()->hasRole('bpm') ? 'BKHM' : (auth()->user()->hasRole('bem') ? 'BPM' : 'BEM');
                    @endphp
                    <div class="mt-8 pt-4 border-t flex justify-end">
                        <form action="{{ route('pengajuan.ajukan', $pengajuan) }}" method="POST">
                            @csrf
                            <x-primary-button onclick="return confirm('Kirim pengajuan ke {{ $submitTarget }} sekarang? Pastikan data sudah benar.')">
                                Ajukan ke {{ $submitTarget }}
                            </x-primary-button>
                        </form>
                    </div>
                    @endif

                    @php
                        $canCancelInVerification = in_array($pengajuan->state->name, ['submitted', 'bem_approved', 'bpm_approved', 'bkhm_approved', 'wr3_approved', 'to_treasurer']) && $pengajuan->user_id === Auth::id();
                    @endphp
                    @if($canCancelInVerification)
                    <div class="mt-8 pt-4 border-t flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200" x-data="{ showCancelModal: false }">
                        <div>
                            <div class="text-xs font-bold text-slate-800">Opsi Pengaju</div>
                            <p class="text-xs text-slate-500 mt-0.5">Jika kegiatan batal dilaksanakan atau terdapat kesalahan fatal pada berkas/anggaran, Anda dapat membatalkan pengajuan ini.</p>
                        </div>
                        <button type="button" @click="showCancelModal = true" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition shadow-2xs shrink-0">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Batalkan Pengajuan</span>
                        </button>

                        <!-- Modal Konfirmasi Pembatalan -->
                        <div x-show="showCancelModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                                <div class="fixed inset-0 transition-opacity bg-slate-900/60" @click="showCancelModal = false"></div>
                                <div class="relative inline-block w-full max-w-lg p-6 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-slate-200">
                                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                                            <span class="text-rose-600">⚠️</span> Konfirmasi Pembatalan Pengajuan
                                        </h3>
                                        <button type="button" @click="showCancelModal = false" class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
                                    </div>
                                    <form action="{{ route('pengajuan.batalkan', $pengajuan) }}" method="POST" class="mt-4 space-y-4">
                                        @csrf
                                        <p class="text-xs text-slate-600 leading-relaxed">
                                            Apakah Anda yakin ingin membatalkan pengajuan <strong>{{ $pengajuan->nama_kegiatan }}</strong>? Proposal akan ditarik dari antrean verifikator dan dicatat pembatalan resmi.
                                        </p>
                                        <div>
                                            <label for="alasan" class="block text-xs font-bold text-slate-700 mb-1">
                                                Alasan Pembatalan <span class="text-rose-500">*</span>
                                            </label>
                                            <textarea id="alasan" name="alasan" rows="3" required minlength="5" maxlength="500" placeholder="Contoh: Kegiatan dibatalkan panitia / Salah mengunggah berkas rancangan anggaran..." class="w-full text-xs rounded-xl border border-slate-200 focus:border-rose-500 focus:ring-1 focus:ring-rose-500 p-2.5"></textarea>
                                            <p class="text-[11px] text-slate-400 mt-1">Alasan pembatalan akan disimpan di catatan riwayat alur pengajuan.</p>
                                        </div>
                                        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                                            <button type="button" @click="showCancelModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                                                Kembali
                                            </button>
                                            <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                                Ya, Batalkan Proposal Ini
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($pengajuan->state->name === 'funds_disbursed' && $pengajuan->user_id === Auth::id())
                    <div class="mt-8 pt-4 border-t">
                        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        💰 Dana Telah Dicairkan
                                    </span>
                                </div>
                                <h4 class="font-bold text-emerald-950 text-sm mt-1.5">Langkah Selanjutnya: Unggah Laporan Pertanggungjawaban (LPJ)</h4>
                                <p class="text-xs text-emerald-800 mt-0.5">
                                    Dana kas sebesar <strong>Rp {{ number_format($pengajuan->dana_diajukan, 0, ',', '.') }}</strong> telah dicairkan oleh Bendahara. Setelah kegiatan selesai dilaksanakan, segera unggah dokumen LPJ format PDF beserta bukti transaksi/dokumentasi untuk menyelesaikan siklus anggaran dan membuka kembali akses pengajuan baru.
                                </p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if($pengajuan->dana?->bukti_transfer)
                                    <a href="{{ route('dokumen.bukti-transfer', $pengajuan->dana) }}" target="_blank" class="inline-flex items-center px-3.5 py-2.5 bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-50 font-bold rounded-lg text-xs shadow-sm transition">
                                        <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        Bukti Transfer
                                    </a>
                                @endif
                                <a href="{{ route('lpj.create', $pengajuan) }}" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs shadow transition">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Unggah Dokumen LPJ
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(in_array($pengajuan->state->name, ['lpj_submitted', 'lpj_wr3_review', 'completed']))
                    <div class="mt-8 pt-4 border-t">
                        <div class="p-4 {{ $pengajuan->state->name === 'completed' ? 'bg-green-50 border border-green-200' : 'bg-indigo-50 border border-indigo-200' }} rounded-lg flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $pengajuan->state->name === 'completed' ? 'bg-green-100 text-green-800' : 'bg-indigo-100 text-indigo-800' }}">
                                        {{ $pengajuan->state->name === 'completed' ? '✅ LPJ Disetujui & Selesai' : '📑 LPJ Sedang Ditinjau' }}
                                    </span>
                                </div>
                                <h4 class="font-bold text-gray-900 text-sm mt-1.5">Dokumen Laporan Pertanggungjawaban (LPJ)</h4>
                                <p class="text-xs text-gray-600 mt-0.5">
                                    @if($pengajuan->state->name === 'completed')
                                        Siklus kegiatan ini telah selesai secara penuh dan evaluasi akhir telah disetujui oleh WR3.
                                    @else
                                        Dokumen LPJ telah diunggah dan saat ini sedang dalam proses evaluasi oleh <strong>{{ $pengajuan->state->label }}</strong>.
                                    @endif
                                </p>
                            </div>
                            @if($pengajuan->file_lpj)
                            <div class="flex items-center gap-2 flex-wrap shrink-0">
                                <a href="{{ route('dokumen.lpj', ['pengajuan' => $pengajuan, 'mode' => 'pengesahan']) }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-purple-50 border border-purple-200 hover:bg-purple-100 text-purple-700 font-semibold rounded-lg text-xs shadow-2xs transition">
                                    <svg class="w-3.5 h-3.5 mr-1 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Lembar Pengesahan (PDF)
                                </a>
                                <a href="{{ route('dokumen.lpj', $pengajuan) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-xs shadow transition">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Dokumen LPJ Terlegalisir
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- History Timeline -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-fit">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Riwayat & Status PIC</h3>
                    
                    @if($pengajuan->state && $pengajuan->state->pic_role)
                    <div class="mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-lg text-xs">
                        <span class="font-bold text-indigo-800 uppercase block mb-1">Status PIC Saat Ini:</span>
                        <p class="text-indigo-900 font-semibold">{{ $pengajuan->state->pic_role }}</p>
                        <p class="text-indigo-700 mt-0.5">Kontak / Pihak terkait: <span class="font-medium">{{ $pengajuan->state->pic_contact }}</span></p>

                        @if($pengajuan->user_id === Auth::id() && $pengajuan->targetRoleNudge())
                            @if($pengajuan->bisaDiingatkan())
                            <div class="mt-3 pt-3 border-t border-indigo-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <span class="text-indigo-700 text-xs">Perlu tindak lanjut lebih cepat?</span>
                                <form action="{{ route('pengajuan.nudge', $pengajuan) }}" method="POST" onsubmit="return confirm('Kirim notifikasi pengingat cepat kepada {{ $pengajuan->targetLembagaLabel() }} sekarang?')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded shadow-sm transition text-xs">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                        Kirim Pengingat ke {{ $pengajuan->targetLembagaLabel() }}
                                    </button>
                                </form>
                            </div>
                            @elseif($pengajuan->apakahDalamCooldown())
                            <div class="mt-3 pt-3 border-t border-indigo-200">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-600 font-medium">Pengingat telah dikirim ({{ $pengajuan->jumlah_nudge }}x)</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-medium text-[11px]">
                                        <svg class="w-3 h-3 mr-1 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        Cooldown: {{ $pengajuan->sisaWaktuCooldown() }} lagi
                                    </span>
                                </div>
                                <p class="text-[11px] text-gray-500 mt-1">Pengingat terakhir dikirim {{ $pengajuan->terakhir_diingatkan_at->format('d/m/Y H:i') }} WIB.</p>
                            </div>
                            @endif
                        @endif
                    </div>
                    @endif

                    <div class="relative border-l border-gray-200 ml-3 space-y-4">
                        @forelse($pengajuan->histori as $history)
                        <div class="mb-4 ml-4">
                            <div class="absolute w-3 h-3 bg-indigo-500 rounded-full -left-1.5 border border-white mt-1.5"></div>
                            <time class="mb-1 text-xs font-normal text-gray-400">{{ $history->created_at->format('d/m/Y H:i') }}</time>
                            <h4 class="text-sm font-semibold text-gray-900">{{ $history->state->label ?? 'Status' }}</h4>
                            @if($history->state && $history->state->pic_role)
                            <p class="text-[11px] text-indigo-600 font-medium">PIC: {{ $history->state->pic_role }} ({{ $history->state->pic_contact }})</p>
                            @endif
                            <p class="text-xs text-gray-500 mb-1">Oleh: {{ $history->user->name ?? 'Sistem' }}</p>
                            @if($history->catatan)
                            <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded border mt-1">{{ $history->catatan }}</p>
                            @endif
                            @if($history->catatan_kendala)
                            <p class="text-sm text-red-700 bg-red-50 p-2 rounded border border-red-200 mt-1"><strong>Kendala / Catatan Revisi:</strong> {{ $history->catatan_kendala }}</p>
                            @endif
                        </div>
                        @empty
                        <div class="mb-4 ml-4">
                            <div class="absolute w-3 h-3 bg-indigo-500 rounded-full -left-1.5 border border-white mt-1.5"></div>
                            <time class="mb-1 text-xs font-normal text-gray-400">{{ $pengajuan->created_at->format('d/m/Y H:i') }}</time>
                            <h4 class="text-sm font-semibold text-gray-900">{{ $pengajuan->state->label ?? 'Draft / Inisiasi' }}</h4>
                            <p class="text-xs text-gray-500 mb-1">Oleh: {{ $pengajuan->user->name ?? 'Pengusul' }}</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- FR-011: Follow-up / Komunikasi Pengajuan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-fit">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Diskusi & Follow-up</h3>


                    <div class="space-y-3 mb-4 max-h-72 overflow-y-auto">
                        @forelse($pengajuan->komunikasi as $k)
                            <div class="p-3 rounded-lg {{ $k->user_id === Auth::id() ? 'bg-indigo-50 ml-6' : 'bg-gray-50 mr-6' }}">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-700">{{ $k->user->name ?? 'Pengguna' }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $k->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-800 mt-1">{{ $k->pesan }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 italic">Belum ada diskusi. Mulai follow-up di sini.</p>
                        @endforelse
                    </div>

                    <form id="form-komunikasi" action="{{ route('pengajuan.komunikasi.store', $pengajuan) }}" method="POST">
                        @csrf
                        <textarea name="pesan" rows="2" class="w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Tulis pesan follow-up..." required></textarea>
                        <div class="flex justify-end mt-2">
                            <x-primary-button id="btn-kirim-komunikasi" class="text-xs">Kirim</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>