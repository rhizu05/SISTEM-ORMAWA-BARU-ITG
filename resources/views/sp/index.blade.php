<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Surat Peringatan Resmi') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Daftar surat peringatan resmi institusi yang ditujukan kepada organisasi Anda</p>
            </div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-xs font-semibold text-gray-600 hover:text-gray-900 bg-white border border-gray-300 px-3 py-1.5 rounded-lg shadow-sm hover:bg-gray-50 transition">
                &larr; Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Banner info konsekuensi SP -->
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="space-y-1">
                    <p class="font-bold">Informasi Penegakan Peraturan Organisasi Mahasiswa</p>
                    <p class="text-amber-800">
                        Surat Peringatan diterbitkan oleh pimpinan institusi (BKHM/BPM) atas pelanggaran kepatuhan operasional seperti keterlambatan LPJ atau pelanggaran regulasi kampus. Harap segera tindak lanjuti sanksi yang tertulis agar status keaktifan dan hak pendanaan organisasi tetap terjaga.
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6">
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Riwayat Surat Peringatan Organisasi</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar sanksi dan ketetapan disiplin yang tercatat pada lembaga Anda</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        Total: {{ $spList->total() }} Dokumen
                    </span>
                </div>

                @if($spList->isEmpty())
                    <div class="py-12 px-4 text-center">
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-base font-bold text-slate-800">Status Organisasi Baik</h4>
                        <p class="text-xs text-slate-500 max-w-md mx-auto mt-1">
                            Organisasi Anda tidak memiliki catatan surat peringatan aktif. Pertahankan kepatuhan pelaporan LPJ dan regulasi kegiatan kemahasiswaan!
                        </p>
                    </div>
                @else
                    <x-table>
                        <x-table.thead>
                            <tr>
                                <x-table.th align="center">Tingkat</x-table.th>
                                <x-table.th>Nomor Surat</x-table.th>
                                <x-table.th>Tanggal Terbit</x-table.th>
                                <x-table.th>Perihal &amp; Alasan</x-table.th>
                                <x-table.th>Sanksi</x-table.th>
                                <x-table.th>Penerbit</x-table.th>
                                <x-table.th align="right">Aksi</x-table.th>
                            </tr>
                        </x-table.thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($spList as $sp)
                            <x-table.tr>
                                <x-table.td align="center">
                                    @php
                                        $spClass = match($sp->tingkat) {
                                            'SP-3' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'SP-2' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            default => 'bg-amber-50 text-amber-800 border-amber-200'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $spClass }}">
                                        {{ $sp->tingkat }}
                                    </span>
                                </x-table.td>
                                <x-table.td>
                                    <span class="font-mono text-xs font-semibold text-slate-900">{{ $sp->nomor_surat }}</span>
                                </x-table.td>
                                <x-table.td>
                                    <span class="text-xs text-slate-600">{{ $sp->tanggal_surat ? $sp->tanggal_surat->format('d M Y') : '-' }}</span>
                                </x-table.td>
                                <x-table.td>
                                    <p class="font-semibold text-slate-900 line-clamp-1">{{ $sp->perihal }}</p>
                                    <p class="text-slate-500 text-xs line-clamp-1 mt-0.5">{{ $sp->alasan_singkat }}</p>
                                </x-table.td>
                                <x-table.td>
                                    <span class="text-xs text-rose-700 font-medium line-clamp-2">{{ $sp->sanksi }}</span>
                                </x-table.td>
                                <x-table.td>
                                    <span class="text-xs text-slate-700 font-medium">{{ $sp->penandatangan ?? ($sp->creator->name ?? 'Institusi') }}</span>
                                </x-table.td>
                                <x-table.td align="right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('sp.saya.show', $sp) }}" class="inline-flex items-center px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-lg text-xs transition">
                                            Detail &rarr;
                                        </a>
                                        <a href="{{ route('sp.saya.pdf', $sp) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold border border-slate-200 rounded-lg text-xs transition" title="Unduh PDF Resmi">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            PDF
                                        </a>
                                    </div>
                                </x-table.td>
                            </x-table.tr>
                            @endforeach
                        </tbody>
                    </x-table>
                    <div class="mt-4">
                        {{ $spList->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
