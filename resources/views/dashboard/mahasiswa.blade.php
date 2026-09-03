<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Mahasiswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center text-center border-l-4 border-indigo-500">
                    <h3 class="text-xs font-medium text-gray-500 uppercase">Aspirasi Saya</h3>
                    <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $stats['total_aspirasi'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Announcements -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Pengumuman Terbaru</h3>
                    <div class="space-y-4">
                        @forelse($announcements as $ann)
                            <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                                <p class="text-sm font-bold">{{ $ann->judul }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ $ann->isi }}</p>
                                <span class="text-[10px] text-gray-400 mt-2 block">{{ $ann->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm text-center py-4">Belum ada pengumuman.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Agenda Rapat -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Agenda Kampus Mendatang</h3>
                    <div class="space-y-4">
                        @forelse($meetings as $meeting)
                            <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 rounded-lg border border-transparent hover:border-gray-200 transition">
                                <div class="bg-indigo-100 text-indigo-600 p-2 rounded-lg text-center min-w-[60px]">
                                    <div class="text-xs font-bold uppercase">{{ \Carbon\Carbon::parse($meeting->tanggal_rapat)->format('M') }}</div>
                                    <div class="text-xl font-bold">{{ \Carbon\Carbon::parse($meeting->tanggal_rapat)->format('d') }}</div>
                                </div>
                                <div>
                                    <p class="text-sm font-bold">{{ $meeting->judul_rapat }}</p>
                                    <p class="text-xs text-gray-500">{{ $meeting->lokasi }} | {{ $meeting->jam_rapat }} WIB</p>
                                    @if($meeting->link_meeting)
                                        <a href="{{ $meeting->link_meeting }}" target="_blank" class="text-xs text-indigo-600 hover:underline mt-1 inline-block font-medium">Link Meeting</a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 italic text-sm text-center py-4">Tidak ada agenda terdekat.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Calendar Section -->
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Kalender Kegiatan & Fasilitas</h3>
                    <div id="calendar" class="p-2 border rounded shadow-sm"></div>
                </div>
            </div>

            <!-- Quick Action -->
            <div class="mt-8 bg-indigo-900 rounded-lg p-8 text-white shadow-lg flex flex-col md:flex-row items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold mb-2">Punya Aspirasi atau Keluhan?</h3>
                    <p class="text-indigo-200">Suaramu membantu membangun kampus yang lebih baik.</p>
                </div>
                <a href="{{ route('aspirasi.create') }}" class="mt-4 md:mt-0 px-6 py-3 bg-white text-indigo-900 font-bold rounded-lg hover:bg-indigo-50 transition shadow-md">
                    Kirim Aspirasi Sekarang
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'id',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
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
                ]
            });
            calendar.render();
        });
    </script>
</x-app-layout>
