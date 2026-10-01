<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Dashboard BEM</h2></x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-4 rounded shadow">Selamat Datang kembali, {{ Auth::user()->name }}!</div>

            {{-- Widget Agenda Rapat & Koordinasi --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
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
                <div class="overflow-x-auto skin-scrollbar">
                    <table class="min-w-full divide-y divide-slate-100 text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider select-none">
                                <th scope="col" class="py-3 px-5">Waktu Pelaksanaan</th>
                                <th scope="col" class="py-3 px-5">Agenda &amp; Topik</th>
                                <th scope="col" class="py-3 px-5">Lokasi / Ruang</th>
                                <th scope="col" class="py-3 px-5">Penyelenggara</th>
                                <th scope="col" class="py-3 px-5 text-center">Tautan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($rapats as $r)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-5 whitespace-nowrap">
                                        <div class="font-bold text-slate-800 text-xs">{{ \Carbon\Carbon::parse($r->tanggal_rapat)->isoFormat('D MMM Y') }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $r->jam_rapat ?? '' }} WIB</div>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-slate-900 text-sm">{{ $r->judul_rapat }}</div>
                                    </td>
                                    <td class="py-3.5 px-5 text-xs text-slate-600">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100/80 text-slate-700 font-medium">
                                            🏢 {{ $r->lokasi }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5 text-xs font-semibold text-slate-700">
                                        {{ $r->penyelenggara->name ?? '-' }}
                                    </td>
                                    <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                        @if($r->link_meeting)
                                            <a href="{{ $r->link_meeting }}" target="_blank" 
                                               class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-bold transition">
                                                <span>Meeting</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Offline</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty 
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 text-xs italic">
                                        Belum ada agenda rapat terdaftar saat ini.
                                    </td>
                                </tr> 
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
                <a href="{{ route('verifikasi.index') }}" class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:border-blue-300 hover:shadow-md transition block group">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Tahap 1 Verifikasi</div>
                            <div class="text-3xl font-extrabold text-[#1E40AF] mt-1">{{ $counts['verifikasi_proposal'] }}</div>
                            <div class="text-sm font-bold text-slate-800 mt-0.5">Antrean Proposal Ormawa</div>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xl group-hover:scale-110 transition">
                            📑
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-700">
                        <span>Buka Antrean Verifikasi</span>
                        <span>&rarr;</span>
                    </div>
                </a>
                <a href="{{ route('proker.index') }}" class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] hover:border-blue-300 hover:shadow-md transition block group">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-wider text-slate-400">Rencana Kegiatan</div>
                            <div class="text-3xl font-extrabold text-emerald-600 mt-1">📋</div>
                            <div class="text-sm font-bold text-slate-800 mt-0.5">Program Kerja Ormawa</div>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl group-hover:scale-110 transition">
                            🎯
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-emerald-700">
                        <span>Kelola &amp; Pantau Proker</span>
                        <span>&rarr;</span>
                    </div>
                </a>
            </div>

            {{-- Widget Tabel Verifikasi Proposal --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">Antrean Verifikasi Proposal (BEM)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Proposal kegiatan yang diajukan ormawa dan membutuhkan telaah awal BEM.</p>
                    </div>
                    <a href="{{ route('verifikasi.index') }}" class="text-xs font-bold text-blue-700 hover:text-blue-900 transition">
                        Buka Semua &rarr;
                    </a>
                </div>
                <div class="overflow-x-auto skin-scrollbar">
                    <table class="min-w-full divide-y divide-slate-100 text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider select-none">
                                <th scope="col" class="py-3 px-5 text-center w-12">No</th>
                                <th scope="col" class="py-3 px-5">Nama Kegiatan</th>
                                <th scope="col" class="py-3 px-5">Ormawa Pengusul</th>
                                <th scope="col" class="py-3 px-5">Jadwal Acara</th>
                                <th scope="col" class="py-3 px-5">Dana Diajukan</th>
                                <th scope="col" class="py-3 px-5 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($proposalQueue as $i=>$p)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-5 text-center text-xs font-bold text-slate-400">{{ $i+1 }}</td>
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-slate-900 text-sm">{{ $p->nama_kegiatan }}</div>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-bold">
                                            🏛️ {{ $p->user->name }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5 text-xs text-slate-600 whitespace-nowrap">
                                        {{ $p->tanggal_pengajuan ?? $p->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3.5 px-5 font-mono font-extrabold text-slate-900 text-xs whitespace-nowrap">
                                        Rp {{ number_format($p->dana_diajukan,0,',','.') }}
                                    </td>
                                    <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                        <a href="{{ route('verifikasi.show',$p) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#0B1528] hover:bg-[#1E3A8A] text-white rounded-xl text-xs font-bold shadow-2xs transition active:scale-95">
                                            <span>Telaah</span>
                                            <svg class="w-3 h-3 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400 text-xs italic">
                                        Tidak ada proposal untuk diverifikasi saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 border-b pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900">Jadwal Terpadu Fasilitas & Barang</h3>
                        <p class="text-xs text-slate-500 mt-1">Klik pada agenda untuk melihat detail kegiatan</p>
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
                </div>>
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
