<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Dashboard WR3</h2></x-slot>
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
                    <a href="{{ route('verifikasi.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition min-h-[40px]">
                        Antrean Verifikasi Proposal
                    </a>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-3">Agenda Rapat & Koordinasi</h3>
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50"><tr><th class="p-2 border">Waktu</th><th class="p-2 border">Agenda</th><th class="p-2 border">Lokasi</th><th class="p-2 border">Penyelenggara</th><th class="p-2 border">Link</th></tr></thead>
                    <tbody>
                    @forelse($rapats as $r)
                    <tr><td class="p-2 border">{{ $r->tanggal_rapat }} {{ $r->jam_rapat }}</td><td class="p-2 border">{{ $r->judul_rapat }}</td><td class="p-2 border">{{ $r->lokasi }}</td><td class="p-2 border">{{ $r->penyelenggara->name ?? '-' }}</td><td class="p-2 border">@if($r->link_meeting)<a href="{{ $r->link_meeting }}" target="_blank" class="text-indigo-600 underline">Link</a>@else - @endif</td></tr>
                    @empty <tr><td colspan="5" class="p-4 text-center text-gray-500">Belum ada agenda rapat terdaftar.</td></tr> @endforelse
                    </tbody>
                </table>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white p-6 rounded shadow flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold text-indigo-600">{{ $proposalQueue->count() }}</div>
                        <div class="text-sm font-semibold text-gray-700 mt-1">Verifikasi Proposal</div>
                        <div class="text-xs text-gray-400">Tugas Verifikasi Anda</div>
                    </div>
                    <a href="{{ route('verifikasi.index') }}" class="p-3 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </a>
                </div>

                <div class="bg-white p-6 rounded shadow flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold text-amber-600">{{ $pendingSpCount ?? 0 }}</div>
                        <div class="text-sm font-semibold text-gray-700 mt-1">Validasi Surat Peringatan</div>
                        <div class="text-xs text-gray-400">Draf Masuk dari BKHM</div>
                    </div>
                    <a href="{{ route('wr3.sp.index') }}" class="p-3 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </a>
                </div>
            </div>

            @if(isset($pendingSpQueue) && $pendingSpQueue->count() > 0)
            <div class="bg-amber-50/60 border border-amber-200/80 p-5 rounded-2xl shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <h3 class="font-bold text-slate-900 text-sm">Surat Peringatan Menunggu Tanda Tangan Anda</h3>
                    </div>
                    <a href="{{ route('wr3.sp.index') }}" class="text-xs font-bold text-indigo-700 hover:underline">
                        Lihat Semua ({{ $pendingSpCount }}) &rarr;
                    </a>
                </div>
                <div class="overflow-x-auto bg-white rounded-xl border border-amber-200/90 shadow-2xs">
                    <table class="min-w-full text-xs divide-y divide-amber-100 text-left">
                        <thead class="bg-amber-100/50 text-amber-900 font-bold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th scope="col" class="py-2.5 px-4 text-center">Tingkat</th>
                                <th scope="col" class="py-2.5 px-4">Nomor Surat</th>
                                <th scope="col" class="py-2.5 px-4">Penerima</th>
                                <th scope="col" class="py-2.5 px-4">Alasan Singkat</th>
                                <th scope="col" class="py-2.5 px-4 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-100">
                            @foreach($pendingSpQueue as $sp)
                            <tr class="hover:bg-amber-50/50 transition">
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full bg-amber-200/70 text-amber-900 font-extrabold text-[10px]">
                                        {{ $sp->tingkat }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-900 font-bold">{{ $sp->nomor_surat }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-800">{{ $sp->nama_penerima }}</td>
                                <td class="py-3 px-4 text-slate-600 truncate max-w-xs">{{ $sp->alasan_singkat }}</td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <a href="{{ route('wr3.sp.show', $sp) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-[#0B1528] hover:bg-[#1E3A8A] text-white rounded-lg font-bold text-xs shadow-2xs transition">
                                        <span>Validasi</span>
                                        <span>&rarr;</span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Widget Verifikasi Proposal Pimpinan --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">Antrean Persetujuan Proposal (WR3)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Tahap pengesahan akhir proposal kegiatan mahasiswa tingkat pimpinan kampus.</p>
                    </div>
                    <a href="{{ route('verifikasi.index') }}" class="text-xs font-bold text-blue-700 hover:text-blue-900 transition">Semua &rarr;</a>
                </div>
                <div class="overflow-x-auto skin-scrollbar">
                    <table class="min-w-full divide-y divide-slate-100 text-left border-collapse text-xs">
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
                                    <td class="py-3.5 px-5 font-bold text-slate-900 text-sm">{{ $p->nama_kegiatan }}</td>
                                    <td class="py-3.5 px-5">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-bold">
                                            🏛️ {{ $p->user->name }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-5 text-xs text-slate-600 whitespace-nowrap">{{ $p->tanggal_pengajuan ?? $p->created_at->format('d/m/Y') }}</td>
                                    <td class="py-3.5 px-5 font-mono font-extrabold text-slate-900 text-xs whitespace-nowrap">Rp {{ number_format($p->dana_diajukan,0,',','.') }}</td>
                                    <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                        <a href="{{ route('verifikasi.show',$p) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#0B1528] hover:bg-[#1E3A8A] text-white rounded-xl text-xs font-bold shadow-2xs transition active:scale-95">
                                            <span>Validasi</span>
                                            <svg class="w-3 h-3 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400 text-xs italic">Tidak ada proposal untuk diverifikasi saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Widget Manajemen Saldo Ormawa --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Monitoring Saldo Kas Ormawa &amp; Lembaga</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar rincian pagu anggaran dan realisasi kas seluruh organisasi mahasiswa ITG</p>
                </div>
                <div class="overflow-x-auto skin-scrollbar">
                    <table class="min-w-full divide-y divide-slate-100 text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider select-none">
                                <th scope="col" class="py-3 px-5 text-center w-12">No</th>
                                <th scope="col" class="py-3 px-5">Nama Ormawa / Lembaga</th>
                                <th scope="col" class="py-3 px-5">Saldo Awal</th>
                                <th scope="col" class="py-3 px-5">Total Terpakai &amp; Diproses</th>
                                <th scope="col" class="py-3 px-5">Sisa Saldo Tersedia</th>
                                <th scope="col" class="py-3 px-5 text-center">Status Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($usersWithSaldo as $i=>$u)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3.5 px-5 text-center text-xs font-bold text-slate-400">{{ $i+1 }}</td>
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-slate-800 text-xs">{{ $u['name'] }}</div>
                                        <div class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">{{ $u['role'] }}</div>
                                    </td>
                                    <td class="py-3.5 px-5 font-mono text-slate-600 text-xs">Rp {{ number_format($u['saldo_awal'],0,',','.') }}</td>
                                    <td class="py-3.5 px-5 font-mono font-bold text-amber-600 text-xs">Rp {{ number_format($u['terpakai'],0,',','.') }}</td>
                                    <td class="py-3.5 px-5 font-mono font-extrabold text-emerald-600 text-xs">Rp {{ number_format($u['saldo'],0,',','.') }}</td>
                                    <td class="py-3.5 px-5 text-center whitespace-nowrap">
                                        @if($u['terpakai'] > 0)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">
                                                ● Ada Pengajuan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[11px]">
                                                Belum Ada Pengajuan
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-400 text-xs italic">Belum ada data saldo.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Widget Riwayat Perubahan Saldo --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 text-sm sm:text-base">Audit Log Mutasi Saldo Kas</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Catatan riwayat transaksi dan penyesuaian nominal saldo ormawa</p>
                </div>
                <div class="overflow-x-auto skin-scrollbar">
                    <table class="min-w-full divide-y divide-slate-100 text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider select-none">
                                <th scope="col" class="py-3 px-5">Waktu Pencatatan</th>
                                <th scope="col" class="py-3 px-5">Target Akun</th>
                                <th scope="col" class="py-3 px-5">Aktor Eksekusi</th>
                                <th scope="col" class="py-3 px-5">Perubahan Nominal</th>
                                <th scope="col" class="py-3 px-5">Alasan / Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($saldoHistori as $history)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-5 text-slate-600 whitespace-nowrap">{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-3 px-5 font-bold text-slate-800">{{ $history->user->name }}</td>
                                    <td class="py-3 px-5 text-slate-700">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 font-semibold text-[11px]">
                                            👤 {{ $history->actor->name }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-5 font-mono whitespace-nowrap">
                                        <span class="text-slate-400">Rp {{ number_format($history->nominal_sebelum, 0, ',', '.') }}</span>
                                        <span class="mx-1 text-slate-300">&rarr;</span>
                                        <span class="font-extrabold text-emerald-600">Rp {{ number_format($history->nominal_sesudah, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="py-3 px-5 text-slate-600">{{ $history->catatan }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 text-xs italic">Belum ada riwayat perubahan saldo.</td>
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