<x-app-layout>
    <x-slot name="header"><h2 class="font-bold text-xl text-slate-900 leading-tight">Dashboard WR3</h2></x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 mb-2">
                        Pimpinan Kemahasiswaan
                    </span>
                    <h3 class="text-xl font-bold text-slate-900">Selamat Datang, Wakil Rektor III</h3>
                    <p class="text-xs text-slate-500 mt-1">Portal otoritas pengesahan proposal kegiatan, validasi surat peringatan, dan pemantauan anggaran ormawa.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('verifikasi.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition min-h-[44px] focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-600 focus-visible:ring-offset-2">
                        Antrean Verifikasi Proposal
                    </a>
                </div>
            </div>

            <!-- Widget Agenda Rapat -->
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Agenda Rapat &amp; Koordinasi</h3>
                    <span class="text-xs text-slate-500">{{ count($rapats) }} Agenda Tercatat</span>
                </div>

                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            <x-table.th>Waktu</x-table.th>
                            <x-table.th>Agenda</x-table.th>
                            <x-table.th>Lokasi</x-table.th>
                            <x-table.th>Penyelenggara</x-table.th>
                            <x-table.th align="center">Link</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-sm">
                        @forelse($rapats as $r)
                            <x-table.tr>
                                <x-table.td class="text-xs text-slate-700 whitespace-nowrap">{{ $r->tanggal_rapat }} {{ $r->jam_rapat }}</x-table.td>
                                <x-table.td class="font-bold text-slate-900">{{ $r->judul_rapat }}</x-table.td>
                                <x-table.td class="text-xs text-slate-600">{{ $r->lokasi }}</x-table.td>
                                <x-table.td class="text-xs font-semibold text-slate-700">{{ $r->penyelenggara->name ?? '-' }}</x-table.td>
                                <x-table.td align="center">
                                    @if($r->link_meeting)
                                        <a href="{{ $r->link_meeting }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline font-semibold text-xs">Link</a>
                                    @else
                                        <span class="text-slate-400 text-xs italic">-</span>
                                    @endif
                                </x-table.td>
                            </x-table.tr>
                        @empty 
                            <x-table.empty colspan="5" message="Belum ada agenda rapat terdaftar." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-extrabold font-mono text-indigo-600">{{ $proposalQueue->count() }}</div>
                        <div class="text-sm font-semibold text-slate-800 mt-1">Verifikasi Proposal</div>
                        <div class="text-xs text-slate-500">Tugas Verifikasi Anda</div>
                    </div>
                    <a href="{{ route('verifikasi.index') }}" class="inline-flex items-center justify-center p-3 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-100 transition min-h-[44px] min-w-[44px] focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </a>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-extrabold font-mono text-amber-600">{{ $pendingSpCount ?? 0 }}</div>
                        <div class="text-sm font-semibold text-slate-800 mt-1">Validasi Surat Peringatan</div>
                        <div class="text-xs text-slate-500">Draf Masuk dari BKHM</div>
                    </div>
                    <a href="{{ route('wr3.sp.index') }}" class="inline-flex items-center justify-center p-3 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-100 transition min-h-[44px] min-w-[44px] focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </a>
                </div>
            </div>

            @if(isset($pendingSpQueue) && $pendingSpQueue->count() > 0)
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">Surat Peringatan Menunggu Tanda Tangan Anda</h3>
                    </div>
                    <a href="{{ route('wr3.sp.index') }}" class="inline-flex items-center min-h-[36px] px-2 text-xs font-bold text-indigo-700 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-600 rounded">
                        Lihat Semua ({{ $pendingSpCount }}) &rarr;
                    </a>
                </div>

                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            <x-table.th align="center">Tingkat</x-table.th>
                            <x-table.th>Nomor Surat</x-table.th>
                            <x-table.th>Penerima</x-table.th>
                            <x-table.th>Alasan Singkat</x-table.th>
                            <x-table.th align="right">Tindakan</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-xs">
                        @foreach($pendingSpQueue as $sp)
                        <x-table.tr>
                            <x-table.td align="center">
                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 font-extrabold text-[10px] border border-amber-200">
                                    {{ $sp->tingkat }}
                                </span>
                            </x-table.td>
                            <x-table.td class="font-mono text-slate-900 font-bold whitespace-nowrap">{{ $sp->nomor_surat }}</x-table.td>
                            <x-table.td class="font-semibold text-slate-800">{{ $sp->nama_penerima }}</x-table.td>
                            <x-table.td class="text-slate-600 truncate max-w-xs">{{ $sp->alasan_singkat }}</x-table.td>
                            <x-table.td align="right" class="whitespace-nowrap">
                                <a href="{{ route('wr3.sp.show', $sp) }}" class="inline-flex items-center justify-center min-h-[36px] px-3.5 py-1.5 bg-[#0B1528] hover:bg-[#1E3A8A] text-white rounded-lg font-bold text-xs shadow-2xs transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                                    <span>Validasi</span>
                                    <span>&rarr;</span>
                                </a>
                            </x-table.td>
                        </x-table.tr>
                        @endforeach
                    </tbody>
                </x-table>
            </div>
            @endif

            {{-- Widget Verifikasi Proposal Pimpinan --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-6 space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">Antrean Persetujuan Proposal (WR3)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Tahap pengesahan akhir proposal kegiatan mahasiswa tingkat pimpinan kampus.</p>
                    </div>
                    <a href="{{ route('verifikasi.index') }}" class="inline-flex items-center min-h-[36px] px-2 text-xs font-bold text-blue-700 hover:text-blue-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 rounded">Semua &rarr;</a>
                </div>

                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            <x-table.th align="center" class="w-12">No</x-table.th>
                            <x-table.th>Nama Kegiatan</x-table.th>
                            <x-table.th>Ormawa Pengusul</x-table.th>
                            <x-table.th>Jadwal Acara</x-table.th>
                            <x-table.th>Dana Diajukan</x-table.th>
                            <x-table.th align="center">Tindakan</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100 text-sm bg-white">
                        @forelse($proposalQueue as $i=>$p)
                            <x-table.tr>
                                <x-table.td align="center" class="text-xs font-bold text-slate-500">{{ $i+1 }}</x-table.td>
                                <x-table.td class="font-bold text-slate-900 text-sm">{{ $p->nama_kegiatan }}</x-table.td>
                                <x-table.td>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold">
                                        <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        <span>{{ $p->user->name }}</span>
                                    </span>
                                </x-table.td>
                                <x-table.td class="text-xs text-slate-600 whitespace-nowrap">{{ $p->tanggal_pengajuan ?? $p->created_at->format('d/m/Y') }}</x-table.td>
                                <x-table.td class="font-mono font-extrabold text-slate-900 text-xs whitespace-nowrap">Rp {{ number_format($p->dana_diajukan,0,',','.') }}</x-table.td>
                                <x-table.td align="center" class="whitespace-nowrap">
                                    <a href="{{ route('verifikasi.show',$p) }}" 
                                       class="inline-flex items-center justify-center min-h-[36px] gap-1 px-3.5 py-1.5 bg-[#0B1528] hover:bg-[#1E3A8A] text-white rounded-xl text-xs font-bold shadow-2xs transition active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                                        <span>Validasi</span>
                                        <svg class="w-3 h-3 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </x-table.td>
                            </x-table.tr>
                        @empty
                            <x-table.empty colspan="6" message="Tidak ada proposal untuk diverifikasi saat ini." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            {{-- Widget Manajemen Saldo Ormawa --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-6 space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Monitoring Saldo Kas Ormawa &amp; Lembaga</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar rincian pagu anggaran dan realisasi kas seluruh organisasi mahasiswa ITG</p>
                </div>

                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            <x-table.th align="center" class="w-12">No</x-table.th>
                            <x-table.th>Nama Ormawa / Lembaga</x-table.th>
                            <x-table.th>Saldo Awal</x-table.th>
                            <x-table.th>Total Terpakai &amp; Diproses</x-table.th>
                            <x-table.th>Sisa Saldo Tersedia</x-table.th>
                            <x-table.th align="center">Status Aktivitas</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100 text-sm bg-white">
                        @forelse($usersWithSaldo as $i=>$u)
                            <x-table.tr>
                                <x-table.td align="center" class="text-xs font-bold text-slate-500">{{ $i+1 }}</x-table.td>
                                <x-table.td>
                                    <div class="font-bold text-slate-800 text-xs">{{ $u['name'] }}</div>
                                    <div class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">{{ $u['role'] }}</div>
                                </x-table.td>
                                <x-table.td class="font-mono text-slate-600 text-xs whitespace-nowrap">Rp {{ number_format($u['saldo_awal'],0,',','.') }}</x-table.td>
                                <x-table.td class="font-mono font-bold text-amber-600 text-xs whitespace-nowrap">Rp {{ number_format($u['terpakai'],0,',','.') }}</x-table.td>
                                <x-table.td class="font-mono font-extrabold text-emerald-600 text-xs whitespace-nowrap">Rp {{ number_format($u['saldo'],0,',','.') }}</x-table.td>
                                <x-table.td align="center" class="whitespace-nowrap">
                                    @if($u['terpakai'] > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">
                                            ● Ada Pengajuan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[11px]">
                                            Belum Ada Pengajuan
                                        </span>
                                    @endif
                                </x-table.td>
                            </x-table.tr>
                        @empty
                            <x-table.empty colspan="6" message="Belum ada data saldo." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            {{-- Widget Riwayat Perubahan Saldo --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-6 space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Audit Log Mutasi Saldo Kas</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Catatan riwayat transaksi dan penyesuaian nominal saldo ormawa</p>
                </div>

                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            <x-table.th>Waktu Pencatatan</x-table.th>
                            <x-table.th>Target Akun</x-table.th>
                            <x-table.th>Aktor Eksekusi</x-table.th>
                            <x-table.th>Perubahan Nominal</x-table.th>
                            <x-table.th>Alasan / Catatan</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-xs">
                        @forelse($saldoHistori as $history)
                            <x-table.tr>
                                <x-table.td class="text-slate-600 whitespace-nowrap">{{ $history->created_at->format('d/m/Y H:i') }}</x-table.td>
                                <x-table.td class="font-bold text-slate-800">{{ $history->user->name }}</x-table.td>
                                <x-table.td class="text-slate-700">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-slate-100 font-semibold text-[11px] text-slate-700">
                                        <svg class="w-3 h-3 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <span>{{ $history->actor->name }}</span>
                                    </span>
                                </x-table.td>
                                <x-table.td class="font-mono whitespace-nowrap">
                                    <span class="text-slate-500">Rp {{ number_format($history->nominal_sebelum, 0, ',', '.') }}</span>
                                    <span class="mx-1 text-slate-400">&rarr;</span>
                                    <span class="font-extrabold text-emerald-600">Rp {{ number_format($history->nominal_sesudah, 0, ',', '.') }}</span>
                                </x-table.td>
                                <x-table.td class="text-slate-600">{{ $history->catatan }}</x-table.td>
                            </x-table.tr>
                        @empty
                            <x-table.empty colspan="5" message="Belum ada riwayat perubahan saldo." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Jadwal Terpadu Fasilitas &amp; Barang</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Klik pada agenda untuk melihat detail kegiatan dan peminjaman fasilitas</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 text-xs mt-3 sm:mt-0">
                        <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                            <span class="w-3 h-3 rounded-full bg-indigo-500 shadow-sm ring-2 ring-indigo-100"></span> 
                            Fasilitas
                        </span>
                        <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                            <span class="w-3 h-3 rounded-full bg-amber-500 shadow-sm ring-2 ring-amber-100"></span> 
                            Barang
                        </span>
                    </div>
                </div>
                <x-calendar-style />
                <div class="skin-calendar-wrapper">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
        const el=document.getElementById('calendar');
        if(!el) return;
        const cal=new FullCalendar.Calendar(el,{locale:'id',initialView:'dayGridMonth',headerToolbar:{left:'prev,next today',center:'title',right:'dayGridMonth,timeGridWeek'},events:[
            @foreach($calendarTempat as $c){title:'Tempat: {{ $c->ruangan->nama_ruangan ?? $c->nama_kegiatan }}',start:'{{ $c->tgl_mulai }}',end:'{{ \Carbon\Carbon::parse($c->tgl_selesai)->addDay()->format('Y-m-d') }}',color:'#4f46e5'},
            @endforeach
            @foreach($calendarBarang as $c){title:'Barang: {{ $c->nama_kegiatan }}',start:'{{ $c->tgl_mulai }}',end:'{{ \Carbon\Carbon::parse($c->tgl_selesai)->addDay()->format('Y-m-d') }}',color:'#f59e0b'},
            @endforeach
        ]});cal.render();
    });
    </script>
</x-app-layout>