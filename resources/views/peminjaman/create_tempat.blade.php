<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajukan Peminjaman Ruangan') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200/90">
                <div class="p-4 sm:p-6 lg:p-8 text-gray-900 max-w-3xl mx-auto" x-data="slotChecker()">
                    
                    <form method="POST" action="{{ route('peminjaman.tempat.store') }}" enctype="multipart/form-data" @submit="submitting = true">
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

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
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

                        <!-- Alur Persetujuan Terpadu BKHM & Sarpras -->
                        <div class="mb-6 p-4 bg-indigo-50 border border-indigo-200 rounded-xl text-xs text-indigo-900 leading-relaxed flex items-start gap-3">
                            <svg class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="font-bold text-indigo-950 mb-0.5">Alur Konfirmasi Peminjaman Terpadu:</p>
                                <p class="text-indigo-800">Pengajuan peminjaman ruangan ini akan masuk ke <strong>Biro Kemahasiswaan (BKHM)</strong> terlebih dahulu untuk konfirmasi kegiatan. Setelah disetujui BKHM, pengajuan diteruskan otomatis ke <strong>Bagian Sarana &amp; Prasarana (Sarpras)</strong> untuk verifikasi ketersediaan dan penerbitan izin penggunaan ruangan.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4 gap-3">
                            <a class="min-h-[44px] inline-flex items-center justify-center underline text-sm text-gray-600 hover:text-gray-900 px-3" href="{{ route('peminjaman.index') }}">Batal</a>
                            <x-primary-button class="min-h-[44px]" x-bind:disabled="submitting">
                                <span x-show="!submitting">Ajukan Peminjaman</span>
                                <span x-show="submitting" class="inline-flex items-center gap-2" style="display: none;" x-cloak>
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Memproses...</span>
                                </span>
                            </x-primary-button>
                        </div>
                    </form>

                    {{-- FR-018 / UI-013: kalender ketersediaan ruangan --}}
                    <div class="mt-10 pt-6 border-t border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 border-b pb-3 gap-2">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Kalender Ketersediaan Fasilitas</h3>
                                <p class="text-xs text-slate-500 mt-1">Jadwal resmi perkuliahan dan peminjaman kegiatan ormawa.</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-4 text-xs mt-2 sm:mt-0">
                                <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                                    <span class="w-3 h-3 rounded-full bg-indigo-500 shadow-sm ring-2 ring-indigo-100"></span> 
                                    Jadwal Kuliah
                                </span>
                                <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                                    <span class="w-3 h-3 rounded-full bg-amber-500 shadow-sm ring-2 ring-amber-100"></span> 
                                    Peminjaman Ormawa
                                </span>
                            </div>
                        </div>
                        <x-calendar-style />
                        <div class="skin-calendar-wrapper mt-4">
                            <div id="calendar"></div>
                        </div>
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
                submitting: false,
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

            const isSmallScreen = window.innerWidth < 640;
            const cal = new FullCalendar.Calendar(el, {
                locale: 'id',
                initialView: isSmallScreen ? 'listMonth' : 'dayGridMonth',
                headerToolbar: isSmallScreen
                    ? { left: 'prev,next', center: 'title', right: 'listMonth,dayGridMonth' }
                    : { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' },
                height: isSmallScreen ? 420 : 520,
                events: events
            });
            cal.render();
        });
    </script>
</x-app-layout>