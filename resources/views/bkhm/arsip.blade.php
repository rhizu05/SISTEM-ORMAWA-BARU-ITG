<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Arsip Persuratan & Penerbitan Dokumen Resmi') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Kelola seluruh arsip nomor surat persetujuan kegiatan dan surat peringatan resmi</p>
            </div>
            <a href="{{ route('bkhm.sp.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Terbitkan Surat Peringatan (SP)</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ tab: '{{ request('tab', 'pengajuan') }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Navigasi Tab -->
            <div class="flex border-b border-gray-200 gap-4">
                <button @click="tab = 'pengajuan'" :class="tab === 'pengajuan' ? 'border-indigo-600 text-indigo-600 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700 font-medium'" class="pb-3 text-sm px-2 transition">
                    Surat Pengajuan Kegiatan ({{ $arsip->total() }})
                </button>
                <button @click="tab = 'sp'" :class="tab === 'sp' ? 'border-indigo-600 text-indigo-600 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700 font-medium'" class="pb-3 text-sm px-2 transition flex items-center gap-1.5">
                    <span>Surat Peringatan Resmi ({{ $arsipSp->total() }})</span>
                    @if($arsipSp->total() > 0)
                        <span class="bg-red-100 text-red-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $arsipSp->total() }}</span>
                    @endif
                </button>
                <button @click="tab = 'sk'" :class="tab === 'sk' ? 'border-indigo-600 text-indigo-600 font-bold border-b-2' : 'text-gray-500 hover:text-gray-700 font-medium'" class="pb-3 text-sm px-2 transition flex items-center gap-1.5">
                    <span>Surat Keputusan (SK) Ormawa ({{ $arsipSk->total() }})</span>
                    @if($arsipSk->total() > 0)
                        <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $arsipSk->total() }}</span>
                    @endif
                </button>
            </div>

            <!-- Tab 1: Surat Pengajuan Kegiatan -->
            <div x-show="tab === 'pengajuan'" class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Arsip Surat Pengajuan Kegiatan</h3>
                        <p class="text-xs text-gray-500">Daftar proposal kemahasiswaan yang telah memperoleh nomor surat resmi</p>
                    </div>
                    <form method="GET" class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no surat / kegiatan..." class="border-gray-300 rounded-lg px-3 py-1.5 text-xs w-64 focus:ring-indigo-500 focus:border-indigo-500">
                        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold transition">Cari</button>
                    </form>
                </div>

                <x-table>
                    <x-table.thead>
                        <tr>
                            <x-table.th align="center">No</x-table.th>
                            <x-table.th>Nama Kegiatan</x-table.th>
                            <x-table.th>Ormawa</x-table.th>
                            <x-table.th>Nomor Surat</x-table.th>
                            <x-table.th>Status Terakhir</x-table.th>
                            <x-table.th align="right">Aksi</x-table.th>
                        </tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($arsip as $i => $a)
                        <x-table.tr>
                            <x-table.td align="center">
                                <span class="font-medium text-slate-500 text-xs">{{ $arsip->firstItem() + $i }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="font-semibold text-slate-900">{{ $a->nama_kegiatan }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="text-xs text-slate-700 font-medium">{{ $a->user->name }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="font-mono text-xs font-semibold text-slate-900">{{ $a->nomor_surat }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    {{ $a->state->label ?? $a->status_akhir ?? '-' }}
                                </span>
                            </x-table.td>
                            <x-table.td align="right">
                                <a href="{{ route('verifikasi.show', $a) }}" class="inline-flex items-center px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg font-semibold text-xs transition">
                                    Detail &rarr;
                                </a>
                            </x-table.td>
                        </x-table.tr>
                        @empty
                        <x-table.empty colspan="6" message="Belum ada arsip surat pengajuan." />
                        @endforelse
                    </tbody>
                </x-table>
                <div class="mt-4">{{ $arsip->links() }}</div>
            </div>

            <!-- Tab 2: Surat Peringatan (SP) Resmi -->
            <div x-show="tab === 'sp'" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Arsip Dokumen Surat Peringatan Resmi (SP)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Rekapitulasi surat peringatan yang diterbitkan untuk Ormawa maupun Mahasiswa perorangan</p>
                    </div>
                </div>

                <x-table>
                    <x-table.thead>
                        <tr>
                            <x-table.th align="center">Tingkat</x-table.th>
                            <x-table.th align="center">Penerbit</x-table.th>
                            <x-table.th align="center">Status Validasi</x-table.th>
                            <x-table.th>Nomor Surat</x-table.th>
                            <x-table.th>Sasaran / Penerima</x-table.th>
                            <x-table.th>Tanggal</x-table.th>
                            <x-table.th>Perihal &amp; Alasan</x-table.th>
                            <x-table.th>Penandatangan (WR3)</x-table.th>
                            <x-table.th align="right">Aksi</x-table.th>
                        </tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($arsipSp as $sp)
                        <x-table.tr>
                            <x-table.td align="center">
                                @php
                                    $tingkatClass = match($sp->tingkat) {
                                        'SP-3' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'SP-2' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default => 'bg-amber-50 text-amber-800 border-amber-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-bold text-xs border {{ $tingkatClass }}">
                                    {{ $sp->tingkat }}
                                </span>
                            </x-table.td>
                            <x-table.td align="center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-bold text-[10px] {{ $sp->penerbit_label === 'BPM' ? 'bg-indigo-50 text-indigo-800 border border-indigo-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                    {{ $sp->penerbit_label }}
                                </span>
                            </x-table.td>
                            <x-table.td align="center">
                                @if($sp->isDisetujui())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Disetujui
                                    </span>
                                @elseif($sp->isMenungguBkhm())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> Menunggu BKHM
                                    </span>
                                @elseif($sp->isMenungguValidasi())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Menunggu WR3
                                    </span>
                                @elseif($sp->isDitolak())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-[10px] bg-rose-50 text-rose-700 border border-rose-200" title="{{ $sp->catatan_wr3 }}">
                                        <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Dikembalikan
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] bg-slate-100 text-slate-700">{{ $sp->status }}</span>
                                @endif
                            </x-table.td>
                            <x-table.td>
                                <span class="font-mono text-xs font-semibold text-slate-900">{{ $sp->nomor_surat }}</span>
                            </x-table.td>
                            <x-table.td>
                                @if($sp->isMahasiswa())
                                    <div class="font-semibold text-slate-900">{{ $sp->nama_penerima }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $sp->identitas_penerima }}</div>
                                @else
                                    <div class="font-semibold text-slate-900">{{ $sp->target?->name ?? 'Ormawa' }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $sp->target?->username }}</div>
                                @endif
                            </x-table.td>
                            <x-table.td>
                                <span class="text-xs text-slate-600">{{ $sp->tanggal_surat ? $sp->tanggal_surat->format('d/m/Y') : '-' }}</span>
                            </x-table.td>
                            <x-table.td>
                                <div class="font-semibold text-slate-900 line-clamp-1">{{ $sp->perihal }}</div>
                                <div class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $sp->alasan_singkat }}</div>
                                @if($sp->isDitolak() && $sp->catatan_wr3)
                                    <div class="text-[10px] text-rose-700 font-medium mt-1 bg-rose-50 p-1.5 rounded-lg border border-rose-200">
                                        <strong>Catatan WR3:</strong> {{ $sp->catatan_wr3 }}
                                    </div>
                                @endif
                                @if($sp->isDitolak() && $sp->catatan_bkhm)
                                    <div class="text-[10px] text-rose-700 font-medium mt-1 bg-rose-50 p-1.5 rounded-lg border border-rose-200">
                                        <strong>Catatan BKHM:</strong> {{ $sp->catatan_bkhm }}
                                    </div>
                                @endif
                            </x-table.td>
                            <x-table.td>
                                <div class="font-medium text-slate-900 text-xs">{{ $sp->pejabat_nama ?? 'Pejabat Berwenang' }}</div>
                                <div class="text-[10px] text-slate-500">{{ $sp->pejabat_jabatan ?? 'Wakil Rektor III' }}</div>
                            </x-table.td>
                            <x-table.td align="right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('bkhm.sp.show', $sp) }}" class="inline-flex items-center px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg font-semibold text-xs transition">
                                        {{ $sp->isDisetujui() ? 'Pratinjau' : 'Tinjau Draf' }}
                                    </a>
                                    @if($sp->isMenungguBkhm())
                                    <button type="button" onclick="openSpReviewModal({{ $sp->id }}, '{{ $sp->nomor_surat }}', 'teruskan')" class="inline-flex items-center px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg font-semibold text-xs border border-emerald-200 transition">Teruskan</button>
                                    <button type="button" onclick="openSpReviewModal({{ $sp->id }}, '{{ $sp->nomor_surat }}', 'kembalikan')" class="inline-flex items-center px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg font-semibold text-xs border border-rose-200 transition">Kembalikan</button>
                                    @endif
                                    @if($sp->isDisetujui())
                                    <a href="{{ route('bkhm.sp.pdf', $sp) }}" class="inline-flex items-center px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold text-xs transition" title="Unduh PDF Resmi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                    @endif
                                </div>
                            </x-table.td>
                        </x-table.tr>
                        @empty
                        <x-table.empty colspan="9" message="Belum ada riwayat surat peringatan yang diterbitkan." />
                        @endforelse
                    </tbody>
                </x-table>
                <div class="mt-4">{{ $arsipSp->links() }}</div>
            </div>

            <!-- Tab 3: Surat Keputusan (SK) Ormawa -->
            <div x-show="tab === 'sk'" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Arsip Surat Keputusan (SK) Ormawa</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar Surat Keputusan legalitas kepengurusan seluruh Organisasi Mahasiswa ITG yang terdaftar</p>
                    </div>
                    <form method="GET" class="flex gap-2">
                        <input type="hidden" name="tab" value="sk">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ormawa / nomor SK..." class="border-gray-300 rounded-lg px-3 py-1.5 text-xs w-64 focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold transition">Cari</button>
                        @if(request('search'))
                            <a href="{{ route('bkhm.arsip.index', ['tab' => 'sk']) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-semibold transition">Reset</a>
                        @endif
                    </form>
                </div>

                <x-table>
                    <x-table.thead>
                        <tr>
                            <x-table.th align="center">No</x-table.th>
                            <x-table.th>Nama Organisasi</x-table.th>
                            <x-table.th>Peran / Kategori</x-table.th>
                            <x-table.th>Nomor SK</x-table.th>
                            <x-table.th>Tanggal SK</x-table.th>
                            <x-table.th>Dokumen SK</x-table.th>
                            <x-table.th align="right">Aksi</x-table.th>
                        </tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($arsipSk as $i => $u)
                        <x-table.tr>
                            <x-table.td align="center">
                                <span class="font-medium text-slate-500 text-xs">{{ $arsipSk->firstItem() + $i }}</span>
                            </x-table.td>
                            <x-table.td>
                                <div class="font-semibold text-slate-900">{{ $u->name }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $u->email }}</div>
                            </x-table.td>
                            <x-table.td>
                                @foreach($u->roles as $role)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $role->name === 'bem' ? 'bg-amber-100 text-amber-800' : ($role->name === 'bpm' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ $role->name }}
                                    </span>
                                @endforeach
                            </x-table.td>
                            <x-table.td>
                                <span class="font-mono text-xs font-bold text-slate-900">{{ $u->nomor_sk ?? '-' }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="text-xs text-slate-600">{{ $u->tanggal_sk ? $u->tanggal_sk->format('d/m/Y') : '-' }}</span>
                            </x-table.td>
                            <x-table.td>
                                @if($u->file_sk)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span>PDF Terverifikasi</span>
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum diunggah</span>
                                @endif
                            </x-table.td>
                            <x-table.td align="right">
                                @if($u->file_sk)
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('dokumen.sk-ormawa', $u) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg font-semibold text-xs transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Buka SK</span>
                                    </a>
                                    <a href="{{ route('dokumen.sk-ormawa', ['user' => $u, 'download' => 1]) }}" class="inline-flex items-center p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" title="Unduh File SK">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                </div>
                                @else
                                <span class="text-xs text-slate-400">-</span>
                                @endif
                            </x-table.td>
                        </x-table.tr>
                        @empty
                        <x-table.empty colspan="7" message="Belum ada arsip Surat Keputusan (SK) Ormawa yang tersimpan." />
                        @endforelse
                    </tbody>
                </x-table>
                <div class="mt-4">{{ $arsipSk->appends(['tab' => 'sk'])->links() }}</div>
            </div>

        </div>
    </div>
    <!-- Modal Tinjauan BKHM atas draf SP dari BPM -->
    <div id="modal-sp-review" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" onclick="closeSpReviewModal()"></div>
            <div class="bg-white rounded-2xl shadow-xl z-10 max-w-lg w-full p-6 relative border border-gray-200">
                <h3 class="text-base font-bold text-gray-900 mb-1" id="sp-review-title">Tinjau Draf SP</h3>
                <p class="text-xs text-gray-500 mb-4" id="sp-review-sub">Nomor: -</p>
                <form id="form-sp-review" method="POST" action="">
                    @csrf
                    <label for="catatan_bkhm" class="block text-xs font-semibold text-gray-700 mb-1">
                        Catatan Tinjauan BKHM <span id="sp-review-required" class="text-red-500 hidden">*</span>
                    </label>
                    <textarea name="catatan_bkhm" id="catatan_bkhm" rows="4" class="w-full border-gray-300 rounded-xl text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Tuliskan catatan tinjauan atau alasan pengembalian..."></textarea>
                    <div class="flex justify-end mt-6 gap-2">
                        <button type="button" onclick="closeSpReviewModal()" class="inline-flex items-center min-h-[44px] px-4 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">Batal</button>
                        <button type="submit" id="sp-review-submit" class="inline-flex items-center min-h-[44px] px-5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openSpReviewModal(id, nomor, mode) {
            const modal = document.getElementById('modal-sp-review');
            const form = document.getElementById('form-sp-review');
            const title = document.getElementById('sp-review-title');
            const sub = document.getElementById('sp-review-sub');
            const submit = document.getElementById('sp-review-submit');
            const req = document.getElementById('sp-review-required');

            form.action = '/bkhm/surat-peringatan/' + id + '/' + mode;
            sub.textContent = 'Nomor: ' + nomor;

            if (mode === 'teruskan') {
                title.textContent = 'Teruskan Draf SP ke WR3';
                submit.textContent = 'Teruskan ke WR3';
                req.classList.add('hidden');
                form.catatan_bkhm.removeAttribute('required');
            } else {
                title.textContent = 'Kembalikan Draf SP ke BPM';
                submit.textContent = 'Kembalikan ke BPM';
                req.classList.remove('hidden');
                form.catatan_bkhm.setAttribute('required', 'required');
            }

            modal.style.display = 'block';
        }

        function closeSpReviewModal() {
            document.getElementById('modal-sp-review').style.display = 'none';
        }
    </script>
</x-app-layout>
