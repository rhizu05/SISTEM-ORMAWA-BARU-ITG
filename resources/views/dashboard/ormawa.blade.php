<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Ormawa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Top Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center text-center border-l-4 border-green-500">
                    <h3 class="text-xs font-medium text-gray-500 uppercase">Sisa Saldo Tersedia</h3>
                    <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($stats['saldo'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center text-center border-l-4 border-blue-500">
                    <h3 class="text-xs font-medium text-gray-500 uppercase">Total Dana Diberikan</h3>
                    <p class="text-2xl font-bold text-blue-600 mt-1">Rp {{ number_format($stats['total_dana'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center text-center border-l-4 border-yellow-500">
                    <h3 class="text-xs font-medium text-gray-500 uppercase">Dana Terpakai & Diproses</h3>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">Rp {{ number_format($stats['dana_diproses'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center text-center border-l-4 border-indigo-500">
                    <h3 class="text-xs font-medium text-gray-500 uppercase">Total Proposal Diajukan</h3>
                    <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $stats['total_pengajuan'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center text-center border-l-4 border-purple-500">
                    <h3 class="text-xs font-medium text-gray-500 uppercase">Proposal Dalam Proses</h3>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['sedang_proses'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Financial Chart -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Visualisasi Dana</h3>
                    <div style="height: 300px;">
                        <canvas id="financeChart"></canvas>
                    </div>
                </div>

                <!-- Quick Info / Account -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Informasi Akun</h3>
                    <div class="flex items-center space-x-4 mb-6">
                        <img src="{{ Auth::user()->foto_profil ? asset('storage/'.Auth::user()->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" class="w-16 h-16 rounded-full shadow">
                        <div>
                            <p class="font-bold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-sm text-gray-500">{{ Auth::user()->username }}</p>
                        </div>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status Akun:</span>
                            <span class="font-medium">{{ Auth::user()->status_akun ?? 'Aktif' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Role:</span>
                            <span class="font-medium">Ormawa</span>
                        </div>
                    </div>
                </div>

                <!-- Calendar / Facility Usage -->
                <div class="lg:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Kalender Pemakaian Fasilitas</h3>
                    <div id="calendar" class="bg-white p-4 border rounded shadow-sm"></div>
                </div>

                <!-- Agenda & Facilities List (Simplified) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Agenda Rapat Mendatang</h3>
                    <div class="space-y-4">
                        @forelse($meetings ?? [] as $meeting)
                            <div class="flex items-start space-x-3 p-2 hover:bg-gray-50 rounded transition">
                                <div class="bg-indigo-100 text-indigo-600 p-2 rounded text-center min-w-[50px]">
                                    <div class="text-xs font-bold uppercase">{{ \Carbon\Carbon::parse($meeting->tanggal_rapat)->format('M') }}</div>
                                    <div class="text-lg font-bold">{{ \Carbon\Carbon::parse($meeting->tanggal_rapat)->format('d') }}</div>
                                </div>
                                <div>
                                    <p class="text-sm font-bold">{{ $meeting->judul_rapat }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($meeting->tanggal_rapat)->format('d M Y') }} {{ $meeting->jam_rapat ?? '' }} WIB</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm">Tidak ada jadwal rapat.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Pemakaian Fasilitas Terdekat</h3>
                    <div class="space-y-4">
                        @forelse($facilities ?? [] as $fac)
                            <div class="flex items-center justify-between p-2 border-b last:border-0">
                                <div>
                                    <p class="text-sm font-bold">{{ $fac->ruangan->nama_ruangan ?? $fac->nama_kegiatan }}</p>
                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($fac->tgl_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($fac->tgl_selesai)->format('d M Y') }}</p>
                                </div>
                                <span class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded">{{ $fac->status_akhir ?? 'Aktif' }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm">Tidak ada jadwal pemakaian.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Pengumuman Terbaru</h3>
                    <div class="space-y-4">
                        @forelse($announcements ?? [] as $ann)
                            <div class="p-3 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                                <p class="text-sm font-bold">{{ $ann->judul }}</p>
                                <p class="text-xs text-gray-600 line-clamp-2">{{ $ann->isi }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm">Belum ada pengumuman.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Finance Chart
            const ctx = document.getElementById('financeChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sisa Saldo', 'Dana Diproses', 'Dana Terpakai'],
                    datasets: [{
                        data: [
                            {{ $stats['saldo'] }}, 
                            {{ $stats['dana_diproses'] }}, 
                            {{ $stats['total_dana'] }}
                        ],
                        backgroundColor: ['#16a34a', '#eab308', '#ef4444'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });

            // FullCalendar Integration
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [
                    @foreach($facilities as $fac)
                        {
                            title: '{{ $fac->ruangan->nama_ruangan ?? $fac->nama_kegiatan }}',
                            start: '{{ $fac->tgl_mulai }}',
                            end: '{{ \Carbon\Carbon::parse($fac->tgl_selesai)->addDay()->format('Y-m-d') }}',
                            color: '#4f46e5'
                        },
                    @endforeach
                    @foreach($meetings as $m)
                        {
                            title: 'Rapat: {{ $m->judul_rapat }}',
                            start: '{{ $m->tanggal_rapat }}',
                            color: '#f59e0b'
                        },
                    @endforeach
                ],
                eventClick: function(info) {
                    alert('Peminjaman: ' + info.event.title);
                }
            });
            calendar.render();
        });
    </script>
</x-app-layout>
