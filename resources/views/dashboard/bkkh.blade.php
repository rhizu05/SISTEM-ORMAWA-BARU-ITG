<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Dashboard BKKH</h2></x-slot>
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

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white p-4 rounded shadow text-center"><div class="text-2xl font-bold">{{ $counts['verifikasi_proposal'] }}</div><div class="text-xs">Verifikasi Proposal</div></div>
                <div class="bg-white p-4 rounded shadow text-center"><div class="text-2xl font-bold">{{ $counts['verifikasi_lpj'] }}</div><div class="text-xs">Verifikasi LPJ</div></div>
                <div class="bg-white p-4 rounded shadow text-center"><div class="text-2xl font-bold">{{ $counts['siap_bendahara'] }}</div><div class="text-xs">Siap ke Bendahara</div></div>
                <div class="bg-white p-4 rounded shadow text-center"><div class="text-2xl font-bold">{{ $counts['verifikasi_tempat'] }}</div><div class="text-xs">Verifikasi Tempat</div><a href="{{ route('bkkh.verifikasi-tempat.index') }}" class="text-xs text-indigo-600">Kelola</a></div>
                <div class="bg-white p-4 rounded shadow text-center"><div class="text-2xl font-bold">{{ $counts['verifikasi_barang'] }}</div><div class="text-xs">Verifikasi Barang</div><a href="{{ route('peminjaman.verifikasi.index') }}" class="text-xs text-indigo-600">Kelola</a></div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-2">Tabel Verifikasi Proposal</h3>
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50"><tr><th class="p-2 border">No</th><th class="p-2 border">Nama Kegiatan</th><th class="p-2 border">Ormawa</th><th class="p-2 border">Tanggal</th><th class="p-2 border">Dana Diajukan</th><th class="p-2 border">Aksi</th></tr></thead>
                    <tbody>
                    @forelse($proposalQueue as $i=>$p)<tr><td class="p-2 border">{{ $i+1 }}</td><td class="p-2 border">{{ $p->nama_kegiatan }}</td><td class="p-2 border">{{ $p->user->name }}</td><td class="p-2 border">{{ $p->tanggal_pengajuan ?? $p->created_at->format('d/m/Y') }}</td><td class="p-2 border">Rp {{ number_format($p->dana_diajukan,0,',','.') }}</td><td class="p-2 border"><a href="{{ route('verifikasi.show',$p) }}" class="text-indigo-600">Verifikasi</a></td></tr>
                    @empty<tr><td colspan="6" class="p-4 text-center text-gray-500">Tidak ada proposal untuk diverifikasi saat ini.</td></tr>@endforelse
                    </tbody>
                </table>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-2">Antrean Verifikasi Tempat (BKKH)</h3>
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50"><tr><th class="p-2 border">Ormawa</th><th class="p-2 border">Kegiatan</th><th class="p-2 border">Ruangan</th><th class="p-2 border">Waktu</th><th class="p-2 border">Aksi</th></tr></thead>
                    <tbody>@forelse($tempatQueue as $p)<tr><td class="p-2 border">{{ $p->user->name }}</td><td class="p-2 border">{{ $p->nama_kegiatan }}</td><td class="p-2 border">{{ $p->ruangan->nama_ruangan ?? '-' }}</td><td class="p-2 border">{{ $p->tgl_mulai }} {{ $p->jam_mulai }}</td><td class="p-2 border"><a href="{{ route('peminjaman.verifikasi.index') }}" class="text-indigo-600">Proses</a></td></tr>@empty<tr><td colspan="5" class="p-4 text-center text-gray-500">Tidak ada antrean verifikasi tempat.</td></tr>@endforelse</tbody>
                </table>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold mb-2">Antrean Verifikasi Barang (BKKH)</h3>
                <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50"><tr><th class="p-2 border">Ormawa</th><th class="p-2 border">Kegiatan</th><th class="p-2 border">Barang</th><th class="p-2 border">Waktu</th><th class="p-2 border">Aksi</th></tr></thead>
                    <tbody>@forelse($barangQueue as $p)<tr><td class="p-2 border">{{ $p->user->name }}</td><td class="p-2 border">{{ $p->nama_kegiatan }}</td><td class="p-2 border">{{ collect($p->kebutuhan_barang)->pluck('nama_barang')->implode(', ') }}</td><td class="p-2 border">{{ $p->tgl_mulai }}</td><td class="p-2 border"><a href="{{ route('peminjaman.verifikasi.index') }}" class="text-indigo-600">Proses</a></td></tr>@empty<tr><td colspan="5" class="p-4 text-center text-gray-500">Tidak ada antrean verifikasi barang.</td></tr>@endforelse</tbody>
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
