<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Validasi & Pengesahan Surat Peringatan (WR3)') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Tinjau usulan surat peringatan dari BKHM dan sahkan menggunakan Tanda Tangan Digital Kriptografis resmi.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Summary KPI -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Menunggu Validasi</div>
                        <div class="text-2xl font-bold text-amber-600 mt-1">{{ $antrean->count() }}</div>
                        <div class="text-[11px] text-gray-400 mt-0.5">Membutuhkan persetujuan WR3</div>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Disetujui &amp; Terbit</div>
                        <div class="text-2xl font-bold text-emerald-600 mt-1">
                            {{ $riwayat->where('status', 'disetujui')->count() }}
                        </div>
                        <div class="text-[11px] text-gray-400 mt-0.5">Sah &amp; TTD Digital Aktif</div>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Ditolak / Revisi BKHM</div>
                        <div class="text-2xl font-bold text-rose-600 mt-1">
                            {{ $riwayat->where('status', 'ditolak')->count() }}
                        </div>
                        <div class="text-[11px] text-gray-400 mt-0.5">Dikembalikan ke staf BKHM</div>
                    </div>
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Antrean Utama: Menunggu Validasi WR3 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden p-6">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2 border-b border-slate-100 pb-4 mb-6">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <h3 class="font-bold text-slate-900 text-base">Antrean Surat Peringatan Masuk</h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Draf SP dari BKHM / BPM yang memerlukan tinjauan materi dan pengesahan TTD Digital WR3</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        {{ $antrean->count() }} Dokumen Menunggu
                    </span>
                </div>

                @if($antrean->isEmpty())
                    <div class="py-10 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto text-emerald-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="font-medium text-slate-600">Tidak ada antrean surat peringatan saat ini</p>
                        <p class="text-xs text-slate-400 mt-1">Seluruh draf SP yang masuk dari BKHM maupun BPM telah ditinjau dan divalidasi.</p>
                    </div>
                @else
                    <x-table>
                        <x-table.thead>
                            <tr>
                                <x-table.th align="center">Tingkat</x-table.th>
                                <x-table.th>Nomor Surat</x-table.th>
                                <x-table.th>Sasaran / Penerima</x-table.th>
                                <x-table.th>Alasan &amp; Perihal</x-table.th>
                                <x-table.th>Draf Dibuat Oleh</x-table.th>
                                <x-table.th>Tanggal Surat</x-table.th>
                                <x-table.th align="right">Aksi</x-table.th>
                            </tr>
                        </x-table.thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($antrean as $sp)
                            <x-table.tr>
                                <x-table.td align="center">
                                    @php
                                        $tingkatClass = match($sp->tingkat) {
                                            'SP-3' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'SP-2' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            default => 'bg-amber-50 text-amber-800 border-amber-200'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-bold text-xs border {{ $tingkatClass }}">
                                        {{ $sp->tingkat }}
                                    </span>
                                </x-table.td>
                                <x-table.td>
                                    <span class="font-mono text-xs font-semibold text-slate-900">{{ $sp->nomor_surat }}</span>
                                </x-table.td>
                                <x-table.td>
                                    @if($sp->isMahasiswa())
                                        <div class="font-semibold text-slate-900">{{ $sp->nama_penerima }}</div>
                                        <div class="text-[11px] text-slate-500 font-mono">{{ $sp->identitas_penerima }}</div>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] bg-slate-100 text-slate-700 font-semibold border border-slate-200/60 mt-0.5">Mahasiswa</span>
                                    @else
                                        <div class="font-semibold text-slate-900">{{ $sp->target->name ?? '-' }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $sp->target->email ?? '-' }}</div>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] bg-indigo-50 text-indigo-700 font-semibold border border-indigo-200/60 mt-0.5">Organisasi Mahasiswa</span>
                                    @endif
                                </x-table.td>
                                <x-table.td>
                                    <div class="font-semibold text-slate-900 line-clamp-1">{{ $sp->perihal }}</div>
                                    <div class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $sp->alasan_singkat }}</div>
                                </x-table.td>
                                <x-table.td>
                                    <div class="font-medium text-slate-800 text-xs">{{ $sp->creator->name ?? 'BKHM' }}</div>
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold border mt-0.5 {{ $sp->penerbit_label === 'BPM' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-slate-50 text-slate-700 border-slate-200' }}">{{ $sp->penerbit_label }}</span>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $sp->created_at->format('d/m/Y H:i') }}</div>
                                </x-table.td>
                                <x-table.td>
                                    <span class="text-xs text-slate-700">{{ \Carbon\Carbon::parse($sp->tanggal_surat)->translatedFormat('d F Y') }}</span>
                                </x-table.td>
                                <x-table.td align="right">
                                    <a href="{{ route('wr3.sp.show', $sp) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold text-xs shadow-sm transition">
                                        <span>Tinjau</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                </x-table.td>
                            </x-table.tr>
                            @endforeach
                        </tbody>
                    </x-table>
                @endif
            </div>

            <!-- Riwayat Keputusan WR3 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden p-6">
                <div class="border-b border-slate-100 pb-4 mb-6">
                    <h3 class="font-bold text-slate-900 text-base">Riwayat Validasi &amp; Pengesahan Dokumen</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar arsip keputusan surat peringatan yang telah disetujui atau dikembalikan oleh WR3</p>
                </div>

                <x-table>
                    <x-table.thead>
                        <tr>
                            <x-table.th align="center">Tingkat</x-table.th>
                            <x-table.th align="center">Status</x-table.th>
                            <x-table.th>Nomor Surat</x-table.th>
                            <x-table.th>Sasaran / Penerima</x-table.th>
                            <x-table.th>Tanggal Validasi</x-table.th>
                            <x-table.th>Catatan WR3</x-table.th>
                            <x-table.th align="right">Aksi</x-table.th>
                        </tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($riwayat as $sp)
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
                                @if($sp->isDisetujui())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-xs bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Disetujui
                                    </span>
                                @elseif($sp->isDitolak())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-xs bg-rose-50 text-rose-700 border border-rose-200">
                                        <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Dikembalikan
                                    </span>
                                @endif
                            </x-table.td>
                            <x-table.td>
                                <span class="font-mono text-xs font-semibold text-slate-900">{{ $sp->nomor_surat }}</span>
                            </x-table.td>
                            <x-table.td>
                                @if($sp->isMahasiswa())
                                    <div class="font-semibold text-slate-900">{{ $sp->nama_penerima }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">{{ $sp->identitas_penerima }}</div>
                                @else
                                    <div class="font-semibold text-slate-900">{{ $sp->target->name ?? '-' }}</div>
                                @endif
                            </x-table.td>
                            <x-table.td>
                                @if($sp->validated_at)
                                    <div class="text-xs text-slate-700">{{ $sp->validated_at->translatedFormat('d F Y H:i') }}</div>
                                    <div class="text-[10px] text-slate-400">Oleh: {{ $sp->validator->name ?? 'WR3' }}</div>
                                @else
                                    -
                                @endif
                            </x-table.td>
                            <x-table.td>
                                <span class="text-xs text-slate-600 max-w-xs line-clamp-1">{{ $sp->catatan_wr3 ?: '-' }}</span>
                            </x-table.td>
                            <x-table.td align="right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('wr3.sp.show', $sp) }}" class="inline-flex items-center px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold text-xs transition">
                                        Lihat
                                    </a>
                                    @if($sp->isDisetujui())
                                    <a href="{{ route('bkhm.sp.pdf', $sp) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg font-semibold text-xs transition">
                                        PDF
                                    </a>
                                    @endif
                                </div>
                            </x-table.td>
                        </x-table.tr>
                        @empty
                        <x-table.empty colspan="7" message="Belum ada riwayat keputusan surat peringatan." />
                        @endforelse
                    </tbody>
                </x-table>
                <div class="mt-4">{{ $riwayat->links() }}</div>
            </div>

        </div>
    </div>
</x-app-layout>
