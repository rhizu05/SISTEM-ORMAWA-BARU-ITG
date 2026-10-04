<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Jadwal Perkuliahan (Pola Mingguan)') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6">
                <div class="mb-6">
                    <h3 class="text-base font-bold text-slate-900">Tambah Jadwal Perkuliahan</h3>
                    <p class="text-xs text-slate-500">Daftarkan mata kuliah rutin mingguan untuk sinkronisasi ketersediaan ruangan</p>
                </div>
                <form action="{{ route('sarpras.jadwal.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @csrf
                    <div>
                        <x-input-label for="ruangan_id" :value="__('Ruangan')" />
                        <select name="ruangan_id" id="ruangan_id" class="mt-1 block w-full border-slate-300 rounded-xl shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach($ruangans as $r)
                                <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="hari" :value="__('Hari')" />
                        <select name="hari" id="hari" class="mt-1 block w-full border-slate-300 rounded-xl shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @foreach(\App\Models\JadwalKuliah::HARI as $num => $nama)
                                <option value="{{ $num }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="mata_kuliah" :value="__('Mata Kuliah / Kegiatan')" />
                        <x-text-input id="mata_kuliah" name="mata_kuliah" type="text" class="mt-1 block w-full rounded-xl" required />
                    </div>
                    <div>
                        <x-input-label for="jam_mulai" :value="__('Jam Mulai')" />
                        <x-text-input id="jam_mulai" name="jam_mulai" type="time" class="mt-1 block w-full rounded-xl" required />
                    </div>
                    <div>
                        <x-input-label for="jam_selesai" :value="__('Jam Selesai')" />
                        <x-text-input id="jam_selesai" name="jam_selesai" type="time" class="mt-1 block w-full rounded-xl" required />
                    </div>
                    <div>
                        <x-input-label for="semester" :value="__('Semester (opsional)')" />
                        <x-text-input id="semester" name="semester" type="text" class="mt-1 block w-full rounded-xl" placeholder="Cth: Ganjil 2026/2027" />
                    </div>
                    <div class="md:col-span-3 flex justify-end pt-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-slate-900">Daftar Jadwal Kuliah</h3>
                    <p class="text-xs text-slate-500">Pola rutin perkuliahan mingguan untuk proteksi jadwal bentrok</p>
                </div>
                
                <x-table>
                    <x-table.thead>
                        <tr>
                            <x-table.th>Hari</x-table.th>
                            <x-table.th>Jam Perkuliahan</x-table.th>
                            <x-table.th>Mata Kuliah / Kegiatan</x-table.th>
                            <x-table.th>Ruangan</x-table.th>
                            <x-table.th>Semester</x-table.th>
                            <x-table.th align="center">Aksi</x-table.th>
                        </tr>
                    </x-table.thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($jadwals as $j)
                        <x-table.tr>
                            <x-table.td>
                                <span class="font-semibold text-slate-900">{{ \App\Models\JadwalKuliah::HARI[$j->hari] ?? $j->hari }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-mono font-medium bg-slate-100 text-slate-700">
                                    {{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }} WIB
                                </span>
                            </x-table.td>
                            <x-table.td>
                                <span class="font-medium text-slate-900">{{ $j->mata_kuliah }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="text-sm text-slate-700">{{ $j->ruangan->nama_ruangan ?? '-' }}</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="text-xs text-slate-600">{{ $j->semester ?? '-' }}</span>
                            </x-table.td>
                            <x-table.td align="center">
                                <form action="{{ route('sarpras.jadwal.destroy', $j) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">Hapus</button>
                                </form>
                            </x-table.td>
                        </x-table.tr>
                        @empty
                        <x-table.empty colspan="6" message="Belum ada jadwal kuliah terdaftar." />
                        @endforelse
                    </tbody>
                </x-table>
                <div class="mt-4">{{ $jadwals->links() }}</div>
            </div>

            {{-- FR-018 / UI-013: kalender interaktif ketersediaan ruangan --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 pb-4 mb-6">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Kalender Ketersediaan Ruangan</h3>
                        <p class="text-xs text-slate-500">Visualisasi jadwal kuliah dan reservasi ruangan kampus</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 text-xs mt-3 sm:mt-0">
                        <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                            <span class="w-3 h-3 rounded-full bg-indigo-500 shadow-sm ring-2 ring-indigo-100"></span> 
                            Jadwal Kuliah
                        </span>
                        <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                            <span class="w-3 h-3 rounded-full bg-amber-500 shadow-sm ring-2 ring-amber-100"></span> 
                            Peminjaman Ruangan
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
