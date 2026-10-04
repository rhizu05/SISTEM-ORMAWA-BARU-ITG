<x-app-layout>
    <x-slot name="header"><h2 class="font-bold text-xl text-slate-900 leading-tight">Dashboard BEM</h2></x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm text-sm font-semibold text-slate-800">
                Selamat Datang kembali, {{ Auth::user()->name }}!
            </div>

            {{-- Widget Agenda Rapat & Koordinasi --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">Agenda Rapat &amp; Koordinasi BEM</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Jadwal pertemuan resmi, evaluasi proker, dan koordinasi pimpinan ormawa.</p>
                    </div>
                    @if(isset($rapats) && count($rapats) > 0)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-bold">
                            {{ count($rapats) }} Agenda
                        </span>
                    @endif
                </div>

                <x-table>
                    <x-table.thead>
                        <x-table.tr>
                            <x-table.th>Waktu Pelaksanaan</x-table.th>
                            <x-table.th>Agenda &amp; Topik</x-table.th>
                            <x-table.th>Lokasi / Ruang</x-table.th>
                            <x-table.th>Penyelenggara</x-table.th>
                            <x-table.th align="center">Tautan</x-table.th>
                        </x-table.tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100 bg-white text-sm">
                        @forelse($rapats as $r)
                            <x-table.tr>
                                <x-table.td class="whitespace-nowrap">
                                    <div class="font-bold text-slate-800 text-xs">{{ \Carbon\Carbon::parse($r->tanggal_rapat)->isoFormat('D MMM Y') }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $r->jam_rapat ?? '' }} WIB</div>
                                </x-table.td>
                                <x-table.td>
                                    <div class="font-bold text-slate-900 text-sm">{{ $r->judul_rapat }}</div>
                                </x-table.td>
                                <x-table.td class="text-xs text-slate-600">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-medium">
                                        <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        <span>{{ $r->lokasi }}</span>
                                    </span>
                                </x-table.td>
                                <x-table.td class="text-xs font-semibold text-slate-700">
                                    {{ $r->penyelenggara->name ?? '-' }}
                                </x-table.td>
                                <x-table.td align="center" class="whitespace-nowrap">
                                    @if($r->link_meeting)
                                        <a href="{{ $r->link_meeting }}" target="_blank" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 min-h-[32px] bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-bold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                            <span>Meeting</span>
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-500 italic">Offline</span>
                                    @endif
                                </x-table.td>
                            </x-table.tr>
                        @empty 
                            <x-table.empty colspan="5" message="Belum ada agenda rapat terdaftar saat ini." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.02)]">
                <h3 class="font-bold text-slate-900 text-sm sm:text-base mb-3">Status Dana Anda</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="border border-slate-200/80 p-4 rounded-xl text-center bg-slate-50/50">
                        <div class="text-xs text-slate-500 font-medium">Total Saldo Diberikan</div>
                        <div class="text-xl font-extrabold text-slate-900 mt-1 font-mono">Rp {{ number_format($saldoAwal,0,',','.') }}</div>
                    </div>
                    <div class="border border-slate-200/80 p-4 rounded-xl text-center bg-slate-50/50">
                        <div class="text-xs text-slate-500 font-medium">Saldo Terpakai &amp; Diproses</div>
                        <div class="text-xl font-extrabold text-amber-600 mt-1 font-mono">Rp {{ number_format($terpakai,0,',','.') }}</div>
                    </div>
                    <div class="border border-slate-200/80 p-4 rounded-xl text-center bg-slate-50/50">
                        <div class="text-xs text-slate-500 font-medium">Sisa Saldo Tersedia</div>
                        <div class="text-xl font-extrabold text-emerald-600 mt-1 font-mono">Rp {{ number_format(Auth::user()->saldo,0,',','.') }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('verifikasi.index') }}" class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:border-blue-300 hover:shadow-md transition block group focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Tahap 1 Verifikasi</div>
                            <div class="text-3xl font-extrabold text-[#1E40AF] mt-1 font-mono">{{ $counts['verifikasi_proposal'] }}</div>
                            <div class="text-sm font-bold text-slate-800 mt-0.5">Antrean Proposal Ormawa</div>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold group-hover:scale-110 transition shadow-2xs">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-700">
                        <span>Buka Antrean Verifikasi</span>
                        <span>&rarr;</span>
                    </div>
                </a>
                <a href="{{ route('proker.index') }}" class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:border-blue-300 hover:shadow-md transition block group focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Rencana Kegiatan</div>
                            <div class="text-3xl font-extrabold text-emerald-600 mt-1 font-mono">{{ \App\Models\ProgramKerja::count() }}</div>
                            <div class="text-sm font-bold text-slate-800 mt-0.5">Program Kerja Ormawa</div>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold group-hover:scale-110 transition shadow-2xs">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-emerald-700">
                        <span>Kelola &amp; Pantau Proker</span>
                        <span>&rarr;</span>
                    </div>
                </a>
            </div>

            {{-- Widget Tabel Verifikasi Proposal --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">Antrean Verifikasi Proposal (BEM)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Proposal kegiatan yang diajukan ormawa dan membutuhkan telaah awal BEM.</p>
                    </div>
                    <a href="{{ route('verifikasi.index') }}" class="text-xs font-bold text-blue-700 hover:text-blue-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded">
                        Buka Semua &rarr;
                    </a>
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
                    <tbody class="divide-y divide-slate-100 bg-white text-sm">
                        @forelse($proposalQueue as $i=>$p)
                            <x-table.tr>
                                <x-table.td align="center" class="text-xs font-bold text-slate-500">{{ $i+1 }}</x-table.td>
                                <x-table.td>
                                    <div class="font-bold text-slate-900 text-sm">{{ $p->nama_kegiatan }}</div>
                                </x-table.td>
                                <x-table.td>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold">
                                        <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        <span>{{ $p->user->name }}</span>
                                    </span>
                                </x-table.td>
                                <x-table.td class="text-xs text-slate-600 whitespace-nowrap">
                                    {{ $p->tanggal_pengajuan ?? $p->created_at->format('d/m/Y') }}
                                </x-table.td>
                                <x-table.td class="font-mono font-extrabold text-slate-900 text-xs whitespace-nowrap">
                                    Rp {{ number_format($p->dana_diajukan,0,',','.') }}
                                </x-table.td>
                                <x-table.td align="center" class="whitespace-nowrap">
                                    <a href="{{ route('verifikasi.show',$p) }}" 
                                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 min-h-[36px] bg-[#0B1528] hover:bg-[#1E3A8A] text-white rounded-xl text-xs font-bold shadow-2xs transition active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                                        <span>Telaah</span>
                                        <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </x-table.td>
                            </x-table.tr>
                        @empty
                            <x-table.empty colspan="6" message="Tidak ada proposal untuk diverifikasi saat ini." />
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Jadwal Terpadu Fasilitas &amp; Barang</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pemantauan agenda dan ketersediaan sarana prasarana kampus</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 text-xs mt-2 sm:mt-0">
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
