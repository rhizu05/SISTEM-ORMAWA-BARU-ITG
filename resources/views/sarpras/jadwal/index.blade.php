<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Jadwal Perkuliahan (Pola Mingguan)') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Tambah Jadwal</h3>
                    <form action="{{ route('sarpras.jadwal.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @csrf
                        <div>
                            <x-input-label for="ruangan_id" :value="__('Ruangan')" />
                            <select name="ruangan_id" id="ruangan_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($ruangans as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="hari" :value="__('Hari')" />
                            <select name="hari" id="hari" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                @foreach(\App\Models\JadwalKuliah::HARI as $num => $nama)
                                    <option value="{{ $num }}">{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="mata_kuliah" :value="__('Mata Kuliah / Kegiatan')" />
                            <x-text-input id="mata_kuliah" name="mata_kuliah" type="text" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="jam_mulai" :value="__('Jam Mulai')" />
                            <x-text-input id="jam_mulai" name="jam_mulai" type="time" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="jam_selesai" :value="__('Jam Selesai')" />
                            <x-text-input id="jam_selesai" name="jam_selesai" type="time" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <x-input-label for="semester" :value="__('Semester (opsional)')" />
                            <x-text-input id="semester" name="semester" type="text" class="mt-1 block w-full" placeholder="Cth: Ganjil 2026/2027" />
                        </div>
                        <div class="md:col-span-3 flex justify-end">
                            <x-primary-button>Simpan Jadwal</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Daftar Jadwal Kuliah</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="p-2 border">Hari</th>
                                    <th class="p-2 border">Jam</th>
                                    <th class="p-2 border">Mata Kuliah</th>
                                    <th class="p-2 border">Ruangan</th>
                                    <th class="p-2 border">Semester</th>
                                    <th class="p-2 border text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jadwals as $j)
                                <tr class="border-b">
                                    <td class="p-2 border">{{ \App\Models\JadwalKuliah::HARI[$j->hari] ?? $j->hari }}</td>
                                    <td class="p-2 border">{{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }}</td>
                                    <td class="p-2 border">{{ $j->mata_kuliah }}</td>
                                    <td class="p-2 border">{{ $j->ruangan->nama_ruangan ?? '-' }}</td>
                                    <td class="p-2 border">{{ $j->semester ?? '-' }}</td>
                                    <td class="p-2 border text-center">
                                        <form action="{{ route('sarpras.jadwal.destroy', $j) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline text-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="p-4 text-center text-gray-500">Belum ada jadwal kuliah.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $jadwals->links() }}</div>
                </div>
            </div>

            {{-- FR-018 / UI-013: kalender interaktif ketersediaan ruangan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Kalender Ketersediaan Ruangan</h3>
                    <p class="text-xs text-gray-500 mb-3">Biru = jadwal kuliah (pola mingguan). Oranye = peminjaman ruangan disetujui/proses.</p>
                    <div id="calendar" class="p-2 border rounded shadow-sm"></div>
                </div>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('calendar');
            if (!el || typeof FullCalendar === 'undefined') return;
            const cal = new FullCalendar.Calendar(el, {
                locale: 'id',
                initialView: 'timeGridWeek',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'timeGridWeek,dayGridMonth' },
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                height: 'auto',
                events: [
                    @foreach($calendarJadwals as $j)
                    { title: 'Kuliah: {{ $j->mata_kuliah }} ({{ $j->ruangan->nama_ruangan ?? '-' }})', daysOfWeek: [{{ $j->hari % 7 }}], startTime: '{{ substr($j->jam_mulai, 0, 5) }}', endTime: '{{ substr($j->jam_selesai, 0, 5) }}', color: '#4f46e5' },
                    @endforeach
                    @foreach($peminjamanTempat as $p)
                    { title: 'Pinjam: {{ $p->nama_kegiatan }} ({{ $p->ruangan->nama_ruangan ?? '-' }})', start: '{{ $p->tgl_mulai }}T{{ substr($p->jam_mulai, 0, 5) }}', end: '{{ $p->tgl_selesai }}T{{ substr($p->jam_selesai, 0, 5) }}', color: '#f59e0b' },
                    @endforeach
                ]
            });
            cal.render();
        });
    </script>
</x-app-layout>
