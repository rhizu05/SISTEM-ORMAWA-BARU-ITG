<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajukan Peminjaman Ruangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 max-w-3xl mx-auto" x-data="slotChecker()">
                    
                    <form method="POST" action="{{ route('peminjaman.tempat.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Ruangan -->
                        <div class="mb-4">
                            <x-input-label for="ruangan_id" :value="__('Pilih Ruangan')" />
                            <select id="ruangan_id" name="ruangan_id" x-model="ruanganId" @change="cekJadwal()" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($ruangans as $ruang)
                                    <option value="{{ $ruang->id }}" {{ old('ruangan_id') == $ruang->id ? 'selected' : '' }}>
                                        {{ $ruang->nama_ruangan }} (Kapasitas: {{ $ruang->kapasitas }} orang)
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('ruangan_id')" class="mt-2" />
                        </div>

                        <!-- Nama Kegiatan -->
                        <div class="mb-4">
                            <x-input-label for="nama_kegiatan" :value="__('Nama Kegiatan')" />
                            <x-text-input id="nama_kegiatan" class="block mt-1 w-full" type="text" name="nama_kegiatan" :value="old('nama_kegiatan')" required />
                            <x-input-error :messages="$errors->get('nama_kegiatan')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <!-- Tanggal Mulai -->
                            <div>
                                <x-input-label for="tgl_mulai" :value="__('Tanggal Mulai')" />
                                <x-text-input id="tgl_mulai" class="block mt-1 w-full" type="date" name="tgl_mulai" x-model="tanggal" @change="cekJadwal()" :value="old('tgl_mulai', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('tgl_mulai')" class="mt-2" />
                            </div>
                            
                            <!-- Tanggal Selesai -->
                            <div>
                                <x-input-label for="tgl_selesai" :value="__('Tanggal Selesai')" />
                                <x-text-input id="tgl_selesai" class="block mt-1 w-full" type="date" name="tgl_selesai" :value="old('tgl_selesai', date('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('tgl_selesai')" class="mt-2" />
                            </div>

                            <!-- Jam Mulai -->
                            <div>
                                <x-input-label for="jam_mulai" :value="__('Jam Mulai')" />
                                <x-text-input id="jam_mulai" class="block mt-1 w-full" type="time" name="jam_mulai" :value="old('jam_mulai')" required />
                                <x-input-error :messages="$errors->get('jam_mulai')" class="mt-2" />
                            </div>
                            
                            <!-- Jam Selesai -->
                            <div>
                                <x-input-label for="jam_selesai" :value="__('Jam Selesai')" />
                                <x-text-input id="jam_selesai" class="block mt-1 w-full" type="time" name="jam_selesai" :value="old('jam_selesai')" required />
                                <x-input-error :messages="$errors->get('jam_selesai')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Panel Inspektur Jadwal Hari Terpilih (Interactive Slot Checker) -->
                        <div x-show="ruanganId" class="mb-5 p-4 rounded-xl border transition-all" :class="hasConflict ? 'bg-amber-50/70 border-amber-200' : 'bg-emerald-50/70 border-emerald-200'">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-bold text-xs uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                    <span x-show="loading" class="animate-spin inline-block w-3.5 h-3.5 border-2 border-indigo-600 border-t-transparent rounded-full"></span>
                                    <span>Jadwal Ruangan Terpakai pada Hari <span x-text="hariNama"></span> (<span x-text="tanggal"></span>):</span>
                                </span>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full" :class="hasConflict ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'" x-text="hasConflict ? 'Ada ' + totalSlots + ' Jadwal Terisi' : 'Ruangan Kosong Bebas Digunakan'"></span>
                            </div>

                            <div x-show="!hasConflict && !loading" class="text-xs text-emerald-700 font-medium">
                                Tidak ada jadwal kuliah maupun kegiatan lain pada tanggal ini. Anda bebas memilih rentang jam.
                            </div>

                            <div x-show="hasConflict && !loading" class="space-y-1.5 mt-2">
                                <template x-for="item in slots" :key="item.judul + item.jam_mulai">
                                    <div class="text-xs p-2 rounded-lg flex items-center justify-between border" :class="item.tipe === 'kuliah' ? 'bg-blue-50 border-blue-200 text-blue-900' : 'bg-amber-100/80 border-amber-300 text-amber-950'">
                                        <div class="flex items-center gap-2">
                                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded" :class="item.tipe === 'kuliah' ? 'bg-blue-600 text-white' : 'bg-amber-600 text-white'" x-text="item.tipe === 'kuliah' ? 'Kuliah' : 'Ormawa'"></span>
                                            <span class="font-semibold" x-text="item.judul"></span>
                                        </div>
                                        <span class="font-mono font-bold text-xs" x-text="item.jam_mulai + ' - ' + item.jam_selesai + ' WIB'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-6">
                            <x-input-label for="deskripsi_kegiatan" :value="__('Deskripsi Kegiatan Singkat')" />
                            <textarea id="deskripsi_kegiatan" name="deskripsi_kegiatan" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('deskripsi_kegiatan') }}</textarea>
                            <x-input-error :messages="$errors->get('deskripsi_kegiatan')" class="mt-2" />
                        </div>

                        <!-- Q-SAR-04: Dokumen Persetujuan Prodi (wajib untuk HIMA) -->
                        <div class="mb-6">
                            <x-input-label for="file_persetujuan_prodi" :value="Auth::user()->isHima() ? __('Dokumen Persetujuan Prodi (PDF, wajib untuk HIMA)') : __('Dokumen Persetujuan Prodi (PDF, opsional)')" />
                            <input id="file_persetujuan_prodi" type="file" name="file_persetujuan_prodi" accept=".pdf" class="block mt-1 w-full border border-gray-300 rounded p-2" @if(Auth::user()->isHima()) required @endif />
                            <p class="text-xs text-gray-500 mt-1">Persetujuan Prodi dilakukan di luar sistem; unggah dokumen buktinya di sini.</p>
                            <x-input-error :messages="$errors->get('file_persetujuan_prodi')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 mr-4" href="{{ route('peminjaman.index') }}">Batal</a>
                            <x-primary-button>Ajukan Peminjaman</x-primary-button>
                        </div>
                    </form>

                    {{-- FR-018 / UI-013: kalender ketersediaan ruangan --}}
                    <div class="mt-10 pt-6 border-t border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-3 gap-2">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Kalender Ketersediaan Fasilitas</h3>
                                <p class="text-xs text-slate-500">Jadwal resmi perkuliahan dan peminjaman kegiatan ormawa.</p>
                            </div>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-blue-600 inline-block"></span> Jadwal Kuliah</span>
                                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-amber-500 inline-block"></span> Peminjaman Ormawa</span>
                            </div>
                        </div>
                        <div id="calendar" class="p-3 bg-white border border-slate-200 rounded-xl shadow-xs"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        function slotChecker() {
            return {
                ruanganId: '{{ old('ruangan_id', '') }}',
                tanggal: '{{ old('tgl_mulai', date('Y-m-d')) }}',
                hariNama: '',
                slots: [],
                loading: false,
                get totalSlots() {
                    return this.slots.length;
                },
                get hasConflict() {
                    return this.slots.length > 0;
                },
                init() {
                    if (this.ruanganId) {
                        this.cekJadwal();
                    }
                },
                async cekJadwal() {
                    if (!this.ruanganId || !this.tanggal) {
                        this.slots = [];
                        return;
                    }
                    this.loading = true;
                    try {
                        const res = await fetch(`{{ route('peminjaman.tempat.jadwal-ruangan') }}?ruangan_id=${this.ruanganId}&tanggal=${this.tanggal}`);
                        const data = await res.json();
                        this.hariNama = data.hari_nama || '';
                        this.slots = [...(data.kuliah || []), ...(data.peminjaman || [])];
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('calendar');
            if (!el || typeof FullCalendar === 'undefined') return;

            const events = [
                @foreach($peminjamanTempat as $p)
                { 
                    title: '{{ $p->nama_kegiatan }} ({{ $p->ruangan->nama_ruangan ?? '-' }})', 
                    start: '{{ $p->tgl_mulai }}T{{ substr($p->jam_mulai, 0, 5) }}', 
                    end: '{{ $p->tgl_selesai }}T{{ substr($p->jam_selesai, 0, 5) }}', 
                    color: '#f59e0b' 
                },
                @endforeach
                @foreach($kuliahEvents as $k)
                { 
                    title: '{{ addslashes($k['title']) }}', 
                    start: '{{ $k['start'] }}', 
                    end: '{{ $k['end'] }}', 
                    color: '{{ $k['color'] }}' 
                },
                @endforeach
            ];

            const cal = new FullCalendar.Calendar(el, {
                locale: 'id',
                initialView: 'dayGridMonth',
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
                height: 520,
                events: events
            });
            cal.render();
        });
    </script>
</x-app-layout>