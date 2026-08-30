<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Dashboard BEM</h2></x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-4 rounded shadow">Selamat Datang kembali, {{ Auth::user()->name }}!</div>

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

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-3">Status Dana Anda</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="border p-4 rounded text-center"><div class="text-xs text-gray-500">Total Saldo Diberikan</div><div class="text-xl font-bold">Rp {{ number_format($saldoAwal,0,',','.') }}</div></div>
                    <div class="border p-4 rounded text-center"><div class="text-xs text-gray-500">Saldo Terpakai & Diproses</div><div class="text-xl font-bold">Rp {{ number_format($terpakai,0,',','.') }}</div></div>
                    <div class="border p-4 rounded text-center"><div class="text-xs text-gray-500">Sisa Saldo Tersedia</div><div class="text-xl font-bold text-green-600">Rp {{ number_format(Auth::user()->saldo,0,',','.') }}</div></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-6 rounded shadow text-center"><div class="text-3xl font-bold">{{ $counts['verifikasi_proposal'] }}</div><div class="text-sm text-gray-600">Verifikasi Proposal</div></div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-2">Tabel Verifikasi Proposal</h3>
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50"><tr><th class="p-2 border">No</th><th class="p-2 border">Nama Kegiatan</th><th class="p-2 border">Ormawa</th><th class="p-2 border">Tanggal Kegiatan</th><th class="p-2 border">Dana Diajukan</th><th class="p-2 border">Aksi</th></tr></thead>
                    <tbody>
                    @forelse($proposalQueue as $i=>$p)<tr><td class="p-2 border">{{ $i+1 }}</td><td class="p-2 border">{{ $p->nama_kegiatan }}</td><td class="p-2 border">{{ $p->user->name }}</td><td class="p-2 border">{{ $p->tanggal_pengajuan ?? $p->created_at->format('d/m/Y') }}</td><td class="p-2 border">Rp {{ number_format($p->dana_diajukan,0,',','.') }}</td><td class="p-2 border"><a href="{{ route('verifikasi.show',$p) }}" class="text-indigo-600">Verifikasi</a></td></tr>
                    @empty<tr><td colspan="6" class="p-4 text-center text-gray-500">Tidak ada proposal untuk diverifikasi saat ini.</td></tr>@endforelse
                    </tbody>
                </table>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-2">Jadwal Terpadu Fasilitas & Barang</h3>
                <p class="text-xs text-gray-500 mb-2">Klik pada agenda untuk melihat detail kegiatan</p>
                <div id="calendar" class="border p-2 rounded"></div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
        const el=document.getElementById('calendar');
        if(!el) return;
        const cal=new FullCalendar.Calendar(el,{initialView:'dayGridMonth',headerToolbar:{left:'prev,next today',center:'title',right:'dayGridMonth,timeGridWeek'},events:[
            @foreach($calendarTempat as $c){title:'Tempat: {{ $c->ruangan->nama_ruangan ?? $c->nama_kegiatan }}',start:'{{ $c->tgl_mulai }}',end:'{{ \Carbon\Carbon::parse($c->tgl_selesai)->addDay()->format('Y-m-d') }}',color:'#4f46e5'},
            @endforeach
            @foreach($calendarBarang as $c){title:'Barang: {{ $c->nama_kegiatan }}',start:'{{ $c->tgl_mulai }}',end:'{{ \Carbon\Carbon::parse($c->tgl_selesai)->addDay()->format('Y-m-d') }}',color:'#f59e0b'},
            @endforeach
        ]});cal.render();
    });
    </script>
</x-app-layout>
